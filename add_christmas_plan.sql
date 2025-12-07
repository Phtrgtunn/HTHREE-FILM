-- =====================================================
-- Thêm Gói Giáng Sinh (Christmas Special Plan)
-- Gói đặc biệt mùa lễ hội với giá ưu đãi
-- =====================================================

INSERT INTO subscription_plans (
    name,
    slug,
    description,
    price,
    duration_days,
    quality,
    max_devices,
    has_ads,
    can_download,
    early_access,
    display_order,
    is_active,
    created_at
) VALUES (
    'Giáng Sinh 2024',                          -- Tên gói
    'christmas',                                 -- Slug
    '🎄 Gói đặc biệt mùa Giáng Sinh - Ưu đãi 40% - Chỉ có trong tháng 12!', -- Mô tả
    180000,                                      -- Giá: 180,000đ (giảm 40% từ 300k)
    30,                                          -- Thời hạn: 30 ngày
    'Full HD',                                   -- Chất lượng
    3,                                           -- Số thiết bị: 3
    0,                                           -- Không quảng cáo
    1,                                           -- Có tải xuống
    1,                                           -- Xem sớm
    99,                                          -- Thứ tự hiển thị (cuối cùng)
    1,                                           -- Kích hoạt
    NOW()
);

-- Kiểm tra kết quả
SELECT * FROM subscription_plans WHERE slug = 'christmas';

-- =====================================================
-- HƯỚNG DẪN SỬ DỤNG:
-- =====================================================
-- 1. Mở phpMyAdmin trong AMPPS
-- 2. Chọn database: hthree_film
-- 3. Vào tab SQL
-- 4. Copy toàn bộ script này và paste vào
-- 5. Click "Go" để chạy
-- 6. Refresh trang Pricing để xem gói mới!
--
-- ĐẶC ĐIỂM GÓI GIÁNG SINH:
-- - Giá ưu đãi: 180,000đ (giảm 40%)
-- - Chất lượng: Full HD
-- - 3 thiết bị cùng lúc
-- - Không quảng cáo
-- - Tải xuống được
-- - Xem phim sớm
-- - Thời hạn: 1 tháng
--
-- LƯU Ý: Sau mùa Giáng Sinh, bạn có thể:
-- - Tắt gói: UPDATE subscription_plans SET is_active = 0 WHERE slug = 'christmas';
-- - Hoặc xóa: DELETE FROM subscription_plans WHERE slug = 'christmas';
-- =====================================================
