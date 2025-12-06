<template>
  <Transition name="success-fade">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none"
    >
      <!-- Success Checkmark -->
      <div class="relative">
        <!-- Ripple Effect -->
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="w-32 h-32 rounded-full bg-green-500/20 animate-ping"></div>
        </div>
        
        <!-- Circle Background -->
        <div class="relative w-24 h-24 rounded-full bg-green-500 flex items-center justify-center shadow-2xl animate-scale-bounce">
          <!-- Checkmark -->
          <svg class="w-12 h-12 text-white animate-draw-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="3" 
              d="M5 13l4 4L19 7"
              class="checkmark-path"
            />
          </svg>
        </div>
        
        <!-- Success Text -->
        <div v-if="message" class="absolute top-full mt-4 left-1/2 -translate-x-1/2 whitespace-nowrap">
          <p class="text-white font-bold text-lg bg-gray-900/90 px-6 py-2 rounded-full shadow-xl animate-slide-up">
            {{ message }}
          </p>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: ''
  },
  duration: {
    type: Number,
    default: 2000
  }
});

const emit = defineEmits(['close']);

watch(() => props.show, (newVal) => {
  if (newVal) {
    setTimeout(() => {
      emit('close');
    }, props.duration);
  }
});
</script>

<style scoped>
@keyframes scale-bounce {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes draw-check {
  0% {
    stroke-dasharray: 0, 100;
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    stroke-dasharray: 100, 0;
    opacity: 1;
  }
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-scale-bounce {
  animation: scale-bounce 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.animate-draw-check {
  animation: draw-check 0.6s ease-out 0.2s forwards;
  stroke-dasharray: 0, 100;
}

.animate-slide-up {
  animation: slide-up 0.4s ease-out 0.5s forwards;
  opacity: 0;
}

.success-fade-enter-active,
.success-fade-leave-active {
  transition: opacity 0.3s ease;
}

.success-fade-enter-from,
.success-fade-leave-to {
  opacity: 0;
}
</style>
