<template>
  <div class="min-h-screen bg-gradient-to-b from-black via-gray-900 to-black flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
      <!-- Loading -->
      <div v-if="loading" class="text-center">
        <div class="inline-block animate-spin rounded-full h-16 w-16 border-4 border-t-red-600 border-r-transparent border-b-red-600 border-l-transparent mb-4"></div>
        <p class="text-gray-400">Đang xử lý kết quả thanh toán...</p>
      </div>

      <!-- Success -->
      <div v-else-if="status === 'success'" class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-8 border border-gray-700/50 text-center">
        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-green-600 to-green-700 rounded-full flex items-center justify-center animate-bounce">
          <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
        </div>
        
        <h1 class="text-3xl font-black text-white mb-3">Thanh toán thành công!</h1>
        <p class="text-gray-400 mb-6">Đơn hàng của bạn đã được xác nhận</p>
        
        <div class="bg-gray-800/50 rounded-xl p-6 mb-6 text-left">
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-400">Mã đơn hàng:</span>
              <span class="text-white font-bold">{{ orderCode }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Số tiền:</span>
              <span class="text-white font-bold">{{ amount }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Phương thức:</span>
              <span class="text-white font-bold">{{ paymentMethod }}</span>
            </div>
          </div>
        </div>

        <div class="mb-4 text-gray-400 text-sm">
          Tự động chuyển đến trang tài khoản sau {{ countdown }} giây...
        </div>

        <div class="flex gap-4">
          <router-link
            to="/account"
            class="flex-1 bg-gradient-to-r from-red-600 to-yellow-500 text-white py-3 rounded-xl font-bold hover:from-red-700 hover:to-yellow-600 transition-all"
          >
            Xem gói của tôi
          </router-link>
          <router-link
            to="/home"
            class="flex-1 bg-gray-700 text-white py-3 rounded-xl font-bold hover:bg-gray-600 transition-all"
          >
            Về trang chủ
          </router-link>
        </div>
      </div>

      <!-- Pending (Bank Transfer) -->
      <div v-else-if="status === 'pending'" class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-8 border border-gray-700/50 text-center">
        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-yellow-600 to-orange-600 rounded-full flex items-center justify-center animate-pulse">
          <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
          </svg>
        </div>
        
        <h1 class="text-3xl font-black text-white mb-3">Đơn hàng đang chờ xử lý</h1>
        <p class="text-gray-400 mb-6">Vui lòng chuyển khoản để kích hoạt gói. Admin sẽ xác nhận sau khi nhận được tiền.</p>
        
        <div class="bg-gray-800/50 rounded-xl p-6 mb-6 text-left">
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-400">Mã đơn hàng:</span>
              <span class="text-white font-bold">{{ orderCode }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Số tiền:</span>
              <span class="text-white font-bold">{{ amount }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Phương thức:</span>
              <span class="text-white font-bold">Chuyển khoản ngân hàng</span>
            </div>
          </div>
        </div>

        <div class="mb-4 text-gray-400 text-sm">
          Tự động chuyển đến trang tài khoản sau {{ countdown }} giây...
        </div>

        <div class="flex gap-4">
          <router-link
            to="/account"
            class="flex-1 bg-gradient-to-r from-red-600 to-yellow-500 text-white py-3 rounded-xl font-bold hover:from-red-700 hover:to-yellow-600 transition-all"
          >
            Xem đơn hàng của tôi
          </router-link>
          <router-link
            to="/home"
            class="flex-1 bg-gray-700 text-white py-3 rounded-xl font-bold hover:bg-gray-600 transition-all"
          >
            Về trang chủ
          </router-link>
        </div>
      </div>

      <!-- Failed -->
      <div v-else-if="status === 'failed'" class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-8 border border-gray-700/50 text-center">
        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-red-600 to-red-700 rounded-full flex items-center justify-center">
          <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
        </div>
        
        <h1 class="text-3xl font-black text-white mb-3">Thanh toán thất bại!</h1>
        <p class="text-gray-400 mb-6">{{ errorMessage }}</p>
        
        <div class="flex gap-4">
          <router-link
            to="/checkout"
            class="flex-1 bg-gradient-to-r from-red-600 to-yellow-500 text-white py-3 rounded-xl font-bold hover:from-red-700 hover:to-yellow-600 transition-all"
          >
            Thử lại
          </router-link>
          <router-link
            to="/home"
            class="flex-1 bg-gray-700 text-white py-3 rounded-xl font-bold hover:bg-gray-600 transition-all"
          >
            Về trang chủ
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import paymentService from '@/services/paymentService';

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const status = ref('');
const orderCode = ref('');
const amount = ref('');
const paymentMethod = ref('');
const errorMessage = ref('');
const countdown = ref(5);

// Auto redirect countdown
let countdownInterval = null;

const startCountdown = () => {
  console.log('Starting countdown from', countdown.value);
  countdownInterval = setInterval(() => {
    countdown.value--;
    console.log('Countdown:', countdown.value);
    if (countdown.value <= 0) {
      console.log('Redirecting to /account');
      clearInterval(countdownInterval);
      router.push('/account');
    }
  }, 1000);
};

onMounted(async () => {
  // Lấy params từ URL
  const params = route.query;
  
  // Xử lý trường hợp pending (bank transfer)
  if (params.status === 'pending' && params.method === 'bank_transfer') {
    status.value = 'pending';
    orderCode.value = params.order_code;
    amount.value = 'Đang chờ xác nhận';
    paymentMethod.value = 'Chuyển khoản ngân hàng';
    loading.value = false;
    
    // Start countdown to redirect to account page
    startCountdown();
    return;
  }
  
  try {
    // Verify payment với backend cho các phương thức khác
    const result = await paymentService.verifyPayment(params);
    
    if (result.success) {
      status.value = 'success';
      orderCode.value = result.data.order_code;
      amount.value = result.data.amount_formatted;
      paymentMethod.value = result.data.payment_method;
      
      // Start countdown to redirect to account page
      startCountdown();
    } else {
      status.value = 'failed';
      errorMessage.value = result.message || 'Giao dịch không thành công';
    }
  } catch (error) {
    status.value = 'failed';
    errorMessage.value = 'Có lỗi xảy ra khi xử lý thanh toán';
  } finally {
    loading.value = false;
  }
});

// Cleanup countdown on unmount
onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval);
  }
});
</script>
