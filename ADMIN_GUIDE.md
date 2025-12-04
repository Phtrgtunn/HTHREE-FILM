# 🎬 HTHREE Film - Hướng dẫn Admin Panel

## 📋 Tổng quan

Admin Panel là trang quản trị dành cho quản lý website thương mại điện tử phim HTHREE.

## 🔐 Truy cập Admin Panel

### Cách 1: Qua URL
Truy cập: `http://localhost:5173/admin`

### Cách 2: Qua Navbar
- Đăng nhập với tài khoản admin
- Click vào icon bánh răng màu tím ở góc phải navbar

### Tài khoản Admin mặc định
- Email: `admin@hthree.com`
- Để thêm quyền admin cho user khác, cập nhật field `role = 'admin'` trong database

## 🎯 Các chức năng chính

### 1. Dashboard (Tổng quan)
- **Thống kê tổng quan:**
  - Tổng số đơn hàng
  - Tổng doanh thu
  - Số lượng khách hàng
  - Đơn hàng chờ xử lý

### 2. Quản lý Đơn hàng
- Xem danh sách tất cả đơn hàng
- Lọc theo trạng thái: Tất cả / Chờ xử lý / Đã thanh toán / Hoàn thành / Đã hủy
- Xem chi tiết đơn hàng
- Cập nhật trạng thái đơn hàng
- Thông tin hiển thị:
  - Mã đơn hàng
  - Thông tin khách hàng
  - Tổng tiền
  - Trạng thái thanh toán
  - Trạng thái đơn hàng
  - Ngày tạo

### 3. Quản lý Khách hàng
- Xem danh sách khách hàng
- Thông tin hiển thị:
  - ID
  - Email
  - Tên
  - Gói hiện tại
  - Ngày tham gia
- Xem chi tiết từng khách hàng

### 4. Quản lý Gói dịch vụ
- Xem tất cả gói dịch vụ (Free, Basic, Premium, VIP)
- Thêm gói mới
- Chỉnh sửa gói
- Bật/Tắt gói
- Thông tin hiển thị:
  - Tên gói
  - Giá
  - Chất lượng video
  - Số thiết bị
  - Trạng thái

### 5. Quản lý Mã giảm giá
- Xem danh sách mã giảm giá
- Thêm mã mới
- Chỉnh sửa mã
- Bật/Tắt mã
- Thông tin hiển thị:
  - Mã code
  - Mô tả
  - Giá trị giảm (% hoặc số tiền)
  - Số lần đã dùng / Giới hạn
  - Hạn sử dụng
  - Trạng thái

## 🛠️ Cấu trúc File

```
src/
├── pages/
│   └── Admin.vue          # Trang Admin Panel chính
├── router/
│   └── index.js           # Route /admin
└── components/
    └── NetflixNavbar.vue  # Navbar có link Admin
```

## 🎨 Giao diện

- **Màu chủ đạo:** Đen, Xám, Đỏ, Vàng
- **Sidebar:** Menu điều hướng các chức năng
- **Header:** Logo, thông tin admin, nút đăng xuất
- **Main Content:** Hiển thị nội dung theo tab được chọn

## 📊 API Backend

Admin Panel sử dụng các API sau:
- `GET /api/orders.php` - Lấy danh sách đơn hàng
- `PUT /api/orders.php` - Cập nhật đơn hàng
- `GET /api/users.php` - Lấy danh sách khách hàng
- `GET /api/plans.php` - Lấy danh sách gói
- `PUT /api/plans.php` - Cập nhật gói
- `GET /api/coupons.php` - Lấy danh sách mã giảm giá
- `PUT /api/coupons.php` - Cập nhật mã giảm giá

## 🚀 Tính năng sẽ bổ sung

- [ ] Biểu đồ thống kê doanh thu
- [ ] Export báo cáo Excel/PDF
- [ ] Gửi email thông báo đơn hàng
- [ ] Quản lý bình luận
- [ ] Quản lý banner/quảng cáo
- [ ] Phân quyền admin chi tiết

## 💡 Lưu ý

- Chỉ user có email `admin@hthree.com` hoặc `role = 'admin'` mới thấy icon Admin
- Cần đảm bảo backend API đang chạy
- Database phải có đầy đủ các bảng: orders, users, subscription_plans, coupons

## 🐛 Troubleshooting

**Không thấy icon Admin?**
- Kiểm tra email đăng nhập có phải `admin@hthree.com`
- Hoặc cập nhật role trong database

**Không load được dữ liệu?**
- Kiểm tra backend API đang chạy
- Kiểm tra CORS headers
- Xem console log để debug

**Lỗi 404 khi truy cập /admin?**
- Chạy lại dev server: `npm run dev`
- Kiểm tra file `src/router/index.js` đã có route `/admin`
