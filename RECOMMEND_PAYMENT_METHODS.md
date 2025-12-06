# 💳 Recommend: Phương Thức Thanh Toán An Toàn

## 🎯 Tổng Quan

Hiện tại hệ thống hỗ trợ:

- ✅ **VNPay** - Tự động callback ⭐⭐⭐⭐⭐
- ✅ **MoMo** - Tự động callback ⭐⭐⭐⭐⭐
- ⚠️ **Chuyển khoản** - Cần kích hoạt thủ công ⭐⭐

## 🏆 Top Recommendations

### 1. VNPay (Khuyến Nghị Nhất) ⭐⭐⭐⭐⭐

**Ưu điểm:**

- ✅ Tự động callback → Tự động kích hoạt
- ✅ Hỗ trợ nhiều ngân hàng (ATM, Visa, MasterCard)
- ✅ Bảo mật cao (SSL, 3D Secure)
- ✅ Phí thấp (1.5% - 2%)
- ✅ Dễ tích hợp
- ✅ Hỗ trợ 24/7

**Nhược điểm:**

- ⚠️ Cần đăng ký doanh nghiệp
- ⚠️ Thời gian duyệt: 3-5 ngày

**Đăng ký:**

```
1. Truy cập: https://vnpay.vn
2. Đăng ký tài khoản doanh nghiệp
3. Cung cấp giấy tờ: GPKD, CMND, giấy phép kinh doanh
4. Chờ duyệt 3-5 ngày
5. Nhận Merchant ID và Secret Key
6. Tích hợp vào website
```

**Chi phí:**

- Phí giao dịch: 1.5% - 2%
- Phí setup: Miễn phí
- Phí duy trì: Miễn phí

---

### 2. MoMo (Khuyến Nghị) ⭐⭐⭐⭐⭐

**Ưu điểm:**

- ✅ Tự động callback → Tự động kích hoạt
- ✅ Phổ biến tại Việt Nam
- ✅ Thanh toán nhanh (QR code)
- ✅ Không cần thẻ ngân hàng
- ✅ Phí cạnh tranh (1.5% - 2.5%)
- ✅ Dễ tích hợp

**Nhược điểm:**

- ⚠️ Cần đăng ký doanh nghiệp
- ⚠️ Giới hạn giao dịch (50 triệu/ngày)

**Đăng ký:**

```
1. Truy cập: https://business.momo.vn
2. Đăng ký tài khoản doanh nghiệp
3. Cung cấp giấy tờ
4. Chờ duyệt 3-7 ngày
5. Nhận Partner Code và Secret Key
6. Tích hợp vào website
```

**Chi phí:**

- Phí giao dịch: 1.5% - 2.5%
- Phí setup: Miễn phí
- Phí duy trì: Miễn phí

---

### 3. ZaloPay ⭐⭐⭐⭐

**Ưu điểm:**

- ✅ Tự động callback
- ✅ Tích hợp với Zalo (nhiều user)
- ✅ Thanh toán nhanh
- ✅ Phí thấp (1.5% - 2%)

**Nhược điểm:**

- ⚠️ Ít phổ biến hơn MoMo
- ⚠️ Cần đăng ký doanh nghiệp

**Đăng ký:**

```
1. Truy cập: https://zalopay.vn/business
2. Đăng ký tài khoản
3. Cung cấp giấy tờ
4. Chờ duyệt
5. Tích hợp
```

---

### 4. VietQR (Chuyển Khoản Tự Động) ⭐⭐⭐⭐⭐

**Ưu điểm:**

- ✅ **Tự động xác nhận** qua API ngân hàng
- ✅ Không cần đăng ký cổng thanh toán
- ✅ Phí thấp (0% - 0.5%)
- ✅ Hỗ trợ tất cả ngân hàng
- ✅ QR code tự động
- ✅ Matching tự động

**Cách hoạt động:**

```
1. Khách quét QR → Chuyển khoản
2. Nội dung: Mã đơn hàng (ORD123456)
3. API ngân hàng webhook → Server
4. Server matching mã đơn
5. Tự động kích hoạt subscription
```

**Tích hợp:**

```javascript
// Frontend: Tạo QR code
const qrUrl = `https://img.vietqr.io/image/${bankCode}-${accountNo}-compact2.jpg?amount=${amount}&addInfo=${orderCode}`;

// Backend: Webhook từ ngân hàng
app.post("/webhook/bank", (req, res) => {
  const { amount, content } = req.body;

  // Extract order code từ content
  const orderCode = extractOrderCode(content);

  // Tìm order
  const order = findOrder(orderCode);

  // Kiểm tra số tiền
  if (order.total === amount) {
    // Kích hoạt tự động
    activateSubscription(orderCode);
  }
});
```

**Ngân hàng hỗ trợ API:**

- ✅ VCB (Vietcombank) - API Corporate
- ✅ TCB (Techcombank) - Open API
- ✅ MB (MB Bank) - Open API
- ✅ ACB - Open API
- ✅ VPBank - Open API

**Chi phí:**

- Phí giao dịch: 0% (chuyển khoản thường)
- Phí API: 500k - 2 triệu/tháng (tùy ngân hàng)
- Phí setup: Miễn phí

---

### 5. Casso (Tự Động Đối Soát) ⭐⭐⭐⭐⭐

**Ưu điểm:**

- ✅ **Tự động đối soát** giao dịch ngân hàng
- ✅ Webhook real-time
- ✅ Hỗ trợ 30+ ngân hàng
- ✅ Không cần API ngân hàng
- ✅ Dễ tích hợp
- ✅ Dashboard quản lý

**Cách hoạt động:**

```
1. Kết nối tài khoản ngân hàng với Casso
2. Casso theo dõi giao dịch real-time
3. Có giao dịch mới → Webhook đến server
4. Server matching mã đơn
5. Tự động kích hoạt
```

**Đăng ký:**

```
1. Truy cập: https://casso.vn
2. Đăng ký tài khoản
3. Kết nối ngân hàng (SMS Banking hoặc Internet Banking)
4. Cấu hình webhook
5. Tích hợp vào website
```

**Chi phí:**

- Gói Free: 0đ (100 giao dịch/tháng)
- Gói Basic: 99k/tháng (1000 giao dịch)
- Gói Pro: 299k/tháng (không giới hạn)

**Tích hợp:**

```php
// Webhook từ Casso
Route::post('/webhook/casso', function(Request $request) {
    $data = $request->all();

    // Lấy thông tin giao dịch
    $amount = $data['amount'];
    $description = $data['description'];

    // Extract order code
    preg_match('/ORD\d+/', $description, $matches);
    $orderCode = $matches[0] ?? null;

    if ($orderCode) {
        // Tìm order
        $order = Order::where('order_code', $orderCode)->first();

        // Kiểm tra số tiền
        if ($order && $order->total == $amount) {
            // Kích hoạt tự động
            activateSubscription($orderCode);

            return response()->json(['success' => true]);
        }
    }

    return response()->json(['success' => false]);
});
```

---

### 6. PayOS (Mới, Hiện Đại) ⭐⭐⭐⭐⭐

**Ưu điểm:**

- ✅ Tự động callback
- ✅ Hỗ trợ nhiều phương thức (QR, ATM, Visa)
- ✅ Phí thấp (1.5%)
- ✅ Dashboard đẹp
- ✅ API hiện đại (RESTful)
- ✅ Webhook real-time

**Đăng ký:**

```
1. Truy cập: https://payos.vn
2. Đăng ký nhanh (không cần GPKD)
3. Xác thực tài khoản
4. Nhận API Key
5. Tích hợp
```

**Chi phí:**

- Phí giao dịch: 1.5%
- Phí setup: Miễn phí
- Phí duy trì: Miễn phí

---

## 📊 So Sánh

| Phương thức      | Tự động | Phí           | Dễ đăng ký | Phổ biến   | Tổng điểm  |
| ---------------- | ------- | ------------- | ---------- | ---------- | ---------- |
| **VNPay**        | ✅      | 1.5-2%        | ⚠️         | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **MoMo**         | ✅      | 1.5-2.5%      | ⚠️         | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **ZaloPay**      | ✅      | 1.5-2%        | ⚠️         | ⭐⭐⭐     | ⭐⭐⭐⭐   |
| **VietQR + API** | ✅      | 0-0.5%        | ⚠️⚠️       | ⭐⭐⭐⭐   | ⭐⭐⭐⭐⭐ |
| **Casso**        | ✅      | 99-299k/tháng | ✅         | ⭐⭐⭐⭐   | ⭐⭐⭐⭐⭐ |
| **PayOS**        | ✅      | 1.5%          | ✅         | ⭐⭐⭐     | ⭐⭐⭐⭐⭐ |
| **Chuyển khoản** | ❌      | 0%            | ✅         | ⭐⭐⭐⭐⭐ | ⭐⭐       |

---

## 🎯 Khuyến Nghị Cho HTHREE Film

### Giải Pháp Tối Ưu (Kết Hợp)

#### Phase 1: Ngay Lập Tức

```
✅ VNPay (chính)
✅ MoMo (phụ)
✅ Casso (cho chuyển khoản tự động)
```

**Lý do:**

- VNPay + MoMo: Phổ biến, tự động 100%
- Casso: Giải quyết vấn đề chuyển khoản thủ công

#### Phase 2: Sau 1-2 Tháng

```
✅ Thêm ZaloPay
✅ Thêm PayOS
```

#### Phase 3: Tương Lai

```
✅ Tích hợp API ngân hàng trực tiếp (VietQR)
✅ Crypto payment (USDT, Bitcoin)
```

---

## 🔧 Tích Hợp Casso (Khuyến Nghị)

### Bước 1: Đăng Ký

```
1. Vào https://casso.vn
2. Đăng ký tài khoản
3. Kết nối ngân hàng
4. Lấy Webhook URL và Secret Key
```

### Bước 2: Tạo Webhook Endpoint

```php
// backend/api/webhook/casso.php
<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

// Lấy data từ Casso
$data = json_decode(file_get_contents('php://input'), true);

// Verify signature (bảo mật)
$signature = $_SERVER['HTTP_X_CASSO_SIGNATURE'] ?? '';
$secretKey = 'YOUR_CASSO_SECRET_KEY';
$expectedSignature = hash_hmac('sha256', json_encode($data), $secretKey);

if ($signature !== $expectedSignature) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid signature']);
    exit;
}

// Lấy thông tin giao dịch
$amount = $data['amount'];
$description = $data['description'];
$transactionId = $data['id'];

// Extract order code từ description
preg_match('/ORD\d+/', $description, $matches);
$orderCode = $matches[0] ?? null;

if (!$orderCode) {
    echo json_encode(['success' => false, 'message' => 'Order code not found']);
    exit;
}

$conn = getDBConnection();

// Tìm order
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ?");
$stmt->bind_param("s", $orderCode);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

// Kiểm tra số tiền
if ($order['total'] != $amount) {
    echo json_encode(['success' => false, 'message' => 'Amount mismatch']);
    exit;
}

// Kiểm tra đã kích hoạt chưa
if ($order['payment_status'] === 'paid') {
    echo json_encode(['success' => true, 'message' => 'Already activated']);
    exit;
}

// Kích hoạt tự động
$conn->begin_transaction();

try {
    // Update order
    $stmt = $conn->prepare("
        UPDATE orders
        SET payment_status = 'paid',
            status = 'completed',
            paid_at = NOW(),
            completed_at = NOW(),
            transaction_id = ?
        WHERE id = ?
    ");
    $stmt->bind_param("si", $transactionId, $order['id']);
    $stmt->execute();

    // Kích hoạt subscription (gọi hàm có sẵn)
    require_once __DIR__ . '/../payment/activate_subscription.php';
    activateSubscriptionByOrderId($order['id']);

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order activated successfully',
        'order_code' => $orderCode
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
```

### Bước 3: Cấu Hình Casso

```
1. Vào Casso Dashboard
2. Settings → Webhook
3. Webhook URL: https://yourdomain.com/backend/api/webhook/casso.php
4. Secret Key: Lưu vào code
5. Test webhook
6. Save
```

### Bước 4: Test

```bash
# Test webhook
curl -X POST https://yourdomain.com/backend/api/webhook/casso.php \
  -H "Content-Type: application/json" \
  -H "X-Casso-Signature: YOUR_SIGNATURE" \
  -d '{
    "id": "123456",
    "amount": 50000,
    "description": "Thanh toan don hang ORD20251284144",
    "when": "2024-12-04 10:30:00"
  }'
```

---

## 💰 Chi Phí Ước Tính

### Scenario: 100 đơn/tháng, trung bình 100k/đơn

| Phương thức | Phí/đơn | Tổng phí/tháng | Ghi chú            |
| ----------- | ------- | -------------- | ------------------ |
| VNPay (2%)  | 2,000đ  | 200,000đ       | Tự động            |
| MoMo (2%)   | 2,000đ  | 200,000đ       | Tự động            |
| Casso       | 0đ      | 99,000đ        | Gói Basic          |
| **Tổng**    | -       | **499,000đ**   | **Tất cả tự động** |

### So với Chuyển khoản thủ công:

- Phí: 0đ
- Thời gian admin: 5 phút/đơn × 100 đơn = **500 phút = 8.3 giờ**
- Chi phí nhân công: 50k/giờ × 8.3 = **415,000đ**

**→ Tự động hóa RẺ HƠN và NHANH HƠN!**

---

## 📝 Action Plan

### Tuần 1:

- [ ] Đăng ký Casso
- [ ] Tích hợp Casso webhook
- [ ] Test với chuyển khoản thật

### Tuần 2:

- [ ] Đăng ký VNPay (nếu có GPKD)
- [ ] Hoặc đăng ký PayOS (không cần GPKD)

### Tuần 3:

- [ ] Đăng ký MoMo
- [ ] Tích hợp cả 3 phương thức

### Tuần 4:

- [ ] Monitor và optimize
- [ ] Thêm analytics

---

**Khuyến nghị cuối cùng**: Dùng **Casso** ngay để giải quyết vấn đề chuyển khoản thủ công. Sau đó từ từ thêm VNPay và MoMo.

---

**Cập nhật**: 04/12/2024  
**Version**: 2.0.3  
**Status**: ✅ Ready to Implement
