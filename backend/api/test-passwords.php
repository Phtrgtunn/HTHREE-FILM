<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$passwords = ['', 'mysql', 'root', 'password', '123456'];
$results = [];

foreach ($passwords as $pwd) {
    $conn = @new mysqli('localhost', 'root', $pwd, 'hthree_film');
    
    if ($conn->connect_error) {
        $results[$pwd ?: '(empty)'] = 'Failed: ' . $conn->connect_error;
    } else {
        $results[$pwd ?: '(empty)'] = 'SUCCESS!';
        $conn->close();
    }
}

echo json_encode([
    'results' => $results
], JSON_PRETTY_PRINT);
