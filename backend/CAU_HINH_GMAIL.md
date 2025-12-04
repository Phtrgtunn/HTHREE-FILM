# 📧 HƯỚNG DẪN CÀI ĐẶT GMAIL ĐỂ GỬI EMAIL

## ✅ Bước 1: Bật xác thực 2 bước (2FA) cho Gmail

1. Truy cập: https://myaccount.google.com/security
2. Tìm mục **"Xác minh 2 bước"** (2-Step Verification)
3. Click vào và làm theo hướng dẫn để bật 2FA
4. Bạn sẽ cần xác thực qua số điện thoại

![2FA Setup](https://i.imgur.com/example.png)

---

## ✅ Bước 2: Tạo App Password (Mật khẩu ứng dụng)

### Cách tạo App Password:

1. **Truy cập:** https://myaccount.google.com/apppasswords
2. **Đăng nhập** nếu được yêu cầu

3. **Chọn ứng dụng:**
   - Trong dropdown "Select app", chọn **"Mail"**
4. **Chọn thiết bị:**
   - Trong dropdown "Select device", chọn **"Windows Computer"** (hoặc thiết bị bạn đang dùng)
5. **Click "Generate"**

6. **Sao chép mật khẩu:**
   - Gmail sẽ hiển thị mật khẩu 16 ký tự dạng: `abcd efgh ijkl mnop`
   - **LƯU Ý:** Sao chép ngay vì bạn sẽ không thể xem lại!

---

## ✅ Bước 3: Cấu hình file email.php

Mở file: `backend/config/email.php`

Tìm và sửa 2 dòng sau:

```php
// Dòng 18-19
$this->mail->Username   = 'your-email@gmail.com';     // Thay bằng email Gmail của bạn
$this->mail->Password   = 'your-app-password';        // Thay bằng App Password vừa tạo
```

**Ví dụ:**

```php
$this->mail->Username   = 'hthreefilm@gmail.com';
$this->mail->Password   = 'abcdefghijklmnop';  // Bỏ hết dấu cách
```

Và dòng setFrom (dòng 25):

```php
$this->mail->setFrom('hthreefilm@gmail.com', 'HTHREE Film');
```

---

## ✅ Bước 4: Kiểm tra cấu hình

### File email.php sau khi sửa sẽ như thế này:

```php
public function __construct() {
    $this->mail = new PHPMailer(true);

    try {
        // Cấu hình SMTP
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'hthreefilm@gmail.com';      // ← Email của bạn
        $this->mail->Password   = 'abcdefghijklmnop';          // ← App Password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;

        // Cấu hình người gửi
        $this->mail->setFrom('hthreefilm@gmail.com', 'HTHREE Film');  // ← Email của bạn
        $this->mail->CharSet = 'UTF-8';

    } catch (Exception $e) {
        error_log("Email configuration error: {$this->mail->ErrorInfo}");
    }
}
```

---

## ✅ Bước 5: Test gửi email

1. **Chạy SQL tạo bảng:**

   - Mở AMPPS → phpMyAdmin
   - Chọn database `hthree_film`
   - Chạy file: `backend/database/add_password_reset_table.sql`

2. **Test trên website:**

   - Vào trang Account → Bảo mật → Mật khẩu
   - Nhập email Gmail của bạn
   - Click "Gửi mã xác thực"
   - Kiểm tra email (cả Inbox và Spam)

3. **Nhập mã và đổi mật khẩu:**
   - Nhập mã 6 số nhận được
   - Nhập mật khẩu mới
   - Click "Đổi mật khẩu"

---

## 🔧 XỬ LÝ LỖI THƯỜNG GẶP

### ❌ Lỗi: "SMTP connect() failed"

**Nguyên nhân:**

- Chưa bật 2FA
- App Password sai
- Firewall/Antivirus chặn port 587

**Giải pháp:**

1. Kiểm tra lại đã bật 2FA chưa
2. Tạo lại App Password mới
3. Đảm bảo App Password không có dấu cách
4. Tắt Firewall/Antivirus tạm thời để test
5. Kiểm tra kết nối internet

### ❌ Lỗi: "Invalid credentials"

**Nguyên nhân:**

- Dùng mật khẩu Gmail thường thay vì App Password
- App Password có dấu cách
- Email sai

**Giải pháp:**

1. Đảm bảo dùng App Password (16 ký tự), KHÔNG phải mật khẩu Gmail
2. Xóa hết dấu cách trong App Password
3. Kiểm tra email có đúng không

### ❌ Email không nhận được

**Giải pháp:**

1. Kiểm tra thư mục **Spam/Junk**
2. Đợi 2-3 phút (Gmail có thể delay)
3. Kiểm tra email có đúng không
4. Thử gửi lại

### ❌ Lỗi: "Could not instantiate mail function"

**Nguyên nhân:**

- Chưa cài PHPMailer

**Giải pháp:**

```bash
cd backend
composer install
```

---

## 📝 LƯU Ý QUAN TRỌNG

### ⚠️ Bảo mật:

- **KHÔNG** commit file `email.php` có chứa App Password lên Git
- **KHÔNG** chia sẻ App Password với ai
- App Password chỉ dùng cho ứng dụng, không dùng để đăng nhập Gmail
- Nếu bị lộ, hãy xóa và tạo App Password mới

### 📊 Giới hạn Gmail:

- **Gmail Free:** 500 email/ngày
- **Gmail Workspace:** 2000 email/ngày
- Nếu vượt quá, Gmail sẽ tạm khóa gửi email

### 🚀 Môi trường Production:

- Nên dùng dịch vụ email chuyên nghiệp:
  - **SendGrid** (100 email/day free)
  - **Mailgun** (5000 email/month free)
  - **Amazon SES** (62,000 email/month free)

---

## ✅ CHECKLIST HOÀN THÀNH

- [ ] Đã bật 2FA cho Gmail
- [ ] Đã tạo App Password
- [ ] Đã cập nhật file `email.php` với email và App Password
- [ ] Đã chạy SQL tạo bảng `password_reset_tokens`
- [ ] Đã test gửi email thành công
- [ ] Email đã nhận được mã xác thực

---

## 🎉 HOÀN THÀNH!

Bây giờ hệ thống của bạn đã có thể gửi email xác thực thật qua Gmail!

**Nếu gặp vấn đề, hãy:**

1. Kiểm tra lại từng bước
2. Xem log lỗi trong AMPPS
3. Thử tạo lại App Password
4. Đảm bảo internet ổn định
