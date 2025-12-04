<?php
/**
 * API: Quản lý giỏ hàng
 * GET: Lấy giỏ hàng của user
 * POST: Thêm gói vào giỏ
 * PUT: Cập nhật số lượng
 * DELETE: Xóa khỏi giỏ
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

/**
 * Helper: Get numeric user ID from Firebase UID or numeric ID
 */
function getUserId($input) {
    global $conn;
    
    // If already numeric, return it
    if (is_numeric($input)) {
        return intval($input);
    }
    
    // If Firebase UID (string), try to find by firebase_uid
    $stmt = $conn->prepare("SELECT id FROM users WHERE firebase_uid = ? LIMIT 1");
    $stmt->bind_param("s", $input);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return intval($row['id']);
    }
    
    // Not found, create a temporary user with Firebase UID
    // This is a fallback - ideally user should be created during login
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, firebase_uid) VALUES (?, ?, ?, ?)");
    $username = 'user_' . substr($input, 0, 8);
    $email = $username . '@firebase.temp';
    $password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
    $stmt->bind_param("ssss", $username, $email, $password, $input);
    
    if ($stmt->execute()) {
        return intval($conn->insert_id);
    }
    
    // Failed, return 0
    return 0;
}

try {
    switch ($method) {
        case 'GET':
            getCart();
            break;
        case 'POST':
            addToCart();
            break;
        case 'PUT':
            updateCart();
            break;
        case 'DELETE':
            removeFromCart();
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
 * GET: Lấy giỏ hàng
 */
function getCart() {
    global $conn;
    
    $user_id = isset($_GET['user_id']) ? getUserId($_GET['user_id']) : null;
    
    if (!$user_id) {
        throw new Exception('User ID is required');
    }
    
    $stmt = $conn->prepare("
        SELECT 
            c.id,
            c.user_id,
            c.plan_id,
            c.quantity,
            sp.name as plan_name,
            sp.slug as plan_slug,
            sp.description,
            sp.price,
            sp.duration_days,
            sp.quality,
            sp.max_devices,
            sp.has_ads,
            sp.can_download,
            sp.early_access,
            (sp.price * c.quantity) as subtotal,
            c.created_at
        FROM cart c
        JOIN subscription_plans sp ON c.plan_id = sp.id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
    ");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        // Convert boolean
        $row['has_ads'] = (bool)$row['has_ads'];
        $row['can_download'] = (bool)$row['can_download'];
        $row['early_access'] = (bool)$row['early_access'];
        
        // Format price
        $row['price_formatted'] = number_format($row['price'], 0, ',', '.') . 'đ';
        $row['subtotal_formatted'] = number_format($row['subtotal'], 0, ',', '.') . 'đ';
        
        $total += $row['subtotal'];
        $items[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'items' => $items,
            'count' => count($items),
            'total' => $total,
            'total_formatted' => number_format($total, 0, ',', '.') . 'đ'
        ]
    ]);
}

/**
 * POST: Thêm vào giỏ
 */
function addToCart() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id']) || !isset($data['plan_id'])) {
        throw new Exception('User ID and Plan ID are required');
    }
    
    $user_id = getUserId($data['user_id']);
    $plan_id = intval($data['plan_id']);
    $quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;
    $duration_months = isset($data['duration_months']) ? intval($data['duration_months']) : 1;
    
    if (!$user_id) {
        throw new Exception('Invalid user ID');
    }
    
    // Kiểm tra plan có tồn tại không
    $stmt = $conn->prepare("SELECT id, name, price FROM subscription_plans WHERE id = ? AND is_active = TRUE");
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Plan not found or inactive');
    }
    
    $plan = $result->fetch_assoc();
    
    // Kiểm tra đã có trong giỏ chưa
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND plan_id = ?");
    $stmt->bind_param("ii", $user_id, $plan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Đã có -> cập nhật số lượng
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        $stmt = $conn->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
        $stmt->execute();
        
        $message = 'Cart updated successfully';
        $cart_id = $cart_item['id'];
    } else {
        // Chưa có -> thêm mới
        $stmt = $conn->prepare("INSERT INTO cart (user_id, plan_id, quantity, duration_months) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $user_id, $plan_id, $quantity, $duration_months);
        $stmt->execute();
        
        $message = 'Added to cart successfully';
        $cart_id = $conn->insert_id;
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => [
            'cart_id' => $cart_id,
            'plan_name' => $plan['name'],
            'quantity' => $quantity
        ]
    ]);
}

/**
 * PUT: Cập nhật số lượng
 */
function updateCart() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['cart_id']) || !isset($data['quantity'])) {
        throw new Exception('Cart ID and quantity are required');
    }
    
    $cart_id = intval($data['cart_id']);
    $quantity = intval($data['quantity']);
    
    if ($quantity < 1) {
        throw new Exception('Quantity must be at least 1');
    }
    
    if ($quantity > 12) {
        throw new Exception('Maximum quantity is 12 months');
    }
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $cart_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Cart updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update cart');
    }
}

/**
 * DELETE: Xóa khỏi giỏ
 */
function removeFromCart() {
    global $conn;
    
    $cart_id = isset($_GET['cart_id']) ? intval($_GET['cart_id']) : null;
    $user_id = isset($_GET['user_id']) ? getUserId($_GET['user_id']) : null;
    
    if ($cart_id) {
        // Xóa 1 item
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->bind_param("i", $cart_id);
        $message = 'Item removed from cart';
    } elseif ($user_id) {
        // Xóa toàn bộ giỏ
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $message = 'Cart cleared';
    } else {
        throw new Exception('Cart ID or User ID is required');
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
    } else {
        throw new Exception('Failed to remove from cart');
    }
}
