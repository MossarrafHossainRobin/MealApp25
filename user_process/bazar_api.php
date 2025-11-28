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
                    getStatistics($connection, $_SESSION['user_id']);
                    break;

                case 'get_highest_bearer':
                    getHighestBearer($connection);
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
        SELECT id, amount, description, bazar_date, bazar_count, month, year
        FROM bazar 
        WHERE member_id = ? 
        ORDER BY bazar_date DESC
        LIMIT 50
    ");
    $stmt->execute([$user_id]);
    $contributions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $contributions]);
}

function getStatistics($connection, $user_id)
{
    // Get current month and year
    $current_month = date('m');
    $current_year = date('Y');

    // Get total bazar for current month from bazar table (approved entries)
    $stmt = $connection->prepare("
        SELECT COALESCE(SUM(amount), 0) as total_bazar, 
               COUNT(*) as total_records 
        FROM bazar 
        WHERE member_id = ? 
        AND month = ? 
        AND year = ?
    ");
    $stmt->execute([$user_id, $current_month, $current_year]);
    $user_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_bazar = floatval($user_stats['total_bazar']);
    $total_records = intval($user_stats['total_records']);
    $average_per_entry = $total_records > 0 ? $total_bazar / $total_records : 0;

    // Get highest bearer for current month from bazar table
    $highest_bearer = getHighestBearerData($connection, $current_month, $current_year);

    echo json_encode([
        'status' => 'success',
        'data' => [
            'total_bazar' => $total_bazar,
            'total_records' => $total_records,
            'average_per_entry' => $average_per_entry,
            'highest_bearer' => $highest_bearer,
            'current_month' => date('F Y')
        ]
    ]);
}

function getHighestBearer($connection)
{
    $current_month = date('m');
    $current_year = date('Y');

    $highest_bearer = getHighestBearerData($connection, $current_month, $current_year);

    echo json_encode([
        'status' => 'success',
        'data' => $highest_bearer
    ]);
}

function getHighestBearerData($connection, $month, $year)
{
    $stmt = $connection->prepare("
        SELECT m.name, m.email, SUM(b.amount) as total_amount
        FROM bazar b
        JOIN members m ON b.member_id = m.id
        WHERE b.month = ? 
        AND b.year = ?
        AND m.is_active = 1
        AND m.is_suspended = 0
        GROUP BY b.member_id, m.name, m.email
        ORDER BY total_amount DESC
        LIMIT 1
    ");

    $stmt->execute([$month, $year]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['total_amount'] > 0) {
        return [
            'name' => $result['name'] ?: explode('@', $result['email'])[0], // Use name or email username
            'amount' => floatval($result['total_amount'])
        ];
    }

    return [
        'name' => 'No data available',
        'amount' => 0
    ];
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
    $selected_date = strtotime($bazar_date);
    $today = strtotime(date('Y-m-d'));
    if ($selected_date > $today) {
        echo json_encode(['status' => 'error', 'message' => 'Bazar date cannot be in the future.']);
        return;
    }

    try {
        // Extract month and year from bazar_date for potential future use
        $month = date('m', $selected_date);
        $year = date('Y', $selected_date);

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

        // Check for specific error types
        if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid user account. Please contact administrator.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again.']);
        }
    }
}
?>