<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-900 via-black to-gray-900 pt-24 pb-16">
    <!-- Undo Snackbar -->
    <UndoSnackbar
      :show="showUndo"
      :message="undoMessage"
      @undo="handleUndo"
      @close="closeUndo"
    />
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-2">
          {{ $t('library.title') }}
        </h1>
        <p class="text-gray-400 text-lg">
          {{ $t('library.subtitle') }}
        </p>
      </div>

      <!-- Tabs -->
      <div class="flex gap-2 mb-8 border-b border-gray-800 overflow-x-auto">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-300 relative"
          :class="activeTab === tab.id 
            ? 'text-yellow-400' 
            : 'text-gray-400 hover:text-white'"
        >
          <div class="flex items-center gap-2">
            <component :is="tab.icon" class="w-5 h-5" />
            <span>{{ tab.label }}</span>
            <span 
              v-if="tab.count > 0"
              class="px-2 py-0.5 bg-gray-800 rounded-full text-xs"
              :class="activeTab === tab.id ? 'bg-yellow-500 text-black' : 'bg-gray-800 text-gray-400'"
            >
              {{ tab.count }}
            </span>
          </div>
          <!-- Active indicator -->
          <div
            v-if="activeTab === tab.id"
            class="absolute bottom-0 left-0 right-0 h-0.5 bg-yellow-400"
          ></div>
        </button>
      </div>

      <!-- Tab Content -->
      <div class="mt-8">
        <!-- Yêu thích Tab -->
        <div v-if="activeTab === 'favorites'" class="space-y-6">
          <!-- Bulk Actions Toolbar -->
          <div v-if="favorites.length > 0" class="flex items-center justify-between bg-gray-800 rounded-lg px-4 py-3">
            <div class="flex items-center gap-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input 
                  type="checkbox" 
                  :checked="favorites.length > 0 && favorites.every(m => isSelected(m.id))"
                  @change="favorites.every(m => isSelected(m.id)) ? deselectAll() : selectAll(favorites.map(m => m.id))"
                  class="w-4 h-4 rounded border-gray-600 text-yellow-400 focus:ring-yellow-400"
                />
                <span class="text-white text-sm font-medium">
                  {{ hasSelection ? t('library.selected', { count: selectedItems.length }) : t('library.selectAll') }}
                </span>
              </label>
            </div>
            
            <div v-if="hasSelection" class="flex items-center gap-2">
              <button 
                @click="bulkRemoveFromFavorites"
                class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $t('library.deleteSelected') }}
              </button>
            </div>
          </div>
          
          <EmptyState
            v-if="favorites.length === 0"
            icon="heart"
            :title="$t('library.emptyFavorites')"
            :description="$t('library.emptyFavoritesDesc')"
            :action-text="$t('library.discoverMovies')"
            action-link="/home"
          />
          
          <!-- Movie Grid -->
          <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            <div
              v-for="movie in favorites"
              :key="movie.id"
              class="group relative bg-gray-800 rounded-lg overflow-hidden hover:scale-105 transition-transform duration-300 cursor-pointer"
            >
              <!-- Selection Checkbox -->
              <div class="absolute top-2 left-2 z-10">
                <input 
                  type="checkbox" 
                  :checked="isSelected(movie.id)"
                  @change="toggleSelection(movie.id)"
                  @click.stop
                  class="w-5 h-5 rounded border-gray-600 text-yellow-400 focus:ring-yellow-400 cursor-pointer"
                />
              </div>
              
              <img :src="getImageUrl(movie.poster)" :alt="movie.title" class="w-full aspect-[2/3] object-cover" @error="(e) => e.target.src = 'https://placehold.co/400x600/1a1a1a/fff?text=Error'" />
              <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div class="absolute bottom-0 left-0 right-0 p-4">
                  <h4 class="text-white font-semibold text-sm mb-2">{{ movie.title }}</h4>
                  <div class="flex gap-2">
                    <button @click="watchMovie(movie)" class="flex-1 bg-yellow-500 text-black px-3 py-1.5 rounded text-xs font-semibold hover:bg-yellow-600 transition-colors">
                      {{ $t('library.watchNow') }}
                    </button>
                    <button @click="removeFromFavorites(movie)" class="p-1.5 bg-red-500 text-white rounded hover:bg-red-600 transition-colors" :title="$t('library.removeFromFavorites')">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Danh sách Tab -->
        <div v-if="activeTab === 'watchlist'" class="space-y-6">
          <!-- Bulk Actions Toolbar -->
          <div v-if="watchlist.length > 0" class="flex items-center justify-between bg-gray-800 rounded-lg px-4 py-3">
            <div class="flex items-center gap-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input 
                  type="checkbox" 
                  :checked="watchlist.length > 0 && watchlist.every(m => isSelected(m.id))"
                  @change="watchlist.every(m => isSelected(m.id)) ? deselectAll() : selectAll(watchlist.map(m => m.id))"
                  class="w-4 h-4 rounded border-gray-600 text-yellow-400 focus:ring-yellow-400"
                />
                <span class="text-white text-sm font-medium">
                  {{ hasSelection ? `Đã chọn ${selectedItems.length}` : 'Chọn tất cả' }}
                </span>
              </label>
            </div>
            
            <div v-if="hasSelection" class="flex items-center gap-2">
              <button 
                @click="bulkRemoveFromWatchlist"
                class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Xóa đã chọn
              </button>
            </div>
          </div>
          
          <EmptyState
            v-if="watchlist.length === 0"
            icon="list"
            :title="$t('library.emptyWatchlist')"
            :description="$t('library.emptyWatchlistDesc')"
            :action-text="$t('library.findMovies')"
            action-link="/home"
          />
          
          <!-- Movie Grid -->
          <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            <div
              v-for="movie in watchlist"
              :key="movie.id"
              class="group relative bg-gray-800 rounded-lg overflow-hidden hover:scale-105 transition-transform duration-300 cursor-pointer"
            >
              <!-- Selection Checkbox -->
              <div class="absolute top-2 left-2 z-10">
                <input 
                  type="checkbox" 
                  :checked="isSelected(movie.id)"
                  @change="toggleSelection(movie.id)"
                  @click.stop
                  class="w-5 h-5 rounded border-gray-600 text-yellow-400 focus:ring-yellow-400 cursor-pointer"
                />
              </div>
              
              <img :src="getImageUrl(movie.poster)" :alt="movie.title" class="w-full aspect-[2/3] object-cover" @error="(e) => e.target.src = 'https://placehold.co/400x600/1a1a1a/fff?text=Error'" />
              <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div class="absolute bottom-0 left-0 right-0 p-4">
                  <h4 class="text-white font-semibold text-sm mb-2">{{ movie.title }}</h4>
                  <div class="flex gap-2">
                    <button @click="watchMovie(movie)" class="flex-1 bg-yellow-500 text-black px-3 py-1.5 rounded text-xs font-semibold hover:bg-yellow-600 transition-colors">
                      Xem ngay
                    </button>
                    <button @click="removeFromWatchlist(movie)" class="p-1.5 bg-red-500 text-white rounded hover:bg-red-600 transition-colors" title="Xóa khỏi danh sách">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Xem tiếp Tab -->
        <div v-if="activeTab === 'continue'" class="space-y-6">
          <EmptyState
            v-if="continueWatching.length === 0"
            icon="clock"
            :title="$t('library.emptyHistory')"
            :description="$t('library.emptyHistoryDesc')"
            :action-text="$t('library.startWatching')"
            action-link="/home"
          />
          
          <!-- Movie Grid with Progress -->
          <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            <div
              v-for="movie in continueWatching"
              :key="movie.id"
              class="group relative bg-gray-800 rounded-lg overflow-hidden hover:scale-105 transition-transform duration-300 cursor-pointer"
            >
              <img :src="getImageUrl(movie.poster)" :alt="movie.title" class="w-full aspect-[2/3] object-cover" @error="(e) => e.target.src = 'https://placehold.co/400x600/1a1a1a/fff?text=Error'" />
              
              <!-- Progress Bar -->
              <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-700">
                <div 
                  class="h-full bg-yellow-500 transition-all duration-300"
                  :style="{ width: movie.progress + '%' }"
                ></div>
              </div>
              
              <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div class="absolute bottom-0 left-0 right-0 p-4">
                  <h4 class="text-white font-semibold text-sm mb-1">{{ movie.title }}</h4>
                  <p class="text-gray-400 text-xs mb-2">{{ movie.progress }}% {{ $t('library.watched') }}</p>
                  <div class="flex gap-2">
                    <button @click="watchMovie(movie)" class="flex-1 bg-yellow-500 text-black px-3 py-1.5 rounded text-xs font-semibold hover:bg-yellow-600 transition-colors">
                      {{ $t('library.continueWatching') }}
                    </button>
                    <button @click="removeFromContinue(movie)" class="p-1.5 bg-gray-700 text-white rounded hover:bg-gray-600 transition-colors" :title="$t('library.removeFromHistory')">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, h, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useLibraryStore } from '@/stores/libraryStore';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';
import { useUndo } from '@/composables/useUndo';
import { useBulkActions } from '@/composables/useBulkActions';
import EmptyState from '@/components/EmptyState.vue';
import UndoSnackbar from '@/components/UndoSnackbar.vue';

const router = useRouter();
const route = useRoute();
const libraryStore = useLibraryStore();
const toast = useToast();
const { confirm } = useConfirm();
const { showUndo, undoMessage, triggerUndo, handleUndo, closeUndo } = useUndo();
const { selectedItems, isSelected, toggleSelection, selectAll, deselectAll, hasSelection } = useBulkActions();

// Get tab from query parameter or default to 'favorites'
const activeTab = ref(route.query.tab || 'favorites');

// Tab icons as functional components
const HeartIcon = () => h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
  h('path', { 'fill-rule': 'evenodd', d: 'M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z', 'clip-rule': 'evenodd' })
]);

const ListIcon = () => h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
  h('path', { d: 'M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z' })
]);

const ClockIcon = () => h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
  h('path', { 'fill-rule': 'evenodd', d: 'M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z', 'clip-rule': 'evenodd' })
]);

// Get data from store
const favorites = computed(() => libraryStore.favorites);
const watchlist = computed(() => libraryStore.watchlist);
const continueWatching = computed(() => libraryStore.continueWatching);

import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const tabs = computed(() => [
  {
    id: 'favorites',
    label: t('library.favorites'),
    icon: HeartIcon,
    count: libraryStore.favoritesCount
  },
  {
    id: 'watchlist',
    label: t('library.watchlist'),
    icon: ListIcon,
    count: libraryStore.watchlistCount
  },
  {
    id: 'continue',
    label: t('library.continue'),
    icon: ClockIcon,
    count: libraryStore.continueWatchingCount
  }
]);

// Get image URL
const getImageUrl = (url) => {
  if (!url) return 'https://placehold.co/400x600/1a1a1a/fff?text=No+Image';
  return url.startsWith('http') ? url : `https://phimimg.com/${url}`;
};

// Watch movie
const watchMovie = (movie) => {
  router.push(`/film/${movie.slug}`);
};

// Remove from favorites
const removeFromFavorites = async (movie) => {
  const confirmed = await confirm({
    title: t('library.removeFromFavoritesConfirm'),
    message: t('library.removeFromFavoritesMessage', { title: movie.title }),
    type: 'warning',
    confirmText: t('common.delete'),
    cancelText: t('common.cancel')
  });

  if (confirmed) {
    // Store movie data for undo
    const movieData = { ...movie };
    
    const success = await libraryStore.removeFromFavorites(movie.id);
    if (success) {
      // Show undo snackbar
      triggerUndo(t('library.removedFromFavorites', { title: movie.title }), async () => {
        await libraryStore.addToFavorites(movieData);
        toast.success(t('library.undoSuccess'));
      });
    } else {
      toast.error(t('library.cannotRemove'));
    }
  }
};

// Bulk remove from favorites
const bulkRemoveFromFavorites = async () => {
  if (!hasSelection) return;
  
  const confirmed = await confirm({
    title: t('library.bulkRemoveConfirm'),
    message: t('library.bulkRemoveMessage', { count: selectedItems.length }),
    type: 'warning',
    confirmText: t('library.deleteAll'),
    cancelText: t('common.cancel')
  });

  if (confirmed) {
    const moviesToRemove = favorites.value.filter(m => isSelected(m.id));
    const movieIds = selectedItems.value;
    
    let successCount = 0;
    for (const id of movieIds) {
      const success = await libraryStore.removeFromFavorites(id);
      if (success) successCount++;
    }
    
    deselectAll();
    
    if (successCount > 0) {
      triggerUndo(t('library.bulkRemovedFromFavorites', { count: successCount }), async () => {
        for (const movie of moviesToRemove) {
          await libraryStore.addToFavorites(movie);
        }
        toast.success(t('library.undoSuccess'));
      });
    } else {
      toast.error(t('library.cannotRemove'));
    }
  }
};

// Remove from watchlist
const removeFromWatchlist = async (movie) => {
  const confirmed = await confirm({
    title: t('library.removeFromWatchlistConfirm'),
    message: t('library.removeFromWatchlistMessage', { title: movie.title }),
    type: 'warning',
    confirmText: t('common.delete'),
    cancelText: t('common.cancel')
  });

  if (confirmed) {
    // Store movie data for undo
    const movieData = { ...movie };
    
    const success = await libraryStore.removeFromWatchlist(movie.id);
    if (success) {
      // Show undo snackbar
      triggerUndo(t('library.removedFromWatchlist', { title: movie.title }), async () => {
        await libraryStore.addToWatchlist(movieData);
        toast.success(t('library.undoSuccess'));
      });
    } else {
      toast.error(t('library.cannotRemove'));
    }
  }
};

// Bulk remove from watchlist
const bulkRemoveFromWatchlist = async () => {
  if (!hasSelection) return;
  
  const confirmed = await confirm({
    title: t('library.bulkRemoveConfirm'),
    message: t('library.bulkRemoveFromWatchlistMessage', { count: selectedItems.length }),
    type: 'warning',
    confirmText: t('library.deleteAll'),
    cancelText: t('common.cancel')
  });

  if (confirmed) {
    const moviesToRemove = watchlist.value.filter(m => isSelected(m.id));
    const movieIds = selectedItems.value;
    
    let successCount = 0;
    for (const id of movieIds) {
      const success = await libraryStore.removeFromWatchlist(id);
      if (success) successCount++;
    }
    
    deselectAll();
    
    if (successCount > 0) {
      triggerUndo(t('library.bulkRemovedFromWatchlist', { count: successCount }), async () => {
        for (const movie of moviesToRemove) {
          await libraryStore.addToWatchlist(movie);
        }
        toast.success(t('library.undoSuccess'));
      });
    } else {
      toast.error(t('library.cannotRemove'));
    }
  }
};

// Remove from continue watching
const removeFromContinue = async (movie) => {
  const confirmed = await confirm({
    title: t('library.removeFromHistoryConfirm'),
    message: t('library.removeFromHistoryMessage', { title: movie.title }),
    type: 'warning',
    confirmText: t('common.delete'),
    cancelText: t('common.cancel')
  });

  if (confirmed) {
    // Store movie data for undo
    const movieData = { ...movie };
    const progress = libraryStore.getContinueProgress(movie.id);
    
    const success = await libraryStore.removeFromContinue(movie.id);
    if (success) {
      // Show undo snackbar
      triggerUndo(t('library.removedFromHistory', { title: movie.title }), async () => {
        await libraryStore.saveContinueWatching(movieData, progress);
        toast.success(t('library.undoSuccess'));
      });
    } else {
      toast.error(t('library.cannotRemove'));
    }
  }
};

// Watch for query parameter changes
watch(() => route.query.tab, (newTab) => {
  if (newTab && ['favorites', 'watchlist', 'continue'].includes(newTab)) {
    activeTab.value = newTab;
  }
});

// Clear selections when changing tabs
watch(activeTab, () => {
  deselectAll();
});

// Load data on mount
onMounted(async () => {
  await libraryStore.loadAll();
  
  // Set active tab from query parameter
  if (route.query.tab && ['favorites', 'watchlist', 'continue'].includes(route.query.tab)) {
    activeTab.value = route.query.tab;
  }
});
</script>
