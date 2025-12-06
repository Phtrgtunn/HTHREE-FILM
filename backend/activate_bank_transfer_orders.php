<?php
/**
 * Script kích hoạt đơn hàng chuyển khoản đã thanh toán
 * Chạy file này để kích hoạt tất cả đơn chuyển khoản pending
 */

require_once __DIR__ . '/config/database.php';

echo "<h2>🔄 Kích hoạt đơn hàng chuyển khoản</h2>";
echo "<hr>";

try {
    $conn = getDBConnection();
    
    // Lấy danh sách đơn hàng chuyển khoản đang pending
    $stmt = $conn->prepare("
        SELECT id, order_code, user_id, total, payment_method, created_at
        FROM orders 
        WHERE payment_method = 'bank_transfer' 
        AND payment_status = 'pending'
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
    
    if (empty($orders)) {
        echo "<p style='color: green;'>✅ Không có đơn hàng chuyển khoản nào cần kích hoạt!</p>";
        exit;
    }
    
    echo "<p>Tìm thấy <strong>" . count($orders) . "</strong> đơn hàng chuyển khoản đang chờ:</p>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>
            <th>STT</th>
            <th>Mã đơn</th>
            <th>User ID</th>
            <th>Số tiền</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
          </tr>";
    
    foreach ($orders as $index => $order) {
        $stt = $index + 1;
        $amount = number_format($order['total'], 0, ',', '.') . ' đ';
        
        echo "<tr>";
        echo "<td>{$stt}</td>";
        echo "<td><strong>{$order['order_code']}</strong></td>";
        echo "<td>{$order['user_id']}</td>";
        echo "<td>{$amount}</td>";
        echo "<td>{$order['created_at']}</td>";
        echo "<td>
                <form method='POST' style='display: inline;'>
                    <input type='hidden' name='order_id' value='{$order['id']}'>
                    <input type='hidden' name='order_code' value='{$order['order_code']}'>
                    <button type='submit' name='activate' style='background: green; color: white; padding: 5px 10px; border: none; cursor: pointer;'>
                        ✓ Kích hoạt
                    </button>
                </form>
              </td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Xử lý kích hoạt
    if (isset($_POST['activate'])) {
        $order_id = intval($_POST['order_id']);
        $order_code = $_POST['order_code'];
        
        echo "<hr>";
        echo "<h3>🚀 Đang kích hoạt đơn hàng: {$order_code}</h3>";
        
        $conn->begin_transaction();
        
        try {
            // 1. Cập nhật trạng thái đơn hàng
            $stmt = $conn->prepare("
                UPDATE orders 
                SET payment_status = 'paid',
                    status = 'completed',
                    paid_at = NOW(),
                    completed_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            
            echo "<p>✅ Đã cập nhật trạng thái đơn hàng</p>";
            
            // 2. Lấy thông tin order items
            $stmt = $conn->prepare("
                SELECT oi.*, sp.duration_days, o.user_id
                FROM order_items oi
                JOIN subscription_plans sp ON oi.plan_id = sp.id
                JOIN orders o ON oi.order_id = o.id
                WHERE oi.order_id = ?
            ");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // 3. Kích hoạt từng gói
            while ($item = $result->fetch_assoc()) {
                $user_id = $item['user_id'];
                $plan_id = $item['plan_id'];
                $duration_months = $item['duration_months'];
                $duration_days = $item['duration_days'] * $duration_months;
                
                // Kiểm tra đã có subscription chưa (chỉ check những gói chưa hết hạn và chưa bị hủy)
                $check_stmt = $conn->prepare("
                    SELECT id, end_date, status 
                    FROM user_subscriptions 
                    WHERE user_id = ? AND plan_id = ? AND status = 'active' AND end_date > NOW()
                    ORDER BY end_date DESC 
                    LIMIT 1
                ");
                $check_stmt->bind_param("ii", $user_id, $plan_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    // Gia hạn (chỉ khi gói còn hiệu lực)
                    $existing = $check_result->fetch_assoc();
                    $current_end = new DateTime($existing['end_date']);
                    $now = new DateTime();
                    
                    $start_from = $current_end > $now ? $current_end : $now;
                    $new_end = clone $start_from;
                    $new_end->modify("+{$duration_days} days");
                    
                    $update_stmt = $conn->prepare("
                        UPDATE user_subscriptions 
                        SET end_date = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $new_end_str = $new_end->format('Y-m-d H:i:s');
                    $update_stmt->bind_param("si", $new_end_str, $existing['id']);
                    $update_stmt->execute();
                    
                    echo "<p>✅ Đã gia hạn subscription (Plan ID: {$plan_id})</p>";
                } else {
                    // Tạo mới (hoặc gói đã hết hạn/bị hủy)
                    // Trước khi tạo mới, set các gói cũ thành 'expired' nếu đã hết hạn
                    $expire_old = $conn->prepare("
                        UPDATE user_subscriptions 
                        SET status = 'expired', updated_at = NOW()
                        WHERE user_id = ? AND plan_id = ? AND status = 'active' AND end_date <= NOW()
                    ");
                    $expire_old->bind_param("ii", $user_id, $plan_id);
                    $expire_old->execute();
                    
                    $insert_stmt = $conn->prepare("
                        INSERT INTO user_subscriptions (
                            user_id, plan_id, start_date, end_date, status, order_id, created_at, updated_at
                        ) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), 'active', ?, NOW(), NOW())
                    ");
                    $insert_stmt->bind_param("iiii", $user_id, $plan_id, $duration_days, $order_id);
                    $insert_stmt->execute();
                    
                    echo "<p>✅ Đã tạo subscription mới (Plan ID: {$plan_id}) - Thời gian được reset</p>";
                }
            }
            
            $conn->commit();
            
            echo "<p style='color: green; font-weight: bold;'>🎉 Kích hoạt thành công đơn hàng {$order_code}!</p>";
            echo "<p><a href='{$_SERVER['PHP_SELF']}'>← Quay lại danh sách</a></p>";
            
        } catch (Exception $e) {
            $conn->rollback();
            echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>📝 Hướng dẫn</h3>";
    echo "<ol>";
    echo "<li>Kiểm tra khách hàng đã chuyển khoản thành công chưa</li>";
    echo "<li>Click nút <strong>✓ Kích hoạt</strong> để kích hoạt đơn hàng</li>";
    echo "<li>Hệ thống sẽ tự động cập nhật trạng thái và tạo subscription</li>";
    echo "<li>Khách hàng có thể xem phim ngay lập tức</li>";
    echo "</ol>";
    
    echo "<hr>";
    echo "<p><strong>⚠️ Lưu ý:</strong> Chỉ kích hoạt khi đã nhận được tiền!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #f5f5f5;
}
table {
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
button:hover {
    opacity: 0.8;
}
</style>
