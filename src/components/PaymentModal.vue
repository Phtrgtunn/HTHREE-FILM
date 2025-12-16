<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in"
    >
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-black/80 backdrop-blur-sm"
        @click="$emit('close')"
      ></div>

      <!-- Modal -->
      <div
        class="relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border-2 border-gray-700 shadow-2xl animate-scale-in"
      >
        <!-- Close Button -->
        <button
          @click="$emit('close')"
          class="absolute top-4 right-4 w-10 h-10 rounded-full bg-gray-800 hover:bg-gray-700 flex items-center justify-center transition-colors z-10"
        >
          <svg
            class="w-6 h-6 text-gray-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>

        <!-- Content -->
        <div class="p-8">
          <!-- Header -->
          <div class="text-center mb-8">
            <div
              class="w-20 h-20 mx-auto mb-4 rounded-2xl flex items-center justify-center text-4xl"
              :class="getIconBgClass(plan.slug)"
            >
              {{ getIcon(plan.slug) }}
            </div>
            <h2 class="text-3xl font-black text-white mb-2">
              Thanh toán gói {{ plan.name }}
            </h2>
            <p class="text-gray-400">
              {{ plan.description }}
            </p>
          </div>

          <!-- Duration Selection -->
          <div class="mb-6">
            <label class="block text-white font-bold text-lg mb-4"
              >Chọn thời gian:</label
            >
            <div class="space-y-3">
              <label
                v-for="option in durationOptions"
                :key="option.months"
                class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl cursor-pointer border-2 transition-all hover:bg-gray-800"
                :class="
                  duration === option.months
                    ? 'border-red-500 bg-red-500/10'
                    : 'border-transparent'
                "
              >
                <div class="flex items-center gap-4">
                  <input
                    type="radio"
                    :value="option.months"
                    v-model="duration"
                    class="w-5 h-5 text-red-600"
                  />
                  <div>
                    <p class="text-white font-bold">{{ option.label }}</p>
                    <p
                      v-if="option.discount > 0"
                      class="text-green-400 text-sm"
                    >
                      Tiết kiệm {{ option.discount }}% 🎉
                    </p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-white font-bold text-xl">
                    {{
                      formatPrice(
                        calculatePrice(
                          plan.price,
                          option.months,
                          option.discount
                        )
                      )
                    }}
                  </p>
                  <p
                    v-if="option.months > 1"
                    class="text-gray-500 text-sm line-through"
                  >
                    {{ formatPrice(plan.price * option.months) }}
                  </p>
                </div>
              </label>
            </div>
          </div>

          <!-- Total Summary -->
          <div
            class="bg-gradient-to-r from-red-900/30 to-orange-900/30 rounded-xl p-6 mb-6 border border-red-500/30"
          >
            <div class="flex justify-between items-center">
              <span class="text-xl font-bold text-white">Tổng thanh toán</span>
              <span class="text-3xl font-black text-red-400">{{
                formatPrice(totalPrice)
              }}</span>
            </div>
          </div>

          <!-- Customer Info - Compact -->
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-400 text-sm mb-2">Họ tên *</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg border border-gray-700 focus:border-red-600 focus:outline-none transition-colors"
                  placeholder="Nguyễn Văn A"
                />
              </div>

              <div>
                <label class="block text-gray-400 text-sm mb-2"
                  >Số điện thoại</label
                >
                <input
                  v-model="form.phone"
                  type="tel"
                  class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg border border-gray-700 focus:border-red-600 focus:outline-none transition-colors"
                  placeholder="0901234567"
                />
              </div>
            </div>

            <div>
              <label class="block text-gray-400 text-sm mb-2">Email *</label>
              <input
                v-model="form.email"
                type="email"
                required
                class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg border border-gray-700 focus:border-red-600 focus:outline-none transition-colors"
                placeholder="email@example.com"
              />
            </div>

            <!-- Payment Method - Simplified -->
            <div>
              <label class="block text-gray-400 text-sm mb-3"
                >Phương thức thanh toán *</label
              >
              <div class="space-y-2">
                <label
                  v-for="method in paymentMethods"
                  :key="method.value"
                  class="flex items-center gap-3 p-3 bg-gray-800 rounded-lg cursor-pointer hover:bg-gray-700 transition-colors border-2"
                  :class="
                    form.paymentMethod === method.value
                      ? 'border-red-600 bg-red-600/10'
                      : 'border-transparent'
                  "
                >
                  <input
                    v-model="form.paymentMethod"
                    type="radio"
                    :value="method.value"
                    class="w-4 h-4 text-red-600"
                  />
                  <div class="flex-1">
                    <p class="text-white font-bold text-sm">
                      {{ method.label }}
                    </p>
                    <p class="text-gray-400 text-xs">
                      {{ method.description }}
                    </p>
                  </div>
                </label>
              </div>
            </div>

            <!-- Submit Button -->
            <button
              v-if="!showVietQR"
              type="submit"
              :disabled="submitting"
              class="w-full py-4 rounded-xl font-bold text-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              :class="getButtonClass(plan.slug)"
            >
              <template v-if="submitting">
                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                    fill="none"
                  />
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  />
                </svg>
                <span>Thành công!</span>
              </template>
              <template v-else>
                <svg
                  class="w-6 h-6"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                <span
                  >{{
                    form.paymentMethod === "vietqr"
                      ? "Tạo mã QR"
                      : "Xác nhận thanh toán"
                  }}
                  {{ formatPrice(totalPrice) }}</span
                >
              </template>
            </button>
          </form>

          <!-- VietQR Payment Component -->
          <VietQRPayment
            v-if="showVietQR && createdOrderId"
            :order-id="createdOrderId"
            @success="handleVietQRSuccess"
            @expired="handleVietQRExpired"
            @retry="showVietQR = false"
          />
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import { createOrder } from "@/services/ecommerceApi";
import { useToast } from "@/composables/useToast";
import VietQRPayment from "@/components/VietQRPayment.vue";
import { API_CONFIG } from "@/config/api.js";

const toast = useToast();
const router = useRouter();

const props = defineProps({
  plan: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["close", "success"]);

const authStore = useAuthStore();
const duration = ref(1);
const submitting = ref(false);
const createdOrderId = ref(null);
const showVietQR = ref(false);

// Duration options with discounts (thời gian ngắn để test)
const durationOptions = [
  { months: 1, label: "3 phút", discount: 0 },
  { months: 3, label: "5 phút", discount: 5 },
  { months: 6, label: "10 phút", discount: 10 },
  { months: 12, label: "15 phút", discount: 15 },
];

// Lấy thông tin user từ localStorage (đã lưu ở Account page)
const getUserInfo = () => {
  const storedUser = localStorage.getItem("user");
  const storedPhone = localStorage.getItem("userPhoneNumber");

  let userData = {};
  if (storedUser) {
    try {
      userData = JSON.parse(storedUser);
    } catch (e) {
      console.error("Error parsing user data:", e);
    }
  }

  return {
    name:
      userData.full_name ||
      userData.displayName ||
      authStore.user?.full_name ||
      authStore.user?.displayName ||
      "",
    email: userData.email || authStore.user?.email || "",
    phone: storedPhone || userData.phone || userData.phone_number || "",
  };
};

const userInfo = getUserInfo();

const form = ref({
  name: userInfo.name,
  email: userInfo.email,
  phone: userInfo.phone,
  paymentMethod: "bank_transfer",
});

const paymentMethods = [
  {
    value: "vietqr",
    label: "VietQR - Chuyển khoản ngân hàng",
    description:
      "Quét mã QR, tự động kích hoạt ngay khi chuyển khoản thành công",
  },
  {
    value: "bank_transfer",
    label: "Chuyển khoản thủ công",
    description: "Chuyển khoản và chờ admin duyệt",
  },
];

const totalPrice = computed(() => {
  const option = durationOptions.find((d) => d.months === duration.value);
  return calculatePrice(
    props.plan.price,
    duration.value,
    option?.discount || 0
  );
});

// Calculate price with discount
const calculatePrice = (basePrice, months, discount) => {
  const totalPrice = basePrice * months;
  const discountAmount = totalPrice * (discount / 100);
  return Math.round(totalPrice - discountAmount);
};

const handleSubmit = async () => {
  submitting.value = true;
  
  try {
    // Tự động lấy thông tin user
    const API_URL = API_CONFIG.BACKEND_URL;
    
    let currentUserData = null;
    let currentUserId = null;
    
    // Thử lấy từ localStorage trước
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      try {
        const userFromStorage = JSON.parse(storedUser);
        currentUserId = userFromStorage.id;
        currentUserData = userFromStorage;
      } catch (e) {
        console.error('Error parsing user data:', e);
      }
    }
    
    // Nếu không có, lấy user đầu tiên từ database
    if (!currentUserId) {
      try {
        const findUserResponse = await fetch(`${API_URL}/admin/users.php?limit=1`);
        const usersData = await findUserResponse.json();
        if (usersData.success && usersData.data.length > 0) {
          currentUserData = usersData.data[0];
          currentUserId = currentUserData.id;
          console.log('🔄 Auto-detected user for order:', currentUserData);
        }
      } catch (error) {
        console.error('Error finding user:', error);
        currentUserId = 109; // Fallback
      }
    }
    
    // Tạo đơn hàng với thông tin user thật (đã paid để tự động kích hoạt)
    const orderData = {
      user_id: currentUserId,
      customer_name: currentUserData?.full_name || currentUserData?.username || 'Unknown User',
      customer_email: currentUserData?.email || 'unknown@example.com',
      customer_phone: '0123456789',
      payment_method: 'bank_transfer',
      payment_status: 'paid', // Đặt thành paid luôn
      status: 'completed', // Đặt thành completed luôn
      plan_id: props.plan.id,
      duration_months: duration.value, // Sử dụng duration từ form
      total_price: totalPrice.value // Sử dụng giá đã tính discount
    };
    
    console.log('Creating order with real user data:', orderData);
    
    const response = await fetch(`${API_URL}/orders.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(orderData),
    });

    const result = await response.json();

    if (result.success) {
      createdOrderId.value = result.data?.id || result.id;

      // Subscription đã được tự động kích hoạt bởi backend

      // Thông báo thành công ngay lập tức
      toast.success(
        `🎉 Thanh toán thành công! Gói ${props.plan.name} đã được kích hoạt!`
      );

      // Đóng modal và chuyển đến Account ngay
      setTimeout(() => {
        emit("success");
        router.push("/account");
      }, 1000);
    } else {
      toast.error(result.message || "Không thể tạo gói");
    }
  } catch (error) {
    toast.error(error.response?.data?.message || "Không thể tạo đơn hàng");
  } finally {
    submitting.value = false;
  }
};

const handleVietQRSuccess = () => {
  toast.success("Thanh toán thành công! Gói đã được kích hoạt");
  emit("success");
  emit("close");
  router.push("/account");
};

const handleVietQRExpired = () => {
  toast.error("Đơn hàng đã hết hạn. Vui lòng tạo đơn mới");
  showVietQR.value = false;
  createdOrderId.value = null;
};

const formatPrice = (price) => {
  return new Intl.NumberFormat("vi-VN").format(price) + "đ";
};

const getIcon = (slug) => {
  const icons = {
    free: "🎬",
    basic: "⭐",
    premium: "🔥",
    vip: "👑",
  };
  return icons[slug] || "🎬";
};

const getIconBgClass = (slug) => {
  const classes = {
    free: "bg-gradient-to-br from-gray-700 to-gray-600",
    basic: "bg-gradient-to-br from-blue-600 to-cyan-500",
    premium: "bg-gradient-to-br from-red-600 to-yellow-500",
    vip: "bg-gradient-to-br from-purple-600 to-pink-500",
  };
  return classes[slug] || classes.free;
};

const getButtonClass = (slug) => {
  const classes = {
    free: "bg-gray-700 text-white hover:bg-gray-600",
    basic:
      "bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-lg shadow-blue-500/50 hover:shadow-blue-500/70",
    premium:
      "bg-gradient-to-r from-red-600 to-yellow-500 text-white shadow-lg shadow-red-500/50 hover:shadow-red-500/70",
    vip: "bg-gradient-to-r from-purple-600 to-pink-500 text-white shadow-lg shadow-purple-500/50 hover:shadow-purple-500/70",
  };
  return classes[slug] || classes.free;
};
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes scale-in {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

.animate-scale-in {
  animation: scale-in 0.3s ease-out;
}
</style>
