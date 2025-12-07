<?php
/**
 * API: Quản lý gói xem phim (Subscription Plans)
 * GET: Lấy danh sách gói
 * POST: Tạo gói mới (Admin)
 * PUT: Cập nhật gói (Admin)
 * DELETE: Xóa gói (Admin)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            getPlans();
            break;
        case 'POST':
            createPlan();
            break;
        case 'PUT':
            updatePlan();
            break;
        case 'DELETE':
            deletePlan();
            break;
        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * GET: Lấy danh sách gói
 */
function getPlans() {
    global $conn;
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $slug = isset($_GET['slug']) ? $_GET['slug'] : null;
    $active_only = isset($_GET['active_only']) ? filter_var($_GET['active_only'], FILTER_VALIDATE_BOOLEAN) : true;
    
    if ($id) {
        // Lấy 1 gói theo ID
        $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $plan = $result->fetch_assoc();
        
        if (!$plan) {
            throw new Exception('Plan not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $plan
        ]);
    } elseif ($slug) {
        // Lấy 1 gói theo slug
        $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE slug = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $plan = $result->fetch_assoc();
        
        if (!$plan) {
            throw new Exception('Plan not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $plan
        ]);
    } else {
        // Lấy tất cả gói
        $sql = "SELECT * FROM subscription_plans";
        if ($active_only) {
            $sql .= " WHERE is_active = TRUE";
        }
        $sql .= " ORDER BY display_order ASC, price ASC";
        
        $result = $conn->query($sql);
        $plans = [];
        
        while ($row = $result->fetch_assoc()) {
            // Convert boolean
            $row['has_ads'] = (bool)$row['has_ads'];
            $row['can_download'] = (bool)$row['can_download'];
            $row['early_access'] = (bool)$row['early_access'];
            $row['is_active'] = (bool)$row['is_active'];
            
            // Format price
            $row['price_formatted'] = number_format($row['price'], 0, ',', '.') . 'đ';
            
            $plans[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $plans,
            'count' => count($plans)
        ]);
    }
}

/**
 * POST: Tạo gói mới (Admin only)
 */
function createPlan() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate
    $required = ['name', 'slug', 'price', 'duration_days', 'quality'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Nếu không có display_order, tự động lấy số lớn nhất + 1
    if (!isset($data['display_order']) || $data['display_order'] === 0) {
        $result = $conn->query("SELECT MAX(display_order) as max_order FROM subscription_plans");
        $row = $result->fetch_assoc();
        $data['display_order'] = ($row['max_order'] ?? 0) + 1;
    }
    
    $stmt = $conn->prepare("
        INSERT INTO subscription_plans 
        (name, slug, description, promotion_badge, promotion_text, price, duration_days, quality, max_devices, has_ads, can_download, early_access, display_order, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    // Convert empty strings to null for optional fields
    $promotion_badge = !empty($data['promotion_badge']) ? $data['promotion_badge'] : null;
    $promotion_text = !empty($data['promotion_text']) ? $data['promotion_text'] : null;
    $description = !empty($data['description']) ? $data['description'] : null;
    
    $stmt->bind_param(
        "ssssdisiiiiii",
        $data['name'],
        $data['slug'],
        $description,
        $promotion_badge,
        $promotion_text,
        $data['price'],
        $data['duration_days'],
        $data['quality'],
        $data['max_devices'] ?? 1,
        $data['has_ads'] ?? 0,
        $data['can_download'] ?? 0,
        $data['early_access'] ?? 0,
        $data['display_order'],
        $data['is_active'] ?? 1
    );
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Plan created successfully',
            'plan_id' => $conn->insert_id
        ]);
    } else {
        throw new Exception('Failed to create plan: ' . $stmt->error);
    }
}

/**
 * PUT: Cập nhật gói (Admin only)
 */
function updatePlan() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('Plan ID is required');
    }
    
    $updates = [];
    $types = "";
    $values = [];
    
    $allowed_fields = [
        'name' => 's', 'slug' => 's', 'description' => 's', 
        'promotion_badge' => 's', 'promotion_text' => 's',
        'price' => 'd', 'duration_days' => 'i', 'quality' => 's', 'max_devices' => 'i',
        'has_ads' => 'i', 'can_download' => 'i', 'early_access' => 'i',
        'display_order' => 'i', 'is_active' => 'i'
    ];
    
    foreach ($allowed_fields as $field => $type) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $types .= $type;
            // Convert empty strings to null for text fields
            if (in_array($field, ['description', 'promotion_badge', 'promotion_text']) && $data[$field] === '') {
                $values[] = null;
            } else {
                $values[] = $data[$field];
            }
        }
    }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    $sql = "UPDATE subscription_plans SET " . implode(', ', $updates) . " WHERE id = ?";
    $types .= 'i';
    $values[] = $data['id'];
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Plan updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update plan: ' . $stmt->error);
    }
}

/**
 * DELETE: Xóa gói (Admin only)
 */
function deletePlan() {
    global $conn;
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        throw new Exception('Plan ID is required');
    }
    
    // Soft delete: chỉ set is_active = false
    $stmt = $conn->prepare("UPDATE subscription_plans SET is_active = FALSE WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Plan deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete plan: ' . $stmt->error);
    }
}
