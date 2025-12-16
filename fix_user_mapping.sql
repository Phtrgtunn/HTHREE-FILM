-- Tạo bảng ánh xạ Firebase UID với MySQL user_id
CREATE TABLE IF NOT EXISTS firebase_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firebase_uid VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) NOT NULL,
    display_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Thêm user test vào bảng
INSERT IGNORE INTO firebase_users (id, firebase_uid, email, display_name) 
VALUES (1, 'demo123', 'test@demo.com', 'Demo User');

-- Kiểm tra dữ liệu
SELECT * FROM firebase_users;