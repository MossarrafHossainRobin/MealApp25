<?php
require_once '../config/database.php';
header('Content-Type: application/json');

class MealActions
{
    public $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    private function jsonResponse($success, $message = '', $data = null)
    {
        $response = [
            'status' => $success ? 'success' : 'error',
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    private function logActivity($action, $details)
    {
        error_log("MEAL_SYSTEM: " . date('Y-m-d H:i:s') . " - $action - " . json_encode($details));
    }

    /* ---------------- LOAD MEMBERS WITH FULL DETAILS ------------------- */
    public function loadMembers()
    {
        try {
            $sql = "SELECT id, name, email, is_active, created_at, base_rent, is_suspended, fcm_token 
                    FROM members 
                    WHERE is_active = 1 
                    ORDER BY name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->logActivity('LOAD_MEMBERS', ['count' => count($members)]);

            $this->jsonResponse(true, 'Members loaded successfully', ['members' => $members]);
        } catch (Exception $e) {
            $this->logActivity('LOAD_MEMBERS_ERROR', ['error' => $e->getMessage()]);
            $this->jsonResponse(false, 'Failed to load members: ' . $e->getMessage());
        }
    }

    /* ---------------- ADD MEAL ------------------- */
    public function addMeal($data)
    {
        // Validate required fields
        $required = ['member_id', 'meal_date', 'meal_count'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->jsonResponse(false, "Missing required field: $field");
            }
        }

        $member_id = intval($data['member_id']);
        $meal_date = $data['meal_date'];
        $meal_count = floatval($data['meal_count']);
        $description = $data['description'] ?? '';

        // Validate inputs
        if ($member_id <= 0) {
            $this->jsonResponse(false, "Invalid member ID");
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $meal_date)) {
            $this->jsonResponse(false, "Invalid date format. Use YYYY-MM-DD");
        }

        if ($meal_count < 0) {
            $this->jsonResponse(false, "Meal count cannot be negative");
        }

        // Check if member exists and is active
        $member = $this->getMemberDetails($member_id);
        if (!$member) {
            $this->jsonResponse(false, "Member does not exist");
        }
        if (!$member['is_active']) {
            $this->jsonResponse(false, "Member is not active");
        }

        $year = date('Y', strtotime($meal_date));
        $month = date('m', strtotime($meal_date));

        $this->logActivity('ADD_MEAL_START', [
            'member_id' => $member_id,
            'member_name' => $member['name'],
            'meal_date' => $meal_date,
            'meal_count' => $meal_count,
            'year' => $year,
            'month' => $month
        ]);

        try {
            $this->conn->beginTransaction();

            // Check for existing meal on same date for same member
            $existing_meal = $this->getExistingMeal($member_id, $meal_date);

            if ($existing_meal) {
                // Update existing meal
                $result = $this->updateExistingMeal($existing_meal['id'], $meal_count, $description, $year, $month, $member);
            } else {
                // Create new meal
                $result = $this->createNewMeal($member_id, $meal_count, $meal_date, $description, $year, $month, $member);
            }

            $this->conn->commit();

            $this->logActivity('ADD_MEAL_SUCCESS', $result);
            $this->jsonResponse(true, 'Meal saved successfully', $result);

        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logActivity('ADD_MEAL_ERROR', ['error' => $e->getMessage()]);
            $this->jsonResponse(false, 'Failed to save meal: ' . $e->getMessage());
        }
    }

    /* ---------------- GET MEMBER DETAILS ------------------- */
    private function getMemberDetails($member_id)
    {
        $sql = "SELECT id, name, email, is_active, base_rent, is_suspended 
                FROM members 
                WHERE id = :id 
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $member_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ---------------- GET EXISTING MEAL ------------------- */
    private function getExistingMeal($member_id, $meal_date)
    {
        $sql = "SELECT id, meal_count FROM meal_counts 
                WHERE member_id = :member_id AND meal_date = :meal_date 
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':member_id' => $member_id,
            ':meal_date' => $meal_date
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ---------------- UPDATE EXISTING MEAL ------------------- */
    private function updateExistingMeal($meal_id, $new_meal_count, $description, $year, $month, $member)
    {
        // Get current meal count first
        $sql = "SELECT meal_count, member_id FROM meal_counts WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $meal_id]);
        $current_meal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$current_meal) {
            throw new Exception("Meal not found");
        }

        $old_meal_count = floatval($current_meal['meal_count']);
        $member_id = $current_meal['member_id'];
        $difference = $new_meal_count - $old_meal_count;

        // Update the meal
        $update_sql = "UPDATE meal_counts SET 
                      meal_count = :meal_count,
                      description = :description,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($update_sql);
        $stmt->execute([
            ':meal_count' => $new_meal_count,
            ':description' => $description,
            ':id' => $meal_id
        ]);

        // Update monthly total
        $this->updateMonthlyTotal($member_id, $year, $month, $difference);

        // Get updated totals
        $monthly_total = $this->getMemberMonthlyTotal($member_id, $year, $month);
        $all_members_total = $this->getAllMembersMonthlyTotal($year, $month);

        return [
            'id' => $meal_id,
            'action' => 'updated',
            'member' => [
                'id' => $member['id'],
                'name' => $member['name'],
                'email' => $member['email']
            ],
            'old_count' => $old_meal_count,
            'new_count' => $new_meal_count,
            'difference' => $difference,
            'monthly_total' => $monthly_total,
            'all_members_total' => $all_members_total
        ];
    }

    /* ---------------- CREATE NEW MEAL ------------------- */
    private function createNewMeal($member_id, $meal_count, $meal_date, $description, $year, $month, $member)
    {
        $sql = "INSERT INTO meal_counts 
                (member_id, meal_count, meal_date, description, created_at) 
                VALUES (:member_id, :meal_count, :meal_date, :description, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':member_id' => $member_id,
            ':meal_count' => $meal_count,
            ':meal_date' => $meal_date,
            ':description' => $description
        ]);

        $meal_id = $this->conn->lastInsertId();

        // Update monthly total
        $this->updateMonthlyTotal($member_id, $year, $month, $meal_count);

        // Get updated totals
        $monthly_total = $this->getMemberMonthlyTotal($member_id, $year, $month);
        $all_members_total = $this->getAllMembersMonthlyTotal($year, $month);

        return [
            'id' => $meal_id,
            'action' => 'created',
            'member' => [
                'id' => $member['id'],
                'name' => $member['name'],
                'email' => $member['email']
            ],
            'meal_count' => $meal_count,
            'meal_date' => $meal_date,
            'monthly_total' => $monthly_total,
            'all_members_total' => $all_members_total
        ];
    }

    /* ---------------- UPDATE MONTHLY TOTAL ------------------- */
    private function updateMonthlyTotal($member_id, $year, $month, $difference)
    {
        // Check if monthly record exists
        $check_sql = "SELECT id, total_meals FROM monthly_meals 
                     WHERE member_id = :member_id AND year = :year AND month = :month";
        $check_stmt = $this->conn->prepare($check_sql);
        $check_stmt->execute([
            ':member_id' => $member_id,
            ':year' => $year,
            ':month' => $month
        ]);

        $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update existing record
            $new_total = floatval($existing['total_meals']) + $difference;
            $update_sql = "UPDATE monthly_meals SET 
                          total_meals = :total_meals, 
                          updated_at = NOW() 
                      WHERE id = :id";
            $update_stmt = $this->conn->prepare($update_sql);
            $update_stmt->execute([
                ':total_meals' => $new_total,
                ':id' => $existing['id']
            ]);
        } else {
            // Create new record
            $insert_sql = "INSERT INTO monthly_meals 
                          (member_id, year, month, total_meals, created_at) 
                          VALUES (:member_id, :year, :month, :total_meals, NOW())";
            $insert_stmt = $this->conn->prepare($insert_sql);
            $insert_stmt->execute([
                ':member_id' => $member_id,
                ':year' => $year,
                ':month' => $month,
                ':total_meals' => $difference
            ]);
        }
    }

    /* ---------------- GET MEMBER MONTHLY TOTAL ------------------- */
    private function getMemberMonthlyTotal($member_id, $year, $month)
    {
        $sql = "SELECT COALESCE(total_meals, 0) as total_meals 
                FROM monthly_meals 
                WHERE member_id = :member_id AND year = :year AND month = :month";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':member_id' => $member_id,
            ':year' => $year,
            ':month' => $month
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return floatval($result['total_meals'] ?? 0);
    }

    /* ---------------- GET ALL MEMBERS MONTHLY TOTAL ------------------- */
    private function getAllMembersMonthlyTotal($year, $month)
    {
        $sql = "SELECT COALESCE(SUM(total_meals), 0) as total_meals 
                FROM monthly_meals 
                WHERE year = :year AND month = :month";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':year' => $year,
            ':month' => $month
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return floatval($result['total_meals'] ?? 0);
    }

    /* ---------------- LOAD MEALS BY MONTH WITH MEMBER DETAILS ------------------- */
    public function loadMealsByMonth($month, $year)
    {
        // Validate inputs
        if (empty($month) || empty($year)) {
            $this->jsonResponse(false, "Month and year are required");
        }

        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        $year = intval($year);

        $this->logActivity('LOAD_MEALS_MONTH', ['month' => $month, 'year' => $year]);

        try {
            // Get meals for the month with full member details
            $sql = "SELECT mc.*, 
                           m.name as member_name, 
                           m.email as member_email,
                           m.base_rent as member_base_rent,
                           m.is_suspended as member_is_suspended
                    FROM meal_counts mc
                    JOIN members m ON mc.member_id = m.id
                    WHERE YEAR(mc.meal_date) = :year 
                    AND MONTH(mc.meal_date) = :month
                    ORDER BY mc.meal_date DESC, m.name ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':month' => $month,
                ':year' => $year
            ]);

            $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get monthly totals with member details
            $monthly_sql = "SELECT mm.member_id, mm.total_meals, 
                                   m.name as member_name,
                                   m.email as member_email,
                                   m.base_rent as member_base_rent,
                                   m.is_suspended as member_is_suspended
                           FROM monthly_meals mm
                           JOIN members m ON mm.member_id = m.id
                           WHERE mm.year = :year AND mm.month = :month
                           ORDER BY m.name ASC";

            $monthly_stmt = $this->conn->prepare($monthly_sql);
            $monthly_stmt->execute([
                ':year' => $year,
                ':month' => $month
            ]);

            $monthly_totals = $monthly_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate statistics
            $total_meals = 0;
            $member_totals = [];
            $unique_days = [];

            foreach ($monthly_totals as $total) {
                $total_meals += floatval($total['total_meals']);
                $member_totals[$total['member_id']] = [
                    'total_meals' => floatval($total['total_meals']),
                    'member_name' => $total['member_name'],
                    'member_email' => $total['member_email'],
                    'base_rent' => $total['member_base_rent'],
                    'is_suspended' => $total['member_is_suspended']
                ];
            }

            foreach ($meals as $meal) {
                $unique_days[$meal['meal_date']] = true;
            }

            $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $avg_per_day = $days_in_month > 0 ? round($total_meals / $days_in_month, 1) : 0;

            $result = [
                'meals' => $meals,
                'monthly_totals' => $monthly_totals,
                'member_totals' => $member_totals,
                'statistics' => [
                    'total_meals' => $total_meals,
                    'unique_days' => count($unique_days),
                    'total_days' => $days_in_month,
                    'avg_per_day' => $avg_per_day,
                    'total_records' => count($meals)
                ]
            ];

            $this->logActivity('LOAD_MEALS_SUCCESS', [
                'month' => $month,
                'year' => $year,
                'meals_count' => count($meals),
                'total_meals' => $total_meals
            ]);

            $this->jsonResponse(true, 'Meals loaded successfully', $result);

        } catch (Exception $e) {
            $this->logActivity('LOAD_MEALS_ERROR', ['error' => $e->getMessage()]);
            $this->jsonResponse(false, 'Failed to load meals: ' . $e->getMessage());
        }
    }

    /* ---------------- DELETE MEAL ------------------- */
    public function deleteMeal($meal_id)
    {
        $meal_id = intval($meal_id);

        if ($meal_id <= 0) {
            $this->jsonResponse(false, "Invalid meal ID");
        }

        $this->logActivity('DELETE_MEAL_START', ['meal_id' => $meal_id]);

        try {
            $this->conn->beginTransaction();

            // Get meal details with member info
            $sql = "SELECT mc.member_id, mc.meal_count, mc.meal_date, m.name as member_name
                    FROM meal_counts mc
                    JOIN members m ON mc.member_id = m.id
                    WHERE mc.id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $meal_id]);
            $meal = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$meal) {
                throw new Exception("Meal not found");
            }

            $member_id = $meal['member_id'];
            $meal_count = floatval($meal['meal_count']);
            $meal_date = $meal['meal_date'];
            $year = date('Y', strtotime($meal_date));
            $month = date('m', strtotime($meal_date));

            // Delete the meal
            $delete_sql = "DELETE FROM meal_counts WHERE id = :id";
            $delete_stmt = $this->conn->prepare($delete_sql);
            $delete_stmt->execute([':id' => $meal_id]);

            // Update monthly total (subtract the deleted count)
            $this->updateMonthlyTotal($member_id, $year, $month, -$meal_count);

            $this->conn->commit();

            $this->logActivity('DELETE_MEAL_SUCCESS', [
                'meal_id' => $meal_id,
                'member_id' => $member_id,
                'member_name' => $meal['member_name'],
                'meal_count' => $meal_count
            ]);

            $this->jsonResponse(true, 'Meal deleted successfully', [
                'deleted_meal' => $meal,
                'monthly_total' => $this->getMemberMonthlyTotal($member_id, $year, $month)
            ]);

        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->logActivity('DELETE_MEAL_ERROR', ['error' => $e->getMessage()]);
            $this->jsonResponse(false, 'Failed to delete meal: ' . $e->getMessage());
        }
    }

    /* ---------------- GET MEAL STATISTICS ------------------- */
    public function getMealStatistics($month, $year)
    {
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        $year = intval($year);

        try {
            // Get total members with details
            $members_sql = "SELECT COUNT(*) as total_members, 
                                   SUM(base_rent) as total_base_rent
                            FROM members 
                            WHERE is_active = 1 AND is_suspended = 0";
            $members_stmt = $this->conn->prepare($members_sql);
            $members_stmt->execute();
            $members_stats = $members_stmt->fetch(PDO::FETCH_ASSOC);

            // Get monthly statistics
            $stats_sql = "SELECT 
                         COUNT(DISTINCT mm.member_id) as active_members,
                         SUM(mm.total_meals) as total_meals,
                         COUNT(*) as records_count
                     FROM monthly_meals mm
                     JOIN members m ON mm.member_id = m.id
                     WHERE mm.year = :year AND mm.month = :month 
                     AND m.is_active = 1 AND m.is_suspended = 0";

            $stats_stmt = $this->conn->prepare($stats_sql);
            $stats_stmt->execute([':year' => $year, ':month' => $month]);
            $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

            $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $avg_per_day = $days_in_month > 0 ? round(($stats['total_meals'] ?? 0) / $days_in_month, 1) : 0;

            $result = [
                'total_members' => intval($members_stats['total_members'] ?? 0),
                'total_base_rent' => floatval($members_stats['total_base_rent'] ?? 0),
                'active_members' => intval($stats['active_members'] ?? 0),
                'total_meals' => floatval($stats['total_meals'] ?? 0),
                'records_count' => intval($stats['records_count'] ?? 0),
                'days_in_month' => $days_in_month,
                'avg_per_day' => $avg_per_day
            ];

            $this->jsonResponse(true, 'Statistics loaded successfully', $result);

        } catch (Exception $e) {
            $this->jsonResponse(false, 'Failed to load statistics: ' . $e->getMessage());
        }
    }
}

/* ---------- ROUTER ---------- */
try {
    $meal = new MealActions();
    $action = $_POST["action"] ?? $_GET["action"] ?? "";

    switch ($action) {
        case "load_members":
            $meal->loadMembers();
            break;

        case "add_meal":
            $meal->addMeal($_POST);
            break;

        case "load_meals_month":
            $month = $_POST["month"] ?? $_GET["month"] ?? "";
            $year = $_POST["year"] ?? $_GET["year"] ?? date('Y');
            $meal->loadMealsByMonth($month, $year);
            break;

        case "delete_meal":
            $meal_id = $_POST["id"] ?? $_GET["id"] ?? 0;
            $meal->deleteMeal($meal_id);
            break;

        case "get_statistics":
            $month = $_POST["month"] ?? $_GET["month"] ?? date('m');
            $year = $_POST["year"] ?? $_GET["year"] ?? date('Y');
            $meal->getMealStatistics($month, $year);
            break;

        default:
            echo json_encode([
                "status" => "error",
                "message" => "Invalid action specified",
                "available_actions" => [
                    "load_members",
                    "add_meal",
                    "load_meals_month",
                    "delete_meal",
                    "get_statistics"
                ]
            ], JSON_PRETTY_PRINT);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "System error: " . $e->getMessage(),
        "timestamp" => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>