-- ============================================
-- HTHREE FILM - E-COMMERCE SCHEMA
-- Hệ thống bán gói xem phim
-- ============================================

USE hthree_film;

-- ============================================
-- 1. BẢNG SUBSCRIPTION PLANS (Các gói xem phim)
-- ============================================
CREATE TABLE IF NOT EXISTS subscription_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,                    -- Free, Basic, Premium, VIP
    slug VARCHAR(50) UNIQUE NOT NULL,             -- free, basic, premium, vip
    description TEXT,                             -- Mô tả gói
    price DECIMAL(10,2) NOT NULL DEFAULT 0,       -- Giá (VNĐ)
    duration_days INT NOT NULL DEFAULT 30,        -- Thời hạn (ngày)
    
    -- Tính năng
    quality VARCHAR(20) DEFAULT 'SD',             -- SD, HD, FHD, 4K
    max_devices INT DEFAULT 1,                    -- Số thiết bị đồng thời
    has_ads BOOLEAN DEFAULT TRUE,                 -- Có quảng cáo không
    can_download BOOLEAN DEFAULT FALSE,           -- Tải về được không
    early_access BOOLEAN DEFAULT FALSE,           -- Xem trước phim mới
    
    -- Trạng thái
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,                  -- Thứ tự hiển thị
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. BẢNG USER SUBSCRIPTIONS (Gói đang dùng)
-- ============================================
CREATE TABLE IF NOT EXISTS user_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    
    -- Thời gian
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date TIMESTAMP NOT NULL,
    
    -- Trạng thái
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    auto_renew BOOLEAN DEFAULT FALSE,             -- Gia hạn tự động
    
    -- Thanh toán
    order_id INT DEFAULT NULL,                    -- Liên kết đơn hàng
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id),
    
    INDEX idx_user_status (user_id, status),
    INDEX idx_end_date (end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. BẢNG CART (Giỏ hàng)
-- ============================================
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    quantity INT DEFAULT 1,                       -- Số tháng muốn mua
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_cart_item (user_id, plan_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. BẢNG ORDERS (Đơn hàng)
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_code VARCHAR(20) UNIQUE NOT NULL,       -- Mã đơn hàng: ORD20250101001
    user_id INT NOT NULL,
    
    -- Thông tin khách hàng
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    
    -- Giá trị đơn hàng
    subtotal DECIMAL(10,2) NOT NULL,              -- Tổng tiền gói
    discount DECIMAL(10,2) DEFAULT 0,             -- Giảm giá
    total DECIMAL(10,2) NOT NULL,                 -- Tổng thanh toán
    
    -- Thanh toán
    payment_method ENUM('vnpay', 'momo', 'zalopay', 'bank_transfer', 'cod') NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    
    -- Trạng thái đơn hàng
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    
    -- Ghi chú
    note TEXT,
    admin_note TEXT,
    
    -- Thời gian
    paid_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    
    INDEX idx_order_code (order_code),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. BẢNG ORDER ITEMS (Chi tiết đơn hàng)
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    plan_id INT NOT NULL,
    
    -- Thông tin sản phẩm (lưu lại để tránh thay đổi)
    plan_name VARCHAR(50) NOT NULL,
    plan_price DECIMAL(10,2) NOT NULL,
    duration_months INT DEFAULT 1,                -- Số tháng mua
    
    -- Giá
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,                 -- Giá tại thời điểm mua
    total DECIMAL(10,2) NOT NULL,                 -- Tổng = price * quantity
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id),
    
    INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. BẢNG TRANSACTIONS (Lịch sử giao dịch)
-- ============================================
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    
    -- Thông tin giao dịch
    transaction_code VARCHAR(100) UNIQUE,         -- Mã giao dịch từ cổng thanh toán
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    
    -- Trạng thái
    status ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending',
    
    -- Response từ cổng thanh toán
    gateway_response TEXT,                        -- JSON response
    
    -- Thời gian
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id),
    
    INDEX idx_order_id (order_id),
    INDEX idx_transaction_code (transaction_code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. BẢNG COUPONS (Mã giảm giá)
-- ============================================
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,             -- Mã giảm giá: SUMMER2025
    description TEXT,
    
    -- Loại giảm giá
    discount_type ENUM('percent', 'fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,        -- % hoặc số tiền
    
    -- Điều kiện
    min_order_value DECIMAL(10,2) DEFAULT 0,      -- Giá trị đơn tối thiểu
    max_discount DECIMAL(10,2) DEFAULT NULL,      -- Giảm tối đa (cho %)
    
    -- Giới hạn
    usage_limit INT DEFAULT NULL,                 -- Số lần dùng tối đa
    usage_count INT DEFAULT 0,                    -- Đã dùng bao nhiêu lần
    user_limit INT DEFAULT 1,                     -- Mỗi user dùng tối đa
    
    -- Thời gian
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date TIMESTAMP NULL,
    
    -- Trạng thái
    is_active BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_code (code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. BẢNG COUPON USAGE (Lịch sử dùng mã)
-- ============================================
CREATE TABLE IF NOT EXISTS coupon_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coupon_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    
    discount_amount DECIMAL(10,2) NOT NULL,
    
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (coupon_id) REFERENCES coupons(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    
    INDEX idx_coupon_user (coupon_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT DỮ LIỆU MẪU
-- ============================================

-- Các gói xem phim
INSERT INTO subscription_plans (name, slug, description, price, duration_days, quality, max_devices, has_ads, can_download, early_access, display_order) VALUES
('Free', 'free', 'Xem phim miễn phí với quảng cáo, chất lượng SD', 0, 30, 'SD', 1, TRUE, FALSE, FALSE, 1),
('Basic', 'basic', 'Xem phim HD không quảng cáo trên 1 thiết bị', 50000, 30, 'HD', 1, FALSE, FALSE, FALSE, 2),
('Premium', 'premium', 'Xem phim Full HD trên 2 thiết bị, tải về được', 100000, 30, 'FHD', 2, FALSE, TRUE, FALSE, 3),
('VIP', 'vip', 'Xem phim 4K trên 4 thiết bị, xem trước phim mới', 200000, 30, '4K', 4, FALSE, TRUE, TRUE, 4);

-- Mã giảm giá mẫu
INSERT INTO coupons (code, description, discount_type, discount_value, min_order_value, max_discount, usage_limit, start_date, end_date) VALUES
('WELCOME2025', 'Giảm 20% cho đơn hàng đầu tiên', 'percent', 20, 50000, 50000, 100, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY)),
('SUMMER50K', 'Giảm 50k cho đơn từ 100k', 'fixed', 50000, 100000, NULL, 50, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY)),
('VIP30', 'Giảm 30% cho gói VIP', 'percent', 30, 150000, 100000, 20, NOW(), DATE_ADD(NOW(), INTERVAL 90 DAY));

-- ============================================
-- VIEWS & STORED PROCEDURES
-- ============================================

-- View: Gói đang dùng của user
CREATE OR REPLACE VIEW v_active_subscriptions AS
SELECT 
    us.id,
    us.user_id,
    u.username,
    u.email,
    sp.name as plan_name,
    sp.slug as plan_slug,
    sp.quality,
    sp.max_devices,
    us.start_date,
    us.end_date,
    us.status,
    DATEDIFF(us.end_date, NOW()) as days_remaining
FROM user_subscriptions us
JOIN users u ON us.user_id = u.id
JOIN subscription_plans sp ON us.plan_id = sp.id
WHERE us.status = 'active' AND us.end_date > NOW();

-- View: Thống kê đơn hàng
CREATE OR REPLACE VIEW v_order_stats AS
SELECT 
    DATE(created_at) as order_date,
    COUNT(*) as total_orders,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
    SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as total_revenue
FROM orders
GROUP BY DATE(created_at)
ORDER BY order_date DESC;

-- View: Top gói bán chạy
CREATE OR REPLACE VIEW v_top_selling_plans AS
SELECT 
    sp.id,
    sp.name,
    sp.slug,
    sp.price,
    COUNT(oi.id) as total_sold,
    SUM(oi.total) as total_revenue
FROM subscription_plans sp
LEFT JOIN order_items oi ON sp.id = oi.plan_id
LEFT JOIN orders o ON oi.order_id = o.id
WHERE o.payment_status = 'paid'
GROUP BY sp.id, sp.name, sp.slug, sp.price
ORDER BY total_sold DESC;

-- ============================================
-- STORED PROCEDURE: Tạo đơn hàng
-- ============================================
DELIMITER //

CREATE PROCEDURE sp_create_order(
    IN p_user_id INT,
    IN p_customer_name VARCHAR(100),
    IN p_customer_email VARCHAR(100),
    IN p_customer_phone VARCHAR(20),
    IN p_payment_method VARCHAR(50),
    IN p_coupon_code VARCHAR(50),
    OUT p_order_id INT,
    OUT p_order_code VARCHAR(20)
)
BEGIN
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
    DECLARE v_discount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_total DECIMAL(10,2) DEFAULT 0;
    DECLARE v_order_code VARCHAR(20);
    DECLARE v_coupon_id INT DEFAULT NULL;
    
    -- Tạo mã đơn hàng
    SET v_order_code = CONCAT('ORD', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 1000), 3, '0'));
    
    -- Tính tổng tiền từ giỏ hàng
    SELECT COALESCE(SUM(sp.price * c.quantity), 0) INTO v_subtotal
    FROM cart c
    JOIN subscription_plans sp ON c.plan_id = sp.id
    WHERE c.user_id = p_user_id;
    
    -- Kiểm tra giỏ hàng trống
    IF v_subtotal = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cart is empty';
    END IF;
    
    -- Áp dụng mã giảm giá (nếu có)
    IF p_coupon_code IS NOT NULL THEN
        SELECT id INTO v_coupon_id
        FROM coupons
        WHERE code = p_coupon_code
        AND is_active = TRUE
        AND start_date <= NOW()
        AND (end_date IS NULL OR end_date >= NOW())
        AND (usage_limit IS NULL OR usage_count < usage_limit)
        AND v_subtotal >= min_order_value
        LIMIT 1;
        
        IF v_coupon_id IS NOT NULL THEN
            -- Tính giảm giá
            SELECT 
                CASE 
                    WHEN discount_type = 'percent' THEN 
                        LEAST(v_subtotal * discount_value / 100, IFNULL(max_discount, v_subtotal))
                    ELSE discount_value
                END INTO v_discount
            FROM coupons
            WHERE id = v_coupon_id;
        END IF;
    END IF;
    
    SET v_total = v_subtotal - v_discount;
    
    -- Tạo đơn hàng
    INSERT INTO orders (
        order_code, user_id, customer_name, customer_email, customer_phone,
        subtotal, discount, total, payment_method, status, payment_status
    ) VALUES (
        v_order_code, p_user_id, p_customer_name, p_customer_email, p_customer_phone,
        v_subtotal, v_discount, v_total, p_payment_method, 'pending', 'pending'
    );
    
    SET p_order_id = LAST_INSERT_ID();
    SET p_order_code = v_order_code;
    
    -- Thêm chi tiết đơn hàng từ giỏ
    INSERT INTO order_items (order_id, plan_id, plan_name, plan_price, duration_months, quantity, price, total)
    SELECT 
        p_order_id,
        c.plan_id,
        sp.name,
        sp.price,
        c.quantity,
        1,
        sp.price * c.quantity,
        sp.price * c.quantity
    FROM cart c
    JOIN subscription_plans sp ON c.plan_id = sp.id
    WHERE c.user_id = p_user_id;
    
    -- Lưu lịch sử dùng coupon
    IF v_coupon_id IS NOT NULL THEN
        INSERT INTO coupon_usage (coupon_id, user_id, order_id, discount_amount)
        VALUES (v_coupon_id, p_user_id, p_order_id, v_discount);
        
        UPDATE coupons SET usage_count = usage_count + 1 WHERE id = v_coupon_id;
    END IF;
    
    -- Xóa giỏ hàng
    DELETE FROM cart WHERE user_id = p_user_id;
    
END //

DELIMITER ;

-- ============================================
-- FUNCTION: Kiểm tra user có gói active không
-- ============================================
DELIMITER //

CREATE FUNCTION fn_has_active_subscription(p_user_id INT)
RETURNS BOOLEAN
DETERMINISTIC
BEGIN
    DECLARE v_count INT;
    
    SELECT COUNT(*) INTO v_count
    FROM user_subscriptions
    WHERE user_id = p_user_id
    AND status = 'active'
    AND end_date > NOW();
    
    RETURN v_count > 0;
END //

DELIMITER ;

-- ============================================
-- TRIGGER: Tự động kích hoạt gói sau khi thanh toán
-- ============================================
DELIMITER //

CREATE TRIGGER tr_activate_subscription_after_payment
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.payment_status = 'paid' AND OLD.payment_status != 'paid' THEN
        -- Kích hoạt gói cho user
        INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status, order_id)
        SELECT 
            NEW.user_id,
            oi.plan_id,
            NOW(),
            DATE_ADD(NOW(), INTERVAL (sp.duration_days * oi.duration_months) DAY),
            'active',
            NEW.id
        FROM order_items oi
        JOIN subscription_plans sp ON oi.plan_id = sp.id
        WHERE oi.order_id = NEW.id;
        
        -- Cập nhật trạng thái đơn hàng
        UPDATE orders SET status = 'completed', completed_at = NOW() WHERE id = NEW.id;
    END IF;
END //

DELIMITER ;

-- ============================================
-- EVENT: Tự động hết hạn gói
-- ============================================
SET GLOBAL event_scheduler = ON;

DELIMITER //

CREATE EVENT IF NOT EXISTS ev_expire_subscriptions
ON SCHEDULE EVERY 1 HOUR
DO
BEGIN
    UPDATE user_subscriptions
    SET status = 'expired'
    WHERE status = 'active'
    AND end_date < NOW();
END //

DELIMITER ;

