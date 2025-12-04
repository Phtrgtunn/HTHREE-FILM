<?php
// Check all user avatars in database
require_once __DIR__ . '/../config/database.php';

$db = getDBConnection();

try {
    echo "=== CHECKING USER AVATARS ===\n\n";
    
    $sql = "SELECT id, firebase_uid, username, full_name, avatar FROM users ORDER BY id DESC LIMIT 10";
    $result = $db->query($sql);
    
    while ($user = $result->fetch_assoc()) {
        echo "User ID: {$user['id']}\n";
        echo "Name: {$user['full_name']}\n";
        echo "Username: {$user['username']}\n";
        echo "Avatar: {$user['avatar']}\n";
        echo "---\n";
    }
    
    echo "\n=== CHECKING RECENT COMMENTS ===\n\n";
    
    $sql = "SELECT c.id, c.content, u.full_name, u.username, u.avatar 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            ORDER BY c.created_at DESC 
            LIMIT 5";
    $result = $db->query($sql);
    
    while ($comment = $result->fetch_assoc()) {
        echo "Comment ID: {$comment['id']}\n";
        echo "User: {$comment['full_name']}\n";
        echo "Content: " . substr($comment['content'], 0, 50) . "...\n";
        echo "Avatar: {$comment['avatar']}\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$db->close();
?>
