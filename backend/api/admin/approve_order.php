<?php
/**
 * API Admin: Duyệt đơn hàng
 * POST: Duyệt đơn và kích hoạt subscription
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

$conn = getDBConnection();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['order_id'])) {
        throw new Exception('Order ID is required');
    }
    
    $order_id = intval($data['order_id']);
    
    // Kiểm tra order tồn tại
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Order not found');
    }
    
    $order = $result->fetch_assoc();
    
    // Kiểm tra order đã được duyệt chưa
    if ($order['payment_status'] === 'paid') {
        throw new Exception('Order already approved');
    }
    
    $conn->begin_transaction();
    
    try {
        // 1. Update order status
        $stmt = $conn->prepare("
            UPDATE orders 
            SET payment_status = 'paid',
                paid_at = NOW(),
                status = 'completed',
                completed_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        // 2. Tạo hoặc gia hạn subscription
        // Lấy thông tin order items
        $stmt = $conn->prepare("
            SELECT oi.plan_id, oi.duration_months, sp.duration_days, o.user_id
            FROM order_items oi
            JOIN subscription_plans sp ON oi.plan_id = sp.id
            JOIN orders o ON oi.order_id = o.id
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($item = $result->fetch_assoc()) {
            $user_id = $item['user_id'];
            $plan_id = $item['plan_id'];
            $duration_days = $item['duration_days'] * $item['duration_months'];
            
            // Kiểm tra đã có subscription cùng plan chưa
            $check_stmt = $conn->prepare("
                SELECT id, end_date, status 
                FROM user_subscriptions 
                WHERE user_id = ? AND plan_id = ? AND status = 'active'
                ORDER BY end_date DESC 
                LIMIT 1
            ");
            $check_stmt->bind_param("ii", $user_id, $plan_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                // Đã có subscription → Gia hạn thêm
                $existing = $check_result->fetch_assoc();
                $current_end = new DateTime($existing['end_date']);
                $now = new DateTime();
                
                // Nếu còn hạn, gia hạn từ end_date hiện tại
                // Nếu hết hạn, gia hạn từ hôm nay
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
                
            } else {
                // Chưa có subscription → Tạo mới
                $insert_stmt = $conn->prepare("
                    INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status, order_id)
                    VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), 'active', ?)
                ");
                $insert_stmt->bind_param("iiii", $user_id, $plan_id, $duration_days, $order_id);
                $insert_stmt->execute();
            }
        }
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Order approved and subscription activated successfully'
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
