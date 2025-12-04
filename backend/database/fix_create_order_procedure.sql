-- ============================================
-- FIX: Stored Procedure sp_create_order
-- Sửa lỗi: Column 'subtotal' cannot be null
-- ============================================

-- Xóa procedure cũ
DROP PROCEDURE IF EXISTS sp_create_order;

-- Tạo lại procedure với fix
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
    
    -- FIX: Tính tổng tiền từ giỏ hàng (COALESCE để tránh NULL)
    SELECT COALESCE(SUM(sp.price * c.quantity), 0) INTO v_subtotal
    FROM cart c
    JOIN subscription_plans sp ON c.plan_id = sp.id
    WHERE c.user_id = p_user_id;
    
    -- FIX: Kiểm tra giỏ hàng trống
    IF v_subtotal = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cart is empty. Please add items to cart first.';
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
    INSERT INTO order_items (order_id, plan_id, plan_name, plan_price, duration_months, quantity, subtotal)
    SELECT 
        p_order_id,
        c.plan_id,
        sp.name,
        sp.price,
        c.quantity,
        1,
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

-- Test procedure
-- CALL sp_create_order(1, 'Test User', 'test@email.com', '0901234567', 'bank_transfer', NULL, @order_id, @order_code);
-- SELECT @order_id, @order_code;
