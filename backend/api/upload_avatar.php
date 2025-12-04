<?php
// CORS headers - PHẢI đặt trước bất kỳ output nào
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once '../config/database.php';

$db = getDBConnection();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $firebase_uid = $data['firebase_uid'] ?? null;
    $photo_url = $data['photo_url'] ?? null;
    
    if (!$firebase_uid || !$photo_url) {
        throw new Exception('Missing required fields');
    }
    
    // Download image from URL
    $image_data = @file_get_contents($photo_url);
    
    if ($image_data === false) {
        throw new Exception('Failed to download image');
    }
    
    // Create avatars directory if not exists
    $upload_dir = __DIR__ . '/../uploads/avatars/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate filename
    $filename = $firebase_uid . '_' . time() . '.jpg';
    $filepath = $upload_dir . $filename;
    
    // Save image
    if (file_put_contents($filepath, $image_data) === false) {
        throw new Exception('Failed to save image');
    }
    
    // Update database with mysqli
    $avatar_url = 'http://localhost/HTHREE_film/backend/uploads/avatars/' . $filename;
    $stmt = $db->prepare("UPDATE users SET avatar = ? WHERE firebase_uid = ?");
    $stmt->bind_param('ss', $avatar_url, $firebase_uid);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update database');
    }
    
    echo json_encode([
        'success' => true,
        'avatar_url' => $avatar_url,
        'message' => 'Avatar uploaded and saved to database'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$db->close();
