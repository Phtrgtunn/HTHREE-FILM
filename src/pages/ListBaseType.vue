<template>
  <div class="min-h-screen bg-black text-white">
    <!-- Hero Header -->
    <div class="relative bg-gradient-to-b from-gray-900 to-black pt-20 pb-12 px-4">
      <div class="max-w-7xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-6 animate-fade-in">
          <router-link to="/home" class="text-gray-400 hover:text-yellow-400 transition-colors">
            Trang chủ
          </router-link>
          <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
          <span class="text-yellow-400 font-medium">{{ pageTitle }}</span>
        </nav>

        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold mb-4 animate-slide-up">
          <span class="bg-gradient-to-r from-yellow-400 to-yellow-600 bg-clip-text text-transparent">
            {{ pageTitle }}
          </span>
        </h1>
        <p class="text-gray-400 text-lg animate-slide-up animation-delay-100">
          Khám phá bộ sưu tập phim đặc sắc
        </p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="space-y-8">
        <!-- Filter Skeleton -->
        <div class="bg-gray-900/50 rounded-xl p-6 animate-pulse">
          <div class="flex flex-wrap gap-4">
            <div class="h-10 bg-gray-800 rounded-lg w-40"></div>
            <div class="h-10 bg-gray-800 rounded-lg w-40"></div>
            <div class="h-10 bg-gray-800 rounded-lg w-32"></div>
          </div>
        </div>

        <!-- Movie Grid Skeleton -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
          <div v-for="i in 10" :key="i" class="animate-pulse">
            <div class="aspect-[2/3] bg-gray-800 rounded-lg mb-2"></div>
            <div class="h-4 bg-gray-800 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-gray-800 rounded w-1/2"></div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-20 animate-fade-in">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-500/10 rounded-full mb-4">
          <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-red-400 mb-2">Có lỗi xảy ra</h3>
        <p class="text-gray-400">{{ error }}</p>
      </div>

      <!-- Main Content -->
      <template v-else>
        <!-- Modern Filter Bar -->
        <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 mb-8 border border-gray-800 shadow-xl animate-fade-in">
          <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Bộ Lọc
          </h2>
          <div class="flex flex-wrap gap-4">
            <!-- Category Filter -->
            <div class="relative">
              <select 
                v-model="selectedCategory"
                @change="updateFilters"
                class="appearance-none bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 pr-10 text-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 transition-all hover:border-gray-600 cursor-pointer min-w-[180px]"
              >
                <option value="">Tất cả thể loại</option>
                <option v-for="cat in categoryStore.getAllCategories" :key="cat.slug" :value="cat.slug">
                  {{ cat.name }}
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>

            <!-- Country Filter -->
            <div class="relative">
              <select 
                v-model="selectedCountry"
                @change="updateFilters"
                class="appearance-none bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 pr-10 text-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 transition-all hover:border-gray-600 cursor-pointer min-w-[180px]"
              >
                <option value="">Tất cả quốc gia</option>
                <option v-for="country in countryStore.getAllCountries" :key="country.slug" :value="country.slug">
                  {{ country.name }}
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>

            <!-- Year Filter -->
            <div class="relative">
              <select 
                v-model="selectedYear"
                @change="updateFilters"
                class="appearance-none bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 pr-10 text-white focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 transition-all hover:border-gray-600 cursor-pointer min-w-[140px]"
              >
                <option value="">Tất cả năm</option>
                <option v-for="year in availableYears" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>

            <!-- Reset Button -->
            <button 
              v-if="selectedCategory || selectedCountry || selectedYear"
              @click="resetFilters"
              class="bg-gray-800 hover:bg-yellow-400/10 border border-gray-700 hover:border-yellow-400 rounded-lg px-4 py-2.5 text-gray-300 hover:text-yellow-400 transition-all flex items-center gap-2 group"
            >
              <svg class="w-4 h-4 transition-transform group-hover:rotate-90 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Xóa bộ lọc
            </button>
          </div>

          <!-- Results Count -->
          <div class="mt-4 pt-4 border-t border-gray-800">
            <p class="text-sm text-gray-400">
              Tìm thấy <span class="text-yellow-400 font-semibold">{{ movies.length }}</span> phim
              <span v-if="totalPage > 1"> - Trang {{ currentPage }}/{{ totalPage }}</span>
            </p>
          </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
          <!-- Movie Grid -->
          <div class="flex-1">
            <div v-if="movies && movies.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              <MovieCardRecommended
                v-for="(item, index) in movies"
                :key="item.slug || item.id || index"
                :movie="item"
                :trending="false"
                class="animate-fade-in-up"
                :style="{ animationDelay: `${index * 50}ms` }"
              />
            </div>
            <div v-else class="text-center py-20 animate-fade-in">
              <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-800 rounded-full mb-4">
                <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-400 mb-2">Không tìm thấy phim</h3>
              <p class="text-gray-500">Thử thay đổi bộ lọc để xem thêm phim</p>
            </div>
          </div>

          <!-- Ranking Sidebar -->
          <aside class="lg:w-80 flex-shrink-0">
            <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 sticky top-24 animate-fade-in">
              <div class="flex items-center gap-2 mb-6">
                <div class="w-8 h-8 bg-yellow-400/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-white">Bảng Xếp Hạng</h3>
              </div>

              <div v-if="recommendedMoviesStore.getRecommended.length > 0" class="space-y-3 max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-yellow-500 scrollbar-track-gray-800">
                <Rank
                  v-for="(item, index) in recommendedMoviesStore.getRecommended.slice(0, 10)"
                  :key="item.slug || item.id || index"
                  :trendingMovie="item"
                  :index="index"
                  class="animate-fade-in"
                  :style="{ animationDelay: `${index * 100}ms` }"
                />
              </div>
              <p v-else class="text-gray-500 text-center py-8">Đang tải...</p>
            </div>
          </aside>
        </div>

        <!-- Modern Pagination -->
        <div v-if="totalPage > 1" class="flex justify-center mt-12 animate-fade-in">
          <nav class="inline-flex items-center gap-2 bg-gray-900/50 backdrop-blur-sm rounded-xl p-2 border border-gray-800">
            <!-- Previous Button -->
            <button
              @click="handlePageChange(currentPage - 1)"
              :disabled="currentPage === 1"
              :class="[
                'p-2 rounded-lg transition-all',
                currentPage === 1 
                  ? 'text-gray-600 cursor-not-allowed' 
                  : 'text-gray-400 hover:text-yellow-400 hover:bg-gray-800'
              ]"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>

            <!-- Page Numbers -->
            <template v-for="(page, index) in renderPagination()" :key="index">
              <span v-if="page === 'ellipsis'" class="px-3 text-gray-600">...</span>
              <button
                v-else
                @click="handlePageChange(page)"
                :class="[
                  'min-w-[40px] h-10 rounded-lg font-medium transition-all',
                  currentPage === page
                    ? 'bg-yellow-400 text-black shadow-lg shadow-yellow-400/50'
                    : 'text-gray-400 hover:text-white hover:bg-gray-800'
                ]"
              >
                {{ page }}
              </button>
            </template>

            <!-- Next Button -->
            <button
              @click="handlePageChange(currentPage + 1)"
              :disabled="currentPage === totalPage"
              :class="[
                'p-2 rounded-lg transition-all',
                currentPage === totalPage
                  ? 'text-gray-600 cursor-not-allowed'
                  : 'text-gray-400 hover:text-yellow-400 hover:bg-gray-800'
              ]"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </nav>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getMovieList, mapMovieData } from '@/services/movieApi';
import MovieCardRecommended from '@/shared/MovieCardRecommended.vue';
import Rank from '@/shared/Rank.vue';
import { useRecommendedMoviesStore } from '@/stores/RecommendedMovies/recommendedMovies.js';
import { useCategoryStore } from '@/stores/Category/category.js';
import { useCountryStore } from '@/stores/Country/country.js';

const route = useRoute();
const router = useRouter();

const recommendedMoviesStore = useRecommendedMoviesStore();
const categoryStore = useCategoryStore();
const countryStore = useCountryStore();

const movies = ref([]);
const totalPage = ref(1);
const loading = ref(true);
const error = ref(null);

const currentPage = ref(parseInt(route.params.pageNumber) || 1);
const selectedCategory = ref(route.query.category || '');
const selectedCountry = ref(route.query.country || '');
const selectedYear = ref(route.query.year || '');

const MOVIES_PER_PAGE = 20;

// Get current base path and slug from route
const currentBasePath = computed(() => {
  const path = route.path;
  if (path.includes('/phim-le')) return 'phim-le';
  if (path.includes('/phim-bo')) return 'phim-bo';
  if (path.includes('/le')) return 'le';
  if (path.includes('/bo')) return 'bo';
  return 'phim-le';
});

const currentBaseSlug = computed(() => {
  return route.params.typeSlug || currentBasePath.value;
});

// Page title based on filters
const pageTitle = computed(() => {
  const parts = [];
  
  const slug = currentBaseSlug.value;
  if (slug === 'phim-le' || slug === 'le') parts.push('Phim Lẻ');
  else if (slug === 'phim-bo' || slug === 'bo') parts.push('Phim Bộ');
  else if (slug === 'tv-shows') parts.push('TV Shows');
  else if (slug === 'hoat-hinh') parts.push('Hoạt Hình');
  
  if (selectedCategory.value) {
    const cat = categoryStore.getAllCategories.find(c => c.slug === selectedCategory.value);
    if (cat) parts.push(cat.name);
  }
  
  if (selectedCountry.value) {
    const country = countryStore.getAllCountries.find(c => c.slug === selectedCountry.value);
    if (country) parts.push(country.name);
  }
  
  if (selectedYear.value) {
    parts.push(`Năm ${selectedYear.value}`);
  }
  
  return parts.length > 0 ? parts.join(' - ') : 'Danh Sách Phim';
});

// Available years
const availableYears = computed(() => {
  const currentYear = new Date().getFullYear();
  const years = [];
  for (let i = currentYear; i >= currentYear - 20; i--) {
    years.push(i);
  }
  return years;
});

// Update filters
const updateFilters = () => {
  const newQuery = {};
  if (selectedCategory.value) newQuery.category = selectedCategory.value;
  if (selectedCountry.value) newQuery.country = selectedCountry.value;
  if (selectedYear.value) newQuery.year = selectedYear.value;

  router.push({
    path: `/${currentBasePath.value}/page/1`,
    query: newQuery
  });
};

// Reset filters
const resetFilters = () => {
  selectedCategory.value = '';
  selectedCountry.value = '';
  selectedYear.value = '';
  
  router.push({
    path: `/${currentBasePath.value}/page/1`,
    query: {}
  });
};

// Fetch movies
const fetchMovies = async () => {
  loading.value = true;
  error.value = null;
  movies.value = [];

  try {
    const params = {
      page: currentPage.value,
      limit: MOVIES_PER_PAGE,
      sort_field: 'modified.time',
      sort_type: 'desc'
    };

    // Add type filter
    const slug = currentBaseSlug.value;
    if (slug === 'phim-le' || slug === 'le') {
      params.type = 'single';
    } else if (slug === 'phim-bo' || slug === 'bo') {
      params.type = 'series';
    } else if (slug === 'tv-shows') {
      params.type = 'tvshows';
    } else if (slug === 'hoat-hinh') {
      params.type = 'hoathinh';
    }

    // Add additional filters
    if (selectedCategory.value) params.category = selectedCategory.value;
    if (selectedCountry.value) params.country = selectedCountry.value;
    if (selectedYear.value) params.year = parseInt(selectedYear.value);

    const response = await getMovieList(params);

    if (response.status && response.data) {
      movies.value = (response.data.items || []).map(mapMovieData);
      
      // Try multiple ways to get totalPages
      const pagination = response.data.params?.pagination;
      if (pagination) {
        totalPage.value = pagination.totalPages || pagination.total_page || 1;
      } else if (response.data.pagination) {
        totalPage.value = response.data.pagination.totalPages || response.data.pagination.total_page || 1;
      } else {
        totalPage.value = 1;
      }
    } else {
      movies.value = [];
      totalPage.value = 1;
    }
  } catch (err) {
    console.error('Error fetching movies:', err);
    error.value = 'Không thể tải dữ liệu phim. Vui lòng thử lại sau.';
    movies.value = [];
    totalPage.value = 1;
  } finally {
    loading.value = false;
  }
};

// Handle page change
const handlePageChange = (page) => {
  if (page >= 1 && page <= totalPage.value) {
    const newQuery = {};
    if (selectedCategory.value) newQuery.category = selectedCategory.value;
    if (selectedCountry.value) newQuery.country = selectedCountry.value;
    if (selectedYear.value) newQuery.year = selectedYear.value;

    router.push({
      path: `/${currentBasePath.value}/page/${page}`,
      query: newQuery
    });
  }
};

// Render pagination
const renderPagination = () => {
  const pages = new Set();
  const delta = 2;

  pages.add(1);
  if (totalPage.value > 1) pages.add(totalPage.value);

  for (let i = currentPage.value - delta; i <= currentPage.value + delta; i++) {
    if (i >= 1 && i <= totalPage.value) pages.add(i);
  }

  const sortedPages = Array.from(pages).sort((a, b) => a - b);
  const finalPages = [];

  for (let i = 0; i < sortedPages.length; i++) {
    finalPages.push(sortedPages[i]);
    if (i < sortedPages.length - 1 && sortedPages[i + 1] - sortedPages[i] > 1) {
      finalPages.push('ellipsis');
    }
  }
  return finalPages;
};

// Lifecycle and watchers
onMounted(async () => {
  // Load stores
  if (recommendedMoviesStore.getRecommended.length === 0) {
    await recommendedMoviesStore.getRecommendedMovies();
  }
  if (categoryStore.getAllCategories.length === 0) {
    await categoryStore.getCategory();
  }
  if (countryStore.getAllCountries.length === 0) {
    await countryStore.getCountry();
  }

  // Watch route changes
  watch(
    () => route.params,
    (newParams) => {
      currentPage.value = parseInt(newParams.pageNumber) || 1;
      if (categoryStore.getAllCategories.length > 0 && countryStore.getAllCountries.length > 0) {
        fetchMovies();
      }
    },
    { immediate: true }
  );

  watch(
    () => route.query,
    (newQuery) => {
      selectedCategory.value = newQuery.category || '';
      selectedCountry.value = newQuery.country || '';
      selectedYear.value = newQuery.year || '';
      if (categoryStore.getAllCategories.length > 0 && countryStore.getAllCountries.length > 0) {
        fetchMovies();
      }
    },
    { deep: true }
  );

  // Initial fetch
  if (categoryStore.getAllCategories.length > 0 && countryStore.getAllCountries.length > 0) {
    fetchMovies();
  }
});
</script>

<style scoped>
/* Select Dropdown Styling */
select {
  background-image: none;
}

select option {
  background-color: #1f2937;
  color: white;
  padding: 12px 16px;
}

select option:hover {
  background-color: #374151;
}

select option:checked {
  background-color: #eab308;
  color: black;
  font-weight: 600;
}

/* Scrollbar */
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: #1f2937;
  border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #eab308;
  border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: #f59e0b;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
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
  animation: fadeIn 0.5s ease-out;
}

.animate-slide-up {
  animation: slideUp 0.6s ease-out;
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out;
  animation-fill-mode: both;
}

.animation-delay-100 {
  animation-delay: 100ms;
}

/* Responsive */
@media (max-width: 1024px) {
  .lg\:grid-cols-5 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .md\:grid-cols-4 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 640px) {
  .sm\:grid-cols-3 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
