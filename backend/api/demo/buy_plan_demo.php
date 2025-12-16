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
    
    $planSlug = $input['plan_slug'] ?? 'basic';
    
    // Dùng user ID 109 cố định cho đơn giản
    $userId = 109;
    
    // Lấy thông tin plan
    $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE slug = ?");
    $stmt->execute([$planSlug]);
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$plan) {
        throw new Exception('Plan not found');
    }
    
    // Thời gian demo cho giảng viên (5 phút để dễ quan sát)
    $durationMinutes = match($planSlug) {
        'basic' => 5,     // 5 phút
        'premium' => 7,   // 7 phút  
        'vip' => 10,      // 10 phút
        default => 5
    };
    
    // Bắt đầu transaction
    $conn->beginTransaction();
    
    // 1. XÓA TẤT CẢ subscription cũ của user 109
    $stmt = $conn->prepare("DELETE FROM user_subscriptions WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    // 2. XÓA TẤT CẢ orders cũ của user 109 (để clean)
    $stmt = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    // 2. TẠO subscription mới - Sử dụng phút cho demo
    $stmt = $conn->prepare("
        INSERT INTO user_subscriptions 
        (user_id, plan_id, start_date, end_date, status, created_at, updated_at)
        VALUES (?, ?, 
                NOW(), 
                DATE_ADD(NOW(), INTERVAL ? MINUTE), 
                'active', 
                NOW(), 
                NOW())
    ");
    
    $stmt->execute([
        $userId,
        $plan['id'],
        $durationMinutes
    ]);
    
    $subscriptionId = $conn->lastInsertId();
    
    // 3. Tạo order demo (đơn giản)
    $stmt = $conn->prepare("
        INSERT INTO orders 
        (user_id, order_code, customer_name, customer_email, subtotal, total, payment_status, status, payment_method, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'paid', 'completed', 'vietqr', NOW())
    ");
    
    $orderCode = 'DEMO_' . time() . '_' . $userId;
    $stmt->execute([
        $userId, 
        $orderCode, 
        'Demo User', 
        'demo@test.com', 
        $plan['price'], // subtotal
        $plan['price']  // total
    ]);
    $orderId = $conn->lastInsertId();
    
    // Commit transaction
    $conn->commit();
    
    // 4. VERIFY subscription được tạo thành công
    $stmt = $conn->prepare("
        SELECT us.*, sp.name as plan_name,
               TIMESTAMPDIFF(MINUTE, NOW(), us.end_date) as minutes_remaining,
               TIMESTAMPDIFF(SECOND, NOW(), us.end_date) as seconds_remaining
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
            'minutes_remaining' => $subscription['minutes_remaining'],
            'seconds_remaining' => $subscription['seconds_remaining'],
            'database_time' => date('Y-m-d H:i:s')
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