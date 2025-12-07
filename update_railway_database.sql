-- =====================================================
-- UPDATE RAILWAY DATABASE - PROMOTION BADGE SYSTEM
-- =====================================================
-- Chạy script này trên Railway MySQL database
-- để thêm promotion badge cho subscription plans
-- =====================================================

-- Kiểm tra xem columns đã tồn tại chưa
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'railway' 
  AND TABLE_NAME = 'subscription_plans' 
  AND COLUMN_NAME IN ('promotion_badge', 'promotion_text');

-- Nếu chưa có, thêm columns mới
ALTER TABLE subscription_plans 
ADD COLUMN IF NOT EXISTS promotion_badge VARCHAR(100) NULL AFTER description,
ADD COLUMN IF NOT EXISTS promotion_text VARCHAR(255) NULL AFTER promotion_badge;

-- Verify columns đã được thêm
DESCRIBE subscription_plans;

-- Xem dữ liệu hiện tại
SELECT id, name, slug, promotion_badge, promotion_text, is_active 
FROM subscription_plans 
ORDER BY display_order;

-- =====================================================
-- DONE! Giờ có thể thêm promotion badge qua Admin Panel
-- =====================================================
