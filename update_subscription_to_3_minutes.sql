-- Cập nhật subscription hiện tại thành 3 phút
UPDATE user_subscriptions 
SET 
    end_date = DATE_ADD(NOW(), INTERVAL 3 MINUTE),
    updated_at = NOW()
WHERE status = 'active' 
AND user_id = (
    SELECT id FROM users 
    WHERE email LIKE '%vng305%' OR username LIKE '%Phạm Trung Tuấn%'
    LIMIT 1
);

-- Kiểm tra kết quả
SELECT 
    id,
    user_id,
    plan_id,
    start_date,
    end_date,
    TIMESTAMPDIFF(MINUTE, NOW(), end_date) as minutes_remaining,
    status
FROM user_subscriptions 
WHERE status = 'active'
ORDER BY created_at DESC 
LIMIT 5;