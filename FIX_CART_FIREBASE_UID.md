# 🔧 Fix: Cart với Firebase UID

## ❌ Vấn đề

**Firebase UID là string** (VD: "abc123xyz...")
**Backend expect integer** (VD: 1, 2, 3...)

→ `intval("abc123xyz")` = 0 → Lỗi!

## ✅ Giải pháp

### 1. Thêm cột `firebase_uid` vào bảng users

**Chạy SQL này trong phpMyAdmin:**

```sql
ALTER TABLE users 
ADD COLUMN firebase_uid VARCHAR(255) NULL AFTER email,
ADD INDEX idx_firebase_uid (firebase_uid);
```

**Cách chạy:**
1. Mở: http://localhost/phpmyadmin
2. Chọn database `hthree_film`
3. Click tab "SQL"
4. Paste SQL ở trên
5. Click "Go"

### 2. Backend tự động tạo user

**File đã sửa:** `backend/api/cart.php`

**Function mới:** `getUserId($input)`
- Nếu input là số → Return số đó
- Nếu input là string (Firebase UID):
  - Tìm user có `firebase_uid` = input
  - Nếu không tìm thấy → Tạo user mới tự động
  - Return numeric ID

### 3. Test

**Bước 1:** Chạy SQL ở trên

**Bước 2:** Reload trang
```
http://localhost:5174/pricing
```

**Bước 3:** Click "Thêm vào giỏ"

**Expected:**
- ✅ Console: "Adding to cart: { userId: 'abc123...', planId: 3, quantity: 1 }"
- ✅ Backend tự tạo user với firebase_uid
- ✅ Toast: "✅ Đã thêm gói Premium vào giỏ hàng"
- ✅ Navbar badge: "1"

## 🔍 Debug

### Check 1: Cột đã thêm chưa?
```sql
DESCRIBE users;
```
**Expected:** Thấy cột `firebase_uid`

### Check 2: User được tạo chưa?
```sql
SELECT id, username, email, firebase_uid FROM users;
```
**Expected:** Thấy user mới với firebase_uid

### Check 3: Cart có item chưa?
```sql
SELECT * FROM cart;
```
**Expected:** Thấy item với user_id tương ứng

## 📊 Flow

```
Frontend (Firebase UID)
  ↓
  "abc123xyz..."
  ↓
Backend getUserId()
  ↓
  Check: firebase_uid = "abc123xyz..."
  ↓
  Found? → Return numeric ID
  Not found? → Create user → Return new ID
  ↓
  Use numeric ID for cart
  ↓
  Success!
```

## 🎉 Kết quả

**Sau khi fix:**
- ✅ Support Firebase UID (string)
- ✅ Support PHP user ID (integer)
- ✅ Tự động tạo user nếu chưa có
- ✅ Cart hoạt động với cả 2 loại user

---

**Status:** ✅ READY TO TEST

**Next:** Chạy SQL → Reload → Test thêm vào giỏ
