<?php
require_once '../config/database.php';

// Initialize Database
$db = new Database();
$connection = $db->getConnection();

// Handle POST actions
$action = $_POST['action'] ?? '';
$success_message = '';
$error_message = '';
$current_section = $_POST['current_section'] ?? 'home';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        switch ($action) {
            case 'add_member':
                $name = trim($_POST['name'] ?? '');
                if (!empty($name)) {
                    $check = $connection->prepare("SELECT id FROM members WHERE name = ?");
                    $check->execute([$name]);
                    if (!$check->fetch()) {
                        $stmt = $connection->prepare("INSERT INTO members (name) VALUES (?)");
                        $stmt->execute([$name]);
                        $memberId = $connection->lastInsertId();
                        $presentStmt = $connection->prepare("INSERT IGNORE INTO present_members (member_id) VALUES (?)");
                        $presentStmt->execute([$memberId]);
                        $success_message = "Member '$name' added successfully!";
                    } else {
                        $error_message = "Member '$name' already exists!";
                    }
                }
                break;

            case 'add_bazar':
                $memberId = $_POST['member_id'] ?? '';
                $amount = $_POST['amount'] ?? '';
                $bazarCount = $_POST['bazar_count'] ?? 1;
                $description = $_POST['description'] ?? '';
                $bazarDate = $_POST['bazar_date'] ?? date('Y-m-d');
                $selectedMonth = $_POST['selected_month'] ?? date('n');
                $selectedYear = $_POST['selected_year'] ?? date('Y');

                if ($memberId && $amount && $bazarDate) {
                    try {
                        // Validate the bazar date is within selected month
                        $bazarMonth = date('n', strtotime($bazarDate));
                        $bazarYear = date('Y', strtotime($bazarDate));

                        if ($bazarMonth != $selectedMonth || $bazarYear != $selectedYear) {
                            echo json_encode([
                                'success' => false,
                                'error' => 'Bazar date must be within selected month and year'
                            ]);
                            exit;
                        }

                        $stmt = $connection->prepare("
                INSERT INTO bazar (member_id, amount, bazar_count, description, bazar_date) 
                VALUES (?, ?, ?, ?, ?)
            ");
                        $stmt->execute([$memberId, $amount, $bazarCount, $description, $bazarDate]);

                        $bazarId = $connection->lastInsertId();

                        echo json_encode([
                            'success' => true,
                            'message' => 'Bazar entry added successfully!',
                            'bazar_id' => $bazarId
                        ]);
                        exit;

                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'error' => 'Database error: ' . $e->getMessage()
                        ]);
                        exit;
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Missing required fields: member_id, amount, or date'
                    ]);
                    exit;
                }
                break;

            case 'delete_bazar':
                $bazarId = $_POST['bazar_id'] ?? '';
                if ($bazarId) {
                    try {
                        $stmt = $connection->prepare("DELETE FROM bazar WHERE id = ?");
                        $stmt->execute([$bazarId]);

                        // Check if any row was affected
                        if ($stmt->rowCount() > 0) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Bazar entry deleted successfully!'
                            ]);
                        } else {
                            echo json_encode([
                                'success' => false,
                                'error' => 'Bazar entry not found or already deleted'
                            ]);
                        }
                        exit;

                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'error' => 'Database error: ' . $e->getMessage()
                        ]);
                        exit;
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'error' => 'No bazar ID provided'
                    ]);
                    exit;
                }
                break;

            case 'save_single_meal':
                $member_id = $_POST['member_id'] ?? '';
                $meal_date = $_POST['meal_date'] ?? '';
                $meal_count = $_POST['meal_count'] ?? 0;

                try {
                    $stmt = $connection->prepare("
            INSERT INTO meal_counts (member_id, meal_date, meal_count, created_at) 
            VALUES (?, ?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE meal_count = VALUES(meal_count), updated_at = NOW()
        ");

                    $stmt->execute([$member_id, $meal_date, $meal_count]);

                    echo json_encode(['success' => true, 'message' => 'Meal saved']);
                    exit;

                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                    exit;
                }
                break;

            case 'save_all_meals':
                $meals_json = $_POST['meals'] ?? '{}';
                $year = $_POST['year'] ?? date('Y');
                $month = $_POST['month'] ?? date('m');

                try {
                    $meals = json_decode($meals_json, true);
                    $connection->beginTransaction();

                    $stmt = $connection->prepare("
            INSERT INTO meal_counts (member_id, meal_date, meal_count, created_at) 
            VALUES (?, ?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE meal_count = VALUES(meal_count), updated_at = NOW()
        ");

                    $records_saved = 0;
                    foreach ($meals as $member_id => $dates) {
                        foreach ($dates as $date => $count) {
                            $stmt->execute([$member_id, $date, $count]);
                            $records_saved++;
                        }
                    }

                    $connection->commit();

                    echo json_encode([
                        'success' => true,
                        'message' => "Saved $records_saved meal records",
                        'records_saved' => $records_saved
                    ]);
                    exit;

                } catch (Exception $e) {
                    $connection->rollBack();
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                    exit;
                }
                break;

            case 'give_water':
                $memberId = $_POST['member_id'] ?? '';
                if ($memberId) {
                    $stmt = $connection->prepare("INSERT INTO water_duty (member_id) VALUES (?)");
                    $stmt->execute([$memberId]);
                    $success_message = "Water duty recorded successfully!";
                }
                break;

            case 'update_present_members':
                $memberIds = $_POST['member_ids'] ?? [];
                $connection->beginTransaction();
                try {
                    $connection->exec("DELETE FROM present_members");
                    if (!empty($memberIds)) {
                        $stmt = $connection->prepare("INSERT INTO present_members (member_id) VALUES (?)");
                        foreach ($memberIds as $id) {
                            $stmt->execute([$id]);
                        }
                    }
                    $connection->commit();
                    $success_message = "Present members updated successfully!";
                } catch (Exception $e) {
                    $connection->rollBack();
                    $error_message = "Error updating present members: " . $e->getMessage();
                }
                break;
            case 'save_meals':
                $meals = $_POST['meals'] ?? [];
                $selected_year = $_POST['selected_year'] ?? date('Y');
                $selected_month = $_POST['selected_month'] ?? date('m');

                if (!empty($meals)) {
                    $connection->beginTransaction();
                    try {
                        // Prepare the INSERT statement
                        $stmt = $connection->prepare("
                INSERT INTO meal_counts (member_id, meal_date, meal_count, created_at) 
                VALUES (?, ?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE meal_count = VALUES(meal_count), created_at = NOW()
            ");

                        $records_processed = 0;
                        foreach ($meals as $member_id => $dates) {
                            foreach ($dates as $date => $count) {
                                if (isset($member_id, $date, $count)) {
                                    // Validate data
                                    $member_id = intval($member_id);
                                    $count = floatval($count);

                                    // Only insert if count is not zero (or insert zero if you want to track zeros)
                                    if ($count >= 0) {
                                        $stmt->execute([$member_id, $date, $count]);
                                        $records_processed++;
                                    }
                                }
                            }
                        }

                        $connection->commit();
                        $success_message = "Successfully saved $records_processed meal records for " . date('F Y', strtotime("$selected_year-$selected_month-01")) . "!";

                    } catch (Exception $e) {
                        $connection->rollBack();
                        $error_message = "Error saving meals: " . $e->getMessage();

                        // Debug info
                        error_log("Meal save error: " . $e->getMessage());
                        error_log("Meal data: " . print_r($meals, true));
                    }
                } else {
                    $error_message = "No meal data received to save!";
                }
                break;
        }
    }
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}

// Store messages in session to pass to main page
session_start();
$_SESSION['success_message'] = $success_message;
$_SESSION['error_message'] = $error_message;
$_SESSION['current_section'] = $current_section;

// Redirect back to main page
header('Location: ../index.php');
exit();
?>