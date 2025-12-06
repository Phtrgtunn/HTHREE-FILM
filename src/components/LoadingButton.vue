<template>
  <button
    :type="type"
    :disabled="loading || disabled"
    :class="[
      'relative inline-flex items-center justify-center gap-2 font-semibold transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2',
      sizeClasses,
      variantClasses,
      loading || disabled ? 'cursor-not-allowed opacity-70' : 'hover:scale-105'
    ]"
    v-bind="$attrs"
  >
    <!-- Loading Spinner -->
    <div
      v-if="loading"
      class="absolute inset-0 flex items-center justify-center"
    >
      <svg
        class="animate-spin"
        :class="spinnerSizeClasses"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>
    </div>

    <!-- Content (hidden when loading) -->
    <span :class="{ 'invisible': loading }">
      <slot></slot>
    </span>
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  variant: {
    type: String,
    default: 'primary', // primary, secondary, danger, success, ghost
    validator: (value) => ['primary', 'secondary', 'danger', 'success', 'ghost'].includes(value)
  },
  size: {
    type: String,
    default: 'md', // xs, sm, md, lg, xl
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  type: {
    type: String,
    default: 'button'
  }
});

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'px-2 py-1 text-xs rounded',
    sm: 'px-3 py-1.5 text-sm rounded-lg',
    md: 'px-4 py-2 text-base rounded-lg',
    lg: 'px-6 py-3 text-lg rounded-lg',
    xl: 'px-8 py-4 text-xl rounded-xl'
  };
  return sizes[props.size];
});

const spinnerSizeClasses = computed(() => {
  const sizes = {
    xs: 'w-3 h-3',
    sm: 'w-4 h-4',
    md: 'w-5 h-5',
    lg: 'w-6 h-6',
    xl: 'w-8 h-8'
  };
  return sizes[props.size];
});

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-yellow-500 text-black hover:bg-yellow-600 focus:ring-yellow-400 shadow-lg',
    secondary: 'bg-gray-700 text-white hover:bg-gray-600 focus:ring-gray-500',
    danger: 'bg-red-500 text-white hover:bg-red-600 focus:ring-red-400',
    success: 'bg-green-500 text-white hover:bg-green-600 focus:ring-green-400',
    ghost: 'bg-transparent text-white border border-gray-600 hover:bg-gray-800 focus:ring-gray-500'
  };
  return variants[props.variant];
});
</script>
