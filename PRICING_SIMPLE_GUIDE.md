# 🎬 Trang Pricing - Thiết kế đơn giản & hiện đại

## ✨ Tính năng

### 1. **4 Pricing Cards**
- Layout: Grid 4 cột (responsive)
- Mỗi card có:
  - Icon emoji lớn (🎬 ⭐ 🔥 👑)
  - Tên gói
  - Badge chất lượng (SD, HD, FHD, 4K)
  - Giá/tháng + giá/ngày
  - Danh sách tính năng
  - Button CTA

### 2. **Hover Animations**
- **Card hover:**
  - Nâng lên `-translate-y-4`
  - Glow effect (gradient blur)
  - Shadow lớn hơn
  
- **Icon hover:**
  - Scale lớn `scale-110`
  - Xoay nhẹ `rotate-6`
  
- **Text hover:**
  - Plan name đổi màu đỏ
  - Price scale lớn

### 3. **Click → Payment Modal**
- Không qua giỏ hàng
- Modal hiện ngay với:
  - Thông tin gói đã chọn
  - Chọn thời hạn (1-12 tháng)
  - Form thông tin khách hàng
  - Chọn phương thức thanh toán
  - Button xác nhận

### 4. **Payment Flow**
```
Click Card → Modal hiện → Điền form → Submit → Tạo đơn hàng → Success
```

## 🎨 Design System

### Colors
- **Free:** Gray (`from-gray-700 to-gray-600`)
- **Basic:** Blue → Cyan (`from-blue-600 to-cyan-500`)
- **Premium:** Red → Yellow (`from-red-600 to-yellow-500`) ⭐
- **VIP:** Purple → Pink (`from-purple-600 to-pink-500`) 👑

### Animations
- `animate-fade-in`: Header (0.8s)
- `animate-slide-up`: Cards (0.6s, staggered 0.1s)
- `animate-pulse`: Background orbs
- `animate-scale-in`: Modal (0.3s)

### Spacing
- Container: `max-w-7xl mx-auto`
- Gap: `gap-6`
- Padding: `p-8`

## 📱 Responsive

### Desktop (lg)
- 4 cards ngang
- Premium card scale 105%

### Tablet (md)
- 2 cards/hàng

### Mobile
- 1 card/hàng
- Full width

## 🔧 Components

### Pricing.vue
- Hiển thị 4 cards
- Handle click → show modal
- Fetch plans từ API

### PaymentModal.vue
- Form thanh toán
- Chọn thời hạn (1-12 tháng)
- Tính tổng tiền
- Submit → Create order trực tiếp

## 🚀 API Flow

### 1. Get Plans
```javascript
GET /api/plans.php
Response: [{ id, name, slug, price, quality, ... }]
```

### 2. Create Direct Order
```javascript
POST /api/orders.php
Body: {
  user_id, plan_id, duration_months,
  customer_name, customer_email, customer_phone,
  payment_method
}
Response: { order_id, order_code, ... }
```

## 💡 User Experience

### Gói Free
- Click → Toast "Bạn đang dùng gói miễn phí"
- Không hiện modal

### Gói trả phí
1. Click card
2. Modal hiện ngay
3. Điền thông tin (auto-fill từ user)
4. Chọn thời hạn (1-12 tháng)
5. Chọn phương thức thanh toán
6. Click "Xác nhận thanh toán"
7. Tạo đơn hàng
8. Redirect về /account hoặc trang order detail

## 🎯 Advantages

✅ **Đơn giản:** Không qua giỏ hàng
✅ **Nhanh:** Click → Modal → Done
✅ **Trực quan:** Thấy ngay tổng tiền
✅ **Linh hoạt:** Chọn thời hạn 1-12 tháng
✅ **Đẹp:** Animations mượt mà

## 📊 Conversion Optimization

- Premium card nổi bật (scale + badge)
- Giá/ngày để dễ quyết định
- Modal nhanh, không rườm rà
- Auto-fill thông tin user
- Clear CTA buttons

## 🔐 Security

- Validate user đăng nhập
- Validate form data
- Sanitize inputs
- HTTPS required

## 🎉 Demo

**URL:** http://localhost:5174/pricing

**Test Flow:**
1. Truy cập /pricing
2. Hover vào cards → Xem animations
3. Click "Chọn gói Premium"
4. Modal hiện
5. Điền form
6. Submit

**Expected Result:**
- Đơn hàng được tạo
- Toast success
- Redirect về /account
