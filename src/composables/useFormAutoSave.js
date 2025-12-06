import { ref, watch, onUnmounted } from 'vue';

/**
 * Composable for auto-saving form data to localStorage
 * Helps prevent data loss on page refresh or navigation
 */
export function useFormAutoSave(formKey, formData, options = {}) {
  const {
    debounceMs = 2000, // Auto-save every 2 seconds
    enabled = true
  } = options;

  const lastSaved = ref(null);
  const isSaving = ref(false);
  let saveTimeout = null;

  // Storage key
  const storageKey = `form_draft_${formKey}`;

  // Save to localStorage
  const saveToStorage = () => {
    if (!enabled) return;
    
    isSaving.value = true;
    try {
      const dataToSave = {
        data: JSON.parse(JSON.stringify(formData)),
        timestamp: Date.now()
      };
      localStorage.setItem(storageKey, JSON.stringify(dataToSave));
      lastSaved.value = new Date();
      console.log(`✅ Auto-saved form: ${formKey}`);
    } catch (error) {
      console.error('Failed to auto-save form:', error);
    } finally {
      isSaving.value = false;
    }
  };

  // Debounced save
  const debouncedSave = () => {
    if (saveTimeout) {
      clearTimeout(saveTimeout);
    }
    saveTimeout = setTimeout(saveToStorage, debounceMs);
  };

  // Load from localStorage
  const loadFromStorage = () => {
    try {
      const saved = localStorage.getItem(storageKey);
      if (saved) {
        const { data, timestamp } = JSON.parse(saved);
        
        // Check if saved data is not too old (24 hours)
        const age = Date.now() - timestamp;
        const maxAge = 24 * 60 * 60 * 1000; // 24 hours
        
        if (age < maxAge) {
          console.log(`📥 Restored form draft: ${formKey}`);
          return data;
        } else {
          // Remove old draft
          clearStorage();
        }
      }
    } catch (error) {
      console.error('Failed to load form draft:', error);
    }
    return null;
  };

  // Clear storage
  const clearStorage = () => {
    localStorage.removeItem(storageKey);
    lastSaved.value = null;
    console.log(`🗑️ Cleared form draft: ${formKey}`);
  };

  // Watch form data changes
  if (enabled) {
    watch(
      () => formData,
      () => {
        debouncedSave();
      },
      { deep: true }
    );
  }

  // Cleanup on unmount
  onUnmounted(() => {
    if (saveTimeout) {
      clearTimeout(saveTimeout);
    }
  });

  return {
    lastSaved,
    isSaving,
    saveToStorage,
    loadFromStorage,
    clearStorage
  };
}
