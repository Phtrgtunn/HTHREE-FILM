<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
        @click.self="cancel"
      >
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl max-w-md w-full border border-gray-700 shadow-2xl transform transition-all">
          <!-- Icon -->
          <div class="p-6 text-center">
            <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" :class="iconBgClass">
              <component :is="iconComponent" class="w-8 h-8" :class="iconColorClass" />
            </div>
            
            <!-- Title -->
            <h3 class="text-2xl font-black text-white mb-2">{{ title }}</h3>
            
            <!-- Message -->
            <p class="text-gray-300 text-base">{{ message }}</p>
          </div>

          <!-- Actions -->
          <div class="p-6 pt-0 flex gap-3">
            <button
              @click="cancel"
              class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-xl transition-all"
            >
              {{ cancelText }}
            </button>
            <button
              @click="confirm"
              class="flex-1 font-bold py-3 px-6 rounded-xl transition-all"
              :class="confirmButtonClass"
            >
              {{ confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Xác nhận'
  },
  message: {
    type: String,
    default: 'Bạn có chắc chắn muốn thực hiện hành động này?'
  },
  confirmText: {
    type: String,
    default: 'Xác nhận'
  },
  cancelText: {
    type: String,
    default: 'Hủy'
  },
  type: {
    type: String,
    default: 'warning', // warning, danger, success, info
    validator: (value) => ['warning', 'danger', 'success', 'info'].includes(value)
  }
});

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel']);

const iconComponent = computed(() => {
  const icons = {
    warning: 'WarningIcon',
    danger: 'DangerIcon',
    success: 'SuccessIcon',
    info: 'InfoIcon'
  };
  return icons[props.type];
});

const iconBgClass = computed(() => {
  const classes = {
    warning: 'bg-yellow-500/20',
    danger: 'bg-red-500/20',
    success: 'bg-green-500/20',
    info: 'bg-blue-500/20'
  };
  return classes[props.type];
});

const iconColorClass = computed(() => {
  const classes = {
    warning: 'text-yellow-400',
    danger: 'text-red-400',
    success: 'text-green-400',
    info: 'text-blue-400'
  };
  return classes[props.type];
});

const confirmButtonClass = computed(() => {
  const classes = {
    warning: 'bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white',
    danger: 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white',
    success: 'bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white',
    info: 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white'
  };
  return classes[props.type];
});

const confirm = () => {
  emit('confirm');
  emit('update:modelValue', false);
};

const cancel = () => {
  emit('cancel');
  emit('update:modelValue', false);
};
</script>

<script>
// Icon components
const WarningIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
    </svg>
  `
};

const DangerIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
    </svg>
  `
};

const SuccessIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
  `
};

const InfoIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
    </svg>
  `
};

export default {
  components: {
    WarningIcon,
    DangerIcon,
    SuccessIcon,
    InfoIcon
  }
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-active > div,
.modal-leave-active > div {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.9);
  opacity: 0;
}
</style>
