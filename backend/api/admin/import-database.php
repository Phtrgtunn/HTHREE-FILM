<?php
/**
 * Import database endpoint
 * Access: https://your-backend.railway.app/api/admin/import-database.php?secret=YOUR_SECRET
 */

header('Content-Type: application/json');

// Simple security - check secret parameter
$secret = $_GET['secret'] ?? '';
if ($secret !== 'import-hthree-2024') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Railway MySQL connection
$host = getenv('MYSQL_HOST') ?: 'localhost';
$port = getenv('MYSQL_PORT') ?: 3306;
$database = getenv('MYSQL_DATABASE') ?: 'railway';
$username = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: '';

$log = [];
$log[] = "Connecting to Railway MySQL...";
$log[] = "Host: {$host}:{$port}";
$log[] = "Database: {$database}";

// Connect
try {
    $conn = new mysqli($host, $username, $password, $database, $port);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $log[] = "✅ Connected successfully!";
    
    // Read SQL file
    $sqlFile = __DIR__ . '/../../localhost.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("File not found: {$sqlFile}");
    }
    
    $log[] = "Reading SQL file...";
    $sql = file_get_contents($sqlFile);
    
    if (!$sql) {
        throw new Exception("Failed to read SQL file");
    }
    
    $log[] = "SQL file loaded (" . strlen($sql) . " bytes)";
    
    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    
    // Split queries
    $queries = array_filter(
        array_map('trim', explode(';', $sql)),
        function($query) {
            return !empty($query);
        }
    );
    
    $log[] = "Executing " . count($queries) . " queries...";
    
    $success = 0;
    $failed = 0;
    $errors = [];
    
    foreach ($queries as $index => $query) {
        if (empty($query)) continue;
        
        if ($conn->query($query) === TRUE) {
            $success++;
        } else {
            $failed++;
            $errors[] = [
                'query' => substr($query, 0, 100) . '...',
                'error' => $conn->error
            ];
        }
    }
    
    $log[] = "✅ Import completed!";
    $log[] = "Success: {$success} queries";
    $log[] = "Failed: {$failed} queries";
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'log' => $log,
        'stats' => [
            'total' => count($queries),
            'success' => $success,
            'failed' => $failed
        ],
        'errors' => $errors
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'log' => $log
    ], JSON_PRETTY_PRINT);
}
