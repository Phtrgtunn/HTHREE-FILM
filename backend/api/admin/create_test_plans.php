<?php
/**
 * Create Test Duration Plans
 * Tạo các gói test với thời gian ngắn để test hết hạn
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
    // Xóa gói test cũ (nếu có)
    $conn->exec("DELETE FROM subscription_plans WHERE slug LIKE 'test_%'");
    
    // Tạo gói test mới
    $testPlans = [
        [
            'name' => 'Test 2 Phút',
            'slug' => 'test_2min',
            'description' => 'Gói test 2 phút để kiểm tra hết hạn',
            'price' => 1000,
            'duration_days' => 0.00139, // 2 phút = 2/60/24 ngày
            'quality' => 'HD',
            'features' => '["Test hết hạn", "2 phút", "Chỉ để test"]'
        ],
        [
            'name' => 'Test 5 Phút',
            'slug' => 'test_5min', 
            'description' => 'Gói test 5 phút để kiểm tra hết hạn',
            'price' => 1500,
            'duration_days' => 0.00347, // 5 phút = 5/60/24 ngày
            'quality' => 'HD',
            'features' => '["Test hết hạn", "5 phút", "Chỉ để test"]'
        ],
        [
            'name' => 'Test 10 Phút',
            'slug' => 'test_10min',
            'description' => 'Gói test 10 phút để kiểm tra hết hạn', 
            'price' => 2000,
            'duration_days' => 0.00694, // 10 phút = 10/60/24 ngày
            'quality' => 'HD',
            'features' => '["Test hết hạn", "10 phút", "Chỉ để test"]'
        ]
    ];
    
    $stmt = $conn->prepare("
        INSERT INTO subscription_plans 
        (name, slug, description, price, duration_days, quality, is_active) 
        VALUES (?, ?, ?, ?, ?, ?, 1)
    ");
    
    $created = [];
    foreach ($testPlans as $plan) {
        $stmt->execute([
            $plan['name'],
            $plan['slug'], 
            $plan['description'],
            $plan['price'],
            $plan['duration_days'],
            $plan['quality']
        ]);
        
        $created[] = [
            'name' => $plan['name'],
            'duration_minutes' => round($plan['duration_days'] * 24 * 60, 2),
            'price' => $plan['price']
        ];
    }
    
    // Lấy danh sách gói hiện tại
    $stmt = $conn->query("
        SELECT id, name, slug, price, duration_days,
               ROUND(duration_days * 24 * 60, 2) as duration_minutes,
               is_active 
        FROM subscription_plans 
        ORDER BY duration_days
    ");
    $allPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Test plans created successfully',
        'created_plans' => $created,
        'all_plans' => $allPlans
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}