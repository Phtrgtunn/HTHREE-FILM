<?php
/**
 * Admin Users API
 * Quản lý người dùng
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            getUsers($conn);
            break;
        case 'PUT':
            updateUser($conn);
            break;
        case 'DELETE':
            deleteUser($conn);
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

function getUsers($conn) {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    $sql = "SELECT 
                u.id,
                u.username,
                u.email,
                u.full_name,
                u.role,
                u.is_active,
                u.created_at,
                u.last_login,
                COUNT(DISTINCT o.id) as total_orders,
                COALESCE(SUM(CASE WHEN o.payment_status = 'paid' THEN o.total ELSE 0 END), 0) as total_spent,
                COUNT(DISTINCT us.id) as active_subscriptions
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            LEFT JOIN user_subscriptions us ON u.id = us.user_id AND us.status = 'active'
            WHERE 1=1";
    
    $params = [];
    
    if ($search) {
        $sql .= " AND (u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    $sql .= " GROUP BY u.id ORDER BY u.created_at DESC LIMIT " . $limit;
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data
    foreach ($users as &$user) {
        $user['total_spent_formatted'] = number_format($user['total_spent'], 0, ',', '.') . ' đ';
        if ($user['created_at']) {
            $date = new DateTime($user['created_at']);
            $user['created_at_formatted'] = $date->format('d/m/Y H:i');
        }
        if ($user['last_login']) {
            $date = new DateTime($user['last_login']);
            $user['last_login_formatted'] = $date->format('d/m/Y H:i');
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $users,
        'count' => count($users)
    ]);
}

function updateUser($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id'])) {
        throw new Exception('Missing user_id');
    }
    
    $user_id = intval($data['user_id']);
    $updates = [];
    $params = [];
    
    if (isset($data['role'])) {
        $updates[] = "role = ?";
        $params[] = $data['role'];
    }
    
    if (isset($data['is_active'])) {
        $updates[] = "is_active = ?";
        $params[] = $data['is_active'] ? 1 : 0;
    }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    $params[] = $user_id;
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật người dùng thành công'
    ]);
}

function deleteUser($conn) {
    $user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if (!$user_id) {
        throw new Exception('Missing user_id');
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Xóa người dùng thành công'
    ]);
}
