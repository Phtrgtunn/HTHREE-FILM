<template>
  <div class="bg-black min-h-screen text-white">
    <!-- Hero Banner Carousel -->
    <div 
      ref="heroBannerRef"
      class="relative h-[85vh] overflow-hidden group/hero pt-16"
      @wheel="handleWheel"
      @touchstart="handleTouchStart"
      @touchmove="handleTouchMove"
      @touchend="handleTouchEnd"
    >
      <!-- Banners Loop -->
      <div 
        v-for="(movie, index) in featuredMovies" 
        :key="movie.slug"
        class="absolute inset-0 transition-opacity duration-700"
        :class="currentBannerIndex === index ? 'opacity-100 z-10' : 'opacity-0 z-0'"
      >
        <!-- Video Trailer -->
        <div v-if="showTrailer && currentBannerIndex === index && currentTrailerUrl" class="absolute inset-0 z-10 overflow-hidden">
          <iframe
            :src="currentTrailerUrl"
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[177.77vh] h-[56.25vw] min-h-full min-w-full"
            frameborder="0"
            allow="autoplay; encrypted-media"
            allowfullscreen
            style="pointer-events: none;"
          ></iframe>
          <!-- Overlay để che logo YouTube -->
          <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-32 h-20 bg-black"></div>
          </div>
        </div>

        <!-- Background Image - chỉ hiện khi không có trailer -->
        <div v-if="!showTrailer || currentBannerIndex !== index" class="absolute inset-0">
          <img 
            :src="getBannerImage(movie)"
            class="w-full h-full object-cover"
            :alt="movie.name"
            @error="handleImageError"
          />
          <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
        </div>

        <div class="relative z-10 h-full flex items-center container mx-auto px-4 sm:px-6 lg:px-8 pt-20">
          <div class="max-w-2xl">
            <!-- Title -->
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-3 uppercase tracking-tight leading-tight text-white" style="text-shadow: 3px 3px 10px rgba(0,0,0,1), 0 0 30px rgba(0,0,0,0.9), 0 0 50px rgba(0,0,0,0.7);">
              {{ movie?.name || 'KHÁM PHÁ PHIM HAY' }}
            </h1>
            
            <!-- Origin Name -->
            <p class="text-base md:text-lg text-white mb-4 font-semibold" style="text-shadow: 2px 2px 8px rgba(0,0,0,1), 0 0 15px rgba(0,0,0,0.8);">
              {{ movie?.origin_name || '' }}
            </p>

            <!-- Badges -->
            <div class="flex flex-wrap gap-2 mb-4">
              <span v-if="movie?.quality" class="bg-yellow-500 text-black px-3 py-1 rounded text-xs font-bold uppercase shadow-xl">
                {{ movie.quality }}
              </span>
              <span v-if="movie?.lang" class="bg-gray-900/90 text-white px-3 py-1 rounded text-xs font-bold uppercase shadow-xl border-2 border-gray-700">
                {{ movie.lang }}
              </span>
              <span v-if="movie?.year" class="bg-gray-900/90 text-white px-3 py-1 rounded text-xs font-bold shadow-xl border-2 border-gray-700">
                {{ movie.year }}
              </span>
              <span v-if="movie?.time" class="bg-gray-900/90 text-white px-3 py-1 rounded text-xs font-bold shadow-xl border-2 border-gray-700">
                {{ movie.time }}
              </span>
            </div>

            <!-- Categories -->
            <div v-if="movie?.category?.length" class="flex flex-wrap gap-2 mb-5 text-sm">
              <span 
                v-for="(genre, i) in movie.category.slice(0, 4)" 
                :key="i"
                class="text-white font-bold"
                style="text-shadow: 2px 2px 6px rgba(0,0,0,1), 0 0 10px rgba(0,0,0,0.9);"
              >
                {{ genre.name }}
              </span>
            </div>

            <!-- Description -->
            <p class="text-sm md:text-base mb-6 line-clamp-3 text-white leading-relaxed max-w-xl font-semibold" style="text-shadow: 2px 2px 8px rgba(0,0,0,1), 0 0 15px rgba(0,0,0,0.9);">
              {{ movie?.content || 'Khám phá những bộ phim tuyệt vời cùng HTHREE' }}
            </p>

            <!-- Buttons -->
            <div class="flex flex-wrap gap-3">
              <button 
                @click="playMovie(movie)"
                class="flex items-center gap-2 bg-yellow-400 text-black px-8 py-3.5 rounded-full font-bold text-base hover:bg-yellow-500 transition-all shadow-xl hover:scale-105"
              >
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                </svg>
                Xem Ngay
              </button>
              <button 
                class="flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-6 py-3.5 rounded-full font-bold text-base hover:bg-white/30 transition-all border border-white/30"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                </svg>
              </button>
              <button 
                @click="openMovieDetail(movie)"
                class="flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-6 py-3.5 rounded-full font-bold text-base hover:bg-white/30 transition-all border border-white/30"
                title="Các tập & Thông tin"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Dots Indicator -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <button
          v-for="(movie, index) in featuredMovies"
          :key="`dot-${movie.slug}`"
          @click="goToBanner(index)"
          class="transition-all duration-500 ease-out rounded-full transform hover:scale-125"
          :class="currentBannerIndex === index 
            ? 'w-10 h-2.5 bg-yellow-400 shadow-lg shadow-yellow-400/50' 
            : 'w-2.5 h-2.5 bg-white/50 hover:bg-white/80'"
        ></button>
      </div>
    </div>

    <!-- Movie Sections -->
    <!-- Featured Carousel -->
    <FeaturedCarousel />

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-10 relative z-20">

      <!-- Phim Mới Cập Nhật -->
      <MovieRow 
        v-if="newMovies?.length"
        title="🆕 Phim Mới Cập Nhật" 
        :movies="newMovies"
      />

      <!-- Phim Bộ -->
      <MovieRow 
        v-if="seriesMovies?.length"
        title="📺 Phim Bộ Hot" 
        :movies="seriesMovies"
      />

      <!-- Phim Lẻ -->
      <MovieRow 
        v-if="singleMovies?.length"
        title="🎬 Phim Lẻ Mới" 
        :movies="singleMovies"
      />

      <!-- Phim Hàn Quốc -->
      <MovieRow 
        v-if="koreanMovies?.length"
        title="🇰🇷 Phim Hàn Quốc" 
        :movies="koreanMovies"
      />

      <!-- Phim Trung Quốc -->
      <MovieRow 
        v-if="chineseMovies?.length"
        title="🇨🇳 Phim Trung Quốc" 
        :movies="chineseMovies"
      />

      <!-- Phim Anime -->
      <MovieRow 
        v-if="animeMovies?.length"
        title="🎌 Phim Anime" 
        :movies="animeMovies"
      />
    </div>

    <!-- Community Section -->
    <CommunitySection />

    <!-- Movie Detail Modal -->
    <MovieDetailModal
      :is-open="isModalOpen"
      :movie-slug="selectedMovieSlug"
      @close="isModalOpen = false"
      @open-movie="handleOpenMovie"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import MovieRow from '@/components/MovieRow.vue';
import CommunitySection from '@/components/CommunitySection.vue';
import FeaturedCarousel from '@/components/FeaturedCarousel.vue';
import MovieDetailModal from '@/components/MovieDetailModal.vue';

const router = useRouter();

const featuredMovies = ref([]); // Danh sách 3-5 phim featured
const currentBannerIndex = ref(0);
const recommendedMovies = ref([]);
const newMovies = ref([]);
const seriesMovies = ref([]);
const singleMovies = ref([]);
const koreanMovies = ref([]);
const chineseMovies = ref([]);
const animeMovies = ref([]);
const showTrailer = ref(false);
const currentTrailerUrl = ref('');
let autoSlideInterval = null;
const heroBannerRef = ref(null);
let touchStartX = 0;
let touchEndX = 0;
let isScrolling = false;
const isModalOpen = ref(false);
const selectedMovieSlug = ref('');

const featuredMovie = computed(() => featuredMovies.value[currentBannerIndex.value] || null);

const openMovieDetail = (movie) => {
  if (movie?.slug) {
    selectedMovieSlug.value = movie.slug;
    isModalOpen.value = true;
  }
};

const handleOpenMovie = (slug) => {
  console.log('🎬 Opening new movie from modal:', slug);
  selectedMovieSlug.value = slug;
  isModalOpen.value = true;
};

const playMovie = (movie) => {
  if (movie?.slug) {
    router.push(`/film/${movie.slug}`);
  }
};

const getBannerImage = (movie) => {
  if (!movie) return 'https://image.tmdb.org/t/p/original/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg';
  
  // Ưu tiên thumb_url (ảnh ngang cho banner)
  if (movie.thumb_url) {
    const url = movie.thumb_url.startsWith('http') 
      ? movie.thumb_url 
      : `https://phimimg.com/${movie.thumb_url}`;
    console.log('✅ Banner using thumb_url:', url);
    return url;
  }
  
  // Fallback poster_url
  if (movie.poster_url) {
    return movie.poster_url.startsWith('http') 
      ? movie.poster_url 
      : `https://phimimg.com/${movie.poster_url}`;
  }
  
  return 'https://image.tmdb.org/t/p/original/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg';
};

const handleImageError = (e) => {
  e.target.src = 'https://image.tmdb.org/t/p/original/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg';
};

const playTrailer = (url) => {
  if (!url) {
    showTrailer.value = false;
    currentTrailerUrl.value = '';
    return;
  }
  
  // Convert YouTube URL to embed with autoplay và HD quality
  let embedUrl = url;
  if (url.includes('youtube.com/watch')) {
    const videoId = url.split('v=')[1]?.split('&')[0];
    embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&controls=0&showinfo=0&rel=0&modestbranding=1&loop=1&playlist=${videoId}&iv_load_policy=3&disablekb=1&fs=0&vq=hd1080&hd=1&quality=highres`;
  } else if (url.includes('youtu.be/')) {
    const videoId = url.split('youtu.be/')[1]?.split('?')[0];
    embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&controls=0&showinfo=0&rel=0&modestbranding=1&loop=1&playlist=${videoId}&iv_load_policy=3&disablekb=1&fs=0&vq=hd1080&hd=1&quality=highres`;
  }
  
  // Delay để smooth transition
  showTrailer.value = false;
  setTimeout(() => {
    currentTrailerUrl.value = embedUrl;
    showTrailer.value = true;
  }, 500);
};

const nextBanner = () => {
  currentBannerIndex.value = (currentBannerIndex.value + 1) % featuredMovies.value.length;
  const currentMovie = featuredMovies.value[currentBannerIndex.value];
  if (currentMovie?.trailer_url) {
    playTrailer(currentMovie.trailer_url);
  } else {
    showTrailer.value = false;
  }
};

const prevBanner = () => {
  currentBannerIndex.value = currentBannerIndex.value === 0 
    ? featuredMovies.value.length - 1 
    : currentBannerIndex.value - 1;
  const currentMovie = featuredMovies.value[currentBannerIndex.value];
  if (currentMovie?.trailer_url) {
    playTrailer(currentMovie.trailer_url);
  } else {
    showTrailer.value = false;
  }
};

const goToBanner = (index) => {
  currentBannerIndex.value = index;
  const currentMovie = featuredMovies.value[index];
  if (currentMovie?.trailer_url) {
    playTrailer(currentMovie.trailer_url);
  } else {
    showTrailer.value = false;
  }
};

const startAutoSlide = () => {
  // Tắt auto slide - chỉ chuyển khi user tương tác
  // autoSlideInterval = setInterval(() => {
  //   nextBanner();
  // }, 10000);
};

const stopAutoSlide = () => {
  if (autoSlideInterval) {
    clearInterval(autoSlideInterval);
  }
};

const resetAutoSlide = () => {
  // Không reset auto slide nữa
  // stopAutoSlide();
  // startAutoSlide();
};

const handleWheel = (event) => {
  if (isScrolling) return;
  
  // Chỉ xử lý scroll ngang (horizontal scroll)
  if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
    event.preventDefault();
    isScrolling = true;
    
    if (event.deltaX > 0) {
      nextBanner();
    } else {
      prevBanner();
    }
    
    setTimeout(() => {
      isScrolling = false;
    }, 800);
  }
};

const handleTouchStart = (event) => {
  touchStartX = event.changedTouches[0].screenX;
};

const handleTouchMove = (event) => {
  touchEndX = event.changedTouches[0].screenX;
};

const handleTouchEnd = () => {
  if (isScrolling) return;
  
  const swipeDistance = touchStartX - touchEndX;
  const minSwipeDistance = 50;
  
  if (Math.abs(swipeDistance) > minSwipeDistance) {
    isScrolling = true;
    
    if (swipeDistance > 0) {
      // Swipe left - next
      nextBanner();
    } else {
      // Swipe right - prev
      prevBanner();
    }
    
    setTimeout(() => {
      isScrolling = false;
    }, 800);
  }
};

onMounted(async () => {
  try {
    console.log('🚀 Loading movies from API...');
    
    const [
      resFeatured,
      resRecommended,
      resNew,
      resSeries,
      resSingle,
      resKorean,
      resChinese,
      resAnime,
      resMyHeroAcademia
    ] = await Promise.all([
      axios.get('https://phimapi.com/danh-sach/phim-moi-cap-nhat?page=1'),
      axios.get('https://phimapi.com/danh-sach/phim-moi-cap-nhat?page=1'),
      axios.get('https://phimapi.com/danh-sach/phim-moi-cap-nhat?page=2'),
      axios.get('https://phimapi.com/v1/api/danh-sach/phim-bo?page=1&limit=30'),
      axios.get('https://phimapi.com/v1/api/danh-sach/phim-le?page=1&limit=30'),
      axios.get('https://phimapi.com/v1/api/quoc-gia/han-quoc?page=1&limit=25'),
      axios.get('https://phimapi.com/v1/api/quoc-gia/trung-quoc?page=1&limit=25'),
      axios.get('https://phimapi.com/v1/api/quoc-gia/nhat-ban?page=1&limit=25'),
      axios.get('https://phimapi.com/v1/api/tim-kiem?keyword=hoc vien sieu anh hung'),
    ]);
    
    console.log('✅ API calls completed');

    // Thêm "Học viện siêu anh hùng" làm phim ĐẦU TIÊN (nếu tìm được)
    try {
      const myHeroAcademiaList = resMyHeroAcademia?.data?.data?.items || [];
      console.log('🦸 My Hero Academia search results:', myHeroAcademiaList.length);
      
      if (myHeroAcademiaList.length > 0) {
        const myHeroMovie = myHeroAcademiaList[0];
        try {
          const detailRes = await axios.get(`https://phimapi.com/phim/${myHeroMovie.slug}`);
          if (detailRes.data?.movie) {
            featuredMovies.value.push({
              ...myHeroMovie,
              content: detailRes.data.movie.content || myHeroMovie.content,
              trailer_url: detailRes.data.movie.trailer_url
            });
          } else {
            featuredMovies.value.push(myHeroMovie);
          }
        } catch (err) {
          console.log('⚠️ Could not fetch My Hero detail, using basic info');
          featuredMovies.value.push(myHeroMovie);
        }
      }
    } catch (err) {
      console.log('⚠️ My Hero Academia search failed, skipping');
    }
    
    // Lấy 2 phim featured tiếp theo
    const featuredList = resFeatured.data.items?.slice(0, 2) || resFeatured.data.data?.items?.slice(0, 2) || [];
    
    // Fetch chi tiết cho 2 phim featured
    for (const movie of featuredList) {
      if (movie?.slug) {
        try {
          const detailRes = await axios.get(`https://phimapi.com/phim/${movie.slug}`);
          if (detailRes.data?.movie) {
            featuredMovies.value.push({
              ...movie,
              content: detailRes.data.movie.content || movie.content,
              trailer_url: detailRes.data.movie.trailer_url
            });
          } else {
            featuredMovies.value.push(movie);
          }
        } catch (err) {
          featuredMovies.value.push(movie);
        }
      }
    }
    
    // Auto play trailer của phim đầu tiên nếu có
    if (featuredMovies.value[0]?.trailer_url) {
      playTrailer(featuredMovies.value[0].trailer_url);
    }
    
    console.log('✅ Featured movies loaded:', featuredMovies.value.length);
    
    // Sắp xếp phim đề cử theo năm mới nhất và quality cao
    const recommended = resRecommended.data.items || resRecommended.data.data?.items || [];
    recommendedMovies.value = recommended.sort((a, b) => {
      // Ưu tiên quality cao (FHD > HD > SD)
      const qualityOrder = { 'FHD': 3, 'HD': 2, 'SD': 1, '': 0 };
      const qualityA = qualityOrder[a.quality] || 0;
      const qualityB = qualityOrder[b.quality] || 0;
      
      if (qualityA !== qualityB) {
        return qualityB - qualityA; // Quality cao hơn lên trước
      }
      
      // Nếu quality bằng nhau, ưu tiên năm mới hơn
      const yearA = parseInt(a.year) || 0;
      const yearB = parseInt(b.year) || 0;
      return yearB - yearA;
    });
    
    newMovies.value = resNew.data.items || resNew.data.data?.items || [];
    seriesMovies.value = resSeries.data.data?.items || [];
    singleMovies.value = resSingle.data.data?.items || [];
    koreanMovies.value = resKorean.data.data?.items || [];
    chineseMovies.value = resChinese.data.data?.items || [];
    animeMovies.value = resAnime.data.data?.items || [];

    console.log('✅ Movies loaded successfully');
    console.log('🎬 Featured:', featuredMovie.value?.name);
    console.log('📝 Content:', featuredMovie.value?.content?.substring(0, 100));
    
    // Không start auto slide nữa - chỉ chuyển khi user tương tác
  } catch (err) {
    console.error('❌ Error loading movies:', err);
    toast.error('Không thể tải dữ liệu phim');
  }
});

onUnmounted(() => {
  stopAutoSlide();
});
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Ẩn scrollbar hoặc làm mỏng hơn */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}
</style>
