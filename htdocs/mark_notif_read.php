<?php
// mark_notif_read.php – Marks one or all notifications as read
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

if (!empty($_POST['all'])) {
    // Mark all as read for this user
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'ok', 'updated' => $stmt->affected_rows]);
} elseif (!empty($_POST['notif_id'])) {
    // Mark a single notification as read
    $notif_id = (int) $_POST['notif_id'];
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $notif_id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'ok', 'updated' => $stmt->affected_rows]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
}
