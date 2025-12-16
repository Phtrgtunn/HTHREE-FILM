<?php
/**
 * Manual Test Payment
 * API để test thanh toán ngay lập tức (chỉ dùng localhost)
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Chỉ cho phép localhost
$allowedHosts = ['localhost', '127.0.0.1', '::1'];
$clientHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
$clientHost = explode(':', $clientHost)[0]; // Remove port

if (!in_array($clientHost, $allowedHosts)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'This API is only available on localhost for testing'
    ]);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $orderId = $input['order_id'] ?? null;
    
    if (!$orderId) {
        throw new Exception('Order ID is required');
    }
    
    // Tìm đơn hàng
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND payment_status = 'pending'");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode([
            'success' => false,
            'message' => 'Order not found or already processed'
        ]);
        exit;
    }
    
    // Kiểm tra đơn hàng chưa hết hạn
    if (strtotime($order['expires_at']) < time()) {
        echo json_encode([
            'success' => false,
            'message' => 'Order has expired'
        ]);
        exit;
    }
    
    // Tự động approve đơn hàng ngay lập tức
    $approved = approveOrderManual($conn, $order);
    
    if ($approved) {
        echo json_encode([
            'success' => true,
            'message' => 'Test payment successful - Order approved immediately',
            'transaction' => [
                'id' => 'TEST_' . time(),
                'amount' => $order['total'],
                'description' => 'Manual test payment: ' . $order['order_code'],
                'time' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        throw new Exception('Failed to approve order');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Approve đơn hàng ngay lập tức (manual test)
 */
function approveOrderManual($conn, $order) {
    try {
        // Bắt đầu transaction
        $conn->beginTransaction();
        
        // 1. Update order status
        $stmt = $conn->prepare("
            UPDATE orders 
            SET payment_status = 'paid',
                status = 'completed',
                paid_at = NOW(),
                completed_at = NOW(),
                transaction_id = ?
            WHERE id = ?
        ");
        
        $transactionId = 'TEST_MANUAL_' . time();
        $stmt->execute([$transactionId, $order['id']]);
        
        // 2. Kích hoạt subscription
        $stmt = $conn->prepare("
            SELECT oi.plan_id, oi.duration_months, sp.duration_days
            FROM order_items oi
            JOIN subscription_plans sp ON oi.plan_id = sp.id
            WHERE oi.order_id = ?
            LIMIT 1
        ");
        $stmt->execute([$order['id']]);
        $orderItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($orderItem) {
            $totalDays = $orderItem['duration_days'] * $orderItem['duration_months'];
            $endDate = date('Y-m-d H:i:s', strtotime("+{$totalDays} days"));
            
            // Kiểm tra subscription hiện tại
            $checkStmt = $conn->prepare("
                SELECT id, end_date FROM user_subscriptions 
                WHERE user_id = ? AND plan_id = ? AND status = 'active' AND end_date > NOW()
                ORDER BY end_date DESC LIMIT 1
            ");
            $checkStmt->execute([$order['user_id'], $orderItem['plan_id']]);
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                // Gia hạn từ ngày hết hạn hiện tại
                $currentEnd = new DateTime($existing['end_date']);
                $newEnd = clone $currentEnd;
                $newEnd->modify("+{$totalDays} days");
                
                $stmt = $conn->prepare("
                    UPDATE user_subscriptions 
                    SET end_date = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$newEnd->format('Y-m-d H:i:s'), $existing['id']]);
            } else {
                // Tạo subscription mới
                $stmt = $conn->prepare("
                    INSERT INTO user_subscriptions 
                    (user_id, plan_id, start_date, end_date, status, order_id)
                    VALUES (?, ?, NOW(), ?, 'active', ?)
                ");
                
                $stmt->execute([
                    $order['user_id'],
                    $orderItem['plan_id'],
                    $endDate,
                    $order['id']
                ]);
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        return true;
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log("Error approving manual test payment: " . $e->getMessage());
        return false;
    }
}