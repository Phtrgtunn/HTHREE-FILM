<?php
/**
 * Create Active Test Plan
 * Tạo gói test đang hoạt động với thời gian đúng
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

$db = new Database();
$conn = $db->getConnection();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $input['user_id'] ?? null;
    $planId = $input['plan_id'] ?? 6; // Test 2 Phút
    $durationMinutes = $input['duration_minutes'] ?? 2;
    
    if (!$userId) {
        throw new Exception('User ID is required');
    }
    
    // Tính thời gian kết thúc (sử dụng múi giờ database)
    // Database có vẻ dùng UTC+7, nên cộng thêm 7 tiếng
    $startTime = new DateTime();
    $startTime->add(new DateInterval('PT7H')); // Cộng 7 tiếng
    $endTime = clone $startTime;
    $endTime->add(new DateInterval("PT{$durationMinutes}M")); // Thêm X phút
    
    // Xóa subscription cũ của user này (nếu có)
    $stmt = $conn->prepare("
        UPDATE user_subscriptions 
        SET status = 'cancelled', updated_at = NOW()
        WHERE user_id = ? AND status = 'active'
    ");
    $stmt->execute([$userId]);
    
    // Tạo subscription mới
    $stmt = $conn->prepare("
        INSERT INTO user_subscriptions 
        (user_id, plan_id, start_date, end_date, status, order_id, created_at, updated_at)
        VALUES (?, ?, ?, ?, 'active', NULL, NOW(), NOW())
    ");
    
    $stmt->execute([
        $userId,
        $planId,
        $startTime->format('Y-m-d H:i:s'),
        $endTime->format('Y-m-d H:i:s')
    ]);
    
    $subscriptionId = $conn->lastInsertId();
    
    // Lấy thông tin gói vừa tạo
    $stmt = $conn->prepare("
        SELECT us.*, sp.name as plan_name, sp.slug,
               TIMESTAMPDIFF(SECOND, NOW(), us.end_date) as seconds_remaining,
               TIMESTAMPDIFF(MINUTE, NOW(), us.end_date) as minutes_remaining
        FROM user_subscriptions us
        JOIN subscription_plans sp ON us.plan_id = sp.id
        WHERE us.id = ?
    ");
    $stmt->execute([$subscriptionId]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Test subscription created successfully',
        'data' => [
            'subscription_id' => $subscriptionId,
            'user_id' => $userId,
            'plan_name' => $subscription['plan_name'],
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'end_time' => $endTime->format('Y-m-d H:i:s'),
            'duration_minutes' => $durationMinutes,
            'seconds_remaining' => $subscription['seconds_remaining'],
            'minutes_remaining' => $subscription['minutes_remaining'],
            'current_time' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}