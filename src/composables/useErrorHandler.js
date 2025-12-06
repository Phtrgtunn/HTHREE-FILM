import { ref } from 'vue';
import { useToast } from './useToast';
import { getErrorMessage } from '@/utils/errorMessages';

/**
 * Composable for centralized error handling
 * Provides consistent error messages and logging
 */
export function useErrorHandler() {
  const toast = useToast();
  const lastError = ref(null);
  const errorLog = ref([]);

  /**
   * Handle error with user-friendly message
   * @param {Error|string} error - Error to handle
   * @param {Object} options - Options for error handling
   */
  const handleError = (error, options = {}) => {
    const {
      showToast = true,
      logError = true,
      context = '',
      fallbackMessage = null
    } = options;

    // Get formatted error message
    const errorInfo = getErrorMessage(error, {
      includeHelp: true,
      includeLink: false
    });

    // Use fallback message if provided
    const displayMessage = fallbackMessage || errorInfo.fullMessage;

    // Log error
    if (logError) {
      const logEntry = {
        timestamp: new Date(),
        context,
        error: error?.message || error,
        stack: error?.stack,
        displayMessage
      };
      errorLog.value.push(logEntry);
      console.error(`[Error Handler] ${context}:`, error);
    }

    // Store last error
    lastError.value = {
      error,
      message: displayMessage,
      context,
      timestamp: new Date()
    };

    // Show toast notification
    if (showToast) {
      toast.error(displayMessage);
    }

    return errorInfo;
  };

  /**
   * Handle async operation with error handling
   * @param {Function} operation - Async operation to execute
   * @param {Object} options - Error handling options
   */
  const withErrorHandling = async (operation, options = {}) => {
    try {
      return await operation();
    } catch (error) {
      handleError(error, options);
      throw error; // Re-throw for caller to handle if needed
    }
  };

  /**
   * Clear error log
   */
  const clearErrorLog = () => {
    errorLog.value = [];
    lastError.value = null;
  };

  /**
   * Get error log for debugging
   */
  const getErrorLog = () => {
    return errorLog.value;
  };

  return {
    lastError,
    errorLog,
    handleError,
    withErrorHandling,
    clearErrorLog,
    getErrorLog
  };
}
