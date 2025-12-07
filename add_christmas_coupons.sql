-- =====================================================
-- Thêm Mã Giảm Giá Giáng Sinh (Christmas Coupons)
-- Các mã giảm giá đặc biệt cho mùa lễ hội
-- =====================================================

-- Mã 1: GIANGSINH2024 - Giảm 20%
INSERT INTO coupons (
    code,
    description,
    discount_type,
    discount_value,
    min_order_value,
    max_discount,
    usage_limit,
    user_limit,
    start_date,
    end_date,
    is_active,
    created_at
) VALUES (
    'GIANGSINH2024',                            -- Mã giảm giá
    '🎅 Giảm 20% cho tất cả gói - Mừng Giáng Sinh 2024', -- Mô tả
    'percent',                                   -- Loại: Phần trăm
    20,                                          -- Giảm 20%
    100000,                                      -- Đơn tối thiểu: 100,000đ
    100000,                                      -- Giảm tối đa: 100,000đ
    100,                                         -- Giới hạn: 100 lượt dùng
    1,                                           -- Mỗi người dùng 1 lần
    '2024-12-01 00:00:00',                      -- Bắt đầu: 1/12/2024
    '2024-12-31 23:59:59',                      -- Kết thúc: 31/12/2024
    1,                                           -- Kích hoạt
    NOW()
);

-- Mã 2: NOEL50K - Giảm 50,000đ
INSERT INTO coupons (
    code,
    description,
    discount_type,
    discount_value,
    min_order_value,
    max_discount,
    usage_limit,
    user_limit,
    start_date,
    end_date,
    is_active,
    created_at
) VALUES (
    'NOEL50K',                                   -- Mã giảm giá
    '🎄 Giảm 50,000đ - Quà tặng Noel đặc biệt', -- Mô tả
    'fixed',                                     -- Loại: Số tiền cố định
    50000,                                       -- Giảm 50,000đ
    200000,                                      -- Đơn tối thiểu: 200,000đ
    NULL,                                        -- Không giới hạn giảm tối đa
    50,                                          -- Giới hạn: 50 lượt dùng
    1,                                           -- Mỗi người dùng 1 lần
    '2024-12-20 00:00:00',                      -- Bắt đầu: 20/12/2024
    '2024-12-26 23:59:59',                      -- Kết thúc: 26/12/2024 (tuần lễ Noel)
    1,                                           -- Kích hoạt
    NOW()
);

-- Mã 3: NAMMOIVUI - Giảm 30%
INSERT INTO coupons (
    code,
    description,
    discount_type,
    discount_value,
    min_order_value,
    max_discount,
    usage_limit,
    user_limit,
    start_date,
    end_date,
    is_active,
    created_at
) VALUES (
    'NAMMOIVUI',                                 -- Mã giảm giá
    '🎉 Giảm 30% - Chào đón năm mới 2025',     -- Mô tả
    'percent',                                   -- Loại: Phần trăm
    30,                                          -- Giảm 30%
    150000,                                      -- Đơn tối thiểu: 150,000đ
    150000,                                      -- Giảm tối đa: 150,000đ
    200,                                         -- Giới hạn: 200 lượt dùng
    1,                                           -- Mỗi người dùng 1 lần
    '2024-12-28 00:00:00',                      -- Bắt đầu: 28/12/2024
    '2025-01-05 23:59:59',                      -- Kết thúc: 5/1/2025
    1,                                           -- Kích hoạt
    NOW()
);

-- Kiểm tra kết quả
SELECT 
    code,
    description,
    discount_type,
    discount_value,
    usage_limit,
    DATE_FORMAT(start_date, '%d/%m/%Y') as bat_dau,
    DATE_FORMAT(end_date, '%d/%m/%Y') as ket_thuc,
    is_active
FROM coupons 
WHERE code IN ('GIANGSINH2024', 'NOEL50K', 'NAMMOIVUI')
ORDER BY start_date;

-- =====================================================
-- HƯỚNG DẪN SỬ DỤNG:
-- =====================================================
-- 1. Mở phpMyAdmin trong AMPPS
-- 2. Chọn database: hthree_film
-- 3. Vào tab SQL
-- 4. Copy toàn bộ script này và paste vào
-- 5. Click "Go" để chạy
-- 6. Vào Admin Panel → Tab "Mã giảm giá" để xem!
--
-- CHI TIẾT CÁC MÃ:
-- =====================================================
-- 
-- 1. GIANGSINH2024 (1/12 - 31/12/2024)
--    - Giảm 20% cho tất cả gói
--    - Đơn tối thiểu: 100,000đ
--    - Giảm tối đa: 100,000đ
--    - 100 lượt dùng
--
-- 2. NOEL50K (20/12 - 26/12/2024)
--    - Giảm 50,000đ cố định
--    - Đơn tối thiểu: 200,000đ
--    - 50 lượt dùng
--    - Chỉ áp dụng tuần lễ Noel
--
-- 3. NAMMOIVUI (28/12/2024 - 5/1/2025)
--    - Giảm 30% chào năm mới
--    - Đơn tối thiểu: 150,000đ
--    - Giảm tối đa: 150,000đ
--    - 200 lượt dùng
--
-- =====================================================
-- QUẢN LÝ SAU MÙA LỄ HỘI:
-- =====================================================
-- 
-- Tắt tất cả mã Giáng Sinh:
-- UPDATE coupons SET is_active = 0 
-- WHERE code IN ('GIANGSINH2024', 'NOEL50K', 'NAMMOIVUI');
--
-- Xóa tất cả mã Giáng Sinh:
-- DELETE FROM coupons 
-- WHERE code IN ('GIANGSINH2024', 'NOEL50K', 'NAMMOIVUI');
--
-- =====================================================
