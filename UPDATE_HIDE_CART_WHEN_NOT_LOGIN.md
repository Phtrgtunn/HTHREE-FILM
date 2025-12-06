# 🔒 Update: Ẩn Giỏ Hàng Khi Chưa Đăng Nhập

## 📋 Thay Đổi

### Trước

- Giỏ hàng hiển thị cho tất cả (kể cả chưa login)
- Nút "Nâng cấp VIP" hiển thị cho tất cả (kể cả chưa login)

### Sau

- ✅ Giỏ hàng **CHỈ hiển thị** khi:
  - Đã đăng nhập
  - KHÔNG phải admin
- ✅ Nút "Nâng cấp VIP" **CHỈ hiển thị** khi:
  - Đã đăng nhập
  - KHÔNG phải admin

## 🎯 Logic Hiển Thị

### Navbar - Giỏ Hàng

```vue
<!-- Trước -->
<router-link v-if="!isAdmin" to="/cart">
  <!-- Cart icon -->
</router-link>

<!-- Sau -->
<router-link v-if="user && !isAdmin" to="/cart">
  <!-- Cart icon -->
</router-link>
```

### Navbar - Nút VIP

```vue
<!-- Trước -->
<router-link v-if="!isAdmin" to="/pricing">
  Nâng cấp VIP
</router-link>

<!-- Sau -->
<router-link v-if="user && !isAdmin" to="/pricing">
  Nâng cấp VIP
</router-link>
```

## 📊 Ma Trận Hiển Thị

| Trạng thái  | Giỏ hàng | Nút VIP | Admin Panel |
| ----------- | -------- | ------- | ----------- |
| Chưa login  | ❌ Ẩn    | ❌ Ẩn   | ❌ Ẩn       |
| User login  | ✅ Hiện  | ✅ Hiện | ❌ Ẩn       |
| Admin login | ❌ Ẩn    | ❌ Ẩn   | ✅ Hiện     |

## 🎨 Giao Diện

### Chưa Đăng Nhập

```
┌─────────────────────────────────────────┐
│ [HTHREE] [Search...] [Menu]  [User ▼]  │
│                               ↑ Login   │
└─────────────────────────────────────────┘
```

### User Đã Login

```
┌──────────────────────────────────────────────────────────┐
│ [HTHREE] [Search...] [Menu]  [⭐ VIP]  [🛒 2]  [User ▼] │
│                                ↑ Vàng   ↑ Cart          │
└──────────────────────────────────────────────────────────┘
```

### Admin Đã Login

```
┌─────────────────────────────────────────────────────┐
│ [HTHREE] [Search...] [Menu]  [👑 Admin]  [User ▼] │
│                                ↑ Tím               │
└─────────────────────────────────────────────────────┘
```

## 📁 Files Thay Đổi

```
src/components/NetflixNavbar.vue
  - Line ~170: Thêm điều kiện v-if="user && !isAdmin" cho Cart
  - Line ~160: Thêm điều kiện v-if="user && !isAdmin" cho Pricing
```

## ✅ Test

### Test Chưa Login

- [ ] 1. Logout (nếu đang login)
- [ ] 2. Reload trang
- [ ] 3. **Kiểm tra**: KHÔNG thấy icon giỏ hàng
- [ ] 4. **Kiểm tra**: KHÔNG thấy nút "Nâng cấp VIP"
- [ ] 5. **Kiểm tra**: Chỉ thấy nút "User" với icon mặc định

### Test User Login

- [ ] 1. Login: user@example.com
- [ ] 2. **Kiểm tra**: THẤY icon giỏ hàng
- [ ] 3. **Kiểm tra**: THẤY nút "Nâng cấp VIP"
- [ ] 4. Thêm gói vào giỏ
- [ ] 5. **Kiểm tra**: Badge số lượng hiển thị đúng

### Test Admin Login

- [ ] 1. Login: admin@hthree.com
- [ ] 2. **Kiểm tra**: KHÔNG thấy icon giỏ hàng
- [ ] 3. **Kiểm tra**: KHÔNG thấy nút "Nâng cấp VIP"
- [ ] 4. **Kiểm tra**: THẤY nút "Admin Panel"

## 🔄 Luồng Hoạt Động

```
User vào trang
    ↓
Kiểm tra đăng nhập?
    ├─ Chưa → Ẩn giỏ hàng & nút VIP
    └─ Rồi → Kiểm tra role
              ├─ Admin → Ẩn giỏ hàng & nút VIP, hiện Admin Panel
              └─ User → Hiện giỏ hàng & nút VIP
```

## 💡 Lý Do

### Tại Sao Ẩn Khi Chưa Login?

1. **UX tốt hơn**: Không hiển thị tính năng không dùng được
2. **Logic rõ ràng**: Phải login mới mua được
3. **Khuyến khích login**: User thấy cần login để mua
4. **Giảm confusion**: Không click vào giỏ hàng rồi bị yêu cầu login

### Tại Sao Ẩn Với Admin?

1. **Admin không cần mua**: Xem phim miễn phí
2. **Tránh nhầm lẫn**: Admin không nên mua gói
3. **UI sạch hơn**: Chỉ hiện tính năng cần thiết

## 🐛 Edge Cases

### User Logout

- Khi logout → Giỏ hàng tự động ẩn
- Cart store vẫn giữ data (localStorage)
- Login lại → Giỏ hàng hiện lại với data cũ

### Admin Thử Truy Cập Cart

- URL: `/cart` → Route guard chặn → Redirect `/home`
- Console log: "⛔ Admin không thể truy cập trang: Cart"

### User Chưa Login Thử Truy Cập Cart

- URL: `/cart` → Có thể vào (không bị chặn)
- Nhưng không có nút để vào từ navbar
- Nên thêm check trong Cart page để redirect về login

## 📝 Ghi Chú

### Cân Nhắc Thêm

Có thể thêm check trong `Cart.vue`:

```vue
<script setup>
import { onMounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

onMounted(() => {
  const user = localStorage.getItem("user");
  if (!user) {
    // Chưa login → Redirect về trang chủ hoặc login
    router.push("/home");
  }
});
</script>
```

### Tương Tự Cho Checkout

Cũng nên ẩn hoặc check trong `Checkout.vue`

---

**Cập nhật**: 04/12/2024  
**Version**: 2.0.1  
**Status**: ✅ Done
