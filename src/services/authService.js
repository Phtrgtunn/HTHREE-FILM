import axios from 'axios';
import { API_CONFIG } from '@/config/api';

const API_URL = API_CONFIG.BACKEND_URL;

/**
 * Auth Service
 * Quản lý đăng nhập, đăng ký với PHP backend
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
        // Lưu user vào localStorage
        localStorage.setItem('user', JSON.stringify(response.data.user));
        localStorage.setItem('token', response.data.token);
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
  logout() {
    localStorage.removeItem('user');
    localStorage.removeItem('token');
  }

  /**
   * Lấy user hiện tại
   */
  getCurrentUser() {
    const userStr = localStorage.getItem('user');
    if (userStr) {
      return JSON.parse(userStr);
    }
    return null;
  }

  /**
   * Kiểm tra đã đăng nhập chưa
   */
  isLoggedIn() {
    return !!localStorage.getItem('token');
  }

  /**
   * Lấy token
   */
  getToken() {
    return localStorage.getItem('token');
  }
}

export default new AuthService();
