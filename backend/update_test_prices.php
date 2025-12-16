<?php
/**
 * Update Test Prices for Subscription Plans
 * Cập nhật giá test nhỏ để dễ test thanh toán
 */

require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    echo "🔄 Updating subscription plan prices for testing...\n\n";
    
    // Cập nhật giá test
    $updates = [
        ['slug' => 'basic', 'price' => 2000, 'name' => 'Basic'],
        ['slug' => 'premium', 'price' => 4000, 'name' => 'Premium'],
        ['slug' => 'vip', 'price' => 5000, 'name' => 'VIP']
    ];
    
    foreach ($updates as $update) {
        $stmt = $conn->prepare("UPDATE subscription_plans SET price = ? WHERE slug = ?");
        $stmt->execute([$update['price'], $update['slug']]);
        
        echo "✅ Updated {$update['name']} plan: {$update['price']}đ\n";
    }
    
    echo "\n📋 Current subscription plans:\n";
    echo "================================\n";
    
    // Hiển thị kết quả
    $stmt = $conn->prepare("SELECT id, name, slug, price, duration_days FROM subscription_plans ORDER BY price");
    $stmt->execute();
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($plans as $plan) {
        $priceFormatted = number_format($plan['price']) . 'đ';
        echo "• {$plan['name']} ({$plan['slug']}): {$priceFormatted} / {$plan['duration_days']} ngày\n";
    }
    
    echo "\n🎉 Test prices updated successfully!\n";
    echo "💡 Now you can test VietQR payments with small amounts.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}