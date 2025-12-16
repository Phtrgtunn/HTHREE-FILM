<?php
/**
 * Check Bank Transactions (MB Bank API)
 * Kiểm tra giao dịch thực tế từ MB Bank API
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
    
    // Kiểm tra giao dịch từ MB Bank API
    $transactions = checkMBBankTransactions($orderCode, $amount);
    
    if (!empty($transactions)) {
        // Tìm thấy giao dịch khớp
        $transaction = $transactions[0];
        
        // Tự động approve đơn hàng
        $approved = approveOrderByBank($conn, $orderCode, $transaction);
        
        if ($approved) {
            echo json_encode([
                'success' => true,
                'message' => 'Real payment detected and order approved',
                'transaction' => $transaction
            ]);
        } else {
            throw new Exception('Failed to approve order');
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No matching transaction found yet',
            'checking' => true
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
 * Kiểm tra giao dịch từ MB Bank API
 */
function checkMBBankTransactions($orderCode, $expectedAmount) {
    // Thông tin tài khoản MB Bank (cần cập nhật thông tin thật)
    $accountNo = BANK_ACCOUNT_NO; // Từ config_payment.php
    
    // Gọi MB Bank API (cần token thật)
    // Đây là ví dụ - cần thay bằng API thật của MB Bank
    $apiUrl = 'https://online.mbbank.com.vn/api/retail-web-internetbankingms/getCasaAccountTransactionHistory';
    
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer YOUR_MB_BANK_TOKEN', // Cần token thật
        'X-Request-Id: ' . uniqid(),
        'DeviceId: YOUR_DEVICE_ID'
    ];
    
    $postData = [
        'accountNo' => $accountNo,
        'fromDate' => date('d/m/Y', strtotime('-1 day')), // 1 ngày trước
        'toDate' => date('d/m/Y'),
        'historyNumber' => '',
        'historyType' => 'DATE_RANGE'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Log để debug
    error_log("MB Bank API Response: " . $response);
    
    if ($httpCode !== 200) {
        return [];
    }
    
    $data = json_decode($response, true);
    
    if (!$data || $data['result']['responseCode'] !== '00') {
        return [];
    }
    
    $matchingTransactions = [];
    
    foreach ($data['transactionHistoryList'] as $transaction) {
        $description = $transaction['description'] ?? '';
        $creditAmount = $transaction['creditAmount'] ?? 0;
        
        // Kiểm tra nội dung chuyển khoản có chứa order code và số tiền khớp
        if (stripos($description, $orderCode) !== false && $creditAmount == $expectedAmount) {
            $matchingTransactions[] = [
                'id' => $transaction['refNo'],
                'amount' => $creditAmount,
                'description' => $description,
                'transactionDate' => $transaction['transactionDate'],
                'type' => 'mb_bank_api'
            ];
        }
    }
    
    return $matchingTransactions;
}

/**
 * Approve đơn hàng khi tìm thấy giao dịch thật
 */
function approveOrderByBank($conn, $orderCode, $transaction) {
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
            'Thanh toán qua VietQR - MB Bank API (Phát hiện giao dịch thực tế)',
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
        error_log("Error approving order: " . $e->getMessage());
        return false;
    }
}