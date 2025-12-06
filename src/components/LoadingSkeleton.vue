<template>
  <div class="animate-pulse">
    <!-- Movie Card Skeleton -->
    <div v-if="type === 'card'" class="bg-gray-800 rounded-xl overflow-hidden">
      <div class="aspect-[2/3] bg-gray-700"></div>
      <div class="p-3 space-y-2">
        <div class="h-4 bg-gray-700 rounded w-3/4"></div>
        <div class="h-3 bg-gray-700 rounded w-1/2"></div>
      </div>
    </div>

    <!-- Hero Banner Skeleton -->
    <div v-else-if="type === 'hero'" class="relative h-[85vh] bg-gray-800">
      <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
      <div class="absolute bottom-20 left-8 space-y-4 max-w-2xl">
        <div class="h-12 bg-gray-700 rounded w-3/4"></div>
        <div class="h-4 bg-gray-700 rounded w-full"></div>
        <div class="h-4 bg-gray-700 rounded w-5/6"></div>
        <div class="flex gap-3 mt-6">
          <div class="h-12 w-32 bg-gray-700 rounded-full"></div>
          <div class="h-12 w-32 bg-gray-700 rounded-full"></div>
        </div>
      </div>
    </div>

    <!-- Movie Row Skeleton -->
    <div v-else-if="type === 'row'" class="space-y-4">
      <div class="h-6 bg-gray-700 rounded w-48"></div>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div v-for="i in 6" :key="i" class="aspect-[2/3] bg-gray-700 rounded-xl"></div>
      </div>
    </div>

    <!-- Pricing Card Skeleton -->
    <div v-else-if="type === 'pricing'" class="bg-gray-800 rounded-2xl p-6 border-2 border-gray-700">
      <div class="w-20 h-20 bg-gray-700 rounded-2xl mb-6"></div>
      <div class="h-8 bg-gray-700 rounded w-3/4 mb-2"></div>
      <div class="h-4 bg-gray-700 rounded w-1/2 mb-6"></div>
      <div class="h-12 bg-gray-700 rounded w-full mb-4"></div>
      <div class="space-y-3">
        <div v-for="i in 4" :key="i" class="h-4 bg-gray-700 rounded"></div>
      </div>
      <div class="h-12 bg-gray-700 rounded-xl mt-6"></div>
    </div>

    <!-- Cart Item Skeleton -->
    <div v-else-if="type === 'cart'" class="bg-gray-800 rounded-2xl p-6 border border-gray-700">
      <div class="flex gap-6">
        <div class="w-20 h-20 bg-gray-700 rounded-2xl flex-shrink-0"></div>
        <div class="flex-1 space-y-3">
          <div class="h-6 bg-gray-700 rounded w-1/2"></div>
          <div class="h-4 bg-gray-700 rounded w-3/4"></div>
          <div class="flex gap-2">
            <div class="h-6 w-16 bg-gray-700 rounded-full"></div>
            <div class="h-6 w-16 bg-gray-700 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- List Skeleton (generic) -->
    <div v-else-if="type === 'list'" class="space-y-3">
      <div v-for="i in count" :key="i" class="h-16 bg-gray-700 rounded-xl"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'card',
    validator: (value) => ['card', 'hero', 'row', 'pricing', 'cart', 'list'].includes(value)
  },
  count: {
    type: Number,
    default: 3
  }
});
</script>
