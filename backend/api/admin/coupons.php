<?php
/**
 * Admin Coupons API
 * Quản lý mã giảm giá
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
            getCoupons($conn);
            break;
        case 'POST':
            createCoupon($conn);
            break;
        case 'PUT':
            updateCoupon($conn);
            break;
        case 'DELETE':
            deleteCoupon($conn);
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

function getCoupons($conn) {
    $sql = "SELECT 
                c.*,
                COUNT(cu.id) as times_used,
                COALESCE(SUM(cu.discount_amount), 0) as total_discount_given
            FROM coupons c
            LEFT JOIN coupon_usage cu ON c.id = cu.coupon_id
            GROUP BY c.id
            ORDER BY c.created_at DESC";
    
    $stmt = $conn->query($sql);
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data
    foreach ($coupons as &$coupon) {
        if ($coupon['discount_type'] === 'percent') {
            $coupon['discount_formatted'] = $coupon['discount_value'] . '%';
        } else {
            $coupon['discount_formatted'] = number_format($coupon['discount_value'], 0, ',', '.') . ' đ';
        }
        
        $coupon['total_discount_given_formatted'] = number_format($coupon['total_discount_given'], 0, ',', '.') . ' đ';
        
        if ($coupon['start_date']) {
            $date = new DateTime($coupon['start_date']);
            $coupon['start_date_formatted'] = $date->format('d/m/Y');
        }
        
        if ($coupon['end_date']) {
            $date = new DateTime($coupon['end_date']);
            $coupon['end_date_formatted'] = $date->format('d/m/Y');
            $coupon['is_expired'] = $date < new DateTime();
        } else {
            $coupon['is_expired'] = false;
        }
        
        $coupon['usage_percent'] = $coupon['usage_limit'] > 0 
            ? ($coupon['usage_count'] / $coupon['usage_limit']) * 100 
            : 0;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $coupons
    ]);
}

function createCoupon($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $required = ['code', 'discount_type', 'discount_value'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $sql = "INSERT INTO coupons 
            (code, description, discount_type, discount_value, min_order_value, max_discount, usage_limit, user_limit, start_date, end_date, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        strtoupper($data['code']),
        $data['description'] ?? '',
        $data['discount_type'],
        $data['discount_value'],
        $data['min_order_value'] ?? 0,
        $data['max_discount'] ?? null,
        $data['usage_limit'] ?? null,
        $data['user_limit'] ?? 1,
        $data['start_date'] ?? date('Y-m-d H:i:s'),
        $data['end_date'] ?? null,
        $data['is_active'] ?? 1
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Tạo mã giảm giá thành công',
        'id' => $conn->lastInsertId()
    ]);
}

function updateCoupon($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('Missing coupon id');
    }
    
    $updates = [];
    $params = [];
    
    $fields = ['code', 'description', 'discount_type', 'discount_value', 'min_order_value', 'max_discount', 'usage_limit', 'user_limit', 'start_date', 'end_date', 'is_active'];
    
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = $field === 'code' ? strtoupper($data[$field]) : $data[$field];
        }
    }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    $params[] = $data['id'];
    $sql = "UPDATE coupons SET " . implode(', ', $updates) . " WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật mã giảm giá thành công'
    ]);
}

function deleteCoupon($conn) {
    $coupon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if (!$coupon_id) {
        throw new Exception('Missing coupon_id');
    }
    
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->execute([$coupon_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Xóa mã giảm giá thành công'
    ]);
}
