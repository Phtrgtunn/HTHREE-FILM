<?php
/**
 * CORS Configuration
 * Cho phép frontend gọi API từ domain khác
 */

// Cho phép tất cả origins (development)
header('Access-Control-Allow-Origin: *');

// Cho phép các methods
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Cho phép các headers
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Cho phép credentials
header('Access-Control-Allow-Credentials: true');

// Cache preflight request
header('Access-Control-Max-Age: 86400');

// Content type
header('Content-Type: application/json; charset=utf-8');

// Xử lý OPTIONS request (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
