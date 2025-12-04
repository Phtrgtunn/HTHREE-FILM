<?php
// backend/api/community.php

// CORS headers - PHẢI đặt trước bất kỳ output nào
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
    // Top commenters
    $topCommenters = [];
    $sql = "SELECT u.id, u.username, u.full_name, u.avatar, u.email,
            COUNT(c.id) as comment_count,
            SUM(c.likes) as total_likes
            FROM users u
            JOIN comments c ON u.id = c.user_id
            GROUP BY u.id
            ORDER BY comment_count DESC, total_likes DESC
            LIMIT 5";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        if (empty($row['avatar']) || strpos($row['avatar'], 'ui-avatars.com') !== false) {
            $name = urlencode($row['full_name'] ?? $row['username']);
            $row['avatar'] = "https://ui-avatars.com/api/?name={$name}&background=f59e0b&color=000&size=128";
        }
        $topCommenters[] = $row;
    }
    
    // Most commented movies (trending)
    $trendingMovies = [];
    $sql = "SELECT movie_slug, COUNT(*) as comment_count
            FROM comments
            WHERE movie_slug IS NOT NULL
            GROUP BY movie_slug
            ORDER BY comment_count DESC
            LIMIT 5";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $trendingMovies[] = $row;
    }
    
    // Recent comments
    $recentComments = [];
    $sql = "SELECT c.*, u.username, u.full_name, u.avatar, u.email
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.parent_id IS NULL
            ORDER BY c.created_at DESC
            LIMIT 5";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        if (empty($row['avatar']) || strpos($row['avatar'], 'ui-avatars.com') !== false) {
            $name = urlencode($row['full_name'] ?? $row['username']);
            $row['avatar'] = "https://ui-avatars.com/api/?name={$name}&background=f59e0b&color=000&size=128";
        }
        $row['time_ago'] = timeAgo($row['created_at']);
        $recentComments[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'top_commenters' => $topCommenters,
            'trending_movies' => $trendingMovies,
            'recent_comments' => $recentComments
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$db->close();

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
