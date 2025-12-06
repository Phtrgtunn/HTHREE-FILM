<template>
  <div class="text-center py-20">
    <!-- Icon -->
    <div class="flex justify-center mb-6">
      <div
        class="w-24 h-24 rounded-full flex items-center justify-center"
        :class="iconBgClass"
      >
        <component :is="iconComponent" class="w-12 h-12" :class="iconColorClass" />
      </div>
    </div>

    <!-- Title -->
    <h3 class="text-2xl font-bold text-white mb-3">
      {{ title }}
    </h3>

    <!-- Description -->
    <p class="text-gray-400 text-base mb-8 max-w-md mx-auto">
      {{ description }}
    </p>

    <!-- Action Button -->
    <router-link
      v-if="actionLink"
      :to="actionLink"
      class="inline-flex items-center gap-2 bg-yellow-500 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition-all hover:scale-105 shadow-lg"
    >
      {{ actionText }}
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </router-link>

    <!-- Custom Action Slot -->
    <slot name="action"></slot>
  </div>
</template>

<script setup>
import { computed, h } from 'vue';

const props = defineProps({
  icon: {
    type: String,
    default: 'box', // heart, list, clock, box, search, film, user, cart
    validator: (value) => ['heart', 'list', 'clock', 'box', 'search', 'film', 'user', 'cart'].includes(value)
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  actionText: {
    type: String,
    default: 'Khám phá ngay'
  },
  actionLink: {
    type: String,
    default: null
  }
});

// Icon components
const icons = {
  heart: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' })
  ]),
  list: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' })
  ]),
  clock: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })
  ]),
  box: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' })
  ]),
  search: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' })
  ]),
  film: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z' })
  ]),
  user: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })
  ]),
  cart: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z' })
  ])
};

const iconComponent = computed(() => icons[props.icon]);

const iconBgClass = computed(() => {
  const bgClasses = {
    heart: 'bg-red-500/20',
    list: 'bg-blue-500/20',
    clock: 'bg-yellow-500/20',
    box: 'bg-gray-500/20',
    search: 'bg-purple-500/20',
    film: 'bg-green-500/20',
    user: 'bg-indigo-500/20',
    cart: 'bg-orange-500/20'
  };
  return bgClasses[props.icon];
});

const iconColorClass = computed(() => {
  const colorClasses = {
    heart: 'text-red-400',
    list: 'text-blue-400',
    clock: 'text-yellow-400',
    box: 'text-gray-400',
    search: 'text-purple-400',
    film: 'text-green-400',
    user: 'text-indigo-400',
    cart: 'text-orange-400'
  };
  return colorClasses[props.icon];
});
</script>
