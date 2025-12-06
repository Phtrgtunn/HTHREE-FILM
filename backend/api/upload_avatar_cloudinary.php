<?php
/**
 * Upload Avatar to Cloudinary
 * Replaces local file upload with cloud storage
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

try {
    // Cloudinary configuration
    // Lấy từ environment variables hoặc hardcode (tạm thời)
    $cloudName = getenv('CLOUDINARY_CLOUD_NAME') ?: 'YOUR_CLOUD_NAME';
    $apiKey = getenv('CLOUDINARY_API_KEY') ?: 'YOUR_API_KEY';
    $apiSecret = getenv('CLOUDINARY_API_SECRET') ?: 'YOUR_API_SECRET';
    
    Configuration::instance([
        'cloud' => [
            'cloud_name' => $cloudName,
            'api_key' => $apiKey,
            'api_secret' => $apiSecret
        ],
        'url' => [
            'secure' => true
        ]
    ]);
    
    // Get user_id from request
    $userId = $_POST['user_id'] ?? null;
    
    if (!$userId) {
        throw new Exception('User ID is required');
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error');
    }
    
    $file = $_FILES['avatar'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, and WEBP are allowed');
    }
    
    // Validate file size (max 2MB)
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 2MB limit');
    }
    
    // Upload to Cloudinary
    $uploadApi = new UploadApi();
    $result = $uploadApi->upload($file['tmp_name'], [
        'folder' => 'hthree_avatars',
        'public_id' => 'user_' . $userId,
        'overwrite' => true,
        'transformation' => [
            'width' => 400,
            'height' => 400,
            'crop' => 'fill',
            'gravity' => 'face',
            'quality' => 'auto'
        ]
    ]);
    
    // Get secure URL
    $avatarUrl = $result['secure_url'];
    
    // Update database
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$avatarUrl, $userId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Avatar uploaded successfully',
        'avatar_url' => $avatarUrl
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
