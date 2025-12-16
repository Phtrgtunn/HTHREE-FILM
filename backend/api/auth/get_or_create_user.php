<?php
/**
 * Simple User Lookup - Chỉ dùng bảng users
 * Tìm user theo email trong bảng users có sẵn
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
    
    $email = $input['email'] ?? null;
    
    if (!$email) {
        throw new Exception('Email is required');
    }
    
    // Tìm user theo email trong bảng users
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Tìm thấy user
        echo json_encode([
            'success' => true,
            'message' => 'User found',
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'exists' => true
        ]);
    } else {
        // Không tìm thấy user
        echo json_encode([
            'success' => false,
            'message' => 'User not found with email: ' . $email,
            'user_id' => null,
            'exists' => false
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}