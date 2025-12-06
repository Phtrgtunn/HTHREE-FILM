<?php
/**
 * Kích hoạt gói subscription sau khi thanh toán thành công
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
    
    $order_id = $data['order_id'] ?? null;
    $order_code = $data['order_code'] ?? null;
    
    if (!$order_id && !$order_code) {
        throw new Exception('Order ID or Order Code is required');
    }
    
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    // Lấy thông tin đơn hàng
    if ($order_code) {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ?");
        $stmt->bind_param("s", $order_code);
    } else {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
    }
    
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
            'message' => 'Subscription already activated'
        ]);
        exit;
    }
    
    // Cập nhật trạng thái đơn hàng - TỰ ĐỘNG THÀNH CÔNG
    $stmt = $conn->prepare("
        UPDATE orders 
        SET payment_status = 'paid', 
            status = 'completed',
            paid_at = NOW(),
            completed_at = NOW(),
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param("i", $order['id']);
    $stmt->execute();
    
    // Lấy danh sách items trong đơn hàng
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Kích hoạt từng gói
    while ($item = $result->fetch_assoc()) {
        $plan_id = $item['plan_id'];
        $duration_months = $item['duration_months'];
        
        // Tính ngày hết hạn
        $end_date = date('Y-m-d H:i:s', strtotime("+{$duration_months} months"));
        
        // Kiểm tra xem user đã có gói này chưa
        $stmt = $conn->prepare("
            SELECT id FROM user_subscriptions 
            WHERE user_id = ? AND plan_id = ? AND status = 'active'
        ");
        $stmt->bind_param("ii", $order['user_id'], $plan_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        
        if ($existing) {
            // Gia hạn thêm
            $stmt = $conn->prepare("
                UPDATE user_subscriptions 
                SET end_date = DATE_ADD(end_date, INTERVAL ? MONTH),
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param("ii", $duration_months, $existing['id']);
            $stmt->execute();
        } else {
            // Tạo mới
            $stmt = $conn->prepare("
                INSERT INTO user_subscriptions (
                    user_id, plan_id, start_date, end_date, status, order_id, created_at, updated_at
                ) VALUES (?, ?, NOW(), ?, 'active', ?, NOW(), NOW())
            ");
            $stmt->bind_param("iisi", $order['user_id'], $plan_id, $end_date, $order['id']);
            $stmt->execute();
        }
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Subscription activated successfully',
        'order_code' => $order['order_code']
    ]);
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
