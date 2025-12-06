<template>
  <Transition name="slide-up">
    <button
      v-if="isVisible"
      @click="scrollToTop"
      class="fixed bottom-6 right-6 z-50 w-12 h-12 bg-yellow-500 hover:bg-yellow-600 text-black rounded-full shadow-2xl hover:shadow-yellow-500/50 transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-yellow-400/50 group"
      aria-label="Quay lại đầu trang"
      title="Quay lại đầu trang"
    >
      <svg
        class="w-6 h-6 mx-auto group-hover:-translate-y-1 transition-transform duration-300"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2.5"
          d="M5 10l7-7m0 0l7 7m-7-7v18"
        />
      </svg>
    </button>
  </Transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const isVisible = ref(false);
const scrollProgress = ref(0);

// Simple version - no progress ring needed

const handleScroll = () => {
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
  
  // Show button after scrolling 300px
  isVisible.value = scrollTop > 300;
};

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  handleScroll(); // Initial check
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.slide-up-enter-active {
  animation: slideUp 0.3s ease-out;
}

.slide-up-leave-active {
  animation: slideUp 0.3s ease-in reverse;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.8);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
</style>
