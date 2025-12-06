<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get order code from query parameter
    $order_code = isset($_GET['order_code']) ? trim($_GET['order_code']) : '';
    
    if (empty($order_code)) {
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu mã đơn hàng'
        ]);
        exit;
    }
    
    // Fetch order with items
    $query = "SELECT 
                o.id,
                o.order_code,
                o.user_id,
                o.customer_name,
                o.customer_email,
                o.customer_phone,
                o.total,
                o.discount,
                o.payment_method,
                o.payment_status,
                o.status,
                o.note,
                o.payment_note,
                o.created_at,
                o.updated_at
              FROM orders o
              WHERE o.order_code = :order_code
              LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_code', $order_code);
    $stmt->execute();
    
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy đơn hàng'
        ]);
        exit;
    }
    
    // Fetch order items with plan details
    $itemsQuery = "SELECT 
                    oi.id,
                    oi.plan_id,
                    oi.quantity,
                    oi.price,
                    oi.subtotal,
                    sp.name as plan_name,
                    sp.slug as plan_slug,
                    sp.quality,
                    sp.max_devices,
                    sp.duration_days
                   FROM order_items oi
                   JOIN subscription_plans sp ON oi.plan_id = sp.id
                   WHERE oi.order_id = :order_id";
    
    $itemsStmt = $db->prepare($itemsQuery);
    $itemsStmt->bindParam(':order_id', $order['id']);
    $itemsStmt->execute();
    
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format order data
    $orderData = [
        'id' => $order['id'],
        'order_code' => $order['order_code'],
        'user_id' => $order['user_id'],
        'customer_name' => $order['customer_name'],
        'customer_email' => $order['customer_email'],
        'customer_phone' => $order['customer_phone'],
        'total' => (float)$order['total'],
        'discount' => (float)$order['discount'],
        'payment_method' => $order['payment_method'],
        'payment_status' => $order['payment_status'],
        'status' => $order['status'],
        'note' => $order['note'],
        'payment_note' => $order['payment_note'],
        'created_at' => $order['created_at'],
        'updated_at' => $order['updated_at'],
        'items' => $items
    ];
    
    // Add plan info for display (first item)
    if (!empty($items)) {
        $firstItem = $items[0];
        $orderData['plan_name'] = $firstItem['plan_name'];
        $orderData['quality'] = $firstItem['quality'];
        $orderData['duration'] = $firstItem['duration_days'] * $firstItem['quantity'];
    }
    
    echo json_encode([
        'success' => true,
        'order' => $orderData
    ]);
    
} catch (Exception $e) {
    error_log("Check order status error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}
