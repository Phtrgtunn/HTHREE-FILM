<template>
  <div class="relative overflow-hidden" :class="containerClass">
    <!-- Blur placeholder -->
    <div
      v-if="!loaded"
      class="absolute inset-0 bg-gray-800 animate-pulse"
      :style="{ backgroundImage: blurDataUrl ? `url(${blurDataUrl})` : 'none' }"
    ></div>

    <!-- Main image -->
    <img
      ref="imageRef"
      :data-src="src"
      :alt="alt"
      :class="[
        imageClass,
        'transition-opacity duration-500',
        loaded ? 'opacity-100' : 'opacity-0'
      ]"
      @load="onLoad"
      @error="onError"
    />

    <!-- Error state -->
    <div
      v-if="error"
      class="absolute inset-0 bg-gray-800 flex items-center justify-center"
    >
      <div class="text-center p-4">
        <svg class="w-12 h-12 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-gray-500 text-sm">{{ errorText }}</p>
      </div>
    </div>

    <!-- Loading spinner (optional) -->
    <div
      v-if="showSpinner && !loaded && !error"
      class="absolute inset-0 flex items-center justify-center"
    >
      <div class="w-8 h-8 border-4 border-gray-600 border-t-yellow-400 rounded-full animate-spin"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: ''
  },
  blurDataUrl: {
    type: String,
    default: null
  },
  imageClass: {
    type: String,
    default: 'w-full h-full object-cover'
  },
  containerClass: {
    type: String,
    default: ''
  },
  showSpinner: {
    type: Boolean,
    default: false
  },
  errorText: {
    type: String,
    default: 'Không tải được ảnh'
  },
  rootMargin: {
    type: String,
    default: '50px'
  }
});

const emit = defineEmits(['load', 'error']);

const imageRef = ref(null);
const loaded = ref(false);
const error = ref(false);
let observer = null;

const loadImage = () => {
  if (!imageRef.value) return;
  
  const img = imageRef.value;
  const src = img.getAttribute('data-src');
  
  if (src) {
    img.src = src;
  }
};

const onLoad = () => {
  loaded.value = true;
  emit('load');
};

const onError = () => {
  error.value = true;
  loaded.value = true;
  emit('error');
};

onMounted(() => {
  // Use Intersection Observer for lazy loading
  if ('IntersectionObserver' in window) {
    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            loadImage();
            if (observer && imageRef.value) {
              observer.unobserve(imageRef.value);
            }
          }
        });
      },
      {
        rootMargin: props.rootMargin
      }
    );

    if (imageRef.value) {
      observer.observe(imageRef.value);
    }
  } else {
    // Fallback for browsers without IntersectionObserver
    loadImage();
  }
});

onUnmounted(() => {
  if (observer && imageRef.value) {
    observer.unobserve(imageRef.value);
  }
});
</script>
