/**
 * E-Commerce API Service
 * Gọi các API liên quan đến giỏ hàng, đơn hàng, gói xem phim
 */

import axios from 'axios';
import { apiCache } from '@/utils/apiCache';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost/HTHREE_film/backend/api';

// ============================================
// SUBSCRIPTION PLANS API
// ============================================

/**
 * Lấy danh sách gói xem phim
 */
export const getPlans = async (activeOnly = true) => {
  try {
    const url = `${API_BASE_URL}/plans.php?active_only=${activeOnly}`;
    // Cache plans for 10 minutes
    return await apiCache.get(url, async () => {
      const response = await axios.get(url);
      return response.data;
    }, 10 * 60 * 1000);
  } catch (error) {
    console.error('Error fetching plans:', error);
    throw error;
  }
};

/**
 * Lấy chi tiết 1 gói
 */
export const getPlanBySlug = async (slug) => {
  try {
    const response = await axios.get(`${API_BASE_URL}/plans.php`, {
      params: { slug }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching plan:', error);
    throw error;
  }
};

// ============================================
// CART API
// ============================================

/**
 * Lấy giỏ hàng
 */
export const getCart = async (userId) => {
  try {
    const response = await axios.get(`${API_BASE_URL}/cart.php`, {
      params: { user_id: userId }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching cart:', error);
    throw error;
  }
};

/**
 * Thêm gói vào giỏ
 */
export const addToCart = async (userId, planId, quantity = 1, durationMonths = 1) => {
  try {
    const response = await axios.post(`${API_BASE_URL}/cart.php`, {
      user_id: userId,
      plan_id: planId,
      quantity,
      duration_months: durationMonths
    });
    return response.data;
  } catch (error) {
    console.error('Error adding to cart:', error);
    throw error;
  }
};

/**
 * Cập nhật số lượng trong giỏ
 */
export const updateCartItem = async (cartId, quantity) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/cart.php`, {
      cart_id: cartId,
      quantity
    });
    return response.data;
  } catch (error) {
    console.error('Error updating cart:', error);
    throw error;
  }
};

/**
 * Xóa item khỏi giỏ
 */
export const removeFromCart = async (cartId) => {
  try {
    const response = await axios.delete(`${API_BASE_URL}/cart.php`, {
      params: { cart_id: cartId }
    });
    return response.data;
  } catch (error) {
    console.error('Error removing from cart:', error);
    throw error;
  }
};

/**
 * Xóa toàn bộ giỏ hàng
 */
export const clearCart = async (userId) => {
  try {
    const response = await axios.delete(`${API_BASE_URL}/cart.php`, {
      params: { user_id: userId }
    });
    return response.data;
  } catch (error) {
    console.error('Error clearing cart:', error);
    throw error;
  }
};

// ============================================
// ORDERS API
// ============================================

/**
 * Tạo đơn hàng
 */
export const createOrder = async (orderData) => {
  try {
    const response = await axios.post(`${API_BASE_URL}/orders.php`, orderData);
    return response.data;
  } catch (error) {
    console.error('Error creating order:', error);
    throw error;
  }
};

/**
 * Lấy danh sách đơn hàng của user
 */
export const getUserOrders = async (userId, limit = 20, offset = 0) => {
  try {
    const response = await axios.get(`${API_BASE_URL}/orders.php`, {
      params: { user_id: userId, limit, offset }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching orders:', error);
    throw error;
  }
};

/**
 * Lấy chi tiết đơn hàng
 */
export const getOrderDetail = async (orderId) => {
  try {
    const response = await axios.get(`${API_BASE_URL}/orders.php`, {
      params: { order_id: orderId }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching order detail:', error);
    throw error;
  }
};

/**
 * Lấy đơn hàng theo mã
 */
export const getOrderByCode = async (orderCode) => {
  try {
    const response = await axios.get(`${API_BASE_URL}/orders.php`, {
      params: { order_code: orderCode }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching order:', error);
    throw error;
  }
};

/**
 * Cập nhật trạng thái đơn hàng
 */
export const updateOrderStatus = async (orderId, status, paymentStatus = null) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/orders.php`, {
      order_id: orderId,
      status,
      payment_status: paymentStatus
    });
    return response.data;
  } catch (error) {
    console.error('Error updating order:', error);
    throw error;
  }
};

// ============================================
// SUBSCRIPTIONS API
// ============================================

/**
 * Lấy gói đang dùng của user
 */
export const getUserSubscriptions = async (userId) => {
  try {
    const response = await axios.get(`${API_BASE_URL}/subscriptions.php`, {
      params: { user_id: userId }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching subscriptions:', error);
    throw error;
  }
};

/**
 * Kiểm tra user có gói active không
 */
export const checkActiveSubscription = async (userId) => {
  try {
    const response = await axios.get(`${API_BASE_URL}/subscriptions.php`, {
      params: { user_id: userId, check_active: true }
    });
    return response.data;
  } catch (error) {
    console.error('Error checking subscription:', error);
    throw error;
  }
};

// ============================================
// COUPONS API
// ============================================

/**
 * Lấy danh sách mã giảm giá
 */
export const getCoupons = async (activeOnly = true) => {
  try {
    const response = await axios.get(`${API_BASE_URL}/coupons.php`, {
      params: { active_only: activeOnly }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching coupons:', error);
    throw error;
  }
};

/**
 * Kiểm tra & áp dụng mã giảm giá
 */
export const validateCoupon = async (code, orderValue, userId = null) => {
  try {
    const response = await axios.post(`${API_BASE_URL}/coupons.php`, {
      code,
      order_value: orderValue,
      user_id: userId
    });
    return response.data;
  } catch (error) {
    console.error('Error validating coupon:', error);
    throw error;
  }
};

export default {
  // Plans
  getPlans,
  getPlanBySlug,
  
  // Cart
  getCart,
  addToCart,
  updateCartItem,
  removeFromCart,
  clearCart,
  
  // Orders
  createOrder,
  getUserOrders,
  getOrderDetail,
  getOrderByCode,
  updateOrderStatus,
  
  // Subscriptions
  getUserSubscriptions,
  checkActiveSubscription,
  
  // Coupons
  getCoupons,
  validateCoupon
};
