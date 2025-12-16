<?php
/**
 * Simulate Bank Transfer (For Testing)
 * API để giả lập việc nhận được giao dịch chuyển khoản
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $orderCode = $input['order_code'] ?? null;
    $amount = $input['amount'] ?? null;
    
    if (!$orderCode || !$amount) {
        throw new Exception('Order code and amount are required');
    }
    
    // Tìm đơn hàng
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ? AND payment_status = 'pending'");
    $stmt->execute([$orderCode]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode([
            'success' => false,
            'message' => 'Order not found or already processed'
        ]);
        exit;
    }
    
    // Kiểm tra số tiền
    if ($order['total'] != $amount) {
        echo json_encode([
            'success' => false,
            'message' => 'Amount mismatch'
        ]);
        exit;
    }
    
    // Giả lập: Kiểm tra xem đã có "giao dịch" trong 30 giây gần đây chưa
    $createdTime = strtotime($order['created_at']);
    $currentTime = time();
    $timeDiff = $currentTime - $createdTime;
    
    // Nếu đã tạo đơn hàng được hơn 5 giây, giả lập đã có giao dịch
    if ($timeDiff >= 5) {
        // Tự động approve đơn hàng
        $approved = approveOrderSimulation($conn, $order);
        
        if ($approved) {
            echo json_encode([
                'success' => true,
                'message' => 'Simulated bank transfer detected and order approved',
                'transaction' => [
                    'id' => 'SIM_' . time(),
                    'amount' => $amount,
                    'description' => 'Simulated transfer: ' . $orderCode,
                    'time' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            throw new Exception('Failed to approve order');
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No transaction detected yet (simulation)',
            'waiting_time' => 10 - $timeDiff
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Approve đơn hàng (simulation)
 */
function approveOrderSimulation($conn, $order) {
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
        
        $transactionId = 'SIM_BANK_' . time();
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
        
        // Commit transaction
        $conn->commit();
        
        return true;
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log("Error approving order simulation: " . $e->getMessage());
        return false;
    }
}