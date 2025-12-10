-- Kiểm tra cấu trúc bảng subscription_plans
DESCRIBE subscription_plans;

-- Xem dữ liệu hiện tại của các gói
SELECT id, name, slug, promotion_badge, promotion_text, is_active 
FROM subscription_plans 
ORDER BY display_order;

-- Test update promotion badge cho gói Basic (id=1)
-- UPDATE subscription_plans 
-- SET promotion_badge = '🎄 Giáng Sinh', 
--     promotion_text = 'Giảm 30% - Chỉ hôm nay!'
-- WHERE id = 1;

-- Test remove promotion badge
-- UPDATE subscription_plans 
-- SET promotion_badge = NULL,    
--     promotion_text = NULL
-- WHERE id = 1;
