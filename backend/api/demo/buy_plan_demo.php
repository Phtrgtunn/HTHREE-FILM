<?php
/**
 * Buy Plan Demo - Simplified for Presentation
 * API đơn giản để mua gói demo, bỏ qua tất cả vấn đề múi giờ
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
    $planSlug = $input['plan_slug'] ?? 'basic';
    
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
    
    // Tính thời gian demo
    $durationMinutes = match($planSlug) {
        'basic' => 3,
        'premium' => 5, 
        'vip' => 10,
        default => 3
    };
    
    // Bắt đầu transaction
    $conn->beginTransaction();
    
    // 1. XÓA TẤT CẢ subscription cũ
    $stmt = $conn->prepare("DELETE FROM user_subscriptions WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    // 2. TẠO subscription mới - Sử dụng múi giờ UTC+7 nhất quán
    $startTime = new DateTime();
    $startTime->add(new DateInterval('PT7H')); // Cộng 7 tiếng cho múi giờ database UTC+7
    $endTime = clone $startTime;
    $endTime->add(new DateInterval("PT{$durationMinutes}M"));
    
    $currentTime = $startTime->format('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("
        INSERT INTO user_subscriptions 
        (user_id, plan_id, start_date, end_date, status, created_at, updated_at)
        VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 7 HOUR), ?, 'active', DATE_ADD(NOW(), INTERVAL 7 HOUR), DATE_ADD(NOW(), INTERVAL 7 HOUR))
    ");
    
    $stmt->execute([
        $userId,
        $plan['id'],
        $endTime->format('Y-m-d H:i:s')
    ]);
    
    $subscriptionId = $conn->lastInsertId();
    
    // 3. Tạo order demo (đơn giản)
    $stmt = $conn->prepare("
        INSERT INTO orders 
        (user_id, order_code, total, payment_status, status, payment_method, created_at)
        VALUES (?, ?, ?, 'paid', 'completed', 'demo', NOW())
    ");
    
    $orderCode = 'DEMO_' . time() . '_' . $userId;
    $stmt->execute([$userId, $orderCode, $plan['price']]);
    $orderId = $conn->lastInsertId();
    
    // Commit transaction
    $conn->commit();
    
    // 4. VERIFY subscription được tạo thành công
    $stmt = $conn->prepare("
        SELECT us.*, sp.name as plan_name,
               TIMESTAMPDIFF(SECOND, NOW(), us.end_date) as seconds_remaining,
               TIMESTAMPDIFF(MINUTE, NOW(), us.end_date) as minutes_remaining
        FROM user_subscriptions us
        JOIN subscription_plans sp ON us.plan_id = sp.id
        WHERE us.id = ?
    ");
    $stmt->execute([$subscriptionId]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$subscription) {
        throw new Exception('Failed to create subscription');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Demo plan purchased successfully',
        'data' => [
            'subscription_id' => $subscriptionId,
            'order_id' => $orderId,
            'order_code' => $orderCode,
            'user_id' => $userId,
            'plan_name' => $subscription['plan_name'],
            'plan_slug' => $planSlug,
            'price' => $plan['price'],
            'start_time' => $subscription['start_date'],
            'end_time' => $subscription['end_date'],
            'duration_minutes' => $durationMinutes,
            'seconds_remaining' => $subscription['seconds_remaining'],
            'minutes_remaining' => $subscription['minutes_remaining'],
            'database_time' => date('Y-m-d H:i:s'),
            'timezone_info' => 'UTC+7 applied'
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