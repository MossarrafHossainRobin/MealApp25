<?php
require_once '../config/database.php';

// Initialize Database
$db = new Database();
$connection = $db->getConnection();

$action = $_POST['action'] ?? '';
$current_section = $_POST['current_section'] ?? 'home';

// Helper to send JSON and exit
function json_exit($arr, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($arr);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        switch ($action) {

            /* --------------------------------
               ADD MEAL
            -------------------------------- */
            case 'add_meal':
                $memberId = intval($_POST['member_id'] ?? 0);
                $mealCount = floatval($_POST['meal_count'] ?? 0);
                $mealDate = trim($_POST['meal_date'] ?? '');
                $description = $_POST['description'] ?? '';

                if (!$memberId || $mealCount < 0 || !$mealDate) {
                    json_exit(['success' => false, 'error' => 'Missing required fields: member, meal count or date'], 400);
                }

                // Validate meal count
                if ($mealCount < 0 || $mealCount > 10) {
                    json_exit(['success' => false, 'error' => 'Meal count must be between 0 and 10'], 400);
                }

                try {
                    // Check if entry already exists for this member and date
                    $check_stmt = $connection->prepare("SELECT id FROM meal_counts WHERE member_id = ? AND meal_date = ?");
                    $check_stmt->execute([$memberId, $mealDate]);
                    $existing = $check_stmt->fetch();

                    if ($existing) {
                        // Update existing entry
                        $stmt = $connection->prepare("
                            UPDATE meal_counts 
                            SET meal_count = ?, description = ?, updated_at = NOW()
                            WHERE member_id = ? AND meal_date = ?
                        ");
                        $stmt->execute([$mealCount, $description, $memberId, $mealDate]);
                        $mealId = $existing['id'];
                        $action_type = 'updated';
                    } else {
                        // Insert new entry
                        $stmt = $connection->prepare("
                            INSERT INTO meal_counts (member_id, meal_count, description, meal_date, created_at)
                            VALUES (?, ?, ?, ?, NOW())
                        ");
                        $stmt->execute([$memberId, $mealCount, $description, $mealDate]);
                        $mealId = $connection->lastInsertId();
                        $action_type = 'added';
                    }

                    json_exit([
                        'success' => true,
                        'message' => 'Meal ' . $action_type . ' successfully!',
                        'meal_id' => $mealId,
                        'action' => $action_type,
                        'data' => [
                            'id' => $mealId,
                            'member_id' => $memberId,
                            'meal_count' => $mealCount,
                            'meal_date' => $mealDate
                        ]
                    ]);

                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        json_exit(['success' => false, 'error' => 'Meal entry already exists for this member and date'], 400);
                    }
                    throw $e;
                }
                break;

            /* --------------------------------
               DELETE MEAL
            -------------------------------- */
            case 'delete_meal':
                $mealId = intval($_POST['meal_id'] ?? 0);

                if (!$mealId) {
                    json_exit(['success' => false, 'error' => 'No meal ID provided'], 400);
                }

                try {
                    $stmt = $connection->prepare("DELETE FROM meal_counts WHERE id = ?");
                    $stmt->execute([$mealId]);

                    if ($stmt->rowCount() > 0) {
                        json_exit(['success' => true, 'message' => 'Meal deleted successfully!']);
                    }

                    json_exit(['success' => false, 'error' => 'Meal not found'], 404);

                } catch (Exception $e) {
                    json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
                }
                break;

            /* --------------------------------
               EDIT MEAL
            -------------------------------- */
            case 'edit_meal':
                $mealId = intval($_POST['meal_id'] ?? 0);
                $memberId = intval($_POST['member_id'] ?? 0);
                $mealCount = floatval($_POST['meal_count'] ?? 0);
                $description = trim($_POST['description'] ?? '');
                $mealDate = $_POST['meal_date'] ?? '';

                if (!$mealId || !$memberId || $mealCount < 0 || !$mealDate) {
                    json_exit(['success' => false, 'error' => 'Missing required fields'], 400);
                }

                // Validate meal count
                if ($mealCount < 0 || $mealCount > 10) {
                    json_exit(['success' => false, 'error' => 'Meal count must be between 0 and 10'], 400);
                }

                try {
                    // Check if another entry exists for same member and date (conflict)
                    $check_stmt = $connection->prepare("
                        SELECT id FROM meal_counts 
                        WHERE member_id = ? AND meal_date = ? AND id != ?
                    ");
                    $check_stmt->execute([$memberId, $mealDate, $mealId]);
                    $conflict = $check_stmt->fetch();

                    if ($conflict) {
                        json_exit(['success' => false, 'error' => 'Another meal entry already exists for this member and date'], 400);
                    }

                    $stmt = $connection->prepare("
                        UPDATE meal_counts 
                        SET member_id = ?, meal_count = ?, description = ?, meal_date = ?, updated_at = NOW()
                        WHERE id = ?
                    ");

                    $stmt->execute([$memberId, $mealCount, $description, $mealDate, $mealId]);

                    if ($stmt->rowCount() > 0) {
                        json_exit(['success' => true, 'message' => 'Meal updated successfully']);
                    }

                    json_exit(['success' => false, 'error' => 'Meal not found'], 404);

                } catch (Exception $e) {
                    json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
                }
                break;

            /* --------------------------------
               GET MEAL DATA
            -------------------------------- */
            case 'get_meal_data':
                $month = intval($_POST['month'] ?? date('n'));
                $year = intval($_POST['year'] ?? date('Y'));

                if ($month < 1 || $month > 12)
                    $month = date('n');
                if ($year < 2000 || $year > 2100)
                    $year = date('Y');

                $monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);
                $firstDay = "$year-$monthFormatted-01";
                $lastDay = date('Y-m-t', strtotime($firstDay));

                // Get all active members
                $members_stmt = $connection->prepare("SELECT id, name FROM members WHERE is_active = 1 ORDER BY name");
                $members_stmt->execute();
                $members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);

                // Get meal entries
                $stmt = $connection->prepare("
                    SELECT 
                        mc.id, mc.member_id, mc.meal_count, mc.description,
                        mc.meal_date, mc.created_at,
                        m.name AS member_name
                    FROM meal_counts mc
                    JOIN members m ON mc.member_id = m.id
                    WHERE mc.meal_date BETWEEN ? AND ?
                    ORDER BY mc.meal_date DESC
                ");

                $stmt->execute([$firstDay, $lastDay]);
                $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Calculate totals
                $totalMeals = 0;
                foreach ($entries as $entry) {
                    $totalMeals += floatval($entry['meal_count']);
                }

                json_exit([
                    'success' => true,
                    'month' => $month,
                    'year' => $year,
                    'entries' => $entries,
                    'members' => $members,
                    'total_entries' => count($entries),
                    'total_meals' => $totalMeals,
                    'first_day' => $firstDay,
                    'last_day' => $lastDay
                ]);
                break;

            /* --------------------------------
               ADD SAMPLE MEAL DATA
            -------------------------------- */
            case 'add_sample_meals':
                $month = intval($_POST['month'] ?? date('n'));
                $year = intval($_POST['year'] ?? date('Y'));

                $membersStmt = $connection->query("SELECT id FROM members WHERE is_active = 1 LIMIT 3");
                $memberIds = $membersStmt->fetchAll(PDO::FETCH_COLUMN);

                if (empty($memberIds)) {
                    json_exit(['success' => false, 'error' => 'No active members available'], 400);
                }

                $connection->beginTransaction();

                try {
                    $stmt = $connection->prepare("
                        INSERT INTO meal_counts (member_id, meal_count, description, meal_date, created_at)
                        VALUES (?, ?, ?, ?, NOW())
                    ");

                    $daysInMonth = date('t', strtotime("$year-$month-01"));
                    $entriesAdded = 0;

                    for ($i = 0; $i < 5; $i++) {
                        $memberId = $memberIds[$i % count($memberIds)];
                        $day = min(1 + $i * 2, $daysInMonth);
                        $mealCount = [0.5, 1.0, 1.5, 2.0][$i % 4];
                        $descriptions = ['Breakfast only', 'Lunch only', 'Dinner only', 'Lunch & Dinner', 'All meals'];

                        $date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);

                        $stmt->execute([$memberId, $mealCount, $descriptions[$i % 5], $date]);
                        $entriesAdded++;
                    }

                    $connection->commit();

                    json_exit([
                        'success' => true,
                        'message' => "Added $entriesAdded sample meal entries!",
                        'entries_added' => $entriesAdded
                    ]);

                } catch (Exception $e) {
                    $connection->rollBack();
                    json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
                }
                break;

            /* --------------------------------
               ADD BAZAR
            -------------------------------- */
            case 'add_bazar':
                $memberId = intval($_POST['member_id'] ?? 0);
                $amount = trim($_POST['amount'] ?? '');
                $bazarCount = intval($_POST['bazar_count'] ?? 1);
                $bazarDate = trim($_POST['bazar_date'] ?? '');
                $description = $_POST['description'] ?? '';

                if (!$memberId || $amount === '' || !$bazarDate) {
                    json_exit(['success' => false, 'error' => 'Missing required fields: member, amount or date'], 400);
                }

                if (!is_numeric($amount)) {
                    json_exit(['success' => false, 'error' => 'Amount must be numeric'], 400);
                }

                $amount = floatval($amount);

                try {
                    $stmt = $connection->prepare("
                        INSERT INTO bazar (member_id, amount, bazar_count, description, bazar_date, created_at)
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$memberId, $amount, $bazarCount, $description, $bazarDate]);

                    $bazarId = $connection->lastInsertId();

                    json_exit([
                        'success' => true,
                        'message' => 'Bazar added successfully!',
                        'bazar_id' => $bazarId,
                        'data' => [
                            'id' => $bazarId,
                            'member_id' => $memberId,
                            'amount' => $amount,
                            'bazar_count' => $bazarCount,
                            'bazar_date' => $bazarDate
                        ]
                    ]);

                } catch (Exception $e) {
                    json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
                }
                break;

            /* --------------------------------
               DELETE BAZAR
            -------------------------------- */
            case 'delete_bazar':
                $bazarId = intval($_POST['bazar_id'] ?? 0);

                if (!$bazarId) {
                    json_exit(['success' => false, 'error' => 'No bazar ID provided'], 400);
                }

                try {
                    $stmt = $connection->prepare("DELETE FROM bazar WHERE id = ?");
                    $stmt->execute([$bazarId]);

                    if ($stmt->rowCount() > 0) {
                        json_exit(['success' => true, 'message' => 'Bazar deleted successfully!']);
                    }

                    json_exit(['success' => false, 'error' => 'Bazar not found'], 404);

                } catch (Exception $e) {
                    json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
                }
                break;

            /* --------------------------------
               GET BAZAR DATA
            -------------------------------- */
            case 'get_bazar_data':
                $month = intval($_POST['month'] ?? date('n'));
                $year = intval($_POST['year'] ?? date('Y'));

                if ($month < 1 || $month > 12)
                    $month = date('n');
                if ($year < 2000 || $year > 2100)
                    $year = date('Y');

                $monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);
                $firstDay = "$year-$monthFormatted-01";
                $lastDay = date('Y-m-t', strtotime($firstDay));

                $stmt = $connection->prepare("
                    SELECT 
                        b.id, b.member_id, b.amount, b.description, 
                        b.bazar_count, b.bazar_date, b.created_at,
                        m.name AS member_name
                    FROM bazar b
                    JOIN members m ON b.member_id = m.id
                    WHERE b.bazar_date BETWEEN ? AND ?
                    ORDER BY b.bazar_date DESC
                ");

                $stmt->execute([$firstDay, $lastDay]);
                $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $totalAmount = 0;
                foreach ($entries as $entry) {
                    $totalAmount += floatval($entry['amount']);
                }

                json_exit([
                    'success' => true,
                    'month' => $month,
                    'year' => $year,
                    'entries' => $entries,
                    'total_entries' => count($entries),
                    'total_amount' => $totalAmount,
                    'first_day' => $firstDay,
                    'last_day' => $lastDay
                ]);
                break;

            /* --------------------------------
               ADD SAMPLE BAZAR
            -------------------------------- */
            case 'add_sample_bazar':
                $month = intval($_POST['month'] ?? date('n'));
                $year = intval($_POST['year'] ?? date('Y'));

                $membersStmt = $connection->query("SELECT id FROM members LIMIT 3");
                $memberIds = $membersStmt->fetchAll(PDO::FETCH_COLUMN);

                if (empty($memberIds)) {
                    json_exit(['success' => false, 'error' => 'No members available'], 400);
                }

                $connection->beginTransaction();

                try {
                    $stmt = $connection->prepare("
                        INSERT INTO bazar (member_id, amount, bazar_count, description, bazar_date, created_at)
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");

                    $daysInMonth = date('t', strtotime("$year-$month-01"));
                    $entriesAdded = 0;

                    for ($i = 0; $i < 3; $i++) {
                        $memberId = $memberIds[$i % count($memberIds)];
                        $day = min(5 + $i * 10, $daysInMonth);

                        $date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                        $amount = rand(200, 800);

                        $stmt->execute([$memberId, $amount, 1, "Sample bazar $i", $date]);
                        $entriesAdded++;
                    }

                    $connection->commit();

                    json_exit([
                        'success' => true,
                        'message' => "Added $entriesAdded sample bazar entries!",
                        'entries_added' => $entriesAdded
                    ]);

                } catch (Exception $e) {
                    $connection->rollBack();
                    json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
                }
                break;

            /* --------------------------------
               EDIT BAZAR
            -------------------------------- */
            case 'edit_bazar':
                $bazarId = intval($_POST['bazar_id'] ?? 0);
                $memberId = intval($_POST['member_id'] ?? 0);
                $amount = floatval($_POST['amount'] ?? 0);
                $bazarCount = intval($_POST['bazar_count'] ?? 0);
                $description = trim($_POST['description'] ?? '');
                $bazarDate = $_POST['bazar_date'] ?? '';

                if (!$bazarId || !$memberId || !$amount || !$bazarDate) {
                    json_exit(['success' => false, 'error' => 'Missing required fields'], 400);
                }

                try {
                    $stmt = $connection->prepare("
                        UPDATE bazar 
                        SET member_id = ?, amount = ?, bazar_count = ?, description = ?, bazar_date = ?, updated_at = NOW()
                        WHERE id = ?
                    ");

                    $stmt->execute([$memberId, $amount, $bazarCount, $description, $bazarDate, $bazarId]);

                    if ($stmt->rowCount() > 0) {
                        json_exit(['success' => true, 'message' => 'Bazar updated successfully']);
                    }

                    json_exit(['success' => false, 'error' => 'Bazar not found'], 404);

                } catch (Exception $e) {
                    json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
                }
                break;

            /* --------------------------------
               PLACEHOLDER FOR OTHER ACTIONS
            -------------------------------- */
            case 'add_member':
            case 'delete_member':
            case 'save_meals':
            case 'update_present_members':
                break;

            default:
                break;
        }
    }

    /* --------------------------------
       GET REQUESTS: MEAL DATA
    -------------------------------- */
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {

        if ($_GET['action'] === 'get_meal_data') {

            $month = intval($_GET['month'] ?? date('n'));
            $year = intval($_GET['year'] ?? date('Y'));

            if ($month < 1 || $month > 12)
                $month = date('n');
            if ($year < 2000 || $year > 2100)
                $year = date('Y');

            $monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);
            $firstDay = "$year-$monthFormatted-01";
            $lastDay = date('Y-m-t', strtotime($firstDay));

            // Get all active members
            $members_stmt = $connection->prepare("SELECT id, name FROM members WHERE is_active = 1 ORDER BY name");
            $members_stmt->execute();
            $members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get meal entries
            $stmt = $connection->prepare("
                SELECT 
                    mc.id, mc.member_id, mc.meal_count, mc.description,
                    mc.meal_date, mc.created_at,
                    m.name AS member_name
                FROM meal_counts mc
                JOIN members m ON mc.member_id = m.id
                WHERE mc.meal_date BETWEEN ? AND ?
                ORDER BY mc.meal_date DESC
            ");

            $stmt->execute([$firstDay, $lastDay]);
            $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate totals
            $totalMeals = 0;
            foreach ($entries as $entry) {
                $totalMeals += floatval($entry['meal_count']);
            }

            json_exit([
                'success' => true,
                'month' => $month,
                'year' => $year,
                'entries' => $entries,
                'members' => $members,
                'total_entries' => count($entries),
                'total_meals' => $totalMeals,
                'first_day' => $firstDay,
                'last_day' => $lastDay
            ]);
        }
    }

    /* --------------------------------
       GET REQUESTS: BAZAR DATA
    -------------------------------- */
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {

        if ($_GET['action'] === 'get_bazar_data') {

            $month = intval($_GET['month'] ?? date('n'));
            $year = intval($_GET['year'] ?? date('Y'));

            if ($month < 1 || $month > 12)
                $month = date('n');
            if ($year < 2000 || $year > 2100)
                $year = date('Y');

            $monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);
            $firstDay = "$year-$monthFormatted-01";
            $lastDay = date('Y-m-t', strtotime($firstDay));

            $stmt = $connection->prepare("
                SELECT 
                    b.id, b.member_id, b.amount, b.description,
                    b.bazar_count, b.bazar_date, b.created_at,
                    m.name AS member_name
                FROM bazar b
                JOIN members m ON b.member_id = m.id
                WHERE b.bazar_date BETWEEN ? AND ?
                ORDER BY b.bazar_date DESC
            ");

            $stmt->execute([$firstDay, $lastDay]);
            $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

            json_exit([
                'success' => true,
                'month' => $month,
                'year' => $year,
                'entries' => $entries,
                'total_entries' => count($entries),
                'first_day' => $firstDay,
                'last_day' => $lastDay
            ]);
        }
    }

} catch (Exception $e) {

    if (!headers_sent()) {
        json_exit(['success' => false, 'error' => 'Server error: ' . $e->getMessage()], 500);
    }

    session_start();
    $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
    header('Location: ../index.php');
    exit;
}

session_start();
$_SESSION['success_message'] = $_SESSION['success_message'] ?? '';
$_SESSION['error_message'] = $_SESSION['error_message'] ?? '';
$_SESSION['current_section'] = $current_section;

header('Location: ../index.php');
exit();