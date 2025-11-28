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
function sendWaterDutyEmail($member_email, $member_name, $duty_date, $type = 'assignment', $time_info = '', $previous_member_name = '', $completion_time = '')
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
            $mail->Subject = "💧 Water Duty Assignment - $duty_date";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px; background: #f8f9fa;'>
                    <h2 style='color: #4e73df; text-align: center;'>💧 Water Duty Assignment</h2>
                    <p>Dear <strong>$member_name</strong>,</p>
                    <p>You have been assigned water duty on:</p>
                    <div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #4e73df; margin: 15px 0;'>
                        <h3 style='margin: 0; color: #2c3e50;'>📅 $duty_date</h3>
                        <h4 style='margin: 5px 0 0 0; color: #7d3c98;'>⏰ $formatted_time</h4>
                    </div>
                    <p><strong>Action Required:</strong> Please ensure water is available by this time and mark it as completed in the system.</p>
                    <div style='background: #e8f4fd; padding: 10px; border-radius: 5px; margin: 15px 0;'>
                        <p style='margin: 0; font-size: 14px;'><strong>⏰ Reminder Timeline:</strong></p>
                        <ul style='margin: 5px 0; padding-left: 20px;'>
                            <li>30 mins: First reminder if not completed</li>
                            <li>1.5 hours: Final warning</li>
                            <li>4 hours: Auto-reassign to another member</li>
                        </ul>
                    </div>
                </div>";
        } elseif ($type === 'first_reminder') {
            $mail->Subject = "🔔 REMINDER: Water Duty Pending - $duty_date";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ffc107; border-radius: 10px; background: #fffbf0;'>
                    <h2 style='color: #856404; text-align: center;'>🔔 First Reminder</h2>
                    <p>Dear <strong>$member_name</strong>,</p>
                    <p>This is a friendly reminder that your water duty for <strong>$duty_date at $formatted_time</strong> is still pending.</p>
                    <div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 15px 0;'>
                        <p style='margin: 0;'><strong>Please complete your duty as soon as possible.</strong></p>
                    </div>
                    <p>If not completed within 1 hour, a final warning will be sent before automatic reassignment.</p>
                </div>";
        } elseif ($type === 'final_warning') {
            $mail->Subject = "⚠️ FINAL WARNING: Water Duty Almost Due - $duty_date";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #dc3545; border-radius: 10px; background: #f8d7da;'>
                    <h2 style='color: #721c24; text-align: center;'>⚠️ Final Warning</h2>
                    <p>Dear <strong>$member_name</strong>,</p>
                    <p>Your water duty for <strong>$duty_date at $formatted_time</strong> is still not completed!</p>
                    <div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545; margin: 15px 0;'>
                        <p style='margin: 0; color: #dc3545;'><strong>This is your FINAL WARNING.</strong></p>
                        <p style='margin: 5px 0 0 0;'>If not completed within 2.5 hours, your duty will be automatically reassigned to another member.</p>
                    </div>
                </div>";
        } elseif ($type === 'reassigned_to') {
            $mail->Subject = "🚨 URGENT: Water Duty Reassigned to You - $duty_date";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #dc3545; border-radius: 10px; background: #f8d7da;'>
                    <h2 style='color: #721c24; text-align: center;'>🚨 URGENT: Duty Reassignment</h2>
                    <p>Dear <strong>$member_name</strong>,</p>";

            if ($previous_member_name) {
                $mail->Body .= "<p>The previous assignee <strong>$previous_member_name</strong> did not complete the water duty despite multiple reminders.</p>";
            } else {
                $mail->Body .= "<p>The previous assignee missed the deadline and did not complete the water duty.</p>";
            }

            $mail->Body .= "
                    <div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545; margin: 15px 0;'>
                        <h3 style='margin: 0; color: #2c3e50;'>📅 $duty_date</h3>
                        <h4 style='margin: 5px 0 0 0; color: #7d3c98;'>⏰ $formatted_time</h4>
                    </div>
                    <p><strong>Action Required Immediately:</strong> Please ensure water is available as soon as possible.</p>
                    <p style='color: #dc3545;'><strong>Note:</strong> This is an urgent reassignment. Please prioritize this duty.</p>
                </div>";
        } elseif ($type === 'reassigned_from') {
            $mail->Subject = "ℹ️ Water Duty Reassigned - $duty_date";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #6c757d; border-radius: 10px; background: #f8f9fa;'>
                    <h2 style='color: #495057; text-align: center;'>ℹ️ Duty Reassignment Notice</h2>
                    <p>Dear <strong>$member_name</strong>,</p>
                    <p>Your water duty for <strong>$duty_date</strong> has been reassigned to another member since the 4-hour deadline passed without completion.</p>
                    <div style='background: #e9ecef; padding: 10px; border-radius: 5px; margin: 15px 0;'>
                        <p style='margin: 0;'><strong>Reason:</strong> Automatic reassignment due to missed deadline</p>
                    </div>
                </div>";
        } elseif ($type === 'reminder') {
            $mail->Subject = "🔔 Reminder: Water Duty Tomorrow - $duty_date";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #17a2b8; border-radius: 10px; background: #d1ecf1;'>
                    <h2 style='color: #0c5460; text-align: center;'>🔔 Tomorrow's Duty Reminder</h2>
                    <p>Dear <strong>$member_name</strong>,</p>
                    <p>This is a reminder that you have water duty scheduled for tomorrow:</p>
                    <div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #17a2b8; margin: 15px 0;'>
                        <h3 style='margin: 0; color: #2c3e50;'>📅 $duty_date</h3>
                        <h4 style='margin: 5px 0 0 0; color: #7d3c98;'>⏰ $formatted_time</h4>
                    </div>
                    <p>Please be prepared to complete your duty on time.</p>
                </div>";
        } elseif ($type === 'next_in_line') {
            $mail->Subject = "🔔 You're Next: Water Duty Assignment - $duty_date";
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #28a745; border-radius: 10px; background: #d4edda;'>
                    <h2 style='color: #155724; text-align: center;'>🔔 You're Next in Line</h2>
                    <p>Dear <strong>$member_name</strong>,</p>
                    
                    <div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745; margin: 15px 0;'>
                        <h4 style='margin: 0 0 10px 0; color: #155724;'>✅ Previous Duty Completed</h4>
                        <p style='margin: 5px 0;'><strong>Previous Member:</strong> $previous_member_name</p>
                        <p style='margin: 5px 0;'><strong>Completion Time:</strong> $completion_time</p>
                        <p style='margin: 5px 0;'><strong>Water Provided:</strong> Filter has been refilled</p>
                    </div>
                    
                    <div style='background: #e8f4fd; padding: 15px; border-radius: 8px; margin: 15px 0;'>
                        <h4 style='margin: 0 0 10px 0; color: #0c5460;'>📅 Your Upcoming Duty</h4>
                        <p style='margin: 5px 0;'><strong>Date:</strong> $duty_date</p>
                        <p style='margin: 5px 0;'><strong>Time:</strong> $formatted_time</p>
                    </div>
                    
                    <p><strong>Action Required:</strong> Please be prepared to provide water at your scheduled time. After completing your duty, please confirm in the system that you have refilled the water filter.</p>
                    
                    <div style='background: #fff3cd; padding: 10px; border-radius: 5px; margin: 15px 0;'>
                        <p style='margin: 0; font-size: 14px;'><strong>📋 Important Instructions:</strong></p>
                        <ul style='margin: 5px 0; padding-left: 20px;'>
                            <li>Check water filter level before your scheduled time</li>
                            <li>Refill the filter if needed</li>
                            <li>After completion, mark the duty as done in the system</li>
                            <li>This will automatically notify the next person in line</li>
                        </ul>
                    </div>
                    
                    <p style='color: #155724;'><strong>Thank you for maintaining our water supply system!</strong></p>
                </div>";
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

// --- Get next duty date (tomorrow or next available) ---
function getNextDutyDate()
{
    global $conn;

    // Find the next date without a duty assignment
    $stmt = $conn->query("
        SELECT DATE_ADD(CURDATE(), INTERVAL 1 DAY) as next_date 
        FROM DUAL 
        WHERE NOT EXISTS (
            SELECT 1 FROM water_duties 
            WHERE duty_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
        )
        UNION
        SELECT DATE_ADD(MAX(duty_date), INTERVAL 1 DAY) as next_date 
        FROM water_duties 
        WHERE duty_date >= CURDATE()
        HAVING next_date IS NOT NULL
        LIMIT 1
    ");

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['next_date'] : date('Y-m-d', strtotime('+1 day'));
}

// --- Automation: Check and Send Reminders ---
function checkAndSendReminders()
{
    global $conn;

    $today = date('Y-m-d');
    $currentTime = time();

    // Get all pending duties for today
    $stmt = $conn->prepare("
        SELECT wd.id, wd.member_id, wd.duty_time, wd.duty_date, wd.assigned_at, wd.last_reminder_sent, 
               m.name, m.email, m.is_active 
        FROM water_duties wd 
        JOIN members m ON wd.member_id = m.id 
        WHERE wd.duty_date = ? AND wd.status = 'pending' AND m.is_active = 1
    ");
    $stmt->execute([$today]);
    $pending_duties = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pending_duties as $duty) {
        $scheduledTime = $duty['duty_time'] ?: '09:00:00';
        $scheduledTimestamp = strtotime("$today $scheduledTime");
        $timeSinceScheduled = $currentTime - $scheduledTimestamp;

        // First reminder after 30 minutes
        if ($timeSinceScheduled >= 1800 && $timeSinceScheduled < 5400) { // 30 mins to 1.5 hours
            if (!$duty['last_reminder_sent'] || $duty['last_reminder_sent'] === 'first_reminder') {
                sendWaterDutyEmail($duty['email'], $duty['name'], $duty['duty_date'], 'first_reminder', $duty['duty_time']);

                // Update last reminder sent
                $update_stmt = $conn->prepare("UPDATE water_duties SET last_reminder_sent = 'first_reminder' WHERE id = ?");
                $update_stmt->execute([$duty['id']]);

                error_log("First reminder sent to {$duty['name']} for duty {$duty['duty_date']}");
            }
        }
        // Final warning after 1.5 hours
        elseif ($timeSinceScheduled >= 5400 && $timeSinceScheduled < 14400) { // 1.5 hours to 4 hours
            if ($duty['last_reminder_sent'] !== 'final_warning') {
                sendWaterDutyEmail($duty['email'], $duty['name'], $duty['duty_date'], 'final_warning', $duty['duty_time']);

                // Update last reminder sent
                $update_stmt = $conn->prepare("UPDATE water_duties SET last_reminder_sent = 'final_warning' WHERE id = ?");
                $update_stmt->execute([$duty['id']]);

                error_log("Final warning sent to {$duty['name']} for duty {$duty['duty_date']}");
            }
        }
    }
}

// --- Sequential Assignment System ---
function getNextAvailableMember($exclude_member_id = null)
{
    global $conn;

    // Get active members ordered by last assigned date (oldest first)
    $query = "
        SELECT m.id, m.name, m.email, 
               COALESCE(MAX(wd.duty_date), '2000-01-01') as last_duty_date,
               COUNT(wd.id) as duty_count
        FROM members m 
        LEFT JOIN water_duties wd ON m.id = wd.member_id 
        WHERE m.is_active = 1 
        AND m.id != ?
        GROUP BY m.id, m.name, m.email 
        ORDER BY last_duty_date ASC, duty_count ASC, RAND()
        LIMIT 1
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([$exclude_member_id ?: 0]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- Notify next person in line ---
function notifyNextInLine($completed_member_name, $completion_time)
{
    global $conn;

    $next_date = getNextDutyDate();

    // Check if next date already has assignment
    $check_stmt = $conn->prepare("SELECT id FROM water_duties WHERE duty_date = ?");
    $check_stmt->execute([$next_date]);

    if ($check_stmt->rowCount() > 0) {
        // Next date already assigned, get that member
        $duty_stmt = $conn->prepare("
            SELECT wd.member_id, m.name, m.email, wd.duty_time 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.duty_date = ?
        ");
        $duty_stmt->execute([$next_date]);
        $next_duty = $duty_stmt->fetch(PDO::FETCH_ASSOC);

        if ($next_duty) {
            // Send next-in-line notification
            sendWaterDutyEmail(
                $next_duty['email'],
                $next_duty['name'],
                $next_date,
                'next_in_line',
                $next_duty['duty_time'],
                $completed_member_name,
                $completion_time
            );
            error_log("Next-in-line notification sent to {$next_duty['name']} for {$next_date}");
        }
    } else {
        // No assignment for next date, assign sequentially and notify
        $next_member = getNextAvailableMember();
        if ($next_member) {
            // Create assignment for next date
            $insert_stmt = $conn->prepare("
                INSERT INTO water_duties (member_id, duty_date, duty_time, status, assigned_at) 
                VALUES (?, ?, '09:00:00', 'pending', NOW())
            ");
            $insert_stmt->execute([$next_member['id'], $next_date]);

            // Send next-in-line notification
            sendWaterDutyEmail(
                $next_member['email'],
                $next_member['name'],
                $next_date,
                'next_in_line',
                '09:00:00',
                $completed_member_name,
                $completion_time
            );
            error_log("Auto-assigned and notified {$next_member['name']} for {$next_date}");
        }
    }
}

// --- GET: Fetch Data & Run Automation ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $today = date('Y-m-d');
        $currentTime = time();

        // Run automation checks
        checkAndSendReminders();

        // 1. FETCH TODAY'S DUTY FIRST
        $check_stmt = $conn->prepare("
            SELECT wd.id, wd.member_id, wd.duty_time, wd.status, wd.last_reminder_sent, wd.completed_at,
                   m.name, m.email 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.duty_date = ? AND wd.status = 'pending'
        ");
        $check_stmt->execute([$today]);
        $current_today_duty = $check_stmt->fetch(PDO::FETCH_ASSOC);

        // 2. AUTO-REASSIGNMENT LOGIC (Enhanced 4-Hour Rule with Sequential Assignment)
        if ($current_today_duty && !isset($_SESSION['reassigned_' . $today])) {
            $scheduledTime = $current_today_duty['duty_time'] ?: '09:00:00';
            $deadlineTimestamp = strtotime("$today $scheduledTime") + (4 * 60 * 60);

            if ($currentTime > $deadlineTimestamp) {
                // Find next available member using sequential assignment
                $replacement = getNextAvailableMember($current_today_duty['member_id']);

                if ($replacement) {
                    $update_stmt = $conn->prepare("UPDATE water_duties SET member_id = ?, last_reminder_sent = NULL WHERE id = ?");
                    $update_stmt->execute([$replacement['id'], $current_today_duty['id']]);

                    // Send emails
                    sendWaterDutyEmail($current_today_duty['email'], $current_today_duty['name'], $today, 'reassigned_from');
                    sendWaterDutyEmail($replacement['email'], $replacement['name'], $today, 'reassigned_to', $scheduledTime, $current_today_duty['name']);

                    $_SESSION['reassigned_' . $today] = true;
                    error_log("Auto-reassigned duty from {$current_today_duty['name']} to {$replacement['name']}");
                }
            }
        }

        // 3. RE-FETCH DATA FOR UI
        $today_stmt = $conn->prepare("
            SELECT wd.id, wd.member_id, wd.duty_time, wd.status, wd.last_reminder_sent, wd.completed_at,
                   m.name, m.email 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.duty_date = ?
        ");
        $today_stmt->execute([$today]);
        $today_duty = $today_stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate remaining time for UI with enhanced status
        $time_remaining_percent = 0;
        $hours_left = 0;
        $reminder_status = 'none';

        if ($today_duty && $today_duty['status'] == 'pending') {
            $sTime = $today_duty['duty_time'] ?: '09:00:00';
            $deadline = strtotime("$today $sTime") + (4 * 60 * 60);
            $total_window = 4 * 60 * 60;
            $elapsed = $currentTime - strtotime("$today $sTime");

            if ($currentTime < $deadline && $currentTime > strtotime("$today $sTime")) {
                $time_remaining_percent = ($elapsed / $total_window) * 100;
                $hours_left = round(($deadline - $currentTime) / 3600, 1);

                // Set reminder status
                if ($elapsed >= 5400) { // 1.5 hours
                    $reminder_status = 'final_warning';
                } elseif ($elapsed >= 1800) { // 30 minutes
                    $reminder_status = 'first_reminder';
                }
            } elseif ($currentTime >= $deadline) {
                $time_remaining_percent = 100;
                $reminder_status = 'overdue';
            }
        }

        // Last Completed with completion time
        $last_stmt = $conn->query("
            SELECT m.name, wd.duty_date, wd.duty_time, wd.completed_at 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.status = 'completed' 
            ORDER BY wd.completed_at DESC 
            LIMIT 1
        ");
        $last_duty = $last_stmt->fetch(PDO::FETCH_ASSOC);

        // Tomorrow
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $tomorrow_stmt = $conn->prepare("
            SELECT wd.id, wd.duty_time, m.name, wd.member_id 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.duty_date = ? AND wd.status = 'pending'
        ");
        $tomorrow_stmt->execute([$tomorrow]);
        $tomorrow_duty = $tomorrow_stmt->fetch(PDO::FETCH_ASSOC);

        // Upcoming duties with enhanced info
        $duties_stmt = $conn->query("
            SELECT wd.id, wd.duty_date, wd.duty_time, wd.status, wd.last_reminder_sent, wd.completed_at,
                   m.name, wd.member_id, m.email 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.duty_date >= CURDATE() 
            ORDER BY wd.duty_date, wd.duty_time
        ");
        $upcoming_duties = $duties_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Member statistics for sequential assignment
        $member_stats_stmt = $conn->query("
            SELECT m.id, m.name, 
                   COUNT(wd.id) as total_duties,
                   MAX(wd.duty_date) as last_duty_date
            FROM members m 
            LEFT JOIN water_duties wd ON m.id = wd.member_id 
            WHERE m.is_active = 1
            GROUP BY m.id, m.name 
            ORDER BY last_duty_date ASC, total_duties ASC
        ");
        $member_stats = $member_stats_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Next available duty date
        $next_available_date = getNextDutyDate();

        echo json_encode([
            'status' => 'success',
            'data' => [
                'today_duty' => $today_duty,
                'timer' => [
                    'percent' => $time_remaining_percent,
                    'hours_left' => $hours_left,
                    'reminder_status' => $reminder_status
                ],
                'last_duty' => $last_duty,
                'tomorrow_duty' => $tomorrow_duty,
                'members' => $conn->query("SELECT id, name FROM members WHERE is_active = 1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC),
                'upcoming_duties' => $upcoming_duties,
                'member_stats' => $member_stats,
                'next_available_date' => $next_available_date,
                'formatted_date' => date('F j, Y'),
                'min_date' => date('Y-m-d')
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- POST: Assign Duty (Enhanced with Sequential Option) ---
if (isset($_POST['assign_duty'])) {
    $member_id = $_POST['member_id'];
    $duty_date = $_POST['duty_date'];
    $duty_time = $_POST['duty_time'] ?: '09:00:00';
    $send_email = isset($_POST['send_email_now']) ? true : false;
    $assign_sequentially = isset($_POST['assign_sequentially']) ? true : false;

    try {
        // Check if date already assigned
        $stmt = $conn->prepare("SELECT id FROM water_duties WHERE duty_date = ?");
        $stmt->execute([$duty_date]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => "Date already assigned!"]);
            exit;
        }

        // If sequential assignment requested, get next available member
        if ($assign_sequentially && $member_id === 'sequential') {
            $next_member = getNextAvailableMember();
            if ($next_member) {
                $member_id = $next_member['id'];
            } else {
                echo json_encode(['status' => 'error', 'message' => "No available members found for sequential assignment!"]);
                exit;
            }
        }

        // Insert duty with assigned_at timestamp
        $stmt = $conn->prepare("INSERT INTO water_duties (member_id, duty_date, duty_time, status, assigned_at) VALUES (?, ?, ?, 'pending', NOW())");
        $stmt->execute([$member_id, $duty_date, $duty_time]);

        $msg = "Assigned successfully.";
        if ($assign_sequentially) {
            $msg .= " (Sequentially assigned to next available member)";
        }

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

// --- POST: Mark Completed (Enhanced with Next-in-Line Notification) ---
if (isset($_POST['mark_completed'])) {
    $duty_id = $_POST['duty_id'];

    try {
        // Get duty info before marking completed
        $duty_info_stmt = $conn->prepare("
            SELECT wd.member_id, wd.duty_date, m.name, m.email 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.id = ?
        ");
        $duty_info_stmt->execute([$duty_id]);
        $duty_info = $duty_info_stmt->fetch(PDO::FETCH_ASSOC);

        if ($duty_info) {
            // Mark as completed with timestamp
            $update_stmt = $conn->prepare("UPDATE water_duties SET status = 'completed', completed_at = NOW() WHERE id = ?");
            $update_stmt->execute([$duty_id]);

            $completion_time = date('F j, Y \a\t h:i A');

            // Notify next person in line
            notifyNextInLine($duty_info['name'], $completion_time);

            echo json_encode([
                'status' => 'success',
                'message' => "Marked completed! Next person has been notified.",
                'reload' => true
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Duty not found."]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- POST: Manual Reminder Trigger ---
if (isset($_POST['send_manual_reminder'])) {
    $duty_id = $_POST['duty_id'];
    $reminder_type = $_POST['reminder_type'] ?? 'first_reminder';

    try {
        $d = $conn->query("
            SELECT wd.duty_date, wd.duty_time, m.email, m.name, wd.member_id 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.id = $duty_id
        ")->fetch(PDO::FETCH_ASSOC);

        if ($d) {
            sendWaterDutyEmail($d['email'], $d['name'], $d['duty_date'], $reminder_type, $d['duty_time']);

            // Update last reminder sent
            $update_stmt = $conn->prepare("UPDATE water_duties SET last_reminder_sent = ? WHERE id = ?");
            $update_stmt->execute([$reminder_type, $duty_id]);

            echo json_encode(['status' => 'success', 'message' => ucfirst(str_replace('_', ' ', $reminder_type)) . " sent manually!"]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Duty not found."]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- POST: Manual Email Trigger ---
if (isset($_POST['send_manual_email'])) {
    $duty_id = $_POST['duty_id'];
    try {
        $d = $conn->query("SELECT wd.duty_date, wd.duty_time, m.email, m.name, wd.member_id FROM water_duties wd JOIN members m ON wd.member_id = m.id WHERE wd.id = $duty_id")->fetch(PDO::FETCH_ASSOC);
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
        $stmt = $conn->prepare("UPDATE water_duties SET member_id = ?, duty_date = ?, duty_time = ?, last_reminder_sent = NULL WHERE id = ?");
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

// --- POST: Reminder ---
if (isset($_POST['send_reminder'])) {
    $duty_id = $_POST['duty_id'];
    try {
        $d = $conn->query("SELECT wd.duty_date, wd.duty_time, m.email, m.name, wd.member_id FROM water_duties wd JOIN members m ON wd.member_id = m.id WHERE wd.id = $duty_id")->fetch(PDO::FETCH_ASSOC);
        sendWaterDutyEmail($d['email'], $d['name'], $d['duty_date'], 'reminder', $d['duty_time']);
        echo json_encode(['status' => 'success', 'message' => "Reminder sent!"]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- POST: Force Reassign ---
if (isset($_POST['force_reassign'])) {
    $duty_id = $_POST['duty_id'];

    try {
        // Get current duty info
        $current_stmt = $conn->prepare("
            SELECT wd.member_id, wd.duty_date, wd.duty_time, m.name, m.email 
            FROM water_duties wd 
            JOIN members m ON wd.member_id = m.id 
            WHERE wd.id = ?
        ");
        $current_stmt->execute([$duty_id]);
        $current_duty = $current_stmt->fetch(PDO::FETCH_ASSOC);

        if ($current_duty) {
            // Find replacement
            $replacement = getNextAvailableMember($current_duty['member_id']);

            if ($replacement) {
                // Update duty
                $update_stmt = $conn->prepare("UPDATE water_duties SET member_id = ?, last_reminder_sent = NULL WHERE id = ?");
                $update_stmt->execute([$replacement['id'], $duty_id]);

                // Send emails
                sendWaterDutyEmail($current_duty['email'], $current_duty['name'], $current_duty['duty_date'], 'reassigned_from');
                sendWaterDutyEmail($replacement['email'], $replacement['name'], $current_duty['duty_date'], 'reassigned_to', $current_duty['duty_time'], $current_duty['name']);

                echo json_encode(['status' => 'success', 'message' => "Duty reassigned from {$current_duty['name']} to {$replacement['name']}!", 'reload' => true]);
            } else {
                echo json_encode(['status' => 'error', 'message' => "No available members found for reassignment!"]);
            }
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
?>