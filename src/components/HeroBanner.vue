<template>
  <div class="hero min-h-[500px] bg-base-200 relative overflow-hidden">
    <!-- Background image với overlay -->
    <div 
      class="absolute inset-0 bg-cover bg-center"
      :style="{ backgroundImage: `url(${featuredMovie?.poster_url || 'https://placehold.co/1920x1080/1a1a1a/fff'})` }"
    >
      <div class="absolute inset-0 bg-gradient-to-r from-base-100 via-base-100/80 to-transparent"></div>
    </div>
    
    <div class="hero-content text-left relative z-10 max-w-7xl w-full">
      <div class="max-w-2xl">
        <div class="flex gap-2 mb-4">
          <div class="badge badge-error">HOT</div>
          <div class="badge badge-warning">{{ featuredMovie?.year || '2024' }}</div>
          <div class="badge badge-info">{{ featuredMovie?.quality || 'HD' }}</div>
        </div>
        
        <h1 class="text-5xl font-bold mb-4 drop-shadow-lg">
          {{ featuredMovie?.name || 'Phim nổi bật' }}
        </h1>
        
        <p class="text-lg mb-2 text-base-content/90">
          {{ featuredMovie?.origin_name || 'Original Title' }}
        </p>
        
        <p class="mb-6 line-clamp-3 text-base-content/80">
          {{ featuredMovie?.description || 'Mô tả phim sẽ hiển thị ở đây. Đây là một bộ phim hấp dẫn với nhiều tình tiết thú vị...' }}
        </p>
        
        <div class="flex gap-3">
          <button class="btn btn-primary btn-lg gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
              <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
            </svg>
            Xem ngay
          </button>
          <button class="btn btn-outline btn-lg gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Thông tin
          </button>
        </div>
        
        <!-- Thể loại -->
        <div class="flex gap-2 mt-6">
          <span class="text-sm text-base-content/70">Thể loại:</span>
          <div class="flex gap-2 flex-wrap">
            <span 
              v-for="genre in featuredMovie?.genres?.slice(0, 4)" 
              :key="genre"
              class="text-sm text-primary font-medium"
            >
              {{ genre }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  featuredMovie: {
    type: Object,
    default: null
  }
});
</script>
