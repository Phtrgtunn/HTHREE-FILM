<template>
  <div class="relative flex flex-col sm:flex-row bg-gray-800 rounded-xl p-2 gap-2" ref="filterRef">
    <!-- Dropdown Thể loại -->
    <div class="relative flex-1">
      <button
        @click="toggleDropdown('category')"
        @keydown.enter="toggleDropdown('category')"
        class="w-full bg-gray-700 text-white py-3 px-4 rounded-lg flex items-center justify-between text-sm font-medium hover:bg-gray-600 transition-colors duration-200"
        :aria-expanded="isCategoryDropdownOpen"
        aria-controls="category-dropdown"
      >
        <span>{{ selectedCategoryDisplay }}</span>
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="{ 'rotate-180': isCategoryDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div
        v-if="isCategoryDropdownOpen"
        id="category-dropdown"
        class="absolute z-50 mt-2 w-full bg-white text-gray-900 rounded-lg shadow-xl max-h-80 overflow-y-auto transform transition-all duration-300 scrollbar-thin"
        :class="isCategoryDropdownOpen ? 'opacity-100 scale-100' : 'opacity-0 scale-95 pointer-events-none'"
      >
        <input
          v-model="categorySearch"
          type="text"
          placeholder="Tìm thể loại..."
          class="w-full px-4 py-2 border-b border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <!-- Tùy chọn "Tất cả Thể loại" -->
        <div
          @click="selectCategory('', 'Tất cả Thể loại')"
          @keydown.enter="selectCategory('', 'Tất cả Thể loại')"
          class="px-4 py-2 cursor-pointer hover:bg-blue-50 focus:bg-blue-50 focus:outline-none"
          tabindex="0"
          :class="{'bg-blue-100 font-semibold': internalSelectedCategory === ''}"
        >
          Tất cả Thể loại
        </div>
        <!-- Thông báo khi không tìm thấy kết quả tìm kiếm -->
        <div v-if="filteredCategories.length === 0 && categorySearch !== ''" class="px-4 py-2 text-gray-500">Không tìm thấy thể loại.</div>
        <!-- Thông báo khi không có thể loại nào được tải -->
        <div v-else-if="props.allCategories.length === 0 && categorySearch === ''" class="px-4 py-2 text-gray-500">Không có thể loại nào.</div>

        <div
          v-for="item in filteredCategories"
          :key="item.slug"
          @click="selectCategory(item.slug, item.name)"
          @keydown.enter="selectCategory(item.slug, item.name)"
          class="px-4 py-2 cursor-pointer hover:bg-blue-50 focus:bg-blue-50 focus:outline-none"
          tabindex="0"
          :class="{'bg-blue-100 font-semibold': internalSelectedCategory === item.slug}"
        >
          {{ item.name }}
        </div>
      </div>
    </div>

    <!-- Dropdown Quốc gia -->
    <div class="relative flex-1">
      <button
        @click="toggleDropdown('country')"
        @keydown.enter="toggleDropdown('country')"
        class="w-full bg-gray-700 text-white py-3 px-4 rounded-lg flex items-center justify-between text-sm font-medium hover:bg-gray-600 transition-colors duration-200"
        :aria-expanded="isCountryDropdownOpen"
        aria-controls="country-dropdown"
      >
        <span>{{ selectedCountryDisplay }}</span>
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="{ 'rotate-180': isCountryDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div
        v-if="isCountryDropdownOpen"
        id="country-dropdown"
        class="absolute z-50 mt-2 w-full bg-white text-gray-900 rounded-lg shadow-xl max-h-80 overflow-y-auto transform transition-all duration-300 scrollbar-thin"
        :class="isCountryDropdownOpen ? 'opacity-100 scale-100' : 'opacity-0 scale-95 pointer-events-none'"
      >
        <input
          v-model="countrySearch"
          type="text"
          placeholder="Tìm quốc gia..."
          class="w-full px-4 py-2 border-b border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <!-- Tùy chọn "Tất cả Quốc gia" -->
        <div
          @click="selectCountry('', 'Tất cả Quốc gia')"
          @keydown.enter="selectCountry('', 'Tất cả Quốc gia')"
          class="px-4 py-2 cursor-pointer hover:bg-blue-50 focus:bg-blue-50 focus:outline-none"
          tabindex="0"
          :class="{'bg-blue-100 font-semibold': internalSelectedCountry === ''}"
        >
          Tất cả Quốc gia
        </div>
        <!-- Thông báo khi không tìm thấy kết quả tìm kiếm -->
        <div v-if="filteredCountries.length === 0 && countrySearch !== ''" class="px-4 py-2 text-gray-500">Không tìm thấy quốc gia.</div>
        <!-- Thông báo khi không có quốc gia nào được tải -->
        <div v-else-if="props.allCountries.length === 0 && countrySearch === ''" class="px-4 py-2 text-gray-500">Không có quốc gia nào.</div>

        <div
          v-for="item in filteredCountries"
          :key="item.slug"
          @click="selectCountry(item.slug, item.name)"
          @keydown.enter="selectCountry(item.slug, item.name)"
          class="px-4 py-2 cursor-pointer hover:bg-blue-50 focus:bg-blue-50 focus:outline-none"
          tabindex="0"
          :class="{'bg-blue-100 font-semibold': internalSelectedCountry === item.slug}"
        >
          {{ item.name }}
        </div>
      </div>
    </div>

    <!-- Dropdown Năm -->
    <div class="relative flex-1">
      <button
        @click="toggleDropdown('year')"
        @keydown.enter="toggleDropdown('year')"
        class="w-full bg-gray-700 text-white py-3 px-4 rounded-lg flex items-center justify-between text-sm font-medium hover:bg-gray-600 transition-colors duration-200"
        :aria-expanded="isYearDropdownOpen"
        aria-controls="year-dropdown"
      >
        <span>{{ internalSelectedYear || 'Tất cả Năm' }}</span>
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="{ 'rotate-180': isYearDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div
        v-if="isYearDropdownOpen"
        id="year-dropdown"
        class="absolute z-50 mt-2 w-full bg-white text-gray-900 rounded-lg shadow-xl max-h-80 overflow-y-auto transform transition-all duration-300 scrollbar-thin"
        :class="isYearDropdownOpen ? 'opacity-100 scale-100' : 'opacity-0 scale-95 pointer-events-none'"
      >
        <input
          v-model="yearSearch"
          type="text"
          placeholder="Tìm năm..."
          class="w-full px-4 py-2 border-b border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <!-- Tùy chọn "Tất cả Năm" -->
        <div
          @click="selectYear('')"
          @keydown.enter="selectYear('')"
          class="px-4 py-2 cursor-pointer hover:bg-blue-50 focus:bg-blue-50 focus:outline-none"
          tabindex="0"
          :class="{'bg-blue-100 font-semibold': internalSelectedYear === ''}"
        >
          Tất cả Năm
        </div>
        <!-- Thông báo khi không tìm thấy kết quả tìm kiếm -->
        <div v-if="filteredYears.length === 0 && yearSearch !== ''" class="px-4 py-2 text-gray-500">Không tìm thấy năm.</div>
        <!-- Thông báo khi không có năm nào được tải -->
        <div v-else-if="props.availableYears.length === 0 && yearSearch === ''" class="px-4 py-2 text-gray-500">Không có năm nào.</div>

        <div
          v-for="yearItem in filteredYears"
          :key="yearItem"
          @click="selectYear(yearItem)"
          @keydown.enter="selectYear(yearItem)"
          class="px-4 py-2 cursor-pointer hover:bg-blue-50 focus:bg-blue-50 focus:outline-none"
          tabindex="0"
          :class="{'bg-blue-100 font-semibold': internalSelectedYear === String(yearItem)}"
        >
          {{ yearItem }}
        </div>
      </div>
    </div>

    <!-- Nút Lọc và Xóa -->
    <div class="flex gap-2">
      <button
        @click="handleFilter"
        class="bg-blue-600 text-white py-3 px-6 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200 disabled:bg-blue-400"
        :disabled="isLoading"
      >
        <span v-if="isLoading" class="flex items-center">
          <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Đang lọc...
        </span>
        <span v-else>Lọc Phim</span>
      </button>
      <button
        @click="clearFilters"
        class="bg-gray-600 text-white py-3 px-4 rounded-lg text-sm font-medium hover:bg-gray-500 transition-colors duration-200"
      >
        Xóa Bộ Lọc
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCountryStore } from '@/stores/Country/country';
import { useCategoryStore } from '@/stores/Category/category';

// Props
const props = defineProps({
  baseSlug: {
    type: String,
    default: '', 
  },
  basePath: {
    type: String,
    required: true,
  },
  selectedCategory: {
    type: String,
    default: '',
  },
  selectedCountry: {
    type: String,
    default: '',
  },
  selectedYear: {
    type: String,
    default: '',
  },
  allCategories: {
    type: Array,
    default: () => [],
  },
  allCountries: {
    type: Array,
    default: () => [],
  },
  availableYears: {
    type: Array,
    default: () => [],
  },
});

// Emits
const emit = defineEmits([
  'update:selectedCategory',
  'update:selectedCountry',
  'update:selectedYear',
]);

// Store và router
const countryStore = useCountryStore(); 
const categoryStore = useCategoryStore(); 
const route = useRoute();
const router = useRouter();

// Trạng thái cục bộ cho dropdown và search
const isCategoryDropdownOpen = ref(false);
const isCountryDropdownOpen = ref(false);
const isYearDropdownOpen = ref(false);
const isLoading = ref(false); 
const categorySearch = ref('');
const countrySearch = ref('');
const yearSearch = ref('');
const errorMessage = ref(''); 

// Các biến reactive nội bộ để binding với các select/button và emit lên parent
const internalSelectedCategory = ref(props.selectedCategory);
const internalSelectedCountry = ref(props.selectedCountry);
const internalSelectedYear = ref(props.selectedYear);

// Watch props để cập nhật các ref nội bộ khi parent thay đổi chúng (ví dụ từ route.query)
watch(() => props.selectedCategory, (newVal) => { 
  internalSelectedCategory.value = newVal; 
  console.log('FilterBar: props.selectedCategory changed to:', newVal, 'internalSelectedCategory:', internalSelectedCategory.value);
});
watch(() => props.selectedCountry, (newVal) => { 
  internalSelectedCountry.value = newVal; 
  console.log('FilterBar: props.selectedCountry changed to:', newVal, 'internalSelectedCountry:', internalSelectedCountry.value);
});
watch(() => props.selectedYear, (newVal) => { 
  internalSelectedYear.value = newVal; 
  console.log('FilterBar: props.selectedYear changed to:', newVal, 'internalSelectedYear:', internalSelectedYear.value);
});


// Computed properties cho hiển thị tên đã chọn
const selectedCategoryDisplay = computed(() => {
    const category = props.allCategories.find(cat => cat.slug === internalSelectedCategory.value);
    return category ? category.name : 'Chọn Thể Loại';
});

const selectedCountryDisplay = computed(() => {
    // Nếu internalSelectedCountry rỗng, hiển thị 'Tất cả Quốc gia'
    if (internalSelectedCountry.value === '') {
      return 'Tất cả Quốc gia';
    }
    const country = props.allCountries.find(c => c.slug === internalSelectedCountry.value);
    return country ? country.name : 'Chọn Quốc Gia'; // Fallback nếu không tìm thấy
});

// Lọc danh sách bằng computed properties (dựa trên props)
const filteredCategories = computed(() => {
  return props.allCategories.filter(item =>
    item?.name?.toLowerCase().includes(categorySearch.value.toLowerCase())
  );
});

const filteredCountries = computed(() => {
  return props.allCountries.filter(item =>
    item?.name?.toLowerCase().includes(countrySearch.value.toLowerCase())
  );
});

const filteredYears = computed(() =>
  props.availableYears.filter(item => 
    String(item).toLowerCase().includes(yearSearch.value.toLowerCase())
  )
);

// Hàm bật/tắt dropdown
const toggleDropdown = (type) => {
  const wasCategoryOpen = isCategoryDropdownOpen.value;
  const wasCountryOpen = isCountryDropdownOpen.value;
  const wasYearOpen = isYearDropdownOpen.value;

  isCategoryDropdownOpen.value = false;
  isCountryDropdownOpen.value = false;
  isYearDropdownOpen.value = false;
  
  categorySearch.value = '';
  countrySearch.value = '';
  yearSearch.value = '';

  if (type === 'category' && !wasCategoryOpen) {
    isCategoryDropdownOpen.value = true;
  } else if (type === 'country' && !wasCountryOpen) {
    isCountryDropdownOpen.value = true;
  } else if (type === 'year' && !wasYearOpen) {
    isYearDropdownOpen.value = true;
  }
};

// Hàm chọn giá trị và emit lên parent
const selectCategory = (slug, name) => {
  internalSelectedCategory.value = slug; 
  emit('update:selectedCategory', slug); 
  isCategoryDropdownOpen.value = false;
};

const selectCountry = (slug, name) => {
  internalSelectedCountry.value = slug; 
  emit('update:selectedCountry', slug); 
  isCountryDropdownOpen.value = false;
};

const selectYear = (year) => {
  internalSelectedYear.value = String(year); 
  emit('update:selectedYear', String(year)); 
  isYearDropdownOpen.value = false;
};

// Xóa bộ lọc
const clearFilters = () => {
  internalSelectedCategory.value = '';
  internalSelectedCountry.value = '';
  internalSelectedYear.value = '';
  emit('update:selectedCategory', '');
  emit('update:selectedCountry', '');
  emit('update:selectedYear', '');

  const currentPageNumber = route.params.pageNumber || 1; 
  let targetPath = `/${props.basePath}`; 

  if (props.baseSlug) {
      targetPath += `/${props.baseSlug}`;
  }
  targetPath += `/page/${currentPageNumber}`;

  router.push({ 
    path: targetPath, 
    query: {} 
  });

  isCategoryDropdownOpen.value = false;
  isCountryDropdownOpen.value = false;
  isYearDropdownOpen.value = false;
  categorySearch.value = '';
  countrySearch.value = '';
  yearSearch.value = '';
};

// Xử lý click bên ngoài
const filterRef = ref(null);
const handleClickOutside = (event) => {
  if (filterRef.value && !filterRef.value.contains(event.target)) {
    isCategoryDropdownOpen.value = false;
    isCountryDropdownOpen.value = false;
    isYearDropdownOpen.value = false;
    categorySearch.value = ''; 
    countrySearch.value = '';
    yearSearch.value = '';
  }
};

// Xử lý lọc - Cập nhật query parameters trên URL
const handleFilter = async () => {
  isLoading.value = true;
  errorMessage.value = '';
  try {
    const currentRouteParams = route.params;
    const currentPageNumber = currentRouteParams.pageNumber || 1;

    const newQuery = {};
    if (internalSelectedCategory.value) {
      newQuery.category = internalSelectedCategory.value;
    }
    if (internalSelectedCountry.value) {
      newQuery.country = internalSelectedCountry.value;
    }
    if (internalSelectedYear.value) {
      newQuery.year = internalSelectedYear.value;
    }

    let pathSegments = [];
    if (props.basePath) {
      pathSegments.push(props.basePath);
    }
    if (props.baseSlug) {
      pathSegments.push(props.baseSlug);
    }
    pathSegments.push('page', currentPageNumber);
    const targetPath = `/${pathSegments.join('/')}`;

    await router.push({
      path: targetPath, 
      query: newQuery 
    });

  } catch (error) {
    console.error('Lỗi khi lọc:', error);
    errorMessage.value = 'Đã có lỗi xảy ra khi lọc phim. Vui lòng thử lại!';
  } finally {
    isLoading.value = false;
  }
};

// Vòng đời
onMounted(() => { 
  document.addEventListener('click', handleClickOutside);
  // Log giá trị ban đầu của props.selectedCountry và internalSelectedCountry
  console.log('FilterBar onMounted: props.selectedCountry:', props.selectedCountry, 'internalSelectedCountry:', internalSelectedCountry.value);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Watch route.query để đồng bộ hiển thị của bộ lọc với URL
watch(
  () => route.query,
  (newQuery) => {
    internalSelectedCategory.value = newQuery.category || '';
    internalSelectedCountry.value = newQuery.country || '';
    internalSelectedYear.value = newQuery.year || '';
    console.log('FilterBar watch route.query: internalSelectedCountry updated to:', internalSelectedCountry.value);
  },
  { deep: true, immediate: true } 
);
</script>

<style scoped>
/* Đảm bảo hiệu ứng mượt mà */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
/* Tùy chỉnh thanh cuộn */
.scrollbar-thin {
  scrollbar-width: thin;
  scrollbar-color: #f97316 #e0e0e0; /* Màu thumb và track */
}

.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: #e0e0e0;
  border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #f97316;
  border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: #ea580c;
}
</style>
