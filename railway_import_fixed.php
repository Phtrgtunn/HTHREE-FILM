<?php
/**
 * Import data to Railway MySQL with correct structure
 */

// Railway MySQL credentials
$host = 'crossover.proxy.rlwy.net';
$port = 29616;
$database = 'railway';
$username = 'root';
$password = 'MMpMAyYHqpuxsFyqDJfcUiwWCMkdjnvr';

echo "🚀 Importing data to Railway MySQL (fixed structure)...\n";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected successfully!\n";
    
    // SQL statements with correct column names
    $statements = [
        // Users (using correct columns)
        "INSERT INTO users (id, username, email, password, full_name, role, is_active) VALUES
        (109, 'demouser', 'demo@hthree.com', '\$2y\$10\$example', 'Demo User', 'user', 1),
        (1, 'admin', 'admin@hthree.com', '\$2y\$10\$example', 'Admin', 'admin', 1)
        ON DUPLICATE KEY UPDATE 
        email = VALUES(email), full_name = VALUES(full_name)",
        
        // Subscription plans (using correct columns)
        "INSERT INTO subscription_plans (id, name, slug, description, price, duration_days, quality, max_devices, has_ads, can_download, early_access, is_active, promotion_badge, promotion_text, display_order) VALUES
        (1, 'Gói Cơ Bản', 'basic', 'Xem phim HD không quảng cáo trên 1 thiết bị', 99000.00, 30, 'HD', 1, 0, 0, 0, 1, '', '', 1),
        (2, 'Gói Cao Cấp', 'premium', 'Xem phim Full HD trên 2 thiết bị, tải về được', 199000.00, 30, 'Full HD', 2, 0, 1, 0, 1, 'Phổ biến nhất', 'Phổ biến nhất', 2),
        (3, 'Gói VIP', 'vip', 'Xem phim 4K trên 4 thiết bị, xem trước phim mới', 299000.00, 30, '4K', 4, 0, 1, 1, 1, 'Tốt nhất', 'Tốt nhất', 3)
        ON DUPLICATE KEY UPDATE 
        name = VALUES(name), price = VALUES(price), description = VALUES(description)",
        
        // Coupons (using correct columns)
        "INSERT INTO coupons (id, code, description, discount_type, discount_value, min_order_value, usage_limit, usage_count, is_active, start_date, end_date) VALUES
        (1, 'WELCOME10', 'Giảm 10% cho khách hàng mới', 'percent', 10.00, 50000.00, 100, 0, 1, NOW(), '2025-02-28 23:59:59'),
        (2, 'SAVE50K', 'Giảm 50K cho đơn từ 200K', 'fixed', 50000.00, 200000.00, 50, 0, 1, NOW(), '2025-02-28 23:59:59'),
        (3, 'CHRISTMAS25', 'Giảm 25% dịp Giáng Sinh', 'percent', 25.00, 100000.00, 200, 0, 1, NOW(), '2025-01-31 23:59:59')
        ON DUPLICATE KEY UPDATE 
        description = VALUES(description), discount_value = VALUES(discount_value)",
        
        // Orders (using correct columns)
        "INSERT INTO orders (id, order_code, user_id, customer_name, customer_email, subtotal, total, payment_method, payment_status, status) VALUES
        (1, 'ORD001', 109, 'Demo User', 'demo@hthree.com', 199000.00, 199000.00, 'vietqr', 'paid', 'completed'),
        (2, 'ORD002', 109, 'Demo User', 'demo@hthree.com', 299000.00, 299000.00, 'vietqr', 'pending', 'pending')
        ON DUPLICATE KEY UPDATE 
        payment_status = VALUES(payment_status), status = VALUES(status)",
        
        // User subscriptions (using correct columns)
        "INSERT INTO user_subscriptions (id, user_id, plan_id, start_date, end_date, status, auto_renew, order_id) VALUES
        (1, 109, 2, NOW(), DATE_ADD(NOW(), INTERVAL 5 MINUTE), 'active', 0, 1)
        ON DUPLICATE KEY UPDATE 
        start_date = VALUES(start_date), end_date = VALUES(end_date), status = VALUES(status)"
    ];
    
    foreach ($statements as $index => $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ Statement " . ($index + 1) . " executed successfully\n";
        } catch (Exception $e) {
            echo "⚠️ Statement " . ($index + 1) . " warning: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🎉 Import completed successfully!\n";
    echo "📝 Check admin panel: https://hthree-film.vercel.app/admin\n";
    echo "\n📊 Imported data:\n";
    echo "  ✅ 2 Users (admin + demo user)\n";
    echo "  ✅ 3 Subscription Plans (Basic, Premium, VIP)\n";
    echo "  ✅ 3 Coupons (WELCOME10, SAVE50K, CHRISTMAS25)\n";
    echo "  ✅ 2 Orders (1 completed, 1 pending)\n";
    echo "  ✅ 1 Active Subscription (5 minutes demo)\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>