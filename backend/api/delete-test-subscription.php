<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

// Delete test subscription (ID 31) for user 103
$subscription_id = 31;

$sql = "DELETE FROM user_subscriptions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $subscription_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Test subscription deleted successfully',
        'deleted_id' => $subscription_id
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'success' => false,
        'error' => $stmt->error
    ], JSON_PRETTY_PRINT);
}

$stmt->close();
$conn->close();
