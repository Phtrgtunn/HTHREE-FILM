import { onMounted, onUnmounted } from 'vue';

/**
 * Composable for keyboard shortcuts
 * Provides a centralized way to handle keyboard shortcuts
 */
export function useKeyboardShortcuts(shortcuts = {}) {
  const handleKeyDown = (event) => {
    // Build key combination string
    const keys = [];
    if (event.ctrlKey || event.metaKey) keys.push('ctrl');
    if (event.shiftKey) keys.push('shift');
    if (event.altKey) keys.push('alt');
    keys.push(event.key.toLowerCase());
    
    const combination = keys.join('+');
    
    // Check if this combination has a handler
    const handler = shortcuts[combination];
    if (handler) {
      // Prevent default browser behavior
      event.preventDefault();
      event.stopPropagation();
      
      // Execute handler
      handler(event);
    }
  };

  onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
  });

  onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
  });

  return {
    handleKeyDown
  };
}

/**
 * Global keyboard shortcuts
 * These work across the entire app
 */
export const GLOBAL_SHORTCUTS = {
  'ctrl+k': 'Focus search',
  'escape': 'Close modal/dialog',
  '/': 'Quick search',
  'ctrl+z': 'Undo last action',
  'ctrl+shift+z': 'Redo last action',
  'ctrl+s': 'Save (where applicable)',
  'ctrl+f': 'Find in page',
  '?': 'Show keyboard shortcuts help'
};

/**
 * Page-specific shortcuts
 */
export const PAGE_SHORTCUTS = {
  library: {
    'f': 'Toggle favorites filter',
    'w': 'Toggle watchlist filter',
    'c': 'Toggle continue watching filter',
    'delete': 'Remove selected item'
  },
  movieDetail: {
    'space': 'Play/Pause video',
    'f': 'Toggle fullscreen',
    'm': 'Toggle mute',
    'arrowleft': 'Rewind 10s',
    'arrowright': 'Forward 10s'
  },
  cart: {
    'ctrl+enter': 'Proceed to checkout',
    'delete': 'Remove selected item'
  }
};

/**
 * Check if user is typing in an input
 */
export function isTyping() {
  const activeElement = document.activeElement;
  const tagName = activeElement?.tagName.toLowerCase();
  return ['input', 'textarea', 'select'].includes(tagName) || 
         activeElement?.isContentEditable;
}

/**
 * Format shortcut for display
 */
export function formatShortcut(shortcut) {
  return shortcut
    .split('+')
    .map(key => {
      const keyMap = {
        'ctrl': '⌘',
        'shift': '⇧',
        'alt': '⌥',
        'escape': 'Esc',
        'arrowleft': '←',
        'arrowright': '→',
        'arrowup': '↑',
        'arrowdown': '↓'
      };
      return keyMap[key.toLowerCase()] || key.toUpperCase();
    })
    .join(' + ');
}
