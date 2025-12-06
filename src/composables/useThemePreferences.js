import { ref, onMounted, watch } from 'vue';

/**
 * Composable để quản lý theme preferences và accessibility settings
 * Hỗ trợ: dark mode, reduced motion, high contrast
 */
export function useThemePreferences() {
  const prefersReducedMotion = ref(false);
  const prefersHighContrast = ref(false);
  const prefersDarkMode = ref(false);

  // Check user preferences
  const checkPreferences = () => {
    // Check reduced motion
    if (window.matchMedia) {
      prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      prefersHighContrast.value = window.matchMedia('(prefers-contrast: high)').matches;
      prefersDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
  };

  // Listen for changes
  const setupListeners = () => {
    if (window.matchMedia) {
      // Reduced motion listener
      const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
      motionQuery.addEventListener('change', (e) => {
        prefersReducedMotion.value = e.matches;
      });

      // High contrast listener
      const contrastQuery = window.matchMedia('(prefers-contrast: high)');
      contrastQuery.addEventListener('change', (e) => {
        prefersHighContrast.value = e.matches;
      });

      // Dark mode listener
      const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
      darkModeQuery.addEventListener('change', (e) => {
        prefersDarkMode.value = e.matches;
      });
    }
  };

  // Get animation duration based on preferences
  const getAnimationDuration = (defaultDuration = 300) => {
    return prefersReducedMotion.value ? 0 : defaultDuration;
  };

  // Get transition class based on preferences
  const getTransitionClass = (defaultClass = 'transition-all duration-300') => {
    return prefersReducedMotion.value ? '' : defaultClass;
  };

  // Check if animations should be enabled
  const shouldAnimate = () => {
    return !prefersReducedMotion.value;
  };

  onMounted(() => {
    checkPreferences();
    setupListeners();
  });

  return {
    prefersReducedMotion,
    prefersHighContrast,
    prefersDarkMode,
    getAnimationDuration,
    getTransitionClass,
    shouldAnimate
  };
}
