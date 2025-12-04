# 🔧 FIX LỖI CORS

## ❌ **LỖI:**
```
Access to XMLHttpRequest has been blocked by CORS policy
Network Error
```

## 🔍 **NGUYÊN NHÂN:**
1. Backend không chạy
2. URL backend sai
3. CORS headers thiếu

---

## ✅ **GIẢI PHÁP:**

### **Bước 1: Kiểm tra backend có chạy không**

Mở trình duyệt và truy cập:
```
http://localhost/HTHREE_film/backend/api/test.php
```

**Kết quả mong đợi:**
```json
{
  "success": true,
  "message": "Backend is working! 🎉",
  "timestamp": "2025-01-XX XX:XX:XX"
}
```

**Nếu không mở được:**
- ❌ XAMPP/WAMP chưa start
- ❌ Folder không đúng vị trí
- ❌ Apache chưa chạy

---

### **Bước 2: Kiểm tra URL trong config**

Mở file `src/config/api.js`:

```javascript
export const API_CONFIG = {
  BACKEND_URL: 'http://localhost/HTHREE_film/backend/api'
  // ⚠️ Kiểm tra URL này có đúng không!
};
```

**Các URL phổ biến:**
- XAMPP: `http://localhost/HTHREE_film/backend/api`
- WAMP: `http://localhost/HTHREE_film/backend/api`
- Laragon: `http://hthree-film.test/backend/api`

---

### **Bước 3: Test từng API**

#### Test Plans API:
```
http://localhost/HTHREE_film/backend/api/plans.php?active_only=true
```

Kết quả: Danh sách gói subscription

#### Test Cart API:
```
http://localhost/HTHREE_film/backend/api/cart.php?user_id=1
```

Kết quả: Giỏ hàng của user

---

### **Bước 4: Fix CORS nếu vẫn lỗi**

Nếu backend chạy nhưng vẫn lỗi CORS, thêm vào **ĐẦU FILE** mỗi PHP:

```php
<?php
// Thêm vào đầu file
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ... rest of code
```

**Các file cần thêm:**
- ✅ `backend/api/plans.php`
- ✅ `backend/api/cart.php`
- ✅ `backend/api/orders.php`
- ✅ `backend/api/ecommerce.php`

---

### **Bước 5: Restart Apache**

1. Mở **XAMPP Control Panel**
2. Click **Stop** Apache
3. Click **Start** Apache
4. Test lại

---

## 🧪 **QUICK TEST:**

### Test 1: Backend có chạy không?
```bash
# Mở browser
http://localhost/HTHREE_film/backend/api/test.php
```

### Test 2: Plans API có hoạt động không?
```bash
http://localhost/HTHREE_film/backend/api/plans.php
```

### Test 3: Frontend có gọi đúng URL không?
```javascript
// Mở Console (F12) → Console tab
console.log(import.meta.env.VITE_API_URL);
```

---

## 📝 **CHECKLIST:**

- [ ] XAMPP/WAMP đang chạy
- [ ] Apache đang start
- [ ] MySQL đang start
- [ ] Folder `HTHREE_film` ở đúng vị trí (`htdocs` hoặc `www`)
- [ ] URL trong `api.js` đúng
- [ ] Test API trả về JSON (không phải HTML error)
- [ ] CORS headers đã thêm vào tất cả file PHP

---

## 🆘 **NẾU VẪN LỖI:**

### Lỗi 1: "Failed to load resource: net::ERR_FAILED"
**Nguyên nhân:** Backend không chạy
**Fix:** Start XAMPP/WAMP

### Lỗi 2: "404 Not Found"
**Nguyên nhân:** URL sai
**Fix:** Kiểm tra lại đường dẫn folder

### Lỗi 3: "500 Internal Server Error"
**Nguyên nhân:** Lỗi PHP code
**Fix:** Xem log lỗi trong `php_error.log`

### Lỗi 4: "CORS policy"
**Nguyên nhân:** Thiếu headers
**Fix:** Thêm CORS headers vào đầu file PHP

---

## 🎯 **GIẢI PHÁP NHANH:**

Copy đoạn này vào **ĐẦU MỖI FILE PHP** trong `backend/api/`:

```php
<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
```

Sau đó restart Apache và test lại! 🚀
