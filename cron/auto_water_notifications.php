<?php
// cron/auto_water_notifications.php - Run this daily via cron job
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';
require '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Get today's duty
$today = date('Y-m-d');
$stmt = $conn->prepare("
    SELECT wd.id, m.name, m.email 
    FROM water_duties wd 
    JOIN members m ON wd.member_id = m.id 
    WHERE wd.duty_date = ? AND wd.status = 'pending'
");
$stmt->execute([$today]);
$today_duty = $stmt->fetch(PDO::FETCH_ASSOC);

if ($today_duty) {
    // Send today's notification
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
        $mail->addAddress($today_duty['email'], $today_duty['name']);

        $mail->isHTML(true);
        $mail->Subject = "Water Duty Today - $today";
        $mail->Body = "
            <h3>Water Duty Today!</h3>
            <p>Hello {$today_duty['name']},</p>
            <p>This is to remind you that <strong>today is your water duty day ($today)</strong>.</p>
            <p>Please ensure you provide water for all members today.</p>
            <p>Thank you for your cooperation!</p>
            <br>
            <p><em>Meal App25 Water Management System</em></p>
        ";

        if ($mail->send()) {
            echo "Auto notification sent to {$today_duty['name']} for today's water duty.";
        } else {
            echo "Failed to send auto notification.";
        }
    } catch (Exception $e) {
        echo "Error sending auto notification: {$mail->ErrorInfo}";
    }
} else {
    echo "No water duty found for today.";
}
?>