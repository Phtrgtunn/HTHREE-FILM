-- Export data from localhost to Railway
-- Run this in localhost phpMyAdmin first, then import to Railway

-- Users data
INSERT INTO users (id, email, full_name, phone, password, created_at, updated_at) VALUES
(109, 'user@example.com', 'Demo User', '0123456789', '$2y$10$example', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
email = VALUES(email),
full_name = VALUES(full_name),
phone = VALUES(phone);

-- Subscription plans
INSERT INTO subscription_plans (id, name, slug, price, duration_days, quality, features, is_active, promotion_badge, promotion_text, created_at, updated_at) VALUES
(1, 'Gói Cơ Bản', 'basic', 99000, 30, 'HD', 'Xem phim HD, 1 thiết bị', 1, 0, '', NOW(), NOW()),
(2, 'Gói Cao Cấp', 'premium', 199000, 30, 'Full HD', 'Xem phim Full HD, 2 thiết bị, tải về', 1, 1, 'Phổ biến nhất', NOW(), NOW()),
(3, 'Gói VIP', 'vip', 299000, 30, '4K', 'Xem phim 4K, 4 thiết bị, xem trước', 1, 1, 'Tốt nhất', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
name = VALUES(name),
price = VALUES(price),
features = VALUES(features),
promotion_badge = VALUES(promotion_badge),
promotion_text = VALUES(promotion_text);

-- Payment methods
INSERT INTO payment_methods (id, name, type, is_active, created_at, updated_at) VALUES
(1, 'Chuyển khoản ngân hàng', 'bank_transfer', 1, NOW(), NOW()),
(2, 'VietQR', 'vietqr', 1, NOW(), NOW()),
(3, 'Ví điện tử', 'ewallet', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
name = VALUES(name),
is_active = VALUES(is_active);

-- Coupons
INSERT INTO coupons (id, code, name, discount_type, discount_value, min_order_value, max_uses, used_count, is_active, expires_at, created_at, updated_at) VALUES
(1, 'WELCOME10', 'Giảm 10% cho khách hàng mới', 'percentage', 10, 50000, 100, 0, 1, DATE_ADD(NOW(), INTERVAL 30 DAY), NOW(), NOW()),
(2, 'SAVE50K', 'Giảm 50K cho đơn từ 200K', 'fixed', 50000, 200000, 50, 0, 1, DATE_ADD(NOW(), INTERVAL 30 DAY), NOW(), NOW())
ON DUPLICATE KEY UPDATE 
name = VALUES(name),
discount_value = VALUES(discount_value),
is_active = VALUES(is_active);