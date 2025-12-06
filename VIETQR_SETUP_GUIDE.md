# 🎯 HƯỚNG DẪN TÍCH HỢP VIETQR CHO WEBSITE PHIM

## 📋 TỔNG QUAN

VietQR là giải pháp thanh toán **MIỄN PHÍ 100%** cho website bán gói xem phim. Không cần đăng ký doanh nghiệp, không phí giao dịch, tự động kích hoạt gói.

---

## 🚀 BƯỚC 1: ĐĂNG KÝ CASSO (5 PHÚT)

### 1.1. Truy cập Casso

- Website: https://casso.vn
- Click "Đăng ký miễn phí"

### 1.2. Kết nối ngân hàng

- Chọn ngân hàng của bạn (MB Bank, Vietcombank, Techcombank...)
- Nhập thông tin đăng nhập internet banking
- Casso sẽ đồng bộ giao dịch tự động

### 1.3. Lấy API Key

- Vào **Tài khoản** → **API**
- Copy **API Key** và **Webhook Secret**
- Lưu lại để dùng ở bước sau

---

## 🔧 BƯỚC 2: CẤU HÌNH BACKEND

### 2.1. Update file config

Mở file `backend/api/payment/config_payment.php` và điền thông tin:

```php
// Thông tin ngân hàng của bạn
define('BANK_ID', '970422');              // Mã ngân hàng
define('BANK_ACCOUNT_NO', '0123456789');  // Số tài khoản
define('BANK_ACCOUNT_NAME', 'NGUYEN VAN A'); // Tên (VIẾT HOA, KHÔNG DẤU)

// Casso API
define('CASSO_API_KEY', 'AK_CS.xxxxx');   // API Key từ Casso
define('CASSO_WEBHOOK_SECRET', 'xxxxx');   // Webhook Secret
```

**Lấy mã ngân hàng:**

- MB Bank: `970422`
- Vietcombank: `970436`
- Techcombank: `970407`
- BIDV: `970418`
- ACB: `970416`
- [Xem đầy đủ](https://api.vietqr.io/v2/banks)

### 2.2. Tạo bảng database

Chạy SQL sau để thêm các cột cần thiết:

```sql
-- Thêm cột vào bảng orders
ALTER TABLE orders
ADD COLUMN qr_code_url TEXT,
ADD COLUMN transfer_content VARCHAR(255),
ADD COLUMN expires_at DATETIME,
ADD COLUMN transaction_id VARCHAR(100);

-- Tạo thư mục logs
CREATE TABLE IF NOT EXISTS payment_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    event_type VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🌐 BƯỚC 3: SETUP WEBHOOK TRÊN CASSO

### 3.1. Tạo Webhook URL

Webhook URL của bạn sẽ là:

```
https://yourdomain.com/backend/api/payment/casso_webhook.php
```

**Lưu ý:**

- Phải là HTTPS (không phải HTTP)
- Nếu đang dev local, dùng ngrok: `ngrok http 80`

### 3.2. Cấu hình trên Casso

1. Vào **Cài đặt** → **Webhook**
2. Click **Thêm Webhook**
3. Điền thông tin:
   - **URL**: `https://yourdomain.com/backend/api/payment/casso_webhook.php`
   - **Secret**: (tự động generate hoặc tự đặt)
   - **Events**: Chọn "Giao dịch mới"
4. Click **Lưu**

### 3.3. Test Webhook

Casso có nút "Test Webhook" để kiểm tra kết nối.

---

## 💻 BƯỚC 4: TÍCH HỢP FRONTEND

### 4.1. Update PaymentModal.vue

Thêm VietQR vào danh sách payment methods:

```vue
<script setup>
import VietQRPayment from "@/components/VietQRPayment.vue";

const paymentMethods = [
  {
    value: "vietqr",
    label: "VietQR - Chuyển khoản ngân hàng",
    description: "Quét mã QR, tự động kích hoạt gói",
  },
  // ... other methods
];
</script>

<template>
  <!-- Hiển thị VietQR component khi chọn -->
  <VietQRPayment
    v-if="form.paymentMethod === 'vietqr' && orderId"
    :order-id="orderId"
    @success="handlePaymentSuccess"
    @expired="handleExpired"
  />
</template>
```

### 4.2. Tạo đơn hàng trước khi hiển thị QR

```javascript
const handleSubmit = async () => {
  // 1. Tạo đơn hàng
  const response = await createOrder({
    user_id: userId,
    plan_id: plan.id,
    duration_months: duration.value,
    payment_method: "vietqr",
  });

  orderId.value = response.data.id;

  // 2. Component VietQRPayment sẽ tự động generate QR
};
```

---

## 🧪 BƯỚC 5: TEST THANH TOÁN

### 5.1. Test Flow

1. Chọn gói Premium → Click "Mua ngay"
2. Chọn phương thức "VietQR"
3. Hệ thống hiển thị mã QR
4. Mở app ngân hàng → Quét QR
5. Nhập đúng nội dung: `HTHREE 20241205ABC123`
6. Chuyển khoản
7. Đợi 5-30 giây
8. Gói tự động kích hoạt ✅

### 5.2. Kiểm tra Logs

Xem file log để debug:

```bash
tail -f backend/logs/casso_webhook.log
```

### 5.3. Test Cases

| Test Case                      | Kết quả mong đợi    |
| ------------------------------ | ------------------- |
| Chuyển đúng số tiền + nội dung | ✅ Kích hoạt gói    |
| Chuyển sai số tiền             | ❌ Đơn hàng failed  |
| Chuyển sai nội dung            | ❌ Không nhận diện  |
| Quá 15 phút chưa chuyển        | ❌ Đơn hàng expired |

---

## 📊 LOGIC THANH TOÁN CHI TIẾT

### Luồng hoạt động:

```
┌─────────────┐
│   User      │
│ Chọn gói    │
└──────┬──────┘
       │
       ▼
┌─────────────────────┐
│  Frontend           │
│  Tạo đơn hàng       │
│  POST /orders.php   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────────────┐
│  Backend                    │
│  1. Tạo order (pending)     │
│  2. Generate order_code     │
│  3. Return order_id         │
└──────┬──────────────────────┘
       │
       ▼
┌─────────────────────────────┐
│  Frontend                   │
│  Call generate_vietqr.php   │
└──────┬──────────────────────┘
       │
       ▼
┌─────────────────────────────┐
│  Backend                    │
│  1. Generate QR URL         │
│  2. Save to orders table    │
│  3. Return QR data          │
└──────┬──────────────────────┘
       │
       ▼
┌─────────────────────────────┐
│  Frontend                   │
│  1. Hiển thị QR code        │
│  2. Start polling (3s)      │
│  3. Countdown timer         │
└──────┬──────────────────────┘
       │
       ▼
┌─────────────┐
│   User      │
│ Quét QR     │
│ Chuyển khoản│
└──────┬──────┘
       │
       ▼
┌─────────────────────────────┐
│  Banking App                │
│  Thực hiện chuyển khoản     │
└──────┬──────────────────────┘
       │
       ▼
┌─────────────────────────────┐
│  Casso                      │
│  1. Nhận giao dịch          │
│  2. Parse description       │
│  3. Call webhook            │
└──────┬──────────────────────┘
       │
       ▼
┌─────────────────────────────┐
│  Backend (Webhook)          │
│  1. Extract order_code      │
│  2. Find order              │
│  3. Verify amount           │
│  4. Update order (paid)     │
│  5. Activate subscription   │
└──────┬──────────────────────┘
       │
       ▼
┌─────────────────────────────┐
│  Frontend (Polling)         │
│  1. Detect paid status      │
│  2. Show success message    │
│  3. Redirect to account     │
└─────────────────────────────┘
```

---

## ⚠️ XỬ LÝ CÁC TRƯỜNG HỢP ĐẶC BIỆT

### 1. User chuyển sai số tiền

```php
// Backend tự động đánh dấu failed
if ($order['total'] != $amount) {
    updateOrderStatus($orderId, 'failed');
    sendEmailNotification($user, 'Số tiền không khớp');
}
```

**Giải pháp:** Admin xem log → Hoàn tiền hoặc yêu cầu chuyển lại

### 2. User chuyển sai nội dung

```php
// Webhook không tìm thấy order_code
if (!$orderCode) {
    logWebhook("Cannot extract order code");
    // Không xử lý gì
}
```

**Giải pháp:** User liên hệ support → Admin kích hoạt thủ công

### 3. Đơn hàng hết hạn (15 phút)

```php
// Frontend countdown hết → Auto redirect
if (timeRemaining <= 0) {
    emit('expired');
    router.push('/pricing');
}
```

**Giải pháp:** User tạo đơn mới

### 4. Webhook bị delay

```php
// Frontend polling 3 giây/lần
// Tối đa đợi 15 phút
setInterval(checkPaymentStatus, 3000);
```

**Giải pháp:** Tự động retry, user không cần làm gì

---

## 🎨 CUSTOMIZATION

### Thay đổi thời gian timeout

```php
// config_payment.php
define('ORDER_TIMEOUT', 1800); // 30 phút
```

### Thay đổi template QR

```php
// config_payment.php
define('VIETQR_TEMPLATE', 'qr_only'); // compact2, print, qr_only
```

### Thêm email notification

```php
// casso_webhook.php
function sendActivationEmail($email, $order) {
    // Dùng PHPMailer hoặc SendGrid
    $mail = new PHPMailer();
    $mail->setFrom('noreply@hthree.com');
    $mail->addAddress($email);
    $mail->Subject = 'Gói đã được kích hoạt';
    $mail->Body = "Chào bạn, gói {$order['plan_name']} đã được kích hoạt...";
    $mail->send();
}
```

---

## 🐛 TROUBLESHOOTING

### Lỗi: "Cannot generate QR"

**Nguyên nhân:** Thông tin ngân hàng sai

**Giải pháp:** Kiểm tra lại `BANK_ID`, `BANK_ACCOUNT_NO`

### Lỗi: "Webhook not working"

**Nguyên nhân:**

- URL không đúng
- Server không public
- Firewall chặn

**Giải pháp:**

1. Test webhook trên Casso
2. Kiểm tra logs: `backend/logs/casso_webhook.log`
3. Dùng ngrok nếu dev local

### Lỗi: "Payment not detected"

**Nguyên nhân:**

- Nội dung chuyển khoản sai
- Webhook chưa được gọi

**Giải pháp:**

1. Kiểm tra description trong app banking
2. Xem logs Casso
3. Test lại webhook

---

## 📈 MONITORING & ANALYTICS

### Theo dõi giao dịch

```sql
-- Tổng doanh thu hôm nay
SELECT SUM(total) FROM orders
WHERE payment_status = 'paid'
AND DATE(paid_at) = CURDATE();

-- Số đơn hàng theo trạng thái
SELECT payment_status, COUNT(*)
FROM orders
GROUP BY payment_status;

-- Conversion rate
SELECT
    COUNT(*) as total_orders,
    SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_orders,
    ROUND(SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate
FROM orders;
```

---

## ✅ CHECKLIST TRƯỚC KHI GO LIVE

- [ ] Đã test thanh toán thành công
- [ ] Webhook hoạt động ổn định
- [ ] Logs được ghi đầy đủ
- [ ] Email notification hoạt động
- [ ] Timeout được set hợp lý
- [ ] UI/UX mượt mà
- [ ] Mobile responsive
- [ ] Có hướng dẫn rõ ràng cho user
- [ ] Admin có thể xem logs
- [ ] Có backup plan nếu Casso down

---

## 🚀 NÂNG CẤP SAU NÀY

Khi doanh thu tăng, có thể nâng cấp:

1. **Thêm VNPay/MoMo** - Trải nghiệm tốt hơn
2. **Auto refund** - Hoàn tiền tự động
3. **Invoice system** - Xuất hóa đơn
4. **Subscription management** - Gia hạn tự động
5. **Analytics dashboard** - Báo cáo chi tiết

---

**Chúc bạn thành công! 🎉**

Nếu cần hỗ trợ, liên hệ: support@hthree.com
