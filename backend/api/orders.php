<?php
/**
 * API: Quản lý đơn hàng
 * GET: Lấy danh sách đơn hàng
 * POST: Tạo đơn hàng mới
 * PUT: Cập nhật trạng thái đơn hàng
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
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
            getOrders();
            break;
        case 'POST':
            createOrder();
            break;
        case 'PUT':
            updateOrder();
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
 * GET: Lấy danh sách đơn hàng hoặc chi tiết đơn hàng
 */
function getOrders() {
    global $conn;
    
    // Nếu có order_id, trả về chi tiết đơn hàng và items
    if (isset($_GET['order_id'])) {
        $order_id = intval($_GET['order_id']);
        
        // Lấy thông tin order items
        $sql = "SELECT oi.*, sp.name as plan_name, sp.slug as plan_slug
                FROM order_items oi
                JOIN subscription_plans sp ON oi.plan_id = sp.id
                WHERE oi.order_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode([
            'success' => true,
            'items' => $items
        ]);
        return;
    }
    
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
    $order_code = isset($_GET['order_code']) ? $_GET['order_code'] : null;
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    
    if ($order_id) {
        // Lấy 1 đơn hàng chi tiết
        getOrderDetail($order_id);
    } elseif ($order_code) {
        // Lấy đơn theo mã
        $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ?");
        $stmt->bind_param("s", $order_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => formatOrder($order)
        ]);
    } else {
        // Lấy danh sách đơn hàng
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];
        $types = "";
        
        if ($user_id) {
            $sql .= " AND user_id = ?";
            $types .= "i";
            $params[] = $user_id;
        }
        
        if ($status) {
            $sql .= " AND status = ?";
            $types .= "s";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = formatOrder($row);
        }
        
        echo json_encode([
            'success' => true,
            'data' => $orders,
            'count' => count($orders)
        ]);
    }
}

/**
 * Lấy chi tiết đơn hàng (bao gồm items)
 */
function getOrderDetail($order_id) {
    global $conn;
    
    // Lấy thông tin đơn hàng
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if (!$order) {
        throw new Exception('Order not found');
    }
    
    // Lấy items
    $stmt = $conn->prepare("
        SELECT 
            oi.*,
            sp.slug as plan_slug,
            sp.quality,
            sp.max_devices
        FROM order_items oi
        LEFT JOIN subscription_plans sp ON oi.plan_id = sp.id
        WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $row['price_formatted'] = number_format($row['price'], 0, ',', '.') . 'đ';
        $row['total_formatted'] = number_format($row['total'], 0, ',', '.') . 'đ';
        $items[] = $row;
    }
    
    $order = formatOrder($order);
    $order['items'] = $items;
    
    echo json_encode([
        'success' => true,
        'data' => $order
    ]);
}

/**
 * POST: Tạo đơn hàng mới
 */
function createOrder() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate
    $required = ['user_id', 'customer_name', 'customer_email', 'payment_method'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $user_id = intval($data['user_id']);
    $customer_name = $data['customer_name'];
    $customer_email = $data['customer_email'];
    $customer_phone = $data['customer_phone'] ?? null;
    $payment_method = $data['payment_method'];
    $coupon_code = $data['coupon_code'] ?? null;
    $note = $data['note'] ?? null;
    
    // Kiểm tra xem có mua trực tiếp không (không qua giỏ hàng)
    if (isset($data['plan_id']) && isset($data['duration_months'])) {
        // Mua trực tiếp
        createDirectOrder($data);
        return;
    }
    
    // Tạo đơn hàng không dùng stored procedure
    $conn->begin_transaction();
    
    try {
        // Tạo mã đơn hàng
        $order_code = 'ORD' . date('Ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        // Tính tổng tiền từ giỏ hàng
        $stmt = $conn->prepare("
            SELECT SUM(sp.price * c.quantity) as subtotal, COUNT(*) as item_count
            FROM cart c
            JOIN subscription_plans sp ON c.plan_id = sp.id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_total = $result->fetch_assoc();
        $subtotal = floatval($cart_total['subtotal'] ?? 0);
        $item_count = intval($cart_total['item_count'] ?? 0);
        
        if ($item_count == 0 || $subtotal == 0) {
            throw new Exception('Cart is empty. Please add items to cart before checkout.');
        }
        
        // Áp dụng mã giảm giá (nếu có)
        $discount = 0;
        $coupon_id = null;
        if ($coupon_code) {
            $stmt = $conn->prepare("
                SELECT id, discount_type, discount_value, max_discount
                FROM coupons
                WHERE code = ? AND is_active = 1
                AND (end_date IS NULL OR end_date >= NOW())
                AND (usage_limit IS NULL OR usage_count < usage_limit)
            ");
            $stmt->bind_param("s", $coupon_code);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $coupon = $result->fetch_assoc();
                $coupon_id = $coupon['id'];
                
                if ($coupon['discount_type'] == 'percent') {
                    $discount = $subtotal * ($coupon['discount_value'] / 100);
                } else {
                    $discount = $coupon['discount_value'];
                }
                
                if ($coupon['max_discount']) {
                    $discount = min($discount, $coupon['max_discount']);
                }
            }
        }
        
        $total = $subtotal - $discount;
        
        // Tạo đơn hàng
        $stmt = $conn->prepare("
            INSERT INTO orders (
                order_code, user_id, customer_name, customer_email, customer_phone,
                subtotal, discount, total, payment_method, payment_status, status, note,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, NOW(), NOW())
        ");
        $stmt->bind_param(
            "sisssdddss",
            $order_code, $user_id, $customer_name, $customer_email, $customer_phone,
            $subtotal, $discount, $total, $payment_method, $note
        );
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Thêm chi tiết đơn hàng từ giỏ
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, plan_id, plan_name, plan_price, duration_months, quantity, price, total, created_at)
            SELECT ?, c.plan_id, sp.name, sp.price, c.quantity, 1, sp.price, sp.price * c.quantity, NOW()
            FROM cart c
            JOIN subscription_plans sp ON c.plan_id = sp.id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        
        // Cập nhật coupon usage
        if ($coupon_id) {
            $stmt = $conn->prepare("UPDATE coupons SET usage_count = usage_count + 1 WHERE id = ?");
            $stmt->bind_param("i", $coupon_id);
            $stmt->execute();
            
            $stmt = $conn->prepare("INSERT INTO coupon_usage (coupon_id, user_id, order_id, discount_amount, used_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("iiid", $coupon_id, $user_id, $order_id, $discount);
            $stmt->execute();
        }
        
        // Xóa giỏ hàng
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $conn->commit();
        
        // Lấy thông tin đơn hàng vừa tạo
        getOrderDetail($order_id);
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

/**
 * Tạo đơn hàng trực tiếp (không qua giỏ hàng)
 */
function createDirectOrder($data) {
    global $conn;
    
    $user_id = intval($data['user_id']);
    $plan_id = intval($data['plan_id']);
    $duration_months = intval($data['duration_months']);
    
    // Debug log
    error_log("Creating direct order - user_id: $user_id, plan_id: $plan_id, duration: $duration_months");
    
    // Kiểm tra user có tồn tại không
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        error_log("User not found with ID: $user_id");
        throw new Exception("User not found (ID: $user_id). Please login again.");
    }
    
    $user = $result->fetch_assoc();
    error_log("User found: " . json_encode($user));
    
    // Lấy thông tin plan
    $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE id = ?");
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Plan not found');
    }
    
    $plan = $result->fetch_assoc();
    
    // Tính giá
    $subtotal = $plan['price'] * $duration_months;
    
    // Nếu frontend gửi total_price (đã tính discount), sử dụng nó
    if (isset($data['total_price']) && $data['total_price'] > 0) {
        $total = floatval($data['total_price']);
        $discount = $subtotal - $total; // Tính ngược discount
    } else {
        // Fallback: Tính discount dựa trên duration
        $discount_percent = 0;
        if ($duration_months == 3) {
            $discount_percent = 5;
        } elseif ($duration_months == 6) {
            $discount_percent = 10;
        } elseif ($duration_months == 12) {
            $discount_percent = 15;
        }
        $discount = $subtotal * ($discount_percent / 100);
        $total = $subtotal - $discount;
    }
    
    // Tạo mã đơn hàng
    $order_code = 'ORD' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    // Tạo đơn hàng
    $customer_phone = isset($data['customer_phone']) ? $data['customer_phone'] : null;
    
    $stmt = $conn->prepare("
        INSERT INTO orders (
            order_code, user_id, customer_name, customer_email, customer_phone,
            subtotal, discount, total, payment_method, status, payment_status,
            created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', NOW(), NOW())
    ");
    
    $stmt->bind_param(
        "sisssddss",
        $order_code,
        $user_id,
        $data['customer_name'],
        $data['customer_email'],
        $customer_phone,
        $subtotal,
        $discount,
        $total,
        $data['payment_method']
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create order: ' . $stmt->error);
    }
    
    $order_id = $conn->insert_id;
    
    // Thêm order item
    $stmt = $conn->prepare("
        INSERT INTO order_items (
            order_id, plan_id, plan_name, plan_price, duration_months, quantity, price, total, created_at
        ) VALUES (?, ?, ?, ?, ?, 1, ?, ?, NOW())
    ");
    
    $stmt->bind_param(
        "iisdidd",
        $order_id,
        $plan_id,
        $plan['name'],
        $plan['price'],
        $duration_months,
        $total,
        $total
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create order item: ' . $stmt->error);
    }
    
    // Trả về thông tin đơn hàng
    getOrderDetail($order_id);
}

/**
 * PUT: Cập nhật trạng thái đơn hàng
 */
function updateOrder() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['order_id'])) {
        throw new Exception('Order ID is required');
    }
    
    $order_id = intval($data['order_id']);
    
    // Kiểm tra đơn hàng tồn tại
    $stmt = $conn->prepare("SELECT id, status, payment_status FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Order not found');
    }
    
    $order = $result->fetch_assoc();
    
    // Cập nhật các trường
    $updates = [];
    $types = "";
    $values = [];
    
    // Payment status
    if (isset($data['payment_status'])) {
        $updates[] = "payment_status = ?";
        $types .= "s";
        $values[] = $data['payment_status'];
        
        if ($data['payment_status'] === 'paid' && $order['payment_status'] !== 'paid') {
            $updates[] = "paid_at = NOW()";
        }
    }
    
    // Order status
    if (isset($data['status'])) {
        $updates[] = "status = ?";
        $types .= "s";
        $values[] = $data['status'];
        
        if ($data['status'] === 'completed' && $order['status'] !== 'completed') {
            $updates[] = "completed_at = NOW()";
        } elseif ($data['status'] === 'cancelled' && $order['status'] !== 'cancelled') {
            $updates[] = "cancelled_at = NOW()";
        }
    }
    
    // Admin note
    if (isset($data['admin_note'])) {
        $updates[] = "admin_note = ?";
        $types .= "s";
        $values[] = $data['admin_note'];
    }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    $sql = "UPDATE orders SET " . implode(', ', $updates) . " WHERE id = ?";
    $types .= "i";
    $values[] = $order_id;
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Order updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update order: ' . $stmt->error);
    }
}

/**
 * Format order data
 */
function formatOrder($order) {
    $order['subtotal_formatted'] = number_format($order['subtotal'], 0, ',', '.') . 'đ';
    $order['discount_formatted'] = number_format($order['discount'], 0, ',', '.') . 'đ';
    $order['total_formatted'] = number_format($order['total'], 0, ',', '.') . 'đ';
    
    return $order;
}
