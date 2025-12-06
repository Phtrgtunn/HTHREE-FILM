<template>
  <Transition name="slide-down">
    <div v-if="show" class="fixed top-0 left-0 right-0 z-[9999] bg-red-600 text-white px-4 py-3 shadow-lg">
      <div class="container mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
          <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"/>
          </svg>
          <div>
            <p class="font-bold text-sm">Mất kết nối Internet</p>
            <p class="text-xs opacity-90">Vui lòng kiểm tra kết nối mạng của bạn</p>
          </div>
        </div>
        <button
          v-if="showRetry"
          @click="$emit('retry')"
          class="bg-white text-red-600 px-4 py-1.5 rounded-lg text-sm font-bold hover:bg-gray-100 transition-colors"
        >
          Thử lại
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  },
  showRetry: {
    type: Boolean,
    default: false
  }
});

defineEmits(['retry']);
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  transform: translateY(-100%);
  opacity: 0;
}

.slide-down-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}
</style>
