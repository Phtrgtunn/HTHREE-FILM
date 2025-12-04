<?php
/**
 * Admin Statistics API
 * Lấy thống kê tổng quan cho dashboard
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // 1. Tổng doanh thu
    $stmt = $conn->query("
        SELECT 
            COALESCE(SUM(total), 0) as total_revenue,
            COUNT(*) as total_orders
        FROM orders 
        WHERE payment_status = 'paid'
    ");
    $revenue_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 2. Doanh thu tháng này
    $stmt = $conn->query("
        SELECT 
            COALESCE(SUM(total), 0) as month_revenue,
            COUNT(*) as month_orders
        FROM orders 
        WHERE payment_status = 'paid'
        AND MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    $month_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 3. Doanh thu tháng trước
    $stmt = $conn->query("
        SELECT 
            COALESCE(SUM(total), 0) as last_month_revenue
        FROM orders 
        WHERE payment_status = 'paid'
        AND MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)
        AND YEAR(created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
    ");
    $last_month_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 4. Tính % thay đổi doanh thu
    $revenue_change = 0;
    if ($last_month_data['last_month_revenue'] > 0) {
        $revenue_change = (($month_data['month_revenue'] - $last_month_data['last_month_revenue']) / $last_month_data['last_month_revenue']) * 100;
    } elseif ($month_data['month_revenue'] > 0) {
        $revenue_change = 100;
    }
    
    // 5. Tổng số người dùng
    $stmt = $conn->query("SELECT COUNT(*) as total_users FROM users");
    $users_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 6. Người dùng mới tháng này
    $stmt = $conn->query("
        SELECT COUNT(*) as new_users
        FROM users 
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    $new_users_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 7. Đơn hàng chờ xử lý
    $stmt = $conn->query("
        SELECT COUNT(*) as pending_orders
        FROM orders 
        WHERE payment_status = 'pending'
    ");
    $pending_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 8. Top gói bán chạy
    $stmt = $conn->query("
        SELECT 
            sp.name,
            sp.slug,
            COUNT(oi.id) as order_count,
            SUM(oi.total) as total_revenue
        FROM order_items oi
        JOIN subscription_plans sp ON oi.plan_id = sp.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.payment_status = 'paid'
        GROUP BY sp.id
        ORDER BY order_count DESC
        LIMIT 5
    ");
    $top_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Tính % cho top plans
    $max_count = $top_plans[0]['order_count'] ?? 1;
    foreach ($top_plans as &$plan) {
        $plan['percentage'] = ($plan['order_count'] / $max_count) * 100;
        $plan['revenue_formatted'] = number_format($plan['total_revenue']) . ' đ';
    }
    
    // Format numbers
    $total_revenue_formatted = number_format($revenue_data['total_revenue']) . ' đ';
    $month_revenue_formatted = number_format($month_data['month_revenue']) . ' đ';
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_revenue' => $revenue_data['total_revenue'],
            'total_revenue_formatted' => $total_revenue_formatted,
            'revenue_change' => round($revenue_change, 1),
            'revenue_change_formatted' => ($revenue_change >= 0 ? '+' : '') . round($revenue_change, 1) . '%',
            
            'total_orders' => $revenue_data['total_orders'],
            'month_orders' => $month_data['month_orders'],
            
            'total_users' => $users_data['total_users'],
            'new_users' => $new_users_data['new_users'],
            
            'pending_orders' => $pending_data['pending_orders'],
            
            'top_plans' => $top_plans
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi lấy thống kê: ' . $e->getMessage()
    ]);
}
