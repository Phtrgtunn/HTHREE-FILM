<?php
/**
 * Database Configuration
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'hthree_film';
    private $username = 'root';
    private $password = 'mysql'; // AMPPS default password
    private $conn;
    
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
    $host = 'localhost';
    $db_name = 'hthree_film';
    $username = 'root';
    $password = 'mysql';
    
    $conn = new mysqli($host, $username, $password, $db_name);
    
    if ($conn->connect_error) {
        die(json_encode([
            'success' => false,
            'error' => 'Database connection failed: ' . $conn->connect_error
        ]));
    }
    
    $conn->set_charset('utf8mb4');
    return $conn;
}
