-- =====================================================
-- CẬP NHẬT DATABASE CHO ADMIN PANEL
-- File này chứa các cập nhật cần thiết cho trang Admin
-- =====================================================

USE `hthree_film`;

-- =====================================================
-- 1. CẬP NHẬT BẢNG ORDERS - Thêm các trường cần thiết
-- =====================================================

-- Thêm cột subtotal nếu chưa có (bỏ qua lỗi nếu đã tồn tại)
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'orders' 
     AND COLUMN_NAME = 'subtotal') = 0,
    'ALTER TABLE `orders` ADD COLUMN `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `customer_phone`',
    'SELECT "Column subtotal already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm cột discount nếu chưa có
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'orders' 
     AND COLUMN_NAME = 'discount') = 0,
    'ALTER TABLE `orders` ADD COLUMN `discount` DECIMAL(10,2) DEFAULT 0.00 AFTER `subtotal`',
    'SELECT "Column discount already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm cột admin_note nếu chưa có
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'orders' 
     AND COLUMN_NAME = 'admin_note') = 0,
    'ALTER TABLE `orders` ADD COLUMN `admin_note` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `note`',
    'SELECT "Column admin_note already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm cột paid_at nếu chưa có
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'orders' 
     AND COLUMN_NAME = 'paid_at') = 0,
    'ALTER TABLE `orders` ADD COLUMN `paid_at` TIMESTAMP NULL DEFAULT NULL AFTER `admin_note`',
    'SELECT "Column paid_at already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm cột completed_at nếu chưa có
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'orders' 
     AND COLUMN_NAME = 'completed_at') = 0,
    'ALTER TABLE `orders` ADD COLUMN `completed_at` TIMESTAMP NULL DEFAULT NULL AFTER `paid_at`',
    'SELECT "Column completed_at already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm cột cancelled_at nếu chưa có
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'orders' 
     AND COLUMN_NAME = 'cancelled_at') = 0,
    'ALTER TABLE `orders` ADD COLUMN `cancelled_at` TIMESTAMP NULL DEFAULT NULL AFTER `completed_at`',
    'SELECT "Column cancelled_at already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- 2. CẬP NHẬT BẢNG ORDER_ITEMS - Thêm trường duration_months
-- =====================================================

-- Thêm cột duration_months nếu chưa có
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'order_items' 
     AND COLUMN_NAME = 'duration_months') = 0,
    'ALTER TABLE `order_items` ADD COLUMN `duration_months` INT DEFAULT 1 AFTER `plan_price`',
    'SELECT "Column duration_months already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Cập nhật subtotal cho các order_items hiện có (nếu cột total tồn tại)
UPDATE `order_items` 
SET `total` = `price` * `quantity` 
WHERE `total` = 0 OR `total` IS NULL;

-- =====================================================
-- 3. TẠO/CẬP NHẬT STORED PROCEDURES CHO ADMIN
-- =====================================================

-- Drop procedures cũ nếu tồn tại
DROP PROCEDURE IF EXISTS `sp_get_admin_statistics`;
DROP PROCEDURE IF EXISTS `sp_get_orders_list`;
DROP PROCEDURE IF EXISTS `sp_confirm_order_payment`;
DROP PROCEDURE IF EXISTS `sp_get_order_details`;

DELIMITER $$

-- Procedure: Lấy thống kê cho Admin Dashboard
CREATE PROCEDURE `sp_get_admin_statistics`()
BEGIN
    -- Tổng doanh thu và đơn hàng đã thanh toán
    SELECT 
        COALESCE(SUM(total), 0) as total_revenue,
        COUNT(*) as total_orders
    FROM orders 
    WHERE payment_status = 'paid';
    
    -- Doanh thu tháng này
    SELECT 
        COALESCE(SUM(total), 0) as month_revenue,
        COUNT(*) as month_orders
    FROM orders 
    WHERE payment_status = 'paid'
    AND MONTH(created_at) = MONTH(CURRENT_DATE())
    AND YEAR(created_at) = YEAR(CURRENT_DATE());
    
    -- Doanh thu tháng trước
    SELECT 
        COALESCE(SUM(total), 0) as last_month_revenue
    FROM orders 
    WHERE payment_status = 'paid'
    AND MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)
    AND YEAR(created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH);
    
    -- Tổng số người dùng
    SELECT COUNT(*) as total_users FROM users;
    
    -- Người dùng mới tháng này
    SELECT COUNT(*) as new_users
    FROM users 
    WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
    AND YEAR(created_at) = YEAR(CURRENT_DATE());
    
    -- Đơn hàng chờ xử lý
    SELECT COUNT(*) as pending_orders
    FROM orders 
    WHERE payment_status = 'pending';
    
    -- Top 5 gói bán chạy
    SELECT 
        sp.id,
        sp.name,
        sp.slug,
        COUNT(oi.id) as order_count,
        SUM(oi.total) as total_revenue
    FROM order_items oi
    JOIN subscription_plans sp ON oi.plan_id = sp.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.payment_status = 'paid'
    GROUP BY sp.id, sp.name, sp.slug
    ORDER BY order_count DESC
    LIMIT 5;
END$$

-- Procedure: Lấy danh sách đơn hàng với filter
CREATE PROCEDURE `sp_get_orders_list`(
    IN p_status VARCHAR(20),
    IN p_search VARCHAR(100),
    IN p_limit INT
)
BEGIN
    SET @sql = 'SELECT 
        o.*,
        DATE_FORMAT(o.created_at, "%d/%m/%Y %H:%i") as created_at_formatted,
        CONCAT(FORMAT(o.total, 0), " đ") as total_formatted
    FROM orders o
    WHERE 1=1';
    
    IF p_status IS NOT NULL AND p_status != '' THEN
        SET @sql = CONCAT(@sql, ' AND o.payment_status = "', p_status, '"');
    END IF;
    
    IF p_search IS NOT NULL AND p_search != '' THEN
        SET @sql = CONCAT(@sql, ' AND (o.order_code LIKE "%', p_search, '%" 
            OR o.customer_name LIKE "%', p_search, '%"
            OR o.customer_email LIKE "%', p_search, '%")');
    END IF;
    
    SET @sql = CONCAT(@sql, ' ORDER BY o.created_at DESC LIMIT ', p_limit);
    
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

-- Procedure: Xác nhận thanh toán đơn hàng
CREATE PROCEDURE `sp_confirm_order_payment`(
    IN p_order_id INT,
    OUT p_success BOOLEAN,
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_order_code VARCHAR(20);
    DECLARE v_user_id INT;
    DECLARE v_payment_status VARCHAR(20);
    
    -- Bắt đầu transaction
    START TRANSACTION;
    
    -- Lấy thông tin đơn hàng
    SELECT order_code, user_id, payment_status 
    INTO v_order_code, v_user_id, v_payment_status
    FROM orders 
    WHERE id = p_order_id;
    
    -- Kiểm tra đơn hàng tồn tại
    IF v_order_code IS NULL THEN
        SET p_success = FALSE;
        SET p_message = 'Không tìm thấy đơn hàng';
        ROLLBACK;
    -- Kiểm tra đã thanh toán chưa
    ELSEIF v_payment_status = 'paid' THEN
        SET p_success = FALSE;
        SET p_message = 'Đơn hàng đã được thanh toán trước đó';
        ROLLBACK;
    ELSE
        -- Cập nhật trạng thái đơn hàng
        UPDATE orders 
        SET payment_status = 'paid',
            paid_at = NOW(),
            status = 'processing',
            updated_at = NOW()
        WHERE id = p_order_id;
        
        -- Kích hoạt subscription (trigger sẽ tự động xử lý)
        
        SET p_success = TRUE;
        SET p_message = CONCAT('Đã xác nhận thanh toán cho đơn hàng ', v_order_code);
        COMMIT;
    END IF;
END$$

-- Procedure: Lấy chi tiết đơn hàng
CREATE PROCEDURE `sp_get_order_details`(IN p_order_id INT)
BEGIN
    -- Thông tin đơn hàng
    SELECT * FROM orders WHERE id = p_order_id;
    
    -- Chi tiết sản phẩm trong đơn
    SELECT 
        oi.*,
        sp.name as plan_name,
        sp.slug as plan_slug,
        sp.quality,
        sp.max_devices
    FROM order_items oi
    JOIN subscription_plans sp ON oi.plan_id = sp.id
    WHERE oi.order_id = p_order_id;
    
    -- Thông tin subscription được tạo
    SELECT 
        us.*,
        sp.name as plan_name,
        sp.slug as plan_slug,
        DATEDIFF(us.end_date, NOW()) as days_remaining
    FROM user_subscriptions us
    JOIN subscription_plans sp ON us.plan_id = sp.id
    WHERE us.order_id = p_order_id;
END$$

DELIMITER ;

-- =====================================================
-- 4. TẠO/CẬP NHẬT VIEWS CHO ADMIN
-- =====================================================

-- View: Thống kê đơn hàng theo ngày
DROP VIEW IF EXISTS `v_daily_order_stats`;
CREATE VIEW `v_daily_order_stats` AS
SELECT 
    DATE(created_at) as order_date,
    COUNT(*) as total_orders,
    SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_orders,
    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed_orders,
    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as total_revenue
FROM orders
GROUP BY DATE(created_at)
ORDER BY order_date DESC;

-- View: Thống kê người dùng
DROP VIEW IF EXISTS `v_user_statistics`;
CREATE VIEW `v_user_statistics` AS
SELECT 
    u.id,
    u.username,
    u.email,
    u.role,
    u.created_at,
    COUNT(DISTINCT o.id) as total_orders,
    SUM(CASE WHEN o.payment_status = 'paid' THEN o.total ELSE 0 END) as total_spent,
    COUNT(DISTINCT us.id) as active_subscriptions,
    MAX(o.created_at) as last_order_date
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
LEFT JOIN user_subscriptions us ON u.id = us.user_id AND us.status = 'active'
GROUP BY u.id, u.username, u.email, u.role, u.created_at;

-- View: Thống kê gói subscription
DROP VIEW IF EXISTS `v_plan_statistics`;
CREATE VIEW `v_plan_statistics` AS
SELECT 
    sp.id,
    sp.name,
    sp.slug,
    sp.price,
    sp.duration_days,
    COUNT(DISTINCT oi.id) as times_purchased,
    SUM(oi.quantity) as total_quantity_sold,
    SUM(oi.total) as total_revenue,
    COUNT(DISTINCT us.id) as active_subscriptions,
    COUNT(DISTINCT CASE WHEN us.status = 'active' THEN us.user_id END) as active_users
FROM subscription_plans sp
LEFT JOIN order_items oi ON sp.id = oi.plan_id
LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'paid'
LEFT JOIN user_subscriptions us ON sp.id = us.plan_id
GROUP BY sp.id, sp.name, sp.slug, sp.price, sp.duration_days;

-- =====================================================
-- 5. TẠO INDEXES ĐỂ TỐI ƯU PERFORMANCE
-- =====================================================

-- Index cho orders (bỏ qua lỗi nếu đã tồn tại)
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'orders' 
     AND INDEX_NAME = 'idx_orders_payment_created') = 0,
    'CREATE INDEX idx_orders_payment_created ON orders(payment_status, created_at DESC)',
    'SELECT "Index idx_orders_payment_created already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'orders' 
     AND INDEX_NAME = 'idx_orders_user_payment') = 0,
    'CREATE INDEX idx_orders_user_payment ON orders(user_id, payment_status)',
    'SELECT "Index idx_orders_user_payment already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index cho order_items
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'order_items' 
     AND INDEX_NAME = 'idx_order_items_plan') = 0,
    'CREATE INDEX idx_order_items_plan ON order_items(plan_id, order_id)',
    'SELECT "Index idx_order_items_plan already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index cho user_subscriptions
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'user_subscriptions' 
     AND INDEX_NAME = 'idx_subscriptions_status_end') = 0,
    'CREATE INDEX idx_subscriptions_status_end ON user_subscriptions(status, end_date)',
    'SELECT "Index idx_subscriptions_status_end already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'user_subscriptions' 
     AND INDEX_NAME = 'idx_subscriptions_user_status') = 0,
    'CREATE INDEX idx_subscriptions_user_status ON user_subscriptions(user_id, status, end_date)',
    'SELECT "Index idx_subscriptions_user_status already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- 6. CẬP NHẬT DỮ LIỆU HIỆN CÓ
-- =====================================================

-- Cập nhật paid_at cho các đơn đã thanh toán
UPDATE orders 
SET paid_at = updated_at 
WHERE payment_status = 'paid' AND paid_at IS NULL;

-- Cập nhật completed_at cho các đơn đã hoàn thành
UPDATE orders 
SET completed_at = updated_at 
WHERE status = 'completed' AND completed_at IS NULL;

-- Cập nhật subtotal cho orders nếu chưa có
UPDATE orders o
SET o.subtotal = (
    SELECT COALESCE(SUM(oi.total), 0)
    FROM order_items oi
    WHERE oi.order_id = o.id
)
WHERE o.subtotal = 0;

-- =====================================================
-- 7. TẠO TRIGGER TỰ ĐỘNG CẬP NHẬT TIMESTAMPS
-- =====================================================

DROP TRIGGER IF EXISTS `tr_update_order_paid_at`;
DELIMITER $$
CREATE TRIGGER `tr_update_order_paid_at`
BEFORE UPDATE ON `orders`
FOR EACH ROW
BEGIN
    -- Tự động set paid_at khi payment_status chuyển sang paid
    IF NEW.payment_status = 'paid' AND OLD.payment_status != 'paid' THEN
        SET NEW.paid_at = NOW();
        IF NEW.status = 'pending' THEN
            SET NEW.status = 'processing';
        END IF;
    END IF;
    
    -- Tự động set completed_at khi status chuyển sang completed
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
        SET NEW.completed_at = NOW();
    END IF;
    
    -- Tự động set cancelled_at khi status chuyển sang cancelled
    IF NEW.status = 'cancelled' AND OLD.status != 'cancelled' THEN
        SET NEW.cancelled_at = NOW();
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- 8. THÊM DỮ LIỆU MẪU CHO TESTING (OPTIONAL)
-- =====================================================

-- Uncomment để thêm dữ liệu test
/*
-- Thêm admin note cho một số đơn hàng
UPDATE orders 
SET admin_note = 'Đơn hàng đã được xác nhận và kích hoạt subscription'
WHERE payment_status = 'paid' 
LIMIT 5;
*/

-- =====================================================
-- HOÀN TẤT
-- =====================================================

SELECT 'Database đã được cập nhật thành công!' as message;
SELECT 'Các stored procedures đã được tạo:' as info;
SELECT ROUTINE_NAME, ROUTINE_TYPE 
FROM information_schema.ROUTINES 
WHERE ROUTINE_SCHEMA = 'hthree_film' 
AND ROUTINE_NAME LIKE 'sp_%';

SELECT 'Các views đã được tạo:' as info;
SELECT TABLE_NAME 
FROM information_schema.VIEWS 
WHERE TABLE_SCHEMA = 'hthree_film';
