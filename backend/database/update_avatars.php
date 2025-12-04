<?php
// Script to update all user avatars from Firebase to local server
require_once __DIR__ . '/../config/database.php';

$db = getDBConnection();

try {
    // Get all users with Firebase photoURL pattern or UI Avatars
    $sql = "SELECT id, firebase_uid, full_name, username, avatar FROM users 
            WHERE avatar LIKE '%ui-avatars.com%' OR avatar LIKE '%googleusercontent.com%'";
    $result = $db->query($sql);
    
    $updated = 0;
    $failed = 0;
    
    while ($user = $result->fetch_assoc()) {
        $name = $user['full_name'] ?? $user['username'] ?? 'User';
        $newAvatar = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=f59e0b&color=000&size=128";
        
        // Update avatar
        $updateSql = "UPDATE users SET avatar = ? WHERE id = ?";
        $stmt = $db->prepare($updateSql);
        $stmt->bind_param('si', $newAvatar, $user['id']);
        
        if ($stmt->execute()) {
            $updated++;
            echo "✅ Updated user {$user['id']}: {$name}\n";
        } else {
            $failed++;
            echo "❌ Failed to update user {$user['id']}\n";
        }
    }
    
    echo "\n📊 Summary:\n";
    echo "Updated: $updated\n";
    echo "Failed: $failed\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$db->close();
?>
