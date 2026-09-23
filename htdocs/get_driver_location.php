<?php
// get_driver_location.php – Aurora Tri-Go
// Returns the driver's current GPS coordinates for a booking (passenger polling)
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

if (!$booking_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing booking_id']);
    exit;
}

// Verify this passenger owns this booking
$stmt = $conn->prepare("
    SELECT b.driver_id, b.status,
           dd.current_lat, dd.current_lng,
           dd.is_online
    FROM bookings b
    LEFT JOIN driver_details dd ON dd.user_id = b.driver_id
    WHERE b.id = ? AND b.passenger_id = ?
");
$stmt->bind_param('ii', $booking_id, $user_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
    exit;
}

echo json_encode([
    'status'      => 'ok',
    'driver_lat'  => $row['current_lat'],
    'driver_lng'  => $row['current_lng'],
    'driver_online' => (bool)$row['is_online'],
    'booking_status' => $row['status']
]);
