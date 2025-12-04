# 📊 HƯỚNG DẪN CẬP NHẬT DATABASE CHO ADMIN PANEL

## 🎯 Mục đích
File này hướng dẫn cập nhật database để hỗ trợ đầy đủ các tính năng của Admin Panel.

## 📋 Các cập nhật chính

### 1. **Bảng Orders**
- ✅ Thêm cột `subtotal` - Tổng tiền trước giảm giá
- ✅ Thêm cột `discount` - Số tiền giảm giá
- ✅ Thêm cột `admin_note` - Ghi chú của admin
- ✅ Thêm cột `paid_at` - Thời điểm thanh toán
- ✅ Thêm cột `completed_at` - Thời điểm hoàn thành
- ✅ Thêm cột `cancelled_at` - Thời điểm hủy

### 2. **Bảng Order Items**
- ✅ Thêm cột `duration_months` - Số tháng sử dụng
- ✅ Thêm cột `subtotal` - Tổng tiền từng item

### 3. **Stored Procedures**
- ✅ `sp_get_admin_statistics()` - Lấy thống kê dashboard
- ✅ `sp_get_orders_list()` - Lấy danh sách đơn hàng có filter
- ✅ `sp_confirm_order_payment()` - Xác nhận thanh toán
- ✅ `sp_get_order_details()` - Lấy chi tiết đơn hàng

### 4. **Views**
- ✅ `v_daily_order_stats` - Thống kê đơn hàng theo ngày
- ✅ `v_user_statistics` - Thống kê người dùng
- ✅ `v_plan_statistics` - Thống kê gói subscription

### 5. **Triggers**
- ✅ `tr_update_order_paid_at` - Tự động cập nhật timestamps

### 6. **Indexes**
- ✅ Tối ưu performance cho queries thường dùng

## 🚀 CÁCH CẬP NHẬT

### Phương pháp 1: Sử dụng phpMyAdmin (Khuyến nghị)

1. **Mở phpMyAdmin**
   - Truy cập: `http://localhost/phpmyadmin`
   - Hoặc: `http://localhost:8080/phpmyadmin`

2. **Chọn database**
   - Click vào database `hthree_film` ở sidebar bên trái

3. **Import file SQL**
   - Click tab **SQL** ở menu trên
   - Copy toàn bộ nội dung file `update_database_for_admin.sql`
   - Paste vào ô SQL query
   - Click nút **Go** (hoặc **Thực hiện**)

4. **Kiểm tra kết quả**
   - Nếu thành công, bạn sẽ thấy thông báo màu xanh
   - Kiểm tra các bảng, procedures, views đã được tạo

### Phương pháp 2: Sử dụng MySQL Command Line

```bash
# Mở Command Prompt hoặc Terminal
# Di chuyển đến thư mục chứa file SQL
cd path/to/your/project

# Chạy lệnh import
mysql -u root -p hthree_film < update_database_for_admin.sql

# Nhập password khi được yêu cầu
```

### Phương pháp 3: Sử dụng AMPPS MySQL

```bash
# Mở AMPPS MySQL Command Line
# Hoặc sử dụng MySQL Workbench

# Kết nối đến database
USE hthree_film;

# Copy paste từng phần của file SQL và chạy
```

## ✅ KIỂM TRA SAU KHI CẬP NHẬT

### 1. Kiểm tra Stored Procedures
```sql
-- Xem danh sách procedures
SHOW PROCEDURE STATUS WHERE Db = 'hthree_film';

-- Test procedure thống kê
CALL sp_get_admin_statistics();

-- Test procedure lấy đơn hàng
CALL sp_get_orders_list(NULL, NULL, 10);
```

### 2. Kiểm tra Views
```sql
-- Xem danh sách views
SHOW FULL TABLES WHERE Table_type = 'VIEW';

-- Test view thống kê ngày
SELECT * FROM v_daily_order_stats LIMIT 10;

-- Test view thống kê user
SELECT * FROM v_user_statistics LIMIT 10;

-- Test view thống kê plans
SELECT * FROM v_plan_statistics;
```

### 3. Kiểm tra Triggers
```sql
-- Xem danh sách triggers
SHOW TRIGGERS WHERE `Table` = 'orders';

-- Test trigger bằng cách update một order
UPDATE orders 
SET payment_status = 'paid' 
WHERE id = 1 AND payment_status = 'pending';

-- Kiểm tra paid_at đã được set chưa
SELECT id, payment_status, paid_at FROM orders WHERE id = 1;
```

### 4. Kiểm tra Indexes
```sql
-- Xem indexes của bảng orders
SHOW INDEX FROM orders;

-- Xem indexes của bảng order_items
SHOW INDEX FROM order_items;

-- Xem indexes của bảng user_subscriptions
SHOW INDEX FROM user_subscriptions;
```

## 🔧 XỬ LÝ LỖI

### Lỗi: "Table already exists"
```sql
-- Bỏ qua lỗi này, có nghĩa là bảng đã tồn tại
-- Script sử dụng IF NOT EXISTS nên an toàn
```

### Lỗi: "Column already exists"
```sql
-- Bỏ qua lỗi này, cột đã tồn tại
-- Script sử dụng ADD COLUMN IF NOT EXISTS
```

### Lỗi: "Procedure already exists"
```sql
-- Script đã có DROP PROCEDURE IF EXISTS
-- Nếu vẫn lỗi, chạy thủ công:
DROP PROCEDURE IF EXISTS sp_get_admin_statistics;
DROP PROCEDURE IF EXISTS sp_get_orders_list;
DROP PROCEDURE IF EXISTS sp_confirm_order_payment;
DROP PROCEDURE IF EXISTS sp_get_order_details;

-- Sau đó chạy lại script
```

### Lỗi: "View already exists"
```sql
-- Script đã có DROP VIEW IF EXISTS
-- Nếu vẫn lỗi, chạy thủ công:
DROP VIEW IF EXISTS v_daily_order_stats;
DROP VIEW IF EXISTS v_user_statistics;
DROP VIEW IF EXISTS v_plan_statistics;

-- Sau đó chạy lại script
```

## 📊 DỮ LIỆU SAU KHI CẬP NHẬT

### Dữ liệu được giữ nguyên
- ✅ Tất cả đơn hàng hiện có
- ✅ Tất cả người dùng
- ✅ Tất cả subscriptions
- ✅ Tất cả order items

### Dữ liệu được cập nhật tự động
- ✅ `paid_at` cho các đơn đã thanh toán
- ✅ `completed_at` cho các đơn đã hoàn thành
- ✅ `subtotal` cho orders và order_items

## 🎨 TÍNH NĂNG MỚI SAU KHI CẬP NHẬT

### 1. Admin Dashboard
- Thống kê realtime
- Doanh thu theo tháng
- Số đơn hàng pending
- Top gói bán chạy

### 2. Quản lý đơn hàng
- Xem danh sách đơn hàng
- Filter theo trạng thái
- Tìm kiếm đơn hàng
- Xác nhận thanh toán
- Xem chi tiết đơn hàng

### 3. Auto-refresh
- Dữ liệu tự động cập nhật mỗi 30 giây
- Nút refresh thủ công
- Loading indicator

### 4. Báo cáo
- Thống kê theo ngày
- Thống kê người dùng
- Thống kê gói subscription

## 🔐 BẢO MẬT

### Backup trước khi cập nhật
```sql
-- Export database hiện tại
mysqldump -u root -p hthree_film > backup_before_update.sql

-- Hoặc sử dụng phpMyAdmin:
-- 1. Chọn database hthree_film
-- 2. Click tab Export
-- 3. Click Go để download
```

### Restore nếu có vấn đề
```sql
-- Import backup
mysql -u root -p hthree_film < backup_before_update.sql

-- Hoặc sử dụng phpMyAdmin:
-- 1. Chọn database hthree_film
-- 2. Click tab Import
-- 3. Choose file và upload backup
```

## 📝 GHI CHÚ

- ⚠️ **Quan trọng**: Backup database trước khi cập nhật
- ✅ Script an toàn, sử dụng IF NOT EXISTS
- ✅ Không xóa dữ liệu hiện có
- ✅ Tương thích với MySQL 5.7+
- ✅ Đã test trên MySQL 8.0

## 🆘 HỖ TRỢ

Nếu gặp vấn đề:
1. Kiểm tra phiên bản MySQL: `SELECT VERSION();`
2. Kiểm tra quyền user: `SHOW GRANTS;`
3. Xem log lỗi trong phpMyAdmin
4. Chạy từng phần của script để tìm lỗi

## ✨ KẾT QUẢ MONG ĐỢI

Sau khi cập nhật thành công:
- ✅ Admin panel hoạt động đầy đủ
- ✅ Thống kê hiển thị chính xác
- ✅ Xác nhận thanh toán hoạt động
- ✅ Auto-refresh cập nhật dữ liệu
- ✅ Performance được tối ưu

## 🎉 HOÀN TẤT!

Database đã sẵn sàng cho Admin Panel! 🚀
