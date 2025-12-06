<template>
  <Transition name="slide-up">
    <div
      v-if="show"
      class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-4 bg-gray-800 text-white px-6 py-4 rounded-lg shadow-2xl border border-gray-700 min-w-[320px] max-w-md"
    >
      <!-- Icon -->
      <div class="flex-shrink-0">
        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
      </div>
      
      <!-- Message -->
      <div class="flex-1">
        <p class="text-sm font-medium">{{ message }}</p>
      </div>
      
      <!-- Undo Button -->
      <button
        @click="handleUndo"
        class="flex-shrink-0 bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded-lg font-bold text-sm transition-all hover:scale-105"
      >
        Hoàn tác
      </button>
      
      <!-- Close Button -->
      <button
        @click="handleClose"
        class="flex-shrink-0 text-gray-400 hover:text-white transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
      
      <!-- Progress Bar -->
      <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-700 rounded-b-lg overflow-hidden">
        <div
          class="h-full bg-yellow-500 transition-all ease-linear"
          :style="{ width: `${progress}%` }"
        ></div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch, onUnmounted } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: 'Đã xóa'
  },
  duration: {
    type: Number,
    default: 5000 // 5 seconds
  }
});

const emit = defineEmits(['undo', 'close']);

const progress = ref(100);
let timer = null;
let progressTimer = null;

const handleUndo = () => {
  emit('undo');
  handleClose();
};

const handleClose = () => {
  emit('close');
  clearTimers();
};

const clearTimers = () => {
  if (timer) clearTimeout(timer);
  if (progressTimer) clearInterval(progressTimer);
  progress.value = 100;
};

watch(() => props.show, (newVal) => {
  if (newVal) {
    progress.value = 100;
    
    // Auto close after duration
    timer = setTimeout(() => {
      emit('close');
    }, props.duration);
    
    // Update progress bar
    const interval = 50; // Update every 50ms
    const decrement = (100 / props.duration) * interval;
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

onUnmounted(() => {
  clearTimers();
});
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translate(-50%, 20px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translate(-50%, 20px);
}
</style>
