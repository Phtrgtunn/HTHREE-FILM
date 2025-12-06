/**
 * Centralized error message handler
 * Provides user-friendly error messages with helpful guidance
 */

export const ERROR_MESSAGES = {
  // Network errors
  NETWORK_ERROR: 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối Internet và thử lại.',
  TIMEOUT_ERROR: 'Yêu cầu quá thời gian chờ. Vui lòng thử lại.',
  
  // Authentication errors
  AUTH_FAILED: 'Đăng nhập thất bại. Vui lòng kiểm tra lại tên đăng nhập và mật khẩu.',
  AUTH_EXPIRED: 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.',
  AUTH_REQUIRED: 'Vui lòng đăng nhập để tiếp tục.',
  
  // Validation errors
  INVALID_EMAIL: 'Email không hợp lệ. Vui lòng nhập đúng định dạng (ví dụ: ten@domain.com).',
  INVALID_PHONE: 'Số điện thoại không hợp lệ. Vui lòng nhập đúng định dạng (ví dụ: 0901234567).',
  WEAK_PASSWORD: 'Mật khẩu quá yếu. Vui lòng sử dụng ít nhất 8 ký tự, bao gồm chữ và số.',
  REQUIRED_FIELD: 'Trường này không được để trống.',
  
  // Payment errors
  PAYMENT_FAILED: 'Thanh toán thất bại. Vui lòng kiểm tra thông tin thanh toán và thử lại.',
  INSUFFICIENT_FUNDS: 'Số dư không đủ. Vui lòng nạp thêm tiền hoặc chọn phương thức thanh toán khác.',
  INVALID_COUPON: 'Mã giảm giá không hợp lệ hoặc đã hết hạn. Vui lòng kiểm tra lại.',
  
  // Cart errors
  CART_EMPTY: 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.',
  ITEM_OUT_OF_STOCK: 'Sản phẩm đã hết hàng. Vui lòng chọn sản phẩm khác.',
  
  // Server errors
  SERVER_ERROR: 'Lỗi máy chủ. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.',
  NOT_FOUND: 'Không tìm thấy dữ liệu. Vui lòng kiểm tra lại.',
  FORBIDDEN: 'Bạn không có quyền thực hiện hành động này.',
  
  // Generic
  UNKNOWN_ERROR: 'Đã xảy ra lỗi không xác định. Vui lòng thử lại hoặc liên hệ hỗ trợ.'
};

export const HELP_LINKS = {
  SUPPORT: '/support',
  FAQ: '/faq',
  CONTACT: '/contact'
};

/**
 * Get user-friendly error message with helpful guidance
 * @param {Error|string} error - Error object or error code
 * @param {Object} options - Additional options
 * @returns {Object} - { message, helpText, helpLink }
 */
export function getErrorMessage(error, options = {}) {
  const { includeHelp = true, includeLink = false } = options;
  
  let message = ERROR_MESSAGES.UNKNOWN_ERROR;
  let helpText = '';
  let helpLink = null;
  
  // Handle different error types
  if (typeof error === 'string') {
    message = ERROR_MESSAGES[error] || error;
  } else if (error?.response) {
    // Axios error
    const status = error.response.status;
    const data = error.response.data;
    
    if (status === 401) {
      message = ERROR_MESSAGES.AUTH_EXPIRED;
      helpText = 'Vui lòng đăng nhập lại để tiếp tục.';
    } else if (status === 403) {
      message = ERROR_MESSAGES.FORBIDDEN;
      helpText = 'Liên hệ quản trị viên nếu bạn cần quyền truy cập.';
    } else if (status === 404) {
      message = ERROR_MESSAGES.NOT_FOUND;
    } else if (status >= 500) {
      message = ERROR_MESSAGES.SERVER_ERROR;
      helpText = 'Chúng tôi đang khắc phục sự cố. Vui lòng thử lại sau.';
      if (includeLink) {
        helpLink = HELP_LINKS.SUPPORT;
      }
    } else if (data?.message) {
      message = data.message;
    }
  } else if (error?.message) {
    if (error.message.includes('Network Error')) {
      message = ERROR_MESSAGES.NETWORK_ERROR;
      helpText = 'Kiểm tra kết nối WiFi/4G và thử lại.';
    } else if (error.message.includes('timeout')) {
      message = ERROR_MESSAGES.TIMEOUT_ERROR;
      helpText = 'Kết nối chậm. Vui lòng thử lại.';
    } else {
      message = error.message;
    }
  }
  
  return {
    message,
    helpText: includeHelp ? helpText : '',
    helpLink: includeLink ? helpLink : null,
    fullMessage: helpText ? `${message} ${helpText}` : message
  };
}

/**
 * Format validation error for display
 * @param {string} field - Field name
 * @param {string} rule - Validation rule that failed
 * @returns {string} - User-friendly error message
 */
export function getValidationError(field, rule) {
  const fieldLabels = {
    email: 'Email',
    password: 'Mật khẩu',
    phone: 'Số điện thoại',
    name: 'Họ tên',
    address: 'Địa chỉ'
  };
  
  const fieldLabel = fieldLabels[field] || field;
  
  const ruleMessages = {
    required: `${fieldLabel} không được để trống`,
    email: ERROR_MESSAGES.INVALID_EMAIL,
    phone: ERROR_MESSAGES.INVALID_PHONE,
    minLength: `${fieldLabel} quá ngắn`,
    maxLength: `${fieldLabel} quá dài`,
    pattern: `${fieldLabel} không đúng định dạng`
  };
  
  return ruleMessages[rule] || `${fieldLabel} không hợp lệ`;
}
