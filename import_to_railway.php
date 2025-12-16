<?php
/**
 * Import data to Railway MySQL
 * Run this script locally to import data to Railway
 */

// Railway connection info (get from Railway Variables tab)
$railway_host = getenv('MYSQLHOST') ?: 'autorack.proxy.rlwy.net';
$railway_port = getenv('MYSQLPORT') ?: 47470;
$railway_db = getenv('MYSQLDATABASE') ?: 'railway';
$railway_user = getenv('MYSQLUSER') ?: 'root';
$railway_pass = getenv('MYSQLPASSWORD') ?: 'PASTE_YOUR_PASSWORD_HERE';

echo "🚀 Starting Railway import...\n";

try {
    // Connect to Railway
    $pdo = new PDO(
        "mysql:host=$railway_host;port=$railway_port;dbname=$railway_db;charset=utf8mb4", 
        $railway_user, 
        $railway_pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to Railway MySQL\n";
    
    // Read and execute SQL file
    $sql = file_get_contents('railway_import.sql');
    
    // Split by semicolon and execute each statement
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "✅ Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (Exception $e) {
                echo "⚠️ Warning: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "🎉 Import completed successfully!\n";
    echo "📝 Check your admin panel: https://hthree-film.vercel.app/admin\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Make sure to:\n";
    echo "   1. Get Railway password from dashboard\n";
    echo "   2. Update \$railway_pass variable\n";
    echo "   3. Check Railway connection details\n";
}
?>