<?php
// admin_stats.php
// Returns fresh dashboard stats as JSON.
// Must be in the same folder as Admin_Dashboard.php and db_connect.php.
session_start();
require_once 'db_connect.php';

// Only allow logged-in admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$s = [];
$s['total_users']      = (int)$conn->query("SELECT COUNT(*) c FROM users WHERE role != 'admin'")->fetch_assoc()['c'];
$s['total_drivers']    = (int)$conn->query("SELECT COUNT(*) c FROM users WHERE role='driver'")->fetch_assoc()['c'];
$s['total_passengers'] = (int)$conn->query("SELECT COUNT(*) c FROM users WHERE role='passenger'")->fetch_assoc()['c'];
$s['banned_users']     = (int)$conn->query("SELECT COUNT(*) c FROM users WHERE is_active=0 AND role!='admin'")->fetch_assoc()['c'];
$s['total_bookings']   = (int)$conn->query("SELECT COUNT(*) c FROM bookings")->fetch_assoc()['c'];
$s['completed_rides']  = (int)$conn->query("SELECT COUNT(*) c FROM bookings WHERE status='completed'")->fetch_assoc()['c'];
$s['total_revenue']    = (float)$conn->query("SELECT COALESCE(SUM(fare),0) s FROM bookings WHERE status='completed'")->fetch_assoc()['s'];
$s['warned_users']     = (int)$conn->query("SELECT COUNT(*) c FROM users WHERE warnings>0 AND role!='admin'")->fetch_assoc()['c'];

echo json_encode($s);
