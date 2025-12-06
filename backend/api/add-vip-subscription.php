<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

// Add VIP subscription for user ID 103
$user_id = 103;
$plan_id = 4; // VIP plan
$start_date = date('Y-m-d H:i:s');
$end_date = date('Y-m-d H:i:s', strtotime('+30 days'));

$sql = "INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status) 
        VALUES (?, ?, ?, ?, 'active')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $user_id, $plan_id, $start_date, $end_date);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'VIP subscription added successfully',
        'subscription_id' => $stmt->insert_id,
        'user_id' => $user_id,
        'plan_id' => $plan_id,
        'start_date' => $start_date,
        'end_date' => $end_date
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'success' => false,
        'error' => $stmt->error
    ], JSON_PRETTY_PRINT);
}

$stmt->close();
$conn->close();
