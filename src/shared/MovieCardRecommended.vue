<template>
  <router-link
    v-if="movie"
    :to="`/film/${movie?.slug}`"
    class="relative block w-full aspect-[2/3] rounded-md overflow-hidden group transition-transform duration-300 ease-out hover:scale-105"
  >
    <img
      :src="getImageUrl(movie)"
      :alt="movie?.title || movie?.name || 'Movie Poster'"
      class="w-full h-full object-cover"
      @error="handleImageError"
      @load="handleImageLoad"
      loading="lazy"
    />
    <div
      class="absolute bottom-0 left-0 right-0 h-2/5 bg-gradient-to-t from-black/80 via-black/40 to-transparent"
    ></div>

    <div
      v-if="movie?.episode_current && movie?.lang"
      class="absolute top-1 left-0 bg-black/60 text-white text-[10px] px-1.5 py-0.5 rounded-sm"
    >
      {{ movie.episode_current }} + {{ movie.lang }}
    </div>
    <div
      v-if="trending"
      class="absolute top-8 left-0 text-white bg-red-500 text-xs font-semibold px-2 py-1 rounded-sm shadow-md"
    >
      Trending
    </div>

    <div class="absolute bottom-0 left-0 right-0 p-2.5 sm:p-3 text-white z-10">
      <h3
        class="text-sm sm:text-base font-bold truncate group-hover:whitespace-normal group-hover:text-phim-red transition-colors"
        :title="movie?.title || movie?.name"
      >
        {{ movie?.title || movie?.name }}
      </h3>
      <p
        v-if="movie?.original_title || movie?.origin_name"
        class="text-xs text-gray-300 truncate group-hover:whitespace-normal"
        :title="movie?.original_title || movie?.origin_name"
      >
        {{ movie?.original_title || movie?.origin_name }}
      </p>
    </div>
    <div
      class="absolute inset-0 opacity-0 flex justify-center items-center group-hover:opacity-100 transition-opacity duration-300"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="64"
        height="64"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="text-white opacity-80"
      >
        <circle cx="12" cy="12" r="10" />
        <polygon points="10 8 16 12 10 16 10 8" />
      </svg>
    </div>
  </router-link>
</template>

<script setup>
import { defineProps, watch } from 'vue'; 
import { RouterLink } from 'vue-router'; 

const props = defineProps({
  movie: {
    type: Object,
    default: null,
  },
  trending: {
    type: Boolean,
    default: false,
  },
});

// Get image URL with fallback
const getImageUrl = (movie) => {
  if (!movie) {
    return 'https://via.placeholder.com/400x600/1a1a1a/ffffff?text=No+Movie';
  }
  
  // Try different image sources
  // poster_path = ảnh dọc (dùng cho card)
  // backdrop_path = ảnh ngang (dùng cho banner)
  const imageUrl = movie.poster_path || movie.poster_url || movie.backdrop_path || movie.thumb_url;
  
  // Debug log - Check which image is used
  if (movie.title === 'Hai Cuộc Đời Một Trái Tim (Bác Sĩ Tim)' || movie.name === 'Hai Cuộc Đời Một Trái Tim (Bác Sĩ Tim)') {
    console.log('🔍 First movie image check:', {
      title: movie.title || movie.name,
      backdrop_path: movie.backdrop_path,
      poster_path: movie.poster_path,
      selected: imageUrl
    });
  }
  
  if (!imageUrl) {
    return 'https://via.placeholder.com/400x600/1a1a1a/ffffff?text=No+Image';
  }
  
  // If URL is relative, add base URL
  if (!imageUrl.startsWith('http')) {
    // Add base URL for phimimg.com (silent fix)
    return `https://phimimg.com/${imageUrl}`;
  }
  
  // WORKAROUND: If phimimg.com has CORS issue, use proxy
  // Uncomment this if images don't load:
  // if (imageUrl.includes('phimimg.com')) {
  //   return `https://images.weserv.nl/?url=${encodeURIComponent(imageUrl)}`;
  // }
  
  return imageUrl;
};

// Handle image load success
const handleImageLoad = (e) => {
  // Silent success
};

// Handle image error
const handleImageError = (e) => {
  // Set placeholder silently
  e.target.src = 'https://via.placeholder.com/400x600/1a1a1a/ffffff?text=Error';
}; 
</script>

<style scoped>
.group-hover\:text-phim-red {
  color: #f09258; 
}
</style>
