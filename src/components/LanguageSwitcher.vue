<template>
  <div class="relative" ref="dropdownRef">
    <button
      @click="toggleDropdown"
      class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-800/50 hover:bg-gray-700/50 transition-colors border border-gray-700 hover:border-gray-600"
      :aria-label="$t('common.language')"
    >
      <span class="text-xl">{{ currentLocale.flag }}</span>
      <span class="text-sm font-medium text-white hidden sm:inline">{{ currentLocale.name }}</span>
      <svg 
        class="w-4 h-4 text-gray-400 transition-transform"
        :class="{ 'rotate-180': isOpen }"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-48 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50 overflow-hidden"
      >
        <button
          v-for="locale in availableLocales"
          :key="locale.code"
          @click="changeLanguage(locale.code)"
          class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-700 transition-colors text-left"
          :class="{ 'bg-gray-700/50': currentLocale.code === locale.code }"
        >
          <span class="text-xl">{{ locale.flag }}</span>
          <span class="text-sm font-medium text-white">{{ locale.name }}</span>
          <svg
            v-if="currentLocale.code === locale.code"
            class="w-5 h-5 text-yellow-400 ml-auto"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { getAvailableLocales, setLocale } from '@/i18n';

const { locale } = useI18n();
const isOpen = ref(false);
const dropdownRef = ref(null);

const availableLocales = getAvailableLocales();

const currentLocale = computed(() => {
  return availableLocales.find(l => l.code === locale.value) || availableLocales[0];
});

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const changeLanguage = (code) => {
  setLocale(code);
  isOpen.value = false;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
