<?php
// process/water_actions.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';
require '../config/database.php';

session_start();
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

// --- Helper: Send Email ---
function sendWaterDutyEmail($member_email, $member_name, $duty_date, $type = 'assignment', $time_info = '')
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'meal.query@gmail.com';
        $mail->Password = 'ogjngrxxihxxtree';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('meal.query@gmail.com', 'Meal App25 Water Duty');
        $mail->addAddress($member_email, $member_name);

        $mail->isHTML(true);

        $formatted_time = date('h:i A', strtotime($time_info ?: '09:00:00'));

        if ($type === 'assignment') {
            $mail->Subject = "Water Duty Assignment - $duty_date";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 5px;'>
                    <h2 style='color: #4e73df;'>Water Duty Assignment</h2>
                    <p>Hello <strong>$member_name</strong>,</p>
                    <p>You have been assigned water duty on:</p>
                    <h3 style='background: #f8f9fc; padding: 10px;'>📅 $duty_date <br> ⏰ $formatted_time</h3>
                    <p>Please ensure water is available by this time.</p>
                </div>";
        } elseif ($type === 'reassigned_to') {
            $mail->Subject = "URGENT: Duty Re-assigned to You";
            $mail->Body = "<h3>Hello $member_name,</h3><p>The previous assignee missed the deadline. Duty for today ($duty_date) has been <strong>reassigned to you</strong>.</p>";
        } elseif ($type === 'reassigned_from') {
            $mail->Subject = "Duty Re-assigned (Missed Deadline)";
            $mail->Body = "<h3>Hello $member_name,</h3><p>Since the 4-hour deadline passed, your duty for today has been reassigned.</p>";
        } elseif ($type === 'reminder') {
            $mail->Subject = "Reminder: Water Duty Tomorrow";
            $mail->Body = "<h3>Reminder</h3><p>Hello $member_name, you have duty tomorrow ($duty_date) at $formatted_time.</p>";
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// --- GET: Fetch Data & Run Automation ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $today = date('Y-m-d');
        $currentTime = time();

        // 1. FETCH TODAY'S DUTY FIRST
        // We need to know the scheduled time to calculate the 4-hour deadline
        $check_stmt = $conn->prepare("
            SELECT wd.id, wd.member_id, wd.duty_time, wd.status, m.email, m.name 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.duty_date = ? AND wd.status = 'pending'
        ");
        $check_stmt->execute([$today]);
        $current_today_duty = $check_stmt->fetch(PDO::FETCH_ASSOC);

        // 2. AUTO-REASSIGNMENT LOGIC (Dynamic 4-Hour Rule)
        if ($current_today_duty && !isset($_SESSION['reassigned_' . $today])) {
            $scheduledTime = $current_today_duty['duty_time'] ?: '09:00:00';
            $deadlineTimestamp = strtotime("$today $scheduledTime") + (4 * 60 * 60); // Scheduled Time + 4 Hours

            // If current time is PAST the deadline
            if ($currentTime > $deadlineTimestamp) {
                // Find replacement
                $replace_stmt = $conn->prepare("SELECT id, name, email FROM members WHERE is_active = 1 AND id != ? ORDER BY RAND() LIMIT 1");
                $replace_stmt->execute([$current_today_duty['member_id']]);
                $replacement = $replace_stmt->fetch(PDO::FETCH_ASSOC);

                if ($replacement) {
                    $update_stmt = $conn->prepare("UPDATE water_duties SET member_id = ? WHERE id = ?");
                    $update_stmt->execute([$replacement['id'], $current_today_duty['id']]);

                    sendWaterDutyEmail($current_today_duty['email'], $current_today_duty['name'], $today, 'reassigned_from');
                    sendWaterDutyEmail($replacement['email'], $replacement['name'], $today, 'reassigned_to');

                    $_SESSION['reassigned_' . $today] = true;
                }
            }
        }

        // 3. RE-FETCH DATA FOR UI (In case update happened above)
        $today_stmt = $conn->prepare("
            SELECT wd.id, wd.member_id, wd.duty_time, wd.status, m.name, m.email 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.duty_date = ?
        ");
        $today_stmt->execute([$today]);
        $today_duty = $today_stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate remaining time for UI
        $time_remaining_percent = 0;
        $hours_left = 0;
        if ($today_duty && $today_duty['status'] == 'pending') {
            $sTime = $today_duty['duty_time'] ?: '09:00:00';
            $deadline = strtotime("$today $sTime") + (4 * 60 * 60);
            $total_window = 4 * 60 * 60;
            $elapsed = $currentTime - strtotime("$today $sTime");

            if ($currentTime < $deadline && $currentTime > strtotime("$today $sTime")) {
                $time_remaining_percent = ($elapsed / $total_window) * 100;
                $hours_left = round(($deadline - $currentTime) / 3600, 1);
            } elseif ($currentTime >= $deadline) {
                $time_remaining_percent = 100; // Window closed
            }
        }

        // Last Completed
        $last_stmt = $conn->query("SELECT m.name, wd.duty_date, wd.duty_time FROM water_duties wd JOIN members m ON wd.member_id = m.id WHERE wd.status = 'completed' ORDER BY wd.duty_date DESC LIMIT 1");
        $last_duty = $last_stmt->fetch(PDO::FETCH_ASSOC);

        // Tomorrow
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $tomorrow_stmt = $conn->prepare("SELECT wd.id, wd.duty_time, m.name FROM water_duties wd JOIN members m ON wd.member_id = m.id WHERE wd.duty_date = ? AND wd.status = 'pending'");
        $tomorrow_stmt->execute([$tomorrow]);
        $tomorrow_duty = $tomorrow_stmt->fetch(PDO::FETCH_ASSOC);

        // Upcoming
        $duties_stmt = $conn->query("SELECT wd.id, wd.duty_date, wd.duty_time, wd.status, m.name, wd.member_id, m.email FROM water_duties wd JOIN members m ON wd.member_id = m.id WHERE wd.duty_date >= CURDATE() ORDER BY wd.duty_date, wd.duty_time");
        $upcoming_duties = $duties_stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => [
                'today_duty' => $today_duty,
                'timer' => ['percent' => $time_remaining_percent, 'hours_left' => $hours_left],
                'last_duty' => $last_duty,
                'tomorrow_duty' => $tomorrow_duty,
                'members' => $conn->query("SELECT id, name FROM members WHERE is_active = 1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC),
                'upcoming_duties' => $upcoming_duties,
                'formatted_date' => date('F j, Y'),
                'min_date' => date('Y-m-d')
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- POST: Assign Duty ---
if (isset($_POST['assign_duty'])) {
    $member_id = $_POST['member_id'];
    $duty_date = $_POST['duty_date'];
    $duty_time = $_POST['duty_time'] ?: '09:00:00'; // Default 9 AM
    $send_email = isset($_POST['send_email_now']) ? true : false;

    try {
        $stmt = $conn->prepare("SELECT id FROM water_duties WHERE duty_date = ?");
        $stmt->execute([$duty_date]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => "Date already assigned!"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO water_duties (member_id, duty_date, duty_time, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$member_id, $duty_date, $duty_time]);

        $msg = "Assigned successfully.";
        if ($send_email) {
            $stmt = $conn->prepare("SELECT name, email FROM members WHERE id = ?");
            $stmt->execute([$member_id]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);
            sendWaterDutyEmail($member['email'], $member['name'], $duty_date, 'assignment', $duty_time);
            $msg .= " Email sent.";
        } else {
            $msg .= " Email skipped (manual).";
        }

        echo json_encode(['status' => 'success', 'message' => $msg, 'reload' => true]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- POST: Manual Email Trigger ---
if (isset($_POST['send_manual_email'])) {
    $duty_id = $_POST['duty_id'];
    try {
        $d = $conn->query("SELECT wd.duty_date, wd.duty_time, m.email, m.name FROM water_duties wd JOIN members m ON wd.member_id = m.id WHERE wd.id = $duty_id")->fetch(PDO::FETCH_ASSOC);
        if ($d) {
            sendWaterDutyEmail($d['email'], $d['name'], $d['duty_date'], 'assignment', $d['duty_time']);
            echo json_encode(['status' => 'success', 'message' => "Notification sent manually!"]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Duty not found."]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- POST: Edit Duty ---
if (isset($_POST['edit_duty'])) {
    $duty_id = $_POST['duty_id'];
    $member_id = $_POST['member_id'];
    $duty_date = $_POST['duty_date'];
    $duty_time = $_POST['duty_time'];

    try {
        $stmt = $conn->prepare("UPDATE water_duties SET member_id = ?, duty_date = ?, duty_time = ? WHERE id = ?");
        $stmt->execute([$member_id, $duty_date, $duty_time, $duty_id]);
        echo json_encode(['status' => 'success', 'message' => "Duty updated!", 'reload' => true]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- POST: Delete Duty ---
if (isset($_POST['delete_duty'])) {
    $duty_id = $_POST['duty_id'];
    $conn->prepare("DELETE FROM water_duties WHERE id = ?")->execute([$duty_id]);
    echo json_encode(['status' => 'success', 'message' => "Deleted.", 'reload' => true]);
    exit;
}

// --- POST: Mark Completed ---
if (isset($_POST['mark_completed'])) {
    $duty_id = $_POST['duty_id'];
    $conn->prepare("UPDATE water_duties SET status = 'completed' WHERE id = ?")->execute([$duty_id]);
    echo json_encode(['status' => 'success', 'message' => "Marked completed!", 'reload' => true]);
    exit;
}

// --- POST: Reminder ---
if (isset($_POST['send_reminder'])) {
    $duty_id = $_POST['duty_id'];
    $d = $conn->query("SELECT wd.duty_date, wd.duty_time, m.email, m.name FROM water_duties wd JOIN members m ON wd.member_id = m.id WHERE wd.id = $duty_id")->fetch(PDO::FETCH_ASSOC);
    sendWaterDutyEmail($d['email'], $d['name'], $d['duty_date'], 'reminder', $d['duty_time']);
    echo json_encode(['status' => 'success', 'message' => "Reminder sent!"]);
    exit;
}
?>