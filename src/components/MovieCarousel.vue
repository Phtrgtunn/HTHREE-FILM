<template>
  <div class="mb-8">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">{{ title }}</h2>
      <router-link :to="viewAllLink" class="btn btn-ghost btn-sm gap-2" v-if="viewAllLink">
        Xem tất cả
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </router-link>
    </div>
    
    <!-- Carousel với DaisyUI -->
    <div class="carousel carousel-center space-x-4 p-4 bg-base-200 rounded-box">
      <div 
        v-for="movie in movies" 
        :key="movie.slug"
        class="carousel-item w-48"
      >
        <router-link :to="`/film/${movie.slug}`" class="w-full">
          <MovieCard :movie="movie" />
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import MovieCard from './MovieCard.vue';

defineProps({
  title: {
    type: String,
    required: true
  },
  movies: {
    type: Array,
    default: () => []
  },
  viewAllLink: {
    type: String,
    default: ''
  }
});
</script>
