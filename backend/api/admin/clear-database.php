<?php
/**
 * Clear all tables in database
 */

header('Content-Type: text/plain');

$secret = $_GET['secret'] ?? '';
if ($secret !== 'import-hthree-2024') {
    http_response_code(403);
    die('Forbidden');
}

$host = getenv('MYSQLHOST') ?: 'localhost';
$port = getenv('MYSQLPORT') ?: 3306;
$database = getenv('MYSQLDATABASE') ?: 'railway';
$username = 'root';
$password = getenv('MYSQLPASSWORD') ?: '';

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to database: {$database}\n\n";

// Get all tables
$result = $conn->query("SHOW TABLES");
$tables = [];

while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

echo "Found " . count($tables) . " tables\n\n";

if (count($tables) > 0) {
    // Disable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    
    foreach ($tables as $table) {
        echo "Dropping table: {$table}... ";
        if ($conn->query("DROP TABLE `{$table}`")) {
            echo "✅\n";
        } else {
            echo "❌ " . $conn->error . "\n";
        }
    }
    
    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n✅ All tables dropped!\n";
} else {
    echo "No tables to drop.\n";
}

$conn->close();
