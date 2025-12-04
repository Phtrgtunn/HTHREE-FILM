<template>
  <transition name="fade">
    <div v-if="show" class="fixed inset-0 z-[9999] flex items-center justify-center bg-gradient-to-br from-gray-900 via-black to-gray-900">
      <div class="text-center">
        <!-- Logo -->
        <div class="mb-6 animate-bounce-slow">
          <svg class="w-24 h-24 mx-auto text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
          </svg>
        </div>

        <!-- Brand Name -->
        <h1 class="text-5xl font-black text-white mb-2 tracking-wider" style="text-shadow: 0 0 20px rgba(250, 204, 21, 0.5);">
          HTHREE-FILM
        </h1>
        
        <!-- Tagline -->
        <p class="text-gray-400 text-lg mb-8 font-medium">
          Xem Phim Miễn Phí Cực Nhanh, Chất Lượng Cao Và Cập Nhật Liên Tục
        </p>

        <!-- Loading Animation -->
        <div class="flex justify-center gap-2">
          <div class="w-3 h-3 bg-yellow-400 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
          <div class="w-3 h-3 bg-yellow-400 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
          <div class="w-3 h-3 bg-yellow-400 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const show = ref(true);

onMounted(() => {
  // Ẩn splash screen sau 2.5 giây
  setTimeout(() => {
    show.value = false;
  }, 2500);
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes bounce-slow {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-bounce-slow {
  animation: bounce-slow 2s infinite;
}
</style>
