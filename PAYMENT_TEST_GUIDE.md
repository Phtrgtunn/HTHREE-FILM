# 💳 Hướng dẫn Test Thanh toán

## 🎯 Các phương thức thanh toán đã tích hợp

### 1. VNPay ✅
- **Môi trường**: Sandbox (Test)
- **API**: `backend/api/payment/vnpay_create.php`
- **Tài khoản test**: https://sandbox.vnpayment.vn/apis/vnpay-demo/

**Thông tin test:**
```
Ngân hàng: NCB
Số thẻ: 9704198526191432198
Tên chủ thẻ: NGUYEN VAN A
Ngày phát hành: 07/15
Mật khẩu OTP: 123456
```

### 2. MoMo ✅
- **Môi trường**: Test
- **API**: `backend/api/payment/momo_create.php`
- **Tài khoản test**: Tự động chuyển đến trang test MoMo

**Thông tin test:**
```
Số điện thoại: 0999999999
OTP: Bất kỳ (test mode)
```

### 3. ZaloPay ✅
- **Môi trường**: Sandbox
- **API**: `backend/api/payment/zalopay_create.php`
- **Tài khoản test**: Tự động chuyển đến trang test ZaloPay

**Thông tin test:**
```
Số điện thoại: 0999999999
OTP: 111111
```

### 4. Chuyển khoản ngân hàng ✅
- **API**: `backend/api/payment/bank_transfer.php`
- **QR Code**: Tự động tạo bằng VietQR

### 5. COD (Thanh toán khi nhận hàng) ✅
- Không cần API
- Đơn hàng được tạo với trạng thái "pending"

## 🧪 Cách test

### Bước 1: Thêm gói vào giỏ hàng
1. Vào trang `/pricing`
2. Chọn gói (Basic, Premium, VIP)
3. Click "Thêm vào giỏ"

### Bước 2: Checkout
1. Vào `/cart`
2. Click "Thanh toán"
3. Điền thông tin:
   - Họ tên
   - Email
   - Số điện thoại (tùy chọn)

### Bước 3: Chọn phương thức thanh toán

#### Test VNPay:
1. Chọn "VNPay"
2. Click "Xác nhận đặt hàng"
3. Sẽ chuyển đến trang VNPay sandbox
4. Nhập thông tin thẻ test (xem trên)
5. Nhập OTP: `123456`
6. Hoàn tất thanh toán

#### Test MoMo:
1. Chọn "MoMo"
2. Click "Xác nhận đặt hàng"
3. Sẽ chuyển đến trang MoMo test
4. Nhập SĐT: `0999999999`
5. Nhập OTP bất kỳ
6. Xác nhận thanh toán

#### Test ZaloPay:
1. Chọn "ZaloPay"
2. Click "Xác nhận đặt hàng"
3. Sẽ chuyển đến trang ZaloPay sandbox
4. Nhập SĐT: `0999999999`
5. Nhập OTP: `111111`
6. Xác nhận thanh toán

#### Test Chuyển khoản:
1. Chọn "Chuyển khoản ngân hàng"
2. Click "Xác nhận đặt hàng"
3. Sẽ hiển thị thông tin chuyển khoản + QR Code
4. Quét QR hoặc chuyển khoản thủ công
5. Admin xác nhận đơn hàng sau khi nhận tiền

#### Test COD:
1. Chọn "COD"
2. Click "Xác nhận đặt hàng"
3. Đơn hàng được tạo ngay
4. Thanh toán khi nhận hàng

## 📊 Kiểm tra kết quả

### Trong Admin Panel:
1. Vào `/admin`
2. Tab "Đơn hàng"
3. Xem trạng thái:
   - `pending`: Chờ thanh toán
   - `paid`: Đã thanh toán
   - `completed`: Hoàn thành

### Trong Database:
```sql
-- Xem đơn hàng mới nhất
SELECT * FROM orders ORDER BY id DESC LIMIT 5;

-- Xem giao dịch
SELECT * FROM transactions ORDER BY id DESC LIMIT 5;
```

## 🔧 Cấu hình

### VNPay:
File: `backend/api/payment/vnpay_create.php`
```php
$vnp_TmnCode = "DEMO"; // Đổi thành mã thật khi production
$vnp_HashSecret = "DEMOSECRETKEY"; // Đổi thành secret key thật
```

### MoMo:
File: `backend/api/payment/momo_create.php`
```php
$partnerCode = "MOMOBKUN20180529"; // Đổi thành partner code thật
$accessKey = "klm05TvNBzhg7h7j"; // Đổi thành access key thật
$secretKey = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa"; // Đổi thành secret key thật
```

### ZaloPay:
File: `backend/api/payment/zalopay_create.php`
```php
$config = [
    "app_id" => 2553, // Đổi thành app_id thật
    "key1" => "PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL", // Đổi thành key1 thật
    "key2" => "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz" // Đổi thành key2 thật
];
```

## 🚀 Production

Khi deploy lên production:
1. Đổi tất cả config từ sandbox/test sang production
2. Cập nhật `returnUrl` và `notifyUrl` thành domain thật
3. Đăng ký tài khoản merchant thật với các cổng thanh toán
4. Test kỹ trước khi go-live

## 📝 Lưu ý

- Tất cả đang dùng **môi trường test/sandbox**
- Không có tiền thật được giao dịch
- Cần đăng ký tài khoản merchant thật để sử dụng production
- Callback URL cần public để cổng thanh toán gọi được

## 🎉 Hoàn tất!

Website đã tích hợp đầy đủ 5 phương thức thanh toán:
- ✅ VNPay
- ✅ MoMo
- ✅ ZaloPay
- ✅ Chuyển khoản ngân hàng
- ✅ COD

Sẵn sàng cho thương mại điện tử! 🚀
