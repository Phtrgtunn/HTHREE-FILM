/**
 * Pinia Store: Quản lý giỏ hàng
 */

import { defineStore } from 'pinia';
import { getCart, addToCart, updateCartItem, removeFromCart, clearCart } from '@/services/ecommerceApi';
import { useAuthStore } from './authStore';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    total: 0,
    count: 0,
    loading: false,
    error: null
  }),

  getters: {
    /**
     * Tổng số item trong giỏ
     */
    cartCount: (state) => state.count,

    /**
     * Tổng tiền
     */
    cartTotal: (state) => state.total,

    /**
     * Tổng tiền formatted
     */
    cartTotalFormatted: (state) => {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(state.total);
    },

    /**
     * Kiểm tra giỏ có rỗng không
     */
    isEmpty: (state) => state.items.length === 0
  },

  actions: {
    /**
     * Lấy giỏ hàng từ API
     */
    async fetchCart() {
      const authStore = useAuthStore();
      
      const userId = authStore.user?.id || authStore.user?.uid;
      
      if (!userId) {
        this.items = [];
        this.total = 0;
        this.count = 0;
        return;
      }

      this.loading = true;
      this.error = null;

      try {
        const response = await getCart(userId);
        
        if (response.success) {
          this.items = response.data.items;
          this.total = response.data.total;
          this.count = response.data.count;
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error fetching cart:', error);
      } finally {
        this.loading = false;
      }
    },

    /**
     * Thêm gói vào giỏ
     */
    async addItem(planId, quantity = 1, durationMonths = 1) {
      const authStore = useAuthStore();
      
      const userId = authStore.user?.id || authStore.user?.uid;
      
      if (!userId) {
        throw new Error('Vui lòng đăng nhập để thêm vào giỏ hàng');
      }

      this.loading = true;
      this.error = null;

      try {
        console.log('Adding to cart:', { userId, planId, quantity, durationMonths });
        const response = await addToCart(userId, planId, quantity, durationMonths);
        
        if (response.success) {
          // Refresh giỏ hàng
          await this.fetchCart();
          return response;
        }
      } catch (error) {
        console.error('Error adding to cart:', error);
        this.error = error.message;
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Cập nhật số lượng
     */
    async updateQuantity(cartId, quantity) {
      this.loading = true;
      this.error = null;

      try {
        const response = await updateCartItem(cartId, quantity);
        
        if (response.success) {
          // Refresh giỏ hàng
          await this.fetchCart();
          return response;
        }
      } catch (error) {
        this.error = error.message;
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Xóa item khỏi giỏ
     */
    async removeItem(cartId) {
      this.loading = true;
      this.error = null;

      try {
        const response = await removeFromCart(cartId);
        
        if (response.success) {
          // Refresh giỏ hàng
          await this.fetchCart();
          return response;
        }
      } catch (error) {
        this.error = error.message;
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Xóa toàn bộ giỏ
     */
    async clear() {
      const authStore = useAuthStore();
      
      const userId = authStore.user?.id || authStore.user?.uid;
      
      if (!userId) {
        return;
      }

      this.loading = true;
      this.error = null;

      try {
        const response = await clearCart(userId);
        
        if (response.success) {
          this.items = [];
          this.total = 0;
          this.count = 0;
        }
      } catch (error) {
        this.error = error.message;
        throw error;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Kiểm tra plan đã có trong giỏ chưa
     */
    hasItem(planId) {
      return this.items.some(item => item.plan_id === planId);
    },

    /**
     * Lấy item theo plan_id
     */
    getItem(planId) {
      return this.items.find(item => item.plan_id === planId);
    }
  }
});
