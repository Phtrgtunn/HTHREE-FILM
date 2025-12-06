# 🏦 Hướng Dẫn: Kích Hoạt Đơn Chuyển Khoản

## ⚠️ Vấn Đề

Khách hàng đã thanh toán qua **Chuyển khoản ngân hàng** nhưng:

- Admin Panel hiển thị "Chờ xử lý" (màu vàng)
- Subscription chưa được kích hoạt
- Khách hàng chưa xem được phim

## 🔍 Nguyên Nhân

**Chuyển khoản ngân hàng** không có callback tự động như VNPay/MoMo:

- VNPay/MoMo → Tự động callback → Tự động kích hoạt ✅
- Chuyển khoản → Không có callback → Cần kích hoạt thủ công ⚠️

## ✅ Giải Pháp

### Cách 1: Dùng Script PHP (Khuyến Nghị)

1. **Truy cập script**:

   ```
   http://localhost/HTHREE_film/backend/activate_bank_transfer_orders.php
   ```

2. **Xem danh sách đơn chờ**:

   - Script sẽ hiển thị tất cả đơn chuyển khoản đang pending
   - Bảng gồm: Mã đơn, User ID, Số tiền, Ngày tạo

3. **Kích hoạt**:
   - Kiểm tra đã nhận tiền chưa
   - Click nút "✓ Kích hoạt"
   - Hệ thống tự động:
     - Cập nhật order → `paid`
     - Tạo/Gia hạn subscription
     - Khách xem phim ngay

### Cách 2: Dùng SQL (Nhanh)

```sql
-- 1. Kiểm tra đơn hàng
SELECT id, order_code, user_id, total, payment_status
FROM orders
WHERE payment_method = 'bank_transfer'
AND payment_status = 'pending'
ORDER BY created_at DESC;

-- 2. Cập nhật trạng thái (thay ORD123456 bằng mã đơn thực)
UPDATE orders
SET payment_status = 'paid',
    paid_at = NOW(),
    status = 'completed',
    completed_at = NOW()
WHERE order_code = 'ORD123456';

-- 3. Gọi API để kích hoạt subscription
-- POST http://localhost/HTHREE_film/backend/api/payment/activate_subscription.php
-- Body: {"order_code": "ORD123456"}
```

### Cách 3: Dùng API (Cho Developer)

```bash
# Kích hoạt 1 đơn
curl -X POST http://localhost/HTHREE_film/backend/api/payment/activate_subscription.php \
  -H "Content-Type: application/json" \
  -d '{"order_code": "ORD123456"}'

# Kết quả:
# {
#   "success": true,
#   "message": "Subscription activated successfully"
# }
```

## 📋 Quy Trình Xử Lý

```
1. Khách chuyển khoản
        ↓
2. Admin kiểm tra tài khoản ngân hàng
        ↓
3. Đã nhận tiền?
        ├─ Chưa → Chờ
        └─ Rồi → Kích hoạt
                  ↓
4. Chạy script activate_bank_transfer_orders.php
        ↓
5. Click "✓ Kích hoạt" cho đơn hàng
        ↓
6. ✅ Khách xem phim ngay
        ↓
7. Admin Panel hiển thị "Đã thanh toán" (màu xanh)
```

## 🎯 Kích Hoạt Hàng Loạt

Nếu có nhiều đơn cần kích hoạt:

```php
// Chạy trong activate_bank_transfer_orders.php
// Hoặc tạo script riêng

<?php
require_once 'config/database.php';

$conn = getDBConnection();

// Lấy tất cả đơn pending
$stmt = $conn->prepare("
    SELECT id, order_code
    FROM orders
    WHERE payment_method = 'bank_transfer'
    AND payment_status = 'pending'
");
$stmt->execute();
$result = $stmt->get_result();

while ($order = $result->fetch_assoc()) {
    // Gọi API activate cho từng đơn
    $ch = curl_init('http://localhost/HTHREE_film/backend/api/payment/activate_subscription.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'order_code' => $order['order_code']
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    echo "Kích hoạt {$order['order_code']}: " . $response . "\n";
}
?>
```

## 🔍 Kiểm Tra Sau Khi Kích Hoạt

### 1. Trong Admin Panel

- Vào `/admin` → Tab "Đơn hàng"
- Tìm mã đơn vừa kích hoạt
- **Kiểm tra**: Trạng thái = "Đã thanh toán" (màu xanh)

### 2. Trong Database

```sql
-- Kiểm tra order
SELECT payment_status, paid_at, status
FROM orders
WHERE order_code = 'ORD123456';
-- Kết quả: payment_status = 'paid', paid_at != NULL

-- Kiểm tra subscription
SELECT * FROM user_subscriptions
WHERE order_id = (SELECT id FROM orders WHERE order_code = 'ORD123456');
-- Kết quả: Có record mới, status = 'active'
```

### 3. Khách Hàng

- Khách login vào `/account`
- **Kiểm tra**: Thấy gói đã kích hoạt
- **Kiểm tra**: Có thể xem phim

## 📊 Các Trường Hợp

### Trường Hợp 1: Đơn Mới

```
payment_status: pending → paid
status: pending → completed
paid_at: NULL → NOW()
completed_at: NULL → NOW()
Subscription: Tạo mới
```

### Trường Hợp 2: Gia Hạn

```
payment_status: pending → paid
Subscription: Cộng thêm thời gian vào end_date
```

### Trường Hợp 3: Đã Kích Hoạt Rồi

```
Script báo: "Subscription already activated"
Không làm gì cả
```

## ⚠️ Lưu Ý Quan Trọng

### 1. Kiểm Tra Kỹ Trước Khi Kích Hoạt

- ✅ Đã nhận tiền trong tài khoản ngân hàng
- ✅ Số tiền khớp với đơn hàng
- ✅ Nội dung chuyển khoản đúng (mã đơn)

### 2. Không Kích Hoạt Nếu

- ❌ Chưa nhận tiền
- ❌ Số tiền không đủ
- ❌ Nội dung chuyển khoản sai

### 3. Backup Trước Khi Chạy

```sql
-- Backup orders
CREATE TABLE orders_backup AS SELECT * FROM orders;

-- Backup subscriptions
CREATE TABLE user_subscriptions_backup AS SELECT * FROM user_subscriptions;
```

## 🔄 Tự Động Hóa (Tương Lai)

Để tự động hóa hoàn toàn, cần:

1. **Tích hợp API ngân hàng**:

   - VietQR API
   - Banking API
   - Webhook từ ngân hàng

2. **Cron Job**:

   ```bash
   # Chạy mỗi 5 phút
   */5 * * * * php /path/to/check_bank_transactions.php
   ```

3. **Matching tự động**:
   - So sánh giao dịch ngân hàng với đơn hàng
   - Tự động kích hoạt khi khớp

## 📞 Troubleshooting

### Script không chạy?

```bash
# Kiểm tra PHP
php -v

# Kiểm tra quyền file
chmod 755 backend/activate_bank_transfer_orders.php

# Chạy từ command line
php backend/activate_bank_transfer_orders.php
```

### API trả về lỗi?

```bash
# Kiểm tra log
tail -f backend/logs/error.log

# Kiểm tra database connection
php backend/config/database.php
```

### Subscription không tạo?

```sql
-- Kiểm tra bảng có tồn tại không
SHOW TABLES LIKE 'user_subscriptions';

-- Kiểm tra cấu trúc
DESCRIBE user_subscriptions;

-- Kiểm tra foreign key
SHOW CREATE TABLE user_subscriptions;
```

## 📝 Checklist

Sau khi kích hoạt, kiểm tra:

- [ ] Order `payment_status` = 'paid'
- [ ] Order `paid_at` có giá trị
- [ ] Order `status` = 'completed'
- [ ] Có record trong `user_subscriptions`
- [ ] Subscription `status` = 'active'
- [ ] Subscription `end_date` > NOW()
- [ ] Admin Panel hiển thị "Đã thanh toán"
- [ ] Khách hàng xem được phim

---

**Cập nhật**: 04/12/2024  
**Version**: 2.0.2  
**Status**: ✅ Ready to Use
