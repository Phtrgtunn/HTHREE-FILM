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
        case 'PUT':
            updateOrder($conn);
            break;
        case 'POST':
            if (isset($_POST['action']) && $_POST['action'] === 'approve') {
                approveOrder($conn);
            } else {
                throw new Exception('Invalid action');
            }
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
 * Cập nhật trạng thái đơn hàng
 */
function updateOrder($conn) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        throw new Exception('Order ID is required');
    }
    
    $orderId = $input['id'];
    $updates = [];
    $params = [];
    
    // Các trường có thể cập nhật
    $allowedFields = ['payment_status', 'status', 'admin_note'];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updates[] = "$field = ?";
            $params[] = $input[$field];
        }
    }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    // Thêm updated_at
    $updates[] = "updated_at = NOW()";
    
    // Nếu payment_status chuyển thành 'paid', cập nhật paid_at
    if (isset($input['payment_status']) && $input['payment_status'] === 'paid') {
        $updates[] = "paid_at = NOW()";
    }
    
    // Nếu status chuyển thành 'completed', cập nhật completed_at
    if (isset($input['status']) && $input['status'] === 'completed') {
        $updates[] = "completed_at = NOW()";
    }
    
    $params[] = $orderId;
    
    $sql = "UPDATE orders SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() > 0) {
        // Nếu payment_status chuyển thành 'paid', kích hoạt subscription
        if (isset($input['payment_status']) && $input['payment_status'] === 'paid') {
            activateSubscriptionForOrder($conn, $orderId);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Order updated successfully'
        ]);
    } else {
        throw new Exception('Order not found or no changes made');
    }
}

/**
 * Phê duyệt đơn hàng (approve payment)
 */
function approveOrder($conn) {
    $orderId = $_POST['order_id'] ?? null;
    
    if (!$orderId) {
        throw new Exception('Order ID is required');
    }
    
    // Cập nhật trạng thái thanh toán và đơn hàng
    $sql = "UPDATE orders SET 
            payment_status = 'paid', 
            status = 'completed',
            paid_at = NOW(),
            completed_at = NOW(),
            updated_at = NOW()
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$orderId]);
    
    if ($stmt->rowCount() > 0) {
        // Kích hoạt subscription
        activateSubscriptionForOrder($conn, $orderId);
        
        echo json_encode([
            'success' => true,
            'message' => 'Order approved and subscription activated'
        ]);
    } else {
        throw new Exception('Order not found');
    }
}

/**
 * Kích hoạt subscription cho đơn hàng
 */
function activateSubscriptionForOrder($conn, $orderId) {
    // Lấy thông tin đơn hàng và order items
    $sql = "
        SELECT o.user_id, oi.plan_id, sp.duration_days, oi.duration_months
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN subscription_plans sp ON oi.plan_id = sp.id
        WHERE o.id = ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($items as $item) {
        // Tính toán ngày hết hạn
        $durationDays = $item['duration_days'] * ($item['duration_months'] ?? 1);
        
        // Tạo subscription mới
        $insertSql = "
            INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status, order_id)
            VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), 'active', ?)
        ";
        
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->execute([
            $item['user_id'],
            $item['plan_id'],
            $durationDays,
            $orderId
        ]);
    }
}
