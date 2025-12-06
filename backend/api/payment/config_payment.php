<?php
/**
 * Payment Configuration
 * Cấu hình thông tin ngân hàng và Casso
 */

// Thông tin ngân hàng nhận tiền
define('BANK_ID', '970422');           // MB Bank
define('BANK_ACCOUNT_NO', '0825591211'); // Số tài khoản
define('BANK_ACCOUNT_NAME', 'PHAM TRUNG TUAN'); // Tên chủ tài khoản (VIẾT HOA, KHÔNG DẤU)

// Casso API Configuration
define('CASSO_API_KEY', 'YOUR_CASSO_API_KEY'); // Lấy từ https://casso.vn/tai-khoan/api
define('CASSO_WEBHOOK_SECRET', 'YOUR_WEBHOOK_SECRET'); // Secret để verify webhook

// Order Configuration
define('ORDER_PREFIX', 'HTHREE');      // Prefix cho mã đơn hàng
define('ORDER_TIMEOUT', 900);          // Timeout 15 phút (900 giây)

// VietQR Template
define('VIETQR_TEMPLATE', 'compact2'); // compact2, print, qr_only

/**
 * Danh sách mã ngân hàng phổ biến
 * 
 * 970415 - Vietinbank
 * 970422 - MB Bank
 * 970436 - Vietcombank
 * 970418 - BIDV
 * 970405 - Agribank
 * 970407 - Techcombank
 * 970432 - VPBank
 * 970423 - TPBank
 * 970403 - Sacombank
 * 970416 - ACB
 */

return [
    'bank_id' => BANK_ID,
    'account_no' => BANK_ACCOUNT_NO,
    'account_name' => BANK_ACCOUNT_NAME,
    'casso_api_key' => CASSO_API_KEY,
    'webhook_secret' => CASSO_WEBHOOK_SECRET,
    'order_prefix' => ORDER_PREFIX,
    'order_timeout' => ORDER_TIMEOUT,
    'vietqr_template' => VIETQR_TEMPLATE
];
