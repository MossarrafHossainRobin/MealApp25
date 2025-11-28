<?php
// user_section/notification_ajax.php

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Include database connection
require_once __DIR__ . '/../config/database.php';

// Set JSON header
header('Content-Type: application/json');

// Handle mark as read actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_instance = new Database();
    $conn = $db_instance->getConnection();
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['mark_as_read']) && isset($_POST['notification_id']) && isset($_POST['notification_type'])) {
        $notification_id = $_POST['notification_id'];
        $notification_type = $_POST['notification_type'];

        try {
            $success = false;

            if ($notification_type === 'transaction') {
                $stmt = $conn->prepare("UPDATE transaction_logs SET read_status = TRUE WHERE id = ?");
                $success = $stmt->execute([$notification_id]);
            } elseif ($notification_type === 'bazar_request') {
                $stmt = $conn->prepare("UPDATE bazar_requests SET read_status = TRUE WHERE id = ?");
                $success = $stmt->execute([$notification_id]);
            } elseif ($notification_type === 'bazar_approved') {
                $stmt = $conn->prepare("UPDATE bazar SET notification_read = TRUE WHERE id = ?");
                $success = $stmt->execute([$notification_id]);
            }

            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database update failed']);
            }
            exit;

        } catch (PDOException $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    // Mark all as read
    if (isset($_POST['mark_all_read'])) {
        try {
            // Mark all transaction notifications as read
            $stmt1 = $conn->prepare("
                UPDATE transaction_logs 
                SET read_status = TRUE 
                WHERE read_status = FALSE 
                AND id IN (
                    SELECT tl.id 
                    FROM transaction_logs tl
                    INNER JOIN transactions t ON tl.transaction_id = t.id
                    WHERE (t.sender_id = ? OR t.receiver_id = ?)
                )
            ");
            $stmt1->execute([$user_id, $user_id]);
            $transaction_updated = $stmt1->rowCount();

            // Mark all bazar request notifications as read
            $stmt2 = $conn->prepare("
                UPDATE bazar_requests 
                SET read_status = TRUE 
                WHERE member_id = ? 
                AND read_status = FALSE
            ");
            $stmt2->execute([$user_id]);
            $bazar_request_updated = $stmt2->rowCount();

            // Mark all bazar approved notifications as read
            $stmt3 = $conn->prepare("
                UPDATE bazar 
                SET notification_read = TRUE 
                WHERE member_id = ? 
                AND notification_read = FALSE
            ");
            $stmt3->execute([$user_id]);
            $bazar_approved_updated = $stmt3->rowCount();

            $total_updated = $transaction_updated + $bazar_request_updated + $bazar_approved_updated;

            echo json_encode([
                'success' => true,
                'message' => "All notifications marked as read! ($total_updated updated)",
                'counts' => [
                    'transactions' => $transaction_updated,
                    'bazar_requests' => $bazar_request_updated,
                    'bazar_approved' => $bazar_approved_updated
                ]
            ]);
            exit;

        } catch (PDOException $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}

// If no valid action found
echo json_encode(['success' => false, 'error' => 'Invalid request']);
?>