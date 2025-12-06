/**
 * Composable để quản lý Google Analytics 4
 * Hỗ trợ: page views, events, conversions, user properties
 */

/**
 * Initialize Google Analytics
 * @param {string} measurementId - GA4 Measurement ID (G-XXXXXXXXXX)
 */
export function initAnalytics(measurementId) {
  if (!measurementId || typeof window === 'undefined') return;
  
  // Load gtag.js script
  const script = document.createElement('script');
  script.async = true;
  script.src = `https://www.googletagmanager.com/gtag/js?id=${measurementId}`;
  document.head.appendChild(script);
  
  // Initialize dataLayer
  window.dataLayer = window.dataLayer || [];
  function gtag() {
    window.dataLayer.push(arguments);
  }
  window.gtag = gtag;
  
  gtag('js', new Date());
  gtag('config', measurementId, {
    send_page_view: false // We'll send manually
  });
  
  console.log('✅ Google Analytics initialized:', measurementId);
}

/**
 * Composable for analytics tracking
 */
export function useAnalytics() {
  /**
   * Track page view
   * @param {string} pagePath - Page path (e.g., '/home', '/film/slug')
   * @param {string} pageTitle - Page title
   */
  const trackPageView = (pagePath, pageTitle) => {
    if (typeof window === 'undefined' || !window.gtag) return;
    
    window.gtag('event', 'page_view', {
      page_path: pagePath,
      page_title: pageTitle,
      page_location: window.location.href
    });
    
    console.log('📊 Page view tracked:', pagePath);
  };
  
  /**
   * Track custom event
   * @param {string} eventName - Event name (e.g., 'play_movie', 'add_to_cart')
   * @param {object} eventParams - Event parameters
   */
  const trackEvent = (eventName, eventParams = {}) => {
    if (typeof window === 'undefined' || !window.gtag) return;
    
    window.gtag('event', eventName, eventParams);
    
    console.log('📊 Event tracked:', eventName, eventParams);
  };
  
  /**
   * Track movie play
   * @param {object} movie - Movie object
   */
  const trackMoviePlay = (movie) => {
    trackEvent('play_movie', {
      movie_id: movie.id || movie.slug,
      movie_title: movie.name || movie.title,
      movie_type: movie.type,
      movie_quality: movie.quality
    });
  };
  
  /**
   * Track search
   * @param {string} searchTerm - Search query
   * @param {number} resultsCount - Number of results
   */
  const trackSearch = (searchTerm, resultsCount = 0) => {
    trackEvent('search', {
      search_term: searchTerm,
      results_count: resultsCount
    });
  };
  
  /**
   * Track add to cart
   * @param {object} item - Cart item
   */
  const trackAddToCart = (item) => {
    trackEvent('add_to_cart', {
      item_id: item.id,
      item_name: item.name,
      price: item.price,
      quantity: item.quantity || 1
    });
  };
  
  /**
   * Track purchase
   * @param {object} transaction - Transaction details
   */
  const trackPurchase = (transaction) => {
    trackEvent('purchase', {
      transaction_id: transaction.id,
      value: transaction.total,
      currency: 'VND',
      items: transaction.items
    });
  };
  
  /**
   * Track user signup
   * @param {string} method - Signup method (email, google, facebook)
   */
  const trackSignup = (method) => {
    trackEvent('sign_up', {
      method: method
    });
  };
  
  /**
   * Track user login
   * @param {string} method - Login method
   */
  const trackLogin = (method) => {
    trackEvent('login', {
      method: method
    });
  };
  
  /**
   * Set user properties
   * @param {object} properties - User properties
   */
  const setUserProperties = (properties) => {
    if (typeof window === 'undefined' || !window.gtag) return;
    
    window.gtag('set', 'user_properties', properties);
    
    console.log('📊 User properties set:', properties);
  };
  
  /**
   * Set user ID
   * @param {string} userId - User ID
   */
  const setUserId = (userId) => {
    if (typeof window === 'undefined' || !window.gtag) return;
    
    window.gtag('config', window.GA_MEASUREMENT_ID, {
      user_id: userId
    });
    
    console.log('📊 User ID set:', userId);
  };
  
  return {
    trackPageView,
    trackEvent,
    trackMoviePlay,
    trackSearch,
    trackAddToCart,
    trackPurchase,
    trackSignup,
    trackLogin,
    setUserProperties,
    setUserId
  };
}
