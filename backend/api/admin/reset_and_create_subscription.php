<?php
/**
 * Reset and Create New Subscription
 * Xóa tất cả subscription cũ và tạo gói mới cho demo
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
    $planSlug = $input['plan_slug'] ?? 'basic'; // basic, premium, vip
    $durationMinutes = $input['duration_minutes'] ?? null;
    
    if (!$userId) {
        throw new Exception('User ID is required');
    }
    
    // Lấy thông tin plan
    $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE slug = ?");
    $stmt->execute([$planSlug]);
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$plan) {
        throw new Exception('Plan not found');
    }
    
    // Tính thời gian mặc định nếu không có duration_minutes
    if (!$durationMinutes) {
        $durationMinutes = match($planSlug) {
            'basic' => 3,
            'premium' => 5, 
            'vip' => 10,
            default => 3
        };
    }
    
    // Bắt đầu transaction
    $conn->beginTransaction();
    
    // 1. XÓA TẤT CẢ subscription cũ của user
    $stmt = $conn->prepare("DELETE FROM user_subscriptions WHERE user_id = ?");
    $stmt->execute([$userId]);
    $deletedCount = $stmt->rowCount();
    
    // 2. TẠO subscription mới với thời gian đúng (múi giờ database UTC+7)
    $startTime = new DateTime();
    $startTime->add(new DateInterval('PT7H')); // Cộng 7 tiếng cho múi giờ database
    $endTime = clone $startTime;
    $endTime->add(new DateInterval("PT{$durationMinutes}M"));
    
    $stmt = $conn->prepare("
        INSERT INTO user_subscriptions 
        (user_id, plan_id, start_date, end_date, status, order_id, created_at, updated_at)
        VALUES (?, ?, ?, ?, 'active', NULL, NOW(), NOW())
    ");
    
    $stmt->execute([
        $userId,
        $plan['id'],
        $startTime->format('Y-m-d H:i:s'),
        $endTime->format('Y-m-d H:i:s')
    ]);
    
    $subscriptionId = $conn->lastInsertId();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Subscription reset and created successfully',
        'data' => [
            'subscription_id' => $subscriptionId,
            'user_id' => $userId,
            'plan_name' => $plan['name'],
            'plan_slug' => $plan['slug'],
            'price' => $plan['price'],
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'end_time' => $endTime->format('Y-m-d H:i:s'),
            'duration_minutes' => $durationMinutes,
            'deleted_old_subscriptions' => $deletedCount,
            'current_time' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}