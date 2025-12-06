<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-900 via-black to-gray-900 pt-24 pb-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-12 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
          Tất cả thể loại
        </h1>
        <p class="text-gray-400 text-lg">
          Khám phá phim theo thể loại yêu thích của bạn
        </p>
      </div>

      <!-- Categories Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <router-link
          v-for="category in allCategories"
          :key="category.slug"
          :to="`/category/${category.slug}/page/1`"
          class="group relative bg-gray-800 hover:bg-gray-700 rounded-xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-yellow-400/20 border border-gray-700 hover:border-yellow-400"
        >
          <!-- Icon -->
          <div class="flex items-center justify-center mb-4">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
              </svg>
            </div>
          </div>

          <!-- Category Name -->
          <h3 class="text-white font-semibold text-center text-lg group-hover:text-yellow-400 transition-colors">
            {{ category.name }}
          </h3>

          <!-- Arrow Icon -->
          <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </router-link>
      </div>

      <!-- Empty State -->
      <div v-if="allCategories.length === 0" class="text-center py-20">
        <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
        </svg>
        <p class="text-gray-400 text-lg">Không có thể loại nào</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useCategoryStore } from '@/stores/Category/category.js';

const categoryStore = useCategoryStore();

const allCategories = computed(() => {
  return categoryStore.getAllCategories || [];
});

onMounted(async () => {
  if (!categoryStore.getAllCategories?.length) {
    await categoryStore.getCategory();
  }
});
</script>
