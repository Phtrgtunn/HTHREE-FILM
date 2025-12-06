<?php
/**
 * Payment Gateway Configuration
 * Cấu hình các cổng thanh toán
 */

return [
    // VNPay Configuration
    'vnpay' => [
        'tmn_code' => getenv('VNPAY_TMN_CODE') ?: 'DEMO',
        'hash_secret' => getenv('VNPAY_HASH_SECRET') ?: 'DEMOSECRETKEY',
        'url' => getenv('VNPAY_URL') ?: 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
        'return_url' => getenv('VNPAY_RETURN_URL') ?: 'http://localhost:5174/payment-return',
        
        // Production URLs (uncomment khi deploy)
        // 'url' => 'https://vnpayment.vn/paymentv2/vpcpay.html',
        // 'return_url' => 'https://yourdomain.com/payment-return',
    ],
    
    // MoMo Configuration
    'momo' => [
        'partner_code' => getenv('MOMO_PARTNER_CODE') ?: 'MOMOBKUN20180529',
        'access_key' => getenv('MOMO_ACCESS_KEY') ?: 'klm05TvNBzhg7h7j',
        'secret_key' => getenv('MOMO_SECRET_KEY') ?: 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa',
        'endpoint' => getenv('MOMO_ENDPOINT') ?: 'https://test-payment.momo.vn/v2/gateway/api/create',
        'return_url' => getenv('MOMO_RETURN_URL') ?: 'http://localhost:5173/payment-return',
        'notify_url' => getenv('MOMO_NOTIFY_URL') ?: 'http://localhost/HTHREE_film/backend/api/payment/momo_notify.php',
        
        // Production URLs (uncomment khi deploy)
        // 'endpoint' => 'https://payment.momo.vn/v2/gateway/api/create',
        // 'return_url' => 'https://yourdomain.com/payment-return',
        // 'notify_url' => 'https://yourdomain.com/backend/api/payment/momo_notify.php',
    ],
    
    // ZaloPay Configuration (nếu cần)
    'zalopay' => [
        'app_id' => getenv('ZALOPAY_APP_ID') ?: '',
        'key1' => getenv('ZALOPAY_KEY1') ?: '',
        'key2' => getenv('ZALOPAY_KEY2') ?: '',
        'endpoint' => getenv('ZALOPAY_ENDPOINT') ?: 'https://sb-openapi.zalopay.vn/v2/create',
    ],
];
