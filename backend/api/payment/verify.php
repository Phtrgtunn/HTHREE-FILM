<?php
/**
 * Payment Verification
 * Xác thực thanh toán từ các cổng và TỰ ĐỘNG kích hoạt subscription
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Lấy thông tin từ callback
    $payment_method = $data['payment_method'] ?? '';
    $order_code = $data['order_code'] ?? $data['vnp_TxnRef'] ?? $data['orderId'] ?? '';
    
    if (!$order_code) {
        throw new Exception('Order code not found');
    }
    
    $conn = getDBConnection();
    
    // Lấy thông tin đơn hàng
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ?");
    $stmt->bind_param("s", $order_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if (!$order) {
        throw new Exception('Order not found');
    }
    
    // Kiểm tra đã thanh toán chưa
    if ($order['payment_status'] === 'paid') {
        echo json_encode([
            'success' => true,
            'message' => 'Order already paid',
            'data' => [
                'order_code' => $order['order_code'],
                'amount_formatted' => number_format($order['total'], 0, ',', '.') . ' đ',
                'payment_method' => $order['payment_method']
            ]
        ]);
        exit;
    }
    
    // Xác thực thanh toán dựa trên phương thức
    $is_valid = false;
    
    switch ($payment_method) {
        case 'vnpay':
            $is_valid = verifyVNPay($data);
            break;
        case 'momo':
            $is_valid = verifyMoMo($data);
            break;
        case 'zalopay':
            $is_valid = verifyZaloPay($data);
            break;
        default:
            // Nếu không có payment_method, kiểm tra từ params
            if (isset($data['vnp_ResponseCode'])) {
                $is_valid = verifyVNPay($data);
                $payment_method = 'vnpay';
            } elseif (isset($data['resultCode'])) {
                $is_valid = verifyMoMo($data);
                $payment_method = 'momo';
            }
    }
    
    if (!$is_valid) {
        // Cập nhật trạng thái thất bại
        $stmt = $conn->prepare("
            UPDATE orders 
            SET payment_status = 'failed',
                updated_at = NOW()
            WHERE order_code = ?
        ");
        $stmt->bind_param("s", $order_code);
        $stmt->execute();
        
        throw new Exception('Payment verification failed');
    }
    
    // ✅ TỰ ĐỘNG KÍCH HOẠT SUBSCRIPTION
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
            WHERE order_code = ?
        ");
        $stmt->bind_param("s", $order_code);
        $stmt->execute();
        
        // 2. Lấy danh sách items trong đơn hàng
        $stmt = $conn->prepare("
            SELECT oi.*, sp.duration_days 
            FROM order_items oi
            JOIN subscription_plans sp ON oi.plan_id = sp.id
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param("i", $order['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // 3. Kích hoạt từng gói
        while ($item = $result->fetch_assoc()) {
            $plan_id = $item['plan_id'];
            $duration_months = $item['duration_months'];
            $duration_days = $item['duration_days'] * $duration_months;
            
            // Kiểm tra xem user đã có gói này chưa
            $check_stmt = $conn->prepare("
                SELECT id, end_date 
                FROM user_subscriptions 
                WHERE user_id = ? AND plan_id = ? AND status = 'active'
                ORDER BY end_date DESC 
                LIMIT 1
            ");
            $check_stmt->bind_param("ii", $order['user_id'], $plan_id);
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
                    INSERT INTO user_subscriptions (
                        user_id, plan_id, start_date, end_date, status, order_id, created_at, updated_at
                    ) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), 'active', ?, NOW(), NOW())
                ");
                $insert_stmt->bind_param("iiii", $order['user_id'], $plan_id, $duration_days, $order['id']);
                $insert_stmt->execute();
            }
        }
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Payment verified and subscription activated successfully',
            'data' => [
                'order_code' => $order['order_code'],
                'amount_formatted' => number_format($order['total'], 0, ',', '.') . ' đ',
                'payment_method' => $payment_method
            ]
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

/**
 * Xác thực VNPay
 */
function verifyVNPay($data) {
    // Kiểm tra response code
    $vnp_ResponseCode = $data['vnp_ResponseCode'] ?? '';
    
    // 00 = Thành công
    if ($vnp_ResponseCode === '00') {
        return true;
    }
    
    return false;
}

/**
 * Xác thực MoMo
 */
function verifyMoMo($data) {
    // Kiểm tra result code
    $resultCode = $data['resultCode'] ?? '';
    
    // 0 = Thành công
    if ($resultCode === 0 || $resultCode === '0') {
        return true;
    }
    
    return false;
}

/**
 * Xác thực ZaloPay
 */
function verifyZaloPay($data) {
    // Kiểm tra return code
    $return_code = $data['return_code'] ?? '';
    
    // 1 = Thành công
    if ($return_code === 1 || $return_code === '1') {
        return true;
    }
    
    return false;
}
