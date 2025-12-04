<template>
  <div class="bg-black min-h-screen text-white p-8">
    <h1 class="text-3xl font-bold mb-8">🧪 Test Images</h1>
    
    <div class="grid grid-cols-3 gap-4 mb-8">
      <!-- Test 1: Placeholder -->
      <div>
        <h3 class="mb-2">1. Placeholder (Should work)</h3>
        <img 
          src="https://via.placeholder.com/400x600/1a1a1a/ffffff?text=Test"
          class="w-full h-64 object-cover rounded"
          alt="Test 1"
        />
      </div>

      <!-- Test 2: TMDB Image -->
      <div>
        <h3 class="mb-2">2. TMDB (Should work)</h3>
        <img 
          src="https://image.tmdb.org/t/p/w500/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg"
          class="w-full h-64 object-cover rounded"
          alt="Test 2"
        />
      </div>

      <!-- Test 3: phimimg.com - Real URL from Supabase -->
      <div>
        <h3 class="mb-2">3. phimimg.com (Your Supabase URL)</h3>
        <img 
          src="https://phimimg.com/upload/vod/20250916-1/0207ea115f4bfe35d90af8f8807d4cbe.jpg"
          class="w-full h-64 object-cover rounded"
          alt="Test 3"
          @error="handleError"
          @load="handleLoad"
        />
      </div>
    </div>

    <div class="bg-gray-900 p-4 rounded">
      <h3 class="font-bold mb-2">Console Log:</h3>
      <div v-for="(log, i) in logs" :key="i" class="text-sm mb-1">
        {{ log }}
      </div>
    </div>

    <div class="mt-8">
      <router-link to="/home" class="btn bg-red-600 px-6 py-2 rounded">
        ← Back to Home
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const logs = ref([]);

const handleError = (e) => {
  const msg = `❌ Image failed: ${e.target.src}`;
  console.error(msg);
  logs.value.push(msg);
};

const handleLoad = (e) => {
  const msg = `✅ Image loaded: ${e.target.src}`;
  console.log(msg);
  logs.value.push(msg);
};
</script>
