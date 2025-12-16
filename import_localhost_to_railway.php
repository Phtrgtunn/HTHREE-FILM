<?php
/**
 * Import localhost database to Railway MySQL
 * This will copy the complete hthree_film database structure and data
 */

// Railway MySQL credentials
$railway_host = 'crossover.proxy.rlwy.net';
$railway_port = 29616;
$railway_db = 'railway';
$railway_user = 'root';
$railway_pass = 'MMpMAyYHqpuxsFyqDJfcUiwWCMkdjnvr';

// Localhost MySQL credentials
$local_host = 'localhost';
$local_db = 'hthree_film';
$local_user = 'root';
$local_pass = 'tuan1412';

echo "🚀 Starting complete database import from localhost to Railway...\n";

try {
    // Connect to localhost
    echo "📡 Connecting to localhost MySQL...\n";
    $local_pdo = new PDO("mysql:host=$local_host;dbname=$local_db;charset=utf8mb4", $local_user, $local_pass);
    $local_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to localhost\n";
    
    // Connect to Railway
    echo "📡 Connecting to Railway MySQL...\n";
    $railway_pdo = new PDO("mysql:host=$railway_host;port=$railway_port;dbname=$railway_db;charset=utf8mb4", $railway_user, $railway_pass);
    $railway_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to Railway\n";
    
    // Get all tables from localhost
    $tables = $local_pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Found " . count($tables) . " tables to import\n\n";
    
    foreach ($tables as $table) {
        echo "🔄 Processing table: $table\n";
        
        try {
            // Get table structure
            $create_table = $local_pdo->query("SHOW CREATE TABLE `$table`")->fetch();
            $create_sql = $create_table['Create Table'];
            
            // Drop table if exists and recreate
            $railway_pdo->exec("DROP TABLE IF EXISTS `$table`");
            $railway_pdo->exec($create_sql);
            echo "  ✅ Table structure created\n";
            
            // Get data count
            $count = $local_pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            
            if ($count > 0) {
                // Copy data in batches
                $batch_size = 100;
                $offset = 0;
                $total_copied = 0;
                
                while ($offset < $count) {
                    $data = $local_pdo->query("SELECT * FROM `$table` LIMIT $batch_size OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($data)) {
                        // Get column names
                        $columns = array_keys($data[0]);
                        $placeholders = ':' . implode(', :', $columns);
                        $columns_str = '`' . implode('`, `', $columns) . '`';
                        
                        $insert_sql = "INSERT INTO `$table` ($columns_str) VALUES ($placeholders)";
                        $stmt = $railway_pdo->prepare($insert_sql);
                        
                        foreach ($data as $row) {
                            $stmt->execute($row);
                            $total_copied++;
                        }
                    }
                    
                    $offset += $batch_size;
                    echo "  📊 Copied $total_copied/$count rows\r";
                }
                echo "\n  ✅ Data copied: $total_copied rows\n";
            } else {
                echo "  ℹ️ No data to copy\n";
            }
            
        } catch (Exception $e) {
            echo "  ⚠️ Error with table $table: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    echo "🎉 Database import completed successfully!\n";
    echo "📊 Summary:\n";
    echo "  - Tables processed: " . count($tables) . "\n";
    echo "  - Source: localhost/hthree_film\n";
    echo "  - Destination: Railway/railway\n";
    echo "\n📝 Next steps:\n";
    echo "  1. Update .env.production to use Railway backend\n";
    echo "  2. Deploy Vercel frontend\n";
    echo "  3. Test admin panel: https://hthree-film.vercel.app/admin\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>