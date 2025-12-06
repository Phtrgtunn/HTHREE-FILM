<?php
/**
 * Simple database import - execute SQL file line by line
 */

header('Content-Type: text/plain; charset=utf-8');

$secret = $_GET['secret'] ?? '';
if ($secret !== 'import-hthree-2024') {
    http_response_code(403);
    die('Forbidden');
}

set_time_limit(300); // 5 minutes

$host = getenv('MYSQLHOST') ?: 'localhost';
$port = getenv('MYSQLPORT') ?: 3306;
$database = getenv('MYSQLDATABASE') ?: 'railway';
$username = 'root';
$password = getenv('MYSQLPASSWORD') ?: '';

echo "Connecting to MySQL...\n";
echo "Host: {$host}:{$port}\n";
echo "Database: {$database}\n\n";

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected!\n\n";

$sqlFile = __DIR__ . '/../../localhost-clean.sql';
$sql = file_get_contents($sqlFile);

echo "SQL file size: " . strlen($sql) . " bytes\n\n";

// Execute directly
echo "Executing SQL...\n";

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
        echo ".";
    } while ($conn->next_result());
    
    echo "\n\n✅ Import completed successfully!\n";
} else {
    echo "\n\n❌ Error: " . $conn->error . "\n";
}

$conn->close();
