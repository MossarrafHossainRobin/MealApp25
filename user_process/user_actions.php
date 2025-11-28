<?php
// user_process/user_actions.php
// Updated to use the Database class structure from config/database.php

// Path is relative to user_process/, so it goes up one level (..) to the root, then into config/
require_once __DIR__ . '/../config/database.php';

// !!! SECURITY WARNING: Login remains highly insecure (email existence check only) per your request. !!!

class UserActions
{
    private $db;

    public function __construct()
    {
        try {
            // Instantiate your Database class and get the PDO connection
            $db_instance = new Database();
            $this->db = $db_instance->getConnection();

        } catch (\PDOException $e) {
            // Halt execution if the connection (or instantiation) failed
            die("FATAL DB ERROR: Check your credentials in config/database.php. Message: " . $e->getMessage());
        }
    }

    // Login function (INSECURE - for prompt requirement only)
    public function loginUser($email)
    {
        // Ensure session is started before accessing $_SESSION
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Remove the "AND is_active = 1" condition to allow all users regardless of active status
        $stmt = $this->db->prepare("SELECT id, name, email, is_active FROM members WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            return true;
        }
        return false;
    }

    public function getUserData($member_id)
    {
        if (!$member_id)
            return false;

        $data = [];

        // 1. Member Info
        $stmt = $this->db->prepare("SELECT id, name, email, is_active, created_at, base_rent FROM members WHERE id = ?");
        $stmt->execute([$member_id]);
        $data['member'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Water Duties (Last 5)
        $stmt = $this->db->prepare("SELECT id, duty_date, status, duty_time FROM water_duties WHERE member_id = ? ORDER BY duty_date DESC LIMIT 5");
        $stmt->execute([$member_id]);
        $data['water_duties'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Bazar Expenses (Last 5) - APPROVED ENTRIES
        $stmt = $this->db->prepare("SELECT id, amount, bazar_count, description, bazar_date, month FROM bazar WHERE member_id = ? ORDER BY bazar_date DESC LIMIT 5");
        $stmt->execute([$member_id]);
        $data['bazar'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3b. Bazar Requests (Pending) - NEW!
        $stmt = $this->db->prepare("SELECT id, amount, description, bazar_date, status FROM bazar_requests WHERE member_id = ? AND status = 'Pending' ORDER BY created_at DESC");
        $stmt->execute([$member_id]);
        $data['bazar_requests_pending'] = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // 4. Flat Cost Allocations (Last 5)
        $stmt = $this->db->prepare("SELECT id, amount_due, status, rent_amount, utility_share, special_adjustment, note FROM member_flat_cost_allocations WHERE member_id = ? ORDER BY id DESC LIMIT 5");
        $stmt->execute([$member_id]);
        $data['flat_costs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Meal Counts (Last 7 days)
        $stmt = $this->db->prepare("SELECT id, meal_count, meal_date, description FROM meal_counts WHERE member_id = ? ORDER BY meal_date DESC LIMIT 7");
        $stmt->execute([$member_id]);
        $data['meal_counts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    /**
     * Updates the status of a specific water duty to 'Completed' if it was Pending.
     */
    public function markWaterDutyComplete($duty_id, $member_id)
    {
        $stmt = $this->db->prepare("UPDATE water_duties SET status = 'Completed', assigned_at = NOW() WHERE id = ? AND member_id = ? AND status = 'Pending'");
        $result = $stmt->execute([$duty_id, $member_id]);
        return $result;
    }

    /**
     * Update user presence status using PDO
     */
    public function updateUserPresence($user_id, $is_present)
    {
        try {
            $is_present = $is_present ? 1 : 0;

            $stmt = $this->db->prepare("UPDATE members SET is_active = ? WHERE id = ?");
            $result = $stmt->execute([$is_present, $user_id]);

            return $result;

        } catch (PDOException $e) {
            error_log("Presence update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * User requests a Bazar addition, which goes into bazar_requests for admin approval.
     */
    public function requestBazarAddition($member_id, $amount, $description, $bazar_date)
    {
        if (empty($member_id) || empty($amount) || empty($bazar_date)) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO bazar_requests (member_id, amount, description, bazar_date, status)
                VALUES (?, ?, ?, ?, 'Pending')
            ");

            // Note: Bazar count is not requested from the user, as it's typically tracked by the manager.
            // We only request the amount, description, and date.
            $result = $stmt->execute([$member_id, $amount, $description, $bazar_date]);

            return $result;

        } catch (PDOException $e) {
            error_log("Bazar request error: " . $e->getMessage());
            return false;
        }
    }
}
// End of UserActions class
?>