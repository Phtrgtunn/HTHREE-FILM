<template>
  <div class="min-h-screen bg-black relative overflow-hidden">
    <!-- Success Animation -->
    <SuccessAnimation 
      :show="showSuccess" 
      :message="successMessage"
      @close="showSuccess = false"
    />
    
    <!-- Animated Background -->
    <div class="absolute inset-0">
      <div class="absolute top-0 left-1/4 w-96 h-96 bg-red-600/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-yellow-600/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-4 py-20">
      <!-- Breadcrumb -->
      <Breadcrumb :items="breadcrumbItems" class="mb-8" />

      <!-- Header -->
      <div class="text-center mb-16 animate-fade-in">
        <h1 class="text-6xl font-black text-white mb-4">
          {{ $t('pricing.chooseTitle') }} <span class="text-red-600">{{ $t('pricing.chooseTitleHighlight') }}</span> {{ $t('pricing.chooseTitleEnd') }}
        </h1>
        <p class="text-xl text-gray-400">
          {{ $t('pricing.subtitle') }}
        </p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-20">
        <div class="inline-block animate-spin rounded-full h-16 w-16 border-4 border-t-red-600 border-r-transparent border-b-red-600 border-l-transparent"></div>
      </div>

      <!-- Pricing Cards -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-[1400px] mx-auto">
        <div
          v-for="(plan, index) in plans"
          :key="plan.id"
          @mouseenter="hoveredPlan = plan.slug"
          @mouseleave="hoveredPlan = null"
          class="group relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-6 border-2 transition-all duration-300 animate-slide-up"
          :style="{ animationDelay: `${index * 0.1}s` }"
          :class="[
            plan.slug === 'premium' 
              ? 'border-red-600/50 lg:scale-105 shadow-lg shadow-red-600/10' 
              : 'border-gray-700/50 hover:border-gray-600',
            hoveredPlan === plan.slug ? 'scale-102 shadow-2xl' : ''
          ]"
        >
          <!-- Subtle Glow Effect - Reduced opacity -->
          <div 
            class="absolute -inset-0.5 rounded-2xl opacity-0 transition-opacity duration-300 blur-lg -z-10"
            :class="[
              getGlowClass(plan.slug),
              hoveredPlan === plan.slug ? 'opacity-20' : ''
            ]"
          ></div>

          <!-- Shine Effect on Hover - Removed for cleaner look -->
          <!-- <div 
            class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"
            style="background: linear-gradient(135deg, transparent 0%, rgba(255,255,255,0.03) 50%, transparent 100%)"
          ></div> -->

          <!-- Popular Badge - Removed animate-pulse -->
          <div v-if="plan.slug === 'premium'" class="absolute -top-3 left-1/2 -translate-x-1/2">
            <span class="bg-gradient-to-r from-red-600 to-yellow-500 text-white px-4 py-1 rounded-full text-xs font-bold uppercase shadow-lg">
              ⭐ {{ $t('pricing.mostPopular') }}
            </span>
          </div>

          <!-- Icon with Tooltip -->
          <div class="mb-6 relative">
            <div 
              class="w-20 h-20 rounded-2xl flex items-center justify-center text-4xl shadow-lg transform transition-all duration-300"
              :class="[
                getIconBgClass(plan.slug),
                hoveredPlan === plan.slug ? 'scale-110 rotate-3' : ''
              ]"
            >
              {{ getIcon(plan.slug) }}
            </div>
            
            <!-- Tooltip on hover - Removed animate-bounce -->
            <div 
              v-if="hoveredPlan === plan.slug"
              class="absolute -top-2 -right-2 bg-white text-black text-xs font-bold px-2 py-1 rounded-full shadow-lg"
            >
              {{ $t('pricing.clickToSelect') }}
            </div>
          </div>

          <!-- Plan Name -->
          <h3 
            class="text-3xl font-black mb-2 transition-all duration-300"
            :class="hoveredPlan === plan.slug ? 'text-red-500 scale-105' : 'text-white'"
          >
            {{ plan.name }}
          </h3>

          <!-- Quality -->
          <div 
            class="inline-block px-3 py-1 rounded-full text-xs font-bold mb-6"
            :class="getQualityClass(plan.slug)"
          >
            {{ plan.quality }}
          </div>

          <!-- Price Display -->
          <div class="mb-6">
            <div class="flex items-baseline gap-2">
              <span 
                class="text-5xl font-black text-white transition-all duration-300"
                :class="hoveredPlan === plan.slug ? 'scale-110' : ''"
              >
                {{ plan.slug === 'free' ? $t('pricing.free') : formatPrice(plan.price) }}
              </span>
              <span v-if="plan.slug !== 'free'" class="text-gray-400">/tháng</span>
            </div>
            
            <p v-if="plan.price > 0" class="text-gray-400 text-sm mt-2">
              Hoặc {{ formatPrice(calculatePrice(plan.price, 12, 15)) }}/năm (tiết kiệm 15%)
            </p>
          </div>

          <!-- Features with smooth animations -->
          <ul class="space-y-3 mb-8">
            <li 
              v-for="(feature, idx) in getFeatures(plan)" 
              :key="idx"
              class="flex items-center gap-2 text-gray-300 transition-all duration-300"
              :class="hoveredPlan === plan.slug ? 'translate-x-1' : ''"
              :style="{ transitionDelay: hoveredPlan === plan.slug ? `${idx * 50}ms` : '0ms' }"
            >
              <svg class="w-5 h-5 flex-shrink-0" :class="feature.iconClass" fill="currentColor" viewBox="0 0 20 20">
                <path v-if="feature.checked" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                <path v-else fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
              <span class="text-sm">{{ feature.text }}</span>
            </li>
          </ul>

          <!-- Action Buttons -->
          <div class="space-y-3">
            <!-- Add to Cart Button -->
            <button
              v-if="plan.slug !== 'free'"
              @click.stop="handleAddToCart(plan)"
              :disabled="addingToCart === plan.id"
              class="w-full py-3.5 rounded-xl font-bold text-center transition-all duration-300 border-2 flex items-center justify-center gap-2"
              :class="[
                plan.slug === 'basic' ? 'border-blue-500 text-blue-400 hover:bg-blue-500 hover:text-white hover:scale-105' : '',
                plan.slug === 'premium' ? 'border-red-500 text-red-400 hover:bg-red-500 hover:text-white hover:scale-105' : '',
                plan.slug === 'vip' ? 'border-purple-500 text-purple-400 hover:bg-purple-500 hover:text-white hover:scale-105' : '',
                addingToCart === plan.id ? 'opacity-50 cursor-not-allowed' : ''
              ]"
            >
              <svg v-if="addingToCart === plan.id" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              {{ $t('pricing.addToCart') }}
            </button>

            <!-- Buy Now Button -->
            <button
              @click.stop="handleBuyNow(plan)"
              class="relative w-full py-4 rounded-xl font-bold text-center transition-all duration-300 overflow-hidden flex items-center justify-center gap-2"
              :class="[
                getButtonClass(plan.slug),
                hoveredPlan === plan.slug ? 'scale-105 shadow-2xl' : ''
              ]"
            >
              <!-- Button Shine -->
              <div 
                v-if="hoveredPlan === plan.slug"
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shine"
              ></div>
              
              <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
              <span class="relative z-10">
                {{ plan.slug === 'free' ? $t('pricing.useFree') : $t('pricing.buyNow') }}
              </span>
            </button>
          </div>
          
          <!-- Popular Choice Indicator -->
          <div 
            v-if="plan.slug === 'premium' && hoveredPlan === plan.slug"
            class="mt-4 text-center text-yellow-400 text-sm font-bold animate-pulse"
          >
            ⭐ {{ $t('pricing.popularChoice') }}
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <PaymentModal 
      v-if="showPaymentModal" 
      :plan="selectedPlan"
      @close="showPaymentModal = false"
      @success="handlePaymentSuccess"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { getPlans } from '@/services/ecommerceApi';
import { useAuthStore } from '@/stores/authStore';
import { useCartStore } from '@/stores/cartStore';
import { useToast } from '@/composables/useToast';
import PaymentModal from '@/components/PaymentModal.vue';
import LoadingButton from '@/components/LoadingButton.vue';
import SuccessAnimation from '@/components/SuccessAnimation.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const toast = useToast();

const plans = ref([]);
const loading = ref(false);
const showPaymentModal = ref(false);
const selectedPlan = ref(null);
const hoveredPlan = ref(null);
const billingPeriod = ref('monthly');
const addingToCart = ref(null);
const showSuccess = ref(false);
const successMessage = ref('');
const selectedDurations = ref({});

// Duration options with discounts
const durationOptions = [
  { months: 1, label: '1 tháng', discount: 0 },
  { months: 3, label: '3 tháng', discount: 5 },
  { months: 6, label: '6 tháng', discount: 10 },
  { months: 12, label: '12 tháng', discount: 15 }
];

// Breadcrumb items
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const breadcrumbItems = [
  { label: t('common.home'), to: '/home' },
  { label: t('pricing.title'), to: '/pricing' }
];

onMounted(async () => {
  await fetchPlans();
});

const fetchPlans = async () => {
  loading.value = true;
  try {
    const response = await getPlans(true);
    if (response.success) {
      plans.value = response.data;
      // Initialize selected durations (default to 1 month)
      plans.value.forEach(plan => {
        selectedDurations.value[plan.id] = 1;
      });
    }
  } catch (err) {
    const { handleError } = await import('@/composables/useErrorHandler');
    const errorHandler = handleError();
    errorHandler.handleError(err, {
      context: 'Fetch Plans',
      fallbackMessage: 'Không thể tải danh sách gói. Vui lòng kiểm tra kết nối Internet và thử lại.'
    });
  } finally {
    loading.value = false;
  }
};

// Calculate price with discount
const calculatePrice = (basePrice, months, discount) => {
  const totalPrice = basePrice * months;
  const discountAmount = totalPrice * (discount / 100);
  return Math.round(totalPrice - discountAmount);
};

// Thêm vào giỏ hàng
const handleAddToCart = async (plan) => {
  // Kiểm tra đăng nhập
  if (!authStore.user) {
    toast.warning(t('pricing.loginToAddCart'));
    router.push('/account');
    return;
  }

  // Confirm before adding
  const { useConfirm } = await import('@/composables/useConfirm');
  const { confirm } = useConfirm();
  
  const durationText = billingPeriod.value === 'yearly' ? t('pricing.twelveMonths') : t('pricing.oneMonth');
  const confirmed = await confirm({
    title: t('pricing.addToCartConfirm'),
    message: t('pricing.addToCartMessage', { plan: plan.name, duration: durationText }),
    type: 'info',
    confirmText: t('pricing.addToCart'),
    cancelText: t('common.cancel')
  });
  
  if (!confirmed) return;

  addingToCart.value = plan.id;

  try {
    // Lấy duration đã chọn
    const durationMonths = selectedDurations.value[plan.id] || 1;
    const durationOption = durationOptions.find(d => d.months === durationMonths);
    const durationText = durationOption?.label || '1 tháng';
    
    await cartStore.addItem(plan.id, 1, durationMonths);
    
    // Show success animation
    successMessage.value = t('pricing.addedToCart', { plan: plan.name });
    showSuccess.value = true;
    
    toast.success(t('pricing.addedToCartSuccess', { plan: plan.name, duration: durationText }));
  } catch (err) {
    const errorMsg = err.message || t('pricing.addToCartError');
    const helpText = ' ' + t('pricing.tryAgainOrContact');
    toast.error(errorMsg + helpText);
  } finally {
    addingToCart.value = null;
  }
};

// Mua ngay (thanh toán trực tiếp)
const handleBuyNow = (plan) => {
  // Kiểm tra đăng nhập
  if (!authStore.user) {
    toast.warning(t('pricing.loginToBuy'));
    router.push('/account');
    return;
  }

  // Gói free không cần thanh toán
  if (plan.slug === 'free') {
    toast.info(t('pricing.usingFreePlan'));
    return;
  }

  // Hiện modal thanh toán
  selectedPlan.value = plan;
  showPaymentModal.value = true;
};

const handlePaymentSuccess = () => {
  showPaymentModal.value = false;
  toast.success(t('pricing.paymentSuccess'));
  router.push('/account');
};

const formatPrice = (price) => {
  if (price === 0) return t('pricing.free');
  return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
};

const getDisplayPrice = (monthlyPrice) => {
  if (monthlyPrice === 0) return 0;
  if (billingPeriod.value === 'yearly') {
    // Yearly with 15% discount
    return Math.round(monthlyPrice * 12 * 0.85);
  }
  return monthlyPrice;
};

const getFeatures = (plan) => {
  const features = [
    {
      text: plan.has_ads ? t('pricing.hasAds') : t('pricing.noAds'),
      checked: !plan.has_ads,
      iconClass: plan.has_ads ? 'text-red-500' : 'text-green-500'
    },
    {
      text: t('pricing.devices', { count: plan.max_devices }),
      checked: true,
      iconClass: 'text-green-500'
    },
    {
      text: plan.can_download ? t('pricing.canDownload') : t('pricing.noDownload'),
      checked: plan.can_download,
      iconClass: plan.can_download ? 'text-green-500' : 'text-gray-500'
    }
  ];
  
  if (plan.early_access) {
    features.push({
      text: t('pricing.earlyAccess'),
      checked: true,
      iconClass: 'text-yellow-500'
    });
  }
  
  return features;
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

const getGlowClass = (slug) => {
  const classes = {
    free: 'bg-gradient-to-r from-gray-600 to-gray-400',
    basic: 'bg-gradient-to-r from-blue-600 to-cyan-500',
    premium: 'bg-gradient-to-r from-red-600 to-yellow-500',
    vip: 'bg-gradient-to-r from-purple-600 to-pink-500'
  };
  return classes[slug] || classes.free;
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

const getQualityClass = (slug) => {
  const classes = {
    free: 'bg-gray-700 text-gray-300',
    basic: 'bg-blue-600/20 text-blue-400 border border-blue-500/50',
    premium: 'bg-red-600/20 text-red-400 border border-red-500/50',
    vip: 'bg-purple-600/20 text-purple-400 border border-purple-500/50'
  };
  return classes[slug] || classes.free;
};

const getButtonClass = (slug) => {
  const classes = {
    free: 'bg-gray-700 text-white',
    basic: 'bg-gradient-to-r from-blue-600 to-cyan-500 text-white shadow-lg shadow-blue-500/50',
    premium: 'bg-gradient-to-r from-red-600 to-yellow-500 text-white shadow-lg shadow-red-500/50',
    vip: 'bg-gradient-to-r from-purple-600 to-pink-500 text-white shadow-lg shadow-purple-500/50'
  };
  return classes[slug] || classes.free;
};
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.8s ease-out;
}

.animate-slide-up {
  animation: slide-up 0.6s ease-out forwards;
  opacity: 0;
}

@keyframes shine {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.animate-shine {
  animation: shine 1.5s ease-in-out infinite;
}
</style>
