-- Cập nhật tất cả subscription active thành thời gian phút
UPDATE user_subscriptions 
SET 
    end_date = CASE 
        WHEN plan_id = 1 THEN DATE_ADD(NOW(), INTERVAL 3 MINUTE)  -- Free = 3 phút
        WHEN plan_id = 2 THEN DATE_ADD(NOW(), INTERVAL 5 MINUTE)  -- Basic = 5 phút  
        WHEN plan_id = 3 THEN DATE_ADD(NOW(), INTERVAL 10 MINUTE) -- Premium = 10 phút
        WHEN plan_id = 4 THEN DATE_ADD(NOW(), INTERVAL 15 MINUTE) -- VIP = 15 phút
        ELSE DATE_ADD(NOW(), INTERVAL 3 MINUTE)
    END,
    updated_at = NOW()
WHERE status = 'active';

-- Kiểm tra kết quả
SELECT 
    us.id,
    us.user_id,
    sp.name as plan_name,
    us.start_date,
    us.end_date,
    TIMESTAMPDIFF(MINUTE, NOW(), us.end_date) as minutes_remaining,
    us.status
FROM user_subscriptions us
LEFT JOIN subscription_plans sp ON us.plan_id = sp.id
WHERE us.status = 'active'
ORDER BY us.created_at DESC;