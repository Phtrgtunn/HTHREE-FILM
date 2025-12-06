/**
 * Composable để quản lý error tracking
 * Hỗ trợ: Sentry integration, custom error logging
 */

/**
 * Initialize Sentry (placeholder - cần cài @sentry/vue)
 * @param {string} dsn - Sentry DSN
 * @param {object} options - Sentry options
 */
export function initSentry(dsn, options = {}) {
  if (!dsn || typeof window === 'undefined') return;
  
  // TODO: Install @sentry/vue package
  // import * as Sentry from "@sentry/vue";
  // Sentry.init({
  //   app,
  //   dsn: dsn,
  //   integrations: [
  //     new Sentry.BrowserTracing(),
  //     new Sentry.Replay()
  //   ],
  //   tracesSampleRate: 1.0,
  //   replaysSessionSampleRate: 0.1,
  //   replaysOnErrorSampleRate: 1.0,
  //   ...options
  // });
  
  console.log('⚠️ Sentry not initialized - install @sentry/vue package');
}

/**
 * Composable for error tracking
 */
export function useErrorTracking() {
  /**
   * Log error to console and tracking service
   * @param {Error} error - Error object
   * @param {object} context - Additional context
   */
  const logError = (error, context = {}) => {
    console.error('❌ Error:', error);
    console.error('📝 Context:', context);
    
    // TODO: Send to Sentry
    // if (window.Sentry) {
    //   window.Sentry.captureException(error, {
    //     extra: context
    //   });
    // }
    
    // Fallback: Send to custom backend
    sendErrorToBackend(error, context);
  };
  
  /**
   * Log warning
   * @param {string} message - Warning message
   * @param {object} context - Additional context
   */
  const logWarning = (message, context = {}) => {
    console.warn('⚠️ Warning:', message);
    console.warn('📝 Context:', context);
    
    // TODO: Send to Sentry
    // if (window.Sentry) {
    //   window.Sentry.captureMessage(message, {
    //     level: 'warning',
    //     extra: context
    //   });
    // }
  };
  
  /**
   * Log info message
   * @param {string} message - Info message
   * @param {object} context - Additional context
   */
  const logInfo = (message, context = {}) => {
    console.log('ℹ️ Info:', message);
    
    // TODO: Send to Sentry
    // if (window.Sentry) {
    //   window.Sentry.captureMessage(message, {
    //     level: 'info',
    //     extra: context
    //   });
    // }
  };
  
  /**
   * Set user context for error tracking
   * @param {object} user - User object
   */
  const setUserContext = (user) => {
    if (!user) return;
    
    // TODO: Set Sentry user
    // if (window.Sentry) {
    //   window.Sentry.setUser({
    //     id: user.id,
    //     email: user.email,
    //     username: user.username
    //   });
    // }
    
    console.log('👤 User context set:', user.id);
  };
  
  /**
   * Add breadcrumb for debugging
   * @param {string} message - Breadcrumb message
   * @param {object} data - Additional data
   */
  const addBreadcrumb = (message, data = {}) => {
    // TODO: Add Sentry breadcrumb
    // if (window.Sentry) {
    //   window.Sentry.addBreadcrumb({
    //     message: message,
    //     data: data,
    //     level: 'info'
    //   });
    // }
    
    console.log('🍞 Breadcrumb:', message, data);
  };
  
  /**
   * Send error to custom backend
   * @param {Error} error - Error object
   * @param {object} context - Additional context
   */
  const sendErrorToBackend = async (error, context = {}) => {
    try {
      // TODO: Implement custom error logging endpoint
      // await fetch('/api/log-error', {
      //   method: 'POST',
      //   headers: { 'Content-Type': 'application/json' },
      //   body: JSON.stringify({
      //     message: error.message,
      //     stack: error.stack,
      //     context: context,
      //     timestamp: new Date().toISOString(),
      //     userAgent: navigator.userAgent,
      //     url: window.location.href
      //   })
      // });
    } catch (err) {
      console.error('Failed to send error to backend:', err);
    }
  };
  
  return {
    logError,
    logWarning,
    logInfo,
    setUserContext,
    addBreadcrumb
  };
}

/**
 * Global error handler
 */
export function setupGlobalErrorHandler(app) {
  // Vue error handler
  app.config.errorHandler = (err, instance, info) => {
    console.error('Vue Error:', err);
    console.error('Component:', instance);
    console.error('Info:', info);
    
    const { logError } = useErrorTracking();
    logError(err, {
      component: instance?.$options?.name || 'Unknown',
      info: info
    });
  };
  
  // Global unhandled promise rejection
  window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled Promise Rejection:', event.reason);
    
    const { logError } = useErrorTracking();
    logError(new Error(event.reason), {
      type: 'unhandledrejection'
    });
  });
  
  // Global error
  window.addEventListener('error', (event) => {
    console.error('Global Error:', event.error);
    
    const { logError } = useErrorTracking();
    logError(event.error, {
      type: 'global',
      filename: event.filename,
      lineno: event.lineno,
      colno: event.colno
    });
  });
  
  console.log('✅ Global error handlers setup');
}
