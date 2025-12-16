<?php
/**
 * Create User in Database
 * Tạo user trong database khi đăng ký Firebase
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

$db = new Database();
$conn = $db->getConnection();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $uid = $input['uid'] ?? null;
    $email = $input['email'] ?? null;
    $displayName = $input['displayName'] ?? null;
    
    if (!$uid || !$email) {
        throw new Exception('UID and email are required');
    }
    
    // Kiểm tra user đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM users WHERE firebase_uid = ? OR email = ?");
    $stmt->execute([$uid, $email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingUser) {
        // User đã tồn tại, trả về ID
        echo json_encode([
            'success' => true,
            'message' => 'User already exists',
            'user_id' => $existingUser['id'],
            'exists' => true
        ]);
        return;
    }
    
    // Tạo user mới
    $stmt = $conn->prepare("
        INSERT INTO users (firebase_uid, email, full_name, created_at, updated_at)
        VALUES (?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->execute([$uid, $email, $displayName]);
    $userId = $conn->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'User created successfully',
        'user_id' => $userId,
        'exists' => false
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}