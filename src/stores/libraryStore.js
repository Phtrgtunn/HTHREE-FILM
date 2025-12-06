import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useLibraryStore = defineStore('library', () => {
  // State
  const favorites = ref([]);
  const watchlist = ref([]);
  const continueWatching = ref([]);
  const loading = ref(false);
  const error = ref(null);

  // Computed
  const favoritesCount = computed(() => favorites.value.length);
  const watchlistCount = computed(() => watchlist.value.length);
  const continueWatchingCount = computed(() => continueWatching.value.length);

  // Helper: Get from localStorage
  const getFromLocalStorage = (key) => {
    try {
      const data = localStorage.getItem(key);
      return data ? JSON.parse(data) : [];
    } catch (e) {
      console.error(`Error reading ${key} from localStorage:`, e);
      return [];
    }
  };

  // Helper: Save to localStorage
  const saveToLocalStorage = (key, data) => {
    try {
      localStorage.setItem(key, JSON.stringify(data));
    } catch (e) {
      console.error(`Error saving ${key} to localStorage:`, e);
    }
  };

  // Load all data
  const loadAll = async () => {
    loading.value = true;
    error.value = null;

    try {
      // Load from localStorage (fallback if no API)
      favorites.value = getFromLocalStorage('library_favorites');
      watchlist.value = getFromLocalStorage('library_watchlist');
      continueWatching.value = getFromLocalStorage('library_continue');

      // TODO: Load from API when backend is ready
      // const response = await fetch('/api/library/all');
      // const data = await response.json();
      // favorites.value = data.favorites;
      // watchlist.value = data.watchlist;
      // continueWatching.value = data.continueWatching;
    } catch (e) {
      error.value = e.message;
      console.error('Error loading library:', e);
    } finally {
      loading.value = false;
    }
  };

  // ==================== FAVORITES ====================

  // Check if movie is in favorites
  const isFavorite = (movieId) => {
    return favorites.value.some(m => m.id === movieId || m.slug === movieId);
  };

  // Add to favorites
  const addToFavorites = async (movie) => {
    try {
      // Check if already exists
      if (isFavorite(movie.id || movie.slug)) {
        console.log('Movie already in favorites');
        return;
      }

      // Add to state
      const favoriteMovie = {
        id: movie.id || movie.slug,
        slug: movie.slug,
        title: movie.name || movie.title,
        poster: movie.poster_url || movie.poster,
        year: movie.year,
        quality: movie.quality,
        addedAt: new Date().toISOString()
      };

      favorites.value.unshift(favoriteMovie);
      saveToLocalStorage('library_favorites', favorites.value);

      // TODO: Save to API
      // await fetch('/api/library/favorites', {
      //   method: 'POST',
      //   body: JSON.stringify(favoriteMovie)
      // });

      return true;
    } catch (e) {
      error.value = e.message;
      console.error('Error adding to favorites:', e);
      return false;
    }
  };

  // Remove from favorites
  const removeFromFavorites = async (movieId) => {
    try {
      favorites.value = favorites.value.filter(
        m => m.id !== movieId && m.slug !== movieId
      );
      saveToLocalStorage('library_favorites', favorites.value);

      // TODO: Delete from API
      // await fetch(`/api/library/favorites/${movieId}`, {
      //   method: 'DELETE'
      // });

      return true;
    } catch (e) {
      error.value = e.message;
      console.error('Error removing from favorites:', e);
      return false;
    }
  };

  // Toggle favorite
  const toggleFavorite = async (movie) => {
    if (isFavorite(movie.id || movie.slug)) {
      return await removeFromFavorites(movie.id || movie.slug);
    } else {
      return await addToFavorites(movie);
    }
  };

  // ==================== WATCHLIST ====================

  // Check if movie is in watchlist
  const isInWatchlist = (movieId) => {
    return watchlist.value.some(m => m.id === movieId || m.slug === movieId);
  };

  // Add to watchlist
  const addToWatchlist = async (movie) => {
    try {
      if (isInWatchlist(movie.id || movie.slug)) {
        console.log('Movie already in watchlist');
        return;
      }

      const watchlistMovie = {
        id: movie.id || movie.slug,
        slug: movie.slug,
        title: movie.name || movie.title,
        poster: movie.poster_url || movie.poster,
        year: movie.year,
        quality: movie.quality,
        addedAt: new Date().toISOString()
      };

      watchlist.value.unshift(watchlistMovie);
      saveToLocalStorage('library_watchlist', watchlist.value);

      // TODO: Save to API
      // await fetch('/api/library/watchlist', {
      //   method: 'POST',
      //   body: JSON.stringify(watchlistMovie)
      // });

      return true;
    } catch (e) {
      error.value = e.message;
      console.error('Error adding to watchlist:', e);
      return false;
    }
  };

  // Remove from watchlist
  const removeFromWatchlist = async (movieId) => {
    try {
      watchlist.value = watchlist.value.filter(
        m => m.id !== movieId && m.slug !== movieId
      );
      saveToLocalStorage('library_watchlist', watchlist.value);

      // TODO: Delete from API
      // await fetch(`/api/library/watchlist/${movieId}`, {
      //   method: 'DELETE'
      // });

      return true;
    } catch (e) {
      error.value = e.message;
      console.error('Error removing from watchlist:', e);
      return false;
    }
  };

  // Toggle watchlist
  const toggleWatchlist = async (movie) => {
    if (isInWatchlist(movie.id || movie.slug)) {
      return await removeFromWatchlist(movie.id || movie.slug);
    } else {
      return await addToWatchlist(movie);
    }
  };

  // ==================== CONTINUE WATCHING ====================

  // Check if movie is in continue watching
  const isInContinue = (movieId) => {
    return continueWatching.value.some(m => m.id === movieId || m.slug === movieId);
  };

  // Save continue watching
  const saveContinueWatching = async (movie, progress, currentTime = 0) => {
    try {
      // Remove if already exists
      continueWatching.value = continueWatching.value.filter(
        m => m.id !== (movie.id || movie.slug) && m.slug !== movie.slug
      );

      // Add/Update
      const continueMovie = {
        id: movie.id || movie.slug,
        slug: movie.slug,
        title: movie.name || movie.title,
        poster: movie.poster_url || movie.poster,
        year: movie.year,
        quality: movie.quality,
        progress: Math.round(progress), // %
        currentTime: Math.round(currentTime), // seconds
        lastWatched: new Date().toISOString()
      };

      continueWatching.value.unshift(continueMovie);
      
      // Keep only last 20 items
      if (continueWatching.value.length > 20) {
        continueWatching.value = continueWatching.value.slice(0, 20);
      }

      saveToLocalStorage('library_continue', continueWatching.value);

      // TODO: Save to API
      // await fetch('/api/library/continue', {
      //   method: 'POST',
      //   body: JSON.stringify(continueMovie)
      // });

      return true;
    } catch (e) {
      error.value = e.message;
      console.error('Error saving continue watching:', e);
      return false;
    }
  };

  // Get continue watching progress
  const getContinueProgress = (movieId) => {
    const movie = continueWatching.value.find(
      m => m.id === movieId || m.slug === movieId
    );
    return movie ? { progress: movie.progress, currentTime: movie.currentTime } : null;
  };

  // Remove from continue watching
  const removeFromContinue = async (movieId) => {
    try {
      continueWatching.value = continueWatching.value.filter(
        m => m.id !== movieId && m.slug !== movieId
      );
      saveToLocalStorage('library_continue', continueWatching.value);

      // TODO: Delete from API
      // await fetch(`/api/library/continue/${movieId}`, {
      //   method: 'DELETE'
      // });

      return true;
    } catch (e) {
      error.value = e.message;
      console.error('Error removing from continue:', e);
      return false;
    }
  };

  // Clear all
  const clearAll = () => {
    favorites.value = [];
    watchlist.value = [];
    continueWatching.value = [];
    
    localStorage.removeItem('library_favorites');
    localStorage.removeItem('library_watchlist');
    localStorage.removeItem('library_continue');
  };

  return {
    // State
    favorites,
    watchlist,
    continueWatching,
    loading,
    error,

    // Computed
    favoritesCount,
    watchlistCount,
    continueWatchingCount,

    // Actions
    loadAll,
    
    // Favorites
    isFavorite,
    addToFavorites,
    removeFromFavorites,
    toggleFavorite,
    
    // Watchlist
    isInWatchlist,
    addToWatchlist,
    removeFromWatchlist,
    toggleWatchlist,
    
    // Continue Watching
    isInContinue,
    saveContinueWatching,
    getContinueProgress,
    removeFromContinue,
    
    // Utils
    clearAll
  };
});
