-- Kiểm tra trạng thái đơn hàng
-- Chạy trong phpMyAdmin hoặc MySQL client

-- 1. Xem chi tiết đơn hàng
SELECT 
    id,
    order_code,
    user_id,
    total,
    payment_method,
    payment_status,
    status,
    paid_at,
    created_at
FROM orders 
WHERE order_code IN ('ORD20251284144', 'ORD20251284621', 'ORD20251125535', 'ORD20251125481', 'ORD20251125738', 'ORD20251125728')
ORDER BY created_at DESC;

-- 2. Kiểm tra subscription đã được tạo chưa
SELECT 
    us.id,
    us.user_id,
    us.plan_id,
    us.order_id,
    us.status,
    us.start_date,
    us.end_date,
    o.order_code
FROM user_subscriptions us
LEFT JOIN orders o ON us.order_id = o.id
WHERE o.order_code IN ('ORD20251284144', 'ORD20251284621', 'ORD20251125535', 'ORD20251125481', 'ORD20251125738', 'ORD20251125728')
ORDER BY us.created_at DESC;

-- 3. Nếu đơn hàng đã thanh toán nhưng chưa có subscription
-- Cần kích hoạt thủ công bằng cách update payment_status
UPDATE orders 
SET payment_status = 'paid',
    paid_at = NOW(),
    status = 'completed',
    completed_at = NOW()
WHERE order_code = 'ORD20251284144' -- Thay bằng mã đơn cần kích hoạt
AND payment_status = 'pending';

-- 4. Sau đó gọi API để kích hoạt subscription
-- POST http://localhost/HTHREE_film/backend/api/payment/activate_subscription.php
-- Body: {"order_code": "ORD20251284144"}
