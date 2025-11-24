<?php
// user_process/user_water_duty.php - Water Duty API with PDO

// Include database connection
require_once __DIR__ . '/../config/database.php';

class UserWaterDuty
{
    private $db;

    public function __construct()
    {
        try {
            $db_instance = new Database();
            $this->db = $db_instance->getConnection();
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Simple update user presence status using PDO
     */
    public function updateUserPresence($user_id, $is_present)
    {
        $response = ['success' => false, 'message' => ''];

        try {
            $is_present = $is_present ? 1 : 0;

            $stmt = $this->db->prepare("UPDATE members SET is_active = ? WHERE id = ?");
            $result = $stmt->execute([$is_present, $user_id]);

            if ($result && $stmt->rowCount() > 0) {
                $response['success'] = true;
                $response['message'] = $is_present ?
                    'You are now present in the house' :
                    'You are now absent from the house';
            } else {
                $response['message'] = 'No changes made or user not found';
            }

        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
            error_log("Presence API Error: " . $e->getMessage());
        }

        return $response;
    }

    /**
     * Confirm water duty completion
     */
    public function confirmWaterDuty($duty_id, $user_id)
    {
        $response = ['success' => false, 'message' => ''];

        try {
            // First check if duty exists and belongs to user
            $check_stmt = $this->db->prepare("SELECT id, status FROM water_duties WHERE id = ? AND member_id = ?");
            $check_stmt->execute([$duty_id, $user_id]);
            $duty = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$duty) {
                $response['message'] = 'Water duty not found or does not belong to you';
                return $response;
            }

            if ($duty['status'] === 'Completed') {
                $response['message'] = 'Water duty already completed';
                $response['data'] = ['already_completed' => true];
                return $response;
            }

            // Update duty status to Completed
            $update_stmt = $this->db->prepare("UPDATE water_duties SET status = 'Completed', completed_at = NOW() WHERE id = ? AND member_id = ?");
            $result = $update_stmt->execute([$duty_id, $user_id]);

            if ($result && $update_stmt->rowCount() > 0) {
                $response['success'] = true;
                $response['message'] = 'Water duty confirmed successfully!';
                $response['data'] = [
                    'duty_id' => $duty_id,
                    'status' => 'Completed'
                ];
            } else {
                $response['message'] = 'Failed to update water duty status';
            }

        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
            error_log("Water Duty API Error: " . $e->getMessage());
        }

        return $response;
    }

    /**
     * Get water duty statistics for user
     */
    public function getDutyStatistics($user_id)
    {
        $response = ['success' => false, 'message' => '', 'data' => null];

        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_duties,
                    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_duties,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_duties,
                    CASE 
                        WHEN COUNT(*) > 0 THEN ROUND((SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 1)
                        ELSE 0 
                    END as completion_rate
                FROM water_duties 
                WHERE member_id = ?
            ");
            $stmt->execute([$user_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            $response['success'] = true;
            $response['data'] = $stats ?: [
                'total_duties' => 0,
                'completed_duties' => 0,
                'pending_duties' => 0,
                'completion_rate' => 0
            ];

        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }

        return $response;
    }
}

// API Handler
if (isset($_GET['action'])) {

    // Start session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    header('Content-Type: application/json');

    // Check login
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    try {
        $waterDutyAPI = new UserWaterDuty();

        switch ($_GET['action']) {
            case 'update_presence':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['is_present'])) {
                    $is_present = $_POST['is_present'] === '1' ? 1 : 0;
                    $result = $waterDutyAPI->updateUserPresence($user_id, $is_present);
                    echo json_encode($result);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid request']);
                }
                break;

            case 'confirm_duty':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duty_id'])) {
                    $duty_id = (int) $_POST['duty_id'];
                    $result = $waterDutyAPI->confirmWaterDuty($duty_id, $user_id);
                    echo json_encode($result);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid request']);
                }
                break;

            case 'get_statistics':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $result = $waterDutyAPI->getDutyStatistics($user_id);
                    echo json_encode($result);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
                }
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Action not found']);
                break;
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}
?>