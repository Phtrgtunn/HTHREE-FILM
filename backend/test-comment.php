<?php
/**
 * Test Comment API
 * Kiểm tra xem comment có lưu được vào database không
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🧪 TEST COMMENT API</h1>";
echo "<hr>";

// Test 1: Kết nối database
echo "<h2>Test 1: Kết nối Database</h2>";
require_once 'config/database.php';

try {
    $db = getDBConnection();
    echo "✅ Kết nối database thành công!<br>";
    
    // Kiểm tra bảng users
    $result = $db->query("SELECT COUNT(*) as total FROM users");
    $row = $result->fetch_assoc();
    echo "👤 Tổng users: " . $row['total'] . "<br>";
    
    // Kiểm tra bảng comments
    $result = $db->query("SELECT COUNT(*) as total FROM comments");
    $row = $result->fetch_assoc();
    echo "💬 Tổng comments: " . $row['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "<br>";
    exit;
}

echo "<hr>";

// Test 2: Tạo comment mẫu
echo "<h2>Test 2: Tạo Comment Mẫu</h2>";

try {
    // Lấy user đầu tiên
    $result = $db->query("SELECT id, username, email FROM users LIMIT 1");
    $user = $result->fetch_assoc();
    
    if (!$user) {
        echo "❌ Không có user nào trong database!<br>";
        echo "👉 Chạy file: backend/database/schema.sql để tạo users mẫu<br>";
        exit;
    }
    
    echo "👤 Sử dụng user: " . $user['username'] . " (ID: " . $user['id'] . ")<br>";
    
    // Tạo comment test
    $test_content = "Test comment - " . date('Y-m-d H:i:s');
    $test_movie_slug = "test-movie-slug";
    
    $sql = "INSERT INTO comments (user_id, movie_slug, content) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('iss', $user['id'], $test_movie_slug, $test_content);
    
    if ($stmt->execute()) {
        $comment_id = $stmt->insert_id;
        echo "✅ Tạo comment thành công! ID: " . $comment_id . "<br>";
        echo "📝 Nội dung: " . $test_content . "<br>";
        
        // Lấy comment vừa tạo
        $sql = "SELECT c.*, u.username, u.email, u.full_name, u.avatar 
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comment = $result->fetch_assoc();
        
        echo "<pre>";
        print_r($comment);
        echo "</pre>";
        
    } else {
        echo "❌ Không thể tạo comment: " . $stmt->error . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 3: Test API endpoint
echo "<h2>Test 3: Test API Endpoint</h2>";
echo "<p>Gọi API: <code>api/comments.php?type=movie&movie_slug=test-movie-slug</code></p>";

$api_url = "http://localhost/HTHREE_film/backend/api/comments.php?type=movie&movie_slug=test-movie-slug&limit=5";
echo "<p><a href='$api_url' target='_blank'>Click để test API →</a></p>";

echo "<hr>";

// Test 4: Hiển thị tất cả comments
echo "<h2>Test 4: Tất cả Comments</h2>";

try {
    $result = $db->query("SELECT c.*, u.username, u.email, u.full_name 
                          FROM comments c 
                          JOIN users u ON c.user_id = u.id 
                          ORDER BY c.created_at DESC 
                          LIMIT 10");
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>User</th><th>Movie Slug</th><th>Content</th><th>Likes</th><th>Created At</th>";
    echo "</tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "<br><small>" . $row['email'] . "</small></td>";
        echo "<td>" . ($row['movie_slug'] ?: '<i>null</i>') . "</td>";
        echo "<td>" . htmlspecialchars($row['content']) . "</td>";
        echo "<td>" . $row['likes'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "<br>";
}

$db->close();

echo "<hr>";
echo "<h2>✅ Test hoàn tất!</h2>";
echo "<p>Nếu tất cả đều OK, comment form sẽ hoạt động bình thường.</p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #f5f5f5;
}
h1 { color: #333; }
h2 { color: #666; margin-top: 20px; }
code { background: #eee; padding: 2px 6px; border-radius: 3px; }
pre { background: #eee; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>
