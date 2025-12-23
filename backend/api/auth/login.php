<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://hthree-film.vercel.app');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Direct database connection for Railway
function getDBConnection() {
    $host = getenv('MYSQLHOST') ?: 'localhost';
    $dbname = getenv('MYSQLDATABASE') ?: 'railway';
    $username = getenv('MYSQLUSER') ?: 'root';
    $password = getenv('MYSQLPASSWORD') ?: '';
    $port = getenv('MYSQLPORT') ?: 3306;
    
    $conn = new mysqli($host, $username, $password, $dbname, $port);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    return $conn;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $login = trim($input['login'] ?? ''); // username or email
    $password = $input['password'] ?? '';
    
    // Validation
    if (empty($login) || empty($password)) {
        throw new Exception('Vui lòng nhập tên đăng nhập và mật khẩu');
    }
    
    $conn = getDBConnection();
    
    // Find user by username or email
    $stmt = $conn->prepare("SELECT id, username, email, password, full_name, role, email_verified, email_verified_at, created_at FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param('ss', $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Tài khoản không tồn tại');
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Check if email is verified
    if ($user['email_verified'] == 0) {
        throw new Exception('Vui lòng xác nhận email trước khi đăng nhập. Kiểm tra hộp thư của bạn.');
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        throw new Exception('Mật khẩu không chính xác');
    }
    
    // Update last login
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param('i', $user['id']);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    
    // Remove password from response
    unset($user['password']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập thành công!',
        'data' => [
            'user' => $user,
            'token' => base64_encode(json_encode(['user_id' => $user['id'], 'exp' => time() + 86400])) // 24h
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>