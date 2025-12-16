-- Update thời gian gói thành 3 phút để demo cho giảng viên
-- Chạy script này trước khi demo

-- 1. Update tất cả subscription hiện tại thành 3 phút
UPDATE user_subscriptions 
SET 
    start_date = NOW(),
    end_date = DATE_ADD(NOW(), INTERVAL 3 MINUTE),
    status = 'active'
WHERE user_id = 109;

-- 2. Xóa tất cả subscription cũ của user 109 (để clean)
-- DELETE FROM user_subscriptions WHERE user_id = 109;

-- 3. Tạo gói demo mới 3 phút cho user 109
INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status, created_at, updated_at)
VALUES (
    109,
    (SELECT id FROM subscription_plans WHERE slug = 'basic' LIMIT 1),
    NOW(),
    DATE_ADD(NOW(), INTERVAL 3 MINUTE),
    'active',
    NOW(),
    NOW()
);

-- 4. Kiểm tra kết quả
SELECT 
    us.id,
    us.user_id,
    sp.name as plan_name,
    us.start_date,
    us.end_date,
    us.status,
    TIMESTAMPDIFF(SECOND, NOW(), us.end_date) as seconds_remaining,
    TIMESTAMPDIFF(MINUTE, NOW(), us.end_date) as minutes_remaining
FROM user_subscriptions us
JOIN subscription_plans sp ON us.plan_id = sp.id
WHERE us.user_id = 109
ORDER BY us.created_at DESC;