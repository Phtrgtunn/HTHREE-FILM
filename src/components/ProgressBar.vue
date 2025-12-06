<template>
  <div class="w-full">
    <!-- Steps -->
    <div v-if="steps.length > 0" class="flex items-center justify-between mb-4">
      <div
        v-for="(step, index) in steps"
        :key="index"
        class="flex items-center"
        :class="{ 'flex-1': index < steps.length - 1 }"
      >
        <!-- Step Circle -->
        <div class="relative flex flex-col items-center">
          <div
            :class="[
              'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300',
              index < currentStep ? 'bg-green-500 text-white' :
              index === currentStep ? 'bg-yellow-500 text-black ring-4 ring-yellow-500/30 animate-pulse' :
              'bg-gray-700 text-gray-400'
            ]"
          >
            <svg v-if="index < currentStep" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            <span v-else>{{ index + 1 }}</span>
          </div>
          
          <!-- Step Label -->
          <span
            :class="[
              'absolute top-12 text-xs font-medium whitespace-nowrap transition-colors',
              index <= currentStep ? 'text-white' : 'text-gray-500'
            ]"
          >
            {{ step }}
          </span>
        </div>
        
        <!-- Connector Line -->
        <div
          v-if="index < steps.length - 1"
          class="flex-1 h-1 mx-2 rounded-full transition-all duration-500"
          :class="index < currentStep ? 'bg-green-500' : 'bg-gray-700'"
        ></div>
      </div>
    </div>
    
    <!-- Progress Bar (percentage) -->
    <div v-else class="space-y-2">
      <div class="flex items-center justify-between text-sm">
        <span class="text-gray-400">{{ label }}</span>
        <span class="text-white font-bold">{{ percentage }}%</span>
      </div>
      
      <div class="w-full h-2 bg-gray-700 rounded-full overflow-hidden">
        <div
          class="h-full bg-gradient-to-r from-yellow-500 to-green-500 rounded-full transition-all duration-500 ease-out relative"
          :style="{ width: `${percentage}%` }"
        >
          <!-- Shimmer Effect -->
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  steps: {
    type: Array,
    default: () => []
  },
  currentStep: {
    type: Number,
    default: 0
  },
  percentage: {
    type: Number,
    default: 0,
    validator: (value) => value >= 0 && value <= 100
  },
  label: {
    type: String,
    default: 'Đang xử lý'
  }
});
</script>

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.animate-shimmer {
  animation: shimmer 2s infinite;
}
</style>
