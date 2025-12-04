# 🎨 ADMIN PANEL - HƯỚNG DẪN

## ✨ **TÍNH NĂNG:**

### 1️⃣ **Dashboard**
- 📊 Statistics cards với animations
- 📈 Biểu đồ doanh thu
- 📋 Đơn hàng gần đây
- 🏆 Top gói phổ biến

### 2️⃣ **Quản lý Đơn hàng**
- 🔍 Tìm kiếm đơn hàng
- 🎯 Lọc theo trạng thái
- ✅ Xác nhận thanh toán
- 👁️ Xem chi tiết

### 3️⃣ **Notification Modal**
- ✅ Success notifications
- ❌ Error notifications
- ⚠️ Warning notifications
- ℹ️ Info notifications
- ⏱️ Auto-close sau 5s
- 📊 Progress bar

---

## 🚀 **SETUP:**

### Bước 1: Thêm route

Mở `src/router/index.js` và thêm:

```javascript
import Admin from '@/pages/Admin.vue';

const routes = [
  // ... existing routes
  {
    path: '/admin',
    name: 'Admin',
    component: Admin,
    meta: { requiresAuth: true, requiresAdmin: true }
  }
];
```

### Bước 2: Tạo icons components

Tạo file `src/components/icons/AdminIcons.vue`:

```vue
<script setup>
// Dashboard Icon
export const DashboardIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
  `
};

// Orders Icon
export const OrdersIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
  `
};

// Users Icon
export const UsersIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
  `
};

// Plans Icon
export const PlansIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
    </svg>
  `
};

// Coupons Icon
export const CouponsIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
    </svg>
  `
};

// Money Icon
export const MoneyIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
  `
};

// Cart Icon
export const CartIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
  `
};

// Clock Icon
export const ClockIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
  `
};
</script>
```

### Bước 3: Import icons vào Admin.vue

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

## 🎯 **SỬ DỤNG NOTIFICATION MODAL:**

### Trong component:

```vue
<script setup>
import { ref } from 'vue';

const showNotification = ref(false);
const notification = ref({
  type: 'success', // success, error, warning, info
  title: 'Thành công!',
  message: 'Đơn hàng đã được xác nhận'
});

// Show notification
const showSuccess = () => {
  notification.value = {
    type: 'success',
    title: 'Thành công!',
    message: 'Thao tác đã được thực hiện'
  };
  showNotification.value = true;
};

const showError = () => {
  notification.value = {
    type: 'error',
    title: 'Lỗi!',
    message: 'Có lỗi xảy ra, vui lòng thử lại'
  };
  showNotification.value = true;
};
</script>

<template>
  <NotificationModal
    v-model="showNotification"
    :type="notification.type"
    :title="notification.title"
    :message="notification.message"
  />
</template>
```

---

## 🎨 **CUSTOMIZATION:**

### Thay đổi màu sắc:

Trong `Admin.vue`, tìm `stats` array và thay đổi:

```javascript
{
  iconBg: 'bg-gradient-to-br from-green-600 to-green-700', // Icon background
  changeBg: 'bg-green-500/20 text-green-400', // Change badge
  gradient: 'from-green-600 to-green-700' // Hover gradient
}
```

### Thay đổi thời gian auto-close notification:

```vue
<NotificationModal
  v-model="showNotification"
  :duration="3000"
/>
```

---

## 📊 **TÍCH HỢP API:**

### Fetch orders từ backend:

```javascript
import axios from 'axios';

const fetchOrders = async () => {
  try {
    const response = await axios.get('/api/orders.php');
    recentOrders.value = response.data.data;
  } catch (error) {
    notification.value = {
      type: 'error',
      title: 'Lỗi!',
      message: 'Không thể tải danh sách đơn hàng'
    };
    showNotification.value = true;
  }
};
```

### Xác nhận thanh toán:

```javascript
const confirmPayment = async (order) => {
  try {
    await axios.post(`/api/orders.php?id=${order.id}`, {
      action: 'confirm_payment'
    });
    
    notification.value = {
      type: 'success',
      title: 'Thành công!',
      message: `Đã xác nhận thanh toán cho đơn ${order.order_code}`
    };
    showNotification.value = true;
    
    // Reload orders
    fetchOrders();
  } catch (error) {
    notification.value = {
      type: 'error',
      title: 'Lỗi!',
      message: 'Không thể xác nhận thanh toán'
    };
    showNotification.value = true;
  }
};
```

---

## 🔒 **BẢO MẬT:**

### Middleware kiểm tra admin:

Tạo file `src/middleware/adminGuard.js`:

```javascript
export const adminGuard = (to, from, next) => {
  const user = JSON.parse(localStorage.getItem('user'));
  
  if (!user || user.role !== 'admin') {
    next('/');
    return;
  }
  
  next();
};
```

Thêm vào router:

```javascript
{
  path: '/admin',
  component: Admin,
  beforeEnter: adminGuard
}
```

---

## ✅ **CHECKLIST:**

- [ ] Đã tạo file `Admin.vue`
- [ ] Đã tạo file `NotificationModal.vue`
- [ ] Đã tạo file icons
- [ ] Đã thêm route `/admin`
- [ ] Đã thêm middleware bảo mật
- [ ] Đã test notification modal
- [ ] Đã tích hợp API

---

## 🎉 **KẾT QUẢ:**

Trang Admin hiện đại với:
- ✅ Dashboard với statistics
- ✅ Quản lý orders
- ✅ Notification modal đẹp
- ✅ Smooth animations
- ✅ Responsive design
- ✅ Easy to customize

**Sẵn sàng sử dụng!** 🚀
