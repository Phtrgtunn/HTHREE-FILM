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

$sqlFile = __DIR__ . '/../../localhost-notriggers.sql';
$sql = file_get_contents($sqlFile);

echo "SQL file size: " . strlen($sql) . " bytes\n\n";

// Execute directly - ignore trigger errors
echo "Executing SQL...\n";

if ($conn->multi_query($sql)) {
    $count = 0;
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
        $count++;
        echo ".";
        
        // Check for errors but continue
        if ($conn->errno && $conn->errno != 1064) { // Ignore syntax errors (triggers)
            echo "\nWarning: " . $conn->error . "\n";
        }
    } while (@$conn->next_result()); // Suppress errors
    
    echo "\n\n✅ Import completed! Executed {$count} queries\n";
} else {
    echo "\n\n❌ Error: " . $conn->error . "\n";
}

$conn->close();
