-- Tạo gói test với thời gian ngắn để test hết hạn
-- Thêm các gói test: 2 phút, 5 phút, 10 phút

INSERT INTO subscription_plans (name, slug, description, price, duration_days, quality, features, is_active) VALUES
('Test 2 Phút', 'test_2min', 'Gói test 2 phút để kiểm tra hết hạn', 1000, 0.00139, 'HD', '["Test hết hạn", "2 phút", "Chỉ để test"]', 1),
('Test 5 Phút', 'test_5min', 'Gói test 5 phút để kiểm tra hết hạn', 1500, 0.00347, 'HD', '["Test hết hạn", "5 phút", "Chỉ để test"]', 1),
('Test 10 Phút', 'test_10min', 'Gói test 10 phút để kiểm tra hết hạn', 2000, 0.00694, 'HD', '["Test hết hạn", "10 phút", "Chỉ để test"]', 1);

-- Kiểm tra kết quả
SELECT id, name, slug, price, duration_days, 
       ROUND(duration_days * 24 * 60, 2) as duration_minutes,
       is_active 
FROM subscription_plans 
WHERE slug LIKE 'test_%' 
ORDER BY duration_days;