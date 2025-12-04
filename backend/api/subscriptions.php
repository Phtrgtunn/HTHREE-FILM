<?php
/**
 * API: Quản lý gói đăng ký của user
 * GET: Lấy gói đang dùng
 * POST: Kích hoạt gói thủ công (Admin)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
            getSubscriptions();
            break;
        case 'POST':
            activateSubscription();
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
 * GET: Lấy gói đăng ký
 */
function getSubscriptions() {
    global $conn;
    
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    $check_active = isset($_GET['check_active']) ? filter_var($_GET['check_active'], FILTER_VALIDATE_BOOLEAN) : false;
    
    if (!$user_id) {
        throw new Exception('User ID is required');
    }
    
    if ($check_active) {
        // Chỉ kiểm tra có gói active không
        $has_active = checkActiveSubscription($user_id);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'has_active_subscription' => $has_active,
                'user_id' => $user_id
            ]
        ]);
    } else {
        // Lấy tất cả gói của user
        $stmt = $conn->prepare("
            SELECT 
                us.*,
                sp.name as plan_name,
                sp.slug as plan_slug,
                sp.price,
                sp.quality,
                sp.max_devices,
                sp.has_ads,
                sp.can_download,
                sp.early_access,
                DATEDIFF(us.end_date, NOW()) as days_remaining,
                CASE 
                    WHEN us.status = 'active' AND us.end_date > NOW() THEN TRUE
                    ELSE FALSE
                END as is_active
            FROM user_subscriptions us
            JOIN subscription_plans sp ON us.plan_id = sp.id
            WHERE us.user_id = ?
            ORDER BY us.created_at DESC
        ");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $subscriptions = [];
        $active_subscription = null;
        
        while ($row = $result->fetch_assoc()) {
            // Convert boolean
            $row['has_ads'] = (bool)$row['has_ads'];
            $row['can_download'] = (bool)$row['can_download'];
            $row['early_access'] = (bool)$row['early_access'];
            $row['auto_renew'] = (bool)$row['auto_renew'];
            $row['is_active'] = (bool)$row['is_active'];
            
            // Format dates
            $row['start_date_formatted'] = date('d/m/Y', strtotime($row['start_date']));
            $row['end_date_formatted'] = date('d/m/Y', strtotime($row['end_date']));
            
            if ($row['is_active']) {
                $active_subscription = $row;
            }
            
            $subscriptions[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'active_subscription' => $active_subscription,
                'all_subscriptions' => $subscriptions,
                'count' => count($subscriptions)
            ]
        ]);
    }
}

/**
 * POST: Kích hoạt gói thủ công (Admin only)
 */
function activateSubscription() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $required = ['user_id', 'plan_id', 'duration_months'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $user_id = intval($data['user_id']);
    $plan_id = intval($data['plan_id']);
    $duration_months = intval($data['duration_months']);
    
    // Lấy thông tin plan
    $stmt = $conn->prepare("SELECT duration_days FROM subscription_plans WHERE id = ?");
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Plan not found');
    }
    
    $plan = $result->fetch_assoc();
    $total_days = $plan['duration_days'] * $duration_months;
    
    // Tạo subscription
    $stmt = $conn->prepare("
        INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status)
        VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), 'active')
    ");
    
    $stmt->bind_param("iii", $user_id, $plan_id, $total_days);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Subscription activated successfully',
            'subscription_id' => $conn->insert_id
        ]);
    } else {
        throw new Exception('Failed to activate subscription: ' . $stmt->error);
    }
}

/**
 * Kiểm tra user có gói active không
 */
function checkActiveSubscription($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT fn_has_active_subscription(?) as has_active");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return (bool)$row['has_active'];
}
