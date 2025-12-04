-- Thêm cột role vào bảng users
USE hthree_film;

-- Thêm cột role
ALTER TABLE users 
ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER is_active;

-- Cập nhật user có email admin@hthree.com thành admin
UPDATE users 
SET role = 'admin' 
WHERE email = 'admin@hthree.com';

-- Kiểm tra kết quả
SELECT id, email, username, full_name, role, is_active 
FROM users 
WHERE email = 'admin@hthree.com';
