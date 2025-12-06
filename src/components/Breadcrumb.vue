<template>
  <nav aria-label="Breadcrumb" class="mb-6">
    <ol class="flex items-center gap-2 text-sm">
      <li v-for="(item, index) in items" :key="index" class="flex items-center gap-2">
        <!-- Home Icon for first item -->
        <router-link
          v-if="index === 0"
          :to="item.to"
          class="flex items-center gap-1 text-gray-400 hover:text-yellow-400 transition-colors"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
          </svg>
          <span>{{ item.label }}</span>
        </router-link>

        <!-- Regular breadcrumb items -->
        <template v-else>
          <!-- Separator -->
          <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
          </svg>

          <!-- Link or current page -->
          <router-link
            v-if="index < items.length - 1"
            :to="item.to"
            class="text-gray-400 hover:text-yellow-400 transition-colors"
          >
            {{ item.label }}
          </router-link>
          <span
            v-else
            class="text-white font-semibold"
            aria-current="page"
          >
            {{ item.label }}
          </span>
        </template>
      </li>
    </ol>
  </nav>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    required: true,
    // Example: [{ label: 'Trang chủ', to: '/home' }, { label: 'Phim Lẻ', to: '/list/phim-le/page/1' }]
  }
});
</script>
