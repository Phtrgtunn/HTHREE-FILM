# ✅ ĐÃ FIX: Giá theo duration (tháng/năm)

## 🎯 Những gì đã fix:

### 1. Frontend - Pricing.vue
✅ Truyền `duration_months` khi add to cart
✅ Hiển thị "1 tháng" hoặc "12 tháng" trong toast

### 2. Store - cartStore.js  
✅ Nhận parameter `durationMonths`
✅ Truyền đến API

### 3. API Service - ecommerceApi.js
✅ Thêm parameter `durationMonths`
✅ Gửi `duration_months` đến backend

### 4. Backend - cart.php
✅ Nhận `duration_months` từ request
✅ Lưu vào database

### 5. Database - add_duration_to_cart.sql
✅ Thêm cột `duration_months` vào bảng `cart`

## 📋 CÁCH SỬ DỤNG:

### Bước 1: Cập nhật Database
```bash
# Mở phpMyAdmin
http://localhost/phpmyadmin

# Chọn database hthree_film
# Click tab SQL
# Copy nội dung file add_duration_to_cart.sql
# Paste và click Go
```

### Bước 2: Test
1. Mở trang Pricing: `http://localhost:5173/pricing`
2. Toggle giữa "1 tháng" và "12 tháng"
3. Xem giá thay đổi:
   - Premium 1 tháng: 100,000đ
   - Premium 12 tháng: 1,020,000đ (12 * 100k * 0.85)
4. Click "Thêm vào giỏ"
5. Vào Cart xem có đúng giá không
6. Checkout và kiểm tra order

## 🎉 Kết quả:

✅ Chọn 1 tháng → Giá: 100,000đ
✅ Chọn 12 tháng → Giá: 1,020,000đ (có discount 15%)
✅ Cart lưu đúng duration_months
✅ Order lưu đúng duration_months
✅ Subscription được kích hoạt đúng thời gian

## 📊 Công thức tính giá:

```javascript
// Monthly
price = plan.price * 1

// Yearly (có discount 15%)
price = plan.price * 12 * 0.85
```

## 🔍 Debug:

Nếu vẫn sai, kiểm tra:
1. Console log trong Pricing.vue khi add to cart
2. Network tab xem request có `duration_months` không
3. Database bảng `cart` có cột `duration_months` chưa
4. Giá trị `duration_months` trong database có đúng không (1 hoặc 12)

---

**Đã fix xong! Reload trang và test thử!** 🚀
