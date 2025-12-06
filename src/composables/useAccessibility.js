import { onMounted, onUnmounted } from 'vue';

/**
 * Composable for keyboard navigation and accessibility
 */
export function useAccessibility() {
  // Trap focus within a modal/dialog
  const trapFocus = (element) => {
    const focusableElements = element.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    
    const firstFocusable = focusableElements[0];
    const lastFocusable = focusableElements[focusableElements.length - 1];

    const handleTabKey = (e) => {
      if (e.key !== 'Tab') return;

      if (e.shiftKey) {
        if (document.activeElement === firstFocusable) {
          lastFocusable.focus();
          e.preventDefault();
        }
      } else {
        if (document.activeElement === lastFocusable) {
          firstFocusable.focus();
          e.preventDefault();
        }
      }
    };

    element.addEventListener('keydown', handleTabKey);
    firstFocusable?.focus();

    return () => {
      element.removeEventListener('keydown', handleTabKey);
    };
  };

  // Handle Escape key
  const onEscape = (callback) => {
    const handleEscape = (e) => {
      if (e.key === 'Escape') {
        callback();
      }
    };

    document.addEventListener('keydown', handleEscape);

    return () => {
      document.removeEventListener('keydown', handleEscape);
    };
  };

  // Keyboard navigation for lists/grids
  const useArrowNavigation = (containerRef, options = {}) => {
    const {
      itemSelector = '[role="button"], button, a',
      orientation = 'horizontal', // 'horizontal' | 'vertical' | 'both'
      loop = true
    } = options;

    const handleArrowKeys = (e) => {
      if (!containerRef.value) return;

      const items = Array.from(containerRef.value.querySelectorAll(itemSelector));
      const currentIndex = items.indexOf(document.activeElement);

      if (currentIndex === -1) return;

      let nextIndex = currentIndex;

      switch (e.key) {
        case 'ArrowRight':
          if (orientation === 'horizontal' || orientation === 'both') {
            nextIndex = currentIndex + 1;
            if (nextIndex >= items.length) {
              nextIndex = loop ? 0 : currentIndex;
            }
            e.preventDefault();
          }
          break;
        case 'ArrowLeft':
          if (orientation === 'horizontal' || orientation === 'both') {
            nextIndex = currentIndex - 1;
            if (nextIndex < 0) {
              nextIndex = loop ? items.length - 1 : currentIndex;
            }
            e.preventDefault();
          }
          break;
        case 'ArrowDown':
          if (orientation === 'vertical' || orientation === 'both') {
            nextIndex = currentIndex + 1;
            if (nextIndex >= items.length) {
              nextIndex = loop ? 0 : currentIndex;
            }
            e.preventDefault();
          }
          break;
        case 'ArrowUp':
          if (orientation === 'vertical' || orientation === 'both') {
            nextIndex = currentIndex - 1;
            if (nextIndex < 0) {
              nextIndex = loop ? items.length - 1 : currentIndex;
            }
            e.preventDefault();
          }
          break;
        case 'Home':
          nextIndex = 0;
          e.preventDefault();
          break;
        case 'End':
          nextIndex = items.length - 1;
          e.preventDefault();
          break;
      }

      if (nextIndex !== currentIndex && items[nextIndex]) {
        items[nextIndex].focus();
      }
    };

    onMounted(() => {
      document.addEventListener('keydown', handleArrowKeys);
    });

    onUnmounted(() => {
      document.removeEventListener('keydown', handleArrowKeys);
    });
  };

  // Announce to screen readers
  const announce = (message, priority = 'polite') => {
    const announcement = document.createElement('div');
    announcement.setAttribute('role', 'status');
    announcement.setAttribute('aria-live', priority);
    announcement.setAttribute('aria-atomic', 'true');
    announcement.className = 'sr-only';
    announcement.textContent = message;

    document.body.appendChild(announcement);

    setTimeout(() => {
      document.body.removeChild(announcement);
    }, 1000);
  };

  // Check if reduced motion is preferred
  const prefersReducedMotion = () => {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  };

  return {
    trapFocus,
    onEscape,
    useArrowNavigation,
    announce,
    prefersReducedMotion
  };
}

// Screen reader only class (add to your CSS)
/*
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}
*/
