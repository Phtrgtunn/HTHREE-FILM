<?php
/**
 * Configuration file
 */

// API Configuration
define('API_BASE_URL', 'https://phimapi.com');
define('API_VERSION', 'v1');
define('API_CDN_IMAGE', 'https://phimimg.com');

// Cache Configuration
define('CACHE_ENABLED', true);
define('CACHE_TIME', 3600); // 1 hour in seconds
define('CACHE_DIR', __DIR__ . '/../cache/');

// CORS Configuration
define('ALLOWED_ORIGINS', [
    'http://localhost:5173',
    'http://localhost:5174',
    'http://localhost:3000',
    'http://127.0.0.1:5173',
    'http://127.0.0.1:5174'
]);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in production

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
