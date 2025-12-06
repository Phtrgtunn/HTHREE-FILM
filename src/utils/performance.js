/**
 * Performance utilities for optimizing app performance
 */

// Debounce function
export function debounce(func, wait = 300) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Throttle function
export function throttle(func, limit = 300) {
  let inThrottle;
  return function executedFunction(...args) {
    if (!inThrottle) {
      func(...args);
      inThrottle = true;
      setTimeout(() => (inThrottle = false), limit);
    }
  };
}

// Lazy load images with Intersection Observer
export function lazyLoadImages(selector = 'img[data-src]') {
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
          imageObserver.unobserve(img);
        }
      });
    });

    document.querySelectorAll(selector).forEach((img) => {
      imageObserver.observe(img);
    });

    return imageObserver;
  }
}

// Preload critical images
export function preloadImage(src) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => resolve(img);
    img.onerror = reject;
    img.src = src;
  });
}

// Preload multiple images
export async function preloadImages(srcs) {
  const promises = srcs.map((src) => preloadImage(src));
  return Promise.all(promises);
}

// Check if element is in viewport
export function isInViewport(element) {
  const rect = element.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
}

// Measure performance
export function measurePerformance(name, fn) {
  const start = performance.now();
  const result = fn();
  const end = performance.now();
  console.log(`⏱️ ${name} took ${(end - start).toFixed(2)}ms`);
  return result;
}

// Async measure performance
export async function measurePerformanceAsync(name, fn) {
  const start = performance.now();
  const result = await fn();
  const end = performance.now();
  console.log(`⏱️ ${name} took ${(end - start).toFixed(2)}ms`);
  return result;
}

// Request idle callback wrapper
export function runWhenIdle(callback, options = {}) {
  if ('requestIdleCallback' in window) {
    return requestIdleCallback(callback, options);
  } else {
    // Fallback for browsers that don't support requestIdleCallback
    return setTimeout(callback, 1);
  }
}

// Cancel idle callback
export function cancelIdle(id) {
  if ('cancelIdleCallback' in window) {
    cancelIdleCallback(id);
  } else {
    clearTimeout(id);
  }
}

// Batch DOM updates
export function batchDOMUpdates(updates) {
  requestAnimationFrame(() => {
    updates.forEach((update) => update());
  });
}

// Virtual scroll helper
export function calculateVisibleRange(scrollTop, itemHeight, containerHeight, totalItems, overscan = 3) {
  const startIndex = Math.max(0, Math.floor(scrollTop / itemHeight) - overscan);
  const endIndex = Math.min(
    totalItems - 1,
    Math.ceil((scrollTop + containerHeight) / itemHeight) + overscan
  );
  
  return { startIndex, endIndex };
}

// Memory usage (if available)
export function getMemoryUsage() {
  if (performance.memory) {
    return {
      used: (performance.memory.usedJSHeapSize / 1048576).toFixed(2) + ' MB',
      total: (performance.memory.totalJSHeapSize / 1048576).toFixed(2) + ' MB',
      limit: (performance.memory.jsHeapSizeLimit / 1048576).toFixed(2) + ' MB'
    };
  }
  return null;
}

// Log performance metrics
export function logPerformanceMetrics() {
  if (window.performance && window.performance.timing) {
    const timing = window.performance.timing;
    const metrics = {
      'DNS Lookup': timing.domainLookupEnd - timing.domainLookupStart,
      'TCP Connection': timing.connectEnd - timing.connectStart,
      'Request Time': timing.responseStart - timing.requestStart,
      'Response Time': timing.responseEnd - timing.responseStart,
      'DOM Processing': timing.domComplete - timing.domLoading,
      'Total Load Time': timing.loadEventEnd - timing.navigationStart
    };

    console.table(metrics);
  }
}

// Check if user prefers reduced motion
export function prefersReducedMotion() {
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

// Optimize animation based on user preference
export function getAnimationDuration(defaultDuration = 300) {
  return prefersReducedMotion() ? 0 : defaultDuration;
}
