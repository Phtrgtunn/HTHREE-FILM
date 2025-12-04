# 🚀 Test Account - Quick Switch

## Vấn đề hiện tại:
- Đăng nhập Google → user_id là Firebase UID (string)
- Backend cần user_id là số từ MySQL
- Đăng nhập email/password bị CORS error

## ✅ Giải pháp nhanh: Dùng script switch account

### Bước 1: Tạo user test trong database

Chạy SQL này:

```sql
-- Tạo user test với ID = 99
INSERT INTO users (id, username, email, password, full_name, role, is_active, created_at, updated_at)
VALUES (99, 'test_user', 'test@hthree.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User', 'admin', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE id = 99;

-- Thêm gói vào giỏ
DELETE FROM cart WHERE user_id = 99;
INSERT INTO cart (user_id, plan_id, quantity, created_at, updated_at)
VALUES (99, 3, 1, NOW(), NOW());

-- Kiểm tra
SELECT * FROM users WHERE id = 99;
SELECT * FROM cart WHERE user_id = 99;
```

### Bước 2: Switch sang test account

Mở Console (F12) và chạy:

```javascript
// Switch to test account
localStorage.setItem('user', JSON.stringify({
  id: 99,
  email: 'test@hthree.com',
  username: 'test_user',
  full_name: 'Test User',
  role: 'admin'
}));

localStorage.setItem('token', 'test_token_123');

console.log('✅ Switched to test account (ID: 99)');
location.reload();
```

### Bước 3: Test thanh toán

1. Vào `/cart` - Sẽ thấy gói Premium
2. Click "Thanh toán"
3. Điền thông tin và chọn phương thức
4. Click "Xác nhận đặt hàng"
5. Thành công! 🎉

## 🔄 Switch về tài khoản Google

```javascript
// Logout và đăng nhập lại bằng Google
localStorage.clear();
location.reload();
```

## 📝 Tài khoản test có sẵn trong database:

| ID | Email | Username | Password | Role |
|----|-------|----------|----------|------|
| 1 | admin@hthree.com | admin | Admin@123456 | admin |
| 2 | user1@hthree.com | user1 | 123456 | user |
| 99 | test@hthree.com | test_user | Admin@123456 | admin |

## 💡 Lưu ý:

- Đây là giải pháp tạm thời để test
- Cần fix đồng bộ Firebase ↔ MySQL để hoạt động tự động
- Hoặc chỉ dùng 1 hệ thống authentication (Firebase hoặc PHP)

## 🎯 Để fix vĩnh viễn:

Cần sửa code để:
1. Khi đăng nhập Google → Gọi API sync_user.php → Lưu MySQL user_id vào localStorage
2. Hoặc sửa backend để chấp nhận Firebase UID và tự map sang MySQL user_id
3. Hoặc bỏ PHP auth, chỉ dùng Firebase

Nhưng để test nhanh, dùng script switch ở trên là được! 🚀
