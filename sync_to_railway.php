<?php
/**
 * Sync localhost database to Railway
 * Run this script to copy essential data from localhost to Railway
 */

// Railway database credentials (from environment or hardcoded)
$railway_host = 'autorack.proxy.rlwy.net';
$railway_port = 47470;
$railway_db = 'railway';
$railway_user = 'root';
$railway_pass = 'your_railway_password'; // Replace with actual password

// Localhost database credentials
$local_host = 'localhost';
$local_db = 'hthree_film';
$local_user = 'root';
$local_pass = 'tuan1412';

try {
    // Connect to localhost
    $local_pdo = new PDO("mysql:host=$local_host;dbname=$local_db;charset=utf8mb4", $local_user, $local_pass);
    $local_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Connect to Railway
    $railway_pdo = new PDO("mysql:host=$railway_host;port=$railway_port;dbname=$railway_db;charset=utf8mb4", $railway_user, $railway_pass);
    $railway_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to both databases\n";
    
    // Sync subscription_plans
    echo "🔄 Syncing subscription_plans...\n";
    $plans = $local_pdo->query("SELECT * FROM subscription_plans")->fetchAll();
    foreach ($plans as $plan) {
        $stmt = $railway_pdo->prepare("
            INSERT INTO subscription_plans (id, name, slug, price, duration_days, quality, features, is_active, promotion_badge, promotion_text, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            price = VALUES(price),
            features = VALUES(features),
            promotion_badge = VALUES(promotion_badge),
            promotion_text = VALUES(promotion_text)
        ");
        $stmt->execute([
            $plan['id'], $plan['name'], $plan['slug'], $plan['price'], 
            $plan['duration_days'], $plan['quality'], $plan['features'], 
            $plan['is_active'], $plan['promotion_badge'], $plan['promotion_text'],
            $plan['created_at'], $plan['updated_at']
        ]);
    }
    echo "✅ Synced " . count($plans) . " plans\n";
    
    // Sync users (demo user)
    echo "🔄 Syncing demo user...\n";
    $railway_pdo->exec("
        INSERT INTO users (id, email, full_name, phone, password, created_at, updated_at) VALUES
        (109, 'demo@hthree.com', 'Demo User', '0123456789', '$2y$10\$example', NOW(), NOW())
        ON DUPLICATE KEY UPDATE 
        email = VALUES(email),
        full_name = VALUES(full_name)
    ");
    echo "✅ Synced demo user\n";
    
    // Sync payment_methods
    echo "🔄 Syncing payment_methods...\n";
    $methods = $local_pdo->query("SELECT * FROM payment_methods")->fetchAll();
    foreach ($methods as $method) {
        $stmt = $railway_pdo->prepare("
            INSERT INTO payment_methods (id, name, type, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            is_active = VALUES(is_active)
        ");
        $stmt->execute([
            $method['id'], $method['name'], $method['type'], 
            $method['is_active'], $method['created_at'], $method['updated_at']
        ]);
    }
    echo "✅ Synced " . count($methods) . " payment methods\n";
    
    // Sync coupons
    echo "🔄 Syncing coupons...\n";
    $coupons = $local_pdo->query("SELECT * FROM coupons")->fetchAll();
    foreach ($coupons as $coupon) {
        $stmt = $railway_pdo->prepare("
            INSERT INTO coupons (id, code, name, discount_type, discount_value, min_order_value, max_uses, used_count, is_active, expires_at, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            discount_value = VALUES(discount_value),
            is_active = VALUES(is_active)
        ");
        $stmt->execute([
            $coupon['id'], $coupon['code'], $coupon['name'], $coupon['discount_type'],
            $coupon['discount_value'], $coupon['min_order_value'], $coupon['max_uses'],
            $coupon['used_count'], $coupon['is_active'], $coupon['expires_at'],
            $coupon['created_at'], $coupon['updated_at']
        ]);
    }
    echo "✅ Synced " . count($coupons) . " coupons\n";
    
    echo "🎉 Sync completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>