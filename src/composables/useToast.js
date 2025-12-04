// Composable for using custom toast
let toastInstance = null;

export function setToastInstance(instance) {
  toastInstance = instance;
}

export function useToast() {
  return {
    success: (message, duration = 3000) => {
      if (toastInstance) {
        toastInstance.success(message, duration);
      }
    },
    error: (message, duration = 3000) => {
      if (toastInstance) {
        toastInstance.error(message, duration);
      }
    },
    warning: (message, duration = 3000) => {
      if (toastInstance) {
        toastInstance.warning(message, duration);
      }
    },
    info: (message, duration = 3000) => {
      if (toastInstance) {
        toastInstance.info(message, duration);
      }
    }
  };
}
