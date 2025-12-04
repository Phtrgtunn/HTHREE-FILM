# Hướng dẫn cài đặt gửi email qua Gmail

## Bước 1: Cài đặt PHPMailer

Mở Command Prompt/Terminal trong thư mục `backend` và chạy:

```bash
cd backend
composer install
```

Nếu chưa có Composer, tải tại: https://getcomposer.org/download/

## Bước 2: Cấu hình Gmail

### 2.1. Bật xác thực 2 bước (2FA) cho Gmail

1. Truy cập: https://myaccount.google.com/security
2. Tìm "Xác minh 2 bước" và bật nó lên

### 2.2. Tạo App Password cho Gmail

1. Truy cập: https://myaccount.google.com/apppasswords
2. Chọn "Mail" và "Windows Computer" (hoặc thiết bị khác)
3. Click "Generate"
4. Sao chép mật khẩu 16 ký tự (dạng: xxxx xxxx xxxx xxxx)

### 2.3. Cập nhật file config/email.php

Mở file `backend/config/email.php` và thay đổi:

```php
$this->mail->Username   = 'your-email@gmail.com';     // Email Gmail của bạn
$this->mail->Password   = 'your-app-password';        // App Password vừa tạo (16 ký tự, bỏ dấu cách)
```

Ví dụ:

```php
$this->mail->Username   = 'hthreefilm@gmail.com';
$this->mail->Password   = 'abcd efgh ijkl mnop';  // Hoặc 'abcdefghijklmnop' (không có dấu cách)
```

Cũng cập nhật dòng setFrom:

```php
$this->mail->setFrom('hthreefilm@gmail.com', 'HTHREE Film');
```

## Bước 3: Test gửi email

1. Chạy SQL trong phpMyAdmin để tạo bảng:

   - Mở file `backend/database/add_password_reset_table.sql`
   - Copy và chạy trong phpMyAdmin

2. Vào trang Account → Bảo mật → Mật khẩu
3. Nhập email và click "Gửi mã xác thực"
4. Kiểm tra hộp thư email (cả Inbox và Spam)

## Lưu ý quan trọng

⚠️ **Bảo mật:**

- KHÔNG commit file `config/email.php` có chứa mật khẩu thật lên Git
- Nên tạo file `.env` để lưu thông tin nhạy cảm
- App Password chỉ dùng cho ứng dụng, không dùng để đăng nhập Gmail

⚠️ **Gmail Limits:**

- Gmail giới hạn 500 email/ngày cho tài khoản thường
- Nếu gửi nhiều email, nên dùng dịch vụ chuyên nghiệp như SendGrid, Mailgun

## Troubleshooting

### Lỗi "SMTP connect() failed"

- Kiểm tra kết nối internet
- Đảm bảo đã bật 2FA và tạo App Password
- Kiểm tra firewall/antivirus có chặn port 587 không

### Email không nhận được

- Kiểm tra thư mục Spam
- Đợi vài phút (đôi khi Gmail delay)
- Kiểm tra log lỗi trong file `error_log` của PHP

### Lỗi "Invalid credentials"

- Đảm bảo dùng App Password, không phải mật khẩu Gmail thường
- Xóa hết dấu cách trong App Password
- Thử tạo lại App Password mới

## Alternative: Sử dụng dịch vụ email khác

Nếu không muốn dùng Gmail, có thể dùng:

- **SendGrid** (free 100 emails/day): https://sendgrid.com
- **Mailgun** (free 5000 emails/month): https://mailgun.com
- **Mailtrap** (cho testing): https://mailtrap.io
