/**
 * API Response Caching Utility
 * Implements stale-while-revalidate strategy
 */

class APICache {
  constructor(options = {}) {
    this.cache = new Map();
    this.defaultTTL = options.defaultTTL || 5 * 60 * 1000; // 5 minutes
    this.maxSize = options.maxSize || 100; // Max 100 cached items
  }

  /**
   * Generate cache key from request
   */
  generateKey(url, params = {}) {
    const sortedParams = Object.keys(params)
      .sort()
      .map(key => `${key}=${JSON.stringify(params[key])}`)
      .join('&');
    return `${url}?${sortedParams}`;
  }

  /**
   * Get cached data
   */
  get(key) {
    const cached = this.cache.get(key);
    
    if (!cached) {
      return null;
    }

    const now = Date.now();
    const age = now - cached.timestamp;

    // Return cached data with metadata
    return {
      data: cached.data,
      age,
      isStale: age > cached.ttl,
      isFresh: age <= cached.ttl
    };
  }

  /**
   * Set cache data
   */
  set(key, data, ttl = this.defaultTTL) {
    // Implement LRU: Remove oldest if cache is full
    if (this.cache.size >= this.maxSize) {
      const firstKey = this.cache.keys().next().value;
      this.cache.delete(firstKey);
    }

    this.cache.set(key, {
      data,
      timestamp: Date.now(),
      ttl
    });
  }

  /**
   * Delete cached item
   */
  delete(key) {
    this.cache.delete(key);
  }

  /**
   * Clear all cache
   */
  clear() {
    this.cache.clear();
  }

  /**
   * Clear expired cache
   */
  clearExpired() {
    const now = Date.now();
    for (const [key, value] of this.cache.entries()) {
      if (now - value.timestamp > value.ttl * 2) {
        // Delete if older than 2x TTL
        this.cache.delete(key);
      }
    }
  }

  /**
   * Get cache stats
   */
  getStats() {
    return {
      size: this.cache.size,
      maxSize: this.maxSize,
      keys: Array.from(this.cache.keys())
    };
  }
}

// Create singleton instance
const cache = new APICache({
  defaultTTL: 5 * 60 * 1000, // 5 minutes
  maxSize: 100
});

/**
 * Simple wrapper for caching async functions
 * Usage: await apiCache.get(url, fetchFunction, ttl)
 */
export const apiCache = {
  async get(key, fetchFunction, ttl = 5 * 60 * 1000) {
    // Check cache
    const cached = cache.get(key);
    
    // Cache hit and fresh: return immediately
    if (cached && cached.isFresh) {
      console.log(`✅ Cache HIT (fresh): ${key}`);
      return cached.data;
    }
    
    // Cache hit but stale: return stale data and revalidate in background
    if (cached && cached.isStale) {
      console.log(`⚠️ Cache HIT (stale): ${key} - Revalidating...`);
      
      // Return stale data immediately
      const staleData = cached.data;
      
      // Revalidate in background (don't await)
      fetchFunction()
        .then(freshData => {
          cache.set(key, freshData, ttl);
          console.log(`✅ Cache UPDATED: ${key}`);
        })
        .catch(error => {
          console.error(`❌ Cache revalidation failed: ${key}`, error);
        });
      
      return staleData;
    }
    
    // Cache miss: fetch and cache
    console.log(`❌ Cache MISS: ${key}`);
    try {
      const data = await fetchFunction();
      cache.set(key, data, ttl);
      return data;
    } catch (error) {
      // If fetch fails and we have stale data, return it
      if (cached) {
        console.warn(`⚠️ Fetch failed, returning stale data: ${key}`);
        return cached.data;
      }
      throw error;
    }
  },
  
  invalidate(pattern) {
    const stats = cache.getStats();
    const keysToDelete = stats.keys.filter(key => key.includes(pattern));
    
    keysToDelete.forEach(key => {
      cache.delete(key);
    });
    
    console.log(`🗑️ Invalidated ${keysToDelete.length} cache entries matching: ${pattern}`);
  },
  
  clear() {
    cache.clear();
    console.log('🗑️ All cache cleared');
  },
  
  getStats() {
    return cache.getStats();
  }
};

/**
 * Cached fetch wrapper
 * Implements stale-while-revalidate pattern
 */
export async function cachedFetch(url, options = {}) {
  const {
    params = {},
    ttl,
    forceRefresh = false,
    staleWhileRevalidate = true
  } = options;

  const cacheKey = cache.generateKey(url, params);

  // Force refresh: skip cache
  if (forceRefresh) {
    const response = await fetch(url, options);
    const data = await response.json();
    cache.set(cacheKey, data, ttl);
    return data;
  }

  // Check cache
  const cached = cache.get(cacheKey);

  // Cache hit and fresh: return immediately
  if (cached && cached.isFresh) {
    console.log(`✅ Cache HIT (fresh): ${cacheKey}`);
    return cached.data;
  }

  // Cache hit but stale: return stale data and revalidate in background
  if (cached && cached.isStale && staleWhileRevalidate) {
    console.log(`⚠️ Cache HIT (stale): ${cacheKey} - Revalidating...`);
    
    // Return stale data immediately
    const staleData = cached.data;
    
    // Revalidate in background (don't await)
    fetch(url, options)
      .then(response => response.json())
      .then(freshData => {
        cache.set(cacheKey, freshData, ttl);
        console.log(`✅ Cache UPDATED: ${cacheKey}`);
      })
      .catch(error => {
        console.error(`❌ Cache revalidation failed: ${cacheKey}`, error);
      });
    
    return staleData;
  }

  // Cache miss: fetch and cache
  console.log(`❌ Cache MISS: ${cacheKey}`);
  try {
    const response = await fetch(url, options);
    const data = await response.json();
    cache.set(cacheKey, data, ttl);
    return data;
  } catch (error) {
    // If fetch fails and we have stale data, return it
    if (cached) {
      console.warn(`⚠️ Fetch failed, returning stale data: ${cacheKey}`);
      return cached.data;
    }
    throw error;
  }
}

/**
 * Invalidate cache by pattern
 */
export function invalidateCache(pattern) {
  const stats = cache.getStats();
  const keysToDelete = stats.keys.filter(key => key.includes(pattern));
  
  keysToDelete.forEach(key => {
    cache.delete(key);
  });
  
  console.log(`🗑️ Invalidated ${keysToDelete.length} cache entries matching: ${pattern}`);
}

/**
 * Clear all cache
 */
export function clearAllCache() {
  cache.clear();
  console.log('🗑️ All cache cleared');
}

/**
 * Get cache stats
 */
export function getCacheStats() {
  return cache.getStats();
}

/**
 * Preload data into cache
 */
export async function preloadCache(url, params = {}, ttl) {
  const cacheKey = cache.generateKey(url, params);
  
  try {
    const response = await fetch(url);
    const data = await response.json();
    cache.set(cacheKey, data, ttl);
    console.log(`📥 Preloaded cache: ${cacheKey}`);
    return data;
  } catch (error) {
    console.error(`❌ Preload failed: ${cacheKey}`, error);
    throw error;
  }
}

// Auto-clear expired cache every 10 minutes
setInterval(() => {
  cache.clearExpired();
  console.log('🧹 Cleared expired cache');
}, 10 * 60 * 1000);

// Export simple functions for testing
export function getCachedData(key) {
  const cached = cache.get(key);
  return cached && cached.isFresh ? cached.data : null;
}

export function setCachedData(key, data, ttl) {
  cache.set(key, data, ttl);
}

export function clearCache() {
  cache.clear();
}

export function clearExpiredCache() {
  cache.clearExpired();
}
