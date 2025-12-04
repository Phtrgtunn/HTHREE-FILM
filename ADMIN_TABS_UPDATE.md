# 📋 CẬP NHẬT ADMIN PANEL - THÊM 3 TAB MỚI

## ✅ Đã hoàn thành:
1. ✅ API Backend cho Users, Plans, Coupons
2. ✅ ConfirmModal component
3. ✅ Dashboard tab với thống kê
4. ✅ Orders tab với danh sách đơn hàng
5. ✅ Order Detail Modal

## ⏳ Cần thêm vào Admin.vue:

### 1. Import ConfirmModal

```vue
<script setup>
import ConfirmModal from '@/components/ConfirmModal.vue';
// ... existing imports
</script>
```

### 2. Thêm ConfirmModal vào template (sau NotificationModal)

```vue
<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900">
    <!-- Notification Modal -->
    <NotificationModal ... />
    
    <!-- Confirm Modal -->
    <ConfirmModal
      v-model="showConfirm"
      :title="confirmData.title"
      :message="confirmData.message"
      :type="confirmData.type"
      :confirm-text="confirmData.confirmText"
      @confirm="confirmData.onConfirm"
    />
    
    <!-- Rest of template -->
  </div>
</template>
```

### 3. Thêm reactive variables

```javascript
const showConfirm = ref(false);
const confirmData = ref({
  title: '',
  message: '',
  type: 'warning',
  confirmText: 'Xác nhận',
  onConfirm: () => {}
});

// Data cho các tab mới
const users = ref([]);
const plans = ref([]);
const coupons = ref([]);
```

### 4. Thêm methods fetch data

```javascript
// Fetch users
const fetchUsers = async () => {
  try {
    const response = await axios.get(`${API_URL}/admin/users.php?limit=100`);
    if (response.data.success) {
      users.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching users:', error);
  }
};

// Fetch plans
const fetchPlans = async () => {
  try {
    const response = await axios.get(`${API_URL}/admin/plans.php`);
    if (response.data.success) {
      plans.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching plans:', error);
  }
};

// Fetch coupons
const fetchCoupons = async () => {
  try {
    const response = await axios.get(`${API_URL}/admin/coupons.php`);
    if (response.data.success) {
      coupons.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching coupons:', error);
  }
};
```

### 5. Cập nhật onMounted

```javascript
onMounted(() => {
  fetchStatistics();
  fetchOrders();
  fetchUsers();
  fetchPlans();
  fetchCoupons();
  startAutoRefresh();
});
```

### 6. Thay thế confirm() browser bằng ConfirmModal

```javascript
// CŨ:
const confirmPayment = async (order) => {
  if (!confirm(`Xác nhận thanh toán cho đơn hàng ${order.order_code}?`)) return;
  // ...
};

// MỚI:
const confirmPayment = async (order) => {
  confirmData.value = {
    title: 'Xác nhận thanh toán',
    message: `Bạn có chắc chắn muốn xác nhận thanh toán cho đơn hàng ${order.order_code}?`,
    type: 'success',
    confirmText: 'Xác nhận',
    onConfirm: async () => {
      loading.value = true;
      try {
        const response = await axios.post(`${API_URL}/admin/orders.php`, {
          order_id: order.id
        });
        
        if (response.data.success) {
          notification.value = {
            type: 'success',
            title: 'Thành công!',
            message: response.data.message
          };
          showNotification.value = true;
          
          await Promise.all([
            fetchOrders(),
            fetchStatistics()
          ]);
        }
      } catch (error) {
        notification.value = {
          type: 'error',
          title: 'Lỗi!',
          message: error.response?.data?.message || 'Không thể xác nhận thanh toán'
        };
        showNotification.value = true;
      } finally {
        loading.value = false;
      }
    }
  };
  showConfirm.value = true;
};
```

## 🎨 Hoặc đơn giản hơn:

Mình đã chuẩn bị sẵn tất cả backend API. Bạn chỉ cần:

1. **Test xem Dashboard và Orders tab hoạt động chưa**
2. **Nếu OK**, mình sẽ tạo file Admin.vue mới hoàn chỉnh với:
   - ✅ 3 tab mới (Users, Plans, Coupons)
   - ✅ ConfirmModal thay thế browser confirm
   - ✅ CRUD operations cho từng tab
   - ✅ Search và filter
   - ✅ Beautiful UI

## 📝 Bạn muốn:

**Option 1**: Mình tạo file Admin.vue mới hoàn chỉnh (khuyến nghị)
**Option 2**: Bạn tự thêm từng phần theo hướng dẫn trên

Chọn option nào? 🚀
