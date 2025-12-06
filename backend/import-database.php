<?php
/**
 * Import database from localhost.sql to Railway MySQL
 * Run this once to setup production database
 */

// Railway MySQL connection
$host = getenv('MYSQL_HOST') ?: 'localhost';
$port = getenv('MYSQL_PORT') ?: 3306;
$database = getenv('MYSQL_DATABASE') ?: 'railway';
$username = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: '';

echo "🔄 Connecting to Railway MySQL...\n";
echo "Host: {$host}:{$port}\n";
echo "Database: {$database}\n\n";

// Connect
$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error . "\n");
}

echo "✅ Connected successfully!\n\n";

// Read SQL file
$sqlFile = __DIR__ . '/../localhost.sql';

if (!file_exists($sqlFile)) {
    die("❌ File not found: {$sqlFile}\n");
}

echo "📖 Reading SQL file...\n";
$sql = file_get_contents($sqlFile);

if (!$sql) {
    die("❌ Failed to read SQL file\n");
}

echo "✅ SQL file loaded (" . strlen($sql) . " bytes)\n\n";

// Split into individual queries
echo "🔄 Executing SQL queries...\n";

// Remove comments and split by semicolon
$sql = preg_replace('/--.*$/m', '', $sql); // Remove single-line comments
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Remove multi-line comments

$queries = array_filter(
    array_map('trim', explode(';', $sql)),
    function($query) {
        return !empty($query);
    }
);

$success = 0;
$failed = 0;

foreach ($queries as $index => $query) {
    if (empty($query)) continue;
    
    // Show progress
    if ($index % 10 == 0) {
        echo "Progress: " . ($index + 1) . "/" . count($queries) . "\n";
    }
    
    if ($conn->query($query) === TRUE) {
        $success++;
    } else {
        $failed++;
        echo "⚠️  Query failed: " . substr($query, 0, 100) . "...\n";
        echo "   Error: " . $conn->error . "\n";
    }
}

echo "\n✅ Import completed!\n";
echo "   Success: {$success} queries\n";
echo "   Failed: {$failed} queries\n";

$conn->close();
