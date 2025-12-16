<?php
/**
 * Import data to Railway MySQL using real credentials
 */

// Railway MySQL credentials from variables
$host = 'crossover.proxy.rlwy.net';
$port = 29616;
$database = 'railway';
$username = 'root';
$password = 'MMpMAyYHqpuxsFyqDJfcUiwWCMkdjnvr';

echo "🚀 Connecting to Railway MySQL...\n";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected successfully!\n";
    
    // SQL statements to execute
    $statements = [
        // Users
        "INSERT INTO users (id, email, full_name, phone, password, role) VALUES
        (109, 'demo@hthree.com', 'Demo User', '0123456789', '\$2y\$10\$example', 'user'),
        (1, 'admin@hthree.com', 'Admin', '0987654321', '\$2y\$10\$example', 'admin')
        ON DUPLICATE KEY UPDATE 
        email = VALUES(email), full_name = VALUES(full_name)",
        
        // Subscription plans
        "INSERT INTO subscription_plans (id, name, slug, price, duration_days, quality, features, is_active, promotion_badge, promotion_text) VALUES
        (1, 'Gói Cơ Bản', 'basic', 99000.00, 30, 'HD', 'Xem phim HD không quảng cáo trên 1 thiết bị', 1, 0, ''),
        (2, 'Gói Cao Cấp', 'premium', 199000.00, 30, 'Full HD', 'Xem phim Full HD trên 2 thiết bị, tải về được', 1, 1, 'Phổ biến nhất'),
        (3, 'Gói VIP', 'vip', 299000.00, 30, '4K', 'Xem phim 4K trên 4 thiết bị, xem trước phim mới', 1, 1, 'Tốt nhất')
        ON DUPLICATE KEY UPDATE 
        name = VALUES(name), price = VALUES(price), features = VALUES(features)",
        
        // Payment methods
        "INSERT INTO payment_methods (id, name, type, is_active) VALUES
        (1, 'Chuyển khoản ngân hàng', 'bank_transfer', 1),
        (2, 'VietQR', 'vietqr', 1),
        (3, 'Ví điện tử', 'ewallet', 1)
        ON DUPLICATE KEY UPDATE 
        name = VALUES(name), is_active = VALUES(is_active)",
        
        // Coupons
        "INSERT INTO coupons (id, code, name, discount_type, discount_value, min_order_value, max_uses, used_count, is_active, expires_at) VALUES
        (1, 'WELCOME10', 'Giảm 10% cho khách hàng mới', 'percentage', 10.00, 50000.00, 100, 0, 1, '2025-02-28 23:59:59'),
        (2, 'SAVE50K', 'Giảm 50K cho đơn từ 200K', 'fixed', 50000.00, 200000.00, 50, 0, 1, '2025-02-28 23:59:59'),
        (3, 'CHRISTMAS25', 'Giảm 25% dịp Giáng Sinh', 'percentage', 25.00, 100000.00, 200, 0, 1, '2025-01-31 23:59:59')
        ON DUPLICATE KEY UPDATE 
        name = VALUES(name), discount_value = VALUES(discount_value)",
        
        // Sample orders
        "INSERT INTO orders (id, user_id, order_code, total_price, status, payment_method_id) VALUES
        (1, 109, 'ORD001', 199000.00, 'completed', 2),
        (2, 109, 'ORD002', 299000.00, 'pending', 2)
        ON DUPLICATE KEY UPDATE 
        status = VALUES(status), total_price = VALUES(total_price)",
        
        // Sample subscription
        "INSERT INTO user_subscriptions (id, user_id, plan_id, order_id, start_date, end_date, status) VALUES
        (1, 109, 2, 1, NOW(), DATE_ADD(NOW(), INTERVAL 5 MINUTE), 'active')
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
    
    echo "\n🎉 Import completed!\n";
    echo "📝 Check admin panel: https://hthree-film.vercel.app/admin\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>