<?php
/**
 * Debug Timezone - Kiểm tra múi giờ database vs server
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
    // Lấy thời gian từ database
    $stmt = $conn->prepare("
        SELECT 
            NOW() as db_now,
            DATE_ADD(NOW(), INTERVAL 7 HOUR) as db_utc_plus_7,
            UNIX_TIMESTAMP(NOW()) as db_timestamp,
            UNIX_TIMESTAMP(DATE_ADD(NOW(), INTERVAL 7 HOUR)) as db_utc7_timestamp
    ");
    $stmt->execute();
    $dbTime = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Thời gian PHP server
    $phpTime = [
        'php_now' => date('Y-m-d H:i:s'),
        'php_timestamp' => time(),
        'php_timezone' => date_default_timezone_get()
    ];
    
    // Test tạo subscription với thời gian khác nhau
    $testTimes = [
        'method_1_php_time' => date('Y-m-d H:i:s', strtotime('+3 minutes')),
        'method_2_php_utc7' => date('Y-m-d H:i:s', strtotime('+7 hours +3 minutes')),
        'method_3_db_utc7' => null // Sẽ được tính từ database
    ];
    
    // Tính method 3 từ database
    $stmt = $conn->prepare("SELECT DATE_ADD(DATE_ADD(NOW(), INTERVAL 7 HOUR), INTERVAL 3 MINUTE) as end_time");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $testTimes['method_3_db_utc7'] = $result['end_time'];
    
    echo json_encode([
        'success' => true,
        'database_time' => $dbTime,
        'php_server_time' => $phpTime,
        'test_end_times' => $testTimes,
        'recommendation' => 'Sử dụng method_3_db_utc7 cho nhất quán với database',
        'current_issue' => 'Database có thể đang lưu theo UTC, cần cộng 7 tiếng'
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}