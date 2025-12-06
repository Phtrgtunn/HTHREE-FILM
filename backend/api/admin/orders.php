<?php
/**
 * Admin Orders API
 * Quản lý đơn hàng cho admin
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            getOrders($conn);
            break;
        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Lấy danh sách đơn hàng
 */
function getOrders($conn) {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    $sql = "
        SELECT o.*
        FROM orders o
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($status) {
        $sql .= " AND o.payment_status = ?";
        $params[] = $status;
    }
    
    if ($search) {
        $sql .= " AND (o.order_code LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    $sql .= " ORDER BY o.created_at DESC LIMIT " . $limit;
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dữ liệu cho frontend
    foreach ($orders as &$order) {
        // Format số tiền
        $order['total_formatted'] = number_format($order['total'], 0, ',', '.') . ' đ';
        
        // Format ngày tháng
        if (isset($order['created_at'])) {
            $date = new DateTime($order['created_at']);
            $order['created_at'] = $date->format('d/m/Y H:i');
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $orders,
        'count' => count($orders)
    ]);
}

/**
 * Đã bỏ function confirmPayment
 * Thanh toán tự động kích hoạt subscription qua activate_subscription.php
 */
