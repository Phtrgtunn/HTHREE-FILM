<?php
header('Content-Type: application/json');

$host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: 'localhost';
$port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: 3306;
$database = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'hthree_film';
$username = 'root';
$password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: 'tuan1412';

try {
    $conn = new mysqli($host, $username, $password, $database, $port);
    
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    echo json_encode([
        'success' => true,
        'connection' => [
            'host' => $host,
            'port' => $port,
            'database' => $database
        ],
        'tables' => $tables
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
