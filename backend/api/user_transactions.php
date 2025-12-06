<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

try {
    $user_id = $_GET['user_id'] ?? null;
    
    if (!$user_id) {
        throw new Exception('User ID is required');
    }
    
    // Get all orders/transactions for this user
    $sql = "SELECT 
                o.id,
                o.order_code,
                o.user_id,
                o.total as total_price,
                o.discount,
                o.payment_method,
                o.payment_status,
                o.status,
                o.created_at,
                oi.plan_name,
                sp.slug as plan_slug,
                oi.duration_months as duration,
                sp.quality
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN subscription_plans sp ON oi.plan_id = sp.id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        // Format created_at
        $created_at = new DateTime($row['created_at']);
        $row['created_at_formatted'] = $created_at->format('d/m/Y H:i');
        
        // Map payment_method to readable name
        $payment_methods = [
            'vnpay' => 'VNPay',
            'momo' => 'MoMo',
            'zalopay' => 'ZaloPay',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'vietqr' => 'VietQR',
            'cod' => 'Thanh toán khi nhận hàng'
        ];
        $row['payment_method_name'] = $payment_methods[$row['payment_method']] ?? $row['payment_method'];
        
        // Convert duration from months to days (30 days per month)
        $row['duration'] = ($row['duration'] ?? 1) * 30;
        
        $transactions[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'transactions' => $transactions,
        'count' => count($transactions)
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
