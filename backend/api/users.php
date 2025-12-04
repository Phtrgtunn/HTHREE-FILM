<?php
// backend/api/users.php

// CORS headers - PHẢI đặt trước bất kỳ output nào
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDBConnection();

// POST - Register or get user by Firebase UID
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $firebase_uid = $data['firebase_uid'] ?? null;
    $email = $data['email'] ?? null;
    $username = $data['username'] ?? null;
    $full_name = $data['full_name'] ?? null;
    $avatar = $data['avatar'] ?? null;
    
    if (!$firebase_uid || !$email) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    try {
        // Check if user exists
        $sql = "SELECT * FROM users WHERE firebase_uid = ? OR email = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ss', $firebase_uid, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // User exists, update info
            $user = $result->fetch_assoc();
            
            $sql = "UPDATE users SET 
                    username = COALESCE(?, username),
                    full_name = COALESCE(?, full_name),
                    avatar = COALESCE(?, avatar),
                    last_login = NOW()
                    WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('sssi', $username, $full_name, $avatar, $user['id']);
            $stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => 'User updated',
                'user' => [
                    'id' => $user['id'],
                    'firebase_uid' => $user['firebase_uid'],
                    'email' => $user['email'],
                    'username' => $username ?? $user['username'],
                    'full_name' => $full_name ?? $user['full_name'],
                    'avatar' => $avatar ?? $user['avatar']
                ]
            ]);
        } else {
            // Create new user
            $sql = "INSERT INTO users (firebase_uid, email, username, full_name, avatar) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('sssss', $firebase_uid, $email, $username, $full_name, $avatar);
            
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                
                echo json_encode([
                    'success' => true,
                    'message' => 'User created',
                    'user' => [
                        'id' => $user_id,
                        'firebase_uid' => $firebase_uid,
                        'email' => $email,
                        'username' => $username,
                        'full_name' => $full_name,
                        'avatar' => $avatar
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create user']);
            }
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

// GET - Get user by Firebase UID or email
elseif ($method === 'GET') {
    $firebase_uid = $_GET['firebase_uid'] ?? null;
    $email = $_GET['email'] ?? null;
    
    if (!$firebase_uid && !$email) {
        echo json_encode(['success' => false, 'message' => 'Missing firebase_uid or email']);
        exit;
    }
    
    try {
        if ($firebase_uid) {
            $sql = "SELECT * FROM users WHERE firebase_uid = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('s', $firebase_uid);
        } else {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('s', $email);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'User not found'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

$db->close();
?>
