<template>
  <div v-if="hasError" class="min-h-[400px] flex items-center justify-center p-8">
    <div class="max-w-md w-full text-center">
      <!-- Error Icon -->
      <div class="w-24 h-24 mx-auto mb-6 relative">
        <div class="absolute inset-0 bg-red-500/20 rounded-full animate-ping"></div>
        <div class="relative w-24 h-24 bg-gradient-to-br from-red-600 to-red-700 rounded-full flex items-center justify-center">
          <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
      </div>

      <!-- Error Message -->
      <h2 class="text-2xl font-bold text-white mb-3">
        {{ errorTitle }}
      </h2>
      <p class="text-gray-400 mb-6 leading-relaxed">
        {{ errorMessage }}
      </p>

      <!-- Error Details (collapsible) -->
      <details v-if="showDetails && errorDetails" class="mb-6 text-left">
        <summary class="cursor-pointer text-sm text-gray-500 hover:text-gray-400 mb-2">
          Chi tiết lỗi
        </summary>
        <pre class="bg-gray-900 text-red-400 p-4 rounded-lg text-xs overflow-auto max-h-40">{{ errorDetails }}</pre>
      </details>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <button
          @click="handleRetry"
          class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-xl font-bold hover:from-red-700 hover:to-red-800 transition-all hover:scale-105 shadow-lg"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Thử lại
        </button>
        
        <button
          v-if="showHomeButton"
          @click="goHome"
          class="inline-flex items-center justify-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl font-bold hover:bg-gray-700 transition-all border border-gray-700"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          Về trang chủ
        </button>
      </div>
    </div>
  </div>
  
  <slot v-else></slot>
</template>

<script setup>
import { ref, onErrorCaptured } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  errorTitle: {
    type: String,
    default: 'Đã có lỗi xảy ra'
  },
  errorMessage: {
    type: String,
    default: 'Không thể tải nội dung. Vui lòng thử lại sau.'
  },
  showDetails: {
    type: Boolean,
    default: false
  },
  showHomeButton: {
    type: Boolean,
    default: true
  },
  onRetry: {
    type: Function,
    default: null
  }
});

const emit = defineEmits(['retry', 'error']);
const router = useRouter();

const hasError = ref(false);
const errorDetails = ref(null);

// Catch errors from child components
onErrorCaptured((err, instance, info) => {
  console.error('ErrorBoundary caught error:', err);
  hasError.value = true;
  errorDetails.value = `${err.message}\n\nComponent: ${instance?.$options?.name || 'Unknown'}\nInfo: ${info}`;
  emit('error', err);
  return false; // Prevent error from propagating
});

const handleRetry = () => {
  hasError.value = false;
  errorDetails.value = null;
  
  if (props.onRetry) {
    props.onRetry();
  }
  
  emit('retry');
};

const goHome = () => {
  router.push('/');
};

// Expose method to manually trigger error state
defineExpose({
  setError: (error) => {
    hasError.value = true;
    errorDetails.value = error?.message || error;
  },
  reset: () => {
    hasError.value = false;
    errorDetails.value = null;
  }
});
</script>
