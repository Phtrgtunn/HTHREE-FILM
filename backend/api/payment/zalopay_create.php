<?php
/**
 * ZaloPay Payment Gateway
 * Tạo URL thanh toán ZaloPay
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ZaloPay Config (Sandbox)
$config = [
    "app_id" => 2553,
    "key1" => "PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL",
    "key2" => "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz",
    "endpoint" => "https://sb-openapi.zalopay.vn/v2/create"
];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $orderId = $data['order_code'];
    $amount = (int)$data['amount'];
    $orderInfo = $data['order_info'] ?? "Thanh toán đơn hàng " . $orderId;
    
    $embeddata = json_encode([
        'redirecturl' => 'http://localhost:5173/payment-return'
    ]);
    
    $items = json_encode([]);
    $transID = time();
    
    $order = [
        "app_id" => $config["app_id"],
        "app_trans_id" => date("ymd") . "_" . $transID,
        "app_user" => "user123",
        "app_time" => round(microtime(true) * 1000),
        "item" => $items,
        "embed_data" => $embeddata,
        "amount" => $amount,
        "description" => $orderInfo,
        "bank_code" => "",
        "callback_url" => "http://localhost/HTHREE_film/HTHREE_film/backend/api/payment/zalopay_notify.php"
    ];
    
    // Tạo MAC
    $data_sign = $order["app_id"] . "|" . 
                 $order["app_trans_id"] . "|" . 
                 $order["app_user"] . "|" . 
                 $order["amount"] . "|" . 
                 $order["app_time"] . "|" . 
                 $order["embed_data"] . "|" . 
                 $order["item"];
    
    $order["mac"] = hash_hmac("sha256", $data_sign, $config["key1"]);
    
    // Gọi API ZaloPay
    $ch = curl_init($config["endpoint"]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($order));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $jsonResult = json_decode($result, true);
    
    if ($httpCode == 200 && $jsonResult['return_code'] == 1) {
        echo json_encode([
            'success' => true,
            'payment_url' => $jsonResult['order_url'],
            'message' => 'Đang chuyển đến ZaloPay...'
        ]);
    } else {
        throw new Exception($jsonResult['return_message'] ?? 'Lỗi kết nối ZaloPay');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
