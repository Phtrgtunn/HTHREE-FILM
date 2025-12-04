<?php
header('Content-Type: application/json');

// Đọc raw input
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

echo json_encode([
    'raw_input' => $rawInput,
    'decoded_data' => $data,
    'action' => $data['action'] ?? 'NO ACTION',
    'email' => $data['email'] ?? 'NO EMAIL',
    'token' => $data['token'] ?? 'NO TOKEN',
    'newPassword' => isset($data['newPassword']) ? '***' : 'NO PASSWORD'
]);
?>
