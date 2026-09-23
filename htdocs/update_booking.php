<?php
// update_booking.php – Aurora Tri-Go
// Handles: accept, decline, pickup, dropoff, complete, book, cancel, driver_cancel, poll, rate, set_online, update_location
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$role    = strtolower(trim($_SESSION['role'] ?? ''));
$action  = $_POST['action']     ?? '';
$bid     = (int)($_POST['booking_id'] ?? 0);

$actions_without_bid = ['book', 'set_online'];
if (!$action || (!$bid && !in_array($action, $actions_without_bid))) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

switch ($action) {

    // ── DRIVER: Accept a pending booking ──────────────────────────────────
    case 'accept':
        if ($role !== 'driver') { echo json_encode(['status'=>'error','message'=>'Not a driver']); exit; }

        $stmt = $conn->prepare("
            UPDATE bookings
            SET status = 'accepted', driver_id = ?, accepted_at = NOW()
            WHERE id = ? AND status = 'pending' AND driver_id IS NULL
        ");
        $stmt->bind_param('ii', $user_id, $bid);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $onlineStmt = $conn->prepare("UPDATE driver_details SET is_online = 1 WHERE user_id = ?");
            $onlineStmt->bind_param('i', $user_id);
            $onlineStmt->execute();

            $s2 = $conn->prepare("
                SELECT b.*, u.first_name, u.last_name, u.rating AS passenger_rating,
                       u.total_rides AS passenger_rides, u.is_verified AS passenger_verified,
                       u.phone AS passenger_phone
                FROM bookings b JOIN users u ON u.id = b.passenger_id
                WHERE b.id = ?
            ");
            $s2->bind_param('i', $bid);
            $s2->execute();
            $booking = $s2->get_result()->fetch_assoc();
            echo json_encode(['status' => 'accepted', 'booking' => $booking]);
        } else {
            echo json_encode(['status' => 'taken', 'message' => 'Booking already taken by another driver']);
        }
        break;

    // ── DRIVER: Decline ───────────────────────────────────────────────────
    case 'decline':
        echo json_encode(['status' => 'declined']);
        break;

    // ── DRIVER: Mark passenger as picked up (Arrived) ─────────────────────
    case 'pickup':
        if ($role !== 'driver') { echo json_encode(['status'=>'error','message'=>'Not a driver']); exit; }

        // First verify the booking belongs to this driver and is in the right state
        $chk = $conn->prepare("SELECT status FROM bookings WHERE id = ? AND driver_id = ?");
        $chk->bind_param('ii', $bid, $user_id);
        $chk->execute();
        $chkRow = $chk->get_result()->fetch_assoc();

        if (!$chkRow) {
            echo json_encode(['status' => 'error', 'message' => 'Booking not found or not assigned to you']);
            break;
        }

        // If already ongoing or completed, treat as success (idempotent re-click)
        if (in_array($chkRow['status'], ['ongoing', 'completed'])) {
            echo json_encode(['status' => 'picked_up']);
            break;
        }

        if ($chkRow['status'] !== 'accepted') {
            echo json_encode(['status' => 'error', 'message' => 'Booking is not in accepted state (current: ' . $chkRow['status'] . ')']);
            break;
        }

        // Update to ongoing
        $stmt = $conn->prepare("
            UPDATE bookings
            SET status = 'ongoing', picked_up_at = NOW()
            WHERE id = ? AND driver_id = ? AND status = 'accepted'
        ");
        $stmt->bind_param('ii', $bid, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Insert system notification message for passenger
            $notifStmt = $conn->prepare("INSERT INTO messages (booking_id, sender_id, message) VALUES (?, ?, ?)");
            $sysMsg = '🛺 SYSTEM: Your driver has arrived and picked you up! Heading to your destination.';
            $notifStmt->bind_param('iis', $bid, $user_id, $sysMsg);
            $notifStmt->execute();
            echo json_encode(['status' => 'picked_up']);
        } else {
            // Race condition: re-check if it got updated by a concurrent request
            $chk2 = $conn->prepare("SELECT status FROM bookings WHERE id = ? AND driver_id = ?");
            $chk2->bind_param('ii', $bid, $user_id);
            $chk2->execute();
            $chkRow2 = $chk2->get_result()->fetch_assoc();
            if ($chkRow2 && in_array($chkRow2['status'], ['ongoing', 'completed'])) {
                echo json_encode(['status' => 'picked_up']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Could not update booking status. Please try again.']);
            }
        }
        break;

    // ── DRIVER: Mark passenger as dropped off (= complete) ────────────────
    case 'dropoff':
    case 'complete':
        if ($role !== 'driver') { echo json_encode(['status'=>'error','message'=>'Not a driver']); exit; }

        $stmt = $conn->prepare("
            UPDATE bookings
            SET status = 'completed', dropped_off_at = NOW(), completed_at = NOW()
            WHERE id = ? AND driver_id = ? AND status IN ('accepted','ongoing')
        ");
        $stmt->bind_param('ii', $bid, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $s2 = $conn->prepare("SELECT passenger_id, fare, payment_method FROM bookings WHERE id = ?");
            $s2->bind_param('i', $bid);
            $s2->execute();
            $b = $s2->get_result()->fetch_assoc();

            // Record transaction
            $s3 = $conn->prepare("INSERT INTO transactions (booking_id, payer_id, receiver_id, amount, method, status) VALUES (?,?,?,?,?,'paid')");
            $s3->bind_param('iiiis', $bid, $b['passenger_id'], $user_id, $b['fare'], $b['payment_method']);
            $s3->execute();

            // Update ride counts
            $conn->query("UPDATE users SET total_rides = total_rides + 1 WHERE id = $user_id");
            $conn->query("UPDATE users SET total_rides = total_rides + 1 WHERE id = {$b['passenger_id']}");

            // Notify passenger via system message
            $notifStmt = $conn->prepare("INSERT INTO messages (booking_id, sender_id, message) VALUES (?, ?, ?)");
            $sysMsg = '🏁 SYSTEM: You have been dropped off! Thank you for riding with Aurora Tri-Go. Have a safe day! 😊';
            $notifStmt->bind_param('iis', $bid, $user_id, $sysMsg);
            $notifStmt->execute();

            echo json_encode(['status' => 'completed', 'fare' => $b['fare'], 'payment_method' => $b['payment_method']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not complete booking']);
        }
        break;

    // ── PASSENGER: Book a ride ────────────────────────────────────────────
    case 'book':
        if ($role !== 'passenger') { echo json_encode(['status'=>'error','message'=>'Not a passenger']); exit; }

        $pickup_addr  = trim($_POST['pickup_address']  ?? '');
        $pickup_lat   = (float)($_POST['pickup_lat']   ?? 0);
        $pickup_lng   = (float)($_POST['pickup_lng']   ?? 0);
        $dropoff_addr = trim($_POST['dropoff_address'] ?? '');
        $dropoff_lat  = (float)($_POST['dropoff_lat']  ?? 0);
        $dropoff_lng  = (float)($_POST['dropoff_lng']  ?? 0);
        $distance     = (float)($_POST['distance_km']  ?? 0);
        $fare         = (float)($_POST['fare']         ?? 0);
        $payment      = $_POST['payment_method']       ?? 'cash';
        $notes        = trim($_POST['notes']           ?? '');

        if (!$pickup_addr || !$dropoff_addr) {
            echo json_encode(['status' => 'error', 'message' => 'Missing pickup or dropoff']);
            exit;
        }

        // ── Baler-only service area validation ──────────────────────────────
        // Baler, Aurora bounding box
        define('BALER_LAT_MIN', 15.69); define('BALER_LAT_MAX', 15.82);
        define('BALER_LNG_MIN', 121.52); define('BALER_LNG_MAX', 121.65);

        $pickupInBaler  = $pickup_lat  >= BALER_LAT_MIN && $pickup_lat  <= BALER_LAT_MAX
                       && $pickup_lng  >= BALER_LNG_MIN && $pickup_lng  <= BALER_LNG_MAX;
        $dropoffInBaler = $dropoff_lat >= BALER_LAT_MIN && $dropoff_lat <= BALER_LAT_MAX
                       && $dropoff_lng >= BALER_LNG_MIN && $dropoff_lng <= BALER_LNG_MAX;

        if (!$pickupInBaler) {
            echo json_encode(['status' => 'error', 'message' => 'Pickup location is outside the Baler service area.']);
            exit;
        }
        if (!$dropoffInBaler) {
            echo json_encode(['status' => 'error', 'message' => 'Drop-off location is outside the Baler service area.']);
            exit;
        }

        // Cancel any existing pending booking first
        $cancelOld = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE passenger_id=? AND status='pending'");
        $cancelOld->bind_param('i', $user_id);
        $cancelOld->execute();

        $stmt = $conn->prepare("
            INSERT INTO bookings
              (passenger_id, pickup_address, pickup_lat, pickup_lng,
               dropoff_address, dropoff_lat, dropoff_lng, distance_km, fare, payment_method, notes, status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,'pending')
        ");
        $stmt->bind_param('isddsddddss',
            $user_id, $pickup_addr, $pickup_lat, $pickup_lng,
            $dropoff_addr, $dropoff_lat, $dropoff_lng,
            $distance, $fare, $payment, $notes
        );
        $stmt->execute();
        $new_id = $conn->insert_id;
        echo json_encode(['status' => 'booked', 'booking_id' => $new_id]);
        break;

    // ── PASSENGER: Cancel ─────────────────────────────────────────────────
    case 'cancel':
        if ($role !== 'passenger') { echo json_encode(['status'=>'error','message'=>'Not a passenger']); exit; }

        // Grab driver_id before cancelling so we can send notification
        $preCheck = $conn->prepare("SELECT driver_id FROM bookings WHERE id = ? AND passenger_id = ? AND status IN ('pending','accepted')");
        $preCheck->bind_param('ii', $bid, $user_id);
        $preCheck->execute();
        $preRow   = $preCheck->get_result()->fetch_assoc();
        $notifyDriverId = $preRow ? $preRow['driver_id'] : null;

        $stmt = $conn->prepare("
            UPDATE bookings SET status = 'cancelled', cancelled_by = 'passenger'
            WHERE id = ? AND passenger_id = ? AND status IN ('pending','accepted')
        ");
        $stmt->bind_param('ii', $bid, $user_id);
        $stmt->execute();

        // Insert system message so driver's chat poll delivers the notification
        if ($stmt->affected_rows > 0 && $notifyDriverId) {
            $notifStmt = $conn->prepare("INSERT INTO messages (booking_id, sender_id, message) VALUES (?, ?, ?)");
            $sysMsg = '🚫 SYSTEM: The passenger has cancelled this booking.';
            $notifStmt->bind_param('iis', $bid, $user_id, $sysMsg);
            $notifStmt->execute();
        }
        echo json_encode(['status' => 'cancelled', 'affected' => $stmt->affected_rows]);
        break;

    // ── DRIVER: Cancel an accepted booking ────────────────────────────────
    case 'driver_cancel':
        if ($role !== 'driver') { echo json_encode(['status'=>'error','message'=>'Not a driver']); exit; }

        $stmt = $conn->prepare("
            UPDATE bookings SET status = 'cancelled', cancelled_by = 'driver'
            WHERE id = ? AND driver_id = ? AND status IN ('accepted','ongoing')
        ");
        $stmt->bind_param('ii', $bid, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Insert system message so passenger's poll delivers the notification
            $notifStmt = $conn->prepare("INSERT INTO messages (booking_id, sender_id, message) VALUES (?, ?, ?)");
            $sysMsg = '🚫 SYSTEM: Your driver has cancelled this booking. Please book a new ride.';
            $notifStmt->bind_param('iis', $bid, $user_id, $sysMsg);
            $notifStmt->execute();
            echo json_encode(['status' => 'cancelled']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not cancel booking']);
        }
        break;

    // ── PASSENGER: Poll booking status ────────────────────────────────────
    case 'poll':
        $stmt = $conn->prepare("
            SELECT b.status, b.driver_id, b.fare, b.payment_method,
                   b.pickup_address, b.dropoff_address,
                   b.pickup_lat, b.pickup_lng, b.dropoff_lat, b.dropoff_lng,
                   b.picked_up_at, b.dropped_off_at, b.cancelled_by,
                   d_u.first_name AS driver_first, d_u.last_name AS driver_last,
                   d_u.rating AS driver_rating, d_u.total_rides AS driver_rides,
                   d_u.phone AS driver_phone,
                   dd.plate_number, dd.tricycle_color, dd.current_lat, dd.current_lng
            FROM bookings b
            LEFT JOIN users d_u ON d_u.id = b.driver_id
            LEFT JOIN driver_details dd ON dd.user_id = b.driver_id
            WHERE b.id = ? AND b.passenger_id = ?
        ");
        $stmt->bind_param('ii', $bid, $user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        echo json_encode($row ? ['status' => 'ok', 'booking' => $row] : ['status' => 'not_found']);
        break;

    // ── DRIVER: Poll active booking status (for cancellation notification) ─
    case 'driver_poll':
        if ($role !== 'driver') { echo json_encode(['status'=>'error','message'=>'Not a driver']); exit; }
        $stmt = $conn->prepare("
            SELECT b.status, b.cancelled_by, b.passenger_id
            FROM bookings b
            WHERE b.id = ? AND b.driver_id = ?
        ");
        $stmt->bind_param('ii', $bid, $user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        echo json_encode($row ? ['status' => 'ok', 'booking' => $row] : ['status' => 'not_found']);
        break;

    // ── PASSENGER/DRIVER: Submit rating ──────────────────────────────────
    case 'rate':
        $rated_id = (int)($_POST['rated_id'] ?? 0);
        $score    = (int)($_POST['score']    ?? 5);
        $comment  = trim($_POST['comment']   ?? '');

        if (!$rated_id || !$bid) { echo json_encode(['status'=>'error']); exit; }

        $check = $conn->prepare("SELECT id FROM ratings WHERE booking_id=? AND rater_id=?");
        $check->bind_param('ii', $bid, $user_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(['status' => 'already_rated']); exit;
        }

        $stmt = $conn->prepare("INSERT INTO ratings (booking_id, rater_id, rated_id, score) VALUES (?,?,?,?)");
        $stmt->bind_param('iiii', $bid, $user_id, $rated_id, $score);
        $stmt->execute();

        $conn->query("UPDATE users SET rating = (SELECT AVG(score) FROM ratings WHERE rated_id = $rated_id) WHERE id = $rated_id");
        echo json_encode(['status' => 'rated']);
        break;

    // ── DRIVER: Update online status ──────────────────────────────────────
    case 'set_online':
        if ($role !== 'driver') { echo json_encode(['status'=>'error','message'=>'Not a driver']); exit; }
        $online = (int)($_POST['online'] ?? 0);
        $stmt = $conn->prepare("UPDATE driver_details SET is_online = ? WHERE user_id = ?");
        $stmt->bind_param('ii', $online, $user_id);
        $stmt->execute();
        echo json_encode(['status' => 'ok', 'online' => $online]);
        break;

    // ── DRIVER: Update GPS location ───────────────────────────────────────
    case 'update_location':
        if ($role !== 'driver') { echo json_encode(['status'=>'error','message'=>'Not a driver']); exit; }
        $lat = (float)($_POST['lat'] ?? 0);
        $lng = (float)($_POST['lng'] ?? 0);
        if ($lat && $lng) {
            $stmt = $conn->prepare("UPDATE driver_details SET current_lat=?, current_lng=? WHERE user_id=?");
            $stmt->bind_param('ddi', $lat, $lng, $user_id);
            $stmt->execute();
        }
        echo json_encode(['status' => 'ok']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
}