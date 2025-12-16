<?php
/**
 * Check Subscription Status
 * Kiểm tra trạng thái subscription và cập nhật expired
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
    $userId = $_GET['user_id'] ?? null;
    
    // Lấy tất cả subscription của user (hoặc tất cả nếu không có user_id)
    if ($userId) {
        $stmt = $conn->prepare("
            SELECT us.*, sp.name as plan_name, sp.slug,
                   CASE 
                       WHEN us.end_date <= NOW() THEN 'expired'
                       WHEN us.status = 'active' AND us.end_date > NOW() THEN 'active'
                       ELSE us.status
                   END as calculated_status,
                   TIMESTAMPDIFF(SECOND, NOW(), us.end_date) as seconds_remaining,
                   TIMESTAMPDIFF(MINUTE, NOW(), us.end_date) as minutes_remaining
            FROM user_subscriptions us
            JOIN subscription_plans sp ON us.plan_id = sp.id
            WHERE us.user_id = ?
            ORDER BY us.created_at DESC
        ");
        $stmt->execute([$userId]);
    } else {
        $stmt = $conn->query("
            SELECT us.*, sp.name as plan_name, sp.slug, u.email,
                   CASE 
                       WHEN us.end_date <= NOW() THEN 'expired'
                       WHEN us.status = 'active' AND us.end_date > NOW() THEN 'active'
                       ELSE us.status
                   END as calculated_status,
                   TIMESTAMPDIFF(SECOND, NOW(), us.end_date) as seconds_remaining,
                   TIMESTAMPDIFF(MINUTE, NOW(), us.end_date) as minutes_remaining
            FROM user_subscriptions us
            JOIN subscription_plans sp ON us.plan_id = sp.id
            JOIN users u ON us.user_id = u.id
            ORDER BY us.created_at DESC
            LIMIT 20
        ");
    }
    
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cập nhật trạng thái expired cho những gói đã hết hạn
    $updateStmt = $conn->prepare("
        UPDATE user_subscriptions 
        SET status = 'expired', updated_at = NOW()
        WHERE end_date <= NOW() AND status = 'active'
    ");
    $updateStmt->execute();
    $expiredCount = $updateStmt->rowCount();
    
    // Thống kê
    $stats = [
        'total_subscriptions' => count($subscriptions),
        'active_count' => 0,
        'expired_count' => 0,
        'updated_expired' => $expiredCount
    ];
    
    foreach ($subscriptions as &$sub) {
        if ($sub['calculated_status'] === 'active') {
            $stats['active_count']++;
        } else {
            $stats['expired_count']++;
        }
        
        // Format thời gian còn lại
        if ($sub['seconds_remaining'] > 0) {
            $sub['time_remaining'] = [
                'seconds' => $sub['seconds_remaining'],
                'minutes' => $sub['minutes_remaining'],
                'formatted' => formatTimeRemaining($sub['seconds_remaining'])
            ];
        } else {
            $sub['time_remaining'] = [
                'seconds' => 0,
                'minutes' => 0,
                'formatted' => 'Đã hết hạn'
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'current_time' => date('Y-m-d H:i:s'),
        'stats' => $stats,
        'subscriptions' => $subscriptions
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function formatTimeRemaining($seconds) {
    if ($seconds <= 0) return 'Đã hết hạn';
    
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $days = floor($hours / 24);
    
    if ($days > 0) {
        return "{$days} ngày " . ($hours % 24) . " giờ";
    } elseif ($hours > 0) {
        return "{$hours} giờ " . ($minutes % 60) . " phút";
    } else {
        return "{$minutes} phút " . ($seconds % 60) . " giây";
    }
}