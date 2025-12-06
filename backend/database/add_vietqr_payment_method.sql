-- Thêm 'vietqr' vào ENUM payment_method
-- Chạy SQL này trong phpMyAdmin

ALTER TABLE orders 
MODIFY COLUMN payment_method ENUM('vnpay','momo','zalopay','bank_transfer','cod','vietqr') 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

-- Kiểm tra kết quả
SHOW COLUMNS FROM orders LIKE 'payment_method';
