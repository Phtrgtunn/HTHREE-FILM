<template>
  <div ref="movieRowRef" class="movie-row mb-16 px-4 md:px-12 opacity-0 translate-y-10 transition-all duration-700" :class="{ 'opacity-100 translate-y-0': isVisible }">
    <!-- Title with Icon -->
    <div class="flex items-center gap-3 mb-6">
      <div class="w-1 h-8 bg-yellow-400 rounded-full"></div>
      <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
        {{ title }}
      </h2>
    </div>
    
    <div class="relative group/row">
      <!-- Left Arrow -->
      <button 
        @click="scrollLeft"
        class="absolute left-2 top-1/2 -translate-y-1/2 z-10 w-14 h-14 bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 rounded-full flex items-center justify-center transition-all duration-300 opacity-0 group-hover/row:opacity-100 shadow-2xl shadow-yellow-400/50 hover:scale-110 hover:shadow-yellow-400/70 group/arrow"
      >
        <svg class="w-7 h-7 text-black group-hover/arrow:-translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>

      <!-- Right Arrow -->
      <button 
        @click="scrollRight"
        class="absolute right-2 top-1/2 -translate-y-1/2 z-10 w-14 h-14 bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 rounded-full flex items-center justify-center transition-all duration-300 opacity-0 group-hover/row:opacity-100 shadow-2xl shadow-yellow-400/50 hover:scale-110 hover:shadow-yellow-400/70 group/arrow"
      >
        <svg class="w-7 h-7 text-black group-hover/arrow:translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </button>

      <!-- Scroll Container -->
      <div 
        ref="scrollContainer"
        class="flex gap-4 overflow-x-auto scrollbar-hide pb-4 select-none scroll-smooth"
      >
        <div 
          v-for="(movie, index) in movies" 
          :key="movie._id || movie.slug"
          class="movie-card flex-none w-[160px] md:w-[220px] lg:w-[260px] cursor-pointer group/card opacity-0 translate-x-8 transition-all duration-500"
          :class="{ 'opacity-100 translate-x-0': isVisible }"
          @mouseenter="(e) => handleMouseEnter(movie, e)"
          @mouseleave="handleMouseLeave"
          @click="goToMovie(movie)"
          :style="{ transitionDelay: isVisible ? `${index * 50}ms` : '0ms' }"
        >
          <div class="relative rounded-xl overflow-hidden shadow-lg transition-all duration-300 group-hover/card:scale-105 group-hover/card:shadow-2xl group-hover/card:shadow-yellow-400/20">
            <!-- Image -->
            <img 
              :src="getMovieImage(movie)"
              :alt="movie.title || movie.name"
              class="w-full h-[240px] md:h-[330px] lg:h-[390px] object-cover transition-transform duration-500 group-hover/card:scale-110"
              @error="handleImageError"
            />
            
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-60 group-hover/card:opacity-90 transition-opacity duration-300"></div>
            
            <!-- Content -->
            <div class="absolute bottom-0 left-0 right-0 p-4 transform translate-y-2 group-hover/card:translate-y-0 transition-transform duration-300">
              <h3 class="text-sm md:text-base font-bold mb-2 line-clamp-2 text-white">
                {{ movie.title || movie.name }}
              </h3>
              
              <div class="flex items-center gap-2 text-xs mb-2 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300">
                <span v-if="movie.year" class="px-2 py-1 bg-gray-700/80 backdrop-blur-sm rounded text-white font-medium">
                  {{ movie.year }}
                </span>
                <span v-if="movie.quality" class="px-2 py-1 bg-yellow-500 rounded text-black font-bold">
                  {{ movie.quality }}
                </span>
              </div>
              
              <div class="flex gap-1 flex-wrap opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 delay-100">
                <span 
                  v-for="genre in (movie.category || movie.genres)?.slice(0, 2)" 
                  :key="genre.slug || genre"
                  class="text-xs text-gray-300 bg-gray-800/60 backdrop-blur-sm px-2 py-0.5 rounded"
                >
                  {{ genre.name || genre }}
                </span>
              </div>
            </div>

            <!-- Play Icon -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover/card:opacity-100 transition-all duration-300 scale-50 group-hover/card:scale-100">
              <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center shadow-xl">
                <svg class="w-8 h-8 text-black ml-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Card (Small) - Render outside using Teleport -->
  </div>

  <!-- Movie Detail Modal -->
  <MovieDetailModal
    :is-open="isModalOpen"
    :movie-slug="selectedMovieSlug"
    @close="isModalOpen = false"
    @open-movie="handleOpenMovie"
  />

  <!-- Preview Card Portal -->
  <Teleport to="body">
    <div
      v-if="showPreview && previewMovie"
      :style="{ ...previewPosition, zIndex: 99999 }"
      class="fixed w-[320px] animate-scale-in-smooth pointer-events-auto"
      @mouseenter="keepPreviewOpen = true"
      @mouseleave="closePreview"
    >
      <div class="bg-gray-900 rounded-lg overflow-hidden shadow-2xl shadow-yellow-400/20 border border-yellow-400/30 transform transition-all duration-300 hover:scale-105 hover:shadow-yellow-400/40">
        <!-- Header Image -->
        <div class="relative h-[160px] bg-black group/image">
          <img 
            :src="getPreviewImage(previewMovie)"
            :alt="previewMovie.name"
            class="w-full h-full object-cover"
            @error="handleImageError"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
          
          <!-- Play Icon Overlay -->
          <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover/image:opacity-100 transition-all duration-300">
            <div class="w-14 h-14 bg-yellow-400 rounded-full flex items-center justify-center shadow-2xl transform transition-all duration-300 group-hover/image:scale-110 animate-pulse-slow">
              <svg class="w-7 h-7 text-black ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
              </svg>
            </div>
          </div>
          
          <!-- Badge Quality -->
          <div 
            v-if="previewMovie.quality"
            class="absolute top-2 right-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-2 py-1 rounded text-[10px] font-bold shadow-lg"
          >
            {{ previewMovie.quality }}
          </div>
        </div>

        <!-- Content -->
        <div class="p-3">
          <!-- Title -->
          <h3 class="text-base font-bold text-white mb-0.5 line-clamp-1">{{ previewMovie.name }}</h3>
          <p class="text-xs text-yellow-400 mb-2 line-clamp-1">{{ previewMovie.origin_name }}</p>

          <!-- Action Buttons -->
          <div class="flex gap-1.5 mb-2">
            <button
              @click="goToMovie(previewMovie)"
              class="flex-1 flex items-center justify-center gap-1.5 bg-yellow-400 text-black px-3 py-1.5 rounded-lg font-bold text-xs hover:bg-yellow-500 hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-yellow-400/50"
            >
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
              </svg>
              Xem ngay
            </button>
            <button class="w-8 h-8 bg-gray-800 hover:bg-gray-700 hover:scale-110 rounded-lg flex items-center justify-center transition-all duration-300 group/btn">
              <svg class="w-3.5 h-3.5 text-white group-hover/btn:text-red-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
              </svg>
            </button>
            <button
              @click.stop="openMovieDetail(previewMovie)"
              class="w-8 h-8 bg-gray-800 hover:bg-gray-700 hover:scale-110 rounded-lg flex items-center justify-center transition-all duration-300 group/btn"
              title="Các tập & Thông tin"
            >
              <svg class="w-3.5 h-3.5 text-white group-hover/btn:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
          </div>

          <!-- Badges -->
          <div class="flex gap-1.5 mb-2 flex-wrap">
            <!-- Quality Badge - check multiple possible fields -->
            <span v-if="previewMovie.quality" class="bg-yellow-500 text-black px-1.5 py-0.5 rounded text-[10px] font-bold uppercase">
              {{ previewMovie.quality }}
            </span>
            
            <!-- Language Badge - support multiple fields -->
            <span v-if="previewMovie.lang" class="bg-blue-600 text-white px-1.5 py-0.5 rounded text-[10px] font-bold uppercase">
              {{ previewMovie.lang }}
            </span>
            <span v-else-if="previewMovie.sub_docquyen" class="bg-blue-600 text-white px-1.5 py-0.5 rounded text-[10px] font-bold uppercase">
              Vietsub
            </span>
            <span v-else-if="previewMovie.chieurap === false" class="bg-blue-600 text-white px-1.5 py-0.5 rounded text-[10px] font-bold uppercase">
              Vietsub
            </span>
            
            <!-- Year Badge -->
            <span v-if="previewMovie.year" class="bg-gray-700 text-white px-1.5 py-0.5 rounded text-[10px] font-bold">
              {{ previewMovie.year }}
            </span>
            
            <!-- Time/Episode Badge -->
            <span v-if="previewMovie.time" class="bg-gray-700 text-white px-1.5 py-0.5 rounded text-[10px] font-bold">
              {{ previewMovie.time }}
            </span>
            <span v-else-if="previewMovie.episode_current" class="bg-gray-700 text-white px-1.5 py-0.5 rounded text-[10px] font-bold">
              {{ previewMovie.episode_current }}
            </span>
            
            <!-- Type Badge (Phim bộ/Phim lẻ) -->
            <span v-if="previewMovie.type" class="bg-purple-600 text-white px-1.5 py-0.5 rounded text-[10px] font-bold capitalize">
              {{ previewMovie.type === 'series' ? 'Phim bộ' : previewMovie.type === 'single' ? 'Phim lẻ' : previewMovie.type === 'hoathinh' ? 'Hoạt hình' : previewMovie.type }}
            </span>
            
            <!-- Country Badge if available -->
            <span v-if="previewMovie.country && previewMovie.country.length > 0" class="bg-green-600 text-white px-1.5 py-0.5 rounded text-[10px] font-bold">
              {{ Array.isArray(previewMovie.country) ? previewMovie.country[0].name || previewMovie.country[0] : previewMovie.country }}
            </span>
          </div>

          <!-- Genres -->
          <div v-if="getGenres(previewMovie).length" class="text-[10px] text-gray-400 mb-1.5">
            <span
              v-for="(genre, i) in getGenres(previewMovie).slice(0, 3)"
              :key="i"
            >
              {{ genre }}<span v-if="i < Math.min(2, getGenres(previewMovie).length - 1)"> • </span>
            </span>
          </div>

          <!-- Description -->
          <p class="text-[11px] text-gray-300 line-clamp-2 leading-relaxed">
            {{ previewMovie.content || 'Đang cập nhật mô tả...' }}
          </p>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import MovieDetailModal from './MovieDetailModal.vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  movies: {
    type: Array,
    default: () => []
  }
});

const router = useRouter();
const scrollContainer = ref(null);
const movieRowRef = ref(null);
const isVisible = ref(false);
const showPreview = ref(false);
const previewMovie = ref(null);
const previewPosition = ref({});
const keepPreviewOpen = ref(false);
const isModalOpen = ref(false);
const selectedMovieSlug = ref('');
let hoverTimer = null;
let observer = null;

const openMovieDetail = (movie) => {
  console.log('🎬 Opening movie detail for:', movie);
  if (movie?.slug) {
    console.log('✅ Movie slug:', movie.slug);
    selectedMovieSlug.value = movie.slug;
    isModalOpen.value = true;
    closePreview(); // Đóng preview card khi mở modal
  } else {
    console.error('❌ No slug found for movie:', movie);
  }
};

const handleOpenMovie = (slug) => {
  console.log('🎬 Opening new movie from modal:', slug);
  selectedMovieSlug.value = slug;
  isModalOpen.value = true;
};

const getMovieImage = (movie) => {
  // Ưu tiên poster_url từ API phimapi.com (ảnh dọc cho card)
  if (movie.poster_url) {
    const url = movie.poster_url.startsWith('http') 
      ? movie.poster_url 
      : `https://phimimg.com/${movie.poster_url}`;
    console.log('🖼️ Using poster_url:', url);
    return url;
  }
  
  // Fallback thumb_url
  if (movie.thumb_url) {
    const url = movie.thumb_url.startsWith('http') 
      ? movie.thumb_url 
      : `https://phimimg.com/${movie.thumb_url}`;
    console.log('🖼️ Using thumb_url:', url);
    return url;
  }
  
  // Fallback backdrop_path (Supabase)
  if (movie.backdrop_path) {
    console.log('🖼️ Using backdrop_path:', movie.backdrop_path);
    return movie.backdrop_path;
  }
  
  console.log('⚠️ No image found, using placeholder');
  return 'https://placehold.co/250x350/1a1a1a/fff?text=No+Image';
};

const getPreviewImage = (movie) => {
  // Ưu tiên thumb_url (ảnh ngang cho preview card)
  if (movie.thumb_url) {
    const url = movie.thumb_url.startsWith('http') 
      ? movie.thumb_url 
      : `https://phimimg.com/${movie.thumb_url}`;
    console.log('🖼️ Preview using thumb_url:', url);
    return url;
  }
  
  // Fallback poster_url
  if (movie.poster_url) {
    const url = movie.poster_url.startsWith('http') 
      ? movie.poster_url 
      : `https://phimimg.com/${movie.poster_url}`;
    return url;
  }
  
  // Fallback backdrop_path (Supabase)
  if (movie.backdrop_path) {
    return movie.backdrop_path;
  }
  
  return 'https://placehold.co/320x160/1a1a1a/fff?text=No+Image';
};

const getGenres = (movie) => {
  // Xử lý category array (có thể là array of objects hoặc array of strings)
  if (movie.category && Array.isArray(movie.category)) {
    return movie.category.map(cat => typeof cat === 'object' ? cat.name : cat);
  }
  
  // Fallback genres array
  if (movie.genres && Array.isArray(movie.genres)) {
    return movie.genres.map(genre => typeof genre === 'object' ? genre.name : genre);
  }
  
  return [];
};

const goToMovie = (movie) => {
  if (movie.slug) {
    router.push(`/film/${movie.slug}`);
  }
};

const scrollLeft = () => {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({
      left: -800,
      behavior: 'smooth'
    });
  }
};

const scrollRight = () => {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({
      left: 800,
      behavior: 'smooth'
    });
  }
};

const handleMouseEnter = async (movie, event) => {
  console.log('🎬 Mouse enter:', movie.name);
  clearTimeout(hoverTimer);
  
  // Lưu lại element ngay lập tức
  const target = event.currentTarget;
  const rect = target.getBoundingClientRect();
  
  hoverTimer = setTimeout(async () => {
    console.log('✅ Showing preview for:', movie.name);
    
    // Calculate position
    let top = rect.top - 60;
    let left = rect.left - 30;
    
    if (left < 20) {
      left = 20;
    }
    
    if (left + 320 > window.innerWidth) {
      left = window.innerWidth - 340;
    }
    
    if (top < 20) {
      top = 20;
    }
    
    if (top + 400 > window.innerHeight) {
      top = window.innerHeight - 420;
    }
    
    previewPosition.value = {
      top: `${top}px`,
      left: `${left}px`,
    };
    
    // Hiển thị preview với data hiện có trước
    previewMovie.value = movie;
    showPreview.value = true;
    
    // Fetch chi tiết từ API nếu thiếu thông tin
    if (movie.slug && (!movie.quality || !movie.lang || !movie.content)) {
      try {
        const axios = (await import('axios')).default;
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 3000); // 3s timeout
        
        const response = await axios.get(`https://phimapi.com/phim/${movie.slug}`, {
          signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        if (response.data?.movie) {
          const detailData = response.data.movie;
          
          // Merge dữ liệu chi tiết vào movie hiện tại
          const updatedMovie = {
            ...movie,
            quality: detailData.quality || movie.quality,
            lang: detailData.lang || movie.lang,
            content: detailData.content || movie.content,
            time: detailData.time || movie.time,
            episode_current: detailData.episode_current || movie.episode_current,
            type: detailData.type || movie.type,
            country: detailData.country || movie.country,
            category: detailData.category || movie.category,
            chieurap: detailData.chieurap !== undefined ? detailData.chieurap : movie.chieurap,
            sub_docquyen: detailData.sub_docquyen !== undefined ? detailData.sub_docquyen : movie.sub_docquyen
          };
          
          // Cập nhật preview đang hiển thị
          if (previewMovie.value?.slug === movie.slug) {
            previewMovie.value = updatedMovie;
          }
          
          // Cập nhật vào movie object gốc để cache
          Object.assign(movie, updatedMovie);
          
          console.log('✅ Fetched movie details:', updatedMovie.name);
        }
      } catch (err) {
        console.log('⚠️ Could not fetch movie details:', err.message);
      }
    }
  }, 300);
};

const handleMouseLeave = () => {
  clearTimeout(hoverTimer);
  setTimeout(() => {
    if (!keepPreviewOpen.value) {
      closePreview();
    }
  }, 100);
};

const closePreview = () => {
  keepPreviewOpen.value = false;
  showPreview.value = false;
  previewMovie.value = null;
};

const handleImageError = (event) => {
  console.error('❌ Image failed to load:', event.target.src);
  event.target.src = 'https://placehold.co/320x160/1a1a1a/fff?text=Image+Not+Found';
};

// Intersection Observer for scroll reveal
onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          isVisible.value = true;
          // Unobserve sau khi đã visible để tránh re-trigger
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.1, // Trigger khi 10% element visible
      rootMargin: '50px' // Trigger trước 50px
    }
  );

  if (movieRowRef.value) {
    observer.observe(movieRowRef.value);
  }
});

onUnmounted(() => {
  if (observer && movieRowRef.value) {
    observer.unobserve(movieRowRef.value);
  }
});


</script>

<style scoped>
/* Hide scrollbar */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
  scroll-padding: 0;
  overscroll-behavior-x: contain;
}

/* Smooth momentum scrolling */
.scrollbar-hide {
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
  will-change: scroll-position;
}

.movie-card {
  transform-origin: center;
  will-change: transform;
}

/* Removed old fade-in-up animation - now using transition classes */

/* Smooth scroll behavior */
.scroll-smooth {
  scroll-behavior: smooth;
}

/* Modal animations */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes scaleInSmooth {
  0% {
    opacity: 0;
    transform: scale(0.8) translateY(10px);
  }
  60% {
    transform: scale(1.02) translateY(-2px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

.animate-scale-in {
  animation: scaleIn 0.3s ease-out;
}

.animate-scale-in-smooth {
  animation: scaleInSmooth 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes pulseSlow {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.animate-pulse-slow {
  animation: pulseSlow 2s ease-in-out infinite;
}
</style>
