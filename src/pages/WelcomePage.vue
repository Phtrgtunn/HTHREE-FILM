<template>
  <div class="min-h-screen bg-black relative overflow-hidden flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <!-- Animated Background -->
    <div class="absolute inset-0 opacity-20">
      <div class="absolute top-0 left-0 w-96 h-96 bg-yellow-400 rounded-full filter blur-3xl animate-blob"></div>
      <div class="absolute top-0 right-0 w-96 h-96 bg-yellow-600 rounded-full filter blur-3xl animate-blob animation-delay-2000"></div>
      <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-yellow-500 rounded-full filter blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <!-- Film Strip Decoration -->
    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-transparent via-yellow-400 to-transparent"></div>
    <div class="absolute bottom-0 left-0 w-full h-2 bg-gradient-to-r from-transparent via-yellow-400 to-transparent"></div>

    <main class="relative z-10 bg-gray-900/80 backdrop-blur-xl p-6 sm:p-8 rounded-2xl shadow-2xl border border-yellow-400/20 max-w-2xl w-full text-center transform opacity-0 scale-95 animate-fade-in-up">
      <div class="space-y-5">
        <!-- Logo Icon -->
        <div class="flex justify-center mb-3">
          <div class="relative">
            <svg class="w-12 h-12 text-yellow-400 animate-pulse-slow" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
            </svg>
            <div class="absolute -inset-2 bg-yellow-400/20 rounded-full blur-xl"></div>
          </div>
        </div>

        <!-- Header -->
        <div class="space-y-2">
          <p class="text-[10px] sm:text-xs font-medium text-gray-400 uppercase tracking-[0.2em]">Ủy Ban Nhân Dân</p>
          <p class="text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-[0.15em]">Sở Giáo Dục và Đào Tạo</p>
          <h1 class="text-sm sm:text-base font-bold text-white uppercase tracking-wide">
            Trường Cao Đẳng Kinh Tế TP.HCM
          </h1>
          <p class="text-xs sm:text-sm text-yellow-400 font-semibold italic">Dự Án Web Thương Mại Điện Tử</p>
        </div>

        <!-- Team Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-6 border-t border-b border-yellow-400/20 py-6">
          <div class="text-left space-y-2">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
              </svg>
              <p class="text-xs sm:text-sm font-bold text-yellow-400 uppercase">Giảng Viên Hướng Dẫn</p>
            </div>
            <p class="text-xs sm:text-sm text-gray-200 hover:text-yellow-400 transition-colors cursor-pointer pl-6">Phạm Đặng Phương</p>
          </div>
          
          <div class="text-left space-y-2">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
              </svg>
              <p class="text-xs sm:text-sm font-bold text-yellow-400 uppercase">Sinh Viên Thực Hiện</p>
            </div>
            <p class="text-xs sm:text-sm text-gray-200 hover:text-yellow-400 transition-colors cursor-pointer pl-6">Phạm Trung Tuấn</p>
            <p class="text-xs sm:text-sm text-gray-200 hover:text-yellow-400 transition-colors cursor-pointer pl-6">Lê Anh Văn</p>
            <p class="text-xs sm:text-sm text-gray-200 hover:text-yellow-400 transition-colors cursor-pointer pl-6">Trần Lạc Khả Tú</p>
            <p class="text-xs sm:text-sm text-gray-200 hover:text-yellow-400 transition-colors cursor-pointer pl-6">Nguyễn Lý Anh Thư</p>
          </div>
        </div>

        <!-- Brand Name -->
        <div class="pt-4">
          <h2 class="text-4xl sm:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 drop-shadow-2xl tracking-tight">
            HTHREE
          </h2>
          <p class="text-base sm:text-lg text-yellow-400/80 font-light tracking-[0.4em] mt-1">FILM</p>
        </div>
        
        <!-- CTA Button -->
        <button
          @click="navigateToHomepage"
          class="mt-8 px-8 py-3 bg-yellow-400 text-black font-bold text-base rounded-full shadow-xl shadow-yellow-400/50
                 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-yellow-400/70 active:scale-95
                 focus:outline-none focus:ring-4 focus:ring-yellow-400/50 relative overflow-hidden group"
          aria-label="Vào Trang Web Chính"
        >
          <span class="relative z-10 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
            </svg>
            Khám Phá Ngay
          </span>
          <span class="absolute inset-0 bg-yellow-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
        </button>

        <!-- Footer Note -->
        <p class="text-[10px] text-gray-500 mt-6">© 2025 HTHREE Film</p>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const navigateToHomepage = () => {
  router.push('/home'); // Điều hướng đến trang chủ thực sự của ứng dụng
};
</script>

<style scoped>
/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes blob {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  25% {
    transform: translate(20px, -50px) scale(1.1);
  }
  50% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  75% {
    transform: translate(50px, 50px) scale(1.05);
  }
}

@keyframes pulse-slow {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

.animate-fade-in-up {
  animation: fadeInUp 1s ease-out forwards;
}

.animate-blob {
  animation: blob 20s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

.animate-pulse-slow {
  animation: pulse-slow 3s ease-in-out infinite;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .tracking-\[0\.3em\] {
    letter-spacing: 0.2em;
  }
  .tracking-\[0\.25em\] {
    letter-spacing: 0.15em;
  }
  .tracking-\[0\.5em\] {
    letter-spacing: 0.3em;
  }
}
</style>
