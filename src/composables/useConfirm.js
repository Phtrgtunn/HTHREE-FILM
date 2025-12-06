import { ref } from 'vue';

const isOpen = ref(false);
const confirmData = ref({
  title: '',
  message: '',
  confirmText: 'Xác nhận',
  cancelText: 'Hủy',
  type: 'warning', // 'warning', 'danger', 'info'
  onConfirm: null,
  onCancel: null
});

export function useConfirm() {
  const confirm = ({
    title = 'Xác nhận',
    message = 'Bạn có chắc chắn muốn thực hiện hành động này?',
    confirmText = 'Xác nhận',
    cancelText = 'Hủy',
    type = 'warning'
  }) => {
    return new Promise((resolve) => {
      confirmData.value = {
        title,
        message,
        confirmText,
        cancelText,
        type,
        onConfirm: () => {
          isOpen.value = false;
          resolve(true);
        },
        onCancel: () => {
          isOpen.value = false;
          resolve(false);
        }
      };
      isOpen.value = true;
    });
  };

  const close = () => {
    isOpen.value = false;
  };

  return {
    isOpen,
    confirmData,
    confirm,
    close
  };
}
