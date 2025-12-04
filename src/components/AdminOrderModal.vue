<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="$emit('close')">
    <div class="bg-gray-800 rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto border border-gray-700">
      <!-- Header -->
      <div class="sticky top-0 bg-gray-800 border-b border-gray-700 px-6 py-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-white">Chi tiết đơn hàng #{{ order?.order_code }}</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-white transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-6">
        <!-- Customer Info -->
        <div class="bg-gray-900 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-white mb-3">Thông tin khách hàng</h3>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p class="text-gray-400">Tên:</p>
              <p class="text-white font-medium">{{ order?.customer_name }}</p>
            </div>
            <div>
              <p class="text-gray-400">Email:</p>
              <p class="text-white font-medium">{{ order?.customer_email }}</p>
            </div>
            <div>
              <p class="text-gray-400">Số điện thoại:</p>
              <p class="text-white font-medium">{{ order?.customer_phone || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-gray-400">Ngày đặt:</p>
              <p class="text-white font-medium">{{ formatDate(order?.created_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="bg-gray-900 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-white mb-3">Sản phẩm</h3>
          <div class="space-y-2">
            <div v-for="item in order?.items" :key="item.id" class="flex justify-between items-center py-2 border-b border-gray-700 last:border-0">
              <div>
                <p class="text-white font-medium">{{ item.plan_name }}</p>
                <p class="text-gray-400 text-sm">{{ item.duration_months }} tháng × {{ formatCurrency(item.plan_price) }}</p>
              </div>
              <p class="text-white font-semibold">{{ formatCurrency(item.total) }}</p>
            </div>
          </div>
        </div>

        <!-- Payment Info -->
        <div class="bg-gray-900 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-white mb-3">Thanh toán</h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-400">Tạm tính:</span>
              <span class="text-white">{{ formatCurrency(order?.subtotal) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Giảm giá:</span>
              <span class="text-green-400">-{{ formatCurrency(order?.discount) }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-700">
              <span class="text-white">Tổng cộng:</span>
              <span class="text-yellow-400">{{ formatCurrency(order?.total) }}</span>
            </div>
            <div class="flex justify-between pt-2">
              <span class="text-gray-400">Phương thức:</span>
              <span class="text-white">{{ getPaymentMethod(order?.payment_method) }}</span>
            </div>
          </div>
        </div>

        <!-- Status Update -->
        <div class="bg-gray-900 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-white mb-3">Cập nhật trạng thái</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-400 text-sm mb-2">Trạng thái thanh toán</label>
              <select v-model="localOrder.payment_status" class="w-full bg-gray-800 text-white rounded-lg px-4 py-2 border border-gray-700">
                <option value="pending">Chờ thanh toán</option>
                <option value="paid">Đã thanh toán</option>
                <option value="failed">Thất bại</option>
                <option value="refunded">Đã hoàn tiền</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-400 text-sm mb-2">Trạng thái đơn hàng</label>
              <select v-model="localOrder.status" class="w-full bg-gray-800 text-white rounded-lg px-4 py-2 border border-gray-700">
                <option value="pending">Chờ xử lý</option>
                <option value="processing">Đang xử lý</option>
                <option value="completed">Hoàn thành</option>
                <option value="cancelled">Đã hủy</option>
              </select>
            </div>
          </div>
          <div class="mt-4">
            <label class="block text-gray-400 text-sm mb-2">Ghi chú admin</label>
            <textarea v-model="localOrder.admin_note" rows="3" class="w-full bg-gray-800 text-white rounded-lg px-4 py-2 border border-gray-700" placeholder="Thêm ghi chú..."></textarea>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="sticky bottom-0 bg-gray-800 border-t border-gray-700 px-6 py-4 flex justify-end gap-3">
        <button @click="$emit('close')" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
          Đóng
        </button>
        <button @click="saveChanges" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
          Lưu thay đổi
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  show: Boolean,
  order: Object
});

const emit = defineEmits(['close', 'save']);

const localOrder = ref({
  payment_status: '',
  status: '',
  admin_note: ''
});

watch(() => props.order, (newOrder) => {
  if (newOrder) {
    localOrder.value = {
      payment_status: newOrder.payment_status,
      status: newOrder.status,
      admin_note: newOrder.admin_note || ''
    };
  }
}, { immediate: true });

const formatCurrency = (value) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value || 0);
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleString('vi-VN');
};

const getPaymentMethod = (method) => {
  const methods = {
    vnpay: 'VNPay',
    momo: 'MoMo',
    zalopay: 'ZaloPay',
    bank_transfer: 'Chuyển khoản',
    cod: 'COD'
  };
  return methods[method] || method;
};

const saveChanges = () => {
  emit('save', {
    id: props.order.id,
    ...localOrder.value
  });
};
</script>
