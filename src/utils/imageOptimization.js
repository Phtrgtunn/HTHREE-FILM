/**
 * Image Optimization Utilities
 * Cải thiện hiệu suất tải ảnh
 */

/**
 * Tạo srcset cho responsive images
 */
export function generateSrcSet(baseUrl, sizes = [320, 640, 960, 1280, 1920]) {
  if (!baseUrl) return '';
  
  return sizes
    .map(size => `${baseUrl}?w=${size} ${size}w`)
    .join(', ');
}

/**
 * Lazy load image với Intersection Observer
 */
export function lazyLoadImage(img, options = {}) {
  const {
    root = null,
    rootMargin = '50px',
    threshold = 0.01
  } = options;

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const lazyImage = entry.target;
          
          // Load image
          if (lazyImage.dataset.src) {
            lazyImage.src = lazyImage.dataset.src;
          }
          
          if (lazyImage.dataset.srcset) {
            lazyImage.srcset = lazyImage.dataset.srcset;
          }
          
          // Remove loading class
          lazyImage.classList.remove('lazy-loading');
          lazyImage.classList.add('lazy-loaded');
          
          // Stop observing
          observer.unobserve(lazyImage);
        }
      });
    }, { root, rootMargin, threshold });

    observer.observe(img);
    
    return () => observer.disconnect();
  } else {
    // Fallback for browsers without IntersectionObserver
    if (img.dataset.src) {
      img.src = img.dataset.src;
    }
    if (img.dataset.srcset) {
      img.srcset = img.dataset.srcset;
    }
  }
}

/**
 * Preload critical images
 */
export function preloadImage(url) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => resolve(img);
    img.onerror = reject;
    img.src = url;
  });
}

/**
 * Preload multiple images
 */
export async function preloadImages(urls) {
  const promises = urls.map(url => preloadImage(url));
  return Promise.all(promises);
}

/**
 * Get optimized image URL
 */
export function getOptimizedImageUrl(url, options = {}) {
  if (!url) return 'https://placehold.co/400x600/1a1a1a/fff?text=No+Image';
  
  const {
    width,
    height,
    quality = 80,
    format = 'webp'
  } = options;

  // If already a full URL, return as is
  if (url.startsWith('http')) {
    return url;
  }

  // Build optimized URL (adjust based on your CDN)
  let optimizedUrl = `https://phimimg.com/${url}`;
  const params = [];
  
  if (width) params.push(`w=${width}`);
  if (height) params.push(`h=${height}`);
  if (quality) params.push(`q=${quality}`);
  if (format) params.push(`f=${format}`);
  
  if (params.length > 0) {
    optimizedUrl += `?${params.join('&')}`;
  }
  
  return optimizedUrl;
}

/**
 * Create blur placeholder
 */
export function createBlurPlaceholder(width = 10, height = 15) {
  const canvas = document.createElement('canvas');
  canvas.width = width;
  canvas.height = height;
  const ctx = canvas.getContext('2d');
  
  // Create gradient
  const gradient = ctx.createLinearGradient(0, 0, width, height);
  gradient.addColorStop(0, '#1a1a1a');
  gradient.addColorStop(1, '#2a2a2a');
  
  ctx.fillStyle = gradient;
  ctx.fillRect(0, 0, width, height);
  
  return canvas.toDataURL();
}

/**
 * Check if image is in viewport
 */
export function isImageInViewport(img, offset = 0) {
  const rect = img.getBoundingClientRect();
  return (
    rect.top >= -offset &&
    rect.left >= -offset &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) + offset &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth) + offset
  );
}

/**
 * Decode image for better performance
 */
export async function decodeImage(img) {
  if ('decode' in img) {
    try {
      await img.decode();
    } catch (error) {
      console.warn('Image decode failed:', error);
    }
  }
}

/**
 * Get image dimensions without loading full image
 */
export function getImageDimensions(url) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => {
      resolve({
        width: img.naturalWidth,
        height: img.naturalHeight,
        aspectRatio: img.naturalWidth / img.naturalHeight
      });
    };
    img.onerror = reject;
    img.src = url;
  });
}

/**
 * Progressive image loading
 */
export class ProgressiveImage {
  constructor(container, options = {}) {
    this.container = container;
    this.options = {
      placeholder: options.placeholder || createBlurPlaceholder(),
      lowQuality: options.lowQuality,
      highQuality: options.highQuality,
      alt: options.alt || '',
      onLoad: options.onLoad || (() => {}),
      onError: options.onError || (() => {})
    };
    
    this.init();
  }

  init() {
    // Show placeholder
    this.showPlaceholder();
    
    // Load low quality first
    if (this.options.lowQuality) {
      this.loadLowQuality();
    } else {
      this.loadHighQuality();
    }
  }

  showPlaceholder() {
    const img = document.createElement('img');
    img.src = this.options.placeholder;
    img.alt = this.options.alt;
    img.className = 'progressive-placeholder';
    img.style.filter = 'blur(10px)';
    img.style.transition = 'filter 0.3s ease';
    this.container.appendChild(img);
    this.placeholderImg = img;
  }

  loadLowQuality() {
    const img = new Image();
    img.onload = () => {
      this.placeholderImg.src = this.options.lowQuality;
      this.loadHighQuality();
    };
    img.src = this.options.lowQuality;
  }

  loadHighQuality() {
    const img = new Image();
    img.onload = () => {
      this.placeholderImg.src = this.options.highQuality;
      this.placeholderImg.style.filter = 'blur(0)';
      this.options.onLoad();
    };
    img.onerror = () => {
      this.options.onError();
    };
    img.src = this.options.highQuality;
  }
}

export default {
  generateSrcSet,
  lazyLoadImage,
  preloadImage,
  preloadImages,
  getOptimizedImageUrl,
  createBlurPlaceholder,
  isImageInViewport,
  decodeImage,
  getImageDimensions,
  ProgressiveImage
};
