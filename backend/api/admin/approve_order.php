<?php
/**
 * Approve Order (Manual Payment Confirmation)
 * Dùng để admin hoặc test approve đơn hàng thủ công
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

$db = new Database();
$conn = $db->getConnection();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $orderId = $input['order_id'] ?? null;
    $transactionId = $input['transaction_id'] ?? 'MANUAL_' . time();
    
    if (!$orderId) {
        throw new Exception('Order ID is required');
    }
    
    // Bắt đầu transaction
    $conn->beginTransaction();
    
    // 1. Update order status
    $stmt = $conn->prepare("
        UPDATE orders 
        SET payment_status = 'paid',
            status = 'completed',
            paid_at = NOW(),
            completed_at = NOW(),
            transaction_id = ?
        WHERE id = ?
    ");
    
    $stmt->execute([$transactionId, $orderId]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Order not found or already processed');
    }
    
    // 2. Lấy thông tin order để kích hoạt subscription
    $stmt = $conn->prepare("
        SELECT o.*, oi.plan_id, oi.duration_months, sp.duration_days
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN subscription_plans sp ON oi.plan_id = sp.id
        WHERE o.id = ?
        LIMIT 1
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        throw new Exception('Order details not found');
    }
    
    // 3. Kích hoạt subscription
    $totalDays = $order['duration_days'] * $order['duration_months'];
    $endDate = date('Y-m-d H:i:s', strtotime("+{$totalDays} days"));
    
    $stmt = $conn->prepare("
        INSERT INTO user_subscriptions 
        (user_id, plan_id, start_date, end_date, status, order_id)
        VALUES (?, ?, NOW(), ?, 'active', ?)
    ");
    
    $stmt->execute([
        $order['user_id'],
        $order['plan_id'],
        $endDate,
        $orderId
    ]);
    
    // 4. Log payment
    $stmt = $conn->prepare("
        INSERT INTO payment_logs 
        (order_id, order_code, event_type, message, data)
        VALUES (?, ?, 'manual_approval', 'Order manually approved', ?)
    ");
    
    $logData = json_encode([
        'transaction_id' => $transactionId,
        'approved_at' => date('Y-m-d H:i:s'),
        'method' => 'manual'
    ]);
    
    $stmt->execute([
        $orderId,
        $order['order_code'],
        $logData
    ]);
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order approved successfully',
        'data' => [
            'order_id' => $orderId,
            'order_code' => $order['order_code'],
            'subscription_end_date' => $endDate
        ]
    ]);
    
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
