# ✅ HOÀN TẤT TÍCH HỢP VIETQR - CÁC BƯỚC CUỐI CÙNG

## 🎯 **TRẠNG THÁI HIỆN TẠI**

✅ Đã kết nối Casso với MB Bank
✅ Đã test generate QR thành công
✅ Đã tích hợp VietQR vào PaymentModal
✅ Đã tạo component VietQRPayment

---

## 📝 **BƯỚC 1: CHẠY SQL (BẮT BUỘC)**

### Mở phpMyAdmin và chạy file SQL:

```
backend/database/add_payment_columns.sql
```

Hoặc copy SQL này vào phpMyAdmin:

```sql
ALTER TABLE orders
ADD COLUMN IF NOT EXISTS qr_code_url TEXT,
ADD COLUMN IF NOT EXISTS transfer_content VARCHAR(255),
ADD COLUMN IF NOT EXISTS expires_at DATETIME,
ADD COLUMN IF NOT EXISTS transaction_id VARCHAR(100),
ADD COLUMN IF NOT EXISTS payment_note TEXT;

CREATE TABLE IF NOT EXISTS payment_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    order_code VARCHAR(50),
    event_type VARCHAR(50),
    message TEXT,
    data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🧪 **BƯỚC 2: TEST THANH TOÁN**

### 1. Khởi động dev server:

```bash
npm run dev
```

### 2. Truy cập trang Pricing:

```
http://localhost:5174/pricing
```

### 3. Test flow:

1. Click "Mua ngay" gói Premium
2. Chọn phương thức: **"VietQR - Chuyển khoản ngân hàng"**
3. Điền thông tin (tên, email, phone)
4. Click "Tạo mã QR"
5. Quét mã QR bằng app MB Bank
6. Chuyển khoản với nội dung chính xác
7. Đợi 5-30 giây
8. Kiểm tra gói đã được kích hoạt

---

## ⚠️ **LƯU Ý QUAN TRỌNG**

### **Webhook chưa hoạt động (localhost)**

Vì đang chạy localhost, webhook từ Casso **KHÔNG THỂ** gọi được. Có 2 cách xử lý:

#### **Cách 1: Update thủ công (Tạm thời)**

Sau khi chuyển khoản, vào phpMyAdmin và chạy:

```sql
-- 1. Tìm order_code trong Casso
-- VD: HTHREE 20241205ABC123

-- 2. Update order status
UPDATE orders
SET payment_status = 'paid', paid_at = NOW()
WHERE order_code = '20241205ABC123';

-- 3. Kích hoạt subscription
INSERT INTO subscriptions (
    user_id, plan_id, plan_name, plan_slug, quality,
    start_date, end_date, status
) VALUES (
    1,  -- user_id (thay bằng ID user của bạn)
    2,  -- plan_id (Premium = 2)
    'Premium', 'premium', '4K',
    NOW(),
    DATE_ADD(NOW(), INTERVAL 1 MONTH),
    'active'
);
```

#### **Cách 2: Dùng ngrok (Khuyến nghị)**

```bash
# 1. Download ngrok: https://ngrok.com/download
# 2. Chạy ngrok
ngrok http 80

# 3. Copy URL (VD: https://abc123.ngrok-free.app)
# 4. Vào Casso → Thiết lập → Webhook
# 5. Thêm webhook:
https://abc123.ngrok-free.app/backend/api/payment/casso_webhook.php

# 6. Test lại flow → Tự động kích hoạt!
```

---

## 🎨 **BƯỚC 3: CUSTOMIZE (TÙY CHỌN)**

### Thay đổi thời gian timeout:

```php
// backend/api/payment/config_payment.php
define('ORDER_TIMEOUT', 1800); // 30 phút thay vì 15 phút
```

### Thay đổi template QR:

```php
// backend/api/payment/config_payment.php
define('VIETQR_TEMPLATE', 'qr_only'); // Chỉ hiển thị QR, không có logo
```

### Thêm email notification:

Cài PHPMailer:

```bash
composer require phpmailer/phpmailer
```

Thêm vào `casso_webhook.php`:

```php
function sendActivationEmail($email, $order) {
    $mail = new PHPMailer();
    $mail->setFrom('noreply@hthree.com');
    $mail->addAddress($email);
    $mail->Subject = 'Gói đã được kích hoạt - HTHREE Film';
    $mail->Body = "Chào bạn, gói {$order['plan_name']} đã được kích hoạt thành công!";
    $mail->send();
}
```

---

## 📊 **BƯỚC 4: MONITORING**

### Xem logs webhook:

```bash
# Tạo thư mục logs
mkdir backend/logs

# Xem logs real-time
tail -f backend/logs/casso_webhook.log
```

### Kiểm tra giao dịch trong Casso:

1. Vào https://flow.casso.vn
2. Click "Giao dịch"
3. Xem lịch sử chuyển khoản
4. Kiểm tra nội dung có đúng format không

### Query database:

```sql
-- Xem đơn hàng mới nhất
SELECT * FROM orders ORDER BY created_at DESC LIMIT 10;

-- Xem subscription active
SELECT * FROM subscriptions WHERE status = 'active';

-- Xem logs
SELECT * FROM payment_logs ORDER BY created_at DESC LIMIT 20;
```

---

## 🚀 **BƯỚC 5: DEPLOY LÊN HOSTING THẬT**

Khi deploy lên hosting (VD: Vercel, Netlify, VPS):

### 1. Update config:

```php
// backend/api/payment/config_payment.php
// Giữ nguyên thông tin ngân hàng
// Chỉ cần update webhook URL trên Casso
```

### 2. Setup webhook trên Casso:

```
https://yourdomain.com/backend/api/payment/casso_webhook.php
```

### 3. Test lại toàn bộ flow

### 4. Monitor logs

---

## ✅ **CHECKLIST HOÀN THÀNH**

- [ ] Đã chạy SQL thêm cột vào database
- [ ] Đã test generate QR thành công
- [ ] Đã test chuyển khoản thật
- [ ] Đã kiểm tra giao dịch trong Casso
- [ ] (Optional) Đã setup ngrok và test webhook
- [ ] Đã test kích hoạt gói (thủ công hoặc tự động)
- [ ] Đã kiểm tra subscription trong database
- [ ] UI/UX mượt mà, không có lỗi
- [ ] Đã test trên mobile

---

## 🎯 **KẾT QUẢ MONG ĐỢI**

Sau khi hoàn thành:

1. ✅ User chọn gói → Thấy mã QR
2. ✅ User quét QR → Chuyển khoản
3. ✅ Hệ thống nhận webhook (hoặc update thủ công)
4. ✅ Gói tự động kích hoạt
5. ✅ User vào Account → Thấy gói đã active
6. ✅ User có thể xem phim không giới hạn

---

## 🐛 **TROUBLESHOOTING**

### Lỗi: "Cannot generate QR"

→ Kiểm tra thông tin ngân hàng trong `config_payment.php`

### Lỗi: "Order not found"

→ Kiểm tra database có bảng orders không

### Lỗi: "Webhook not working"

→ Dùng ngrok hoặc update thủ công

### QR code không hiển thị

→ Kiểm tra VietQRPayment component đã import đúng chưa

---

## 📞 **HỖ TRỢ**

Nếu gặp vấn đề:

1. Kiểm tra logs: `backend/logs/casso_webhook.log`
2. Kiểm tra database: phpMyAdmin
3. Kiểm tra Casso: https://flow.casso.vn
4. Test lại file: `test_generate_qr.php`

---

**Chúc bạn thành công! 🎉**

Hệ thống thanh toán VietQR của bạn đã sẵn sàng!
