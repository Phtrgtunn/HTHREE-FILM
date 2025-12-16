-- Cập nhật giá gói subscription cho Railway production
-- Giá test: Basic 2k, Premium 4k, VIP 5k

UPDATE subscription_plans SET price = 2000 WHERE slug = 'basic';
UPDATE subscription_plans SET price = 4000 WHERE slug = 'premium'; 
UPDATE subscription_plans SET price = 5000 WHERE slug = 'vip';

-- Kiểm tra kết quả
SELECT id, name, slug, price, duration_days FROM subscription_plans ORDER BY price;