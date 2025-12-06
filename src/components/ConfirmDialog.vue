<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        @click.self="handleCancel"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

        <!-- Dialog -->
        <Transition name="scale">
          <div
            v-if="isOpen"
            class="relative bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-700 overflow-hidden"
            role="dialog"
            aria-modal="true"
            :aria-labelledby="'dialog-title-' + Date.now()"
          >
            <!-- Icon -->
            <div class="flex justify-center pt-6 pb-4">
              <div
                class="w-16 h-16 rounded-full flex items-center justify-center"
                :class="{
                  'bg-yellow-500/20': confirmData.type === 'warning',
                  'bg-red-500/20': confirmData.type === 'danger',
                  'bg-blue-500/20': confirmData.type === 'info'
                }"
              >
                <!-- Warning Icon -->
                <svg
                  v-if="confirmData.type === 'warning'"
                  class="w-8 h-8 text-yellow-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                  />
                </svg>
                <!-- Danger Icon -->
                <svg
                  v-else-if="confirmData.type === 'danger'"
                  class="w-8 h-8 text-red-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                  />
                </svg>
                <!-- Info Icon -->
                <svg
                  v-else
                  class="w-8 h-8 text-blue-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
            </div>

            <!-- Content -->
            <div class="px-6 pb-6 text-center">
              <h3
                :id="'dialog-title-' + Date.now()"
                class="text-xl font-bold text-white mb-2"
              >
                {{ confirmData.title }}
              </h3>
              <p class="text-gray-400 text-sm leading-relaxed">
                {{ confirmData.message }}
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-center gap-3 px-6 pb-6">
              <button
                @click="handleCancel"
                class="flex-1 px-4 py-3 bg-gray-800 text-white rounded-lg font-medium hover:bg-gray-700 transition-all focus:outline-none focus:ring-2 focus:ring-gray-600 flex items-center justify-center"
              >
                {{ confirmData.cancelText }}
              </button>
              <button
                @click="handleConfirm"
                class="flex-1 px-4 py-3 rounded-lg font-medium transition-all focus:outline-none focus:ring-2 flex items-center justify-center"
                :class="{
                  'bg-yellow-500 text-black hover:bg-yellow-600 focus:ring-yellow-400':
                    confirmData.type === 'warning',
                  'bg-red-500 text-white hover:bg-red-600 focus:ring-red-400':
                    confirmData.type === 'danger',
                  'bg-blue-500 text-white hover:bg-blue-600 focus:ring-blue-400':
                    confirmData.type === 'info'
                }"
              >
                {{ confirmData.confirmText }}
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { useConfirm } from '@/composables/useConfirm';

const { isOpen, confirmData } = useConfirm();

const handleConfirm = () => {
  if (confirmData.value.onConfirm) {
    confirmData.value.onConfirm();
  }
};

const handleCancel = () => {
  if (confirmData.value.onCancel) {
    confirmData.value.onCancel();
  }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.scale-enter-active {
  animation: scaleIn 0.3s ease-out;
}

.scale-leave-active {
  animation: scaleIn 0.2s ease-in reverse;
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
