<?php
/**
 * Import essential data to Railway database
 * Call this API to populate Railway with demo data
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';

try {
    $conn = getDBConnection();
    
    $results = [];
    
    // Create tables and insert data
    $queries = [
        // Users
        "INSERT INTO users (id, email, full_name, phone, password, role) VALUES
        (109, 'demo@hthree.com', 'Demo User', '0123456789', '\$2y\$10\$example', 'user'),
        (1, 'admin@hthree.com', 'Admin', '0987654321', '\$2y\$10\$example', 'admin')
        ON DUPLICATE KEY UPDATE 
        email = VALUES(email),
        full_name = VALUES(full_name)",
        
        // Subscription plans
        "INSERT INTO subscription_plans (id, name, slug, price, duration_days, quality, features, is_active, promotion_badge, promotion_text) VALUES
        (1, 'Gói Cơ Bản', 'basic', 99000.00, 30, 'HD', 'Xem phim HD không quảng cáo trên 1 thiết bị', 1, 0, ''),
        (2, 'Gói Cao Cấp', 'premium', 199000.00, 30, 'Full HD', 'Xem phim Full HD trên 2 thiết bị, tải về được', 1, 1, 'Phổ biến nhất'),
        (3, 'Gói VIP', 'vip', 299000.00, 30, '4K', 'Xem phim 4K trên 4 thiết bị, xem trước phim mới', 1, 1, 'Tốt nhất')
        ON DUPLICATE KEY UPDATE 
        name = VALUES(name),
        price = VALUES(price),
        features = VALUES(features),
        promotion_badge = VALUES(promotion_badge),
        promotion_text = VALUES(promotion_text)",
        
        // Payment methods
        "INSERT INTO payment_methods (id, name, type, is_active) VALUES
        (1, 'Chuyển khoản ngân hàng', 'bank_transfer', 1),
        (2, 'VietQR', 'vietqr', 1),
        (3, 'Ví điện tử', 'ewallet', 1)
        ON DUPLICATE KEY UPDATE 
        name = VALUES(name),
        is_active = VALUES(is_active)",
        
        // Coupons
        "INSERT INTO coupons (id, code, name, discount_type, discount_value, min_order_value, max_uses, used_count, is_active, expires_at) VALUES
        (1, 'WELCOME10', 'Giảm 10% cho khách hàng mới', 'percentage', 10.00, 50000.00, 100, 0, 1, DATE_ADD(NOW(), INTERVAL 30 DAY)),
        (2, 'SAVE50K', 'Giảm 50K cho đơn từ 200K', 'fixed', 50000.00, 200000.00, 50, 0, 1, DATE_ADD(NOW(), INTERVAL 30 DAY)),
        (3, 'CHRISTMAS25', 'Giảm 25% dịp Giáng Sinh', 'percentage', 25.00, 100000.00, 200, 0, 1, '2025-01-31 23:59:59')
        ON DUPLICATE KEY UPDATE 
        name = VALUES(name),
        discount_value = VALUES(discount_value),
        is_active = VALUES(is_active)",
        
        // Sample orders
        "INSERT INTO orders (id, user_id, order_code, total_price, status, payment_method_id) VALUES
        (1, 109, 'ORD001', 199000.00, 'completed', 2),
        (2, 109, 'ORD002', 299000.00, 'pending', 2)
        ON DUPLICATE KEY UPDATE 
        status = VALUES(status),
        total_price = VALUES(total_price)",
        
        // Sample subscription
        "INSERT INTO user_subscriptions (id, user_id, plan_id, order_id, start_date, end_date, status) VALUES
        (1, 109, 2, 1, NOW(), DATE_ADD(NOW(), INTERVAL 5 MINUTE), 'active')
        ON DUPLICATE KEY UPDATE 
        start_date = VALUES(start_date),
        end_date = VALUES(end_date),
        status = VALUES(status)"
    ];
    
    foreach ($queries as $index => $query) {
        try {
            $conn->query($query);
            $results[] = "✅ Query " . ($index + 1) . " executed successfully";
        } catch (Exception $e) {
            $results[] = "⚠️ Query " . ($index + 1) . " warning: " . $e->getMessage();
        }
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Data import completed',
        'results' => $results,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>