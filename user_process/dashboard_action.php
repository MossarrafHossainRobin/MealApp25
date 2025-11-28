<?php
// user_process/dashboard_action.php - Dashboard specific data fetching

require_once __DIR__ . '/../config/database.php';

class DashboardAction
{
    private $db;

    public function __construct()
    {
        try {
            $db_instance = new Database();
            $this->db = $db_instance->getConnection();
        } catch (\PDOException $e) {
            error_log("Dashboard DB Error: " . $e->getMessage());
            $this->db = null;
        }
    }

    public function getDashboardData($member_id)
    {
        if (!$member_id || !$this->db) {
            return $this->getEmptyData();
        }

        try {
            $data = [];
            $current_year = date('Y');
            $current_month = date('m');
            $current_year_month = date('Y-m');

            // 1. Get member info and base_rent (for logged-in user) - NOW INCLUDING current_balance
            $stmt = $this->db->prepare("SELECT id, name, base_rent, current_balance FROM members WHERE id = ?");
            $stmt->execute([$member_id]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$member) {
                return $this->getEmptyData();
            }

            $base_rent = floatval($member['base_rent']);
            $stored_current_balance = floatval($member['current_balance']);

            // 2. Get total bazar for current month from ALL members
            $total_bazar_all = 0;
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) as total_bazar FROM bazar WHERE YEAR(bazar_date) = ? AND MONTH(bazar_date) = ?");
            $stmt->execute([$current_year, $current_month]);
            $bazar_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_bazar_all = floatval($bazar_data['total_bazar']);

            // 3. FIXED: Get total meals for current month from ALL members - with proper date filtering
            $total_meals_all = 0;
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(meal_count), 0) as total_meals 
                FROM meal_counts 
                WHERE YEAR(meal_date) = ? AND MONTH(meal_date) = ?
            ");
            $stmt->execute([$current_year, $current_month]);
            $meals_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_meals_all = floatval($meals_data['total_meals']);

            // DEBUG: Log the meal count for troubleshooting
            error_log("Dashboard Meal Count - All Members: " . $total_meals_all . " for " . $current_year . "-" . $current_month);

            // 4. Calculate meal rate: Total Bazar (All Members) / Total Meals (All Members)
            $current_meal_rate = 0;
            if ($total_meals_all > 0) {
                $current_meal_rate = $total_bazar_all / $total_meals_all;
            }

            // 5. FIXED: Get logged-in user's specific data with proper date filtering
            $user_total_meals = 0;
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(meal_count), 0) as total_meals 
                FROM meal_counts 
                WHERE member_id = ? AND YEAR(meal_date) = ? AND MONTH(meal_date) = ?
            ");
            $stmt->execute([$member_id, $current_year, $current_month]);
            $user_meals_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_total_meals = floatval($user_meals_data['total_meals']);

            // DEBUG: Log user meal count
            error_log("Dashboard Meal Count - User ID " . $member_id . ": " . $user_total_meals . " for " . $current_year . "-" . $current_month);

            $user_total_bazar = 0;
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(amount), 0) as total_bazar 
                FROM bazar 
                WHERE member_id = ? AND YEAR(bazar_date) = ? AND MONTH(bazar_date) = ?
            ");
            $stmt->execute([$member_id, $current_year, $current_month]);
            $user_bazar_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_total_bazar = floatval($user_bazar_data['total_bazar']);

            // 6. CORRECTED: Calculate current balance for logged-in user: User's Bazar - (User's Meals × Meal Rate)
            $user_total_meal_cost = $user_total_meals * $current_meal_rate;
            $current_balance = $user_total_bazar - $user_total_meal_cost;

            // NEW: Update current_balance in members table
            $this->updateCurrentBalance($member_id, $current_balance);

            // 7. Calculate total due: Only show if current balance is negative
            $total_due = 0;
            if ($current_balance < 0) {
                $total_due = abs($current_balance);
            }

            // 8. Prepare display data for UI
            $data['display_data'] = [
                'monthly_base_rent' => number_format($base_rent, 2),
                'current_meal_rate' => number_format($current_meal_rate, 2),
                'total_bazar_current_month' => number_format($user_total_bazar, 2),
                'total_meals_current_month' => number_format($user_total_meals, 1),
                'total_bazar_all_members' => number_format($total_bazar_all, 2),
                'total_meals_all_members' => number_format($total_meals_all, 1),
                'total_due' => number_format($total_due, 2),
                'due_amount' => number_format($current_balance, 2),
                'due_amount_abs' => number_format(abs($current_balance), 2),
                'current_balance' => number_format($current_balance, 2),
                'stored_current_balance' => number_format($stored_current_balance, 2), // Show stored value
                'total_meal_cost' => number_format($user_total_meal_cost, 2),
                'due_status' => $current_balance >= 0 ? 'positive' : 'negative',
                'due_message' => $current_balance >= 0 ? 'You will be paid soon' : 'Need to pay',
                'due_color' => $current_balance >= 0 ? '#27ae60' : '#e74c3c',
                // Add raw values and debug info
                'raw_data' => [
                    'user_total_meals' => $user_total_meals,
                    'user_total_bazar' => $user_total_bazar,
                    'total_meals_all' => $total_meals_all,
                    'total_bazar_all' => $total_bazar_all,
                    'meal_rate' => $current_meal_rate,
                    'meal_cost' => $user_total_meal_cost,
                    'current_month' => $current_year . '-' . $current_month
                ]
            ];

            return $data;

        } catch (Exception $e) {
            error_log("Dashboard Data Error: " . $e->getMessage());
            return $this->getEmptyData();
        }
    }

    // NEW METHOD: Update current_balance in members table
    private function updateCurrentBalance($member_id, $current_balance)
    {
        try {
            $stmt = $this->db->prepare("UPDATE members SET current_balance = ? WHERE id = ?");
            $stmt->execute([$current_balance, $member_id]);
            error_log("Updated current_balance for member {$member_id}: {$current_balance}");
            return true;
        } catch (Exception $e) {
            error_log("Error updating current_balance for member {$member_id}: " . $e->getMessage());
            return false;
        }
    }

    // NEW METHOD: Get current balance directly from database (for other parts of your application)
    public function getStoredCurrentBalance($member_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT current_balance FROM members WHERE id = ?");
            $stmt->execute([$member_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? floatval($result['current_balance']) : 0;
        } catch (Exception $e) {
            error_log("Error getting stored current_balance: " . $e->getMessage());
            return 0;
        }
    }

    private function getEmptyData()
    {
        return [
            'display_data' => [
                'monthly_base_rent' => '0.00',
                'current_meal_rate' => '0.00',
                'total_bazar_current_month' => '0.00',
                'total_meals_current_month' => '0.0',
                'total_bazar_all_members' => '0.00',
                'total_meals_all_members' => '0.0',
                'total_due' => '0.00',
                'due_amount' => '0.00',
                'due_amount_abs' => '0.00',
                'current_balance' => '0.00',
                'stored_current_balance' => '0.00',
                'total_meal_cost' => '0.00',
                'due_status' => 'positive',
                'due_message' => 'No data available',
                'due_color' => '#666',
                'raw_data' => [
                    'user_total_meals' => 0,
                    'user_total_bazar' => 0,
                    'total_meals_all' => 0,
                    'total_bazar_all' => 0,
                    'meal_rate' => 0,
                    'meal_cost' => 0,
                    'current_month' => date('Y-m')
                ]
            ]
        ];
    }
}
?>