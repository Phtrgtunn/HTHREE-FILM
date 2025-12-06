import { ref, computed } from 'vue';

/**
 * Composable for bulk actions (select multiple items)
 * Useful for power users to perform batch operations
 */
export function useBulkActions() {
  const selectedItems = ref(new Set());
  const isSelectionMode = ref(false);

  // Toggle selection mode
  const toggleSelectionMode = () => {
    isSelectionMode.value = !isSelectionMode.value;
    if (!isSelectionMode.value) {
      clearSelection();
    }
  };

  // Toggle item selection
  const toggleItem = (itemId) => {
    if (selectedItems.value.has(itemId)) {
      selectedItems.value.delete(itemId);
    } else {
      selectedItems.value.add(itemId);
    }
    // Trigger reactivity
    selectedItems.value = new Set(selectedItems.value);
  };

  // Select all items
  const selectAll = (items) => {
    items.forEach(item => {
      selectedItems.value.add(item.id || item);
    });
    selectedItems.value = new Set(selectedItems.value);
  };

  // Deselect all items
  const clearSelection = () => {
    selectedItems.value.clear();
    selectedItems.value = new Set();
  };

  // Check if item is selected
  const isSelected = (itemId) => {
    return selectedItems.value.has(itemId);
  };

  // Get selected count
  const selectedCount = computed(() => selectedItems.value.size);

  // Get selected items as array
  const selectedArray = computed(() => Array.from(selectedItems.value));

  // Has any selection
  const hasSelection = computed(() => selectedItems.value.size > 0);

  return {
    selectedItems,
    isSelectionMode,
    selectedCount,
    selectedArray,
    hasSelection,
    toggleSelectionMode,
    toggleItem,
    selectAll,
    clearSelection,
    isSelected
  };
}
