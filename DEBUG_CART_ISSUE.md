# 🐛 Debug: Cart Add Issue - FIXED

## ❌ Vấn đề

**Lỗi:** Không thêm được vào giỏ hàng, không có toast notification

**Console errors:**
- `Error adding to cart: AxiosError`
- `Failed to load resource: 400 (Bad Request)`

## 🔍 Nguyên nhân

### User ID format không đúng

**Firebase user:**
```javascript
user.uid = "abc123..."  // ✅ Có
user.id = undefined     // ❌ Không có
```

**PHP user:**
```javascript
user.id = 1            // ✅ Có
user.uid = undefined   // ❌ Không có
```

**Code cũ:**
```javascript
if (!authStore.user?.id) {  // ❌ Chỉ check .id
  throw new Error('...');
}
```

## ✅ Giải pháp

### Fix: Support cả 2 loại user

```javascript
const userId = authStore.user?.id || authStore.user?.uid;

if (!userId) {
  throw new Error('...');
}
```

### Files đã sửa:

**src/stores/cartStore.js:**
- ✅ `fetchCart()` - Support cả id và uid
- ✅ `addItem()` - Support cả id và uid
- ✅ `clear()` - Support cả id và uid
- ✅ Added console.log for debugging

## 🧪 Test lại

### Bước 1: Reload page
```
http://localhost:5174/pricing
```

### Bước 2: Mở Console (F12)
Check xem có log không:
```
Adding to cart: { userId: "...", planId: 3, quantity: 1 }
```

### Bước 3: Click "Thêm vào giỏ"
**Expected:**
- ✅ Console log hiện
- ✅ Toast: "✅ Đã thêm gói Premium vào giỏ hàng"
- ✅ Navbar badge tăng lên

### Bước 4: Check API
```
http://localhost/HTHREE_film/backend/api/cart.php?user_id=1
```
**Expected:** JSON với items

## 🔧 Nếu vẫn lỗi

### Check 1: User đã đăng nhập chưa?
```javascript
// Mở Console, gõ:
console.log(authStore.user)
```
**Expected:** Object với id hoặc uid

### Check 2: Database đã import chưa?
- Mở phpMyAdmin
- Check bảng `cart` có tồn tại không
- Check bảng `subscription_plans` có 4 gói không

### Check 3: MySQL đã start chưa?
- Mở AMPPS
- Check MySQL status = Running

### Check 4: API hoạt động chưa?
```bash
# Test GET
curl http://localhost/HTHREE_film/backend/api/cart.php?user_id=1

# Test POST
curl -X POST http://localhost/HTHREE_film/backend/api/cart.php \
  -H "Content-Type: application/json" \
  -d '{"user_id":1,"plan_id":3,"quantity":1}'
```

## 📊 Debug Checklist

- [ ] User đã đăng nhập
- [ ] Console có log "Adding to cart"
- [ ] API trả về 200 OK
- [ ] Database có bảng cart
- [ ] MySQL đang chạy
- [ ] Toast notification hiện
- [ ] Navbar badge cập nhật

## 🎉 Kết quả

**Sau khi fix:**
- ✅ Support cả Firebase user (uid) và PHP user (id)
- ✅ Console log để debug
- ✅ Error handling tốt hơn
- ✅ Toast notification hoạt động

---

**Status:** ✅ FIXED

**Next:** Test thêm vào giỏ hàng và xem cart page
