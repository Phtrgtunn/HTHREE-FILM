<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $user_id = $data['user_id'] ?? null;
    $full_name = $data['full_name'] ?? null;
    $phone = $data['phone'] ?? null;
    $bio = $data['bio'] ?? null;
    
    if (!$user_id) {
        throw new Exception('User ID is required');
    }
    
    if (!$full_name) {
        throw new Exception('Full name is required');
    }
    
    // Validate phone if provided
    if ($phone && !preg_match('/^[0-9]{10,11}$/', $phone)) {
        throw new Exception('Invalid phone number format');
    }
    
    // Update user profile
    $sql = "UPDATE users SET full_name = ?, phone = ?, bio = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $full_name, $phone, $bio, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update profile');
    }
    
    // Get updated user data including avatar
    $stmt = $conn->prepare("SELECT id, username, email, full_name, phone, bio, avatar, role, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Ensure avatar is included in response
    if (!$user['avatar']) {
        $user['avatar'] = null;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'user' => $user
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
