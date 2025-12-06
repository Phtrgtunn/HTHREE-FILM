-- Thêm các cột cần thiết cho VietQR payment (SAFE VERSION)
-- Chạy file SQL này trong phpMyAdmin

-- Bước 1: Kiểm tra bảng orders có tồn tại không
SHOW TABLES LIKE 'orders';

-- Bước 2: Xem cấu trúc hiện tại của bảng orders
SHOW COLUMNS FROM orders;

-- Bước 3: Thêm các cột (nếu chưa có)
-- Nếu báo lỗi "Duplicate column name" thì bỏ qua, nghĩa là cột đã tồn tại

-- Thêm qr_code_url
ALTER TABLE orders ADD COLUMN qr_code_url TEXT COMMENT 'URL của mã QR';

-- Thêm transfer_content
ALTER TABLE orders ADD COLUMN transfer_content VARCHAR(255) COMMENT 'Nội dung chuyển khoản';

-- Thêm expires_at
ALTER TABLE orders ADD COLUMN expires_at DATETIME COMMENT 'Thời gian hết hạn đơn hàng';

-- Thêm transaction_id
ALTER TABLE orders ADD COLUMN transaction_id VARCHAR(100) COMMENT 'ID giao dịch từ Casso';

-- Thêm payment_note
ALTER TABLE orders ADD COLUMN payment_note TEXT COMMENT 'Ghi chú thanh toán';

-- Bước 4: Tạo bảng payment_logs
CREATE TABLE IF NOT EXISTS payment_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    order_code VARCHAR(50),
    event_type VARCHAR(50) COMMENT 'webhook_received, payment_verified, subscription_activated',
    message TEXT,
    data JSON COMMENT 'Dữ liệu chi tiết',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_order_code (order_code),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bước 5: Kiểm tra kết quả
SHOW COLUMNS FROM orders;
