<?php
/**
 * Watch History API
 * Lưu và lấy lịch sử xem phim
 */

require_once '../../config/config.php';
require_once '../../config/cors.php';
require_once '../../config/database.php';

handleCORS();

session_start();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    // GET - Lấy lịch sử xem
    if ($method === 'GET') {
        $user_id = $_GET['user_id'] ?? null;
        $limit = $_GET['limit'] ?? 20;
        
        if (!$user_id) {
            echo json_encode(['status' => false, 'message' => 'User ID required']);
            exit;
        }
        
        $stmt = $db->prepare("
            SELECT * FROM watch_history 
            WHERE user_id = ? 
            ORDER BY watched_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$user_id, (int)$limit]);
        $history = $stmt->fetchAll();
        
        echo json_encode([
            'status' => true,
            'data' => $history
        ]);
    }
    
    // POST - Thêm lịch sử xem
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $user_id = $data['user_id'] ?? null;
        $movie_slug = $data['movie_slug'] ?? '';
        $movie_name = $data['movie_name'] ?? '';
        $movie_poster = $data['movie_poster'] ?? '';
        $episode = $data['episode'] ?? null;
        $watch_time = $data['watch_time'] ?? 0;
        $duration = $data['duration'] ?? 0;
        
        if (!$user_id || !$movie_slug) {
            echo json_encode(['status' => false, 'message' => 'Missing required fields']);
            exit;
        }
        
        // Check if exists, update or insert
        $stmt = $db->prepare("
            SELECT id FROM watch_history 
            WHERE user_id = ? AND movie_slug = ? AND episode = ?
        ");
        $stmt->execute([$user_id, $movie_slug, $episode]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update
            $stmt = $db->prepare("
                UPDATE watch_history 
                SET watch_time = ?, duration = ?, watched_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$watch_time, $duration, $existing['id']]);
        } else {
            // Insert
            $stmt = $db->prepare("
                INSERT INTO watch_history 
                (user_id, movie_slug, movie_name, movie_poster, episode, watch_time, duration) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $movie_slug, $movie_name, $movie_poster, $episode, $watch_time, $duration]);
        }
        
        echo json_encode([
            'status' => true,
            'message' => 'Đã lưu lịch sử xem'
        ]);
    }
    
    // DELETE - Xóa lịch sử
    elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['status' => false, 'message' => 'ID required']);
            exit;
        }
        
        $stmt = $db->prepare("DELETE FROM watch_history WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'status' => true,
            'message' => 'Đã xóa lịch sử'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
