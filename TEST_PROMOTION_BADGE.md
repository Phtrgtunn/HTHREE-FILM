# 🎄 Hướng dẫn Test Promotion Badge

## Vấn đề đã sửa

Backend API `backend/api/admin/plans.php` thiếu 2 fields:

- `promotion_badge`
- `promotion_text`

Đã cập nhật để hỗ trợ đầy đủ các field này.

## Các thay đổi

### 1. Backend (`backend/api/admin/plans.php`)

- ✅ Thêm `promotion_badge` và `promotion_text` vào function `createPlan()`
- ✅ Thêm `promotion_badge` và `promotion_text` vào function `updatePlan()`
- ✅ Sử dụng `array_key_exists()` thay vì `isset()` để xử lý giá trị `null`
- ✅ Convert empty string thành `null` cho text fields
- ✅ Thêm debug logging để track API calls

### 2. Frontend (`src/pages/Admin.vue`)

- ✅ Đã có sẵn fields trong form
- ✅ Đã có logic convert empty string → null
- ✅ Đã có console.log để debug

## Cách test

### Option 1: Test bằng Admin Panel (Khuyến nghị)

1. Mở trình duyệt và vào: http://localhost:5174/admin
2. Chuyển sang tab "Gói dịch vụ"
3. Click nút "Edit" (icon bút chì) ở gói Basic
4. Thêm badge ưu đãi:
   - Badge Ưu Đãi: `🎄 Giáng Sinh`
   - Text Ưu Đãi: `Giảm 30% - Chỉ hôm nay!`
5. Click "Cập nhật"
6. Mở Console (F12) để xem logs:
   ```
   🚀 Sending to API: PUT http://localhost/HTHREE_film/backend/api/admin/plans.php
   📦 Data: {...}
   ✅ Response: {...}
   ```
7. Refresh trang để xem badge có hiển thị không
8. Thử xóa badge bằng cách xóa hết text trong 2 ô input
9. Click "Cập nhật" lại
10. Refresh để verify badge đã bị xóa

### Option 2: Test bằng HTML Test Page

1. Mở file: `test-promotion-update.html` trong trình duyệt
2. Click "Fetch Plans" để xem tất cả gói
3. Nhập Plan ID (ví dụ: 1 cho Basic)
4. Nhập Badge và Text
5. Click "Update Plan"
6. Xem kết quả trong console
7. Click "Fetch Plans" lại để verify

### Option 3: Test bằng SQL

1. Mở phpMyAdmin hoặc MySQL client
2. Chạy file: `verify_promotion_columns.sql`
3. Xem cấu trúc bảng và dữ liệu hiện tại
4. Uncomment các câu lệnh UPDATE để test trực tiếp

## Kiểm tra logs

### Frontend logs (Browser Console)

```javascript
🚀 Sending to API: PUT http://localhost/HTHREE_film/backend/api/admin/plans.php
📦 Data: {
  id: 1,
  promotion_badge: "🎄 Giáng Sinh",
  promotion_text: "Giảm 30%",
  ...
}
✅ Response: { success: true, message: "..." }
```

### Backend logs (PHP error log)

Kiểm tra file: `D:\Ampps\php\logs\php_error.log`

```
UPDATE PLAN - Received data: {"id":1,"promotion_badge":"🎄 Giáng Sinh",...}
UPDATE PLAN - SQL: UPDATE subscription_plans SET promotion_badge = ?, promotion_text = ? WHERE id = ?
UPDATE PLAN - Params: ["🎄 Giáng Sinh","Giảm 30%",1]
```

## Verify kết quả

### 1. Kiểm tra trong Admin Panel

- Badge có hiển thị trong card của gói không?
- Khi edit lại, các field có giữ giá trị không?

### 2. Kiểm tra trong Pricing Page

- Mở: http://localhost:5174/pricing
- Badge có hiển thị trên card của gói không?
- Text ưu đãi có hiển thị dưới giá không?

### 3. Kiểm tra trong Database

```sql
SELECT id, name, promotion_badge, promotion_text
FROM subscription_plans
WHERE id = 1;
```

## Troubleshooting

### Nếu vẫn không lưu được:

1. **Kiểm tra Console logs**

   - Có lỗi CORS không?
   - Response có success: true không?
   - Data có được gửi đúng không?

2. **Kiểm tra Network tab**

   - Request có được gửi không?
   - Status code là gì? (200 = OK)
   - Response body là gì?

3. **Kiểm tra PHP error log**

   - File: `D:\Ampps\php\logs\php_error.log`
   - Có lỗi SQL không?
   - Data có được nhận đúng không?

4. **Kiểm tra Database**

   - Columns `promotion_badge` và `promotion_text` có tồn tại không?
   - Kiểu dữ liệu có đúng không? (VARCHAR hoặc TEXT, cho phép NULL)

5. **Clear cache**
   - Hard refresh browser: Ctrl + Shift + R
   - Clear localStorage: F12 → Application → Local Storage → Clear
   - Restart dev server nếu cần

## Expected Result

Sau khi test thành công:

- ✅ Có thể thêm badge ưu đãi vào bất kỳ gói nào
- ✅ Có thể xóa badge bằng cách xóa hết text
- ✅ Badge hiển thị trong Admin Panel
- ✅ Badge hiển thị trong Pricing Page
- ✅ Data được lưu vào database
- ✅ Refresh trang vẫn giữ nguyên badge
