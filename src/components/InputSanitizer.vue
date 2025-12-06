<template>
  <input
    :type="type"
    :value="sanitizedValue"
    @input="handleInput"
    @blur="handleBlur"
    v-bind="$attrs"
  />
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  autoTrim: {
    type: Boolean,
    default: true
  },
  autoUppercase: {
    type: Boolean,
    default: false
  },
  preventXSS: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['update:modelValue']);

const sanitizedValue = computed(() => {
  let value = String(props.modelValue || '');
  
  if (props.preventXSS) {
    // Basic XSS prevention
    value = value
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#x27;')
      .replace(/\//g, '&#x2F;');
  }
  
  return value;
});

const handleInput = (event) => {
  let value = event.target.value;
  
  if (props.autoUppercase) {
    value = value.toUpperCase();
  }
  
  emit('update:modelValue', value);
};

const handleBlur = (event) => {
  let value = event.target.value;
  
  if (props.autoTrim) {
    value = value.trim();
  }
  
  if (props.autoUppercase) {
    value = value.toUpperCase();
  }
  
  emit('update:modelValue', value);
};
</script>
