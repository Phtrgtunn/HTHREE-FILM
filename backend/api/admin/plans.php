<?php
/**
 * Admin Plans API
 * Quản lý gói dịch vụ
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
            getPlans($conn);
            break;
        case 'POST':
            createPlan($conn);
            break;
        case 'PUT':
            updatePlan($conn);
            break;
        case 'DELETE':
            deletePlan($conn);
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

function getPlans($conn) {
    $sql = "SELECT 
                sp.*,
                COUNT(DISTINCT oi.id) as times_sold,
                COALESCE(SUM(oi.total), 0) as total_revenue,
                COUNT(DISTINCT us.id) as active_subscriptions
            FROM subscription_plans sp
            LEFT JOIN order_items oi ON sp.id = oi.plan_id
            LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'paid'
            LEFT JOIN user_subscriptions us ON sp.id = us.plan_id AND us.status = 'active'
            GROUP BY sp.id
            ORDER BY sp.display_order ASC";
    
    $stmt = $conn->query($sql);
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data
    foreach ($plans as &$plan) {
        $plan['price_formatted'] = number_format($plan['price'], 0, ',', '.') . ' đ';
        $plan['total_revenue_formatted'] = number_format($plan['total_revenue'], 0, ',', '.') . ' đ';
    }
    
    echo json_encode([
        'success' => true,
        'data' => $plans
    ]);
}

function createPlan($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $required = ['name', 'slug', 'price', 'duration_days'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $sql = "INSERT INTO subscription_plans 
            (name, slug, description, price, duration_days, quality, max_devices, has_ads, can_download, early_access, is_active, display_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $data['name'],
        $data['slug'],
        $data['description'] ?? '',
        $data['price'],
        $data['duration_days'],
        $data['quality'] ?? 'HD',
        $data['max_devices'] ?? 1,
        $data['has_ads'] ?? 0,
        $data['can_download'] ?? 0,
        $data['early_access'] ?? 0,
        $data['is_active'] ?? 1,
        $data['display_order'] ?? 0
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Tạo gói dịch vụ thành công',
        'id' => $conn->lastInsertId()
    ]);
}

function updatePlan($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('Missing plan id');
    }
    
    $updates = [];
    $params = [];
    
    $fields = ['name', 'slug', 'description', 'price', 'duration_days', 'quality', 'max_devices', 'has_ads', 'can_download', 'early_access', 'is_active', 'display_order'];
    
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = $data[$field];
        }
    }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    $params[] = $data['id'];
    $sql = "UPDATE subscription_plans SET " . implode(', ', $updates) . " WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật gói dịch vụ thành công'
    ]);
}

function deletePlan($conn) {
    $plan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if (!$plan_id) {
        throw new Exception('Missing plan_id');
    }
    
    $stmt = $conn->prepare("DELETE FROM subscription_plans WHERE id = ?");
    $stmt->execute([$plan_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Xóa gói dịch vụ thành công'
    ]);
}
