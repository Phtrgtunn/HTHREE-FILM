<template>
  <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 group">
    <figure class="relative overflow-hidden aspect-[2/3]">
      <img 
        :src="movie.thumb_url || 'https://placehold.co/300x450/1a1a1a/fff?text=No+Image'" 
        :alt="movie.name"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
      />
      <!-- Badge chất lượng -->
      <div class="badge badge-primary absolute top-2 right-2">{{ movie.quality || 'HD' }}</div>
      
      <!-- Overlay khi hover -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
        <div class="text-white w-full">
          <p class="text-sm line-clamp-2 mb-2">{{ movie.description || 'Chưa có mô tả' }}</p>
          <div class="flex gap-2">
            <button class="btn btn-primary btn-sm flex-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
              </svg>
              Xem ngay
            </button>
            <button
              @click="openModal"
              class="btn btn-circle btn-sm bg-gray-800 hover:bg-gray-700 border-gray-600"
              title="Các tập & Thông tin"
            >
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </figure>
    
    <div class="card-body p-4">
      <h2 class="card-title text-base line-clamp-1">
        {{ movie.name }}
        <div class="badge badge-secondary badge-sm" v-if="movie.year">{{ movie.year }}</div>
      </h2>
      <p class="text-sm text-base-content/70 line-clamp-1">
        {{ movie.origin_name || movie.name }}
      </p>
      <div class="card-actions justify-between items-center mt-2">
        <div class="flex gap-1">
          <div class="badge badge-outline badge-sm" v-for="genre in movie.genres?.slice(0, 2)" :key="genre">
            {{ genre }}
          </div>
        </div>
        <div class="rating rating-sm">
          <input type="radio" class="mask mask-star-2 bg-orange-400" disabled checked />
          <span class="text-xs ml-1">{{ movie.rating || '8.5' }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Movie Detail Modal -->
  <MovieDetailModal
    :is-open="isModalOpen"
    :movie-slug="movie.slug"
    @close="isModalOpen = false"
  />
</template>

<script setup>
import { ref } from 'vue';
import MovieDetailModal from './MovieDetailModal.vue';

defineProps({
  movie: {
    type: Object,
    required: true
  }
});

const isModalOpen = ref(false);

const openModal = () => {
  isModalOpen.value = true;
};
</script>
