<?php
/**
 * API: Quản lý mã giảm giá
 * GET: Lấy danh sách mã
 * POST: Kiểm tra & áp dụng mã
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
            getCoupons();
            break;
        case 'POST':
            validateCoupon();
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
 * GET: Lấy danh sách mã giảm giá
 */
function getCoupons() {
    global $conn;
    
    $active_only = isset($_GET['active_only']) ? filter_var($_GET['active_only'], FILTER_VALIDATE_BOOLEAN) : true;
    
    $sql = "SELECT * FROM coupons WHERE 1=1";
    
    if ($active_only) {
        $sql .= " AND is_active = TRUE";
        $sql .= " AND start_date <= NOW()";
        $sql .= " AND (end_date IS NULL OR end_date >= NOW())";
        $sql .= " AND (usage_limit IS NULL OR usage_count < usage_limit)";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $result = $conn->query($sql);
    $coupons = [];
    
    while ($row = $result->fetch_assoc()) {
        $row['is_active'] = (bool)$row['is_active'];
        $row['discount_value_formatted'] = $row['discount_type'] === 'percent' 
            ? $row['discount_value'] . '%' 
            : number_format($row['discount_value'], 0, ',', '.') . 'đ';
        
        if ($row['min_order_value']) {
            $row['min_order_value_formatted'] = number_format($row['min_order_value'], 0, ',', '.') . 'đ';
        }
        
        if ($row['max_discount']) {
            $row['max_discount_formatted'] = number_format($row['max_discount'], 0, ',', '.') . 'đ';
        }
        
        $coupons[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $coupons,
        'count' => count($coupons)
    ]);
}

/**
 * POST: Kiểm tra & tính giảm giá
 */
function validateCoupon() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['code']) || !isset($data['order_value'])) {
        throw new Exception('Coupon code and order value are required');
    }
    
    $code = strtoupper(trim($data['code']));
    $order_value = floatval($data['order_value']);
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
    
    // Lấy thông tin coupon
    $stmt = $conn->prepare("
        SELECT * FROM coupons 
        WHERE code = ? 
        AND is_active = TRUE
        AND start_date <= NOW()
        AND (end_date IS NULL OR end_date >= NOW())
        AND (usage_limit IS NULL OR usage_count < usage_limit)
    ");
    
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Mã giảm giá không hợp lệ hoặc đã hết hạn');
    }
    
    $coupon = $result->fetch_assoc();
    
    // Kiểm tra giá trị đơn tối thiểu
    if ($order_value < $coupon['min_order_value']) {
        throw new Exception('Đơn hàng tối thiểu ' . number_format($coupon['min_order_value'], 0, ',', '.') . 'đ để dùng mã này');
    }
    
    // Kiểm tra user đã dùng chưa
    if ($user_id && $coupon['user_limit']) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as usage_count 
            FROM coupon_usage 
            WHERE coupon_id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $coupon['id'], $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $usage = $result->fetch_assoc();
        
        if ($usage['usage_count'] >= $coupon['user_limit']) {
            throw new Exception('Bạn đã sử dụng mã này rồi');
        }
    }
    
    // Tính giảm giá
    if ($coupon['discount_type'] === 'percent') {
        $discount = $order_value * $coupon['discount_value'] / 100;
        
        // Giảm tối đa
        if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
            $discount = $coupon['max_discount'];
        }
    } else {
        $discount = $coupon['discount_value'];
    }
    
    // Không giảm quá giá trị đơn
    if ($discount > $order_value) {
        $discount = $order_value;
    }
    
    $final_total = $order_value - $discount;
    
    echo json_encode([
        'success' => true,
        'message' => 'Áp dụng mã giảm giá thành công',
        'data' => [
            'coupon_id' => $coupon['id'],
            'code' => $coupon['code'],
            'description' => $coupon['description'],
            'discount_type' => $coupon['discount_type'],
            'discount_value' => $coupon['discount_value'],
            'discount_amount' => $discount,
            'discount_amount_formatted' => number_format($discount, 0, ',', '.') . 'đ',
            'order_value' => $order_value,
            'final_total' => $final_total,
            'final_total_formatted' => number_format($final_total, 0, ',', '.') . 'đ'
        ]
    ]);
}
