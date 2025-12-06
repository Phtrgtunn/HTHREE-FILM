<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/database.php';

$conn = getDBConnection();

$columns_added = [];
$errors = [];

// Check and add phone column
$result = $conn->query("SHOW COLUMNS FROM `users` LIKE 'phone'");
if ($result->num_rows == 0) {
    try {
        $conn->query("ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(20) NULL AFTER `email`");
        $columns_added[] = 'phone';
    } catch (Exception $e) {
        $errors[] = 'phone: ' . $e->getMessage();
    }
}

// Check and add bio column
$result = $conn->query("SHOW COLUMNS FROM `users` LIKE 'bio'");
if ($result->num_rows == 0) {
    try {
        $conn->query("ALTER TABLE `users` ADD COLUMN `bio` TEXT NULL AFTER `phone`");
        $columns_added[] = 'bio';
    } catch (Exception $e) {
        $errors[] = 'bio: ' . $e->getMessage();
    }
}

// Check and add avatar column
$result = $conn->query("SHOW COLUMNS FROM `users` LIKE 'avatar'");
if ($result->num_rows == 0) {
    try {
        $conn->query("ALTER TABLE `users` ADD COLUMN `avatar` VARCHAR(255) NULL AFTER `bio`");
        $columns_added[] = 'avatar';
    } catch (Exception $e) {
        $errors[] = 'avatar: ' . $e->getMessage();
    }
}

// Check and add updated_at column
$result = $conn->query("SHOW COLUMNS FROM `users` LIKE 'updated_at'");
if ($result->num_rows == 0) {
    try {
        $conn->query("ALTER TABLE `users` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
        $columns_added[] = 'updated_at';
    } catch (Exception $e) {
        $errors[] = 'updated_at: ' . $e->getMessage();
    }
}

echo json_encode([
    'success' => count($errors) == 0,
    'columns_added' => $columns_added,
    'errors' => $errors,
    'message' => count($columns_added) > 0 ? 'Added columns: ' . implode(', ', $columns_added) : 'All columns already exist'
], JSON_PRETTY_PRINT);

$conn->close();
