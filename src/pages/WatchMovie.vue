<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Error State -->
    <div v-if="error" class="flex justify-center items-center h-screen">
      <div class="text-center animate-fade-in">
        <div class="w-20 h-20 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <p class="text-xl font-semibold text-red-400">{{ error }}</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else-if="!movieData || !episodeLink" class="flex justify-center items-center h-screen">
      <div class="text-center">
        <div class="relative w-20 h-20 mx-auto mb-6">
          <div class="absolute inset-0 border-4 border-yellow-400/30 rounded-full"></div>
          <div class="absolute inset-0 border-4 border-yellow-400 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <p class="text-xl font-semibold text-gray-300 animate-pulse">Đang tải phim...</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="animate-fade-in">
      <!-- Video Player Section -->
      <div class="relative bg-black">
        <div class="max-w-[1920px] mx-auto">
          <div class="aspect-video w-full">
            <iframe
              :src="episodeLink"
              :title="`Xem ${movieData.name} ${movieData.type === 'single' ? 'Full' : `Tập ${tap}`}`"
              class="w-full h-full"
              frameborder="0"
              allowfullscreen
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            ></iframe>
          </div>
        </div>
      </div>

      <!-- Info & Episodes Section -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Movie Title & Info -->
        <div class="mb-8 animate-slide-up">
          <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">
            {{ movieData.name }}
          </h1>
          <div class="flex flex-wrap items-center gap-3 text-sm">
            <span class="px-3 py-1 bg-yellow-500 text-black font-bold rounded-full">
              {{ movieData.type === 'single' ? 'Full' : `Tập ${tap}` }}
            </span>
            <span class="text-gray-400">{{ movieData.year }}</span>
            <span class="text-gray-400">•</span>
            <span class="px-2 py-1 border border-gray-600 text-gray-300 rounded">{{ movieData.quality || 'HD' }}</span>
            <span class="text-gray-400">•</span>
            <span class="text-gray-400">{{ movieData.lang || 'Vietsub' }}</span>
          </div>
        </div>

        <!-- Episode Selection (for series) -->
        <div v-if="episodes.length > 1" class="animate-slide-up" style="animation-delay: 0.1s">
          <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Danh sách tập
              </h2>
              <span class="text-gray-400 text-sm">{{ episodes.length }} tập</span>
            </div>
            
            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 xl:grid-cols-12 gap-3 max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-yellow-500 scrollbar-track-gray-700 pr-2">
              <button
                v-for="(ep, index) in episodes"
                :key="index"
                @click="navigateToEpisode(ep.name)"
                :class="{
                  'bg-yellow-500 text-black ring-2 ring-yellow-400 shadow-lg shadow-yellow-500/50 scale-105': getEpisodeNumber(ep.name) === tap,
                  'bg-gray-700 hover:bg-gray-600 text-white hover:scale-105': getEpisodeNumber(ep.name) !== tap,
                }"
                class="aspect-square flex items-center justify-center rounded-lg font-bold transition-all duration-300 hover:shadow-lg group"
                :aria-label="`Xem tập ${ep.name}`"
              >
                <span class="text-sm">{{ getEpisodeNumber(ep.name) }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

// Khởi tạo router và route
const route = useRoute();
const router = useRouter();

// State reactive
const movieData = ref(null);
const episodeLink = ref(null);
const episodes = ref([]);
const error = ref(null);
const filmName = ref(route.params.filmName);
const tap = ref(route.params.tap);

// Lấy số tập từ tên tập (ví dụ: "Tập 1" -> "1")
const getEpisodeNumber = (episodeName) => {
  return episodeName.startsWith('Tập ') ? episodeName.substring(4).trim() : episodeName;
};

// Điều hướng đến tập phim khác
const navigateToEpisode = (episodeName) => {
  const epNum = getEpisodeNumber(episodeName);
  // Sử dụng encodeURIComponent để đảm bảo slug và tập được mã hóa đúng trong URL
  router.push(`/film/${filmName.value}/tap/${encodeURIComponent(epNum)}`);
};

// Fetch dữ liệu phim từ API phimapi.com
const fetchMovieData = async () => {
  // Đặt lại trạng thái lỗi và dữ liệu khi bắt đầu tải
  error.value = null;
  movieData.value = null;
  episodeLink.value = null;
  episodes.value = [];

  try {
    // Gọi API để lấy chi tiết phim (bao gồm cả danh sách tập)
    const res = await axios.get(`https://phimapi.com/phim/${filmName.value}`);
    const movie = res.data.movie;
    // Lấy dữ liệu tập phim từ server đầu tiên (thường là server chính)
    const serverEpisodes = res.data.episodes?.[0]?.server_data || [];

    movieData.value = movie;
    episodes.value = serverEpisodes;

    // Logic để tìm link embed của tập phim hiện tại
    if (movie.type === 'single') {
      // Đối với phim lẻ, tìm tập có tên 'Full' hoặc tương tự
      const full = serverEpisodes.find(ep => ep.name.toLowerCase().includes('full'));
      episodeLink.value = full?.link_embed || null;
    } else {
      // Đối với phim bộ, tìm tập có số khớp với tham số 'tap' từ URL
      const ep = serverEpisodes.find(e => parseInt(getEpisodeNumber(e.name)) === parseInt(tap.value));
      episodeLink.value = ep?.link_embed || null;
    }

    // Xử lý nếu không tìm thấy link tập phim
    if (!episodeLink.value) {
      error.value = 'Không tìm thấy link tập phim hoặc tập phim không tồn tại.';
    }
  } catch (err) {
    console.error('Lỗi khi fetch dữ liệu phim để xem:', err);
    error.value = 'Không tải được dữ liệu phim hoặc tập phim. Vui lòng thử lại sau.';
    // Đảm bảo các biến về null để hiển thị trạng thái lỗi
    movieData.value = null;
    episodeLink.value = null;
    episodes.value = [];
  }
};

// Theo dõi thay đổi của các tham số route (filmName và tap)
// Khi bất kỳ tham số nào thay đổi, hàm fetchMovieData sẽ được gọi lại
watch(
  () => route.params,
  (newParams) => {
    filmName.value = newParams.filmName;
    tap.value = newParams.tap;
    fetchMovieData(); // Gọi lại hàm fetch
  },
  { immediate: true } // Đảm bảo fetch được gọi ngay khi component được mount lần đầu
);
</script>

<style scoped>
/* Animations */
@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}

.animate-slide-up {
  animation: slide-up 0.6s ease-out;
  animation-fill-mode: both;
}

/* Scrollbar tùy chỉnh */
.scrollbar-thin {
  scrollbar-width: thin;
  scrollbar-color: #eab308 #374151;
}

.scrollbar-thin::-webkit-scrollbar {
  width: 8px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: #374151;
  border-radius: 4px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #eab308;
  border-radius: 4px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: #f59e0b;
}

/* Responsive */
@media (max-width: 1024px) {
  .max-w-7xl {
    max-width: 100%;
  }
}

@media (max-width: 640px) {
  .text-4xl {
    font-size: 2rem;
  }
  .text-2xl {
    font-size: 1.5rem;
  }
}
</style>
