<?php
/**
 * Check Railway database structure
 */

// Railway MySQL credentials
$host = 'crossover.proxy.rlwy.net';
$port = 29616;
$database = 'railway';
$username = 'root';
$password = 'MMpMAyYHqpuxsFyqDJfcUiwWCMkdjnvr';

echo "🔍 Checking Railway database structure...\n";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected successfully!\n\n";
    
    // Check tables
    $tables = ['users', 'subscription_plans', 'coupons', 'orders', 'user_subscriptions'];
    
    foreach ($tables as $table) {
        echo "📋 Table: $table\n";
        try {
            $result = $pdo->query("DESCRIBE $table");
            while ($row = $result->fetch()) {
                echo "  - {$row['Field']} ({$row['Type']}) {$row['Null']} {$row['Key']}\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>