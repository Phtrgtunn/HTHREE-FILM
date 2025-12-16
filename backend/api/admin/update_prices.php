<?php
/**
 * Update Prices API
 * API để cập nhật giá gói subscription
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
    // Cập nhật giá test
    $updates = [
        ['slug' => 'basic', 'price' => 2000],
        ['slug' => 'premium', 'price' => 4000],
        ['slug' => 'vip', 'price' => 5000]
    ];
    
    $results = [];
    
    foreach ($updates as $update) {
        $stmt = $conn->prepare("UPDATE subscription_plans SET price = ? WHERE slug = ?");
        $stmt->execute([$update['price'], $update['slug']]);
        
        $results[] = [
            'slug' => $update['slug'],
            'price' => $update['price'],
            'updated' => $stmt->rowCount() > 0
        ];
    }
    
    // Lấy giá hiện tại
    $stmt = $conn->query("SELECT id, name, slug, price, duration_days FROM subscription_plans ORDER BY price");
    $currentPrices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Prices updated successfully',
        'updates' => $results,
        'current_prices' => $currentPrices
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}