<?php
/**
 * Sync Firebase User to MySQL
 * Tự động tạo/cập nhật user trong MySQL khi đăng nhập Google
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

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $firebase_uid = $data['firebase_uid'] ?? null;
    $email = $data['email'] ?? null;
    $username = $data['username'] ?? null;
    $full_name = $data['full_name'] ?? null;
    $avatar = $data['avatar'] ?? null;
    
    if (!$firebase_uid || !$email) {
        throw new Exception('Firebase UID and email are required');
    }
    
    $conn = getDBConnection();
    
    // Kiểm tra user đã tồn tại chưa (theo email hoặc firebase_uid)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR firebase_uid = ?");
    $stmt->bind_param("ss", $email, $firebase_uid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User đã tồn tại - Cập nhật thông tin
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        
        $stmt = $conn->prepare("
            UPDATE users 
            SET firebase_uid = ?, 
                full_name = COALESCE(?, full_name),
                avatar = COALESCE(?, avatar),
                last_login = NOW(),
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("sssi", $firebase_uid, $full_name, $avatar, $user_id);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'User updated',
            'user_id' => $user_id,
            'is_new' => false
        ]);
    } else {
        // User chưa tồn tại - Tạo mới
        // Tạo username unique nếu chưa có
        if (!$username) {
            $username = explode('@', $email)[0];
        }
        
        // Kiểm tra username đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            // Thêm số random vào username
            $username = $username . rand(1000, 9999);
        }
        
        // Tạo password random (không dùng vì đăng nhập bằng Firebase)
        $random_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("
            INSERT INTO users (
                username, email, firebase_uid, password, full_name, avatar,
                is_active, last_login, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW(), NOW())
        ");
        $stmt->bind_param("ssssss", $username, $email, $firebase_uid, $random_password, $full_name, $avatar);
        $stmt->execute();
        $user_id = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'User created',
            'user_id' => $user_id,
            'is_new' => true
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
