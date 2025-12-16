<?php
/**
 * Import essential data from localhost to Railway
 * Focus on core tables needed for admin panel
 */

// Railway MySQL credentials
$railway_host = 'crossover.proxy.rlwy.net';
$railway_port = 29616;
$railway_db = 'railway';
$railway_user = 'root';
$railway_pass = 'MMpMAyYHqpuxsFyqDJfcUiwWCMkdjnvr';

// Localhost MySQL credentials
$local_host = 'localhost';
$local_db = 'hthree_film';
$local_user = 'root';
$local_pass = 'tuan1412';

echo "🚀 Importing essential data from localhost to Railway...\n";

try {
    // Connect to both databases
    $local_pdo = new PDO("mysql:host=$local_host;dbname=$local_db;charset=utf8mb4", $local_user, $local_pass);
    $local_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $railway_pdo = new PDO("mysql:host=$railway_host;port=$railway_port;dbname=$railway_db;charset=utf8mb4", $railway_user, $railway_pass);
    $railway_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to both databases\n";
    
    // Clear existing data first
    echo "🧹 Clearing existing data...\n";
    $railway_pdo->exec("DELETE FROM user_subscriptions");
    $railway_pdo->exec("DELETE FROM order_items");
    $railway_pdo->exec("DELETE FROM orders");
    $railway_pdo->exec("DELETE FROM coupons");
    $railway_pdo->exec("DELETE FROM subscription_plans");
    $railway_pdo->exec("DELETE FROM users");
    
    // Import users
    echo "👤 Importing users...\n";
    $users = $local_pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        $stmt = $railway_pdo->prepare("
            INSERT INTO users (id, username, email, firebase_uid, password, full_name, avatar, created_at, updated_at, last_login, is_active, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user['id'], $user['username'], $user['email'], $user['firebase_uid'],
            $user['password'], $user['full_name'], $user['avatar'], $user['created_at'],
            $user['updated_at'], $user['last_login'], $user['is_active'], $user['role']
        ]);
    }
    echo "✅ Imported " . count($users) . " users\n";
    
    // Import subscription plans
    echo "📦 Importing subscription plans...\n";
    $plans = $local_pdo->query("SELECT * FROM subscription_plans")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($plans as $plan) {
        $stmt = $railway_pdo->prepare("
            INSERT INTO subscription_plans (id, name, slug, description, price, duration_days, quality, max_devices, has_ads, can_download, early_access, is_active, display_order, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $plan['id'], $plan['name'], $plan['slug'], $plan['description'], $plan['price'],
            $plan['duration_days'], $plan['quality'], $plan['max_devices'], $plan['has_ads'],
            $plan['can_download'], $plan['early_access'], $plan['is_active'], $plan['display_order'],
            $plan['created_at'], $plan['updated_at']
        ]);
    }
    echo "✅ Imported " . count($plans) . " subscription plans\n";
    
    // Import coupons
    echo "🎫 Importing coupons...\n";
    $coupons = $local_pdo->query("SELECT * FROM coupons")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($coupons as $coupon) {
        $stmt = $railway_pdo->prepare("
            INSERT INTO coupons (id, code, description, discount_type, discount_value, min_order_value, max_discount, usage_limit, usage_count, user_limit, start_date, end_date, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $coupon['id'], $coupon['code'], $coupon['description'], $coupon['discount_type'],
            $coupon['discount_value'], $coupon['min_order_value'], $coupon['max_discount'],
            $coupon['usage_limit'], $coupon['usage_count'], $coupon['user_limit'],
            $coupon['start_date'], $coupon['end_date'], $coupon['is_active'],
            $coupon['created_at'], $coupon['updated_at']
        ]);
    }
    echo "✅ Imported " . count($coupons) . " coupons\n";
    
    // Import orders
    echo "🛒 Importing orders...\n";
    $orders = $local_pdo->query("SELECT * FROM orders ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($orders as $order) {
        $stmt = $railway_pdo->prepare("
            INSERT INTO orders (id, order_code, user_id, customer_name, customer_email, customer_phone, subtotal, discount, total, payment_method, payment_status, status, note, admin_note, paid_at, completed_at, cancelled_at, created_at, updated_at, qr_code_url, transfer_content, expires_at, transaction_id, payment_note) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $order['id'], $order['order_code'], $order['user_id'], $order['customer_name'],
            $order['customer_email'], $order['customer_phone'], $order['subtotal'], $order['discount'],
            $order['total'], $order['payment_method'], $order['payment_status'], $order['status'],
            $order['note'], $order['admin_note'], $order['paid_at'], $order['completed_at'],
            $order['cancelled_at'], $order['created_at'], $order['updated_at'], $order['qr_code_url'],
            $order['transfer_content'], $order['expires_at'], $order['transaction_id'], $order['payment_note']
        ]);
    }
    echo "✅ Imported " . count($orders) . " orders\n";
    
    // Import user subscriptions
    echo "📋 Importing user subscriptions...\n";
    $subscriptions = $local_pdo->query("SELECT * FROM user_subscriptions")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($subscriptions as $sub) {
        $stmt = $railway_pdo->prepare("
            INSERT INTO user_subscriptions (id, user_id, plan_id, start_date, end_date, status, auto_renew, order_id, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $sub['id'], $sub['user_id'], $sub['plan_id'], $sub['start_date'], $sub['end_date'],
            $sub['status'], $sub['auto_renew'], $sub['order_id'], $sub['created_at'], $sub['updated_at']
        ]);
    }
    echo "✅ Imported " . count($subscriptions) . " user subscriptions\n";
    
    echo "\n🎉 Import completed successfully!\n";
    echo "📊 Summary:\n";
    echo "  - Users: " . count($users) . "\n";
    echo "  - Plans: " . count($plans) . "\n";
    echo "  - Coupons: " . count($coupons) . "\n";
    echo "  - Orders: " . count($orders) . "\n";
    echo "  - Subscriptions: " . count($subscriptions) . "\n";
    echo "\n📝 Railway database now has complete localhost data!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>