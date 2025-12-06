import axios from 'axios';
import { API_CONFIG } from '@/config/api';
import persistentStorage from '@/utils/persistentStorage';

const API_URL = API_CONFIG.BACKEND_URL;

/**
 * Auth Service
 * Quản lý đăng nhập, đăng ký với PHP backend
 * Sử dụng PersistentStorage để tránh mất data khi reload
 */

class AuthService {
  /**
   * Đăng ký tài khoản mới
   */
  async register(username, email, password, fullName) {
    try {
      const response = await axios.post(`${API_URL}/auth/register.php`, {
        username,
        email,
        password,
        full_name: fullName
      });
      
      return response.data;
    } catch (error) {
      console.error('Register error:', error);
      throw error;
    }
  }

  /**
   * Đăng nhập
   */
  async login(username, password) {
    try {
      const response = await axios.post(`${API_URL}/auth/login.php`, {
        username,
        password
      });
      
      if (response.data.status) {
        // Lưu user vào PERSISTENT storage (IndexedDB + LocalStorage + SessionStorage + Cookie)
        await persistentStorage.setItem('user', JSON.stringify(response.data.user));
        await persistentStorage.setItem('token', response.data.token);
        
        console.log('✅ Auth data saved to persistent storage');
      }
      
      return response.data;
    } catch (error) {
      console.error('Login error:', error);
      throw error;
    }
  }

  /**
   * Đăng xuất
   */
  async logout() {
    await persistentStorage.removeItem('user');
    await persistentStorage.removeItem('token');
    console.log('✅ Auth data removed from all storages');
  }

  /**
   * Lấy user hiện tại
   */
  async getCurrentUser() {
    const userStr = await persistentStorage.getItem('user');
    if (userStr) {
      try {
        return JSON.parse(userStr);
      } catch (e) {
        console.error('Error parsing user data:', e);
        return null;
      }
    }
    return null;
  }

  /**
   * Lấy user hiện tại (synchronous - fallback to localStorage only)
   */
  getCurrentUserSync() {
    try {
      const userStr = localStorage.getItem('user');
      if (userStr) {
        return JSON.parse(userStr);
      }
    } catch (e) {
      console.error('Error getting user sync:', e);
    }
    return null;
  }

  /**
   * Kiểm tra đã đăng nhập chưa
   */
  async isLoggedIn() {
    const token = await persistentStorage.getItem('token');
    return !!token;
  }

  /**
   * Lấy token
   */
  async getToken() {
    return await persistentStorage.getItem('token');
  }

  /**
   * Lấy token (synchronous - fallback to localStorage only)
   */
  getTokenSync() {
    return localStorage.getItem('token');
  }
}

export default new AuthService();
