import { ref } from 'vue';

export function useUndo() {
  const showUndo = ref(false);
  const undoMessage = ref('');
  const undoCallback = ref(null);

  const triggerUndo = (message, callback) => {
    undoMessage.value = message;
    undoCallback.value = callback;
    showUndo.value = true;
  };

  const handleUndo = () => {
    if (undoCallback.value) {
      undoCallback.value();
    }
    closeUndo();
  };

  const closeUndo = () => {
    showUndo.value = false;
    undoMessage.value = '';
    undoCallback.value = null;
  };

  return {
    showUndo,
    undoMessage,
    triggerUndo,
    handleUndo,
    closeUndo
  };
}
