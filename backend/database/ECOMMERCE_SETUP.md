# 🛒 Hướng dẫn cài đặt hệ thống E-Commerce

## 📋 Tổng quan

Hệ thống bán **gói xem phim** (subscription-based) với đầy đủ tính năng:
- ✅ Quản lý gói xem phim (Free, Basic, Premium, VIP)
- ✅ Giỏ hàng
- ✅ Đặt hàng & Thanh toán
- ✅ Mã giảm giá
- ✅ Quản lý đơn hàng
- ✅ Tự động kích hoạt gói sau thanh toán

## 🗄️ Cài đặt Database

### Bước 1: Import Schema

1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Chọn database `hthree_film`
3. Click tab **SQL**
4. Copy toàn bộ nội dung file `ecommerce_schema.sql`
5. Paste và click **Go**

### Bước 2: Kiểm tra

Database sẽ có thêm các bảng:

**Sản phẩm:**
- ✅ `subscription_plans` - Các gói xem phim
- ✅ `user_subscriptions` - Gói đang dùng của user

**Giỏ hàng & Đơn hàng:**
- ✅ `cart` - Giỏ hàng
- ✅ `orders` - Đơn hàng
- ✅ `order_items` - Chi tiết đơn hàng
- ✅ `transactions` - Lịch sử giao dịch

**Khuyến mãi:**
- ✅ `coupons` - Mã giảm giá
- ✅ `coupon_usage` - Lịch sử dùng mã

## 📦 Dữ liệu mẫu

### Các gói xem phim

| Gói | Giá | Chất lượng | Thiết bị | Quảng cáo | Tải về | Xem trước |
|-----|-----|------------|----------|-----------|---------|-----------|
| **Free** | 0đ | SD | 1 | ✅ | ❌ | ❌ |
| **Basic** | 50,000đ/tháng | HD | 1 | ❌ | ❌ | ❌ |
| **Premium** | 100,000đ/tháng | Full HD | 2 | ❌ | ✅ | ❌ |
| **VIP** | 200,000đ/tháng | 4K | 4 | ❌ | ✅ | ✅ |

### Mã giảm giá mẫu

- `WELCOME2025` - Giảm 20% cho đơn đầu tiên (tối đa 50k)
- `SUMMER50K` - Giảm 50k cho đơn từ 100k
- `VIP30` - Giảm 30% cho gói VIP (tối đa 100k)

## 🔄 Quy trình mua hàng

### 1. Xem gói & Thêm vào giỏ

```sql
-- User xem các gói
SELECT * FROM subscription_plans WHERE is_active = TRUE;

-- Thêm gói vào giỏ
INSERT INTO cart (user_id, plan_id, quantity) 
VALUES (1, 3, 1)  -- User 1 mua gói Premium 1 tháng
ON DUPLICATE KEY UPDATE quantity = quantity + 1;
```

### 2. Xem giỏ hàng

```sql
SELECT 
    c.id,
    sp.name,
    sp.price,
    c.quantity,
    (sp.price * c.quantity) as total
FROM cart c
JOIN subscription_plans sp ON c.plan_id = sp.id
WHERE c.user_id = 1;
```

### 3. Tạo đơn hàng

```sql
CALL sp_create_order(
    1,                          -- user_id
    'Nguyen Van A',             -- customer_name
    'user@example.com',         -- customer_email
    '0901234567',               -- customer_phone
    'vnpay',                    -- payment_method
    'WELCOME2025',              -- coupon_code (hoặc NULL)
    @order_id,                  -- OUT: order_id
    @order_code                 -- OUT: order_code
);

SELECT @order_id, @order_code;
```

### 4. Thanh toán

```sql
-- Cập nhật trạng thái thanh toán
UPDATE orders 
SET payment_status = 'paid', paid_at = NOW()
WHERE id = @order_id;

-- Trigger tự động kích hoạt gói cho user
```

### 5. Kiểm tra gói đã kích hoạt

```sql
-- Xem gói active của user
SELECT * FROM v_active_subscriptions WHERE user_id = 1;

-- Hoặc dùng function
SELECT fn_has_active_subscription(1);  -- Returns 1 (TRUE) hoặc 0 (FALSE)
```

## 📊 Các View hữu ích

### 1. Gói đang dùng

```sql
SELECT * FROM v_active_subscriptions;
```

### 2. Thống kê đơn hàng theo ngày

```sql
SELECT * FROM v_order_stats;
```

### 3. Top gói bán chạy

```sql
SELECT * FROM v_top_selling_plans;
```

## 🔧 Stored Procedures & Functions

### 1. Tạo đơn hàng

```sql
CALL sp_create_order(
    user_id, 
    customer_name, 
    customer_email, 
    customer_phone, 
    payment_method, 
    coupon_code,
    @order_id, 
    @order_code
);
```

### 2. Kiểm tra gói active

```sql
SELECT fn_has_active_subscription(user_id);
```

## ⚙️ Triggers & Events

### Trigger: Tự động kích hoạt gói

Khi đơn hàng được thanh toán (`payment_status = 'paid'`):
- ✅ Tự động tạo `user_subscriptions`
- ✅ Tính thời gian hết hạn
- ✅ Cập nhật trạng thái đơn hàng thành `completed`

### Event: Tự động hết hạn gói

Chạy mỗi giờ để:
- ✅ Kiểm tra gói đã hết hạn
- ✅ Cập nhật status thành `expired`

## 🧪 Test Cases

### Test 1: Mua gói Basic

```sql
-- 1. Thêm vào giỏ
INSERT INTO cart (user_id, plan_id, quantity) VALUES (1, 2, 1);

-- 2. Tạo đơn
CALL sp_create_order(1, 'Test User', 'test@test.com', '0901234567', 'vnpay', NULL, @oid, @ocode);

-- 3. Thanh toán
UPDATE orders SET payment_status = 'paid', paid_at = NOW() WHERE id = @oid;

-- 4. Kiểm tra
SELECT * FROM v_active_subscriptions WHERE user_id = 1;
```

### Test 2: Dùng mã giảm giá

```sql
-- Mua gói Premium với mã WELCOME2025
CALL sp_create_order(1, 'Test User', 'test@test.com', '0901234567', 'vnpay', 'WELCOME2025', @oid, @ocode);

-- Xem đơn hàng
SELECT * FROM orders WHERE id = @oid;
-- discount = 20,000đ (20% của 100,000đ)
-- total = 80,000đ
```

### Test 3: Kiểm tra hết hạn

```sql
-- Tạo gói hết hạn
INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status)
VALUES (1, 2, DATE_SUB(NOW(), INTERVAL 31 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 'active');

-- Chạy event thủ công
UPDATE user_subscriptions SET status = 'expired' WHERE status = 'active' AND end_date < NOW();

-- Kiểm tra
SELECT * FROM user_subscriptions WHERE user_id = 1;
```

## 📈 Thống kê

### Doanh thu theo tháng

```sql
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as total_orders,
    SUM(total) as revenue
FROM orders
WHERE payment_status = 'paid'
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC;
```

### Top khách hàng

```sql
SELECT 
    u.username,
    u.email,
    COUNT(o.id) as total_orders,
    SUM(o.total) as total_spent
FROM users u
JOIN orders o ON u.id = o.user_id
WHERE o.payment_status = 'paid'
GROUP BY u.id, u.username, u.email
ORDER BY total_spent DESC
LIMIT 10;
```

### Tỷ lệ chuyển đổi

```sql
SELECT 
    COUNT(DISTINCT user_id) as total_users,
    COUNT(DISTINCT CASE WHEN payment_status = 'paid' THEN user_id END) as paid_users,
    ROUND(COUNT(DISTINCT CASE WHEN payment_status = 'paid' THEN user_id END) * 100.0 / COUNT(DISTINCT user_id), 2) as conversion_rate
FROM orders;
```

## 🚀 Bước tiếp theo

Sau khi setup database xong, bạn cần:

1. **Backend API** - Tạo các endpoint:
   - `/api/plans.php` - Lấy danh sách gói
   - `/api/cart.php` - Quản lý giỏ hàng
   - `/api/orders.php` - Tạo & quản lý đơn hàng
   - `/api/payment/vnpay.php` - Tích hợp VNPay
   - `/api/coupons.php` - Kiểm tra mã giảm giá

2. **Frontend Pages** - Tạo các trang:
   - `/pricing` - Trang giá gói
   - `/cart` - Giỏ hàng
   - `/checkout` - Thanh toán
   - `/orders` - Lịch sử đơn hàng
   - `/admin/orders` - Quản lý đơn (admin)

3. **Payment Integration** - Tích hợp:
   - VNPay
   - MoMo
   - ZaloPay

Bạn muốn mình tiếp tục tạo phần nào trước?

