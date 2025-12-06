<template>
  <div class="flex items-center justify-between p-3 bg-gray-800/50 rounded-lg hover:bg-gray-800 transition-colors">
    <span class="text-gray-300 text-sm">{{ description }}</span>
    <div class="flex items-center gap-1">
      <kbd
        v-for="(key, index) in formattedKeys"
        :key="index"
        class="px-2 py-1 bg-gray-700 text-white rounded text-xs font-mono min-w-[28px] text-center"
      >
        {{ key }}
      </kbd>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatShortcut } from '@/composables/useKeyboardShortcuts';

const props = defineProps({
  shortcut: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  }
});

const formattedKeys = computed(() => {
  return formatShortcut(props.shortcut).split(' + ');
});
</script>
