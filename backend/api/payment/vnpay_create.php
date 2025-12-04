<?php
/**
 * VNPay Payment Gateway
 * Tạo URL thanh toán VNPay
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

// VNPay Config (Sandbox)
$vnp_TmnCode = "DEMO"; // Mã website - Đăng ký tại sandbox.vnpayment.vn
$vnp_HashSecret = "DEMOSECRETKEY"; // Chuỗi bí mật
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_ReturnUrl = "http://localhost:5174/payment-return";

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $order_code = $data['order_code'];
    $amount = $data['amount'];
    $order_info = $data['order_info'] ?? "Thanh toan don hang " . $order_code;
    
    // Tạo mã giao dịch
    $vnp_TxnRef = $order_code;
    $vnp_OrderInfo = $order_info;
    $vnp_Amount = $amount * 100; // VNPay tính bằng đồng (x100)
    
    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
        "vnp_Locale" => "vn",
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => "other",
        "vnp_ReturnUrl" => $vnp_ReturnUrl,
        "vnp_TxnRef" => $vnp_TxnRef
    );
    
    ksort($inputData);
    $query = "";
    $hashdata = "";
    $i = 0;
    
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
        $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }
    
    $vnp_Url = $vnp_Url . "?" . $query;
    
    if (isset($vnp_HashSecret)) {
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
    }
    
    echo json_encode([
        'success' => true,
        'payment_url' => $vnp_Url,
        'message' => 'Đang chuyển đến VNPay...'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi tạo thanh toán: ' . $e->getMessage()
    ]);
}
