-- Cập nhật giá test cho các gói subscription
-- Giá nhỏ để dễ test thanh toán VietQR

UPDATE subscription_plans SET price = 2000 WHERE slug = 'basic';
UPDATE subscription_plans SET price = 4000 WHERE slug = 'premium'; 
UPDATE subscription_plans SET price = 5000 WHERE slug = 'vip';

-- Kiểm tra kết quả
SELECT id, name, slug, price, duration_days FROM subscription_plans ORDER BY price;