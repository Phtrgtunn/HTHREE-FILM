<template>
  <Transition name="modal">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
      @click.self="closeModal"
    >
      <div class="relative w-full max-w-md bg-gray-900 rounded-2xl shadow-2xl overflow-hidden border border-gray-700">
        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-black">Chỉnh sửa gói</h3>
            <button
              @click="closeModal"
              class="text-black/70 hover:text-black transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
          <!-- Plan Info -->
          <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
            <h4 class="text-white font-bold text-lg mb-1">{{ item?.plan_name }}</h4>
            <p class="text-gray-400 text-sm">{{ item?.quality }} • {{ item?.max_devices }} thiết bị</p>
          </div>

          <!-- Quantity -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Số lượng
            </label>
            <div class="flex items-center gap-3">
              <button
                @click="decrementQuantity"
                :disabled="localQuantity <= 1"
                class="w-10 h-10 rounded-lg bg-gray-800 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold transition-colors"
              >
                -
              </button>
              <input
                v-model.number="localQuantity"
                type="number"
                min="1"
                max="10"
                class="w-20 text-center bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-400"
              />
              <button
                @click="incrementQuantity"
                :disabled="localQuantity >= 10"
                class="w-10 h-10 rounded-lg bg-gray-800 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold transition-colors"
              >
                +
              </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Tối đa 10 gói</p>
          </div>

          <!-- Duration -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Thời hạn
            </label>
            <div class="grid grid-cols-3 gap-2">
              <button
                v-for="option in durationOptions"
                :key="option.months"
                @click="localDuration = option.months"
                :class="[
                  'px-4 py-3 rounded-lg font-medium text-sm transition-all',
                  localDuration === option.months
                    ? 'bg-yellow-500 text-black'
                    : 'bg-gray-800 text-white hover:bg-gray-700'
                ]"
              >
                <div class="font-bold">{{ option.label }}</div>
                <div v-if="option.discount" class="text-xs mt-1">
                  {{ option.discount }}
                </div>
              </button>
            </div>
          </div>

          <!-- Price Preview -->
          <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
            <div class="flex items-center justify-between mb-2">
              <span class="text-gray-400">Đơn giá:</span>
              <span class="text-white font-bold">{{ formatPrice(item?.price) }}</span>
            </div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-gray-400">Số lượng:</span>
              <span class="text-white font-bold">x{{ localQuantity }}</span>
            </div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-gray-400">Thời hạn:</span>
              <span class="text-white font-bold">{{ localDuration }} tháng</span>
            </div>
            <div class="border-t border-gray-700 pt-2 mt-2">
              <div class="flex items-center justify-between">
                <span class="text-white font-bold">Tổng cộng:</span>
                <span class="text-yellow-500 font-bold text-xl">
                  {{ formatPrice(totalPrice) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-800/50 px-6 py-4 flex gap-3">
          <button
            @click="closeModal"
            class="flex-1 px-4 py-3 rounded-lg bg-gray-700 hover:bg-gray-600 text-white font-medium transition-colors"
          >
            Hủy
          </button>
          <button
            @click="handleSave"
            class="flex-1 px-4 py-3 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-black font-bold transition-all"
          >
            Lưu thay đổi
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: Boolean,
  item: Object
});

const emit = defineEmits(['update:modelValue', 'save']);

const localQuantity = ref(1);
const localDuration = ref(1);

const durationOptions = [
  { months: 1, label: '1 tháng', discount: null },
  { months: 3, label: '3 tháng', discount: '-5%' },
  { months: 6, label: '6 tháng', discount: '-10%' }
];

const totalPrice = computed(() => {
  if (!props.item) return 0;
  return props.item.price * localQuantity.value * localDuration.value;
});

const formatPrice = (price) => {
  if (!price) return '0₫';
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price);
};

const incrementQuantity = () => {
  if (localQuantity.value < 10) {
    localQuantity.value++;
  }
};

const decrementQuantity = () => {
  if (localQuantity.value > 1) {
    localQuantity.value--;
  }
};

const closeModal = () => {
  emit('update:modelValue', false);
};

const handleSave = () => {
  emit('save', {
    quantity: localQuantity.value,
    duration: localDuration.value
  });
  closeModal();
};

// Initialize values when modal opens
watch(() => props.modelValue, (newVal) => {
  if (newVal && props.item) {
    localQuantity.value = props.item.quantity || 1;
    localDuration.value = props.item.duration_months || 1;
  }
});
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active > div,
.modal-leave-active > div {
  transition: transform 0.3s ease;
}

.modal-enter-from > div {
  transform: scale(0.9);
}

.modal-leave-to > div {
  transform: scale(0.9);
}
</style>
