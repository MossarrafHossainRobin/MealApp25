<?php
// user_section/transaction_email.php

// Include PHPMailer at the top of the file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require PHPMailer files
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';

function sendTransactionOTP($member_email, $member_name, $otp_code, $receiver_name, $amount)
{
    try {
        // For now, simulate email sending and log it
        error_log("OTP Email would be sent to: $member_email");
        error_log("Member: $member_name, OTP: $otp_code, Receiver: $receiver_name, Amount: ৳$amount");

        // Uncomment below to enable real email sending

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'meal.query@gmail.com';
        $mail->Password = 'ogjngrxxihxxtree';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('meal.query@gmail.com', 'Meal App25 Transaction');
        $mail->addAddress($member_email, $member_name);
        $mail->isHTML(true);

        $mail->Subject = "🔐 OTP Verification - Transaction of ৳$amount";
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px; background: #f8f9fa;'>
                <h2 style='color: #8e44ad; text-align: center;'>🔐 OTP Verification</h2>
                <p>Dear <strong>$member_name</strong>,</p>
                <p>You are attempting to transfer <strong>৳$amount</strong> to <strong>$receiver_name</strong>.</p>

                <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #8e44ad; margin: 20px 0; text-align: center;'>
                    <h3 style='margin: 0 0 10px 0; color: #2c3e50;'>Your OTP Code</h3>
                    <div style='font-size: 32px; font-weight: bold; color: #8e44ad; letter-spacing: 8px; background: #f8f9fa; padding: 15px; border-radius: 5px; border: 2px dashed #e9ecef;'>
                        $otp_code
                    </div>
                    <p style='margin: 10px 0 0 0; color: #666; font-size: 14px;'>This OTP will expire in 10 minutes</p>
                </div>

                <div style='background: #e8f4fd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                    <p style='margin: 0; font-size: 14px;'><strong>📋 Transaction Details:</strong></p>
                    <ul style='margin: 5px 0; padding-left: 20px;'>
                        <li><strong>Receiver:</strong> $receiver_name</li>
                        <li><strong>Amount:</strong> ৳$amount</li>
                        <li><strong>Time:</strong> " . date('Y-m-d h:i A') . "</li>
                    </ul>
                </div>

                <p style='color: #dc3545;'><strong>⚠️ Security Alert:</strong> Do not share this OTP with anyone. If you didn't initiate this transaction, please ignore this email.</p>
            </div>";

        return $mail->send();

        return true; // Return true for testing

    } catch (Exception $e) {
        error_log("Transaction OTP Email failed: " . $e->getMessage());
        return false;
    }
}

function sendTransactionConfirmation($sender_email, $sender_name, $receiver_email, $receiver_name, $amount, $description)
{
    try {
        // For now, simulate email sending and log it
        error_log("Confirmation Email would be sent to:");
        error_log("Sender: $sender_email ($sender_name)");
        error_log("Receiver: $receiver_email ($receiver_name)");
        error_log("Amount: ৳$amount, Description: $description");

        // Uncomment below to enable real email sending

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'meal.query@gmail.com';
        $mail->Password = 'ogjngrxxihxxtree';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('meal.query@gmail.com', 'Meal App25 Transaction');

        // Send to sender
        $mail->clearAddresses();
        $mail->addAddress($sender_email, $sender_name);
        $mail->isHTML(true);

        $mail->Subject = "✅ Transaction Successful - ৳$amount sent to $receiver_name";
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #28a745; border-radius: 10px; background: #d4edda;'>
                <h2 style='color: #155724; text-align: center;'>✅ Transaction Successful</h2>
                <p>Dear <strong>$sender_name</strong>,</p>

                <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745; margin: 20px 0;'>
                    <h3 style='margin: 0 0 15px 0; color: #155724;'>Payment Sent Successfully</h3>
                    <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 10px;'>
                        <div style='text-align: center;'>
                            <p style='margin: 0; font-weight: bold; color: #666;'>Amount Sent</p>
                            <p style='margin: 5px 0 0 0; font-size: 24px; color: #155724; font-weight: bold;'>৳$amount</p>
                        </div>
                        <div style='text-align: center;'>
                            <p style='margin: 0; font-weight: bold; color: #666;'>To</p>
                            <p style='margin: 5px 0 0 0; font-size: 18px; color: #155724; font-weight: bold;'>$receiver_name</p>
                        </div>
                    </div>

                    <div style='margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef;'>
                        <p style='margin: 0;'><strong>Description:</strong> $description</p>
                        <p style='margin: 5px 0 0 0;'><strong>Transaction Time:</strong> " . date('Y-m-d h:i A') . "</p>
                    </div>
                </div>

                <div style='background: #e8f4fd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                    <p style='margin: 0; font-size: 14px;'><strong>💡 Note:</strong> This transaction has been recorded in your account history. You can view it in the transactions section.</p>
                </div>
            </div>";

        $mail->send();

        // Send to receiver
        $mail->clearAddresses();
        $mail->addAddress($receiver_email, $receiver_name);

        $mail->Subject = "💰 Payment Received - ৳$amount from $sender_name";
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #17a2b8; border-radius: 10px; background: #d1ecf1;'>
                <h2 style='color: #0c5460; text-align: center;'>💰 Payment Received</h2>
                <p>Dear <strong>$receiver_name</strong>,</p>

                <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #17a2b8; margin: 20px 0;'>
                    <h3 style='margin: 0 0 15px 0; color: #0c5460;'>Payment Received Successfully</h3>
                    <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 10px;'>
                        <div style='text-align: center;'>
                            <p style='margin: 0; font-weight: bold; color: #666;'>Amount Received</p>
                            <p style='margin: 5px 0 0 0; font-size: 24px; color: #0c5460; font-weight: bold;'>৳$amount</p>
                        </div>
                        <div style='text-align: center;'>
                            <p style='margin: 0; font-weight: bold; color: #666;'>From</p>
                            <p style='margin: 5px 0 0 0; font-size: 18px; color: #0c5460; font-weight: bold;'>$sender_name</p>
                        </div>
                    </div>

                    <div style='margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef;'>
                        <p style='margin: 0;'><strong>Description:</strong> $description</p>
                        <p style='margin: 5px 0 0 0;'><strong>Transaction Time:</strong> " . date('Y-m-d h:i A') . "</p>
                    </div>
                </div>

                <div style='background: #e8f4fd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                    <p style='margin: 0; font-size: 14px;'><strong>💡 Note:</strong> This amount has been added to your account balance. You can view it in your dashboard.</p>
                </div>
            </div>";

        return $mail->send();


        return true; // Return true for testing

    } catch (Exception $e) {
        error_log("Transaction Confirmation Email failed: " . $e->getMessage());
        return false;
    }
}
?>