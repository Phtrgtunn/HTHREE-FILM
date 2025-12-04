import axios from 'axios';
import { API_CONFIG } from '@/config/api';

const API_URL = `${API_CONFIG.BACKEND_URL}/user`;

/**
 * User Service
 * Quản lý lịch sử xem, yêu thích
 */

class UserService {
  /**
   * Lấy lịch sử xem
   */
  async getWatchHistory(userId, limit = 20) {
    try {
      const response = await axios.get(`${API_URL}/watch-history.php`, {
        params: { user_id: userId, limit }
      });
      return response.data;
    } catch (error) {
      console.error('Get watch history error:', error);
      throw error;
    }
  }

  /**
   * Thêm lịch sử xem
   */
  async addWatchHistory(data) {
    try {
      const response = await axios.post(`${API_URL}/watch-history.php`, data);
      return response.data;
    } catch (error) {
      console.error('Add watch history error:', error);
      throw error;
    }
  }

  /**
   * Xóa lịch sử xem
   */
  async deleteWatchHistory(id) {
    try {
      const response = await axios.delete(`${API_URL}/watch-history.php`, {
        params: { id }
      });
      return response.data;
    } catch (error) {
      console.error('Delete watch history error:', error);
      throw error;
    }
  }

  /**
   * Lấy danh sách yêu thích
   */
  async getFavorites(userId) {
    try {
      const response = await axios.get(`${API_URL}/favorites.php`, {
        params: { user_id: userId }
      });
      return response.data;
    } catch (error) {
      console.error('Get favorites error:', error);
      throw error;
    }
  }

  /**
   * Thêm vào yêu thích
   */
  async addFavorite(data) {
    try {
      const response = await axios.post(`${API_URL}/favorites.php`, data);
      return response.data;
    } catch (error) {
      console.error('Add favorite error:', error);
      throw error;
    }
  }

  /**
   * Xóa khỏi yêu thích
   */
  async removeFavorite(userId, movieSlug) {
    try {
      const response = await axios.delete(`${API_URL}/favorites.php`, {
        params: { user_id: userId, movie_slug: movieSlug }
      });
      return response.data;
    } catch (error) {
      console.error('Remove favorite error:', error);
      throw error;
    }
  }

  /**
   * Kiểm tra phim có trong yêu thích không
   */
  async isFavorite(userId, movieSlug) {
    try {
      const response = await this.getFavorites(userId);
      if (response.status && response.data) {
        return response.data.some(fav => fav.movie_slug === movieSlug);
      }
      return false;
    } catch (error) {
      return false;
    }
  }
}

export default new UserService();
