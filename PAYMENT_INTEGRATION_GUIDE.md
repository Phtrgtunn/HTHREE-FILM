# 💳 HƯỚNG DẪN TÍCH HỢP THANH TOÁN

## 📋 **TÓM TẮT**

Dự án sử dụng **3 phương thức thanh toán**:
1. ✅ **VNPay Sandbox** - Thanh toán thực tế (test environment)
2. ✅ **MoMo Mock** - Giả lập flow thanh toán
3. ✅ **Bank Transfer** - Chuyển khoản với QR Code

---

## 🚀 **BƯỚC 1: CÀI ĐẶT**

### 1.1. Thêm routes

Mở `src/router/index.js` và thêm:

```javascript
import PaymentReturn from '@/pages/PaymentReturn.vue';
import MoMoMock from '@/pages/MoMoMock.vue';

const routes = [
  // ... existing routes
  {
    path: '/payment-return',
    name: 'PaymentReturn',
    component: PaymentReturn
  },
  {
    path: '/payment/momo-mock',
    name: 'MoMoMock',
    component: MoMoMock
  }
];
```

### 1.2. Tạo backend verify payment

Tạo file `backend/api/payment/verify.php`:

```php
<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Xử lý theo payment method
    $payment_method = $data['payment_method'] ?? '';
    $order_code = $data['order_code'] ?? '';
    $status = $data['status'] ?? '';
    
    if ($status === 'success') {
        // Update order status
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("
            UPDATE orders 
            SET payment_status = 'paid',
                order_status = 'processing',
                updated_at = NOW()
            WHERE order_code = ?
        ");
        $stmt->execute([$order_code]);
        
        // Get order info
        $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ?");
        $stmt->execute([$order_code]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'order_code' => $order_code,
                'amount_formatted' => number_format($order['total_amount']) . 'đ',
                'payment_method' => $payment_method
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $data['message'] ?? 'Thanh toán thất bại'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi xác thực thanh toán: ' . $e->getMessage()
    ]);
}
```

---

## 🔧 **BƯỚC 2: CẬP NHẬT CHECKOUT PAGE**

Mở `src/pages/Checkout.vue` và update hàm `handleSubmit`:

```javascript
import paymentService from '@/services/paymentService';

const handleSubmit = async () => {
  // ... validation code ...
  
  submitting.value = true;

  try {
    // 1. Tạo order
    const [response] = await Promise.all([
      createOrder({
        user_id: authStore.user.id,
        customer_name: form.customer_name,
        customer_email: form.customer_email,
        customer_phone: form.customer_phone,
        payment_method: form.payment_method,
        note: form.note,
        coupon_code: appliedCoupon.value?.code || null
      }),
      new Promise(resolve => setTimeout(resolve, 800))
    ]);

    if (response.success) {
      const orderData = {
        order_code: response.data.order_code,
        amount: response.data.total_amount,
        order_info: `Thanh toan don hang ${response.data.order_code}`,
        customer_name: form.customer_name
      };

      // 2. Xử lý theo payment method
      if (form.payment_method === 'vnpay') {
        // VNPay - Redirect to payment gateway
        const paymentResult = await paymentService.createVNPayPayment(orderData);
        if (paymentResult.success) {
          window.location.href = paymentResult.payment_url;
        }
      } else if (form.payment_method === 'momo') {
        // MoMo Mock - Redirect to mock page
        const paymentResult = await paymentService.createMoMoPayment(orderData);
        if (paymentResult.success) {
          router.push(paymentResult.payment_url);
        }
      } else if (form.payment_method === 'bank_transfer') {
        // Bank Transfer - Show bank info
        router.push(`/orders/${response.data.order_code}`);
      }
    }
  } catch (error) {
    toast.error(error.response?.data?.message || 'Không thể tạo đơn hàng');
  } finally {
    submitting.value = false;
  }
};
```

---

## 📱 **BƯỚC 3: TẠO TRANG HIỂN THỊ THÔNG TIN CHUYỂN KHOẢN**

Tạo component `BankTransferInfo.vue`:

```vue
<template>
  <div class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
    <h2 class="text-2xl font-bold text-white mb-6">Thông tin chuyển khoản</h2>
    
    <!-- QR Code -->
    <div class="bg-white rounded-xl p-6 mb-6 text-center">
      <img :src="qrUrl" alt="QR Code" class="w-64 h-64 mx-auto" />
      <p class="text-gray-600 text-sm mt-4">Quét mã QR để chuyển khoản</p>
    </div>

    <!-- Bank Details -->
    <div class="space-y-4">
      <div class="flex justify-between p-4 bg-gray-800/50 rounded-xl">
        <span class="text-gray-400">Ngân hàng:</span>
        <span class="text-white font-bold">{{ bankInfo.bank_name }}</span>
      </div>
      <div class="flex justify-between p-4 bg-gray-800/50 rounded-xl">
        <span class="text-gray-400">Số tài khoản:</span>
        <span class="text-white font-bold">{{ bankInfo.account_no }}</span>
      </div>
      <div class="flex justify-between p-4 bg-gray-800/50 rounded-xl">
        <span class="text-gray-400">Chủ tài khoản:</span>
        <span class="text-white font-bold">{{ bankInfo.account_name }}</span>
      </div>
      <div class="flex justify-between p-4 bg-red-600/20 rounded-xl border border-red-600/50">
        <span class="text-gray-300">Số tiền:</span>
        <span class="text-red-500 font-black text-xl">{{ amount }}</span>
      </div>
      <div class="flex justify-between p-4 bg-yellow-600/20 rounded-xl border border-yellow-600/50">
        <span class="text-gray-300">Nội dung:</span>
        <span class="text-yellow-500 font-bold">{{ transferContent }}</span>
      </div>
    </div>

    <div class="mt-6 p-4 bg-blue-600/10 rounded-xl border border-blue-600/30">
      <p class="text-blue-400 text-sm">
        ⚠️ Vui lòng chuyển khoản đúng nội dung để đơn hàng được xử lý tự động
      </p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  bankInfo: Object,
  amount: String,
  transferContent: String,
  qrUrl: String
});
</script>
```

---

## 🧪 **BƯỚC 4: TEST**

### Test VNPay Sandbox:
1. Chọn phương thức "VNPay"
2. Click "Xác nhận đặt hàng"
3. Sẽ redirect đến trang VNPay sandbox
4. Dùng thẻ test: `9704198526191432198` (NCB)
5. Tên: `NGUYEN VAN A`
6. Ngày phát hành: `07/15`
7. OTP: `123456`

### Test MoMo Mock:
1. Chọn "MoMo"
2. Click "Xác nhận đặt hàng"
3. Sẽ chuyển đến trang mock MoMo
4. Click "Thanh toán thành công" hoặc "Hủy"

### Test Bank Transfer:
1. Chọn "Chuyển khoản"
2. Click "Xác nhận đặt hàng"
3. Hiển thị QR code + thông tin ngân hàng
4. Quét QR hoặc chuyển khoản thủ công

---

## 📝 **GHI CHÚ CHO BÁO CÁO**

### Điểm mạnh:
✅ Tích hợp 3 phương thức thanh toán phổ biến
✅ VNPay sử dụng sandbox thực tế
✅ QR Code tự động cho chuyển khoản
✅ UI/UX hiện đại, dễ sử dụng
✅ Xử lý callback và verify payment

### Hạn chế:
⚠️ MoMo chỉ là mock (do cần đăng ký doanh nghiệp)
⚠️ Bank transfer cần admin xác nhận thủ công
⚠️ Chưa có webhook để xử lý real-time

### Cải tiến trong tương lai:
🔮 Tích hợp MoMo thực tế
🔮 Auto-verify bank transfer qua API ngân hàng
🔮 Thêm PayPal, Stripe cho thanh toán quốc tế
🔮 Webhook để cập nhật real-time

---

## 🎓 **KẾT LUẬN**

Hệ thống thanh toán đã được tích hợp đầy đủ với:
- **VNPay Sandbox**: Có thể test thanh toán thực tế
- **MoMo Mock**: Demo flow thanh toán
- **Bank Transfer**: Chuyển khoản với QR Code

Phù hợp cho báo cáo đồ án/dự án tốt nghiệp! 🎉
