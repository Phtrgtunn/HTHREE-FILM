<?php
/**
 * Test Email Configuration
 * Chạy file này để kiểm tra cấu hình email có hoạt động không
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/email.php';

echo "<h2>🧪 Test Gửi Email</h2>";
echo "<p>Đang kiểm tra cấu hình email...</p>";

try {
    $emailService = new EmailService();
    
    // Thay email này bằng email bạn muốn test
    $testEmail = 'hient7182@gmail.com';
    $testCode = '123456';
    
    echo "<p>📧 Đang gửi email test đến: <strong>$testEmail</strong></p>";
    
    $result = $emailService->sendPasswordResetCode($testEmail, $testCode);
    
    if ($result) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ <strong>THÀNH CÔNG!</strong><br>";
        echo "Email đã được gửi thành công đến $testEmail<br>";
        echo "Vui lòng kiểm tra hộp thư (kể cả Spam)<br>";
        echo "Mã test: <strong>$testCode</strong>";
        echo "</div>";
        
        echo "<h3>✅ Cấu hình email hoạt động tốt!</h3>";
        echo "<p>Bạn có thể sử dụng tính năng đổi mật khẩu bình thường.</p>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ <strong>THẤT BẠI!</strong><br>";
        echo "Không thể gửi email.<br>";
        echo "</div>";
        
        echo "<h3>❌ Có vấn đề với cấu hình email</h3>";
        echo "<p><strong>Các bước kiểm tra:</strong></p>";
        echo "<ol>";
        echo "<li>Kiểm tra file <code>config/email.php</code></li>";
        echo "<li>Đảm bảo đã bật 2FA cho Gmail: <a href='https://myaccount.google.com/security' target='_blank'>https://myaccount.google.com/security</a></li>";
        echo "<li>Tạo App Password mới: <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a></li>";
        echo "<li>Cập nhật Username và Password trong file email.php</li>";
        echo "<li>Kiểm tra kết nối internet</li>";
        echo "</ol>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>LỖI:</strong><br>";
    echo $e->getMessage();
    echo "</div>";
    
    echo "<h3>🔧 Hướng dẫn sửa lỗi:</h3>";
    
    $errorMsg = $e->getMessage();
    
    if (strpos($errorMsg, 'SMTP connect()') !== false) {
        echo "<p><strong>Lỗi kết nối SMTP</strong></p>";
        echo "<ul>";
        echo "<li>Kiểm tra kết nối internet</li>";
        echo "<li>Tắt Firewall/Antivirus tạm thời</li>";
        echo "<li>Đảm bảo port 587 không bị chặn</li>";
        echo "</ul>";
    } elseif (strpos($errorMsg, 'Invalid credentials') !== false || strpos($errorMsg, 'Username and Password') !== false) {
        echo "<p><strong>Lỗi xác thực</strong></p>";
        echo "<ul>";
        echo "<li>Kiểm tra lại Username (email Gmail)</li>";
        echo "<li>Kiểm tra lại Password (phải là App Password, không phải mật khẩu Gmail thường)</li>";
        echo "<li>Tạo lại App Password mới tại: <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a></li>";
        echo "<li>Xóa hết dấu cách trong App Password</li>";
        echo "</ul>";
    } else {
        echo "<p>Lỗi không xác định. Vui lòng kiểm tra:</p>";
        echo "<ul>";
        echo "<li>File <code>config/email.php</code> có đúng cấu hình không</li>";
        echo "<li>PHPMailer đã được cài đặt chưa (chạy <code>composer install</code>)</li>";
        echo "<li>Gmail có bật 2FA chưa</li>";
        echo "</ul>";
    }
}

echo "<hr>";
echo "<h3>📋 Thông tin cấu hình hiện tại:</h3>";
echo "<p><strong>SMTP Host:</strong> smtp.gmail.com</p>";
echo "<p><strong>SMTP Port:</strong> 587</p>";
echo "<p><strong>Email gửi:</strong> hient7182@gmail.com</p>";
echo "<p><strong>App Password:</strong> ceyt******* (đã ẩn)</p>";

echo "<hr>";
echo "<p><small>File test: backend/test-email.php</small></p>";
?>
