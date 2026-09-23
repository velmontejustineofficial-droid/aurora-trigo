<?php
// get_notifications.php – Returns new notifications since a given ID
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id  = (int) $_SESSION['user_id'];
$since_id = (int) ($_GET['since_id'] ?? 0);

$stmt = $conn->prepare("
    SELECT id, title, message, is_read, created_at
    FROM notifications
    WHERE user_id = ? AND id > ?
    ORDER BY created_at DESC
    LIMIT 10
");
$stmt->bind_param('ii', $user_id, $since_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode([
    'status'        => 'ok',
    'notifications' => $notifications,
]);
