# 📧 HƯỚNG DẪN CÀI ĐẶT GỬI EMAIL QUA GMAIL

## 🎯 Có 2 cách để gửi email:

### ✅ CÁCH 1: Sử dụng PHPMailer (Khuyến nghị - Chuyên nghiệp)

### ✅ CÁCH 2: Sử dụng mail() function (Đơn giản - Đã cài sẵn)

---

## 📌 CÁCH 1: SỬ DỤNG PHPMAILER (Khuyến nghị)

### Bước 1: Cài đặt Composer

1. Tải Composer tại: https://getcomposer.org/download/
2. Cài đặt Composer vào máy

### Bước 2: Cài đặt PHPMailer

Mở Command Prompt và chạy:

```bash
cd C:\Program Files\Ampps\www\HTHREE_Film\backend
composer install
```

### Bước 3: Cấu hình Gmail

#### 3.1. Bật xác thực 2 bước

1. Truy cập: https://myaccount.google.com/security
2. Tìm "Xác minh 2 bước" → Bật lên

#### 3.2. Tạo App Password

1. Truy cập: https://myaccount.google.com/apppasswords
2. Chọn "Mail" và "Windows Computer"
3. Click "Generate"
4. Sao chép mật khẩu 16 ký tự (ví dụ: abcd efgh ijkl mnop)

#### 3.3. Cập nhật file config/email.php

Mở file `backend/config/email.php` và sửa:

```php
$this->mail->Username   = 'email-cua-ban@gmail.com';  // Thay email của bạn
$this->mail->Password   = 'abcdefghijklmnop';         // App Password (bỏ dấu cách)
```

Và dòng setFrom:

```php
$this->mail->setFrom('email-cua-ban@gmail.com', 'HTHREE Film');
```

### Bước 4: Sử dụng API với PHPMailer

Trong file `src/pages/Account.vue`, đổi URL API thành:

```javascript
"http://localhost/HTHREE_Film/backend/api/change-password.php";
```

---

## 📌 CÁCH 2: SỬ DỤNG MAIL() FUNCTION (Đơn giản - Đã sẵn sàng)

### ✅ Ưu điểm:

- Không cần cài Composer
- Không cần cấu hình Gmail App Password
- Đã được tích hợp sẵn

### ⚠️ Nhược điểm:

- Email có thể vào Spam
- Cần cấu hình SMTP trong php.ini (AMPPS đã cấu hình sẵn)

### Cách sử dụng:

**Không cần làm gì thêm!** Frontend đã được cấu hình sử dụng API đơn giản:

```javascript
"http://localhost/HTHREE_Film/backend/api/change-password-simple.php";
```

---

## 🚀 KIỂM TRA HOẠT ĐỘNG

### Bước 1: Tạo bảng database

1. Mở AMPPS → phpMyAdmin
2. Chọn database `hthree_film`
3. Mở file `backend/database/add_password_reset_table.sql`
4. Copy và chạy SQL

### Bước 2: Test gửi email

1. Vào trang web → Account → Bảo mật → Mật khẩu
2. Nhập email và click "Gửi mã xác thực"
3. Kiểm tra:
   - **Nếu gửi thành công**: Kiểm tra email (cả Inbox và Spam)
   - **Nếu không gửi được**: Mã sẽ hiển thị trong thông báo toast

### Bước 3: Đổi mật khẩu

1. Nhập mã 6 số nhận được
2. Nhập mật khẩu mới (tối thiểu 6 ký tự)
3. Xác nhận mật khẩu
4. Click "Đổi mật khẩu"

---

## 🔧 XỬ LÝ LỖI

### Lỗi: "SMTP connect() failed" (Cách 1)

**Nguyên nhân:**

- Chưa bật 2FA
- App Password sai
- Firewall chặn port 587

**Giải pháp:**

1. Kiểm tra lại App Password
2. Tắt firewall/antivirus tạm thời
3. Thử tạo lại App Password mới

### Lỗi: Email không nhận được (Cả 2 cách)

**Giải pháp:**

1. Kiểm tra thư mục Spam
2. Đợi 2-3 phút
3. Kiểm tra email có đúng không
4. Nếu vẫn không được, mã sẽ hiển thị trong toast để test

### Lỗi: "Email không tồn tại trong hệ thống"

**Giải pháp:**

- Đảm bảo email đã đăng ký tài khoản
- Kiểm tra trong database bảng `users`

---

## 📝 LƯU Ý QUAN TRỌNG

### Bảo mật:

- ⚠️ KHÔNG commit file có chứa App Password lên Git
- ⚠️ App Password chỉ dùng cho ứng dụng
- ⚠️ Mã xác thực có hiệu lực 15 phút

### Giới hạn Gmail:

- Gmail free: 500 email/ngày
- Nếu cần gửi nhiều hơn, dùng SendGrid/Mailgun

### Môi trường Production:

- Nên dùng PHPMailer (Cách 1)
- Cấu hình SMTP chuyên nghiệp
- Xóa dòng hiển thị mã trong response

---

## 🎉 HOÀN THÀNH!

Bây giờ bạn đã có tính năng đổi mật khẩu với xác thực qua email!

**Cách hiện tại đang dùng:** Cách 2 (Simple) - Không cần cài đặt gì thêm

**Nếu muốn chuyển sang Cách 1 (PHPMailer):**

1. Chạy `composer install` trong thư mục backend
2. Cấu hình Gmail trong `config/email.php`
3. Đổi URL API trong Account.vue từ `change-password-simple.php` → `change-password.php`
