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

    private function json($arr)
    {
        echo json_encode($arr);
        exit;
    }

    /* ---------------- LOAD MEMBERS ------------------- */
    public function loadMembers()
    {
        $sql = "SELECT id, name FROM members WHERE is_active = 1 ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $this->json([
            "status" => "success",
            "members" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }

    /* ---------------- ADD OR UPDATE MEAL ------------------- */
    public function addMeal($d)
    {
        // Use INSERT ... ON DUPLICATE KEY UPDATE if your table has unique constraint
        // Or use REPLACE INTO if you want simpler approach

        $sql = "INSERT INTO meal_counts 
                (member_id, member_name, meal_count, meal_date, description, created_at) 
                VALUES (:member_id,
                    (SELECT name FROM members WHERE id = :member_id LIMIT 1),
                    :meal_count,
                    :meal_date,
                    :description,
                    NOW()
                )
                ON DUPLICATE KEY UPDATE 
                    meal_count = VALUES(meal_count),
                    description = VALUES(description),
                    updated_at = NOW()";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                ":member_id" => $d["member_id"],
                ":meal_count" => $d["meal_count"],
                ":meal_date" => $d["meal_date"],
                ":description" => $d["description"]
            ]);

            $this->json([
                "status" => "success",
                "message" => "Meal saved successfully",
                "id" => $this->conn->lastInsertId() ?: $this->getMealId($d["member_id"], $d["meal_date"])
            ]);
        } catch (PDOException $e) {
            $this->json(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    }

    /* ---------------- GET MEAL ID ------------------- */
    private function getMealId($member_id, $meal_date)
    {
        $sql = "SELECT id FROM meal_counts WHERE member_id = :member_id AND meal_date = :meal_date";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ":member_id" => $member_id,
            ":meal_date" => $meal_date
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }

    /* ---------------- UPDATE MEAL ------------------- */
    public function updateMeal($d)
    {
        $sql = "UPDATE meal_counts SET 
                    meal_count = :meal_count,
                    description = :description,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                ":meal_count" => $d["meal_count"],
                ":description" => $d["description"],
                ":id" => $d["id"]
            ]);

            $this->json(["status" => "success", "message" => "Meal updated successfully"]);
        } catch (PDOException $e) {
            $this->json(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    }

    /* ---------------- GET SINGLE MEAL ------------------- */
    public function getMeal($id)
    {
        $sql = "SELECT * FROM meal_counts WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);

        $meal = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($meal) {
            $this->json(["status" => "success", "meal" => $meal]);
        } else {
            $this->json(["status" => "error", "message" => "Meal not found"]);
        }
    }

    /* ---------------- DELETE MEAL ------------------- */
    public function deleteMeal($id)
    {
        $sql = "DELETE FROM meal_counts WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([":id" => $id]);

            if ($stmt->rowCount() > 0) {
                $this->json(["status" => "success", "message" => "Meal deleted successfully"]);
            } else {
                $this->json(["status" => "error", "message" => "Meal not found or already deleted"]);
            }
        } catch (PDOException $e) {
            $this->json(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    }

    /* ---------------- LOAD MEALS BY MONTH ------------------- */
    public function loadMealsByMonth($month, $year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $month = str_pad($month, 2, "0", STR_PAD_LEFT);

        $sql = "SELECT mc.*, m.name AS real_name 
                FROM meal_counts mc
                LEFT JOIN members m ON mc.member_id = m.id
                WHERE YEAR(mc.meal_date) = :year 
                AND MONTH(mc.meal_date) = :month
                ORDER BY mc.meal_date ASC, m.name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ":month" => $month,
            ":year" => $year
        ]);

        $this->json([
            "status" => "success",
            "meals" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }
}

/* ---------- ROUTER ---------- */
$meal = new MealActions();
$action = $_POST["action"] ?? "";

switch ($action) {
    case "load_members":
        $meal->loadMembers();
        break;
    case "add_meal":
        $meal->addMeal($_POST);
        break;
    case "update_meal":
        $meal->updateMeal($_POST);
        break;
    case "get_meal":
        $meal->getMeal($_POST["id"]);
        break;
    case "delete_meal":
        $meal->deleteMeal($_POST["id"]);
        break;
    case "load_meals_month":
        $year = $_POST["year"] ?? date('Y');
        $meal->loadMealsByMonth($_POST["month"], $year);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}