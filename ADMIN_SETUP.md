# 🔧 SETUP ADMIN PANEL

## ✅ **ĐÃ TẠO:**

### Backend APIs:
1. ✅ `backend/api/admin/statistics.php` - Lấy thống kê
2. ✅ `backend/api/admin/orders.php` - Quản lý đơn hàng

### Frontend:
1. ✅ `src/pages/Admin.vue` - Trang admin
2. ✅ `src/components/NotificationModal.vue` - Modal thông báo

---

## 🚀 **BƯỚC SETUP:**

### 1. Test Backend APIs

#### Test Statistics API:
```
http://localhost/HTHREE_film/backend/api/admin/statistics.php
```

**Kết quả mong đợi:**
```json
{
  "success": true,
  "data": {
    "total_revenue": 1500000,
    "total_revenue_formatted": "1,500,000 đ",
    "revenue_change": 12.5,
    "total_orders": 25,
    "total_users": 150,
    "pending_orders": 5,
    "top_plans": [...]
  }
}
```

#### Test Orders API:
```
http://localhost/HTHREE_film/backend/api/admin/orders.php
```

### 2. Thêm Route

Mở `src/router/index.js`:

```javascript
import Admin from '@/pages/Admin.vue';

const routes = [
  // ... existing routes
  {
    path: '/admin',
    name: 'Admin',
    component: Admin,
    meta: { requiresAuth: true }
  }
];
```

### 3. Tạo Icons Components

Tạo file `src/components/icons/AdminIcons.vue`:

```vue
<script>
export const DashboardIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'
};

export const OrdersIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'
};

export const UsersIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'
};

export const PlansIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>'
};

export const CouponsIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>'
};

export const MoneyIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
};

export const CartIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
};

export const ClockIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
};
</script>
```

### 4. Import Icons vào Admin.vue

Thêm vào đầu `<script setup>` trong `Admin.vue`:

```javascript
import {
  DashboardIcon,
  OrdersIcon,
  UsersIcon,
  PlansIcon,
  CouponsIcon,
  MoneyIcon,
  CartIcon,
  ClockIcon
} from '@/components/icons/AdminIcons.vue';
```

---

## 🧪 **TEST:**

### 1. Test Backend APIs
```bash
# Statistics
http://localhost/HTHREE_film/backend/api/admin/statistics.php

# Orders
http://localhost/HTHREE_film/backend/api/admin/orders.php
```

### 2. Vào trang Admin
```
http://localhost:5174/admin
```

### 3. Kiểm tra:
- ✅ Dashboard hiển thị số liệu thật
- ✅ Đơn hàng load từ database
- ✅ Top plans hiển thị đúng
- ✅ Notification modal hoạt động

---

## 📊 **DỮ LIỆU HIỂN THỊ:**

### Dashboard Stats:
1. **Tổng doanh thu** - Tổng tiền từ orders đã thanh toán
2. **Đơn hàng** - Tổng số orders
3. **Người dùng** - Tổng số users
4. **Chờ xử lý** - Orders có status = pending

### Top Plans:
- Gói nào bán nhiều nhất
- % so với gói bán chạy nhất
- Progress bar animation

### Recent Orders:
- 5 đơn hàng gần nhất
- Thông tin khách hàng
- Trạng thái thanh toán
- Nút xem chi tiết / xác nhận

---

## 🎯 **TÍNH NĂNG ADMIN:**

### Xác nhận thanh toán:
1. Click nút ✅ ở đơn hàng pending
2. Confirm dialog
3. Update status → paid
4. Notification hiện lên
5. Reload data

### Tìm kiếm đơn hàng:
- Tìm theo mã đơn
- Tìm theo tên khách hàng
- Tìm theo email

### Lọc đơn hàng:
- Tất cả
- Chờ xử lý
- Đã thanh toán
- Thất bại

---

## 🎉 **KẾT QUẢ:**

Admin panel với dữ liệu thật từ database:
- ✅ Real-time statistics
- ✅ Order management
- ✅ Beautiful notifications
- ✅ Smooth animations
- ✅ Easy to use

**Sẵn sàng quản lý!** 🚀
