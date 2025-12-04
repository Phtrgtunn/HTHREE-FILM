-- Tạo Stored Procedures cho HTHREE Film
USE hthree_film;

-- Drop nếu đã tồn tại
DROP PROCEDURE IF EXISTS sp_create_order;

-- Tạo stored procedure tạo đơn hàng
DELIMITER $$

CREATE PROCEDURE sp_create_order(
    IN p_user_id INT,
    IN p_customer_name VARCHAR(100),
    IN p_customer_email VARCHAR(100),
    IN p_customer_phone VARCHAR(20),
    IN p_payment_method VARCHAR(50),
    IN p_note TEXT,
    IN p_coupon_code VARCHAR(50),
    OUT p_order_id INT,
    OUT p_order_code VARCHAR(20),
    OUT p_total DECIMAL(10,2)
)
BEGIN
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
    DECLARE v_discount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_coupon_id INT DEFAULT NULL;
    DECLARE v_order_code VARCHAR(20);
    
    -- Tạo mã đơn hàng
    SET v_order_code = CONCAT('ORD', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 10000), 4, '0'));
    
    -- Tính tổng tiền từ giỏ hàng
    SELECT SUM(sp.price * c.quantity) INTO v_subtotal
    FROM cart c
    JOIN subscription_plans sp ON c.plan_id = sp.id
    WHERE c.user_id = p_user_id;
    
    -- Kiểm tra và áp dụng mã giảm giá
    IF p_coupon_code IS NOT NULL AND p_coupon_code != '' THEN
        SELECT id INTO v_coupon_id
        FROM coupons
        WHERE code = p_coupon_code
        AND is_active = 1
        AND (end_date IS NULL OR end_date >= NOW())
        AND (usage_limit IS NULL OR usage_count < usage_limit)
        LIMIT 1;
        
        IF v_coupon_id IS NOT NULL THEN
            SELECT 
                CASE 
                    WHEN discount_type = 'percent' THEN v_subtotal * (discount_value / 100)
                    ELSE discount_value
                END INTO v_discount
            FROM coupons
            WHERE id = v_coupon_id;
            
            -- Giới hạn giảm giá tối đa
            SELECT LEAST(v_discount, IFNULL(max_discount, v_discount)) INTO v_discount
            FROM coupons
            WHERE id = v_coupon_id;
        END IF;
    END IF;
    
    -- Tính tổng cuối cùng
    SET p_total = v_subtotal - v_discount;
    
    -- Tạo đơn hàng
    INSERT INTO orders (
        order_code,
        user_id,
        customer_name,
        customer_email,
        customer_phone,
        subtotal,
        discount,
        total,
        payment_method,
        payment_status,
        status,
        note,
        created_at,
        updated_at
    ) VALUES (
        v_order_code,
        p_user_id,
        p_customer_name,
        p_customer_email,
        p_customer_phone,
        v_subtotal,
        v_discount,
        p_total,
        p_payment_method,
        'pending',
        'pending',
        p_note,
        NOW(),
        NOW()
    );
    
    SET p_order_id = LAST_INSERT_ID();
    SET p_order_code = v_order_code;
    
    -- Thêm chi tiết đơn hàng từ giỏ hàng
    INSERT INTO order_items (
        order_id,
        plan_id,
        plan_name,
        plan_price,
        duration_months,
        quantity,
        price,
        total,
        created_at
    )
    SELECT 
        p_order_id,
        c.plan_id,
        sp.name,
        sp.price,
        c.quantity,
        1,
        sp.price,
        sp.price * c.quantity,
        NOW()
    FROM cart c
    JOIN subscription_plans sp ON c.plan_id = sp.id
    WHERE c.user_id = p_user_id;
    
    -- Cập nhật số lần sử dụng coupon
    IF v_coupon_id IS NOT NULL THEN
        UPDATE coupons 
        SET usage_count = usage_count + 1
        WHERE id = v_coupon_id;
        
        -- Lưu lịch sử sử dụng coupon
        INSERT INTO coupon_usage (coupon_id, user_id, order_id, discount_amount, used_at)
        VALUES (v_coupon_id, p_user_id, p_order_id, v_discount, NOW());
    END IF;
    
    -- Xóa giỏ hàng
    DELETE FROM cart WHERE user_id = p_user_id;
    
END$$

DELIMITER ;

-- Test stored procedure
SELECT 'Stored procedure created successfully!' as message;
 