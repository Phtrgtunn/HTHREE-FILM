<?php
/**
 * Debug Subscription - Kiểm tra subscription trong database
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
    $userId = $_GET['user_id'] ?? 1;
    
    // 1. Kiểm tra tất cả subscriptions của user
    $stmt = $conn->prepare("
        SELECT us.*, sp.name as plan_name, sp.slug as plan_slug
        FROM user_subscriptions us
        LEFT JOIN subscription_plans sp ON us.plan_id = sp.id
        WHERE us.user_id = ?
        ORDER BY us.created_at DESC
    ");
    $stmt->execute([$userId]);
    $allSubscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Kiểm tra subscription active
    $stmt = $conn->prepare("
        SELECT us.*, sp.name as plan_name,
               TIMESTAMPDIFF(MINUTE, DATE_ADD(NOW(), INTERVAL 7 HOUR), us.end_date) as minutes_remaining,
               TIMESTAMPDIFF(SECOND, DATE_ADD(NOW(), INTERVAL 7 HOUR), us.end_date) as seconds_remaining
        FROM user_subscriptions us
        LEFT JOIN subscription_plans sp ON us.plan_id = sp.id
        WHERE us.user_id = ? AND us.status = 'active'
        ORDER BY us.created_at DESC
    ");
    $stmt->execute([$userId]);
    $activeSubscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Kiểm tra thời gian database
    $stmt = $conn->prepare("SELECT NOW() as db_now, DATE_ADD(NOW(), INTERVAL 7 HOUR) as db_utc7");
    $stmt->execute();
    $dbTime = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'user_id' => $userId,
        'database_time' => $dbTime,
        'all_subscriptions' => $allSubscriptions,
        'active_subscriptions' => $activeSubscriptions,
        'total_count' => count($allSubscriptions),
        'active_count' => count($activeSubscriptions),
        'debug_info' => [
            'query_used' => "SELECT * FROM user_subscriptions WHERE user_id = {$userId}",
            'timezone_applied' => 'UTC+7 for comparison'
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}