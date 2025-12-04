<?php
// Add firebase_uid column to users table
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDBConnection();
    
    echo "🚀 Adding firebase_uid column...\n\n";
    
    // Add firebase_uid column
    $sql = "ALTER TABLE users ADD COLUMN firebase_uid VARCHAR(128) UNIQUE DEFAULT NULL AFTER id";
    
    if ($db->query($sql)) {
        echo "✅ Added firebase_uid column\n";
    } else {
        echo "⚠️  Column might already exist: " . $db->error . "\n";
    }
    
    // Make password nullable
    $sql = "ALTER TABLE users MODIFY COLUMN password VARCHAR(255) DEFAULT NULL";
    if ($db->query($sql)) {
        echo "✅ Made password column nullable\n";
    } else {
        echo "⚠️  Error: " . $db->error . "\n";
    }
    
    echo "\n✨ Migration completed!\n";
    
    $db->close();
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}
?>
