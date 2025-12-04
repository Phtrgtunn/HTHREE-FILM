<template>
  <Teleport to="body">
    <Transition name="notification">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-start justify-end p-6"
        @click.self="close"
      >
        <!-- Notification Card -->
        <div class="w-full max-w-md bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl border overflow-hidden" :class="borderClass">
          <!-- Progress Bar -->
          <div class="h-1 bg-gray-800">
            <div
              class="h-full transition-all duration-[5000ms] ease-linear"
              :class="progressClass"
              :style="{ width: progress + '%' }"
            ></div>
          </div>

          <div class="p-6">
            <div class="flex items-start gap-4">
              <!-- Icon -->
              <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="iconBgClass">
                  <!-- Success Icon -->
                  <svg v-if="type === 'success'" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  
                  <!-- Error Icon -->
                  <svg v-else-if="type === 'error'" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                  </svg>
                  
                  <!-- Warning Icon -->
                  <svg v-else-if="type === 'warning'" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  
                  <!-- Info Icon -->
                  <svg v-else class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <h3 class="text-white font-bold text-lg mb-1">{{ title }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed">{{ message }}</p>
              </div>

              <!-- Close Button -->
              <button
                @click="close"
                class="flex-shrink-0 text-gray-400 hover:text-white transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'info', // success, error, warning, info
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    default: 'Thông báo'
  },
  message: {
    type: String,
    default: ''
  },
  duration: {
    type: Number,
    default: 5000 // 5 seconds
  }
});

const emit = defineEmits(['update:modelValue']);

const progress = ref(100);
let timer = null;
let progressTimer = null;

const borderClass = computed(() => {
  const classes = {
    success: 'border-green-500/50',
    error: 'border-red-500/50',
    warning: 'border-yellow-500/50',
    info: 'border-blue-500/50'
  };
  return classes[props.type];
});

const iconBgClass = computed(() => {
  const classes = {
    success: 'bg-gradient-to-br from-green-600 to-green-700',
    error: 'bg-gradient-to-br from-red-600 to-red-700',
    warning: 'bg-gradient-to-br from-yellow-600 to-orange-500',
    info: 'bg-gradient-to-br from-blue-600 to-blue-700'
  };
  return classes[props.type];
});

const progressClass = computed(() => {
  const classes = {
    success: 'bg-gradient-to-r from-green-600 to-green-500',
    error: 'bg-gradient-to-r from-red-600 to-red-500',
    warning: 'bg-gradient-to-r from-yellow-600 to-orange-500',
    info: 'bg-gradient-to-r from-blue-600 to-blue-500'
  };
  return classes[props.type];
});

const close = () => {
  emit('update:modelValue', false);
  clearTimers();
};

const clearTimers = () => {
  if (timer) clearTimeout(timer);
  if (progressTimer) clearInterval(progressTimer);
  progress.value = 100;
};

watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    progress.value = 100;
    
    // Auto close after duration
    timer = setTimeout(() => {
      close();
    }, props.duration);
    
    // Progress bar animation
    const interval = 50; // Update every 50ms
    const steps = props.duration / interval;
    const decrement = 100 / steps;
    
    progressTimer = setInterval(() => {
      progress.value -= decrement;
      if (progress.value <= 0) {
        clearInterval(progressTimer);
      }
    }, interval);
  } else {
    clearTimers();
  }
});
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100px);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100px) scale(0.9);
}
</style>
