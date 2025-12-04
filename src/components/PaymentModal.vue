<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
      <!-- Backdrop -->
      <div 
        class="absolute inset-0 bg-black/80 backdrop-blur-sm"
        @click="$emit('close')"
      ></div>

      <!-- Modal -->
      <div class="relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border-2 border-gray-700 shadow-2xl animate-scale-in">
        <!-- Close Button -->
        <button
          @click="$emit('close')"
          class="absolute top-4 right-4 w-10 h-10 rounded-full bg-gray-800 hover:bg-gray-700 flex items-center justify-center transition-colors z-10"
        >
          <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

        <!-- Content -->
        <div class="p-8">
          <!-- Header -->
          <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl flex items-center justify-center text-4xl" :class="getIconBgClass(plan.slug)">
              {{ getIcon(plan.slug) }}
            </div>
            <h2 class="text-3xl font-black text-white mb-2">
              Thanh toán gói {{ plan.name }}
            </h2>
            <p class="text-gray-400">
              {{ plan.description }}
            </p>
          </div>

          <!-- Plan Summary -->
          <div class="bg-gray-800/50 rounded-xl p-6 mb-6 border border-gray-700">
            <div class="flex justify-between items-center mb-4">
              <span class="text-gray-400">Gói đã chọn</span>
              <span class="text-white font-bold">{{ plan.name }} - {{ plan.quality }}</span>
            </div>
            <div class="flex justify-between items-center mb-4">
              <span class="text-gray-400">Thời hạn</span>
              <div class="flex items-center gap-2">
                <button
                  @click="duration > 1 && duration--"
                  class="w-8 h-8 rounded-lg bg-gray-700 hover:bg-gray-600 flex items-center justify-center"
                >
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                  </svg>
                </button>
                <span class="text-white font-bold w-20 text-center">{{ duration }} tháng</span>
                <button
                  @click="duration < 12 && duration++"
                  class="w-8 h-8 rounded-lg bg-gray-700 hover:bg-gray-600 flex items-center justify-center"
                >
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                </button>
              </div>
            </div>
            <div class="flex justify-between items-center pt-4 border-t border-gray-700">
              <span class="text-xl font-bold text-white">Tổng cộng</span>
              <span class="text-3xl font-black text-red-500">{{ formatPrice(totalPrice) }}</span>
            </div>
          </div>

          <!-- Customer Info -->
          <form @submit.prevent="handleSubmit" class="space-y-4">
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
              <label class="block text-gray-400 text-sm mb-2">Email *</label>
              <input
                v-model="form.email"
                type="email"
                required
                class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg border border-gray-700 focus:border-red-600 focus:outline-none transition-colors"
                placeholder="email@example.com"
              />
            </div>

            <div>
              <label class="block text-gray-400 text-sm mb-2">Số điện thoại</label>
              <input
                v-model="form.phone"
                type="tel"
                class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg border border-gray-700 focus:border-red-600 focus:outline-none transition-colors"
                placeholder="0901234567"
              />
            </div>

            <!-- Payment Method -->
            <div>
              <label class="block text-gray-400 text-sm mb-3">Phương thức thanh toán *</label>
              <div class="space-y-2">
                <label
                  v-for="method in paymentMethods"
                  :key="method.value"
                  class="flex items-center gap-3 p-4 bg-gray-800 rounded-lg cursor-pointer hover:bg-gray-700 transition-colors border-2"
                  :class="form.paymentMethod === method.value ? 'border-red-600' : 'border-transparent'"
                >
                  <input
                    v-model="form.paymentMethod"
                    type="radio"
                    :value="method.value"
                    class="w-5 h-5 text-red-600"
                  />
                  <div class="flex-1">
                    <p class="text-white font-bold">{{ method.label }}</p>
                    <p class="text-gray-400 text-sm">{{ method.description }}</p>
                  </div>
                </label>
              </div>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="submitting"
              class="w-full py-4 rounded-xl font-bold text-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
              :class="getButtonClass(plan.slug)"
            >
              <span v-if="submitting" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
                Đang xử lý...
              </span>
              <span v-else class="flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Xác nhận thanh toán {{ formatPrice(totalPrice) }}
              </span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { createOrder } from '@/services/ecommerceApi';
import { useToast } from '@/composables/useToast';

const toast = useToast();
const router = useRouter();

const props = defineProps({
  plan: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'success']);

const authStore = useAuthStore();
const duration = ref(1);
const submitting = ref(false);

// Lấy thông tin user từ localStorage (đã lưu ở Account page)
const getUserInfo = () => {
  const storedUser = localStorage.getItem('user');
  const storedPhone = localStorage.getItem('userPhoneNumber');
  
  let userData = {};
  if (storedUser) {
    try {
      userData = JSON.parse(storedUser);
    } catch (e) {
      console.error('Error parsing user data:', e);
    }
  }
  
  return {
    name: userData.full_name || userData.displayName || authStore.user?.full_name || authStore.user?.displayName || '',
    email: userData.email || authStore.user?.email || '',
    phone: storedPhone || userData.phone || userData.phone_number || ''
  };
};

const userInfo = getUserInfo();

const form = ref({
  name: userInfo.name,
  email: userInfo.email,
  phone: userInfo.phone,
  paymentMethod: 'bank_transfer'
});

const paymentMethods = [
  {
    value: 'vnpay',
    label: 'VNPay',
    description: 'Thanh toán qua VNPay (ATM, Visa, MasterCard)'
  },
  {
    value: 'momo',
    label: 'MoMo',
    description: 'Thanh toán qua ví MoMo'
  },
  {
    value: 'bank_transfer',
    label: 'Chuyển khoản ngân hàng',
    description: 'Chuyển khoản trực tiếp'
  }
];

const totalPrice = computed(() => {
  return props.plan.price * duration.value;
});

const handleSubmit = async () => {
  submitting.value = true;

  try {
    // Lấy user_id từ localStorage (MySQL ID)
    const storedUser = localStorage.getItem('user');
    let userId = null;
    
    if (storedUser) {
      try {
        const userData = JSON.parse(storedUser);
        userId = userData.id; // MySQL ID
      } catch (e) {
        console.error('Error parsing user data:', e);
      }
    }
    
    // Fallback to authStore
    if (!userId) {
      userId = authStore.user?.id || authStore.user?.uid;
    }
    
    console.log('Creating order with user_id:', userId, 'plan_id:', props.plan.id);
    
    // Tạo đơn hàng trực tiếp (không qua giỏ hàng)
    const orderData = {
      user_id: userId,
      customer_name: form.value.name,
      customer_email: form.value.email,
      customer_phone: form.value.phone,
      payment_method: form.value.paymentMethod,
      plan_id: props.plan.id,
      duration_months: duration.value
    };

    // Gọi API tạo đơn hàng
    const response = await createOrder(orderData);

    if (response.success) {
      toast.success('Đặt hàng thành công! Đang kích hoạt gói...');
      
      // Auto-approve order for testing (tự động duyệt đơn)
      try {
        const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost/HTHREE_film/HTHREE_film/backend/api';
        await fetch(`${API_URL}/admin/approve_order.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ order_id: response.data.id })
        });
        console.log('✅ Order auto-approved');
      } catch (error) {
        console.error('Failed to auto-approve:', error);
      }
      
      emit('success', response.data);
      
      // Redirect to account page using Vue Router (smooth navigation)
      setTimeout(() => {
        emit('close');
        router.push('/account');
      }, 1500);
    }
  } catch (error) {
    toast.error(error.response?.data?.message || 'Không thể tạo đơn hàng');
  } finally {
    submitting.value = false;
  }
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
};

const getIcon = (slug) => {
  const icons = {
    free: '🎬',
    basic: '⭐',
    premium: '🔥',
    vip: '👑'
  };
  return icons[slug] || '🎬';
};

const getIconBgClass = (slug) => {
  const classes = {
    free: 'bg-gradient-to-br from-gray-700 to-gray-600',
    basic: 'bg-gradient-to-br from-blue-600 to-cyan-500',
    premium: 'bg-gradient-to-br from-red-600 to-yellow-500',
    vip: 'bg-gradient-to-br from-purple-600 to-pink-500'
  };
  return classes[slug] || classes.free;
};

const getButtonClass = (slug) => {
  const classes = {
    free: 'bg-gray-700 text-white hover:bg-gray-600',
    basic: 'bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-lg shadow-blue-500/50 hover:shadow-blue-500/70',
    premium: 'bg-gradient-to-r from-red-600 to-yellow-500 text-white shadow-lg shadow-red-500/50 hover:shadow-red-500/70',
    vip: 'bg-gradient-to-r from-purple-600 to-pink-500 text-white shadow-lg shadow-purple-500/50 hover:shadow-purple-500/70'
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
