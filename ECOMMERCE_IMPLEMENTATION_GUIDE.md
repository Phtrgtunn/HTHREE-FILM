# 🛒 Hướng dẫn triển khai Website Thương mại điện tử - HTHREE Film

## 📋 Tổng quan

Dự án đã được chuyển đổi từ **website xem phim** thành **website thương mại điện tử bán gói xem phim** với đầy đủ tính năng:

✅ **Quản lý sản phẩm** - 4 gói: Free, Basic, Premium, VIP
✅ **Giỏ hàng** - Thêm, sửa, xóa gói
✅ **Đặt hàng** - Checkout với thông tin khách hàng
✅ **Thanh toán** - VNPay, MoMo, Chuyển khoản
✅ **Mã giảm giá** - Coupon system
✅ **Quản lý đơn hàng** - Theo dõi trạng thái
✅ **Subscription** - Tự động kích hoạt gói sau thanh toán

---

## 🚀 BƯỚC 1: SETUP DATABASE

### 1.1. Import Schema

1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Chọn database `hthree_film` (hoặc tạo mới)
3. Click tab **SQL**
4. Copy nội dung file `backend/database/ecommerce_schema.sql`
5. Paste và click **Go**

### 1.2. Kiểm tra

Sau khi import, bạn sẽ có các bảng mới:

**Sản phẩm:**
- `subscription_plans` - Các gói xem phim
- `user_subscriptions` - Gói đang dùng của user

**Giỏ hàng & Đơn hàng:**
- `cart` - Giỏ hàng
- `orders` - Đơn hàng
- `order_items` - Chi tiết đơn hàng
- `transactions` - Lịch sử giao dịch

**Khuyến mãi:**
- `coupons` - Mã giảm giá
- `coupon_usage` - Lịch sử dùng mã

### 1.3. Dữ liệu mẫu

Schema đã tự động tạo:
- 4 gói xem phim (Free, Basic, Premium, VIP)
- 3 mã giảm giá mẫu (WELCOME2025, SUMMER50K, VIP30)

---

## 🔧 BƯỚC 2: CẤU HÌNH BACKEND

### 2.1. Kiểm tra file config

File `backend/config/database.php` phải có:

```php
<?php
$host = 'localhost';
$dbname = 'hthree_film';
$username = 'root';
$password = 'mysql'; // AMPPS default

$conn = new mysqli($host, $username, $password, $dbname);
$conn->set_charset("utf8mb4");
```

### 2.2. Test API

Mở trình duyệt và test các endpoint:

**Lấy danh sách gói:**
```
http://localhost/HTHREE_film/backend/api/plans.php
```

**Lấy giỏ hàng (user_id=1):**
```
http://localhost/HTHREE_film/backend/api/cart.php?user_id=1
```

**Lấy mã giảm giá:**
```
http://localhost/HTHREE_film/backend/api/coupons.php
```

Nếu thấy JSON response → Backend OK ✅

---

## 💻 BƯỚC 3: CẤU HÌNH FRONTEND

### 3.1. Cài đặt dependencies

```bash
npm install
```

### 3.2. Kiểm tra file .env

File `.env` phải có:

```env
VITE_API_BASE_URL=http://localhost/HTHREE_film/backend/api
```

### 3.3. Chạy dev server

```bash
npm run dev
```

Mở: http://localhost:5173

---

## 🎨 BƯỚC 4: TEST CHỨC NĂNG

### 4.1. Test Pricing Page

1. Truy cập: http://localhost:5173/pricing
2. Xem 4 gói: Free, Basic, Premium, VIP
3. Click "Chọn gói này" → Phải đăng nhập

### 4.2. Test Giỏ hàng

1. Đăng nhập (nếu chưa)
2. Chọn gói Premium → Click "Chọn gói này"
3. Tự động chuyển đến: http://localhost:5173/cart
4. Thấy gói trong giỏ hàng
5. Thử tăng/giảm số tháng
6. Thử nhập mã giảm giá: `WELCOME2025`

### 4.3. Test Checkout

1. Từ giỏ hàng → Click "Thanh toán"
2. Điền thông tin:
   - Họ tên: Nguyễn Văn A
   - Email: test@test.com
   - SĐT: 0901234567
3. Chọn phương thức: Chuyển khoản ngân hàng
4. Click "Đặt hàng"
5. Kiểm tra database:

```sql
-- Xem đơn hàng vừa tạo
SELECT * FROM orders ORDER BY created_at DESC LIMIT 1;

-- Xem chi tiết đơn
SELECT * FROM order_items WHERE order_id = (SELECT id FROM orders ORDER BY created_at DESC LIMIT 1);

-- Giỏ hàng phải rỗng
SELECT * FROM cart WHERE user_id = 1;
```

### 4.4. Test Thanh toán

```sql
-- Giả lập thanh toán thành công
UPDATE orders 
SET payment_status = 'paid', paid_at = NOW() 
WHERE order_code = 'ORD20250101001'; -- Thay bằng mã đơn của bạn

-- Kiểm tra gói đã kích hoạt chưa
SELECT * FROM user_subscriptions WHERE user_id = 1;

-- Kiểm tra function
SELECT fn_has_active_subscription(1); -- Phải trả về 1 (TRUE)
```

---

## 📊 BƯỚC 5: QUẢN LÝ ĐƠN HÀNG

### 5.1. Xem đơn hàng qua API

**Lấy đơn hàng của user:**
```
http://localhost/HTHREE_film/backend/api/orders.php?user_id=1
```

**Xem chi tiết đơn:**
```
http://localhost/HTHREE_film/backend/api/orders.php?order_id=1
```

**Xem theo mã đơn:**
```
http://localhost/HTHREE_film/backend/api/orders.php?order_code=ORD20250101001
```

### 5.2. Cập nhật trạng thái (Postman)

**Đánh dấu đã thanh toán:**
```json
PUT http://localhost/HTHREE_film/backend/api/orders.php

{
  "order_id": 1,
  "payment_status": "paid"
}
```

**Hủy đơn:**
```json
PUT http://localhost/HTHREE_film/backend/api/orders.php

{
  "order_id": 1,
  "status": "cancelled"
}
```

---

## 🎯 BƯỚC 6: TÍCH HỢP THANH TOÁN THẬT

### 6.1. VNPay

1. Đăng ký tài khoản: https://sandbox.vnpayment.vn/
2. Lấy `vnp_TmnCode` và `vnp_HashSecret`
3. Tạo file `backend/api/payment/vnpay.php`
4. Tham khảo: https://sandbox.vnpayment.vn/apis/docs/

### 6.2. MoMo

1. Đăng ký: https://business.momo.vn/
2. Lấy `partnerCode` và `accessKey`
3. Tạo file `backend/api/payment/momo.php`
4. Tham khảo: https://developers.momo.vn/

---

## 📱 BƯỚC 7: TÍNH NĂNG BỔ SUNG

### 7.1. Middleware kiểm tra subscription

Tạo file `src/middleware/subscriptionMiddleware.js`:

```javascript
import { checkActiveSubscription } from '@/services/ecommerceApi';

export const requireSubscription = async (to, from, next) => {
  const user = getCurrentUser();
  
  if (!user) {
    return next('/account');
  }
  
  const response = await checkActiveSubscription(user.id);
  
  if (!response.data.has_active_subscription) {
    return next('/pricing');
  }
  
  next();
};
```

Áp dụng cho route xem phim:

```javascript
{
  path: '/film/:filmName/tap/:tap',
  name: 'WatchMovie',
  component: WatchMovie,
  beforeEnter: requireSubscription
}
```

### 7.2. Hiển thị gói đang dùng

Trong `Account.vue`, thêm:

```vue
<script setup>
import { getUserSubscriptions } from '@/services/ecommerceApi';

const subscription = ref(null);

onMounted(async () => {
  const response = await getUserSubscriptions(user.id);
  subscription.value = response.data.active_subscription;
});
</script>

<template>
  <div v-if="subscription" class="bg-gray-800 rounded-lg p-6">
    <h3 class="text-xl font-bold text-white mb-4">Gói đang dùng</h3>
    <div class="flex items-center justify-between">
      <div>
        <p class="text-2xl font-bold text-yellow-400">{{ subscription.plan_name }}</p>
        <p class="text-gray-400">Hết hạn: {{ subscription.end_date_formatted }}</p>
        <p class="text-gray-400">Còn {{ subscription.days_remaining }} ngày</p>
      </div>
      <router-link to="/pricing" class="bg-red-600 text-white px-6 py-3 rounded-lg">
        Gia hạn
      </router-link>
    </div>
  </div>
</template>
```

### 7.3. Admin Panel (Tùy chọn)

Tạo các trang admin:
- `/admin/orders` - Quản lý đơn hàng
- `/admin/users` - Quản lý user
- `/admin/plans` - Quản lý gói
- `/admin/coupons` - Quản lý mã giảm giá

---

## 🐛 TROUBLESHOOTING

### Lỗi: "Failed to fetch"

**Nguyên nhân:** CORS hoặc API URL sai

**Giải pháp:**
1. Kiểm tra `backend/config/cors.php`
2. Thêm origin vào `ALLOWED_ORIGINS`
3. Kiểm tra `.env` có đúng URL không

### Lỗi: "Cart is empty"

**Nguyên nhân:** User chưa đăng nhập hoặc giỏ hàng thực sự rỗng

**Giải pháp:**
1. Kiểm tra `authStore.user` có giá trị không
2. Kiểm tra database: `SELECT * FROM cart WHERE user_id = 1`

### Lỗi: "Stored procedure not found"

**Nguyên nhân:** Chưa import schema đầy đủ

**Giải pháp:**
1. Drop database và tạo lại
2. Import lại `ecommerce_schema.sql`

### Giỏ hàng không cập nhật

**Giải pháp:**
```javascript
// Trong component, gọi lại fetchCart
await cartStore.fetchCart();
```

---

## 📚 TÀI LIỆU THAM KHẢO

### API Endpoints

**Plans:**
- `GET /api/plans.php` - Lấy danh sách gói
- `GET /api/plans.php?slug=premium` - Lấy 1 gói

**Cart:**
- `GET /api/cart.php?user_id=1` - Lấy giỏ hàng
- `POST /api/cart.php` - Thêm vào giỏ
- `PUT /api/cart.php` - Cập nhật số lượng
- `DELETE /api/cart.php?cart_id=1` - Xóa item

**Orders:**
- `GET /api/orders.php?user_id=1` - Lấy đơn hàng
- `POST /api/orders.php` - Tạo đơn hàng
- `PUT /api/orders.php` - Cập nhật trạng thái

**Subscriptions:**
- `GET /api/subscriptions.php?user_id=1` - Lấy gói đang dùng
- `GET /api/subscriptions.php?user_id=1&check_active=true` - Kiểm tra active

**Coupons:**
- `GET /api/coupons.php` - Lấy mã giảm giá
- `POST /api/coupons.php` - Kiểm tra mã

### Database Views

```sql
-- Gói active của user
SELECT * FROM v_active_subscriptions;

-- Thống kê đơn hàng
SELECT * FROM v_order_stats;

-- Top gói bán chạy
SELECT * FROM v_top_selling_plans;
```

---

## ✅ CHECKLIST HOÀN THÀNH

- [x] Database schema
- [x] Backend API (Plans, Cart, Orders, Subscriptions, Coupons)
- [x] Frontend service layer
- [x] Pinia store (Cart)
- [x] Pricing page
- [x] Cart page
- [x] Checkout page
- [x] Router integration
- [x] Navbar cart icon
- [ ] Payment gateway integration (VNPay, MoMo)
- [ ] Admin panel
- [ ] Subscription middleware
- [ ] Email notifications
- [ ] Order history page

---

## 🎉 KẾT LUẬN

Dự án của bạn đã được chuyển đổi thành công thành **website thương mại điện tử** với đầy đủ tính năng cơ bản. 

**Các bước tiếp theo:**
1. Test kỹ tất cả chức năng
2. Tích hợp cổng thanh toán thật (VNPay/MoMo)
3. Xây dựng Admin Panel
4. Thêm email notification
5. Deploy lên production

**Cần hỗ trợ thêm?**
- Tích hợp VNPay/MoMo
- Xây dựng Admin Panel
- Middleware kiểm tra subscription
- Email notifications

Chúc bạn thành công! 🚀
