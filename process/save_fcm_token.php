<?php
// process/save_fcm_token.php - DEBUG VERSION
require_once '../config/database.php';

session_start();
header('Content-Type: application/json');

// Enable CORS for localhost
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');

// Debug logging
error_log("=== FCM TOKEN SAVE ATTEMPT ===");
error_log("Session data: " . json_encode($_SESSION));
error_log("POST raw: " . file_get_contents('php://input'));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("❌ USER NOT LOGGED IN - No user_id in session");
    echo json_encode(['status' => 'error', 'message' => 'User not logged in. Please login first.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Get the token from POST data
$input = json_decode(file_get_contents('php://input'), true);
$token = $input['token'] ?? '';
$user_id = $_SESSION['user_id'];

error_log("User ID from session: " . $user_id);
error_log("Token received: " . $token);

if (empty($token)) {
    error_log("❌ NO TOKEN PROVIDED");
    echo json_encode(['status' => 'error', 'message' => 'No token provided']);
    exit;
}

try {
    // First check if user exists
    $check_stmt = $conn->prepare("SELECT id FROM members WHERE id = ?");
    $check_stmt->execute([$user_id]);
    $user_exists = $check_stmt->fetch();

    if (!$user_exists) {
        error_log("❌ USER DOES NOT EXIST IN DATABASE: " . $user_id);
        echo json_encode(['status' => 'error', 'message' => 'User not found in database']);
        exit;
    }

    // Update the member's FCM token
    $stmt = $conn->prepare("UPDATE members SET fcm_token = ? WHERE id = ?");
    $result = $stmt->execute([$token, $user_id]);

    $affectedRows = $stmt->rowCount();
    error_log("✅ Database update - Affected rows: " . $affectedRows);

    if ($affectedRows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Token saved successfully',
            'user_id' => $user_id,
            'affected_rows' => $affectedRows
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No rows affected - user may not exist',
            'user_id' => $user_id
        ]);
    }

} catch (Exception $e) {
    error_log("❌ DATABASE ERROR: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>