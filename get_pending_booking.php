<?php
// get_pending_booking.php – Aurora Tri-Go
// Returns the oldest pending booking with passenger info (JSON)
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

// Auth guard
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$driver_id = (int) $_SESSION['user_id'];

// Fetch the oldest pending booking (not yet assigned to any driver)
$stmt = $conn->prepare("
    SELECT
        b.id            AS booking_id,
        b.pickup_address,
        b.pickup_lat,
        b.pickup_lng,
        b.dropoff_address,
        b.dropoff_lat,
        b.dropoff_lng,
        b.distance_km,
        b.fare,
        b.payment_method,
        b.notes,
        b.booked_at,
        u.first_name,
        u.last_name,
        u.rating        AS passenger_rating,
        u.total_rides   AS passenger_rides,
        u.is_verified   AS passenger_verified,
        u.phone         AS passenger_phone
    FROM bookings b
    JOIN users u ON u.id = b.passenger_id
    WHERE b.status = 'pending'
      AND b.driver_id IS NULL
    ORDER BY b.booked_at ASC
    LIMIT 1
");
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['status' => 'found', 'booking' => $row]);
} else {
    echo json_encode(['status' => 'none']);
}
