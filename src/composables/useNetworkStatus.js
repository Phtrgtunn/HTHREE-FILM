import { ref, onMounted, onUnmounted } from 'vue';

/**
 * Composable for monitoring network status
 * Shows offline banner and handles reconnection
 */
export function useNetworkStatus() {
  const isOnline = ref(navigator.onLine);
  const showOfflineBanner = ref(false);

  const handleOnline = () => {
    isOnline.value = true;
    showOfflineBanner.value = false;
    console.log('🌐 Network: Online');
  };

  const handleOffline = () => {
    isOnline.value = false;
    showOfflineBanner.value = true;
    console.log('📡 Network: Offline');
  };

  onMounted(() => {
    window.addEventListener('online', handleOnline);
    window.addEventListener('offline', handleOffline);
  });

  onUnmounted(() => {
    window.removeEventListener('online', handleOnline);
    window.removeEventListener('offline', handleOffline);
  });

  return {
    isOnline,
    showOfflineBanner
  };
}
