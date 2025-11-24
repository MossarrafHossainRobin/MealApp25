<?php
// user_process/bazar_api.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

try {
    // Include required files
    require_once '../config/database.php';
    require_once 'user_actions.php';

    $db = new Database();
    $connection = $db->getConnection();
    $action = new UserActions();

    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $input = $_POST;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'get_bazar_requests':
                    getBazarRequests($connection, $_SESSION['user_id']);
                    break;

                case 'get_approved_contributions':
                    getApprovedContributions($connection, $_SESSION['user_id']);
                    break;

                case 'get_statistics':
                    getStatistics($action, $_SESSION['user_id']);
                    break;

                default:
                    submitBazarRequest($connection, $_SESSION['user_id'], $input);
            }
        } else {
            // Default action - submit bazar request
            submitBazarRequest($connection, $_SESSION['user_id'], $input);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    }

} catch (Exception $e) {
    error_log("Bazar API Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again.']);
}

function getBazarRequests($connection, $user_id)
{
    $stmt = $connection->prepare("
        SELECT id, amount, description, bazar_date, status, rejection_reason, created_at 
        FROM bazar_requests 
        WHERE member_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$user_id]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $requests]);
}

function getApprovedContributions($connection, $user_id)
{
    $stmt = $connection->prepare("
        SELECT id, amount, description, bazar_date, bazar_count, month 
        FROM bazar 
        WHERE member_id = ? 
        ORDER BY bazar_date DESC
        LIMIT 50
    ");
    $stmt->execute([$user_id]);
    $contributions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $contributions]);
}

function getStatistics($action, $user_id)
{
    $userData = $action->getUserData($user_id);

    $total_bazar = 0;
    foreach ($userData['bazar'] as $bazar) {
        $total_bazar += $bazar['amount'];
    }

    $total_records = count($userData['bazar']);
    $average_per_entry = $total_records > 0 ? $total_bazar / $total_records : 0;

    echo json_encode([
        'status' => 'success',
        'data' => [
            'total_bazar' => $total_bazar,
            'total_records' => $total_records,
            'average_per_entry' => $average_per_entry
        ]
    ]);
}

function submitBazarRequest($connection, $user_id, $input)
{
    $amount = floatval($input['amount'] ?? 0);
    $bazar_date = $input['bazar_date'] ?? '';
    $description = trim($input['description'] ?? '');

    // Validation
    if ($amount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid amount greater than 0.']);
        return;
    }

    if (empty($bazar_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Please select a date.']);
        return;
    }

    if (empty($description)) {
        echo json_encode(['status' => 'error', 'message' => 'Please provide a description of your purchase.']);
        return;
    }

    // Check if date is not in future
    if (strtotime($bazar_date) > time()) {
        echo json_encode(['status' => 'error', 'message' => 'Bazar date cannot be in the future.']);
        return;
    }

    try {
        $stmt = $connection->prepare("
            INSERT INTO bazar_requests (member_id, amount, description, bazar_date, status, created_at) 
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");

        $result = $stmt->execute([$user_id, $amount, $description, $bazar_date]);

        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Bazar request submitted successfully! Waiting for admin approval.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to submit bazar request. Please try again.']);
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again.']);
    }
}
?>