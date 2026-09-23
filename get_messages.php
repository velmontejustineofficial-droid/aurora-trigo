<?php
// get_messages.php – Aurora Tri-Go
// Returns messages for a booking (polling endpoint)
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id    = (int) $_SESSION['user_id'];
$booking_id = (int) ($_GET['booking_id'] ?? 0);
$since_id   = (int) ($_GET['since_id']   ?? 0);

if (!$booking_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing booking_id']);
    exit;
}

// Verify user is part of this booking (include 'cancelled' so cancel notifications are delivered)
$authStmt = $conn->prepare("
    SELECT id FROM bookings
    WHERE id = ? AND (passenger_id = ? OR driver_id = ?)
");
$authStmt->bind_param('iii', $booking_id, $user_id, $user_id);
$authStmt->execute();
if ($authStmt->get_result()->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized for this booking']);
    exit;
}

// Fetch messages after since_id
// Only return messages sent by the OTHER party (not yourself) plus SYSTEM messages
$stmt = $conn->prepare("
    SELECT m.id, m.sender_id, m.message, m.sent_at,
           u.first_name, u.last_name, u.role
    FROM messages m
    JOIN users u ON u.id = m.sender_id
    WHERE m.booking_id = ? AND m.id > ?
    ORDER BY m.sent_at ASC
");
$stmt->bind_param('ii', $booking_id, $since_id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'status'   => 'ok',
    'messages' => $rows,
    'self_id'  => $user_id
]);
