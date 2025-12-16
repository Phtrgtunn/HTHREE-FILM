-- Import essential data to Railway MySQL
-- Copy this content and run in Railway Query tab

-- Create tables if not exist
CREATE TABLE IF NOT EXISTS `users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `subscription_plans` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `coupons` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_subscriptions` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert demo data
INSERT INTO `users` (`id`, `email`, `full_name`, `phone`, `password`, `role`) VALUES
(109, 'demo@hthree.com', 'Demo User', '0123456789', '$2y$10$example', 'user'),
(1, 'admin@hthree.com', 'Admin', '0987654321', '$2y$10$example', 'admin')
ON DUPLICATE KEY UPDATE 
email = VALUES(email),
full_name = VALUES(full_name);

INSERT INTO `subscription_plans` (`id`, `name`, `slug`, `price`, `duration_days`, `quality`, `features`, `is_active`, `promotion_badge`, `promotion_text`) VALUES
(1, 'Gói Cơ Bản', 'basic', 99000.00, 30, 'HD', 'Xem phim HD không quảng cáo trên 1 thiết bị', 1, 0, ''),
(2, 'Gói Cao Cấp', 'premium', 199000.00, 30, 'Full HD', 'Xem phim Full HD trên 2 thiết bị, tải về được', 1, 1, 'Phổ biến nhất'),
(3, 'Gói VIP', 'vip', 299000.00, 30, '4K', 'Xem phim 4K trên 4 thiết bị, xem trước phim mới', 1, 1, 'Tốt nhất')
ON DUPLICATE KEY UPDATE 
name = VALUES(name),
price = VALUES(price),
features = VALUES(features),
promotion_badge = VALUES(promotion_badge),
promotion_text = VALUES(promotion_text);

INSERT INTO `payment_methods` (`id`, `name`, `type`, `is_active`) VALUES
(1, 'Chuyển khoản ngân hàng', 'bank_transfer', 1),
(2, 'VietQR', 'vietqr', 1),
(3, 'Ví điện tử', 'ewallet', 1)
ON DUPLICATE KEY UPDATE 
name = VALUES(name),
is_active = VALUES(is_active);

INSERT INTO `coupons` (`id`, `code`, `name`, `discount_type`, `discount_value`, `min_order_value`, `max_uses`, `used_count`, `is_active`, `expires_at`) VALUES
(1, 'WELCOME10', 'Giảm 10% cho khách hàng mới', 'percentage', 10.00, 50000.00, 100, 0, 1, DATE_ADD(NOW(), INTERVAL 30 DAY)),
(2, 'SAVE50K', 'Giảm 50K cho đơn từ 200K', 'fixed', 50000.00, 200000.00, 50, 0, 1, DATE_ADD(NOW(), INTERVAL 30 DAY)),
(3, 'CHRISTMAS25', 'Giảm 25% dịp Giáng Sinh', 'percentage', 25.00, 100000.00, 200, 0, 1, '2025-01-31 23:59:59')
ON DUPLICATE KEY UPDATE 
name = VALUES(name),
discount_value = VALUES(discount_value),
is_active = VALUES(is_active);

-- Sample orders for demo
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_price`, `status`, `payment_method_id`) VALUES
(1, 109, 'ORD001', 199000.00, 'completed', 2),
(2, 109, 'ORD002', 299000.00, 'pending', 2)
ON DUPLICATE KEY UPDATE 
status = VALUES(status),
total_price = VALUES(total_price);

-- Sample subscription for demo
INSERT INTO `user_subscriptions` (`id`, `user_id`, `plan_id`, `order_id`, `start_date`, `end_date`, `status`) VALUES
(1, 109, 2, 1, NOW(), DATE_ADD(NOW(), INTERVAL 5 MINUTE), 'active')
ON DUPLICATE KEY UPDATE 
start_date = VALUES(start_date),
end_date = VALUES(end_date),
status = VALUES(status);