# 🎄 Hướng Dẫn Tạo Khuyến Mãi Giáng Sinh

## 📦 Gói Giáng Sinh 2024

### Thông Tin Gói:

- **Tên**: Giáng Sinh 2024
- **Giá**: 180,000đ (giảm 40% từ 300,000đ)
- **Thời hạn**: 30 ngày
- **Chất lượng**: Full HD
- **Thiết bị**: 3 thiết bị cùng lúc
- **Tính năng**:
  - ✅ Không quảng cáo
  - ✅ Tải xuống
  - ✅ Xem phim sớm

---

## 🎁 Mã Giảm Giá Giáng Sinh

### 1. GIANGSINH2024

- **Giảm**: 20%
- **Thời gian**: 1/12 - 31/12/2024
- **Đơn tối thiểu**: 100,000đ
- **Giảm tối đa**: 100,000đ
- **Giới hạn**: 100 lượt

### 2. NOEL50K

- **Giảm**: 50,000đ
- **Thời gian**: 20/12 - 26/12/2024 (Tuần lễ Noel)
- **Đơn tối thiểu**: 200,000đ
- **Giới hạn**: 50 lượt

### 3. NAMMOIVUI

- **Giảm**: 30%
- **Thời gian**: 28/12/2024 - 5/1/2025
- **Đơn tối thiểu**: 150,000đ
- **Giảm tối đa**: 150,000đ
- **Giới hạn**: 200 lượt

---

## 🚀 Cách Thêm Vào Database

### Cách 1: Dùng SQL Script (Nhanh)

1. **Mở AMPPS** → Khởi động MySQL
2. **Mở phpMyAdmin**: `http://localhost/phpmyadmin`
3. **Chọn database**: `hthree_film`
4. **Vào tab SQL**
5. **Copy & Paste**:
   - File `add_christmas_plan.sql` → Chạy
   - File `add_christmas_coupons.sql` → Chạy
6. **Click "Go"**

### Cách 2: Dùng Admin Panel (Dễ)

#### Tạo Gói Giáng Sinh:

1. Vào Admin Panel → Tab "Gói dịch vụ"
2. Click "Tạo gói mới"
3. Điền thông tin:
   - Tên gói: `Giáng Sinh 2024`
   - Slug: `christmas`
   - Mô tả: `🎄 Gói đặc biệt mùa Giáng Sinh - Ưu đãi 40%`
   - Giá: `180000`
   - Thời hạn: `30` ngày
   - Chất lượng: `Full HD`
   - Số thiết bị: `3`
   - ✅ Tải xuống
   - ✅ Xem sớm
   - ✅ Kích hoạt
4. Click "Tạo mới"

#### Tạo Mã Giảm Giá:

1. Vào Admin Panel → Tab "Mã giảm giá"
2. Click "Tạo mã mới"
3. Điền thông tin theo bảng trên
4. Click "Tạo mới"

---

## 📊 Kiểm Tra Kết Quả

### Trang Pricing:

1. Mở: `http://localhost:5173/pricing`
2. Refresh trang (F5)
3. Gói "Giáng Sinh 2024" sẽ xuất hiện cuối cùng

### Admin Panel:

1. Mở: `http://localhost:5173/admin`
2. Tab "Gói dịch vụ" → Thấy gói mới
3. Tab "Mã giảm giá" → Thấy 3 mã mới

---

## 🎯 Chiến Lược Marketing

### Timeline:

- **1-19/12**: Chạy mã `GIANGSINH2024` (20%)
- **20-26/12**: Chạy mã `NOEL50K` (50,000đ) - Tuần lễ Noel
- **27/12**: Ngừng mã Noel, chuẩn bị năm mới
- **28/12-5/1**: Chạy mã `NAMMOIVUI` (30%) - Chào năm mới

### Combo Khuyến Mãi:

- **Gói Giáng Sinh** (180,000đ) + Mã **GIANGSINH2024** (20%)
  = Chỉ còn **144,000đ** (tiết kiệm 156,000đ!)

---

## 🔧 Quản Lý Sau Mùa Lễ

### Tắt Gói Giáng Sinh (sau 31/12):

```sql
UPDATE subscription_plans
SET is_active = 0
WHERE slug = 'christmas';
```

### Tắt Tất Cả Mã Giảm Giá:

```sql
UPDATE coupons
SET is_active = 0
WHERE code IN ('GIANGSINH2024', 'NOEL50K', 'NAMMOIVUI');
```

### Hoặc Dùng Admin Panel:

- Tab "Gói dịch vụ" → Click nút "Tắt" trên gói Giáng Sinh
- Tab "Mã giảm giá" → Click nút "Tắt" trên từng mã

---

## 📈 Theo Dõi Hiệu Quả

### Dashboard Admin:

- **Tổng doanh thu**: Xem tăng bao nhiêu
- **Đơn hàng**: Đếm số đơn mua gói Giáng Sinh
- **Top gói**: Xem gói Giáng Sinh có phổ biến không

### Query Thống Kê:

```sql
-- Số đơn mua gói Giáng Sinh
SELECT COUNT(*) as total_orders, SUM(total) as revenue
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN subscription_plans sp ON oi.plan_id = sp.id
WHERE sp.slug = 'christmas' AND o.payment_status = 'paid';

-- Số lượt dùng mã giảm giá
SELECT code, usage_count, usage_limit
FROM coupons
WHERE code IN ('GIANGSINH2024', 'NOEL50K', 'NAMMOIVUI');
```

---

## 🎉 Kết Luận

Với gói Giáng Sinh và 3 mã giảm giá, bạn có thể:

- ✅ Thu hút khách hàng mới
- ✅ Tăng doanh thu mùa lễ hội
- ✅ Tạo sự kiện đặc biệt
- ✅ Quản lý dễ dàng qua Admin Panel

**Chúc bạn mùa Giáng Sinh thành công! 🎄🎅**
