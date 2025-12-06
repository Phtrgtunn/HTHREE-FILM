<?php
/**
 * Database Configuration
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    
    public function __construct() {
        // Railway environment variables (production)
        $this->host = getenv('MYSQL_HOST') ?: 'localhost';
        $this->db_name = getenv('MYSQL_DATABASE') ?: 'hthree_film';
        $this->username = getenv('MYSQL_USER') ?: 'root';
        $this->password = getenv('MYSQL_PASSWORD') ?: 'mysql';
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
    // Railway environment variables (production)
    $host = getenv('MYSQL_HOST') ?: 'localhost';
    $db_name = getenv('MYSQL_DATABASE') ?: 'hthree_film';
    $username = getenv('MYSQL_USER') ?: 'root';
    $password = getenv('MYSQL_PASSWORD') ?: 'mysql';
    $port = getenv('MYSQL_PORT') ?: 3306;
    
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
