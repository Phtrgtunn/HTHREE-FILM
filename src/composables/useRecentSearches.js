import { ref, watch } from 'vue';

/**
 * Composable for managing recent searches
 * Stores and retrieves recent search queries
 */
export function useRecentSearches(maxItems = 10) {
  const STORAGE_KEY = 'recent_searches';
  
  // Load from localStorage
  const loadSearches = () => {
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      return stored ? JSON.parse(stored) : [];
    } catch (e) {
      console.error('Failed to load recent searches:', e);
      return [];
    }
  };

  const recentSearches = ref(loadSearches());

  // Save to localStorage
  const saveSearches = () => {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(recentSearches.value));
    } catch (e) {
      console.error('Failed to save recent searches:', e);
    }
  };

  // Add search query
  const addSearch = (query) => {
    if (!query || !query.trim()) return;

    const trimmedQuery = query.trim();
    
    // Remove if already exists
    recentSearches.value = recentSearches.value.filter(
      item => item.toLowerCase() !== trimmedQuery.toLowerCase()
    );
    
    // Add to beginning
    recentSearches.value.unshift(trimmedQuery);
    
    // Keep only max items
    if (recentSearches.value.length > maxItems) {
      recentSearches.value = recentSearches.value.slice(0, maxItems);
    }
    
    saveSearches();
  };

  // Remove search query
  const removeSearch = (query) => {
    recentSearches.value = recentSearches.value.filter(
      item => item !== query
    );
    saveSearches();
  };

  // Clear all searches
  const clearSearches = () => {
    recentSearches.value = [];
    localStorage.removeItem(STORAGE_KEY);
  };

  // Get filtered suggestions
  const getFilteredSearches = (query) => {
    if (!query) return recentSearches.value;
    
    const lowerQuery = query.toLowerCase();
    return recentSearches.value.filter(item =>
      item.toLowerCase().includes(lowerQuery)
    );
  };

  return {
    recentSearches,
    addSearch,
    removeSearch,
    clearSearches,
    getFilteredSearches
  };
}
