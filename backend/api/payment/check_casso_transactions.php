<?php
/**
 * Check Casso Transactions
 * Kiểm tra giao dịch thực tế từ Casso API
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/config_payment.php';

$db = new Database();
$conn = $db->getConnection();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $orderCode = $input['order_code'] ?? null;
    $amount = $input['amount'] ?? null;
    
    if (!$orderCode || !$amount) {
        throw new Exception('Order code and amount are required');
    }
    
    // Gọi Casso API để kiểm tra giao dịch
    $transactions = getCassoTransactions($orderCode, $amount);
    
    if (!empty($transactions)) {
        // Tìm thấy giao dịch khớp
        $transaction = $transactions[0];
        
        // Tự động approve đơn hàng
        $approved = approveOrderByCasso($conn, $orderCode, $transaction);
        
        if ($approved) {
            echo json_encode([
                'success' => true,
                'message' => 'Payment confirmed and order approved',
                'transaction' => $transaction
            ]);
        } else {
            throw new Exception('Failed to approve order');
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No matching transaction found',
            'found_transactions' => []
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Gọi Casso API để lấy danh sách giao dịch
 */
function getCassoTransactions($orderCode, $expectedAmount) {
    $apiKey = CASSO_API_KEY;
    
    if (!$apiKey || $apiKey === 'YOUR_CASSO_API_KEY') {
        // Fallback: Không có API key thật, return empty
        return [];
    }
    
    // Gọi Casso API
    $url = 'https://oauth.casso.vn/v2/transactions';
    $headers = [
        'Authorization: Apikey ' . $apiKey,
        'Content-Type: application/json'
    ];
    
    // Tìm giao dịch trong 24h gần nhất
    $fromDate = date('Y-m-d', strtotime('-1 day'));
    $toDate = date('Y-m-d');
    
    $params = [
        'fromDate' => $fromDate,
        'toDate' => $toDate,
        'page' => 1,
        'pageSize' => 100
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return [];
    }
    
    $data = json_decode($response, true);
    
    if (!$data || !isset($data['data']['records'])) {
        return [];
    }
    
    $matchingTransactions = [];
    
    foreach ($data['data']['records'] as $transaction) {
        $description = $transaction['description'] ?? '';
        $amount = $transaction['amount'] ?? 0;
        
        // Kiểm tra nội dung chuyển khoản có chứa order code
        if (stripos($description, $orderCode) !== false && $amount == $expectedAmount) {
            $matchingTransactions[] = $transaction;
        }
    }
    
    return $matchingTransactions;
}

/**
 * Approve đơn hàng khi tìm thấy giao dịch Casso
 */
function approveOrderByCasso($conn, $orderCode, $transaction) {
    try {
        // Tìm đơn hàng
        $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ? AND payment_status = 'pending'");
        $stmt->execute([$orderCode]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            return false;
        }
        
        // Bắt đầu transaction
        $conn->beginTransaction();
        
        // 1. Update order status
        $stmt = $conn->prepare("
            UPDATE orders 
            SET payment_status = 'paid',
                status = 'completed',
                paid_at = NOW(),
                completed_at = NOW(),
                transaction_id = ?,
                payment_note = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $transaction['id'],
            'Thanh toán qua VietQR - Casso (Tự động phát hiện)',
            $order['id']
        ]);
        
        // 2. Kích hoạt subscription
        $stmt = $conn->prepare("
            SELECT oi.plan_id, oi.duration_months, sp.duration_days
            FROM order_items oi
            JOIN subscription_plans sp ON oi.plan_id = sp.id
            WHERE oi.order_id = ?
            LIMIT 1
        ");
        $stmt->execute([$order['id']]);
        $orderItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($orderItem) {
            $totalDays = $orderItem['duration_days'] * $orderItem['duration_months'];
            $endDate = date('Y-m-d H:i:s', strtotime("+{$totalDays} days"));
            
            $stmt = $conn->prepare("
                INSERT INTO user_subscriptions 
                (user_id, plan_id, start_date, end_date, status, order_id)
                VALUES (?, ?, NOW(), ?, 'active', ?)
            ");
            
            $stmt->execute([
                $order['user_id'],
                $orderItem['plan_id'],
                $endDate,
                $order['id']
            ]);
        }
        
        // Commit transaction
        $conn->commit();
        
        return true;
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        return false;
    }
}