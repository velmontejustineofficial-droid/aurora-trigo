<?php
// logout.php – Aurora Tri-Go
session_start();

// If the user is a driver, mark them offline before logging out
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'driver') {
    require_once 'db_connect.php';
    $uid = (int) $_SESSION['user_id'];
    $offlineStmt = $conn->prepare("UPDATE driver_details SET is_online = 0 WHERE user_id = ?");
    if ($offlineStmt) {
        $offlineStmt->bind_param('i', $uid);
        $offlineStmt->execute();
    }
}

// Destroy the session completely
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

// Redirect to login page
header('Location: Login.php');
exit;
