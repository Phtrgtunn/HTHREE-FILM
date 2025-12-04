# 🚀 HƯỚNG DẪN TEST FLOW MUA GÓI

## 📋 **FLOW HOÀN CHỈNH:**

```
Pricing → Cart → Checkout → Payment → Order Success
   ↓        ↓        ↓          ↓           ↓
 Chọn    Xem lại  Điền TT   Thanh toán  Hoàn tất
```

---

## ✅ **BƯỚC 1: ĐĂNG NHẬP**

**URL:** `http://localhost:5174/account`

1. Đăng nhập hoặc đăng ký tài khoản
2. Cập nhật thông tin profile (tên, SĐT) - **Quan trọng!**
3. Thông tin này sẽ tự động điền vào form checkout

---

## ✅ **BƯỚC 2: CHỌN GÓI**

**URL:** `http://localhost:5174/pricing`

### Các gói có sẵn:
- 🎬 **Free** - Miễn phí (không cần mua)
- ⭐ **Basic** - 70,000đ/tháng
- 🔥 **Premium** - 180,000đ/tháng (PHỔ BIẾN)
- 👑 **VIP** - 260,000đ/tháng

### Thao tác:
1. Click nút **"Thêm vào giỏ"** ở gói bạn muốn
2. Toast notification xuất hiện: ✅ "Đã thêm gói Premium vào giỏ hàng"
3. Icon giỏ hàng trên navbar hiện số lượng

---

## ✅ **BƯỚC 3: XEM GIỎ HÀNG**

**URL:** `http://localhost:5174/cart`

### Tính năng:
- ✅ Xem danh sách gói đã chọn
- ✅ Thay đổi số lượng (thời hạn: 1-12 tháng)
- ✅ Xóa gói khỏi giỏ hàng
- ✅ Áp dụng mã giảm giá
- ✅ Xem tổng tiền

### Thao tác:
1. Kiểm tra thông tin gói
2. Điều chỉnh số tháng (nếu cần)
3. Nhập mã giảm giá (nếu có)
4. Click **"Thanh toán ngay"**

---

## ✅ **BƯỚC 4: THANH TOÁN**

**URL:** `http://localhost:5174/checkout`

### Form thông tin:
- ✅ Họ tên (tự động điền từ profile)
- ✅ Email (tự động điền)
- ✅ Số điện thoại (tự động điền)
- ✅ Ghi chú (tùy chọn)

### Chọn phương thức thanh toán:

#### 1️⃣ **VNPay** (Test thực tế)
```
Thẻ test: 9704198526191432198
Tên: NGUYEN VAN A
Ngày: 07/15
OTP: 123456
```

#### 2️⃣ **MoMo** (Mock/Demo)
- Click "Thanh toán thành công" để test
- Hoặc "Hủy thanh toán" để test fail

#### 3️⃣ **Chuyển khoản ngân hàng**
- Hiển thị QR Code
- Thông tin tài khoản
- Nội dung chuyển khoản

### Thao tác:
1. Kiểm tra thông tin đã điền
2. Chọn phương thức thanh toán
3. Click **"Xác nhận đặt hàng"**

---

## ✅ **BƯỚC 5: XỬ LÝ THANH TOÁN**

### Với VNPay:
1. Redirect đến trang VNPay Sandbox
2. Nhập thông tin thẻ test
3. Xác nhận OTP
4. Redirect về `/payment-return`

### Với MoMo Mock:
1. Redirect đến trang MoMo Mock
2. Click "Thanh toán thành công"
3. Redirect về `/payment-return`

### Với Chuyển khoản:
1. Hiển thị thông tin ngân hàng + QR
2. User chuyển khoản thủ công
3. Admin xác nhận (manual)

---

## ✅ **BƯỚC 6: KẾT QUẢ**

**URL:** `http://localhost:5174/payment-return`

### Thành công:
- ✅ Icon check màu xanh
- ✅ Thông tin đơn hàng
- ✅ Nút "Xem đơn hàng" / "Về trang chủ"

### Thất bại:
- ❌ Icon X màu đỏ
- ❌ Thông báo lỗi
- ❌ Nút "Thử lại" / "Về trang chủ"

---

## 🧪 **TEST CASES**

### Test Case 1: Mua 1 gói Premium
```
1. Login
2. Pricing → Click "Thêm vào giỏ" (Premium)
3. Cart → Click "Thanh toán ngay"
4. Checkout → Chọn VNPay → "Xác nhận"
5. VNPay → Nhập thẻ test → Xác nhận
6. Payment Return → Thành công ✅
```

### Test Case 2: Mua nhiều gói
```
1. Login
2. Pricing → Thêm Basic
3. Pricing → Thêm Premium
4. Cart → Xem 2 gói → Thanh toán
5. Checkout → Chọn MoMo → Xác nhận
6. MoMo Mock → "Thanh toán thành công"
7. Payment Return → Thành công ✅
```

### Test Case 3: Áp dụng mã giảm giá
```
1. Login
2. Pricing → Thêm VIP
3. Cart → Nhập mã "DISCOUNT10" → Áp dụng
4. Cart → Xem giá giảm → Thanh toán
5. Checkout → Chọn Bank Transfer
6. Hiển thị QR Code + thông tin
```

### Test Case 4: Hủy thanh toán
```
1. Login
2. Pricing → Thêm Premium
3. Cart → Thanh toán
4. Checkout → Chọn MoMo → Xác nhận
5. MoMo Mock → "Hủy thanh toán"
6. Payment Return → Thất bại ❌
```

---

## 🐛 **TROUBLESHOOTING**

### Vấn đề 1: Không thêm được vào giỏ
**Nguyên nhân:** Chưa đăng nhập
**Giải pháp:** Đăng nhập tại `/account`

### Vấn đề 2: Thông tin không tự động điền
**Nguyên nhân:** Chưa cập nhật profile
**Giải pháp:** Vào Account → Cập nhật tên + SĐT → Save

### Vấn đề 3: VNPay không redirect
**Nguyên nhân:** Backend chưa chạy hoặc config sai
**Giải pháp:** Kiểm tra `backend/api/payment/vnpay_create.php`

### Vấn đề 4: QR Code không hiện
**Nguyên nhân:** API VietQR bị chặn
**Giải pháp:** Kiểm tra internet hoặc dùng QR khác

---

## 📊 **DATABASE CHECK**

### Kiểm tra đơn hàng đã tạo:
```sql
SELECT * FROM orders ORDER BY created_at DESC LIMIT 10;
```

### Kiểm tra giỏ hàng:
```sql
SELECT * FROM cart WHERE user_id = 1;
```

### Kiểm tra payment status:
```sql
SELECT order_code, payment_status, payment_method, total_amount 
FROM orders 
WHERE user_id = 1 
ORDER BY created_at DESC;
```

---

## 🎯 **CHECKLIST DEMO**

Trước khi demo/báo cáo, kiểm tra:

- [ ] Backend đang chạy (`http://localhost/HTHREE_film/backend/api/`)
- [ ] Frontend đang chạy (`http://localhost:5174`)
- [ ] Database có dữ liệu plans
- [ ] Đã tạo tài khoản test
- [ ] Profile đã có tên + SĐT
- [ ] Toast notifications hoạt động
- [ ] Giỏ hàng hiển thị đúng
- [ ] Checkout form auto-fill
- [ ] VNPay sandbox hoạt động
- [ ] MoMo mock hoạt động
- [ ] QR Code hiển thị
- [ ] Payment return xử lý đúng

---

## 🎓 **KẾT LUẬN**

Flow mua gói đã hoàn chỉnh với:
- ✅ UI/UX hiện đại
- ✅ 3 phương thức thanh toán
- ✅ Auto-fill thông tin
- ✅ Real-time feedback
- ✅ Error handling

**Sẵn sàng cho báo cáo/demo!** 🎉
