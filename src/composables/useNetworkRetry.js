/**
 * Composable để retry network requests với exponential backoff
 */

/**
 * Retry async function với exponential backoff
 * @param {Function} fn - Async function to retry
 * @param {Object} options - Retry options
 * @returns {Promise} Result of function
 */
export async function retryWithBackoff(fn, options = {}) {
  const {
    retries = 3,
    initialDelay = 1000,
    maxDelay = 10000,
    timeout = 30000,
    onRetry = null
  } = options;

  let lastError = null;

  for (let attempt = 1; attempt <= retries; attempt++) {
    try {
      // Add timeout to request
      const result = await Promise.race([
        fn(),
        new Promise((_, reject) =>
          setTimeout(() => reject(new Error('Request timeout')), timeout)
        )
      ]);

      return result;
    } catch (error) {
      lastError = error;
      
      console.error(`❌ Attempt ${attempt}/${retries} failed:`, error.message);

      // If not last attempt, wait before retry
      if (attempt < retries) {
        // Exponential backoff with jitter
        const delay = Math.min(
          initialDelay * Math.pow(2, attempt - 1) + Math.random() * 1000,
          maxDelay
        );
        
        console.log(`⏳ Retrying in ${Math.round(delay)}ms...`);
        
        if (onRetry) {
          onRetry(attempt, delay, error);
        }
        
        await new Promise(resolve => setTimeout(resolve, delay));
      }
    }
  }

  // All retries failed
  throw lastError;
}

/**
 * Composable for network retry
 */
export function useNetworkRetry() {
  /**
   * Fetch with retry
   */
  const fetchWithRetry = async (url, options = {}, retryOptions = {}) => {
    return retryWithBackoff(
      () => fetch(url, options).then(res => {
        if (!res.ok) {
          throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }
        return res.json();
      }),
      retryOptions
    );
  };

  /**
   * Supabase query with retry
   */
  const supabaseWithRetry = async (queryFn, retryOptions = {}) => {
    return retryWithBackoff(
      async () => {
        const { data, error } = await queryFn();
        if (error) throw error;
        return data;
      },
      retryOptions
    );
  };

  /**
   * Check network status
   */
  const isOnline = () => {
    return navigator.onLine;
  };

  /**
   * Wait for network to be online
   */
  const waitForOnline = () => {
    return new Promise(resolve => {
      if (navigator.onLine) {
        resolve();
      } else {
        const handler = () => {
          window.removeEventListener('online', handler);
          resolve();
        };
        window.addEventListener('online', handler);
      }
    });
  };

  return {
    fetchWithRetry,
    supabaseWithRetry,
    isOnline,
    waitForOnline,
    retryWithBackoff
  };
}
