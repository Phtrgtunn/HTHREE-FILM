# 🚀 HƯỚNG DẪN NHANH - CÀI ĐẶT GỬI EMAIL

## ✅ ĐÃ HOÀN THÀNH:

- ✅ Cài đặt PHPMailer thành công
- ✅ Cập nhật frontend để sử dụng PHPMailer
- ✅ Tạo các file cấu hình

## 📋 CÒN LẠI 3 BƯỚC:

### BƯỚC 1: Tạo bảng database (1 phút)

1. Mở AMPPS → phpMyAdmin
2. Chọn database `hthree_film`
3. Mở file: `backend/database/add_password_reset_table.sql`
4. Copy toàn bộ SQL và chạy

### BƯỚC 2: Cấu hình Gmail (5 phút)

#### 2.1. Bật 2FA

- Truy cập: https://myaccount.google.com/security
- Bật "Xác minh 2 bước"

#### 2.2. Tạo App Password

- Truy cập: https://myaccount.google.com/apppasswords
- Chọn Mail → Windows Computer → Generate
- Copy mật khẩu 16 ký tự (ví dụ: `abcd efgh ijkl mnop`)

#### 2.3. Cập nhật file email.php

Mở file: `backend/config/email.php`

Sửa dòng 24-25:

```php
$this->mail->Username   = 'email-cua-ban@gmail.com';  // ← Email của bạn
$this->mail->Password   = 'abcdefghijklmnop';         // ← App Password (bỏ dấu cách)
```

Sửa dòng 29:

```php
$this->mail->setFrom('email-cua-ban@gmail.com', 'HTHREE Film');
```

### BƯỚC 3: Test (1 phút)

1. Vào website → Account → Bảo mật → Mật khẩu
2. Nhập email Gmail của bạn
3. Click "Gửi mã xác thực"
4. Kiểm tra email (cả Inbox và Spam)
5. Nhập mã và đổi mật khẩu

---

## 🎯 VÍ DỤ CẤU HÌNH:

**Giả sử email của bạn là:** `hthreefilm@gmail.com`
**App Password là:** `abcd efgh ijkl mnop`

File `email.php` sẽ như sau:

```php
$this->mail->Username   = 'hthreefilm@gmail.com';
$this->mail->Password   = 'abcdefghijklmnop';  // Bỏ hết dấu cách

$this->mail->setFrom('hthreefilm@gmail.com', 'HTHREE Film');
```

---

## ❓ GẶP VẤN ĐỀ?

### Email không nhận được?

- Kiểm tra thư mục Spam
- Đợi 2-3 phút
- Thử gửi lại

### Lỗi "SMTP connect() failed"?

- Kiểm tra đã bật 2FA chưa
- Kiểm tra App Password có đúng không
- Tắt Firewall/Antivirus tạm thời

### Lỗi "Invalid credentials"?

- Đảm bảo dùng App Password, không phải mật khẩu Gmail thường
- Xóa hết dấu cách trong App Password

---

## 📚 TÀI LIỆU CHI TIẾT:

- `CAU_HINH_GMAIL.md` - Hướng dẫn chi tiết cấu hình Gmail
- `HUONG_DAN_GUI_EMAIL.md` - Hướng dẫn đầy đủ cả 2 phương pháp
- `email.example.php` - File mẫu để tham khảo

---

## ✅ CHECKLIST:

- [ ] Đã chạy SQL tạo bảng
- [ ] Đã bật 2FA cho Gmail
- [ ] Đã tạo App Password
- [ ] Đã cập nhật file email.php
- [ ] Đã test gửi email thành công

---

## 🎉 HOÀN THÀNH!

Sau khi làm xong 3 bước trên, hệ thống sẽ gửi email xác thực thật qua Gmail!
