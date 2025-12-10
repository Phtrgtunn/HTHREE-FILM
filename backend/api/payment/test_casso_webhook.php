<?php
/**
 * Test Casso Webhook
 * Giả lập giao dịch từ Casso để test auto-approval
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

$db = new Database();
$conn = $db->getConnection();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $orderCode = $input['order_code'] ?? null;
    $amount = $input['amount'] ?? null;
    
    if (!$orderCode || !$amount) {
        throw new Exception('Order code and amount are required');
    }
    
    // Tìm đơn hàng
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ?");
    $stmt->execute([$orderCode]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        throw new Exception('Order not found');
    }
    
    // Giả lập webhook data từ Casso
    $webhookData = [
        'data' => [
            [
                'id' => 'TEST_' . time(),
                'amount' => (int)$amount,
                'description' => 'HTHREE ' . $orderCode,
                'when' => date('Y-m-d H:i:s'),
                'bank_sub_acc_id' => '12345',
                'tid' => 'FT' . time()
            ]
        ]
    ];
    
    // Gọi webhook handler
    $webhookUrl = 'http://localhost/HTHREE_film/HTHREE_film/backend/api/payment/casso_webhook.php';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webhookUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Casso-Signature: test_signature'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo json_encode([
            'success' => true,
            'message' => 'Webhook test successful',
            'webhook_response' => json_decode($response, true)
        ]);
    } else {
        throw new Exception('Webhook failed: ' . $response);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}