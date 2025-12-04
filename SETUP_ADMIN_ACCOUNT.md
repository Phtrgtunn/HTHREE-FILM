# 🔐 Hướng dẫn Tạo Tài khoản Admin

## 📋 Thông tin tài khoản Admin

```
📧 Email: admin@hthree.com
🔑 Password: Admin@123456
```

## 🚀 Cách 1: Chạy SQL Script (Khuyến nghị)

### Bước 1: Thêm cột role vào bảng users

1. Mở **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Chọn database `hthree_film`
3. Click tab **SQL**
4. Copy và chạy nội dung file `add_role_column.sql`:

```sql
ALTER TABLE users 
ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER is_active;

UPDATE users 
SET role = 'admin' 
WHERE email = 'admin@hthree.com';
```

### Bước 2: Kiểm tra tài khoản

Tài khoản admin đã có sẵn trong database với:
- Email: `admin@hthree.com`
- Password: `Admin@123456` (đã hash)
- Role: `admin`

## 🔧 Cách 2: Chạy PHP Script

### Bước 1: Thêm cột role (như Cách 1)

### Bước 2: Chạy script PHP

```bash
cd backend
php create_admin.php
```

Script sẽ tự động:
- Tạo hoặc cập nhật tài khoản admin
- Hash password
- Set role = 'admin'

⚠️ **LƯU Ý**: Xóa file `backend/create_admin.php` sau khi chạy xong!

## 🎯 Cách 3: Thêm thủ công qua phpMyAdmin

1. Mở phpMyAdmin
2. Chọn database `hthree_film`
3. Chọn bảng `users`
4. Tìm user có email `admin@hthree.com`
5. Click **Edit**
6. Đổi cột `role` thành `admin`
7. Click **Go**

## ✅ Kiểm tra đăng nhập

### Bước 1: Đăng nhập vào website

1. Mở website: `http://localhost:5173`
2. Click vào icon User ở góc phải navbar
3. Chọn **Đăng nhập**
4. Nhập:
   - Email: `admin@hthree.com`
   - Password: `Admin@123456`

### Bước 2: Truy cập Admin Panel

Sau khi đăng nhập, bạn sẽ thấy:
- Icon bánh răng màu **TÍM** ở navbar (bên cạnh giỏ hàng)
- Click vào icon đó hoặc truy cập: `http://localhost:5173/admin`

## 🔒 Bảo mật

### Đổi mật khẩu ngay sau khi đăng nhập lần đầu:

1. Vào trang Account
2. Chọn tab **Bảo mật**
3. Đổi mật khẩu mới

### Tạo thêm admin khác:

```sql
-- Cập nhật user hiện có thành admin
UPDATE users 
SET role = 'admin' 
WHERE email = 'email_cua_ban@example.com';
```

## 🐛 Troubleshooting

### Không thấy icon Admin?

**Nguyên nhân**: User chưa có role = 'admin'

**Giải pháp**:
```sql
UPDATE users 
SET role = 'admin' 
WHERE email = 'admin@hthree.com';
```

### Không đăng nhập được?

**Nguyên nhân**: Password không đúng hoặc chưa hash

**Giải pháp**: Chạy lại script `create_admin.php` hoặc reset password:

```sql
-- Password mới: Admin@123456
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'admin@hthree.com';
```

### Lỗi "Column 'role' doesn't exist"?

**Nguyên nhân**: Chưa thêm cột role

**Giải pháp**: Chạy file `add_role_column.sql`

## 📝 Ghi chú

- Tài khoản admin đã có sẵn trong file `localhost.sql`
- Chỉ cần thêm cột `role` và set role = 'admin'
- Password đã được hash bằng bcrypt
- Có thể tạo nhiều admin bằng cách set role = 'admin' cho user khác

## 🎉 Hoàn tất!

Sau khi setup xong, bạn có thể:
- ✅ Đăng nhập với tài khoản admin
- ✅ Truy cập Admin Panel
- ✅ Quản lý đơn hàng, khách hàng, gói dịch vụ, mã giảm giá
- ✅ Xem thống kê dashboard
