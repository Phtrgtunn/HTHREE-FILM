<template>
  <div class="absolute bottom-20 right-8 z-20 flex items-center gap-3">
    <!-- Pause/Play Trailer Button -->
    <button
      v-if="hasTrailer"
      @click="$emit('toggle-trailer')"
      class="group bg-black/50 backdrop-blur-sm hover:bg-black/70 text-white p-3 rounded-full transition-all duration-300 hover:scale-110 border border-white/20"
      :aria-label="isPlaying ? 'Tạm dừng trailer' : 'Phát trailer'"
      title="isPlaying ? 'Tạm dừng trailer' : 'Phát trailer'"
    >
      <!-- Play Icon -->
      <svg 
        v-if="!isPlaying"
        class="w-5 h-5 transition-transform group-hover:scale-110" 
        fill="currentColor" 
        viewBox="0 0 20 20"
      >
        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
      </svg>
      
      <!-- Pause Icon -->
      <svg 
        v-else
        class="w-5 h-5 transition-transform group-hover:scale-110" 
        fill="currentColor" 
        viewBox="0 0 20 20"
      >
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
      </svg>
    </button>

    <!-- Sound Toggle Button -->
    <button
      v-if="hasTrailer && isPlaying"
      @click="$emit('toggle-sound')"
      class="group bg-black/50 backdrop-blur-sm hover:bg-black/70 text-white p-3 rounded-full transition-all duration-300 hover:scale-110 border border-white/20"
      :aria-label="isMuted ? 'Bật âm thanh' : 'Tắt âm thanh'"
      :title="isMuted ? 'Bật âm thanh' : 'Tắt âm thanh'"
    >
      <!-- Muted Icon -->
      <svg 
        v-if="isMuted"
        class="w-5 h-5 transition-transform group-hover:scale-110" 
        fill="currentColor" 
        viewBox="0 0 20 20"
      >
        <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
      </svg>
      
      <!-- Unmuted Icon -->
      <svg 
        v-else
        class="w-5 h-5 transition-transform group-hover:scale-110" 
        fill="currentColor" 
        viewBox="0 0 20 20"
      >
        <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"/>
      </svg>
    </button>

    <!-- Info Badge -->
    <div 
      v-if="isPlaying"
      class="bg-black/50 backdrop-blur-sm text-white px-3 py-2 rounded-full text-xs font-medium border border-white/20"
    >
      Đang phát trailer
    </div>
  </div>
</template>

<script setup>
defineProps({
  hasTrailer: {
    type: Boolean,
    default: false
  },
  isPlaying: {
    type: Boolean,
    default: false
  },
  isMuted: {
    type: Boolean,
    default: true
  }
});

defineEmits(['toggle-trailer', 'toggle-sound']);
</script>

<style scoped>
/* Smooth animations */
button {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

button:active {
  transform: scale(0.95);
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
  button {
    transition: none;
  }
}
</style>
