<?php
/**
 * Server Time API
 * Returns current server time to prevent client-side time manipulation
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Return current server time in ISO 8601 format
echo json_encode([
    'success' => true,
    'server_time' => date('Y-m-d H:i:s'),
    'timestamp' => time(),
    'timezone' => date_default_timezone_get()
]);
