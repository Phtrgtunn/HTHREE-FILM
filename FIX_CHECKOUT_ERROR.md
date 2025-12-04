# 🔧 FIX LỖI CHECKOUT

## ❌ **LỖI:**
```
Column 'subtotal' cannot be null
Request failed with status code 400
```

## 🔍 **NGUYÊN NHÂN:**
1. Stored procedure `sp_create_order` trả về NULL khi tính subtotal
2. Giỏ hàng có thể trống hoặc không có dữ liệu

## ✅ **GIẢI PHÁP:**

### Bước 1: Chạy SQL fix
Mở **phpMyAdmin** hoặc MySQL client và chạy file:
```
backend/database/fix_create_order_procedure.sql
```

Hoặc copy-paste SQL này:

```sql
DROP PROCEDURE IF EXISTS sp_create_order;

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
    
    SET v_order_code = CONCAT('ORD', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 1000), 3, '0'));
    
    -- FIX: Dùng COALESCE để tránh NULL
    SELECT COALESCE(SUM(sp.price * c.quantity), 0) INTO v_subtotal
    FROM cart c
    JOIN subscription_plans sp ON c.plan_id = sp.id
    WHERE c.user_id = p_user_id;
    
    -- FIX: Kiểm tra giỏ hàng trống
    IF v_subtotal = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cart is empty. Please add items to cart first.';
    END IF;
    
    -- ... rest of procedure ...
END //

DELIMITER ;
```

### Bước 2: Kiểm tra giỏ hàng
Chạy query này để xem giỏ hàng có dữ liệu không:

```sql
SELECT c.*, sp.name, sp.price 
FROM cart c
JOIN subscription_plans sp ON c.plan_id = sp.id
WHERE c.user_id = 1;  -- Thay 1 bằng user_id của bạn
```

Nếu trống → Thêm gói vào giỏ hàng từ trang Pricing

### Bước 3: Test lại
1. Vào `/pricing`
2. Click "Thêm vào giỏ" một gói bất kỳ
3. Vào `/cart` → Kiểm tra có gói
4. Click "Thanh toán ngay"
5. Điền form → "Xác nhận đặt hàng"

## 🧪 **DEBUG:**

### Kiểm tra stored procedure đã update chưa:
```sql
SHOW CREATE PROCEDURE sp_create_order;
```

### Xem log lỗi chi tiết:
Mở Console (F12) → Tab Console → Xem error message

### Test stored procedure trực tiếp:
```sql
CALL sp_create_order(
    1,                    -- user_id
    'Test User',          -- customer_name
    'test@email.com',     -- customer_email
    '0901234567',         -- customer_phone
    'bank_transfer',      -- payment_method
    NULL,                 -- coupon_code
    @order_id,
    @order_code
);

SELECT @order_id, @order_code;
```

## 📝 **CHECKLIST:**

- [ ] Đã chạy file SQL fix
- [ ] Giỏ hàng có ít nhất 1 gói
- [ ] User đã đăng nhập
- [ ] Backend đang chạy
- [ ] Database connection OK

---

Sau khi fix xong, test lại và báo kết quả nhé! 🚀
