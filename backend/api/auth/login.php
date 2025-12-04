<?php
/**
 * Login API
 * Đăng nhập
 */

require_once '../../config/config.php';
require_once '../../config/cors.php';
require_once '../../config/database.php';

handleCORS();

// Start session
session_start();

try {
    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);
    
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    // Validate
    if (empty($username) || empty($password)) {
        echo json_encode([
            'status' => false,
            'message' => 'Vui lòng điền đầy đủ thông tin'
        ]);
        exit;
    }
    
    // Connect database
    $database = new Database();
    $db = $database->getConnection();
    
    // Get user
    $stmt = $db->prepare("
        SELECT id, username, email, password, full_name, avatar, created_at 
        FROM users 
        WHERE username = ? OR email = ?
    ");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode([
            'status' => false,
            'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
        ]);
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode([
            'status' => false,
            'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
        ]);
        exit;
    }
    
    // Update last login
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    
    // Generate token (simple version)
    $token = base64_encode($user['id'] . ':' . time());
    
    // Return user data (without password)
    unset($user['password']);
    
    echo json_encode([
        'status' => true,
        'message' => 'Đăng nhập thành công',
        'token' => $token,
        'user' => $user
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
