import axios from 'axios';
import { API_CONFIG } from '@/config/api';

const API_URL = API_CONFIG.BACKEND_URL;

/**
 * Payment Service
 * Xử lý thanh toán qua các cổng khác nhau
 */

class PaymentService {
  /**
   * Tạo payment URL cho VNPay
   */
  async createVNPayPayment(orderData) {
    try {
      const response = await axios.post(`${API_URL}/payment/vnpay_create.php`, orderData);
      return response.data;
    } catch (error) {
      console.error('VNPay payment error:', error);
      throw error;
    }
  }

  /**
   * Tạo payment URL cho MoMo
   */
  async createMoMoPayment(orderData) {
    try {
      const response = await axios.post(`${API_URL}/payment/momo_create.php`, orderData);
      return response.data;
    } catch (error) {
      console.error('MoMo payment error:', error);
      throw error;
    }
  }

  /**
   * Tạo payment URL cho ZaloPay
   */
  async createZaloPayPayment(orderData) {
    try {
      const response = await axios.post(`${API_URL}/payment/zalopay_create.php`, orderData);
      return response.data;
    } catch (error) {
      console.error('ZaloPay payment error:', error);
      throw error;
    }
  }

  /**
   * Bank Transfer - Tạo thông tin chuyển khoản
   */
  async createBankTransfer(orderData) {
    try {
      const response = await axios.post(`${API_URL}/payment/bank_transfer.php`, orderData);
      return response.data;
    } catch (error) {
      console.error('Bank transfer error:', error);
      throw error;
    }
  }

  /**
   * Verify payment callback
   */
  async verifyPayment(paymentData) {
    try {
      const response = await axios.post(`${API_URL}/payment/verify.php`, paymentData);
      return response.data;
    } catch (error) {
      console.error('Payment verification error:', error);
      throw error;
    }
  }

  /**
   * Generate QR Code cho chuyển khoản
   */
  generateBankQRCode(bankInfo) {
    // Sử dụng VietQR API
    const { bank, accountNo, accountName, amount, description } = bankInfo;
    
    // Format: https://img.vietqr.io/image/{BANK}-{ACCOUNT_NO}-{TEMPLATE}.png?amount={AMOUNT}&addInfo={DESCRIPTION}
    const qrUrl = `https://img.vietqr.io/image/${bank}-${accountNo}-compact2.jpg?amount=${amount}&addInfo=${encodeURIComponent(description)}&accountName=${encodeURIComponent(accountName)}`;
    
    return qrUrl;
  }
}

export default new PaymentService();
