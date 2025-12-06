<?php
/**
 * Database Configuration for Hostinger
 * 
 * HƯỚNG DẪN:
 * 1. Đổi tên file này thành database.php sau khi upload lên Hostinger
 * 2. Thay đổi các thông tin database bên dưới theo thông tin Hostinger của bạn
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    
    public function __construct() {
        // Hostinger database credentials
        // Thay đổi các giá trị này theo database Hostinger của bạn
        $this->host = 'localhost'; // Hoặc IP của MySQL server
        $this->db_name = 'u123456789_hthree'; // Tên database trên Hostinger
        $this->username = 'u123456789_admin'; // Username database
        $this->password = 'YOUR_PASSWORD_HERE'; // Password database
    }
    
    /**
     * Get database connection (PDO)
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo json_encode([
                'status' => false,
                'error' => 'Database connection failed: ' . $e->getMessage()
            ]);
            exit();
        }
        
        return $this->conn;
    }
}

/**
 * Helper function to get MySQLi connection
 */
function getDBConnection() {
    // Hostinger database credentials
    $host = 'localhost';
    $db_name = 'u123456789_hthree'; // Tên database
    $username = 'u123456789_admin'; // Username
    $password = 'YOUR_PASSWORD_HERE'; // Password
    $port = 3306;
    
    $conn = new mysqli($host, $username, $password, $db_name, $port);
    
    if ($conn->connect_error) {
        die(json_encode([
            'success' => false,
            'error' => 'Database connection failed: ' . $conn->connect_error
        ]));
    }
    
    $conn->set_charset('utf8mb4');
    return $conn;
}
