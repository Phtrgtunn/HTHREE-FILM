<?php
/**
 * Change Password API - Simple Version (không cần Composer)
 * Sử dụng mail() function của PHP
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/email_simple.php';

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

// Kết nối database
$conn = getDBConnection();

if ($action === 'send_code') {
    // Gửi mã xác thực
    $email = $data['email'] ?? '';
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email không được để trống']);
        exit();
    }
    
    // Kiểm tra email có tồn tại không
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Email không tồn tại trong hệ thống']);
        exit();
    }
    
    // Tạo mã xác thực 6 số
    $token = sprintf("%06d", mt_rand(0, 999999));
    $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    // Xóa các mã cũ chưa sử dụng của email này
    $stmt = $conn->prepare("DELETE FROM password_reset_tokens WHERE email = ? AND used = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Lưu mã mới
    $stmt = $conn->prepare("INSERT INTO password_reset_tokens (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $token, $expires_at);
    
    if ($stmt->execute()) {
        // Gửi email
        $emailService = new SimpleEmailService();
        $emailSent = $emailService->sendPasswordResetCode($email, $token);
        
        if ($emailSent) {
            echo json_encode([
                'success' => true, 
                'message' => 'Mã xác thực đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư (kể cả thư mục Spam).'
            ]);
        } else {
            // Nếu không gửi được email, hiển thị mã để test
            echo json_encode([
                'success' => true, 
                'message' => 'Không thể gửi email. Mã xác thực của bạn là: ' . $token . ' (có hiệu lực 15 phút)',
                'token' => $token // Chỉ để test
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi tạo mã xác thực']);
    }
    
} elseif ($action === 'verify_and_change') {
    // Xác thực mã và đổi mật khẩu
    $email = $data['email'] ?? '';
    $token = $data['token'] ?? '';
    $newPassword = $data['newPassword'] ?? '';
    
    if (empty($email) || empty($token) || empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
        exit();
    }
    
    // Kiểm tra mã xác thực
    $stmt = $conn->prepare("SELECT id FROM password_reset_tokens WHERE email = ? AND token = ? AND used = 0 AND expires_at > NOW()");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Mã xác thực không hợp lệ hoặc đã hết hạn']);
        exit();
    }
    
    $tokenData = $result->fetch_assoc();
    
    // Hash mật khẩu mới
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    
    // Cập nhật mật khẩu
    $stmt = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    
    if ($stmt->execute()) {
        // Đánh dấu mã đã sử dụng
        $stmt = $conn->prepare("UPDATE password_reset_tokens SET used = 1 WHERE id = ?");
        $stmt->bind_param("i", $tokenData['id']);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công! Bạn có thể đăng nhập với mật khẩu mới.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật mật khẩu']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
}

$conn->close();
?>
