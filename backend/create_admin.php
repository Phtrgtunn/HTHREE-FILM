<?php
/**
 * Script tạo tài khoản Admin
 * Chạy file này 1 lần để tạo tài khoản admin
 * Sau đó XÓA file này để bảo mật
 */

require_once __DIR__ . '/config/database.php';

// Thông tin admin
$adminEmail = 'admin@hthree.com';
$adminPassword = 'Admin@123456'; // Đổi mật khẩu này
$adminName = 'Administrator';
$adminUsername = 'admin';

try {
    $conn = getDBConnection();
    
    // Hash password
    $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);
    
    // Kiểm tra xem admin đã tồn tại chưa
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $adminEmail);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // Cập nhật role thành admin
        $updateStmt = $conn->prepare("UPDATE users SET role = 'admin', password = ?, is_active = 1 WHERE email = ?");
        $updateStmt->bind_param("ss", $hashedPassword, $adminEmail);
        $updateStmt->execute();
        echo "✅ Đã cập nhật tài khoản admin!\n";
    } else {
        // Tạo tài khoản mới
        $insertStmt = $conn->prepare("
            INSERT INTO users (email, password, full_name, username, role, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'admin', 1, NOW(), NOW())
        ");
        $insertStmt->bind_param("ssss", $adminEmail, $hashedPassword, $adminName, $adminUsername);
        $insertStmt->execute();
        echo "✅ Đã tạo tài khoản admin mới!\n";
    }
    
    echo "\n📧 Email: $adminEmail\n";
    echo "🔑 Password: $adminPassword\n";
    echo "\n⚠️  LƯU Ý: Hãy XÓA file này sau khi tạo xong để bảo mật!\n";
    echo "⚠️  Đổi mật khẩu ngay sau khi đăng nhập lần đầu!\n";
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
}
?>
