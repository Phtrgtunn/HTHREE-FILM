<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] flex items-start justify-center pt-[15vh] p-4 bg-black/80 backdrop-blur-sm"
        @click.self="close"
      >
        <div class="bg-gray-900 rounded-2xl max-w-2xl w-full border border-gray-700 shadow-2xl overflow-hidden">
          <!-- Search Input -->
          <div class="p-4 border-b border-gray-700">
            <div class="relative">
              <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input
                ref="searchInput"
                v-model="query"
                type="text"
                placeholder="Tìm kiếm hành động, trang, phim..."
                class="w-full pl-12 pr-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-400/20 transition-all"
                @keydown.down.prevent="selectNext"
                @keydown.up.prevent="selectPrevious"
                @keydown.enter.prevent="executeSelected"
                @keydown.esc="close"
              />
            </div>
          </div>

          <!-- Results -->
          <div class="max-h-[400px] overflow-y-auto">
            <!-- No results -->
            <div v-if="filteredCommands.length === 0" class="p-8 text-center">
              <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="text-gray-400">Không tìm thấy kết quả</p>
            </div>

            <!-- Command Groups -->
            <div v-else>
              <div
                v-for="(group, groupName) in groupedCommands"
                :key="groupName"
                class="py-2"
              >
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                  {{ groupName }}
                </div>
                <button
                  v-for="(command, index) in group"
                  :key="command.id"
                  @click="execute(command)"
                  @mouseenter="selectedIndex = getGlobalIndex(groupName, index)"
                  class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-800 transition-colors text-left"
                  :class="{ 'bg-gray-800': selectedIndex === getGlobalIndex(groupName, index) }"
                >
                  <div class="flex items-center gap-3">
                    <div
                      class="w-8 h-8 rounded-lg flex items-center justify-center"
                      :class="command.iconBg || 'bg-gray-700'"
                    >
                      <component :is="command.icon" v-if="command.icon" class="w-4 h-4" :class="command.iconColor || 'text-white'" />
                      <span v-else class="text-lg">{{ command.emoji }}</span>
                    </div>
                    <div>
                      <p class="text-white font-medium text-sm">{{ command.label }}</p>
                      <p v-if="command.description" class="text-gray-400 text-xs">{{ command.description }}</p>
                    </div>
                  </div>
                  <kbd
                    v-if="command.shortcut"
                    class="px-2 py-1 bg-gray-700 text-gray-300 rounded text-xs font-mono"
                  >
                    {{ command.shortcut }}
                  </kbd>
                </button>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-4 py-3 bg-gray-800/50 border-t border-gray-700 flex items-center justify-between text-xs text-gray-400">
            <div class="flex items-center gap-4">
              <span class="flex items-center gap-1">
                <kbd class="px-1.5 py-0.5 bg-gray-700 rounded">↑↓</kbd> Điều hướng
              </span>
              <span class="flex items-center gap-1">
                <kbd class="px-1.5 py-0.5 bg-gray-700 rounded">Enter</kbd> Chọn
              </span>
              <span class="flex items-center gap-1">
                <kbd class="px-1.5 py-0.5 bg-gray-700 rounded">Esc</kbd> Đóng
              </span>
            </div>
            <span>{{ filteredCommands.length }} kết quả</span>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from '@/composables/useToast';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close']);

const router = useRouter();
const toast = useToast();

const searchInput = ref(null);
const query = ref('');
const selectedIndex = ref(0);

// All available commands
const commands = ref([
  // Navigation
  { id: 'nav-home', label: 'Trang chủ', description: 'Về trang chủ', emoji: '🏠', group: 'Điều hướng', action: () => router.push('/home'), shortcut: 'G+H' },
  { id: 'nav-library', label: 'Thư viện', description: 'Thư viện của tôi', emoji: '📚', group: 'Điều hướng', action: () => router.push('/library'), shortcut: 'G+L' },
  { id: 'nav-pricing', label: 'Gói dịch vụ', description: 'Xem các gói', emoji: '💎', group: 'Điều hướng', action: () => router.push('/pricing'), shortcut: 'G+P' },
  { id: 'nav-cart', label: 'Giỏ hàng', description: 'Xem giỏ hàng', emoji: '🛒', group: 'Điều hướng', action: () => router.push('/cart'), shortcut: 'G+C' },
  { id: 'nav-account', label: 'Tài khoản', description: 'Quản lý tài khoản', emoji: '👤', group: 'Điều hướng', action: () => router.push('/account') },
  
  // Actions
  { id: 'action-search', label: 'Tìm kiếm phim', description: 'Mở thanh tìm kiếm', emoji: '🔍', group: 'Hành động', action: () => focusSearch(), shortcut: '/' },
  { id: 'action-shortcuts', label: 'Phím tắt', description: 'Xem danh sách phím tắt', emoji: '⌨️', group: 'Hành động', action: () => showShortcuts(), shortcut: '?' },
  { id: 'action-theme', label: 'Chuyển theme', description: 'Sáng/Tối', emoji: '🌓', group: 'Hành động', action: () => toggleTheme() },
  
  // Quick Actions
  { id: 'quick-buy-basic', label: 'Mua gói Basic', description: 'Thêm gói Basic vào giỏ', emoji: '⭐', group: 'Mua nhanh', action: () => quickBuy('basic') },
  { id: 'quick-buy-premium', label: 'Mua gói Premium', description: 'Thêm gói Premium vào giỏ', emoji: '🔥', group: 'Mua nhanh', action: () => quickBuy('premium') },
  { id: 'quick-buy-vip', label: 'Mua gói VIP', description: 'Thêm gói VIP vào giỏ', emoji: '👑', group: 'Mua nhanh', action: () => quickBuy('vip') },
]);

// Filter commands based on query
const filteredCommands = computed(() => {
  if (!query.value) return commands.value;
  
  const q = query.value.toLowerCase();
  return commands.value.filter(cmd => 
    cmd.label.toLowerCase().includes(q) ||
    cmd.description?.toLowerCase().includes(q) ||
    cmd.group.toLowerCase().includes(q)
  );
});

// Group commands
const groupedCommands = computed(() => {
  const groups = {};
  filteredCommands.value.forEach(cmd => {
    if (!groups[cmd.group]) {
      groups[cmd.group] = [];
    }
    groups[cmd.group].push(cmd);
  });
  return groups;
});

// Get global index for keyboard navigation
const getGlobalIndex = (groupName, localIndex) => {
  let globalIndex = 0;
  for (const [gName, group] of Object.entries(groupedCommands.value)) {
    if (gName === groupName) {
      return globalIndex + localIndex;
    }
    globalIndex += group.length;
  }
  return 0;
};

// Keyboard navigation
const selectNext = () => {
  selectedIndex.value = Math.min(selectedIndex.value + 1, filteredCommands.value.length - 1);
};

const selectPrevious = () => {
  selectedIndex.value = Math.max(selectedIndex.value - 1, 0);
};

const executeSelected = () => {
  const command = filteredCommands.value[selectedIndex.value];
  if (command) {
    execute(command);
  }
};

// Execute command
const execute = (command) => {
  command.action();
  close();
  toast.success(`✓ ${command.label}`);
};

// Helper actions
const focusSearch = () => {
  // Focus search input in navbar
  const searchInput = document.querySelector('input[placeholder*="Tìm kiếm"]');
  if (searchInput) {
    searchInput.focus();
  }
};

const showShortcuts = () => {
  // Emit event to show shortcuts modal
  window.dispatchEvent(new CustomEvent('show-shortcuts'));
};

const toggleTheme = () => {
  document.documentElement.classList.toggle('dark');
  toast.info('Theme đã được thay đổi');
};

const quickBuy = (planSlug) => {
  router.push(`/pricing?plan=${planSlug}`);
};

const close = () => {
  query.value = '';
  selectedIndex.value = 0;
  emit('close');
};

// Focus input when opened
watch(() => props.show, (newVal) => {
  if (newVal) {
    nextTick(() => {
      searchInput.value?.focus();
    });
  }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

kbd {
  font-family: 'Courier New', monospace;
}
</style>
