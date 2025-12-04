<template>
  <router-link
    v-if="trendingMovie"
    :to="`/film/${trendingMovie?.slug}`"
    class="flex items-start gap-3 mb-4 p-2 rounded-lg transition-colors duration-200 hover:bg-gray-600 cursor-pointer"
  >
    <span class="text-white bg-[#da966e] text-xs px-2 py-1 rounded-sm h-fit font-bold min-w-[28px] text-center">
      {{ index + 1 }}
    </span>
    <img
      :src="trendingMovie.backdrop_path"
      :alt="trendingMovie.title || 'Movie Thumbnail'"
      class="w-[55px] h-[70px] object-cover rounded shadow"
      onerror="this.onerror=null;this.src='https://placehold.co/55x70/ccc/fff?text=No+Img';"
    />
    <div class="flex flex-col text-gray-100 text-sm flex-grow">
      <span class="font-semibold leading-tight break-words" :title="trendingMovie.title">
        {{ trendingMovie.title }}
      </span>
      <span class="text-xs text-gray-400 mt-1">{{ trendingMovie.year }}</span>
      <div class="flex text-yellow-400 mt-1">
        <template v-for="i in 5" :key="i">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 576 512"
            :class="i <= stars ? 'text-yellow-400' : 'text-gray-500'"
            class="w-3 h-3 mr-0.5"
            fill="currentColor"
          >
            <path
              d="M316.9 18.6L225.4 150.3 49.9 171.6c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l129.9 68.3c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L350.6 150.3 259.1 18.6c-10.7-20.9-39.1-20.9-49.8 0z"
            />
          </svg>
        </template>
      </div>
    </div>
  </router-link>
</template>

<script setup>
import { defineProps, computed, watch } from 'vue'; 
import { RouterLink } from 'vue-router'; 

const props = defineProps({
  trendingMovie: { type: Object, default: null },
  index: { type: Number, required: true },
});

// Tính toán số sao dựa trên 'vote_average' từ dữ liệu Supabase
const stars = computed(() => Math.round(props.trendingMovie?.vote_average / 2 || 0));

// DEBUG LOGS (có thể xóa sau khi xác nhận hiển thị đúng)
// Thêm watcher để theo dõi prop 'trendingMovie' và log giá trị
watch(() => props.trendingMovie, (newVal) => {
  if (newVal) {
    console.log(`Rank.vue (Index ${props.index}): trendingMovie received:`, newVal);
    console.log(`Rank.vue (Index ${props.index}): poster_path:`, newVal.backdrop_path); 
    console.log(`Rank.vue (Index ${props.index}): title:`, newVal.title); 
  } else {
    console.log(`Rank.vue (Index ${props.index}): trendingMovie is null.`);
  }
}, { immediate: true }); 
</script>

<style scoped>
/* Không cần thêm style vì đã có trong template */
</style>
