<?php
/**
 * Generate VietQR Code
 * Tạo mã QR thanh toán cho đơn hàng
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/config_payment.php';

$db = new Database();
$conn = $db->getConnection();

try {
    // Nhận dữ liệu từ frontend
    $input = json_decode(file_get_contents('php://input'), true);
    
    $orderId = $input['order_id'] ?? null;
    
    if (!$orderId) {
        throw new Exception('Order ID is required');
    }
    
    // Lấy thông tin đơn hàng
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        throw new Exception('Order not found');
    }
    
    // Kiểm tra đơn hàng đã thanh toán chưa
    if ($order['payment_status'] === 'paid') {
        throw new Exception('Order already paid');
    }
    
    // Generate order code nếu chưa có
    if (empty($order['order_code'])) {
        $orderCode = generateOrderCode();
        
        // Update order code
        $stmt = $conn->prepare("UPDATE orders SET order_code = ? WHERE id = ?");
        $stmt->execute([$orderCode, $orderId]);
    } else {
        $orderCode = $order['order_code'];
    }
    
    // Tạo nội dung chuyển khoản
    $transferContent = ORDER_PREFIX . ' ' . $orderCode;
    
    // Generate VietQR URL
    $qrUrl = generateVietQRUrl(
        BANK_ID,
        BANK_ACCOUNT_NO,
        BANK_ACCOUNT_NAME,
        $order['total'],
        $transferContent
    );
    
    // Lưu thông tin QR vào database
    $expiresAt = date('Y-m-d H:i:s', time() + ORDER_TIMEOUT);
    $stmt = $conn->prepare("
        UPDATE orders 
        SET 
            qr_code_url = ?,
            transfer_content = ?,
            expires_at = ?
        WHERE id = ?
    ");
    $stmt->execute([$qrUrl, $transferContent, $expiresAt, $orderId]);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'order_id' => $orderId,
            'order_code' => $orderCode,
            'qr_url' => $qrUrl,
            'amount' => $order['total'],
            'transfer_content' => $transferContent,
            'bank_name' => getBankName(BANK_ID),
            'account_no' => BANK_ACCOUNT_NO,
            'account_name' => BANK_ACCOUNT_NAME,
            'expires_at' => $expiresAt,
            'timeout_seconds' => ORDER_TIMEOUT
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Generate unique order code
 */
function generateOrderCode() {
    $date = date('Ymd');
    $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    return $date . $random;
}

/**
 * Generate VietQR URL
 */
function generateVietQRUrl($bankId, $accountNo, $accountName, $amount, $content) {
    $template = VIETQR_TEMPLATE;
    
    // Encode content (không dấu, viết hoa)
    $content = removeVietnameseTones($content);
    $content = strtoupper($content);
    $content = urlencode($content);
    
    // Encode account name
    $accountName = removeVietnameseTones($accountName);
    $accountName = strtoupper($accountName);
    $accountName = urlencode($accountName);
    
    // Build URL
    $url = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png";
    $url .= "?amount={$amount}";
    $url .= "&addInfo={$content}";
    $url .= "&accountName={$accountName}";
    
    return $url;
}

/**
 * Remove Vietnamese tones
 */
function removeVietnameseTones($str) {
    $vietnameseTones = [
        'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
        'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
        'ì', 'í', 'ị', 'ỉ', 'ĩ',
        'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
        'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
        'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
        'đ',
        'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
        'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
        'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
        'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
        'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
        'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
        'Đ'
    ];
    
    $replacements = [
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y',
        'd',
        'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
        'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
        'I', 'I', 'I', 'I', 'I',
        'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
        'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
        'Y', 'Y', 'Y', 'Y', 'Y',
        'D'
    ];
    
    return str_replace($vietnameseTones, $replacements, $str);
}

/**
 * Get bank name by ID
 */
function getBankName($bankId) {
    $banks = [
        '970415' => 'Vietinbank',
        '970422' => 'MB Bank',
        '970436' => 'Vietcombank',
        '970418' => 'BIDV',
        '970405' => 'Agribank',
        '970407' => 'Techcombank',
        '970432' => 'VPBank',
        '970423' => 'TPBank',
        '970403' => 'Sacombank',
        '970416' => 'ACB'
    ];
    
    return $banks[$bankId] ?? 'Unknown Bank';
}
