<template>
  <div class="netflix-home bg-black min-h-screen text-white">
    <NetflixNavbar />
    <!-- Hero Banner Carousel -->
    <div class="hero-section relative h-[85vh] overflow-hidden group/hero">
      <!-- Banners -->
      <div 
        v-for="(movie, index) in featuredMovies" 
        :key="movie.slug"
        class="absolute inset-0 transition-opacity duration-700"
        :class="currentHeroIndex === index ? 'opacity-100 z-10' : 'opacity-0 z-0'"
      >
        <!-- Background Image -->
        <div class="absolute inset-0">
          <img 
            :src="getBannerImage(movie)"
            class="w-full h-full object-cover"
            :alt="movie.name"
            @error="handleImageError"
          />
          <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-black/40"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/50"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 h-full flex items-center">
          <div class="container mx-auto px-4 md:px-12 flex items-end pb-20 gap-8">
            <!-- Left: Movie Info -->
            <div class="flex-1 max-w-3xl">
              <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-4 drop-shadow-2xl uppercase tracking-tight leading-tight">
                {{ movie?.name || 'Khám phá những bộ phim tuyệt vời' }}
              </h1>
              <p class="text-sm md:text-base text-gray-300 mb-3">
                {{ movie?.origin_name || '' }}
              </p>

              <!-- Badges -->
              <div class="flex gap-2 mb-4 flex-wrap">
                <span v-if="movie?.quality" class="bg-yellow-500 text-black px-3 py-1 rounded text-xs font-bold uppercase shadow-xl">{{ movie.quality }}</span>
                <span v-if="movie?.lang" class="bg-gray-900/90 text-white px-3 py-1 rounded text-xs font-bold uppercase shadow-xl border-2 border-gray-700">{{ movie.lang }}</span>
                <span v-if="movie?.year" class="bg-gray-900/90 text-white px-3 py-1 rounded text-xs font-bold shadow-xl border-2 border-gray-700">{{ movie.year }}</span>
                <span v-if="movie?.time" class="bg-gray-900/90 text-white px-3 py-1 rounded text-xs font-bold shadow-xl border-2 border-gray-700">{{ movie.time }}</span>
              </div>

              <!-- Genres -->
              <div class="flex gap-3 mb-4 flex-wrap text-sm">
                <span 
                  v-for="genre in movie?.category?.slice(0, 4)" 
                  :key="genre.slug"
                  class="text-gray-300"
                >
                  {{ genre.name }}
                </span>
              </div>

              <!-- Description -->
              <p class="text-sm md:text-base mb-6 line-clamp-3 text-gray-200 max-w-2xl leading-relaxed">
                {{ movie?.content || 'Khám phá những bộ phim tuyệt vời cùng HTHREE' }}
              </p>
              
              <!-- Action Buttons -->
              <div class="flex gap-4 items-center">
                <button 
                  @click="playMovie(movie)"
                  class="btn-play flex items-center justify-center gap-3 bg-yellow-400 text-black px-6 py-3 rounded-full font-bold text-base hover:bg-yellow-500 transition-all shadow-lg hover:shadow-yellow-400/50"
                >
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                  </svg>
                  Xem Ngay
                </button>
                <button class="btn-icon flex items-center justify-center w-12 h-12 bg-gray-800/80 text-white rounded-full hover:bg-gray-700 transition-all">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                  </svg>
                </button>
                <button 
                  @click="playMovie(movie)"
                  class="btn-icon flex items-center justify-center w-12 h-12 bg-gray-800/80 text-white rounded-full hover:bg-gray-700 transition-all"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Right: Thumbnail Carousel -->
            <div class="hidden xl:flex flex-col gap-3 flex-shrink-0">
              <div 
                v-for="(thumbMovie, thumbIndex) in featuredMovies" 
                :key="thumbMovie.slug"
                @click="goToHeroSlide(thumbIndex)"
                class="w-32 h-20 rounded-lg overflow-hidden cursor-pointer transition-all hover:scale-105 hover:ring-2 hover:ring-yellow-400"
                :class="thumbIndex === currentHeroIndex ? 'ring-2 ring-yellow-400' : ''"
              >
                <img 
                  :src="thumbMovie.thumb_url || thumbMovie.poster_url || 'https://placehold.co/128x80/333/fff?text=Movie'"
                  :alt="thumbMovie.name"
                  class="w-full h-full object-cover"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Arrows -->
      <button 
        @click="prevHeroSlide"
        class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-14 h-14 bg-black/50 hover:bg-black/80 rounded-full flex items-center justify-center transition-all opacity-0 group-hover/hero:opacity-100"
      >
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>
      
      <button 
        @click="nextHeroSlide"
        class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-14 h-14 bg-black/50 hover:bg-black/80 rounded-full flex items-center justify-center transition-all opacity-0 group-hover/hero:opacity-100"
      >
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>

      <!-- Dots Indicator -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <button
          v-for="(movie, index) in featuredMovies"
          :key="`hero-dot-${movie.slug}`"
          @click="goToHeroSlide(index)"
          class="transition-all rounded-full"
          :class="currentHeroIndex === index 
            ? 'w-10 h-2.5 bg-yellow-400' 
            : 'w-2.5 h-2.5 bg-white/50 hover:bg-white/80'"
        ></button>
      </div>

      <!-- Fade bottom -->
      <div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-black to-transparent pointer-events-none"></div>
    </div>

    <!-- Movie Rows -->
    <div class="movie-rows -mt-20 relative z-20 pb-20">
      <!-- Banner Carousel: Học viện siêu anh hùng -->
      <BannerCarousel 
        v-if="myHeroAcademiaMovies?.length"
        title="🦸 Học Viện Siêu Anh Hùng" 
        :movies="myHeroAcademiaMovies"
      />

      <!-- Banner Carousel: Đấm phát chết luôn -->
      <BannerCarousel 
        v-if="onePunchManMovies?.length"
        title="👊 Đấm Phát Chết Luôn" 
        :movies="onePunchManMovies"
      />

      <!-- Phim Mới Cập Nhật -->
      <MovieRow 
        v-if="newMovies?.length"
        title="🆕 Phim Mới Cập Nhật" 
        :movies="newMovies"
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

      <!-- Phim Đề Cử -->
      <MovieRow 
        v-if="recommendedMovies?.length"
        title="🔥 Phim Đề Cử" 
        :movies="recommendedMovies"
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

      <!-- Phim Hành Động -->
      <MovieRow 
        v-if="actionMovies?.length"
        title="💥 Phim Hành Động" 
        :movies="actionMovies"
      />

      <!-- Phim Tâm Lý -->
      <MovieRow 
        v-if="dramaMovies?.length"
        title="💔 Phim Tâm Lý" 
        :movies="dramaMovies"
      />

      <!-- Phim Hài Hước -->
      <MovieRow 
        v-if="comedyMovies?.length"
        title="😂 Phim Hài Hước" 
        :movies="comedyMovies"
      />

      <!-- Phim Kinh Dị -->
      <MovieRow 
        v-if="horrorMovies?.length"
        title="👻 Phim Kinh Dị" 
        :movies="horrorMovies"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import NetflixNavbar from '@/components/NetflixNavbar.vue';
import MovieRow from '@/components/MovieRow.vue';
import BannerCarousel from '@/components/BannerCarousel.vue';

const router = useRouter();

const featuredMovies = ref([]);
const currentHeroIndex = ref(0);
let heroAutoSlideInterval = null;
const newMovies = ref([]);
const koreanMovies = ref([]);
const chineseMovies = ref([]);
const animeMovies = ref([]);
const recommendedMovies = ref([]);
const seriesMovies = ref([]);
const singleMovies = ref([]);
const actionMovies = ref([]);
const dramaMovies = ref([]);
const comedyMovies = ref([]);
const horrorMovies = ref([]);
const myHeroAcademiaMovies = ref([]);
const onePunchManMovies = ref([]);

const playMovie = (movie) => {
  if (movie?.slug) {
    router.push(`/film/${movie.slug}`);
  }
};

const goToMovie = (movie) => {
  if (movie?.slug) {
    router.push(`/film/${movie.slug}`);
  }
};

const getBannerImage = (movie) => {
  console.log('🖼️ getBannerImage called with movie:', movie);
  
  if (!movie) {
    console.log('⚠️ No movie provided, using fallback');
    return 'https://image.tmdb.org/t/p/original/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg';
  }
  
  // Ưu tiên thumb_url (ảnh ngang - backdrop)
  if (movie.thumb_url) {
    const url = movie.thumb_url.startsWith('http') 
      ? movie.thumb_url 
      : `https://phimimg.com/${movie.thumb_url}`;
    console.log('✅ Using thumb_url:', url);
    return url;
  }
  
  // Fallback sang poster_url
  if (movie.poster_url) {
    const url = movie.poster_url.startsWith('http') 
      ? movie.poster_url 
      : `https://phimimg.com/${movie.poster_url}`;
    console.log('✅ Using poster_url:', url);
    return url;
  }
  
  // Fallback cuối cùng
  console.log('⚠️ No image found, using fallback');
  return 'https://image.tmdb.org/t/p/original/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg';
};

const handleImageError = (e) => {
  e.target.src = 'https://image.tmdb.org/t/p/original/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg';
};

const nextHeroSlide = () => {
  currentHeroIndex.value = (currentHeroIndex.value + 1) % featuredMovies.value.length;
  resetHeroAutoSlide();
};

const prevHeroSlide = () => {
  currentHeroIndex.value = currentHeroIndex.value === 0 
    ? featuredMovies.value.length - 1 
    : currentHeroIndex.value - 1;
  resetHeroAutoSlide();
};

const goToHeroSlide = (index) => {
  currentHeroIndex.value = index;
  resetHeroAutoSlide();
};

const startHeroAutoSlide = () => {
  heroAutoSlideInterval = setInterval(() => {
    nextHeroSlide();
  }, 10000); // Auto slide every 10 seconds
};

const stopHeroAutoSlide = () => {
  if (heroAutoSlideInterval) {
    clearInterval(heroAutoSlideInterval);
  }
};

const resetHeroAutoSlide = () => {
  stopHeroAutoSlide();
  startHeroAutoSlide();
};

onMounted(async () => {
  try {
    console.log('🚀 Starting to load movies from API...');
    
    // Load tất cả phim từ API
    const [
      resFeatured,
      resNew,
      resKorean,
      resChinese,
      resAnime,
      resRecommended,
      resSeries,
      resSingle,
      resAction,
      resDrama,
      resComedy,
      resHorror,
      resMyHeroAcademia,
      resOnePunchMan
    ] = await Promise.all([
      axios.get('https://phimapi.com/danh-sach/phim-moi-cap-nhat?page=1'),
      axios.get('https://phimapi.com/danh-sach/phim-moi-cap-nhat?page=1'),
      axios.get('https://phimapi.com/v1/api/quoc-gia/han-quoc?page=1'),
      axios.get('https://phimapi.com/v1/api/quoc-gia/trung-quoc?page=1'),
      axios.get('https://phimapi.com/v1/api/quoc-gia/nhat-ban?page=1'),
      axios.get('https://phimapi.com/danh-sach/phim-moi-cap-nhat?page=2'),
      axios.get('https://phimapi.com/v1/api/danh-sach/phim-bo?page=1'),
      axios.get('https://phimapi.com/v1/api/danh-sach/phim-le?page=1'),
      axios.get('https://phimapi.com/v1/api/the-loai/hanh-dong?page=1'),
      axios.get('https://phimapi.com/v1/api/the-loai/tam-ly?page=1'),
      axios.get('https://phimapi.com/v1/api/the-loai/hai-huoc?page=1'),
      axios.get('https://phimapi.com/v1/api/the-loai/kinh-di?page=1'),
      axios.get('https://phimapi.com/v1/api/tim-kiem?keyword=hoc vien sieu anh hung'),
      axios.get('https://phimapi.com/v1/api/tim-kiem?keyword=one punch man'),
    ]);

    console.log('📦 Featured API response:', resFeatured.data);
    
    // Set featured movies (lấy 5 phim đầu cho carousel)
    const allFeatured = resFeatured.data.items || resFeatured.data.data?.items || [];
    featuredMovies.value = allFeatured.slice(0, 5);
    
    console.log('🎬 Featured movies count:', featuredMovies.value.length);

    // Set các danh sách phim
    newMovies.value = resNew.data.items || resNew.data.data?.items || [];
    koreanMovies.value = resKorean.data.data?.items || [];
    chineseMovies.value = resChinese.data.data?.items || [];
    animeMovies.value = resAnime.data.data?.items || [];
    recommendedMovies.value = resRecommended.data.items || resRecommended.data.data?.items || [];
    seriesMovies.value = resSeries.data.data?.items || [];
    singleMovies.value = resSingle.data.data?.items || [];
    actionMovies.value = resAction.data.data?.items || [];
    dramaMovies.value = resDrama.data.data?.items || [];
    comedyMovies.value = resComedy.data.data?.items || [];
    horrorMovies.value = resHorror.data.data?.items || [];
    myHeroAcademiaMovies.value = (resMyHeroAcademia.data.data?.items || []).slice(0, 5);
    onePunchManMovies.value = (resOnePunchMan.data.data?.items || []).slice(0, 5);
    
    console.log('✅ All movies loaded successfully');
    
    // Start hero auto slide
    startHeroAutoSlide();
  } catch (err) {
    console.error('❌ Error loading movies:', err);
    toast.error('Không thể tải dữ liệu phim');
  }
});

onUnmounted(() => {
  stopHeroAutoSlide();
});
</script>

<style scoped>
.netflix-home {
  font-family: 'Netflix Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
}

.btn-play:hover {
  transform: scale(1.05);
}

.btn-info:hover {
  transform: scale(1.05);
}
</style>
