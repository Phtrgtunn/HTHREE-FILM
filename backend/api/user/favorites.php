<?php
/**
 * Favorites API
 * Quản lý phim yêu thích
 */

require_once '../../config/config.php';
require_once '../../config/cors.php';
require_once '../../config/database.php';

handleCORS();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    // GET - Lấy danh sách yêu thích
    if ($method === 'GET') {
        $user_id = $_GET['user_id'] ?? null;
        
        if (!$user_id) {
            echo json_encode(['status' => false, 'message' => 'User ID required']);
            exit;
        }
        
        $stmt = $db->prepare("
            SELECT * FROM favorites 
            WHERE user_id = ? 
            ORDER BY added_at DESC
        ");
        $stmt->execute([$user_id]);
        $favorites = $stmt->fetchAll();
        
        echo json_encode([
            'status' => true,
            'data' => $favorites
        ]);
    }
    
    // POST - Thêm vào yêu thích
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $user_id = $data['user_id'] ?? null;
        $movie_slug = $data['movie_slug'] ?? '';
        $movie_name = $data['movie_name'] ?? '';
        $movie_poster = $data['movie_poster'] ?? '';
        $movie_year = $data['movie_year'] ?? null;
        $movie_quality = $data['movie_quality'] ?? '';
        
        if (!$user_id || !$movie_slug) {
            echo json_encode(['status' => false, 'message' => 'Missing required fields']);
            exit;
        }
        
        // Check if already exists
        $stmt = $db->prepare("
            SELECT id FROM favorites 
            WHERE user_id = ? AND movie_slug = ?
        ");
        $stmt->execute([$user_id, $movie_slug]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'status' => false,
                'message' => 'Phim đã có trong danh sách yêu thích'
            ]);
            exit;
        }
        
        // Insert
        $stmt = $db->prepare("
            INSERT INTO favorites 
            (user_id, movie_slug, movie_name, movie_poster, movie_year, movie_quality) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $movie_slug, $movie_name, $movie_poster, $movie_year, $movie_quality]);
        
        echo json_encode([
            'status' => true,
            'message' => 'Đã thêm vào yêu thích'
        ]);
    }
    
    // DELETE - Xóa khỏi yêu thích
    elseif ($method === 'DELETE') {
        $user_id = $_GET['user_id'] ?? null;
        $movie_slug = $_GET['movie_slug'] ?? null;
        
        if (!$user_id || !$movie_slug) {
            echo json_encode(['status' => false, 'message' => 'Missing required fields']);
            exit;
        }
        
        $stmt = $db->prepare("
            DELETE FROM favorites 
            WHERE user_id = ? AND movie_slug = ?
        ");
        $stmt->execute([$user_id, $movie_slug]);
        
        echo json_encode([
            'status' => true,
            'message' => 'Đã xóa khỏi yêu thích'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
