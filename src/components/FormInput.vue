<template>
  <div class="space-y-1">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-300">
      {{ label }}
      <span v-if="required" class="text-red-400">*</span>
    </label>
    
    <div class="relative">
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        @input="handleInput"
        @blur="handleBlur"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :class="[
          'w-full px-4 py-2.5 rounded-lg bg-gray-800 text-white placeholder-gray-500 border transition-all',
          'focus:outline-none focus:ring-2',
          error ? 'border-red-500 focus:border-red-500 focus:ring-red-500/50' : 
          isValid ? 'border-green-500 focus:border-green-500 focus:ring-green-500/50' :
          'border-gray-700 focus:border-yellow-400 focus:ring-yellow-400/50',
          disabled && 'opacity-50 cursor-not-allowed'
        ]"
      />
      
      <!-- Success Icon -->
      <div v-if="isValid && !error" class="absolute right-3 top-1/2 -translate-y-1/2">
        <svg class="w-5 h-5 text-green-500 animate-scale-in" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
      </div>
      
      <!-- Error Icon -->
      <div v-if="error" class="absolute right-3 top-1/2 -translate-y-1/2">
        <svg class="w-5 h-5 text-red-500 animate-shake" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
      </div>
    </div>
    
    <!-- Error Message -->
    <Transition name="slide-down">
      <p v-if="error" class="text-sm text-red-400 flex items-center gap-1 animate-slide-in">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        {{ error }}
      </p>
    </Transition>
    
    <!-- Helper Text -->
    <p v-if="helperText && !error" class="text-sm text-gray-400">
      {{ helperText }}
    </p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  id: String,
  label: String,
  type: {
    type: String,
    default: 'text'
  },
  modelValue: [String, Number],
  placeholder: String,
  required: Boolean,
  disabled: Boolean,
  helperText: String,
  validation: Function, // Custom validation function
  validateOnInput: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'validate']);

const touched = ref(false);
const error = ref('');

const isValid = computed(() => {
  return touched.value && !error.value && props.modelValue && props.modelValue.length > 0;
});

const handleInput = (e) => {
  const value = e.target.value;
  emit('update:modelValue', value);
  
  if (props.validateOnInput && touched.value) {
    validate(value);
  }
};

const handleBlur = (e) => {
  touched.value = true;
  validate(e.target.value);
};

const validate = (value) => {
  error.value = '';
  
  if (props.required && !value) {
    error.value = 'Trường này là bắt buộc';
    emit('validate', false);
    return false;
  }
  
  if (props.validation) {
    const validationResult = props.validation(value);
    if (validationResult !== true) {
      error.value = validationResult;
      emit('validate', false);
      return false;
    }
  }
  
  // Built-in validations
  if (props.type === 'email' && value) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      error.value = 'Email không hợp lệ';
      emit('validate', false);
      return false;
    }
  }
  
  emit('validate', true);
  return true;
};

defineExpose({ validate });
</script>

<style scoped>
@keyframes scale-in {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}

@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-scale-in {
  animation: scale-in 0.3s ease-out;
}

.animate-shake {
  animation: shake 0.3s ease-in-out;
}

.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
