<?php
/**
 * MoMo Payment Gateway
 * Tạo URL thanh toán MoMo
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// MoMo Config (Test)
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = "MOMOBKUN20180529"; // Test partner code
$accessKey = "klm05TvNBzhg7h7j"; // Test access key
$secretKey = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa"; // Test secret key
$returnUrl = "http://localhost:5173/payment-return";
$notifyUrl = "http://localhost/HTHREE_film/HTHREE_film/backend/api/payment/momo_notify.php";

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $orderId = $data['order_code'];
    $amount = (string)$data['amount'];
    $orderInfo = $data['order_info'] ?? "Thanh toán đơn hàng " . $orderId;
    $requestId = time() . "";
    $extraData = "";
    
    // Tạo chữ ký
    $rawHash = "accessKey=" . $accessKey . 
               "&amount=" . $amount . 
               "&extraData=" . $extraData . 
               "&ipnUrl=" . $notifyUrl . 
               "&orderId=" . $orderId . 
               "&orderInfo=" . $orderInfo . 
               "&partnerCode=" . $partnerCode . 
               "&redirectUrl=" . $returnUrl . 
               "&requestId=" . $requestId . 
               "&requestType=captureWallet";
    
    $signature = hash_hmac("sha256", $rawHash, $secretKey);
    
    $requestData = array(
        'partnerCode' => $partnerCode,
        'partnerName' => "HTHREE Film",
        'storeId' => "HTHREE",
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $returnUrl,
        'ipnUrl' => $notifyUrl,
        'lang' => 'vi',
        'extraData' => $extraData,
        'requestType' => 'captureWallet',
        'signature' => $signature
    );
    
    // Gọi API MoMo
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($requestData))
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $jsonResult = json_decode($result, true);
    
    if ($httpCode == 200 && isset($jsonResult['payUrl'])) {
        echo json_encode([
            'success' => true,
            'payment_url' => $jsonResult['payUrl'],
            'message' => 'Đang chuyển đến MoMo...'
        ]);
    } else {
        throw new Exception($jsonResult['message'] ?? 'Lỗi kết nối MoMo');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
