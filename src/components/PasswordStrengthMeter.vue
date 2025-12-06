<template>
  <div v-if="password" class="mt-2">
    <!-- Strength Bar -->
    <div class="flex gap-1 mb-2">
      <div
        v-for="i in 4"
        :key="i"
        class="h-1 flex-1 rounded-full transition-all duration-300"
        :class="i <= strengthBars ? getBarColor() : 'bg-gray-700'"
      ></div>
    </div>

    <!-- Strength Label -->
    <div class="flex items-center justify-between text-xs">
      <span :class="getLabelColor()">
        {{ strengthData.label }}
      </span>
      <span class="text-gray-400">
        {{ password.length }}/12+ ký tự
      </span>
    </div>

    <!-- Requirements Checklist -->
    <div class="mt-3 space-y-1.5">
      <div class="flex items-center gap-2 text-xs" :class="hasMinLength ? 'text-green-400' : 'text-gray-500'">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path v-if="hasMinLength" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span>Ít nhất 8 ký tự</span>
      </div>

      <div class="flex items-center gap-2 text-xs" :class="hasLetter ? 'text-green-400' : 'text-gray-500'">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path v-if="hasLetter" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span>Có chữ cái</span>
      </div>

      <div class="flex items-center gap-2 text-xs" :class="hasNumber ? 'text-green-400' : 'text-gray-500'">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path v-if="hasNumber" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span>Có số</span>
      </div>

      <div v-if="showAdvanced" class="flex items-center gap-2 text-xs" :class="hasSpecial ? 'text-green-400' : 'text-gray-500'">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path v-if="hasSpecial" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span>Có ký tự đặc biệt (!@#$%...)</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useFormValidation } from '@/composables/useFormValidation';

const props = defineProps({
  password: {
    type: String,
    default: ''
  },
  showAdvanced: {
    type: Boolean,
    default: false
  }
});

const { rules } = useFormValidation();

const strengthData = computed(() => {
  return rules.passwordStrength(props.password);
});

const strengthBars = computed(() => {
  const strength = strengthData.value.strength;
  if (strength <= 1) return 1;
  if (strength <= 2) return 2;
  if (strength <= 4) return 3;
  return 4;
});

const hasMinLength = computed(() => props.password.length >= 8);
const hasLetter = computed(() => /[a-zA-Z]/.test(props.password));
const hasNumber = computed(() => /[0-9]/.test(props.password));
const hasSpecial = computed(() => /[^a-zA-Z0-9]/.test(props.password));

const getBarColor = () => {
  const colors = {
    red: 'bg-red-500',
    orange: 'bg-orange-500',
    yellow: 'bg-yellow-500',
    lime: 'bg-lime-500',
    green: 'bg-green-500'
  };
  return colors[strengthData.value.color] || 'bg-gray-700';
};

const getLabelColor = () => {
  const colors = {
    red: 'text-red-400',
    orange: 'text-orange-400',
    yellow: 'text-yellow-400',
    lime: 'text-lime-400',
    green: 'text-green-400'
  };
  return colors[strengthData.value.color] || 'text-gray-400';
};
</script>
