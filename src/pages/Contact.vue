```vue
<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white py-16 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <div class="max-w-7xl mx-auto">
      <!-- Header with Animation -->
      <div class="text-center mb-12 animate-fade-in">
        <h1 class="text-5xl sm:text-6xl font-extrabold text-yellow-400 uppercase tracking-wide bg-gradient-to-r from-yellow-400 to-yellow-300 bg-clip-text text-transparent">
          HTHREE
        </h1>
        <p class="mt-4 text-lg sm:text-xl text-gray-300">Nền tảng xem phim đỉnh cao - Chất lượng Full HD, cập nhật mỗi ngày</p>
      </div>

      <!-- Main Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Image Card -->
        <div class="relative group">
          <div class="rounded-xl overflow-hidden shadow-2xl transition-all duration-300 group-hover:shadow-yellow-400/50">
            <img
              src="https://univn.vn/wp-content/uploads/2020/12/thiet-ke-web-xem-phim.jpg"
              alt="Nội thất rạp HTHREE"
              class="w-full h-96 object-cover transform transition-transform duration-300 group-hover:scale-105"
            />
          </div>
          <div class="absolute inset-0 bg-gray-900 bg-opacity-10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <p class="text-yellow-400 text-xl font-semibold bg-black bg-opacity-50 px-4 py-2 rounded">Khám phá không gian xem phim đẳng cấp</p>
          </div>
        </div>

        <!-- Info Card -->
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg space-y-6">
          <p class="text-xl leading-relaxed font-semibold text-gray-200">
            HTHREE mang đến trải nghiệm xem phim trực tuyến với hơn 10.000+ bộ phim chất lượng Full HD, giao diện thân thiện, và cập nhật đa dạng mỗi ngày. Chúng tôi cam kết mang đến sự giải trí đỉnh cao cho mọi gia đình và cá nhân.
          </p>
          <ul class="list-none space-y-3 text-gray-300 text-base">
            <li class="flex items-center gap-2"><span class="text-yellow-400">•</span> Quảng cáo trên màn hình, tiếp cận hàng ngàn khán giả</li>
            <li class="flex items-center gap-2"><span class="text-yellow-400">•</span> Tổ chức sự kiện thương hiệu hoặc fan offline chuyên nghiệp</li>
            <li class="flex items-center gap-2"><span class="text-yellow-400">•</span> Xem phim bom tấn riêng tư cùng người thân, bạn bè</li>
            <li class="flex items-center gap-2"><span class="text-yellow-400">•</span> Đặt địa điểm cho họp báo, đào tạo, hoặc sự kiện đặc biệt</li>
          </ul>
        </div>
      </div>

      <!-- Contact Card -->
      <div class="mt-12 bg-gray-800 p-6 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-yellow-400 mb-4">Liên Hệ Với HTHREE</h2>
        <p class="text-gray-300 mb-6">Hãy kết nối ngay hôm nay để nhận được sự hỗ trợ tốt nhất!</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="flex items-center gap-3 p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <div>
              <p class="text-sm text-gray-400">Email</p>
              <a href="mailto:CS@HTHREE.VN" class="text-blue-400 hover:underline">CS@HTHREE.VN</a>
            </div>
          </div>
          <div class="flex items-center gap-3 p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <div>
              <p class="text-sm text-gray-400">Hotline</p>
              <a href="tel:0123.456.789" class="text-blue-400 hover:underline">0123.456.789</a>
            </div>
          </div>
        </div>
        <button
          @click="openContactForm"
          class="mt-6 w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-1"
        >
          Liên Hệ Ngay
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const openContactForm = () => {
  alert('Tính năng form liên hệ đang được phát triển!');
};
</script>

<style scoped>
/* Responsive */
@media (max-width: 1024px) {
  .grid-cols-2 {
    grid-template-columns: 1fr;
  }
  .text-5xl {
    font-size: 3.5rem;
  }
  .text-6xl {
    font-size: 4rem;
  }
  .h-96 {
    height: 60vh;
  }
}

@media (max-width: 640px) {
  .text-5xl {
    font-size: 2.5rem;
  }
  .text-6xl {
    font-size: 3rem;
  }
  .h-96 {
    height: 50vh;
  }
  .max-w-7xl {
    max-width: 100%;
  }
}
</style>
```