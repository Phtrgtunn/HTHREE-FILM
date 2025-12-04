<template>
  <div class="min-h-screen bg-gradient-to-b from-pink-900 via-pink-800 to-pink-900 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 shadow-2xl">
      <!-- MoMo Logo -->
      <div class="text-center mb-6">
        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-pink-600 to-pink-700 rounded-2xl flex items-center justify-center">
          <span class="text-white text-3xl font-bold">M</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Thanh toán MoMo</h1>
        <p class="text-gray-600 text-sm">Môi trường test - Demo</p>
      </div>

      <!-- Order Info -->
      <div class="bg-gray-50 rounded-2xl p-6 mb-6">
        <div class="space-y-3">
          <div class="flex justify-between">
            <span class="text-gray-600">Mã đơn hàng:</span>
            <span class="font-bold text-gray-800">{{ orderCode }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Số tiền:</span>
            <span class="font-bold text-pink-600 text-xl">{{ amount }} đ</span>
          </div>
        </div>
      </div>

      <!-- Mock Payment Buttons -->
      <div class="space-y-3">
        <button
          @click="handlePayment('success')"
          :disabled="processing"
          class="w-full bg-gradient-to-r from-pink-600 to-pink-700 text-white py-4 rounded-2xl font-bold hover:from-pink-700 hover:to-pink-800 transition-all disabled:opacity-50"
        >
          {{ processing ? 'Đang xử lý...' : 'Thanh toán thành công (Demo)' }}
        </button>
        
        <button
          @click="handlePayment('failed')"
          :disabled="processing"
          class="w-full bg-gray-200 text-gray-700 py-4 rounded-2xl font-bold hover:bg-gray-300 transition-all disabled:opacity-50"
        >
          Hủy thanh toán (Demo)
        </button>
      </div>

      <p class="text-center text-gray-500 text-xs mt-6">
        Đây là trang demo MoMo. Trong thực tế, bạn sẽ được chuyển đến ứng dụng MoMo để thanh toán.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const processing = ref(false);
const orderCode = ref('');
const amount = ref('');

onMounted(() => {
  orderCode.value = route.query.order_code || 'N/A';
  amount.value = route.query.amount || '0';
});

const handlePayment = async (status) => {
  processing.value = true;
  
  // Giả lập xử lý thanh toán
  await new Promise(resolve => setTimeout(resolve, 2000));
  
  // Redirect về payment return với kết quả
  const params = new URLSearchParams({
    order_code: orderCode.value,
    amount: amount.value,
    status: status,
    payment_method: 'momo',
    message: status === 'success' ? 'Thanh toán thành công' : 'Người dùng hủy thanh toán'
  });
  
  router.push(`/payment-return?${params.toString()}`);
};
</script>
