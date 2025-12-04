# 🧪 Test Pricing Page

## ✅ ĐÃ SỬA

### Lỗi đã fix:
1. ✅ **Backend API 400** - Sửa lỗi syntax trong `bind_param`
2. ✅ **$conn is null** - Thêm `$conn = getDBConnection()` vào tất cả API files
3. ✅ **API hoạt động** - Test thành công với curl

### Files đã sửa:
- `backend/api/orders.php` - Fix bind_param syntax
- `backend/api/plans.php` - Add $conn initialization
- `backend/api/cart.php` - Add $conn initialization
- `backend/api/subscriptions.php` - Add $conn initialization
- `backend/api/coupons.php` - Add $conn initialization

## 🧪 CÁCH TEST

### 1. Kiểm tra Backend API

**Test Plans API:**
```bash
curl http://localhost/HTHREE_film/backend/api/plans.php
```

**Expected:** JSON với 4 gói (Free, Basic, Premium, VIP)

**Test Cart API:**
```bash
curl "http://localhost/HTHREE_film/backend/api/cart.php?user_id=1"
```

**Expected:** JSON với giỏ hàng (có thể rỗng)

### 2. Kiểm tra Frontend

**Truy cập:**
```
http://localhost:5174/pricing
```

**Expected:**
- Thấy 4 pricing cards
- Hover vào cards → animations
- Click card → Modal hiện

### 3. Test Full Flow

**Bước 1:** Đăng nhập
- Vào `/account`
- Đăng nhập với tài khoản

**Bước 2:** Chọn gói
- Vào `/pricing`
- Click "Chọn gói Premium"

**Bước 3:** Modal hiện
- Thấy form thanh toán
- Thông tin auto-fill
- Chọn thời hạn (1-12 tháng)

**Bước 4:** Submit
- Điền đầy đủ thông tin
- Click "Xác nhận thanh toán"
- Đơn hàng được tạo

## 🐛 Troubleshooting

### Nếu vẫn lỗi "Error fetching cart"

**Kiểm tra:**
1. AMPPS MySQL đã start chưa?
2. Database `hthree_film` đã tạo chưa?
3. Import `ecommerce_schema.sql` chưa?

**Fix:**
```bash
# Mở phpMyAdmin
http://localhost/phpmyadmin

# Import file
backend/database/ecommerce_schema.sql
```

### Nếu API trả về lỗi

**Kiểm tra:**
```bash
# Test từng API
curl http://localhost/HTHREE_film/backend/api/plans.php
curl http://localhost/HTHREE_film/backend/api/cart.php?user_id=1
```

**Xem log:**
- Mở browser DevTools → Console
- Xem lỗi chi tiết

### Nếu Modal không hiện

**Kiểm tra:**
1. User đã đăng nhập chưa?
2. Console có lỗi không?
3. Component `PaymentModal.vue` đã import đúng chưa?

## ✅ Checklist

- [ ] AMPPS đã start
- [ ] MySQL đã start
- [ ] Database `hthree_film` đã tạo
- [ ] Import `ecommerce_schema.sql`
- [ ] Backend API test OK
- [ ] Frontend dev server chạy
- [ ] Đăng nhập thành công
- [ ] Pricing page hiển thị
- [ ] Click card → Modal hiện
- [ ] Submit form → Đơn hàng tạo

## 🎯 Expected Results

### Backend API
```json
{
  "success": true,
  "data": [
    {
      "id": "1",
      "name": "Free",
      "slug": "free",
      "price": "0.00",
      "quality": "SD",
      ...
    },
    ...
  ]
}
```

### Frontend
- 4 cards hiển thị đẹp
- Hover animations mượt
- Click → Modal hiện ngay
- Form auto-fill thông tin user
- Submit → Toast success

## 📝 Notes

- Gói Free không cần thanh toán
- Gói trả phí → Modal thanh toán
- Không qua giỏ hàng (direct checkout)
- Thời hạn: 1-12 tháng
- Tổng tiền = giá × số tháng

---

**Status:** ✅ FIXED - Ready to test!
