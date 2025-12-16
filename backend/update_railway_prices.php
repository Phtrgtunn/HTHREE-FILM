<?php
/**
 * Update Railway Production Prices
 * Script để cập nhật giá gói trên Railway production
 */

// Railway Database Config
$host = 'junction.proxy.rlwy.net';
$port = '17592';
$dbname = 'railway';
$username = 'root';
$password = 'tuan1412';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to Railway database\n";
    
    // Cập nhật giá
    $updates = [
        ['slug' => 'basic', 'price' => 2000],
        ['slug' => 'premium', 'price' => 4000],
        ['slug' => 'vip', 'price' => 5000]
    ];
    
    foreach ($updates as $update) {
        $stmt = $pdo->prepare("UPDATE subscription_plans SET price = ? WHERE slug = ?");
        $stmt->execute([$update['price'], $update['slug']]);
        echo "✅ Updated {$update['slug']}: {$update['price']}đ\n";
    }
    
    // Kiểm tra kết quả
    echo "\n📋 Current prices:\n";
    $stmt = $pdo->query("SELECT id, name, slug, price, duration_days FROM subscription_plans ORDER BY price");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['name']} ({$row['slug']}): " . number_format($row['price']) . "đ - {$row['duration_days']} days\n";
    }
    
    echo "\n🎉 Railway prices updated successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}