<?php
/**
 * API: Lấy thông tin subscription của user
 * GET: Lấy subscription hiện tại
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

$conn = getDBConnection();

try {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    
    if (!$user_id) {
        throw new Exception('User ID is required');
    }
    
    // Lấy TẤT CẢ subscriptions của user (bao gồm cả expired để hiển thị)
    $stmt = $conn->prepare("
        SELECT 
            us.id,
            us.user_id,
            us.plan_id,
            us.start_date,
            us.end_date,
            us.status,
            sp.name as plan_name,
            sp.slug as plan_slug,
            sp.price,
            sp.quality,
            sp.max_devices,
            sp.has_ads,
            sp.can_download,
            TIMESTAMPDIFF(MINUTE, NOW(), us.end_date) as minutes_remaining,
            TIMESTAMPDIFF(SECOND, NOW(), us.end_date) as seconds_remaining,
            TIMESTAMPDIFF(MINUTE, us.start_date, us.end_date) as total_minutes,
            TIMESTAMPDIFF(MINUTE, us.start_date, NOW()) as minutes_used,
            CASE 
                WHEN us.end_date < NOW() THEN 'expired'
                WHEN TIMESTAMPDIFF(MINUTE, NOW(), us.end_date) <= 1 THEN 'expiring_soon'
                ELSE 'active'
            END as subscription_status
        FROM user_subscriptions us
        JOIN subscription_plans sp ON us.plan_id = sp.id
        WHERE us.user_id = ? 
        AND (
            (us.status = 'active' AND us.end_date > NOW()) OR
            (us.status = 'cancelled' AND us.end_date > NOW())
        )
        ORDER BY 
            CASE 
                WHEN us.end_date < NOW() THEN 2
                ELSE 1
            END,
            us.end_date DESC
    ");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subscriptions = [];
    
    while ($row = $result->fetch_assoc()) {
        $subscription = $row;
        
        // Convert boolean
        $subscription['has_ads'] = (bool)$subscription['has_ads'];
        $subscription['can_download'] = (bool)$subscription['can_download'];
        
        // Format dates
        $subscription['start_date_formatted'] = date('d/m/Y', strtotime($subscription['start_date']));
        $subscription['end_date_formatted'] = date('d/m/Y', strtotime($subscription['end_date']));
        
        // Calculate progress based on fixed demo duration
        $planDuration = match($subscription['plan_slug']) {
            'basic' => 3,     // 3 phút
            'premium' => 5,   // 5 phút  
            'vip' => 10,      // 10 phút
            default => 3
        };
        
        $totalMinutes = $planDuration;
        $minutesRemaining = max(0, $subscription['minutes_remaining']);
        $minutesUsed = max(0, $totalMinutes - $minutesRemaining);
        
        $progress = $totalMinutes > 0 ? min(100, ($minutesUsed / $totalMinutes) * 100) : 0;
        
        $subscription['progress'] = round($progress, 2);
        $subscription['total_minutes_display'] = $totalMinutes;
        $subscription['used_minutes_display'] = $minutesUsed;
        
        // Format thời gian còn lại theo phút/giây
        if ($minutesRemaining > 0) {
            $subscription['time_remaining_formatted'] = $minutesRemaining . ' phút';
        } else if ($subscription['seconds_remaining'] > 0) {
            $subscription['time_remaining_formatted'] = $subscription['seconds_remaining'] . ' giây';
        } else {
            $subscription['time_remaining_formatted'] = 'Đã hết hạn';
        }
        
        // Auto-cancel expired subscriptions
        if ($subscription['subscription_status'] === 'expired' && $subscription['status'] === 'active') {
            $update_stmt = $conn->prepare("UPDATE user_subscriptions SET status = 'expired' WHERE id = ? AND end_date < NOW()");
            $update_stmt->bind_param("i", $subscription['id']);
            $update_stmt->execute();
            $subscription['status'] = 'expired';
        }
        
        $subscriptions[] = $subscription;
    }
    
    if (count($subscriptions) > 0) {
        echo json_encode([
            'success' => true,
            'data' => $subscriptions,
            'count' => count($subscriptions)
        ]);
    } else {
        // Không có subscription
        echo json_encode([
            'success' => true,
            'data' => [],
            'count' => 0,
            'message' => 'No subscriptions found'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
