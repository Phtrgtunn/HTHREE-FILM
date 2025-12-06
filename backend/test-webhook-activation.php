<?php
/**
 * Test script để kiểm tra logic activation trong webhook
 */

require_once __DIR__ . '/config/database.php';

$conn = getDBConnection();

// Lấy order mới nhất của user
$user_id = 104; // Thay bằng user_id của bạn

echo "<h2>Testing Subscription Activation Logic</h2>";
echo "<hr>";

// 1. Lấy order mới nhất
$stmt = $conn->prepare("
    SELECT o.*, oi.plan_id, oi.duration_months, sp.duration_days
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN subscription_plans sp ON oi.plan_id = sp.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("No orders found for user {$user_id}");
}

echo "<h3>Latest Order:</h3>";
echo "<pre>";
print_r($order);
echo "</pre>";

$plan_id = $order['plan_id'];
$duration_days = $order['duration_days'] * $order['duration_months'];

echo "<p><strong>Plan ID:</strong> {$plan_id}</p>";
echo "<p><strong>Duration:</strong> {$duration_days} days</p>";

// 2. Check existing subscriptions
echo "<hr>";
echo "<h3>Existing Subscriptions:</h3>";

$stmt = $conn->prepare("
    SELECT * FROM user_subscriptions 
    WHERE user_id = ? AND plan_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("ii", $user_id, $plan_id);
$stmt->execute();
$result = $stmt->get_result();

$subscriptions = [];
while ($row = $result->fetch_assoc()) {
    $subscriptions[] = $row;
}

if (empty($subscriptions)) {
    echo "<p>No existing subscriptions found.</p>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Status</th><th>Start Date</th><th>End Date</th><th>Created At</th></tr>";
    foreach ($subscriptions as $sub) {
        $is_expired = strtotime($sub['end_date']) < time();
        $style = $is_expired ? 'color: red;' : 'color: green;';
        echo "<tr style='{$style}'>";
        echo "<td>{$sub['id']}</td>";
        echo "<td>{$sub['status']}</td>";
        echo "<td>{$sub['start_date']}</td>";
        echo "<td>{$sub['end_date']}</td>";
        echo "<td>{$sub['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 3. Check active subscription
echo "<hr>";
echo "<h3>Active Subscription Check:</h3>";

$stmt = $conn->prepare("
    SELECT id, end_date, status 
    FROM user_subscriptions 
    WHERE user_id = ? AND plan_id = ? AND status = 'active' AND end_date > NOW()
    ORDER BY end_date DESC 
    LIMIT 1
");
$stmt->bind_param("ii", $user_id, $plan_id);
$stmt->execute();
$result = $stmt->get_result();
$active = $result->fetch_assoc();

if ($active) {
    echo "<p style='color: green;'><strong>✓ Found active subscription (will EXTEND)</strong></p>";
    echo "<pre>";
    print_r($active);
    echo "</pre>";
    
    $current_end = new DateTime($active['end_date']);
    $now = new DateTime();
    $start_from = $current_end > $now ? $current_end : $now;
    $new_end = clone $start_from;
    $new_end->modify("+{$duration_days} days");
    
    echo "<p><strong>Current end:</strong> " . $current_end->format('Y-m-d H:i:s') . "</p>";
    echo "<p><strong>New end (after extend):</strong> " . $new_end->format('Y-m-d H:i:s') . "</p>";
} else {
    echo "<p style='color: orange;'><strong>✗ No active subscription (will CREATE NEW)</strong></p>";
    echo "<p>This means subscription will be created with RESET time.</p>";
    
    $new_end = new DateTime();
    $new_end->modify("+{$duration_days} days");
    echo "<p><strong>New subscription end date:</strong> " . $new_end->format('Y-m-d H:i:s') . "</p>";
}

$conn->close();
?>
