import { ref, computed } from 'vue';
import { getAuth } from 'firebase/auth';

/**
 * Composable để quản lý authentication và authorization
 */
export function useAuth() {
  const auth = getAuth();
  const currentUser = computed(() => auth.currentUser);

  /**
   * Kiểm tra xem user có phải admin không
   */
  const isAdmin = () => {
    try {
      const storedUser = localStorage.getItem('user');
      if (!storedUser) return false;

      const userData = JSON.parse(storedUser);
      
      // Kiểm tra role trong localStorage
      if (userData.role === 'admin') {
        return true;
      }

      // Kiểm tra email admin (backup)
      const adminEmails = [
        'hient7182@gmail.com',
        'admin@hthree.com'
      ];
      
      return adminEmails.includes(userData.email);
    } catch (error) {
      console.error('Error checking admin status:', error);
      return false;
    }
  };

  /**
   * Kiểm tra xem user có subscription active không
   */
  const hasActiveSubscription = async () => {
    try {
      const storedUser = localStorage.getItem('user');
      if (!storedUser) return false;

      const userData = JSON.parse(storedUser);
      const userId = userData.id;

      const API_URL = import.meta.env.VITE_API_BASE_URL || '