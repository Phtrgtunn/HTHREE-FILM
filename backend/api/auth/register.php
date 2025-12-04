<?php
/**
 * Register API
 * Đăng ký tài khoản mới
 */

require_once '../../config/config.php';
require_once '../../config/cors.php';
require_once '../../config/database.php';

handleCORS();

try {
    // Get POST data
    $data = json_decode(file_get_contents("php://input"), true);
    
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $full_name = $data['full_name'] ?? '';
    
    // Validate
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode([
            'status' => false,
            'message' => 'Vui lòng điền đầy đủ thông tin'
        ]);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode([
            'status' => false,
            'message' => 'Mật khẩu phải có ít nhất 6 ký tự'
        ]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => false,
            'message' => 'Email không hợp lệ'
        ]);
        exit;
    }
    
    // Connect database
    $database = new Database();
    $db = $database->getConnection();
    
    // Check username exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode([
            'status' => false,
            'message' => 'Tên đăng nhập đã tồn tại'
        ]);
        exit;
    }
    
    // Check email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode([
            'status' => false,
            'message' => 'Email đã được sử dụng'
        ]);
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $db->prepare("
        INSERT INTO users (username, email, password, full_name) 
        VALUES (?, ?, ?, ?)
    ");
    
    if ($stmt->execute([$username, $email, $hashed_password, $full_name])) {
        $user_id = $db->lastInsertId();
        
        echo json_encode([
            'status' => true,
            'message' => 'Đăng ký thành công',
            'user' => [
                'id' => $user_id,
                'username' => $username,
                'email' => $email,
                'full_name' => $full_name
            ]
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Đăng ký thất bại'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
