<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        @click.self="$emit('close')"
      >
        <div class="bg-gray-900 rounded-2xl max-w-3xl w-full max-h-[80vh] overflow-hidden border border-gray-700 shadow-2xl">
          <!-- Header -->
          <div class="bg-gradient-to-r from-yellow-500 to-red-600 p-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-white">Phím tắt</h2>
                <p class="text-white/80 text-sm">Thao tác nhanh hơn với bàn phím</p>
              </div>
            </div>
            <button
              @click="$emit('close')"
              class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors"
            >
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Content -->
          <div class="p-6 overflow-y-auto max-h-[calc(80vh-120px)]">
            <!-- Global Shortcuts -->
            <div class="mb-8">
              <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                </svg>
                Phím tắt chung
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <ShortcutItem
                  v-for="(description, shortcut) in globalShortcuts"
                  :key="shortcut"
                  :shortcut="shortcut"
                  :description="description"
                />
              </div>
            </div>

            <!-- Navigation Shortcuts -->
            <div class="mb-8">
              <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                Điều hướng
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <ShortcutItem shortcut="g+h" description="Về trang chủ" />
                <ShortcutItem shortcut="g+l" description="Thư viện của tôi" />
                <ShortcutItem shortcut="g+p" description="Gói dịch vụ" />
                <ShortcutItem shortcut="g+c" description="Giỏ hàng" />
              </div>
            </div>

            <!-- Library Shortcuts -->
            <div class="mb-8">
              <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
                Thư viện
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <ShortcutItem shortcut="f" description="Lọc yêu thích" />
                <ShortcutItem shortcut="w" description="Lọc danh sách xem" />
                <ShortcutItem shortcut="c" description="Lọc đang xem" />
                <ShortcutItem shortcut="delete" description="Xóa mục đã chọn" />
              </div>
            </div>

            <!-- Video Player Shortcuts -->
            <div>
              <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                </svg>
                Trình phát video
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <ShortcutItem shortcut="space" description="Phát/Tạm dừng" />
                <ShortcutItem shortcut="f" description="Toàn màn hình" />
                <ShortcutItem shortcut="m" description="Tắt/Bật tiếng" />
                <ShortcutItem shortcut="←" description="Tua lùi 10s" />
                <ShortcutItem shortcut="→" description="Tua tới 10s" />
                <ShortcutItem shortcut="↑" description="Tăng âm lượng" />
                <ShortcutItem shortcut="↓" description="Giảm âm lượng" />
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="bg-gray-800/50 px-6 py-4 border-t border-gray-700">
            <p class="text-gray-400 text-sm text-center">
              Nhấn <kbd class="px-2 py-1 bg-gray-700 rounded text-xs">?</kbd> bất cứ lúc nào để xem phím tắt
            </p>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { GLOBAL_SHORTCUTS } from '@/composables/useKeyboardShortcuts';
import ShortcutItem from './ShortcutItem.vue';

defineProps({
  show: {
    type: Boolean,
    default: false
  }
});

defineEmits(['close']);

const globalShortcuts = GLOBAL_SHORTCUTS;
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

kbd {
  font-family: 'Courier New', monospace;
  font-weight: 600;
}
</style>
