<?php
/**
 * Create tables structure in Railway MySQL
 */

// Railway MySQL credentials
$host = 'crossover.proxy.rlwy.net';
$port = 29616;
$database = 'railway';
$username = 'root';
$password = 'MMpMAyYHqpuxsFyqDJfcUiwWCMkdjnvr';

echo "🚀 Creating tables in Railway MySQL...\n";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected successfully!\n";
    
    // Create tables
    $tables = [
        // Users table
        "CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `full_name` varchar(255) DEFAULT NULL,
            `phone` varchar(20) DEFAULT NULL,
            `password` varchar(255) NOT NULL,
            `role` enum('user','admin') DEFAULT 'user',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        // Subscription plans table
        "CREATE TABLE IF NOT EXISTS `subscription_plans` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `slug` varchar(100) NOT NULL,
            `price` decimal(10,2) NOT NULL,
            `duration_days` int(11) NOT NULL DEFAULT 30,
            `quality` varchar(50) DEFAULT NULL,
            `features` text,
            `is_active` tinyint(1) DEFAULT 1,
            `promotion_badge` tinyint(1) DEFAULT 0,
            `promotion_text` varchar(255) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        // Payment methods table
        "CREATE TABLE IF NOT EXISTS `payment_methods` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `type` varchar(100) NOT NULL,
            `is_active` tinyint(1) DEFAULT 1,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        // Coupons table
        "CREATE TABLE IF NOT EXISTS `coupons` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `code` varchar(50) NOT NULL,
            `name` varchar(255) NOT NULL,
            `discount_type` enum('percentage','fixed') NOT NULL,
            `discount_value` decimal(10,2) NOT NULL,
            `min_order_value` decimal(10,2) DEFAULT 0,
            `max_uses` int(11) DEFAULT NULL,
            `used_count` int(11) DEFAULT 0,
            `is_active` tinyint(1) DEFAULT 1,
            `expires_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `code` (`code`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        // Orders table
        "CREATE TABLE IF NOT EXISTS `orders` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `order_code` varchar(50) NOT NULL,
            `total_price` decimal(10,2) NOT NULL,
            `status` enum('pending','completed','cancelled') DEFAULT 'pending',
            `payment_method_id` int(11) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `order_code` (`order_code`),
            KEY `user_id` (`user_id`),
            KEY `payment_method_id` (`payment_method_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        
        // User subscriptions table
        "CREATE TABLE IF NOT EXISTS `user_subscriptions` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `plan_id` int(11) NOT NULL,
            `order_id` int(11) DEFAULT NULL,
            `start_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `end_date` timestamp NULL DEFAULT NULL,
            `status` enum('active','expired','cancelled','pending') DEFAULT 'active',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            KEY `plan_id` (`plan_id`),
            KEY `order_id` (`order_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];
    
    foreach ($tables as $index => $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ Table " . ($index + 1) . " created successfully\n";
        } catch (Exception $e) {
            echo "⚠️ Table " . ($index + 1) . " warning: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🎉 Tables created! Now run railway_import_final.php\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>