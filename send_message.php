<?php
// send_message.php – Aurora Tri-Go
// Posts a chat message for a booking
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id    = (int) $_SESSION['user_id'];
$booking_id = (int) ($_POST['booking_id'] ?? 0);
$message    = trim($_POST['message'] ?? '');

if (!$booking_id || !$message) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

// Sanitize – max 500 chars
$message = mb_substr($message, 0, 500);

// Verify user is part of this booking
$authStmt = $conn->prepare("
    SELECT id FROM bookings
    WHERE id = ? AND (passenger_id = ? OR driver_id = ?)
      AND status IN ('pending','accepted','ongoing','completed')
");
$authStmt->bind_param('iii', $booking_id, $user_id, $user_id);
$authStmt->execute();
if ($authStmt->get_result()->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized for this booking']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO messages (booking_id, sender_id, message) VALUES (?, ?, ?)");
$stmt->bind_param('iis', $booking_id, $user_id, $message);
$stmt->execute();

echo json_encode(['status' => 'sent', 'message_id' => $conn->insert_id]);
