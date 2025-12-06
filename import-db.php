<?php
/**
 * Script import database vào Railway MySQL
 * Chạy: php import-db.php
 */

// Railway MySQL credentials (lấy từ Railway Variables)
$host = getenv('MYSQLHOST') ?: 'localhost';
$port = getenv('MYSQLPORT') ?: 3306;
$database = getenv('MYSQLDATABASE') ?: 'railway';
$username = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';

echo "🔄 Connecting to MySQL...\n";
echo "Host: $host:$port\n";
echo "Database: $database\n";

// Connect
$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error . "\n");
}

echo "✅ Connected successfully!\n\n";

// Read SQL file
$sqlFile = 'localhost.sql';
if (!file_exists($sqlFile)) {
    die("❌ File not found: $sqlFile\n");
}

echo "📂 Reading $sqlFile...\n";
$sql = file_get_contents($sqlFile);

// Remove CREATE DATABASE and USE statements (Railway đã tạo sẵn)
$sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
$sql = preg_replace('/USE `.*?`;/i', '', $sql);

// Split into individual queries
$queries = array_filter(array_map('trim', explode(';', $sql)));

echo "📊 Found " . count($queries) . " queries\n";
echo "🚀 Importing...\n\n";

$success = 0;
$failed = 0;

foreach ($queries as $i => $query) {
    if (empty($query) || substr($query, 0, 2) === '--') {
        continue;
    }
    
    if ($conn->query($query) === TRUE) {
        $success++;
        if ($success % 10 === 0) {
            echo "✓ Imported $success queries...\n";
        }
    } else {
        $failed++;
        echo "❌ Error in query " . ($i + 1) . ": " . $conn->error . "\n";
        if ($failed > 10) {
            echo "⚠️ Too many errors, stopping...\n";
            break;
        }
    }
}

echo "\n";
echo "✅ Import completed!\n";
echo "   Success: $success queries\n";
echo "   Failed: $failed queries\n";

$conn->close();
?>
