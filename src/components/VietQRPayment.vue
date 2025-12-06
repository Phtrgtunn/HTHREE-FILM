<template>
  <div class="vietqr-payment">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div
        class="animate-spin h-12 w-12 border-4 border-yellow-400 rounded-full border-t-transparent mx-auto mb-4"
      ></div>
      <p class="text-white">Đang tạo mã QR...</p>
    </div>

    <!-- QR Code Display -->
    <div v-else-if="qrData" class="space-y-6">
      <!-- Header -->
      <div class="text-center">
        <h3 class="text-2xl font-bold text-white mb-2">
          Quét mã QR để thanh toán
        </h3>
        <p class="text-gray-400">Sử dụng app ngân hàng để quét mã QR</p>
      </div>

      <!-- Hướng dẫn chuyển khoản (Dropdown) -->
      <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg overflow-hidden">
        <button
          @click="showGuide = !showGuide"
          class="w-full p-4 flex items-center justify-between hover:bg-blue-500/5 transition-colors duration-200"
        >
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <h4 class="text-blue-400 font-bold">Hướng dẫn chuyển khoản</h4>
            <span class="text-xs text-blue-300/60">(Click để {{ showGuide ? 'ẩn' : 'xem' }})</span>
          </div>
          <svg 
            class="w-5 h-5 text-blue-400 transition-transform duration-200 ease-out"
            :class="{ 'rotate-180': showGuide }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        
        <Transition
          enter-active-class="transition-all duration-200 ease-out origin-top"
          enter-from-class="opacity-0 scale-y-0"
          enter-to-class="opacity-100 scale-y-100"
          leave-active-class="transition-all duration-150 ease-in origin-top"
          leave-from-class="opacity-100 scale-y-100"
          leave-to-class="opacity-0 scale-y-0"
        >
          <div v-show="showGuide" class="border-t border-blue-500/20">
            <div class="px-4 py-4 space-y-3 text-sm text-gray-300">
              <div class="flex items-start gap-2">
                <span class="text-blue-400 font-bold">📱 Bước 1:</span>
                <span>Mở app ngân hàng của bạn</span>
              </div>
              <div class="flex items-start gap-2">
                <span class="text-blue-400 font-bold">📸 Bước 2:</span>
                <span>Quét mã QR bên dưới</span>
              </div>
              <div class="flex items-start gap-2">
                <span class="text-blue-400 font-bold">✍️ Bước 3:</span>
                <span>Kiểm tra nội dung chuyển khoản (tự động điền)</span>
              </div>
              <div class="flex items-start gap-2">
                <span class="text-blue-400 font-bold">✅ Bước 4:</span>
                <span>Xác nhận chuyển khoản</span>
              </div>
              <div class="flex items-start gap-2">
                <span class="text-blue-400 font-bold">⚡ Bước 5:</span>
                <span>Gói sẽ tự động kích hoạt trong 10-30 giây</span>
              </div>
            </div>
          </div>
        </Transition>
      </div>

      <!-- QR Code -->
      <div class="bg-white p-6 rounded-2xl mx-auto max-w-sm">
        <img
          :src="qrData.qr_url"
          alt="VietQR Code"
          class="w-full h-auto"
          @error="handleQRError"
        />
      </div>

      <!-- Payment Info -->
      <div
        class="bg-gray-800/50 rounded-xl p-6 border border-gray-700 space-y-4"
      >
        <!-- Amount -->
        <div
          class="flex justify-between items-center pb-4 border-b border-gray-700"
        >
          <span class="text-gray-400">Số tiền</span>
          <span class="text-2xl font-bold text-red-500">
            {{ formatPrice(qrData.amount) }}
          </span>
        </div>

        <!-- Bank Info -->
        <div class="space-y-2">
          <div class="flex justify-between">
            <span class="text-gray-400">Ngân hàng</span>
            <span class="text-white font-medium">{{ qrData.bank_name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-400">Số tài khoản</span>
            <span class="text-white font-mono">{{ qrData.account_no }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-400">Chủ tài khoản</span>
            <span class="text-white">{{ qrData.account_name }}</span>
          </div>
        </div>

        <!-- Transfer Content -->
        <div
          class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4"
        >
          <div class="flex items-start gap-3">
            <svg
              class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd"
              />
            </svg>
            <div class="flex-1">
              <p class="text-yellow-400 font-bold text-sm mb-1">
                Nội dung chuyển khoản
              </p>
              <div class="flex items-center gap-2">
                <code
                  class="text-white font-mono text-lg bg-gray-900 px-3 py-1 rounded"
                >
                  {{ qrData.transfer_content }}
                </code>
                <button
                  @click="copyContent"
                  class="p-2 hover:bg-gray-700 rounded transition-colors"
                  :title="copied ? 'Đã copy!' : 'Copy nội dung'"
                >
                  <svg
                    v-if="!copied"
                    class="w-5 h-5 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                    />
                  </svg>
                  <svg
                    v-else
                    class="w-5 h-5 text-green-400"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
              <p class="text-yellow-300 text-xs mt-2">
                ⚠️ Vui lòng nhập chính xác nội dung để hệ thống tự động kích
                hoạt gói
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Countdown Timer -->
      <div
        class="bg-gradient-to-r from-red-900/50 to-orange-900/50 border border-red-500/30 rounded-xl p-4"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <svg
              class="w-6 h-6 text-red-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <div>
              <p class="text-white font-bold">Thời gian còn lại</p>
              <p class="text-gray-400 text-sm">
                Đơn hàng sẽ tự động hủy sau khi hết thời gian
              </p>
            </div>
          </div>
          <div class="text-right">
            <p
              class="text-3xl font-mono font-bold"
              :class="
                timeRemaining < 60 ? 'text-red-400 animate-pulse' : 'text-white'
              "
            >
              {{ formatTime(timeRemaining) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Payment Status -->
      <div
        v-if="checkingPayment"
        class="bg-blue-900/30 border border-blue-500/30 rounded-xl p-6 text-center"
      >
        <div
          class="animate-spin h-8 w-8 border-3 border-blue-400 rounded-full border-t-transparent mx-auto mb-3"
        ></div>
        <p class="text-blue-400 font-bold mb-1">Đang kiểm tra thanh toán...</p>
        <p class="text-gray-400 text-sm">
          Hệ thống sẽ tự động kích hoạt gói sau khi nhận được tiền
        </p>
      </div>

      <!-- Success Message -->
      <div
        v-if="paymentSuccess"
        class="bg-green-900/30 border border-green-500/30 rounded-xl p-6 text-center animate-fade-in"
      >
        <svg
          class="w-16 h-16 text-green-400 mx-auto mb-4"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
        <h3 class="text-2xl font-bold text-green-400 mb-2">
          Thanh toán thành công!
        </h3>
        <p class="text-gray-300 mb-4">Gói của bạn đã được kích hoạt</p>
        <button
          @click="$emit('success')"
          class="bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-3 rounded-lg transition-colors"
        >
          Xem tài khoản
        </button>
      </div>

      <!-- Instructions -->
      <div class="bg-gray-800/30 rounded-xl p-6 border border-gray-700">
        <h4 class="text-white font-bold mb-3 flex items-center gap-2">
          <svg
            class="w-5 h-5 text-yellow-400"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
              clip-rule="evenodd"
            />
          </svg>
          Hướng dẫn thanh toán
        </h4>
        <ol class="space-y-2 text-gray-300 text-sm">
          <li class="flex gap-2">
            <span class="text-yellow-400 font-bold">1.</span>
            <span>Mở app ngân hàng của bạn</span>
          </li>
          <li class="flex gap-2">
            <span class="text-yellow-400 font-bold">2.</span>
            <span>Chọn "Quét mã QR" hoặc "Chuyển khoản"</span>
          </li>
          <li class="flex gap-2">
            <span class="text-yellow-400 font-bold">3.</span>
            <span>Quét mã QR phía trên</span>
          </li>
          <li class="flex gap-2">
            <span class="text-yellow-400 font-bold">4.</span>
            <span>Kiểm tra thông tin và xác nhận chuyển khoản</span>
          </li>
          <li class="flex gap-2">
            <span class="text-yellow-400 font-bold">5.</span>
            <span>Gói sẽ tự động kích hoạt sau 5-30 giây</span>
          </li>
        </ol>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-12">
      <svg
        class="w-16 h-16 text-red-400 mx-auto mb-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
      <p class="text-red-400 font-bold mb-2">{{ error }}</p>
      <button
        @click="$emit('retry')"
        class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors"
      >
        Thử lại
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useToast } from "@/composables/useToast";

const toast = useToast();

const props = defineProps({
  orderId: {
    type: Number,
    required: true,
  },
});

const emit = defineEmits(["success", "retry", "expired"]);

const loading = ref(true);
const qrData = ref(null);
const error = ref(null);
const checkingPayment = ref(false);
const paymentSuccess = ref(false);
const timeRemaining = ref(900); // 15 minutes
const copied = ref(false);
const showGuide = ref(false); // Dropdown state

let checkInterval = null;
let countdownInterval = null;

const API_URL =
  import.meta.env.VITE_API_BASE_URL ||
  "http://localhost/HTHREE_film/HTHREE_film/backend/api";

onMounted(async () => {
  await generateQR();
  startPaymentCheck();
  startCountdown();
});

onUnmounted(() => {
  stopPaymentCheck();
  stopCountdown();
});

const generateQR = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await fetch(`${API_URL}/payment/generate_vietqr.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ order_id: props.orderId }),
    });

    const data = await response.json();

    if (data.success) {
      qrData.value = data.data;
      timeRemaining.value = data.data.timeout_seconds;
    } else {
      throw new Error(data.message);
    }
  } catch (err) {
    error.value = err.message || "Không thể tạo mã QR";
    toast.error(error.value);
  } finally {
    loading.value = false;
  }
};

const startPaymentCheck = () => {
  checkingPayment.value = true;

  checkInterval = setInterval(async () => {
    try {
      const response = await fetch(
        `${API_URL}/payment/check_payment_status.php?order_id=${props.orderId}`
      );
      const data = await response.json();

      if (data.success && data.data.paid) {
        paymentSuccess.value = true;
        checkingPayment.value = false;
        stopPaymentCheck();
        stopCountdown();

        toast.success("🎉 Thanh toán thành công!");

        setTimeout(() => {
          emit("success");
        }, 2000);
      }

      if (data.success && data.data.expired) {
        stopPaymentCheck();
        stopCountdown();
        emit("expired");
      }
    } catch (err) {
      console.error("Error checking payment:", err);
    }
  }, 3000); // Check every 3 seconds
};

const stopPaymentCheck = () => {
  if (checkInterval) {
    clearInterval(checkInterval);
    checkInterval = null;
  }
};

const startCountdown = () => {
  countdownInterval = setInterval(() => {
    timeRemaining.value--;

    if (timeRemaining.value <= 0) {
      stopCountdown();
      stopPaymentCheck();
      emit("expired");
    }
  }, 1000);
};

const stopCountdown = () => {
  if (countdownInterval) {
    clearInterval(countdownInterval);
    countdownInterval = null;
  }
};

const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins.toString().padStart(2, "0")}:${secs
    .toString()
    .padStart(2, "0")}`;
};

const formatPrice = (price) => {
  return new Intl.NumberFormat("vi-VN").format(price) + "đ";
};

const copyContent = async () => {
  try {
    await navigator.clipboard.writeText(qrData.value.transfer_content);
    copied.value = true;
    toast.success("Đã copy nội dung chuyển khoản");

    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (err) {
    toast.error("Không thể copy");
  }
};

const handleQRError = (e) => {
  e.target.src = "https://placehold.co/400x400/1a1a1a/fff?text=QR+Error";
  error.value = "Không thể tải mã QR";
};

const manualConfirm = async () => {
  try {
    // Update order status manually (for localhost testing)
    const response = await fetch(`${API_URL}/admin/approve_order.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        order_id: props.orderId,
        transaction_id: "MANUAL_TEST_" + Date.now(),
      }),
    });

    const data = await response.json();

    if (data.success) {
      paymentSuccess.value = true;
      checkingPayment.value = false;
      stopPaymentCheck();
      stopCountdown();

      toast.success("🎉 Thanh toán thành công!");

      setTimeout(() => {
        emit("success");
      }, 2000);
    } else {
      throw new Error(data.message);
    }
  } catch (err) {
    toast.error("Không thể xác nhận thanh toán: " + err.message);
  }
};
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}
</style>
