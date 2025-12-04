<?php
/**
 * API Admin: Danh sách khách hàng
 * GET: Lấy danh sách khách hàng đã mua hàng
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

$conn = getDBConnection();

try {
    // Lấy danh sách khách hàng với thông tin đơn hàng và subscription
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.username,
            u.email,
            u.full_name,
            u.created_at,
            COUNT(DISTINCT o.id) as total_orders,
            SUM(CASE WHEN o.payment_status = 'paid' THEN o.total ELSE 0 END) as total_spent,
            GROUP_CONCAT(DISTINCT sp.name ORDER BY sp.name SEPARATOR ', ') as active_plans,
            MAX(o.created_at) as last_order_date
        FROM users u
        LEFT JOIN orders o ON u.id = o.user_id
        LEFT JOIN user_subscriptions us ON u.id = us.user_id AND us.status = 'active'
        LEFT JOIN subscription_plans sp ON us.plan_id = sp.id
        GROUP BY u.id, u.username, u.email, u.full_name, u.created_at
        HAVING total_orders > 0
        ORDER BY total_spent DESC, u.created_at DESC
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $row['total_spent_formatted'] = number_format($row['total_spent'], 0, ',', '.') . 'đ';
        $row['created_at_formatted'] = date('d/m/Y', strtotime($row['created_at']));
        $row['last_order_date_formatted'] = $row['last_order_date'] ? date('d/m/Y', strtotime($row['last_order_date'])) : 'N/A';
        $customers[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $customers,
        'count' => count($customers)
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
