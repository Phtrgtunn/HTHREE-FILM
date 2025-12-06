<?php
/**
 * Check Payment Status
 * Frontend polling để check xem đơn hàng đã thanh toán chưa
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
    $orderCode = $_GET['order_code'] ?? null;
    $orderId = $_GET['order_id'] ?? null;
    
    if (!$orderCode && !$orderId) {
        throw new Exception('Order code or order ID is required');
    }
    
    // Tìm đơn hàng
    if ($orderCode) {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ?");
        $stmt->execute([$orderCode]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
    }
    
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        throw new Exception('Order not found');
    }
    
    // Kiểm tra trạng thái
    $isPaid = $order['payment_status'] === 'paid';
    $isExpired = strtotime($order['expires_at']) < time();
    
    // Nếu đã hết hạn mà chưa thanh toán, cập nhật trạng thái
    if ($isExpired && $order['payment_status'] === 'pending') {
        $stmt = $conn->prepare("
            UPDATE orders 
            SET payment_status = 'expired'
            WHERE id = ?
        ");
        $stmt->execute([$order['id']]);
        
        $order['payment_status'] = 'expired';
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'order_id' => $order['id'],
            'order_code' => $order['order_code'],
            'payment_status' => $order['payment_status'],
            'paid' => $isPaid,
            'expired' => $isExpired,
            'paid_at' => $order['paid_at'],
            'expires_at' => $order['expires_at'],
            'remaining_seconds' => max(0, strtotime($order['expires_at']) - time())
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
