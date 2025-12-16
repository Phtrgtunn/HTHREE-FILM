<?php
/**
 * Update Demo Duration for Presentation
 * Thay đổi thời gian gói chính thành thời gian ngắn để demo
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
    // Cập nhật thời gian gói chính thành thời gian demo
    $demoPlans = [
        [
            'slug' => 'basic',
            'name' => 'Basic',
            'description' => 'Gói cơ bản - Demo 3 phút',
            'duration_days' => 0.002083, // 3 phút = 3/60/24 ngày
            'price' => 2000
        ],
        [
            'slug' => 'premium', 
            'name' => 'Premium',
            'description' => 'Gói cao cấp - Demo 5 phút',
            'duration_days' => 0.003472, // 5 phút = 5/60/24 ngày
            'price' => 4000
        ],
        [
            'slug' => 'vip',
            'name' => 'VIP',
            'description' => 'Gói VIP - Demo 10 phút',
            'duration_days' => 0.006944, // 10 phút = 10/60/24 ngày
            'price' => 5000
        ]
    ];
    
    $updated = [];
    
    foreach ($demoPlans as $plan) {
        $stmt = $conn->prepare("
            UPDATE subscription_plans 
            SET name = ?, 
                description = ?, 
                duration_days = ?, 
                price = ?,
                updated_at = NOW()
            WHERE slug = ?
        ");
        
        $stmt->execute([
            $plan['name'],
            $plan['description'], 
            $plan['duration_days'],
            $plan['price'],
            $plan['slug']
        ]);
        
        $updated[] = [
            'slug' => $plan['slug'],
            'name' => $plan['name'],
            'duration_minutes' => round($plan['duration_days'] * 24 * 60, 1),
            'price' => $plan['price'],
            'updated' => $stmt->rowCount() > 0
        ];
    }
    
    // Ẩn các gói test cũ thay vì xóa
    $conn->exec("UPDATE subscription_plans SET is_active = 0 WHERE slug LIKE 'test_%'");
    
    // Lấy danh sách gói hiện tại
    $stmt = $conn->query("
        SELECT id, name, slug, description, price, duration_days,
               ROUND(duration_days * 24 * 60, 2) as duration_minutes,
               is_active 
        FROM subscription_plans 
        WHERE slug IN ('free', 'basic', 'premium', 'vip')
        ORDER BY price
    ");
    $currentPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Demo duration updated successfully',
        'updated_plans' => $updated,
        'current_plans' => $currentPlans
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}