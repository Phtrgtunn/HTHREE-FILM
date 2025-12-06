<?php
/**
 * Casso Webhook Handler
 * Nhận thông báo từ Casso khi có giao dịch mới
 * Tự động kích hoạt gói subscription
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/config_payment.php';

$db = new Database();
$conn = $db->getConnection();

// Log file để debug
$logFile = __DIR__ . '/../../logs/casso_webhook.log';

try {
    // Nhận raw input
    $input = file_get_contents('php://input');
    
    // Log request
    logWebhook("Received webhook: " . $input);
    
    // Parse JSON
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON');
    }
    
    // Verify webhook signature (nếu Casso gửi)
    $signature = $_SERVER['HTTP_X_CASSO_SIGNATURE'] ?? '';
    if ($signature && !verifySignature($input, $signature, CASSO_WEBHOOK_SECRET)) {
        throw new Exception('Invalid signature');
    }
    
    // Xử lý từng giao dịch
    $processedCount = 0;
    
    foreach ($data['data'] as $transaction) {
        $amount = $transaction['amount'];
        $description = $transaction['description'];
        $transactionId = $transaction['id'];
        $transactionDate = $transaction['when'];
        
        logWebhook("Processing transaction: ID={$transactionId}, Amount={$amount}, Description={$description}");
        
        // Extract order code từ description
        // Format: "HTHREE 20241205ABC123" hoặc "HTHREE20241205ABC123"
        $orderCode = extractOrderCode($description);
        
        if (!$orderCode) {
            logWebhook("Cannot extract order code from: {$description}");
            continue;
        }
        
        logWebhook("Extracted order code: {$orderCode}");
        
        // Tìm đơn hàng
        $stmt = $conn->prepare("
            SELECT * FROM orders 
            WHERE order_code = ? 
            AND payment_status = 'pending'
        ");
        $stmt->execute([$orderCode]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            logWebhook("Order not found or already paid: {$orderCode}");
            continue;
        }
        
        // Verify số tiền
        if ($order['total'] != $amount) {
            logWebhook("Amount mismatch: Expected {$order['total']}, Got {$amount}");
            
            // Cập nhật trạng thái lỗi
            $stmt = $conn->prepare("
                UPDATE orders 
                SET 
                    payment_status = 'failed',
                    payment_note = ?
                WHERE id = ?
            ");
            $stmt->execute([
                "Số tiền không khớp. Mong đợi: {$order['total']}, Nhận được: {$amount}",
                $order['id']
            ]);
            
            continue;
        }
        
        // Kiểm tra đơn hàng đã hết hạn chưa
        if (strtotime($order['expires_at']) < time()) {
            logWebhook("Order expired: {$orderCode}");
            
            $stmt = $conn->prepare("
                UPDATE orders 
                SET payment_status = 'expired'
                WHERE id = ?
            ");
            $stmt->execute([$order['id']]);
            
            continue;
        }
        
        // ✅ THANH TOÁN HỢP LỆ - KÍCH HOẠT GÓI
        
        // 1. Cập nhật trạng thái đơn hàng
        $stmt = $conn->prepare("
            UPDATE orders 
            SET 
                payment_status = 'paid',
                paid_at = NOW(),
                transaction_id = ?,
                payment_note = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $transactionId,
            "Thanh toán qua VietQR - Casso",
            $order['id']
        ]);
        
        logWebhook("Order marked as paid: {$orderCode}");
        
        // 2. Kích hoạt subscription
        $activated = activateSubscription(
            $order['user_id'],
            $order['plan_id'],
            $order['duration_months']
        );
        
        if ($activated) {
            logWebhook("Subscription activated for user: {$order['user_id']}");
            
            // 3. Gửi email thông báo (optional)
            sendActivationEmail($order['customer_email'], $order);
            
            $processedCount++;
        } else {
            logWebhook("Failed to activate subscription for user: {$order['user_id']}");
        }
    }
    
    logWebhook("Processed {$processedCount} transactions successfully");
    
    echo json_encode([
        'success' => true,
        'processed' => $processedCount
    ]);
    
} catch (Exception $e) {
    logWebhook("Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Extract order code từ description
 */
function extractOrderCode($description) {
    // Pattern: HTHREE 20241205ABC123 hoặc HTHREE20241205ABC123
    $pattern = '/' . ORDER_PREFIX . '\s*([A-Z0-9]+)/i';
    
    if (preg_match($pattern, $description, $matches)) {
        return $matches[1];
    }
    
    return null;
}

/**
 * Kích hoạt subscription cho user
 */
function activateSubscription($userId, $planId, $durationMonths) {
    global $conn;
    
    try {
        // Lấy thông tin plan
        $stmt = $conn->prepare("SELECT * FROM plans WHERE id = ?");
        $stmt->execute([$planId]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$plan) {
            throw new Exception('Plan not found');
        }
        
        // Tính ngày bắt đầu và kết thúc
        $startDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime("+{$durationMonths} months"));
        
        // Kiểm tra xem user đã có subscription chưa
        $stmt = $conn->prepare("
            SELECT * FROM subscriptions 
            WHERE user_id = ? 
            AND plan_id = ?
            AND status = 'active'
        ");
        $stmt->execute([$userId, $planId]);
        $existingSub = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingSub) {
            // Gia hạn subscription hiện tại
            $newEndDate = date('Y-m-d H:i:s', strtotime($existingSub['end_date'] . " +{$durationMonths} months"));
            
            $stmt = $conn->prepare("
                UPDATE subscriptions 
                SET end_date = ?
                WHERE id = ?
            ");
            $stmt->execute([$newEndDate, $existingSub['id']]);
            
            logWebhook("Extended subscription: {$existingSub['id']} to {$newEndDate}");
        } else {
            // Tạo subscription mới
            $stmt = $conn->prepare("
                INSERT INTO subscriptions (
                    user_id, plan_id, plan_name, plan_slug, quality,
                    start_date, end_date, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())
            ");
            $stmt->execute([
                $userId,
                $planId,
                $plan['name'],
                $plan['slug'],
                $plan['quality'],
                $startDate,
                $endDate
            ]);
            
            logWebhook("Created new subscription for user: {$userId}");
        }
        
        return true;
        
    } catch (Exception $e) {
        logWebhook("Error activating subscription: " . $e->getMessage());
        return false;
    }
}

/**
 * Gửi email thông báo kích hoạt
 */
function sendActivationEmail($email, $order) {
    // TODO: Implement email sending
    // Có thể dùng PHPMailer hoặc service như SendGrid
    
    logWebhook("Email notification sent to: {$email}");
}

/**
 * Verify webhook signature
 */
function verifySignature($payload, $signature, $secret) {
    $expectedSignature = hash_hmac('sha256', $payload, $secret);
    return hash_equals($expectedSignature, $signature);
}

/**
 * Log webhook activity
 */
function logWebhook($message) {
    global $logFile;
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}\n";
    
    // Tạo thư mục logs nếu chưa có
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
