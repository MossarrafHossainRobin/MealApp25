<?php
// user_section/transaction_api.php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Include database connection
require_once __DIR__ . '/../config/database.php';

// Include email functions
require_once __DIR__ . '/transaction_email.php';

try {
    $db_instance = new Database();
    $conn = $db_instance->getConnection();

    $action = $_POST['action'] ?? '';

    if ($action === 'initiate_transaction') {
        $receiver_id = intval($_POST['receiver_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        // Validation
        if (empty($receiver_id) || empty($amount) || empty($description)) {
            throw new Exception('All fields are required');
        }

        if ($amount <= 0) {
            throw new Exception('Invalid amount');
        }

        // Get current month and year
        $current_year = date('Y');
        $current_month = date('m');
        $current_date = date('Y-m-d');

        // Check sender's total bazar for current month
        $bazar_stmt = $conn->prepare("
            SELECT COALESCE(SUM(amount), 0) as total_bazar 
            FROM bazar 
            WHERE member_id = ? AND YEAR(bazar_date) = ? AND MONTH(bazar_date) = ?
        ");
        $bazar_stmt->execute([$_SESSION['user_id'], $current_year, $current_month]);
        $sender_bazar_data = $bazar_stmt->fetch(PDO::FETCH_ASSOC);
        $sender_total_bazar = floatval($sender_bazar_data['total_bazar']);

        // Check if sender has enough bazar amount
        if ($sender_total_bazar < $amount) {
            throw new Exception('Insufficient bazar amount. You have ৳' . $sender_total_bazar . ' in bazar for ' . date('F Y'));
        }

        // Get sender info
        $sender_stmt = $conn->prepare("SELECT name, email FROM members WHERE id = ?");
        $sender_stmt->execute([$_SESSION['user_id']]);
        $sender_data = $sender_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sender_data) {
            throw new Exception('Sender not found');
        }

        $sender_name = $sender_data['name'];
        $sender_email = $sender_data['email'];

        // Check if receiver exists and is active
        $receiver_stmt = $conn->prepare("SELECT name, email FROM members WHERE id = ? AND is_active = 1");
        $receiver_stmt->execute([$receiver_id]);
        $receiver = $receiver_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$receiver) {
            throw new Exception('Invalid receiver selected');
        }

        // Generate OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Create transaction record
        $stmt = $conn->prepare("
            INSERT INTO transactions (sender_id, receiver_id, amount, description, otp_code, otp_expires_at, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");

        $result = $stmt->execute([
            $_SESSION['user_id'],
            $receiver_id,
            $amount,
            $description,
            $otp,
            $otp_expires
        ]);

        if (!$result) {
            throw new Exception('Failed to create transaction record');
        }

        $transaction_id = $conn->lastInsertId();

        // Log transaction initiation
        $log_stmt = $conn->prepare("
            INSERT INTO transaction_logs (transaction_id, action, details) 
            VALUES (?, ?, ?)
        ");
        $log_stmt->execute([
            $transaction_id,
            'initiated',
            'Transaction initiated. Amount: ৳' . $amount . ' to ' . $receiver['name'] .
            '. Sender bazar amount: ৳' . $sender_total_bazar
        ]);

        // Send OTP email
        $email_sent = sendTransactionOTP(
            $sender_email,
            $sender_name,
            $otp,
            $receiver['name'],
            $amount
        );

        if (!$email_sent) {
            // Log email failure but don't fail the transaction
            $log_stmt->execute([
                $transaction_id,
                'email_failed',
                'Failed to send OTP email'
            ]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'OTP sent successfully',
            'transaction_id' => $transaction_id,
            'debug_info' => [
                'sender_bazar_amount' => $sender_total_bazar,
                'amount' => $amount
            ],
            'debug_otp' => $otp // Remove this in production
        ]);

    } elseif ($action === 'verify_otp') {
        $transaction_id = intval($_POST['transaction_id'] ?? 0);
        $otp = trim($_POST['otp'] ?? '');

        if (empty($transaction_id) || empty($otp)) {
            throw new Exception('Transaction ID and OTP are required');
        }

        // Get transaction details
        $stmt = $conn->prepare("
            SELECT t.*, s.name as sender_name, s.email as sender_email, 
                   r.name as receiver_name, r.email as receiver_email 
            FROM transactions t 
            JOIN members s ON t.sender_id = s.id 
            JOIN members r ON t.receiver_id = r.id 
            WHERE t.id = ? AND t.sender_id = ?
        ");
        $stmt->execute([$transaction_id, $_SESSION['user_id']]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$transaction) {
            throw new Exception('Transaction not found');
        }

        if ($transaction['status'] != 'pending') {
            throw new Exception('Transaction already processed');
        }

        // Check OTP expiry
        if (strtotime($transaction['otp_expires_at']) < time()) {
            throw new Exception('OTP has expired');
        }

        // Verify OTP
        if ($transaction['otp_code'] != $otp) {
            throw new Exception('Invalid OTP');
        }

        // Get current month and year
        $current_year = date('Y');
        $current_month = date('m');
        $current_date = date('Y-m-d');

        // Double-check sender's bazar amount before proceeding
        $bazar_check_stmt = $conn->prepare("
            SELECT COALESCE(SUM(amount), 0) as total_bazar 
            FROM bazar 
            WHERE member_id = ? AND YEAR(bazar_date) = ? AND MONTH(bazar_date) = ?
        ");
        $bazar_check_stmt->execute([$_SESSION['user_id'], $current_year, $current_month]);
        $current_sender_bazar = floatval($bazar_check_stmt->fetch(PDO::FETCH_ASSOC)['total_bazar']);

        if ($current_sender_bazar < $transaction['amount']) {
            throw new Exception('Insufficient bazar amount. Available: ৳' . $current_sender_bazar);
        }

        // Start database transaction
        $conn->beginTransaction();

        try {
            // 1. Reduce sender's bazar amount (insert negative entry)
            $reduce_sender_bazar = $conn->prepare("
                INSERT INTO bazar (member_id, amount, bazar_count, description, bazar_date, created_at, updated_at) 
                VALUES (?, ?, 1, ?, ?, NOW(), NOW())
            ");
            $reduce_sender_bazar->execute([
                $_SESSION['user_id'],
                -$transaction['amount'], // Negative amount to reduce
                'Money transfer to ' . $transaction['receiver_name'] . ' - ' . $transaction['description'],
                $current_date
            ]);

            // 2. Add receiver's bazar amount (insert positive entry)
            $add_receiver_bazar = $conn->prepare("
                INSERT INTO bazar (member_id, amount, bazar_count, description, bazar_date, created_at, updated_at) 
                VALUES (?, ?, 1, ?, ?, NOW(), NOW())
            ");
            $add_receiver_bazar->execute([
                $transaction['receiver_id'],
                $transaction['amount'], // Positive amount to add
                'Money received from ' . $transaction['sender_name'] . ' - ' . $transaction['description'],
                $current_date
            ]);

            // 3. Update transaction status
            $update_transaction = $conn->prepare("
                UPDATE transactions 
                SET status = 'completed', otp_code = NULL, completed_at = NOW() 
                WHERE id = ?
            ");
            $update_transaction->execute([$transaction_id]);

            // 4. Get updated bazar amounts for logging
            $sender_bazar_stmt = $conn->prepare("
                SELECT COALESCE(SUM(amount), 0) as total_bazar 
                FROM bazar 
                WHERE member_id = ? AND YEAR(bazar_date) = ? AND MONTH(bazar_date) = ?
            ");
            $sender_bazar_stmt->execute([$_SESSION['user_id'], $current_year, $current_month]);
            $new_sender_bazar = floatval($sender_bazar_stmt->fetch(PDO::FETCH_ASSOC)['total_bazar']);

            $receiver_bazar_stmt = $conn->prepare("
                SELECT COALESCE(SUM(amount), 0) as total_bazar 
                FROM bazar 
                WHERE member_id = ? AND YEAR(bazar_date) = ? AND MONTH(bazar_date) = ?
            ");
            $receiver_bazar_stmt->execute([$transaction['receiver_id'], $current_year, $current_month]);
            $new_receiver_bazar = floatval($receiver_bazar_stmt->fetch(PDO::FETCH_ASSOC)['total_bazar']);

            // 5. Log successful transaction
            $log_stmt = $conn->prepare("
                INSERT INTO transaction_logs (transaction_id, action, details) 
                VALUES (?, ?, ?)
            ");
            $log_stmt->execute([
                $transaction_id,
                'completed',
                'Transaction completed via bazar. Amount: ৳' . $transaction['amount'] .
                '. New sender bazar: ৳' . $new_sender_bazar .
                '. New receiver bazar: ৳' . $new_receiver_bazar
            ]);

            $conn->commit();

            // Log successful completion
            error_log("Bazar Transaction Completed - ID: {$transaction_id}, Amount: {$transaction['amount']}, " .
                "Sender: {$_SESSION['user_id']} (New Bazar: {$new_sender_bazar}), " .
                "Receiver: {$transaction['receiver_id']} (New Bazar: {$new_receiver_bazar})");

            // Send confirmation emails
            $confirmation_sent = sendTransactionConfirmation(
                $transaction['sender_email'],
                $transaction['sender_name'],
                $transaction['receiver_email'],
                $transaction['receiver_name'],
                $transaction['amount'],
                $transaction['description']
            );

            if (!$confirmation_sent) {
                // Log email failure but don't fail the transaction
                $log_stmt->execute([
                    $transaction_id,
                    'confirmation_email_failed',
                    'Failed to send confirmation emails'
                ]);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Transaction completed successfully',
                'debug_info' => [
                    'new_sender_bazar' => $new_sender_bazar,
                    'new_receiver_bazar' => $new_receiver_bazar,
                    'transaction_amount' => $transaction['amount'],
                    'month' => date('F Y')
                ]
            ]);

        } catch (Exception $e) {
            $conn->rollBack();

            // Log transaction failure
            $log_stmt = $conn->prepare("
                INSERT INTO transaction_logs (transaction_id, action, details) 
                VALUES (?, ?, ?)
            ");
            $log_stmt->execute([
                $transaction_id,
                'failed',
                'Transaction failed: ' . $e->getMessage()
            ]);

            throw $e;
        }

    } else {
        throw new Exception('Invalid action: ' . $action);
    }

} catch (Exception $e) {
    // Log the error
    error_log("Transaction API Error: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>