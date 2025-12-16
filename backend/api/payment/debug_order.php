<?php
/**
 * Debug Order Status
 * Kiểm tra trạng thái đơn hàng để debug
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
    $orderId = $_GET['order_id'] ?? null;
    
    if (!$orderId) {
        throw new Exception('Order ID is required');
    }
    
    // Lấy thông tin đơn hàng
    $stmt = $conn->prepare("
        SELECT o.*, oi.plan_id, sp.name as plan_name
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN subscription_plans sp ON oi.plan_id = sp.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        throw new Exception('Order not found');
    }
    
    // Tính thời gian đã tạo
    $createdTime = strtotime($order['created_at']);
    $currentTime = time();
    $timeDiff = $currentTime - $createdTime;
    
    echo json_encode([
        'success' => true,
        'order' => [
            'id' => $order['id'],
            'order_code' => $order['order_code'],
            'payment_status' => $order['payment_status'],
            'status' => $order['status'],
            'total' => $order['total'],
            'plan_name' => $order['plan_name'],
            'created_at' => $order['created_at'],
            'paid_at' => $order['paid_at'],
            'expires_at' => $order['expires_at']
        ],
        'timing' => [
            'created_seconds_ago' => $timeDiff,
            'simulation_ready' => $timeDiff >= 10,
            'expires_in_seconds' => strtotime($order['expires_at']) - $currentTime
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}