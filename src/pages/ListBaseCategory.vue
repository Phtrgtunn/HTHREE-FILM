<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
          <p class="text-xl font-semibold text-gray-300">Đang tải phim...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center text-red-400 text-lg py-12">
        <p class="text-xl font-semibold">{{ error }}</p>
      </div>

      <!-- Main Content -->
      <template v-else>
        <!-- Cập nhật FilterBar để truyền các danh sách lọc và v-model -->
        <FilterBar
          :baseSlug="categorySlug"
          :basePath="'category'"
          :allCategories="categoryStore.getAllCategories"
          :allCountries="countryStore.getAllCountries"
          :availableYears="availableYears"
          v-model:selectedCategory="selectedCategoryQuery"
          v-model:selectedCountry="selectedCountry"
          v-model:selectedYear="selectedYear"
          :isLoadingCategories="categoryStore.getLoading"  
          :isLoadingCountries="countryStore.getLoading"    
          :isLoadingYears="false"                         
        />

        <div class="w-full flex flex-col lg:flex-row gap-6 mt-6">
          <!-- Movie Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 flex-[4]">
            <template v-if="movies && movies.length > 0">
              <MovieCardRecommended
                v-for="(item, index) in movies"
                :key="item.slug || item.id || index"
                :movie="item"
                :trending="false"
              />
            </template>
            <p v-else class="text-center col-span-full text-gray-400 text-lg">Không có dữ liệu phim cho thể loại này.</p>
          </div>

          <!-- Ranking Sidebar -->
          <div class="lg:w-1/4 flex-1 bg-gray-900 p-4 rounded-xl shadow-md">
            <h3 class="uppercase text-yellow-400 text-sm border-b-2 border-dashed border-gray-600 pb-2 mb-4">
              Bảng Xếp Hạng
            </h3>
            <div v-if="recommendedMoviesStore.getRecommended.length > 0" class="space-y-4 max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-yellow-500 scrollbar-track-gray-700 pr-2">
              <Rank
                v-for="(item, index) in recommendedMoviesStore.getRecommended.slice(0, 8)"
                :key="item.slug || item.id || index"
                :trendingMovie="item"
                :index="index"
              />
            </div>
            <p v-else class="text-gray-400 text-lg">Không có dữ liệu xếp hạng.</p>
          </div>
        </div>

        <div class="flex justify-center mt-10">
          <nav role="navigation" aria-label="pagination" class="mx-auto flex w-full justify-center">
            <ul class="flex flex-row items-center gap-2">
              <li>
                <button
                  @click="handlePageChange(currentPage - 1)"
                  :class="{ 'opacity-50 pointer-events-none': currentPage === 1 }"
                  class="px-3 py-1.5 rounded-full text-sm font-medium transition-all hover:bg-gray-700 disabled:opacity-50"
                  aria-label="Previous page"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
                </button>
              </li>

              <li v-for="(page, index) in renderPagination()" :key="index">
                <span v-if="page === 'ellipsis'" class="flex size-9 items-center justify-center text-gray-400">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-more-horizontal"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                  <span class="sr-only">More pages</span>
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
                  :class="{ 'opacity-50 pointer-events-none': currentPage === totalPage }"
                  class="px-3 py-1.5 rounded-full text-sm font-medium transition-all hover:bg-gray-700 disabled:opacity-50"
                  aria-label="Next page"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getMovieList, mapMovieData } from '@/services/movieApi';
import FilterBar from '@/shared/FilterBar.vue';
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
const loading = ref(true); // Trạng thái loading
const error = ref(null);   // Trạng thái lỗi

const categorySlug = ref(route.params.categorySlug); // Slug thể loại từ URL path
const currentPage = ref(parseInt(route.params.pageNumber) || 1);

// Thêm biến reactive cho selectedCategoryQuery để đồng bộ với v-model của FilterBar
const selectedCategoryQuery = ref(route.query.category || ''); // category từ URL query
const selectedCountry = ref(route.query.country || '');
const selectedYear = ref(route.query.year || '');

const MOVIES_PER_PAGE = 20; // Số phim mỗi trang cho lưới

// Tạo danh sách các năm có sẵn
const availableYears = computed(() => {
  const currentYear = new Date().getFullYear();
  const years = [];
  for (let i = currentYear; i >= currentYear - 20; i--) { // Lấy 20 năm gần nhất
    years.push(i);
  }
  return years;
});

// Computed property để lấy tên thể loại từ slug (dùng để lọc trong Supabase)
const categoryName = computed(() => {
  if (categoryStore.getAllCategories.length > 0 && selectedCategoryQuery.value) {
    const category = categoryStore.getAllCategories.find(cat => cat.slug === selectedCategoryQuery.value);
    return category ? category.name : null;
  }
  // Nếu không có selectedCategoryQuery.value nhưng có categorySlug (tức là lọc theo path), sử dụng categorySlug
  if (categoryStore.getAllCategories.length > 0 && categorySlug.value) {
    const category = categoryStore.getAllCategories.find(cat => cat.slug === categorySlug.value);
    return category ? category.name : null;
  }
  return null;
});

// Computed property để lấy tên quốc gia từ slug (dùng để lọc trong Supabase)
const countryName = computed(() => {
  if (countryStore.getAllCountries.length > 0 && selectedCountry.value) {
    const country = countryStore.getAllCountries.find(c => c.slug === selectedCountry.value);
    return country ? country.name : null;
  }
  return null;
});

// Hàm chính để fetch phim từ API
const fetchMovies = async () => {
  loading.value = true;
  error.value = null;
  movies.value = [];
  totalPage.value = 1;

  console.log('ListBaseCategory: Bắt đầu fetchMovies từ API.');
  console.log('ListBaseCategory: Selected Category Slug (query):', selectedCategoryQuery.value);
  console.log('ListBaseCategory: Selected Country Slug (query):', selectedCountry.value);
  console.log('ListBaseCategory: Selected Year (query):', selectedYear.value);
  console.log('ListBaseCategory: Route Category Slug (path):', categorySlug.value);
  console.log('ListBaseCategory: Current Page:', currentPage.value);

  try {
    // Build params cho API
    const params = {
      page: currentPage.value,
      limit: MOVIES_PER_PAGE,
      sort_field: 'modified.time',
      sort_type: 'desc'
    };

    // Lọc theo thể loại (ưu tiên selectedCategoryQuery, sau đó categorySlug)
    const categoryFilter = selectedCategoryQuery.value || categorySlug.value;
    if (categoryFilter) {
      params.category = categoryFilter;
      console.log('ListBaseCategory: Áp dụng lọc theo Thể loại:', categoryFilter);
    }

    // Lọc theo quốc gia
    if (selectedCountry.value) {
      params.country = selectedCountry.value;
      console.log('ListBaseCategory: Áp dụng lọc theo Quốc gia:', selectedCountry.value);
    }

    // Lọc theo năm
    if (selectedYear.value) {
      const year = parseInt(selectedYear.value);
      if (!isNaN(year)) {
        params.year = year;
        console.log('ListBaseCategory: Áp dụng lọc theo Năm:', year);
      }
    }

    // Gọi API
    const response = await getMovieList(params);

    if (response.status && response.data) {
      movies.value = (response.data.items || []).map(mapMovieData);
      
      // Tính tổng số trang từ pagination
      const pagination = response.data.params?.pagination;
      if (pagination) {
        totalPage.value = pagination.totalPages || 1;
      }

      console.log('ListBaseCategory: Phim đã tải từ API:', movies.value.length, 'phim');
      console.log('ListBaseCategory: Tổng số trang:', totalPage.value);
    } else {
      console.warn('ListBaseCategory: API không trả về dữ liệu hợp lệ');
      movies.value = [];
      totalPage.value = 1;
    }

  } catch (err) {
    console.error('Lỗi khi fetch phim từ API (ListBaseCategory):', err);
    error.value = 'Không thể tải dữ liệu phim. Vui lòng thử lại sau.';
    movies.value = [];
    totalPage.value = 1;
  } finally {
    loading.value = false;
    console.log('ListBaseCategory: Kết thúc fetchMovies. Loading:', loading.value, 'Error:', error.value);
  }
};

// Hàm xử lý khi người dùng đổi trang
const handlePageChange = (page) => {
  if (page >= 1 && page <= totalPage.value) {
    // Xây dựng đường dẫn mới dựa trên base path và slug
    let pathSegments = ['category'];
    if (categorySlug.value) { // Giữ nguyên categorySlug trong path nếu có
      pathSegments.push(encodeURIComponent(categorySlug.value));
    }
    pathSegments.push('page', page);

    const newQuery = {};
    // Thêm các query params hiện tại (hoặc đã chọn)
    if (selectedCategoryQuery.value) {
      newQuery.category = encodeURIComponent(selectedCategoryQuery.value);
    }
    if (selectedCountry.value) {
      newQuery.country = encodeURIComponent(selectedCountry.value);
    }
    if (selectedYear.value) {
      newQuery.year = encodeURIComponent(selectedYear.value);
    }

    router.push({
      path: `/${pathSegments.join('/')}`,
      query: newQuery,
    });
  }
};

// Hàm tạo mảng các trang để render pagination
const renderPagination = () => {
  const pages = new Set();
  const delta = 2; // Số trang hiển thị xung quanh trang hiện tại

  // Luôn thêm trang đầu tiên và trang cuối cùng
  pages.add(1);
  if (totalPage.value > 1) {
    pages.add(totalPage.value);
  }

  // Thêm các trang xung quanh trang hiện tại
  for (let i = currentPage.value - delta; i <= currentPage.value + delta; i++) {
    if (i >= 1 && i <= totalPage.value) {
      pages.add(i);
    }
  }

  // Chuyển Set thành Array và sắp xếp
  let sortedPages = Array.from(pages).sort((a, b) => a - b);
  let finalPages = [];

  // Thêm dấu ba chấm vào giữa các nhóm trang
  for (let i = 0; i < sortedPages.length; i++) {
    finalPages.push(sortedPages[i]);
    if (i < sortedPages.length - 1 && sortedPages[i + 1] - sortedPages[i] > 1) {
      finalPages.push('ellipsis');
    }
  }
  return finalPages;
};

// Lifecycle Hooks và Watchers
onMounted(async () => {
  console.log('ListBaseCategory: Component mounted.');

  // Lấy phim đề cử cho sidebar (nếu chưa có)
  if (recommendedMoviesStore.getRecommended.length === 0) {
    console.log('ListBaseCategory: Fetching recommended movies for sidebar...');
    await recommendedMoviesStore.getRecommendedMovies();
  } else {
    console.log('ListBaseCategory: Recommended movies already in store. Count:', recommendedMoviesStore.getRecommended.length);
  }

  // Lấy danh sách thể loại và quốc gia (nếu chưa có)
  // Các hàm fetchMovies phụ thuộc vào dữ liệu này để chuyển đổi slug sang tên
  if (categoryStore.getAllCategories.length === 0) {
    console.log('ListBaseCategory: Fetching categories...');
    await categoryStore.getCategory();
    console.log('ListBaseCategory: Categories loaded. Count:', categoryStore.getAllCategories.length, 'Data:', categoryStore.getAllCategories);
  } else {
    console.log('ListBaseCategory: Categories already in store. Count:', categoryStore.getAllCategories.length, 'Data:', categoryStore.getAllCategories);
  }

  if (countryStore.getAllCountries.length === 0) {
    console.log('ListBaseCategory: Fetching countries...');
    await countryStore.getCountry();
    console.log('ListBaseCategory: Countries loaded. Count:', countryStore.getAllCountries.length, 'Data:', countryStore.getAllCountries);
  } else {
    console.log('ListBaseCategory: Countries already in store. Count:', countryStore.getAllCountries.length, 'Data:', countryStore.getAllCountries);
  }
  console.log('ListBaseCategory: Available years computed:', availableYears.value);

  // Lắng nghe sự thay đổi của route.params và route.query
  // Đây là watcher chính để kích hoạt fetchMovies
  watch(
    () => route.params,
    (newParams) => {
      categorySlug.value = newParams.categorySlug || '';
      currentPage.value = parseInt(newParams.pageNumber) || 1;
      console.log('ListBaseCategory: route.params changed. Triggering fetchMovies (if stores ready).');
      // Fetch phim chỉ khi category và country stores đã load xong
      if (categoryStore.getAllCategories.length > 0 && countryStore.getAllCountries.length > 0) {
        fetchMovies();
      }
    },
    { immediate: true } // Chạy ngay lập tức khi component được mount
  );

  watch(
    () => route.query,
    (newQuery) => {
      selectedCategoryQuery.value = newQuery.category || ''; // Cập nhật ref này
      selectedCountry.value = newQuery.country || '';
      selectedYear.value = newQuery.year || '';
      console.log('ListBaseCategory: route.query changed. Triggering fetchMovies (if stores ready).');
      // Fetch phim chỉ khi category và country stores đã load xong
      if (categoryStore.getAllCategories.length > 0 && countryStore.getAllCountries.length > 0) {
        fetchMovies();
      }
    },
    { deep: true, immediate: false } // immediate: false ở đây vì route.params đã có immediate: true
  );

  // Thêm watcher cho categoryStore và countryStore để đảm bảo fetchMovies được gọi lại
  // sau khi dữ liệu của store đã được tải xong (nếu chúng chưa được tải trong onMounted ban đầu)
  watch([
    () => categoryStore.getAllCategories.length,
    () => countryStore.getAllCountries.length,
    () => categoryStore.getLoading, // Watch loading state as well
    () => countryStore.getLoading   // Watch loading state as well
  ], (
    [newCategoryCount, newCountryCount, newCategoryLoading, newCountryLoading],
    [oldCategoryCount, oldCountryCount, oldCategoryLoading, oldCountryLoading]
  ) => {
    // Kích hoạt fetchMovies khi cả hai store đã tải xong dữ liệu lần đầu tiên
    // và không còn trong trạng thái loading
    if ((newCategoryCount > 0 && newCountryCount > 0) && (!newCategoryLoading && !newCountryLoading)) {
      if (!loading.value) { // Tránh gọi quá nhiều lần nếu đang loading chính
          console.log('ListBaseCategory: Category or Country store data loaded and not loading. Triggering fetchMovies.');
          fetchMovies();
      }
    }
  });

  // Gọi fetchMovies lần đầu sau khi onMounted hoàn tất các hoạt động await cơ bản
  // Điều này là cần thiết nếu không có thay đổi route.params hoặc route.query ngay lập tức
  // nhưng store đã có dữ liệu từ trước (ví dụ: do cache của Pinia)
  if (categoryStore.getAllCategories.length > 0 && countryStore.getAllCountries.length > 0) {
    console.log('ListBaseCategory: Stores already have data, calling fetchMovies for initial load.');
    fetchMovies();
  } else {
    console.log('ListBaseCategory: Stores empty on initial mount, waiting for watchers.');
  }
});
</script>

<style scoped>
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
  .grid-cols-4 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .lg\:w-1\/4 {
    width: 100%;
  }
  .max-h-\[600px\] {
    max-height: 400px;
  }
}

@media (max-width: 640px) {
  .grid-cols-4 {
    grid-template-columns: 1fr;
  }
  .text-lg {
    font-size: 1.125rem;
  }
}
</style>
