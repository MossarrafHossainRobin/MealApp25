<?php
session_start();

// **CRITICAL FIX:** Use __DIR__ for reliable path resolution.
// Assuming cost_api.php is in 'user_process/' and database.php is in 'config/'
// path is user_process/ -> (..) -> / -> config/database.php
require_once __DIR__ . '/../config/database.php';

// NOTE: You must have a 'Database' class defined in database.php 
// with a public method 'getConnection()' that returns a PDO object.

class CostAPI
{
    private $db;
    private $member_id;

    public function __construct()
    {
        // Initialize database connection
        $this->initializeDatabase();

        // Get member_id from session
        // Use a placeholder if necessary for testing, but ensure a real user is logged in
        $this->member_id = $_SESSION['user_id'] ?? null;

        if (!$this->member_id) {
            $this->sendResponse(['success' => false, 'error' => 'Unauthorized - Please login first'], 401);
        }
    }

    private function initializeDatabase()
    {
        try {
            // Use your existing Database class
            $database = new Database();
            $this->db = $database->getConnection();

        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'error' => 'Database connection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';

        try {
            switch ($method) {
                case 'GET':
                    $this->handleGet($action);
                    break;
                default:
                    $this->sendResponse(['success' => false, 'error' => 'Method not allowed'], 405);
            }
        } catch (Exception $e) {
            // Catch any unexpected exceptions during request handling
            $this->sendResponse([
                'success' => false,
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function handleGet($action)
    {
        switch ($action) {
            case 'get_current_month_cost':
                $this->getCurrentMonthCost();
                break;
            case 'get_cost_history':
                $this->getCostHistory();
                break;
            case 'test_connection':
                $this->testConnection();
                break;
            default:
                $this->sendResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
    }

    private function testConnection()
    {
        try {
            // Simple test query
            $stmt = $this->db->query("SELECT 1 as test");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->sendResponse([
                'success' => true,
                'message' => 'Database connection successful',
                'member_id' => $this->member_id,
                'test_result' => $result
            ]);
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'error' => 'Database test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCurrentMonthCost()
    {
        // Get the current month in YYYY-MM format
        $current_month = date('Y-m');

        // Allow optional month/year override for navigation testing
        $target_month = $_GET['month_year'] ?? $current_month;

        try {
            $stmt = $this->db->prepare("
                SELECT mfca.*, mfc.month_year, mfc.total_amount, mfc.per_member_cost
                FROM member_flat_cost_allocations mfca
                LEFT JOIN monthly_flat_costs mfc ON mfca.monthly_cost_id = mfc.id
                WHERE mfca.member_id = ? 
                AND DATE_FORMAT(mfc.month_year, '%Y-%m') = ?
                ORDER BY mfc.month_year DESC 
                LIMIT 1
            ");
            $stmt->execute([$this->member_id, $target_month]);
            $cost = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return null if no cost is found for the month
            if (!$cost) {
                $this->sendResponse([
                    'success' => true,
                    'current_month_cost' => null,
                    'current_month' => $target_month
                ]);
                return;
            }

            $this->sendResponse([
                'success' => true,
                'current_month_cost' => $cost,
                'current_month' => $target_month
            ]);

        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'error' => 'Failed to fetch current month cost: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCostHistory()
    {
        // Ensure limit is an integer and not too large
        $limit = intval($_GET['limit'] ?? 5);
        if ($limit <= 0 || $limit > 50)
            $limit = 5;

        try {
            // PDO execute requires passing limit as a string or using bindValue for INT
            $stmt = $this->db->prepare("
                SELECT mfca.*, mfc.month_year, mfc.total_amount, mfc.per_member_cost
                FROM member_flat_cost_allocations mfca
                LEFT JOIN monthly_flat_costs mfc ON mfca.monthly_cost_id = mfc.id
                WHERE mfca.member_id = ? 
                ORDER BY mfc.month_year DESC 
                LIMIT ?
            ");
            // Use bindValue to ensure LIMIT is treated as an integer
            $stmt->bindValue(1, $this->member_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate summary stats
            $total_due = 0.0;
            $pending_costs = 0;

            foreach ($history as $cost) {
                // Ensure array keys exist and values are convertible
                $amount_due = floatval($cost['amount_due'] ?? 0);
                $status = $cost['status'] ?? 'Paid';

                if (strtolower($status) === 'pending') {
                    $total_due += $amount_due;
                    $pending_costs++;
                }
            }

            $this->sendResponse([
                'success' => true,
                'cost_history' => $history,
                'summary' => [
                    'total_due' => $total_due,
                    'pending_bills' => $pending_costs,
                    'total_bills' => count($history)
                ],
                'total_records' => count($history)
            ]);

        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'error' => 'Failed to fetch cost history: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// Handle the API request
try {
    $api = new CostAPI();
    $api->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'API initialization failed: ' . $e->getMessage()
    ]);
}
?>