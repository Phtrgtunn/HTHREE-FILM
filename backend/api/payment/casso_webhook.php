<?php
/**
 * Casso Webhook Handler
 * Nhận thông báo từ Casso khi có giao dịch mới
 * Tự động kích hoạt gói subscription
 */

header('Content-Type: application/json; charset=utf-8');

// Handle GET request for testing
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'success' => true,
        'message' => 'Casso webhook endpoint is ready',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

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
        
        // 1. Cập nhật trạng thái đơn hàng thành COMPLETED
        $stmt = $conn->prepare("
            UPDATE orders 
            SET 
                payment_status = 'paid',
                status = 'completed',
                paid_at = NOW(),
                completed_at = NOW(),
                transaction_id = ?,
                payment_note = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $transactionId,
            "Thanh toán qua VietQR - Casso (Tự động)",
            $order['id']
        ]);
        
        logWebhook("Order marked as COMPLETED: {$orderCode}");
        
        // 2. Kích hoạt subscription tự động
        $activated = activateSubscriptionAuto(
            $conn,
            $order['id'],
            $order['user_id']
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
    // Pattern 1: HTHREE ORD20241205ABC (có khoảng trắng)
    // Pattern 2: HTHREEORD20241205ABC (không khoảng trắng)
    // Pattern 3: ORD20241205ABC (chỉ có mã đơn)
    
    // Try pattern with ORDER_PREFIX first
    if (defined('ORDER_PREFIX')) {
        $pattern = '/' . ORDER_PREFIX . '\s*(ORD[A-Z0-9]+)/i';
        if (preg_match($pattern, $description, $matches)) {
            return $matches[1];
        }
    }
    
    // Try direct ORD pattern
    $pattern = '/(ORD[0-9]{8,}[A-Z0-9]*)/i';
    if (preg_match($pattern, $description, $matches)) {
        return $matches[1];
    }
    
    return null;
}

/**
 * Kích hoạt subscription tự động (sử dụng logic từ activate_bank_transfer_orders.php)
 */
function activateSubscriptionAuto($conn, $order_id, $user_id) {
    try {
        // Lấy thông tin order items
        $stmt = $conn->prepare("
            SELECT oi.*, sp.slug, sp.quality, sp.duration_days
            FROM order_items oi
            JOIN subscription_plans sp ON oi.plan_id = sp.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($items)) {
            logWebhook("No order items found for order: {$order_id}");
            return false;
        }
        
        foreach ($items as $item) {
            $plan_id = $item['plan_id'];
            $duration_months = $item['duration_months'];
            $duration_days = $item['duration_days'] * $duration_months;
            
            logWebhook("Processing plan_id: {$plan_id}, duration: {$duration_days} days");
            
            // Kiểm tra đã có subscription chưa (chỉ check những gói chưa hết hạn)
            $check_stmt = $conn->prepare("
                SELECT id, end_date, status 
                FROM user_subscriptions 
                WHERE user_id = ? AND plan_id = ? AND status = 'active' AND end_date > NOW()
                ORDER BY end_date DESC 
                LIMIT 1
            ");
            $check_stmt->execute([$user_id, $plan_id]);
            $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                // Gia hạn
                $current_end = new DateTime($existing['end_date']);
                $now = new DateTime();
                
                $start_from = $current_end > $now ? $current_end : $now;
                $new_end = clone $start_from;
                $new_end->modify("+{$duration_days} days");
                
                $update_stmt = $conn->prepare("
                    UPDATE user_subscriptions 
                    SET end_date = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $new_end_str = $new_end->format('Y-m-d H:i:s');
                $update_stmt->execute([$new_end_str, $existing['id']]);
                
                logWebhook("Extended subscription to: {$new_end_str}");
            } else {
                // Tạo mới (hoặc gói đã hết hạn)
                // Set các gói cũ thành expired
                $expire_old = $conn->prepare("
                    UPDATE user_subscriptions 
                    SET status = 'expired', updated_at = NOW()
                    WHERE user_id = ? AND plan_id = ? AND status = 'active' AND end_date <= NOW()
                ");
                $expire_old->execute([$user_id, $plan_id]);
                
                $insert_stmt = $conn->prepare("
                    INSERT INTO user_subscriptions (
                        user_id, plan_id, start_date, end_date, status, order_id, created_at, updated_at
                    ) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), 'active', ?, NOW(), NOW())
                ");
                $insert_stmt->execute([$user_id, $plan_id, $duration_days, $order_id]);
                
                logWebhook("Created new subscription (reset time)");
            }
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
