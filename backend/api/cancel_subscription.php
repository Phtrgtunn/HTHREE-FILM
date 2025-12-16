<?php
/**
 * API: Hủy subscription
 * POST: Hủy subscription của user
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['subscription_id'])) {
        throw new Exception('Subscription ID is required');
    }
    
    $subscription_id = intval($data['subscription_id']);
    
    // Kiểm tra subscription tồn tại
    $stmt = $conn->prepare("SELECT * FROM user_subscriptions WHERE id = ?");
    $stmt->bind_param("i", $subscription_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Subscription not found');
    }
    
    $subscription = $result->fetch_assoc();
    
    // Kiểm tra subscription đã bị hủy chưa - nếu đã hủy thì kích hoạt lại
    if ($subscription['status'] === 'cancelled') {
        // Kích hoạt lại subscription
        $stmt = $conn->prepare("
            UPDATE user_subscriptions 
            SET status = 'active',
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("i", $subscription_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Subscription reactivated successfully'
            ]);
        } else {
            throw new Exception('Failed to reactivate subscription');
        }
        return;
    }
    
    // Hủy subscription
    $stmt = $conn->prepare("
        UPDATE user_subscriptions 
        SET status = 'cancelled',
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param("i", $subscription_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Subscription cancelled successfully'
        ]);
    } else {
        throw new Exception('Failed to cancel subscription');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
