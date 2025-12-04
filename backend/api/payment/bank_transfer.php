<?php
/**
 * Bank Transfer Payment
 * Tạo thông tin chuyển khoản ngân hàng
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

// Thông tin ngân hàng (Thay bằng thông tin thật của bạn)
$BANK_INFO = [
    'bank_code' => 'VCB', // Vietcombank
    'bank_name' => 'Ngân hàng TMCP Ngoại thương Việt Nam (Vietcombank)',
    'account_no' => '1234567890',
    'account_name' => 'NGUYEN VAN A',
    'branch' => 'Chi nhánh TP.HCM'
];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $order_code = $data['order_code'];
    $amount = $data['amount'];
    $customer_name = $data['customer_name'] ?? '';
    
    // Tạo nội dung chuyển khoản
    $transfer_content = $order_code;
    
    // Generate QR Code URL (sử dụng VietQR)
    $qr_url = "https://img.vietqr.io/image/" . 
              $BANK_INFO['bank_code'] . "-" . 
              $BANK_INFO['account_no'] . "-compact2.jpg" .
              "?amount=" . $amount .
              "&addInfo=" . urlencode($transfer_content) .
              "&accountName=" . urlencode($BANK_INFO['account_name']);
    
    // Lưu thông tin thanh toán vào database
    $db = new Database();
    $conn = $db->connect();
    
    $stmt = $conn->prepare("
        UPDATE orders 
        SET payment_info = ?, 
            updated_at = NOW() 
        WHERE order_code = ?
    ");
    
    $payment_info = json_encode([
        'method' => 'bank_transfer',
        'bank' => $BANK_INFO,
        'transfer_content' => $transfer_content,
        'qr_url' => $qr_url
    ]);
    
    $stmt->execute([$payment_info, $order_code]);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'bank_info' => $BANK_INFO,
            'amount' => $amount,
            'transfer_content' => $transfer_content,
            'qr_url' => $qr_url,
            'order_code' => $order_code
        ],
        'message' => 'Vui lòng chuyển khoản theo thông tin bên dưới'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi tạo thông tin chuyển khoản: ' . $e->getMessage()
    ]);
}
