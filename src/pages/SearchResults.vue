<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Search Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">
          Kết quả tìm kiếm: <span class="text-yellow-400">{{ searchKeyword }}</span>
        </h1>
        <p v-if="!loading && movies.length > 0" class="text-gray-400">
          Tìm thấy {{ totalItems }} kết quả
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-96">
        <div class="text-center">
          <svg
            class="animate-spin h-12 w-12 text-yellow-400 mx-auto mb-4"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" />
            <path
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              class="opacity-75"
            />
          </svg>
          <p class="text-xl font-semibold text-gray-300">Đang tìm kiếm...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center text-red-400 text-lg py-12">
        <p class="text-xl font-semibold">{{ error }}</p>
      </div>

      <!-- No Results -->
      <div v-else-if="movies.length === 0" class="text-center py-12">
        <svg class="w-24 h-24 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <p class="text-xl text-gray-400">Không tìm thấy kết quả nào cho "{{ searchKeyword }}"</p>
        <p class="text-gray-500 mt-2">Thử tìm kiếm với từ khóa khác</p>
      </div>

      <!-- Results List -->
      <div v-else>
        <div class="space-y-4 mb-8">
          <router-link
            v-for="(movie, index) in movies"
            :key="movie.slug || movie.id || index"
            :to="`/film/${movie.slug}`"
            class="flex gap-4 bg-gray-800 rounded-lg overflow-hidden hover:bg-gray-750 transition-colors group"
          >
            <!-- Thumbnail bên trái -->
            <div class="flex-shrink-0 w-32 sm:w-40 md:w-48">
              <img
                :src="movie.backdrop_path || movie.poster_path || 'https://placehold.co/300x169/1a1a1a/fff?text=No+Image'"
                :alt="movie.title"
                class="w-full h-full object-cover aspect-video"
                @error="(e) => e.target.src = 'https://placehold.co/300x169/1a1a1a/fff?text=Error'"
              />
            </div>

            <!-- Info bên phải -->
            <div class="flex-1 py-3 pr-4 min-w-0">
              <h3 class="text-lg font-bold text-white group-hover:text-yellow-400 transition-colors truncate">
                {{ movie.title }}
              </h3>
              <p class="text-sm text-gray-400 truncate mb-2">
                {{ movie.original_title }}
              </p>
              
              <div class="flex flex-wrap gap-2 text-xs mb-2">
                <span v-if="movie.year" class="bg-blue-500 text-white px-2 py-1 rounded font-medium">
                  {{ movie.year }}
                </span>
                <span v-if="movie.quality" class="bg-yellow-500 text-black px-2 py-1 rounded font-semibold">
                  {{ movie.quality }}
                </span>
                <span v-if="movie.lang" class="bg-purple-500 text-white px-2 py-1 rounded font-medium">
                  {{ movie.lang }}
                </span>
                <span v-if="movie.episode_current" class="bg-green-500 text-white px-2 py-1 rounded font-medium">
                  {{ movie.episode_current }}
                </span>
              </div>

              <p v-if="movie.overview" class="text-sm text-gray-300 line-clamp-2 hidden sm:block">
                {{ movie.overview }}
              </p>
            </div>
          </router-link>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center mt-10">
          <nav role="navigation" aria-label="pagination" class="mx-auto flex w-full justify-center">
            <ul class="flex flex-row items-center gap-2">
              <li>
                <button
                  @click="handlePageChange(currentPage - 1)"
                  :disabled="currentPage === 1"
                  :class="{ 'opacity-50 pointer-events-none': currentPage === 1 }"
                  class="px-3 py-1.5 rounded-full text-sm font-medium transition-all hover:bg-gray-700"
                  aria-label="Previous page"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6"/>
                  </svg>
                </button>
              </li>

              <li v-for="(page, index) in renderPagination()" :key="index">
                <span v-if="page === 'ellipsis'" class="flex size-9 items-center justify-center text-gray-400">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/>
                  </svg>
                </span>
                <button
                  v-else
                  @click="handlePageChange(page)"
                  :class="{ 'bg-yellow-400 text-black': currentPage === page, 'hover:bg-gray-700': currentPage !== page }"
                  class="inline-flex items-center justify-center whitespace-nowrap rounded-full text-sm font-medium transition-all h-9 px-4 py-1"
                  :aria-label="`Page ${page}`"
                >
                  {{ page }}
                </button>
              </li>

              <li>
                <button
                  @click="handlePageChange(currentPage + 1)"
                  :disabled="currentPage === totalPages"
                  :class="{ 'opacity-50 pointer-events-none': currentPage === totalPages }"
                  class="px-3 py-1.5 rounded-full text-sm font-medium transition-all hover:bg-gray-700"
                  aria-label="Next page"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6"/>
                  </svg>
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { searchMovies, mapMovieData } from '@/services/movieApi';
import MovieCardRecommended from '@/shared/MovieCardRecommended.vue';

const route = useRoute();
const router = useRouter();

const searchKeyword = ref('');
const movies = ref([]);
const loading = ref(false);
const error = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const totalItems = ref(0);

const ITEMS_PER_PAGE = 20;

// Fetch search results
const fetchSearchResults = async () => {
  let keyword = route.query.q;
  
  if (!keyword || !keyword.trim()) {
    movies.value = [];
    error.value = 'Vui lòng nhập từ khóa tìm kiếm';
    return;
  }

  // Normalize keyword: trim và loại bỏ khoảng trắng thừa
  keyword = keyword.trim().replace(/\s+/g, ' ');
  
  searchKeyword.value = keyword;
  loading.value = true;
  error.value = null;

  console.log('🔍 Searching for:', keyword, 'Page:', currentPage.value);

  try {
    const response = await searchMovies(keyword, {
      page: currentPage.value,
      limit: ITEMS_PER_PAGE
    });

    console.log('🔍 Search response:', response);
    console.log('🔍 Response status:', response.status);
    console.log('🔍 Response data:', response.data);

    // API có thể trả về status: 'success' hoặc status: true
    if ((response.status === 'success' || response.status === true) && response.data) {
      // Map và fix URL ảnh
      movies.value = (response.data.items || []).map(item => {
        const mapped = mapMovieData(item);
        
        // Fix URL ảnh nếu thiếu domain
        if (mapped.poster_path && !mapped.poster_path.startsWith('http')) {
          mapped.poster_path = `https://phimimg.com/${mapped.poster_path}`;
        }
        if (mapped.backdrop_path && !mapped.backdrop_path.startsWith('http')) {
          mapped.backdrop_path = `https://phimimg.com/${mapped.backdrop_path}`;
        }
        
        return mapped;
      });
      
      // Get pagination info
      const pagination = response.data.params?.pagination;
      if (pagination) {
        totalPages.value = pagination.totalPages || 1;
        totalItems.value = pagination.totalItems || movies.value.length;
      } else {
        totalPages.value = 1;
        totalItems.value = movies.value.length;
      }

      console.log('✅ Found:', movies.value.length, 'movies');
      console.log('✅ Movies:', movies.value);
    } else {
      console.warn('❌ Không parse được response');
      console.warn('Response:', response);
      movies.value = [];
      totalPages.value = 1;
      totalItems.value = 0;
    }
  } catch (err) {
    console.error('❌ Search error:', err);
    error.value = 'Không thể tìm kiếm. Vui lòng thử lại sau.';
    movies.value = [];
  } finally {
    loading.value = false;
  }
};

// Handle page change
const handlePageChange = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    router.push({
      path: '/search',
      query: { q: searchKeyword.value, page }
    });
  }
};

// Render pagination
const renderPagination = () => {
  const pages = new Set();
  const delta = 2;

  pages.add(1);
  if (totalPages.value > 1) {
    pages.add(totalPages.value);
  }

  for (let i = currentPage.value - delta; i <= currentPage.value + delta; i++) {
    if (i >= 1 && i <= totalPages.value) {
      pages.add(i);
    }
  }

  let sortedPages = Array.from(pages).sort((a, b) => a - b);
  let finalPages = [];

  for (let i = 0; i < sortedPages.length; i++) {
    finalPages.push(sortedPages[i]);
    if (i < sortedPages.length - 1 && sortedPages[i + 1] - sortedPages[i] > 1) {
      finalPages.push('ellipsis');
    }
  }
  return finalPages;
};

// Watch route changes
watch(
  () => [route.query.q, route.query.page],
  ([newKeyword, newPage]) => {
    searchKeyword.value = newKeyword || '';
    currentPage.value = parseInt(newPage) || 1;
    fetchSearchResults();
  },
  { immediate: true }
);
</script>

<style scoped>
/* Line clamp for text truncation */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Hover effect */
.bg-gray-750 {
  background-color: #2d3748;
}

/* Responsive */
@media (max-width: 640px) {
  .line-clamp-2 {
    display: none;
  }
}
</style>
