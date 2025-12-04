<?php
// backend/api/comments.php

// CORS headers - PHẢI đặt trước bất kỳ output nào
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400'); // Cache preflight request for 24 hours
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDBConnection();

// GET - Lấy danh sách comments
if ($method === 'GET') {
    $type = $_GET['type'] ?? 'recent'; // recent, top, movie
    $movie_slug = $_GET['movie_slug'] ?? null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    
    try {
        if ($type === 'recent') {
            // Lấy comments mới nhất
            $sql = "SELECT c.*, u.username, u.full_name, u.avatar, u.email
                    FROM comments c
                    JOIN users u ON c.user_id = u.id
                    WHERE c.parent_id IS NULL
                    ORDER BY c.created_at DESC
                    LIMIT ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('i', $limit);
            
        } elseif ($type === 'top') {
            // Lấy comments có nhiều likes nhất
            $sql = "SELECT c.*, u.username, u.full_name, u.avatar, u.email
                    FROM comments c
                    JOIN users u ON c.user_id = u.id
                    WHERE c.parent_id IS NULL
                    ORDER BY c.likes DESC, c.created_at DESC
                    LIMIT ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('i', $limit);
            
        } elseif ($type === 'movie' && $movie_slug) {
            // Lấy comments của một phim cụ thể
            $sql = "SELECT c.*, u.username, u.full_name, u.avatar, u.email
                    FROM comments c
                    JOIN users u ON c.user_id = u.id
                    WHERE c.movie_slug = ? AND c.parent_id IS NULL
                    ORDER BY c.created_at DESC
                    LIMIT ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('si', $movie_slug, $limit);
            
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid type']);
            exit;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = [];
        
        while ($row = $result->fetch_assoc()) {
            // Generate avatar if not exists
            if (empty($row['avatar'])) {
                $name = urlencode($row['full_name'] ?? $row['username']);
                $row['avatar'] = "https://ui-avatars.com/api/?name={$name}&background=f59e0b&color=000";
            }
            
            // Format time ago
            $row['time_ago'] = timeAgo($row['created_at']);
            
            $comments[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'comments' => $comments,
            'total' => count($comments)
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

// POST - Tạo comment mới
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $user_id = $data['user_id'] ?? null;
    $movie_slug = $data['movie_slug'] ?? null;
    $content = $data['content'] ?? null;
    $parent_id = $data['parent_id'] ?? null;
    
    if (!$user_id || !$content) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    try {
        $sql = "INSERT INTO comments (user_id, movie_slug, content, parent_id) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('issi', $user_id, $movie_slug, $content, $parent_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Comment created successfully',
                'comment_id' => $stmt->insert_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create comment']);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

// PUT - Like comment
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $comment_id = $data['comment_id'] ?? null;
    
    if (!$comment_id) {
        echo json_encode(['success' => false, 'message' => 'Missing comment_id']);
        exit;
    }
    
    try {
        $sql = "UPDATE comments SET likes = likes + 1 WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Comment liked']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to like comment']);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

// DELETE - Xóa comment
elseif ($method === 'DELETE') {
    $comment_id = $_GET['id'] ?? null;
    $user_id = $_GET['user_id'] ?? null;
    
    if (!$comment_id || !$user_id) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }
    
    try {
        // Check if user owns the comment
        $sql = "DELETE FROM comments WHERE id = ? AND user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ii', $comment_id, $user_id);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Comment deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete comment or unauthorized']);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

$db->close();

// Helper function
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
    
    return date('d/m/Y', $time);
}
?>
