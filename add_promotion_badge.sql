-- =====================================================
-- Thêm cột promotion_badge để hiển thị badge ưu đãi
-- =====================================================

ALTER TABLE subscription_plans 
ADD COLUMN promotion_badge VARCHAR(100) NULL AFTER description,
ADD COLUMN promotion_text VARCHAR(255) NULL AFTER promotion_badge;

-- Ví dụ: Thêm ưu đãi Giáng Sinh cho Basic, Premium, VIP
UPDATE subscription_plans 
SET promotion_badge = '🎄 Ưu đãi Giáng Sinh',
    promotion_text = 'Giảm 30% - Chỉ trong tháng 12!'
WHERE slug IN ('basic', 'premium', 'vip');

-- Kiểm tra kết quả
SELECT id, name, slug, price, promotion_badge, promotion_text 
FROM subscription_plans;

-- =====================================================
-- HƯỚNG DẪN:
-- =====================================================
-- 
-- promotion_badge: Text hiển thị trên badge (VD: "🎄 Ưu đãi Giáng Sinh")
-- promotion_text: Text mô tả chi tiết (VD: "Giảm 30% - Chỉ trong tháng 12!")
-- 
-- Để tắt promotion, set NULL:
-- UPDATE subscription_plans SET promotion_badge = NULL, promotion_text = NULL WHERE slug = 'basic';
-- 
-- Admin có thể sửa qua Admin Panel sau khi update frontend
-- =====================================================
