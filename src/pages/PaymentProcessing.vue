<template>
  <div class="min-h-screen bg-black flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
      <!-- Success State -->
      <div v-if="paymentStatus === 'completed'" class="text-center">
        <div class="mb-6">
          <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">🎉 Thanh toán thành công!</h1>
          <p class="text-gray-400 mb-6">Gói của bạn đã được kích hoạt</p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 mb-6 border border-green-500/30">
          <div class="flex items-center justify-between mb-4">
            <span class="text-gray-400">Gói đã mua:</span>
            <span class="text-white font-bold">{{ orderInfo?.plan_name }}</span>
          </div>
          <div class="flex items-center justify-between mb-4">
            <span class="text-gray-400">Số tiền:</span>
            <span class="text-green-400 font-bold">{{ formatPrice(orderInfo?.total) }}đ</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-gray-400">Thời hạn:</span>
            <span class="text-white font-bold">{{ orderInfo?.duration }} ngày</span>
          </div>
        </div>

        <div class="flex gap-3">
          <button
            @click="router.push('/account')"
            class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-6 rounded-lg transition-all"
          >
            Xem gói của tôi
          </button>
          <button
            @click="router.push('/home')"
            class="flex-1 bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-all"
          >
            Xem phim ngay
          </button>
        </div>
      </div>

      <!-- Processing State -->
      <div v-else-if="paymentStatus === 'pending'" class="text-center">
        <div class="mb-6">
          <div class="w-24 h-24 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-12 h-12 text-yellow-400 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">Đang xử lý thanh toán...</h1>
          <p class="text-gray-400 mb-2">Vui lòng đợi trong giây lát</p>
          <p class="text-sm text-gray-500">Tự động kiểm tra mỗi {{ checkInterval / 1000 }}s</p>
        </div>

        <div class="bg-gray-900 rounded-xl p-6 mb-6 border border-gray-800">
          <div class="flex items-start gap-3 mb-4">
            <svg class="w-6 h-6 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
              <h3 class="text-white font-bold mb-2">Đã chuyển khoản?</h3>
              <p class="text-gray-400 text-sm mb-3">
                Hệ thống đang tự động kiểm tra giao dịch của bạn. Thường mất 10-30 giây.
              </p>
              <div class="flex items-center gap-2 text-sm">
                <div class="flex-1 bg-gray-800 rounded-full h-2 overflow-hidden">
                  <div 
                    class="bg-yellow-400 h-full transition-all duration-1000"
                    :style="{ width: progressPercent + '%' }"
                  ></div>
                </div>
                <span class="text-gray-400 text-xs">{{ timeRemaining }}s</span>
              </div>
            </div>
          </div>

          <div class="border-t border-gray-800 pt-4">
            <p class="text-gray-400 text-sm mb-2">Thông tin đơn hàng:</p>
            <div class="flex items-center justify-between text-sm mb-1">
              <span class="text-gray-500">Mã đơn:</span>
              <span class="text-white font-mono">{{ orderCode }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500">Số tiền:</span>
              <span class="text-yellow-400 font-bold">{{ formatPrice(orderInfo?.total) }}đ</span>
            </div>
          </div>
        </div>

        <div class="flex gap-3">
          <button
            @click="checkPaymentStatus"
            :disabled="checking"
            class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-6 rounded-lg transition-all disabled:opacity-50 flex items-center justify-center gap-2"
          >
            <svg 
              class="w-5 h-5"
              :class="{ 'animate-spin': checking }"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>{{ checking ? 'Đang kiểm tra...' : 'Kiểm tra ngay' }}</span>
          </button>
          <button
            @click="router.push('/account')"
            class="flex-1 bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-all"
          >
            Về trang tài khoản
          </button>
        </div>
      </div>

      <!-- Failed State -->
      <div v-else-if="paymentStatus === 'failed'" class="text-center">
        <div class="mb-6">
          <div class="w-24 h-24 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">Thanh toán thất bại</h1>
          <p class="text-gray-400 mb-6">{{ errorMessage || 'Có lỗi xảy ra trong quá trình xử lý' }}</p>
        </div>

        <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-6 mb-6">
          <p class="text-red-400 text-sm">
            Nếu bạn đã chuyển khoản, vui lòng liên hệ support để được hỗ trợ.
          </p>
        </div>

        <div class="flex gap-3">
          <button
            @click="router.push('/pricing')"
            class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-6 rounded-lg transition-all"
          >
            Thử lại
          </button>
          <button
            @click="router.push('/account')"
            class="flex-1 bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-all"
          >
            Về trang tài khoản
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from '@/composables/useToast';
import { useAuthStore } from '@/stores/authStore';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const authStore = useAuthStore();

const orderCode = ref(route.query.order_code || '');
const paymentStatus = ref('pending'); // pending, completed, failed
const orderInfo = ref(null);
const checking = ref(false);
const errorMessage = ref('');

const checkInterval = 10000; // 10 seconds
const maxWaitTime = 300000; // 5 minutes
const startTime = ref(Date.now());
const timeRemaining = ref(300);
const progressPercent = computed(() => {
  const elapsed = Date.now() - startTime.value;
  return Math.min(100, (elapsed / maxWaitTime) * 100);
});

let checkTimer = null;
let countdownTimer = null;

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price);
};

const checkPaymentStatus = async () => {
  if (!orderCode.value) {
    errorMessage.value = 'Không tìm thấy mã đơn hàng';
    paymentStatus.value = 'failed';
    return;
  }

  checking.value = true;

  try {
    const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost/HTHREE_film/backend/api';
    const response = await fetch(`${API_URL}/check_order_status.php?order_code=${orderCode.value}`);
    const data = await response.json();

    if (data.success) {
      orderInfo.value = data.order;

      if (data.order.status === 'completed' && data.order.payment_status === 'paid') {
        paymentStatus.value = 'completed';
        toast.success('🎉 Thanh toán thành công! Gói của bạn đã được kích hoạt.');
        
        // Refresh user data to update subscription info
        if (authStore.user) {
          await authStore.refreshUserData();
        }
        
        // Stop checking
        if (checkTimer) clearInterval(checkTimer);
        if (countdownTimer) clearInterval(countdownTimer);
      } else if (data.order.payment_status === 'failed') {
        paymentStatus.value = 'failed';
        errorMessage.value = data.order.payment_note || 'Thanh toán thất bại';
        
        if (checkTimer) clearInterval(checkTimer);
        if (countdownTimer) clearInterval(countdownTimer);
      }
    } else {
      throw new Error(data.message || 'Không thể kiểm tra trạng thái đơn hàng');
    }
  } catch (error) {
    console.error('Error checking payment status:', error);
    // Don't show error, just continue checking
  } finally {
    checking.value = false;
  }
};

onMounted(() => {
  if (!orderCode.value) {
    toast.error('Không tìm thấy mã đơn hàng');
    router.push('/pricing');
    return;
  }

  // Initial check
  checkPaymentStatus();

  // Auto-check every 10 seconds
  checkTimer = setInterval(() => {
    checkPaymentStatus();
  }, checkInterval);

  // Countdown timer
  countdownTimer = setInterval(() => {
    timeRemaining.value--;
    
    if (timeRemaining.value <= 0) {
      clearInterval(checkTimer);
      clearInterval(countdownTimer);
      
      if (paymentStatus.value === 'pending') {
        toast.warning('Hết thời gian chờ. Vui lòng kiểm tra lại trong trang tài khoản.');
      }
    }
  }, 1000);
});

onUnmounted(() => {
  if (checkTimer) clearInterval(checkTimer);
  if (countdownTimer) clearInterval(countdownTimer);
});
</script>
