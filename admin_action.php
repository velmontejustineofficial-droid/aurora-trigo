<?php
// admin_action.php – Aurora Tri-Go
// Handles all admin moderation actions via POST
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

// ── Auth Guard ──────────────────────────────────────────────
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action  = trim($_POST['action']  ?? '');
$user_id = (int) ($_POST['user_id'] ?? 0);
$reason  = trim($_POST['reason']  ?? '');

if (!$action || !$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

// Prevent admin from acting on themselves
if ($user_id === (int) $_SESSION['user_id']) {
    echo json_encode(['status' => 'error', 'message' => 'Cannot perform action on your own account']);
    exit;
}

// ── Verify target user exists and is not admin ──────────────
$chk = $conn->prepare("SELECT id, first_name, last_name, role FROM users WHERE id = ?");
$chk->bind_param('i', $user_id);
$chk->execute();
$target = $chk->get_result()->fetch_assoc();

if (!$target) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}
if ($target['role'] === 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Cannot moderate another admin']);
    exit;
}

// ── Actions ─────────────────────────────────────────────────
switch ($action) {

    // ── BAN ─────────────────────────────────────────────────
    case 'ban':
        $banReason = $reason ?: 'Violated Terms of Service';
        $stmt = $conn->prepare("
            UPDATE users
            SET is_active  = 0,
                ban_reason = ?,
                banned_at  = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param('si', $banReason, $user_id);
        $stmt->execute();

        // Notify the user they have been banned
        $notifTitle = '🚫 Account Banned';
        $notifBody  = "Your account has been banned. Reason: {$banReason}";
        $notif = $conn->prepare("INSERT INTO notifications (user_id, title, body) VALUES (?, ?, ?)");
        $notif->bind_param('iss', $user_id, $notifTitle, $notifBody);
        $notif->execute();

        echo json_encode(['status' => 'ok', 'message' => 'User banned successfully']);
        break;

    // ── UNBAN ────────────────────────────────────────────────
    case 'unban':
        $stmt = $conn->prepare("
            UPDATE users
            SET is_active  = 1,
                ban_reason = NULL,
                banned_at  = NULL
            WHERE id = ?
        ");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();

        // Notify the user their account has been reinstated
        $notifTitle = '✅ Account Reinstated';
        $notifBody  = 'Your account ban has been lifted. You may now use the app again. Please follow our community guidelines.';
        $notif = $conn->prepare("INSERT INTO notifications (user_id, title, body) VALUES (?, ?, ?)");
        $notif->bind_param('iss', $user_id, $notifTitle, $notifBody);
        $notif->execute();

        echo json_encode(['status' => 'ok', 'message' => 'User unbanned successfully']);
        break;

    // ── WARN ─────────────────────────────────────────────────
    case 'warn':
        $warnReason = $reason ?: 'Conduct warning issued by admin';

        // ── Step 1: Increment warning count ──────────────────
        $stmt = $conn->prepare("UPDATE users SET warnings = warnings + 1 WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();

        // ── Step 2: Fetch updated warning count ──────────────
        $warnStmt = $conn->prepare("SELECT warnings FROM users WHERE id = ?");
        $warnStmt->bind_param('i', $user_id);
        $warnStmt->execute();
        $warnRow      = $warnStmt->get_result()->fetch_assoc();
        $newWarnings  = (int) $warnRow['warnings'];

        // ── Step 3: Decide whether to auto-ban ───────────────
        $autoBanned = false;

        if ($newWarnings >= 3) {
            // Auto-ban the user
            $autoBanReason = "Automatically banned after reaching {$newWarnings} warnings.";
            $banStmt = $conn->prepare("
                UPDATE users
                SET is_active  = 0,
                    ban_reason = ?,
                    banned_at  = NOW()
                WHERE id = ?
            ");
            $banStmt->bind_param('si', $autoBanReason, $user_id);
            $banStmt->execute();
            $autoBanned = true;
        }

        // ── Step 4: Send in-app notification to the user ─────
        if ($autoBanned) {
            // Warning + auto-ban notification
            $notifTitle = '🚫 Account Banned – Warning Limit Reached';
            $notifBody  = "You have received warning #{$newWarnings}: {$warnReason}\n\n"
                        . "You have been automatically banned for reaching {$newWarnings} warnings. "
                        . "Please contact support if you believe this is a mistake.";
        } else {
            // Plain warning notification with remaining allowance
            $remaining   = 3 - $newWarnings;
            $notifTitle  = "⚠️ Account Warning ({$newWarnings}/3)";
            $notifBody   = "You have received an official warning: {$warnReason}\n\n"
                         . "You have {$newWarnings} out of 3 allowed warnings. "
                         . "Your account will be automatically banned if you receive {$remaining} more warning"
                         . ($remaining === 1 ? '.' : 's.');
        }

        $notif = $conn->prepare("INSERT INTO notifications (user_id, title, body) VALUES (?, ?, ?)");
        $notif->bind_param('iss', $user_id, $notifTitle, $notifBody);
        $notif->execute();

        // ── Step 5: Build response payload ───────────────────
        $response = [
            'status'      => 'ok',
            'message'     => $autoBanned
                                ? "Warning issued and user automatically banned (reached {$newWarnings} warnings)"
                                : 'Warning issued successfully',
            'warnings'    => $newWarnings,
            'auto_banned' => $autoBanned,
        ];

        echo json_encode($response);
        break;

    // ── DELETE ───────────────────────────────────────────────
    case 'delete':
        // Hard delete – permanently remove user and all related data
        $conn->begin_transaction();

        try {
            // 1. Delete driver details if user is a driver
            $dd = $conn->prepare("DELETE FROM driver_details WHERE user_id = ?");
            $dd->bind_param('i', $user_id);
            $dd->execute();

            // 2. Delete user's notifications
            $notif = $conn->prepare("DELETE FROM notifications WHERE user_id = ?");
            $notif->bind_param('i', $user_id);
            $notif->execute();

            // 3. Delete messages sent by user
            $msg = $conn->prepare("DELETE FROM messages WHERE sender_id = ?");
            $msg->bind_param('i', $user_id);
            $msg->execute();

            // 4. Delete ratings given by or for this user
            $rat = $conn->prepare("DELETE FROM ratings WHERE rater_id = ? OR rated_id = ?");
            $rat->bind_param('ii', $user_id, $user_id);
            $rat->execute();

            // 5. Delete bookings where user is passenger or driver
            $bkDel = $conn->prepare("DELETE FROM bookings WHERE passenger_id = ? OR driver_id = ?");
            $bkDel->bind_param('ii', $user_id, $user_id);
            $bkDel->execute();

            // 6. Finally, delete the user
            $userDel = $conn->prepare("DELETE FROM users WHERE id = ?");
            $userDel->bind_param('i', $user_id);
            $userDel->execute();

            $conn->commit();
            echo json_encode(['status' => 'ok', 'message' => 'User account permanently deleted']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete user: ' . $e->getMessage()]);
        }
        break;

    // ── ADD USER ─────────────────────────────────────────────
    case 'add_user':
        $fname    = trim($_POST['first_name']  ?? '');
        $lname    = trim($_POST['last_name']   ?? '');
        $email    = trim($_POST['email']       ?? '') ?: null;
        $phone    = trim($_POST['phone']       ?? '');
        $password = trim($_POST['password']    ?? '');
        $role     = trim($_POST['new_role']    ?? 'passenger');

        if (!$fname || !$lname || !$phone || !$password || !in_array($role, ['driver', 'passenger'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            break;
        }

        // Check phone uniqueness
        $chkP = $conn->prepare("SELECT id FROM users WHERE phone = ?");
        $chkP->bind_param('s', $phone);
        $chkP->execute();
        if ($chkP->get_result()->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Phone number already registered']);
            break;
        }

        $hash = hash('sha256', $password);
        $ins  = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role) VALUES (?,?,?,?,?,?)");
        $ins->bind_param('ssssss', $fname, $lname, $email, $phone, $hash, $role);

        if ($ins->execute()) {
            $newId = $conn->insert_id;
            if ($role === 'driver') {
                $dd = $conn->prepare("INSERT INTO driver_details (user_id) VALUES (?)");
                $dd->bind_param('i', $newId);
                $dd->execute();
            }
            echo json_encode(['status' => 'ok', 'message' => 'User created successfully', 'new_id' => $newId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create user']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
}
