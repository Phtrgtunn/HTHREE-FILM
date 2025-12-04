<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[99999] flex flex-col gap-3 pointer-events-none">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="toast-item pointer-events-auto min-w-[320px] max-w-[400px] bg-gray-900/95 backdrop-blur-xl border rounded-xl shadow-2xl overflow-hidden"
          :class="[
            toast.type === 'success' ? 'border-green-500/50 shadow-green-500/20' : 
            toast.type === 'error' ? 'border-red-500/50 shadow-red-500/20' : 
            toast.type === 'warning' ? 'border-yellow-500/50 shadow-yellow-500/20' : 
            'border-blue-500/50 shadow-blue-500/20'
          ]"
        >
          <!-- Progress bar -->
          <div 
            class="absolute top-0 left-0 h-1 transition-all duration-100 ease-linear"
            :class="[
              toast.type === 'success' ? 'bg-green-500' : 
              toast.type === 'error' ? 'bg-red-500' : 
              toast.type === 'warning' ? 'bg-yellow-500' : 
              'bg-blue-500'
            ]"
            :style="{ width: `${toast.progress}%` }"
          ></div>

          <div class="flex items-start gap-3 p-4 pt-5">
            <!-- Icon -->
            <div 
              class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
              :class="[
                toast.type === 'success' ? 'bg-green-500/20' : 
                toast.type === 'error' ? 'bg-red-500/20' : 
                toast.type === 'warning' ? 'bg-yellow-500/20' : 
                'bg-blue-500/20'
              ]"
            >
              <!-- Success Icon -->
              <svg v-if="toast.type === 'success'" class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              
              <!-- Error Icon -->
              <svg v-else-if="toast.type === 'error'" class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
              
              <!-- Warning Icon -->
              <svg v-else-if="toast.type === 'warning'" class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              
              <!-- Info Icon -->
              <svg v-else class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <p 
                class="text-sm font-semibold mb-1"
                :class="[
                  toast.type === 'success' ? 'text-green-400' : 
                  toast.type === 'error' ? 'text-red-400' : 
                  toast.type === 'warning' ? 'text-yellow-400' : 
                  'text-blue-400'
                ]"
              >
                {{ toast.title }}
              </p>
              <p class="text-sm text-gray-300 leading-relaxed">{{ toast.message }}</p>
            </div>

            <!-- Close Button -->
            <button
              @click="removeToast(toast.id)"
              class="flex-shrink-0 w-6 h-6 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors"
            >
              <svg class="w-4 h-4 text-gray-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue';

const toasts = ref([]);
let toastId = 0;

const addToast = (type, title, message, duration = 3000) => {
  const id = ++toastId;
  const toast = {
    id,
    type,
    title,
    message,
    progress: 100
  };
  
  toasts.value.push(toast);
  
  // Progress animation
  const startTime = Date.now();
  const interval = setInterval(() => {
    const elapsed = Date.now() - startTime;
    const remaining = Math.max(0, 100 - (elapsed / duration) * 100);
    
    const toastIndex = toasts.value.findIndex(t => t.id === id);
    if (toastIndex !== -1) {
      toasts.value[toastIndex].progress = remaining;
    }
    
    if (remaining === 0) {
      clearInterval(interval);
    }
  }, 50);
  
  // Auto remove
  setTimeout(() => {
    removeToast(id);
  }, duration);
  
  return id;
};

const removeToast = (id) => {
  const index = toasts.value.findIndex(t => t.id === id);
  if (index !== -1) {
    toasts.value.splice(index, 1);
  }
};

// Expose methods
defineExpose({
  success: (message, duration) => addToast('success', 'Thành công', message, duration),
  error: (message, duration) => addToast('error', 'Lỗi', message, duration),
  warning: (message, duration) => addToast('warning', 'Cảnh báo', message, duration),
  info: (message, duration) => addToast('info', 'Thông tin', message, duration)
});
</script>

<style scoped>
.toast-enter-active {
  animation: toast-in 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-leave-active {
  animation: toast-out 0.2s cubic-bezier(0.4, 0, 1, 1);
}

@keyframes toast-in {
  from {
    opacity: 0;
    transform: translateX(100%) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateX(0) scale(1);
  }
}

@keyframes toast-out {
  from {
    opacity: 1;
    transform: translateX(0) scale(1);
  }
  to {
    opacity: 0;
    transform: translateX(100%) scale(0.9);
  }
}

.toast-move {
  transition: transform 0.3s ease;
}


</style>
