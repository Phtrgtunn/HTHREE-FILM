-- Tạo tài khoản Admin cho HTHREE Film
-- Chạy script này trong phpMyAdmin hoặc MySQL command line

USE hthree_film;

-- Thêm tài khoản admin
-- Email: admin@hthree.com
-- Password: Admin@123456 (đã hash bằng bcrypt)
INSERT INTO users (email, password, full_name, username, role, is_active, created_at, updated_at)
VALUES (
    'admin@hthree.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Password: Admin@123456
    'Administrator',
    'admin',
    'admin',
    1,
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    role = 'admin',
    is_active = 1;

-- Kiểm tra tài khoản đã tạo
SELECT id, email, full_name, username, role, is_active, created_at 
FROM users 
WHERE email = 'admin@hthree.com';

-- Nếu muốn đổi mật khẩu, dùng lệnh này trong PHP:
-- password_hash('Admin@123456', PASSWORD_BCRYPT)
