<template>
  <div class="banner-carousel mb-16 px-4 md:px-12">
    <!-- Title -->
    <div class="flex items-center gap-3 mb-6">
      <div class="w-1 h-8 bg-yellow-400 rounded-full"></div>
      <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
        {{ title }}
      </h2>
    </div>
    
    <div class="relative group/carousel">
      <!-- Banner Container -->
      <div class="relative h-[400px] md:h-[500px] rounded-2xl overflow-hidden">
        <!-- Banners -->
        <div 
          v-for="(movie, index) in movies" 
          :key="movie.slug"
          class="absolute inset-0 transition-all duration-1000 ease-in-out"
          :class="currentIndex === index ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 z-0'"
        >
          <!-- Background Image -->
          <img 
            :src="getBannerImage(movie)"
            :alt="movie.name"
            class="w-full h-full object-cover"
          />
          
          <!-- Gradient Overlays -->
          <div class="absolute inset-0 bg-gradient-to-r from-black via-black/70 to-transparent"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/30"></div>
          
          <!-- Content -->
          <div class="absolute inset-0 flex items-end">
            <div class="p-8 md:p-12 max-w-2xl">
              <h3 class="text-3xl md:text-5xl font-bold mb-3 drop-shadow-2xl">
                {{ movie.name }}
              </h3>
              <p class="text-sm md:text-base text-gray-300 mb-3">
                {{ movie.origin_name }}
              </p>
              
              <!-- Badges -->
              <div class="flex gap-2 mb-4 flex-wrap">
                <span v-if="movie.quality" class="bg-yellow-500 text-black px-3 py-1 rounded text-xs font-bold uppercase shadow-xl">
                  {{ movie.quality }}
                </span>
                <span v-if="movie.lang" class="bg-gray-900/90 text-white px-3 py-1 rounded text-xs font-bold uppercase shadow-xl border-2 border-gray-700">
                  {{ movie.lang }}
                </span>
                <span v-if="movie.year" class="bg-gray-900/90 text-white px-3 py-1 rounded text-xs font-bold shadow-xl border-2 border-gray-700">
                  {{ movie.year }}
                </span>
                <span v-if="movie.time" class="bg-gray-900/90 text-white px-3 py-1 rounded text-xs font-bold shadow-xl border-2 border-gray-700">
                  {{ movie.time }}
                </span>
              </div>
              
              <!-- Genres -->
              <div v-if="movie?.category?.length" class="flex flex-wrap gap-2 mb-4 text-sm">
                <span 
                  v-for="(genre, i) in movie.category.slice(0, 4)" 
                  :key="i"
                  class="text-gray-300 font-semibold"
                >
                  {{ genre.name }}
                </span>
              </div>
              
              <!-- Description -->
              <p class="text-sm md:text-base text-gray-200 mb-6 line-clamp-2 leading-relaxed">
                {{ movie.content }}
              </p>
              
              <!-- Action Button -->
              <button 
                @click="goToMovie(movie)"
                class="flex items-center gap-2 bg-yellow-400 text-black px-6 py-3 rounded-full font-bold hover:bg-yellow-500 transition-all shadow-lg hover:shadow-yellow-400/50"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                </svg>
                Xem Ngay
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Navigation Arrows -->
      <button 
        @click="prevSlide"
        class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-black/50 hover:bg-yellow-400 rounded-full flex items-center justify-center transition-all duration-300 opacity-0 group-hover/carousel:opacity-100 hover:scale-110"
      >
        <svg class="w-6 h-6 text-white hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>
      
      <button 
        @click="nextSlide"
        class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-black/50 hover:bg-yellow-400 rounded-full flex items-center justify-center transition-all duration-300 opacity-0 group-hover/carousel:opacity-100 hover:scale-110"
      >
        <svg class="w-6 h-6 text-white hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>
      
      <!-- Dots Indicator -->
      <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        <button
          v-for="(movie, index) in movies"
          :key="`dot-${movie.slug}`"
          @click="goToSlide(index)"
          class="transition-all duration-500 rounded-full"
          :class="currentIndex === index 
            ? 'w-8 h-2 bg-yellow-400' 
            : 'w-2 h-2 bg-white/50 hover:bg-white/80 hover:scale-125'"
        ></button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';

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
const currentIndex = ref(0);
let autoSlideInterval = null;

const getBannerImage = (movie) => {
  if (movie.thumb_url) {
    return movie.thumb_url.startsWith('http') 
      ? movie.thumb_url 
      : `https://phimimg.com/${movie.thumb_url}`;
  }
  if (movie.poster_url) {
    return movie.poster_url.startsWith('http') 
      ? movie.poster_url 
      : `https://phimimg.com/${movie.poster_url}`;
  }
  return 'https://placehold.co/1200x500/1a1a1a/fff?text=Banner';
};

const nextSlide = () => {
  currentIndex.value = (currentIndex.value + 1) % props.movies.length;
};

const prevSlide = () => {
  currentIndex.value = currentIndex.value === 0 
    ? props.movies.length - 1 
    : currentIndex.value - 1;
};

const goToSlide = (index) => {
  currentIndex.value = index;
};

const goToMovie = (movie) => {
  if (movie.slug) {
    router.push(`/film/${movie.slug}`);
  }
};

const startAutoSlide = () => {
  autoSlideInterval = setInterval(() => {
    nextSlide();
  }, 8000); // Auto slide every 8 seconds
};

const stopAutoSlide = () => {
  if (autoSlideInterval) {
    clearInterval(autoSlideInterval);
  }
};

onMounted(() => {
  startAutoSlide();
});

onUnmounted(() => {
  stopAutoSlide();
});
</script>

<style scoped>
.banner-carousel {
  font-family: 'Netflix Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
}
</style>
