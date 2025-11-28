<?php
// user_section/notification.php

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Include database connection
require_once __DIR__ . '/../config/database.php';

// Handle mark as read actions - MUST BE AT THE TOP BEFORE ANY HTML OUTPUT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_request'])) {
    $db_instance = new Database();
    $conn = $db_instance->getConnection();

    if (isset($_POST['mark_as_read']) && isset($_POST['notification_id']) && isset($_POST['notification_type'])) {
        $notification_id = $_POST['notification_id'];
        $notification_type = $_POST['notification_type'];

        try {
            if ($notification_type === 'transaction') {
                $stmt = $conn->prepare("UPDATE transaction_logs SET read_status = TRUE WHERE id = ?");
                $stmt->execute([$notification_id]);
            } elseif ($notification_type === 'bazar_request') {
                $stmt = $conn->prepare("UPDATE bazar_requests SET read_status = TRUE WHERE id = ?");
                $stmt->execute([$notification_id]);
            } elseif ($notification_type === 'bazar_approved') {
                $stmt = $conn->prepare("UPDATE bazar SET notification_read = TRUE WHERE id = ?");
                $stmt->execute([$notification_id]);
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        } catch (PDOException $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    // Mark all as read
    if (isset($_POST['mark_all_read'])) {
        try {
            // Mark all transaction notifications as read
            $stmt1 = $conn->prepare("
                UPDATE transaction_logs 
                SET read_status = TRUE 
                WHERE read_status = FALSE 
                AND id IN (
                    SELECT tl.id 
                    FROM transaction_logs tl
                    INNER JOIN transactions t ON tl.transaction_id = t.id
                    WHERE (t.sender_id = ? OR t.receiver_id = ?)
                    AND tl.action = 'completed'
                )
            ");
            $stmt1->execute([$_SESSION['user_id'], $_SESSION['user_id']]);

            // Mark all bazar request notifications as read
            $stmt2 = $conn->prepare("
                UPDATE bazar_requests 
                SET read_status = TRUE 
                WHERE member_id = ? 
                AND read_status = FALSE
            ");
            $stmt2->execute([$_SESSION['user_id']]);

            // Mark all bazar approved notifications as read
            $stmt3 = $conn->prepare("
                UPDATE bazar 
                SET notification_read = TRUE 
                WHERE member_id = ? 
                AND notification_read = FALSE
            ");
            $stmt3->execute([$_SESSION['user_id']]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'All notifications marked as read!']);
            exit;
        } catch (PDOException $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            exit;
        }
    }
}

// Get user notifications
$db_instance = new Database();
$conn = $db_instance->getConnection();

$user_id = $_SESSION['user_id'];
$notifications = [];

try {
    // Get transaction notifications (only completed)
    $transaction_stmt = $conn->prepare("
        SELECT 
            tl.id,
            tl.transaction_id,
            tl.action,
            tl.details,
            tl.created_at,
            tl.read_status,
            'transaction' as type,
            t.sender_id,
            t.receiver_id,
            t.amount,
            t.description,
            s.name as sender_name,
            s.id as sender_member_id,
            r.name as receiver_name,
            r.id as receiver_member_id,
            t.status as transaction_status
        FROM transaction_logs tl
        INNER JOIN transactions t ON tl.transaction_id = t.id
        LEFT JOIN members s ON t.sender_id = s.id
        LEFT JOIN members r ON t.receiver_id = r.id
        WHERE (t.sender_id = ? OR t.receiver_id = ?)
        AND tl.action = 'completed'
        ORDER BY tl.created_at DESC
        LIMIT 100
    ");
    $transaction_stmt->execute([$user_id, $user_id]);
    $transaction_notifications = $transaction_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get bazar request notifications
    $bazar_stmt = $conn->prepare("
        SELECT 
            br.id,
            br.member_id,
            br.amount,
            br.description,
            br.rejection_reason,
            br.bazar_date,
            br.status,
            br.created_at,
            br.updated_at,
            'bazar_request' as type,
            COALESCE(br.read_status, FALSE) as read_status,
            m.name as member_name,
            m.id as member_member_id
        FROM bazar_requests br
        LEFT JOIN members m ON br.member_id = m.id
        WHERE br.member_id = ? 
        AND br.status IN ('approved', 'pending', 'rejected')
        AND br.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY)
        ORDER BY br.created_at DESC
        LIMIT 100
    ");
    $bazar_stmt->execute([$user_id]);
    $bazar_notifications = $bazar_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get bazar approved notifications (from bazar table)
    $bazar_approved_stmt = $conn->prepare("
        SELECT 
            b.id,
            b.member_id,
            b.amount,
            b.description,
            b.bazar_date,
            b.created_at,
            'bazar_approved' as type,
            COALESCE(b.notification_read, FALSE) as read_status,
            m.name as member_name,
            m.id as member_member_id,
            'approved' as status
        FROM bazar b
        LEFT JOIN members m ON b.member_id = m.id
        WHERE b.member_id = ? 
        AND b.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY)
        ORDER BY b.created_at DESC
        LIMIT 100
    ");
    $bazar_approved_stmt->execute([$user_id]);
    $bazar_approved_notifications = $bazar_approved_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Merge all notifications
    $all_notifications = array_merge($transaction_notifications, $bazar_notifications, $bazar_approved_notifications);

    // Sort by creation date (newest first)
    usort($all_notifications, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    // Group notifications by date
    $grouped_notifications = [];
    foreach ($all_notifications as $notification) {
        $date_key = date('Y-m-d', strtotime($notification['created_at']));
        if (!isset($grouped_notifications[$date_key])) {
            $grouped_notifications[$date_key] = [];
        }
        $grouped_notifications[$date_key][] = $notification;
    }

} catch (PDOException $e) {
    error_log("Error loading notifications: " . $e->getMessage());
    $grouped_notifications = [];
}

$total_notifications = count($all_notifications);
$unread_count = 0;
foreach ($all_notifications as $notification) {
    if (!$notification['read_status']) {
        $unread_count++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - MealApp25</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #d63384;
            --primary-dark: #c2185b;
            --primary-light: #f8bbd9;
            --gradient: linear-gradient(135deg, #d63384, #e91e63);
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --bazar-color: #6f42c1;
            --bazar-approved: #20c997;
            --today: #e8f5e8;
            --yesterday: #f0f0f0;
        }

        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding-bottom: 80px;
        }

        .notification-header {
            background: var(--gradient);
            color: white;
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .date-group-header {
            background: white;
            padding: 10px 12px;
            margin: 12px 0 6px 0;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--primary);
            border-left: 3px solid var(--primary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .date-group-header.today {
            background: var(--today);
            border-left-color: var(--success);
        }

        .date-group-header.yesterday {
            background: var(--yesterday);
            border-left-color: var(--warning);
        }

        .notification-item {
            background: white;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
            position: relative;
        }

        .notification-item.unread {
            border-left: 3px solid #00c853;
            background: #f8fff9;
        }

        .notification-item.read {
            border-left: 3px solid #dee2e6;
        }

        .notification-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            flex-shrink: 0;
            margin-right: 12px;
        }

        .transaction .notification-icon {
            background: var(--gradient);
        }

        .bazar_request .notification-icon {
            background: var(--bazar-color);
        }

        .bazar_approved .notification-icon {
            background: var(--bazar-approved);
        }

        /* Larger Fiverr-style Message Icon */
        .message-status {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
            margin-left: 8px;
            border: 2px solid transparent;
        }

        .message-status.unread {
            background: #00c853;
            color: white;
            border-color: #00c853;
            animation: pulse 2s infinite;
        }

        .message-status.read {
            background: #6c757d;
            color: white;
            border-color: #6c757d;
        }

        .message-status:hover {
            transform: scale(1.1);
        }

        .notification-header-text {
            font-weight: 600;
            font-size: 0.9rem;
            color: #2c3e50;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .notification-subheader {
            font-size: 0.8rem;
            color: #5a6c7d;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .details-section {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 8px 10px;
            margin: 8px 0;
            font-size: 0.75rem;
        }

        .detail-item {
            display: flex;
            margin-bottom: 4px;
            align-items: flex-start;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 70px;
            flex-shrink: 0;
            font-size: 0.75rem;
        }

        .detail-value {
            color: #6c757d;
            flex: 1;
            font-size: 0.75rem;
            line-height: 1.3;
        }

        .footer-note {
            background: #e9ecef;
            border-radius: 5px;
            padding: 6px 8px;
            margin-top: 8px;
            font-size: 0.7rem;
            color: #495057;
            text-align: center;
            line-height: 1.3;
        }

        .amount-highlight {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.85rem;
        }

        .bazar_request .amount-highlight {
            color: var(--bazar-color);
        }

        .bazar_approved .amount-highlight {
            color: var(--bazar-approved);
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 0.65rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .time-display {
            font-size: 0.7rem;
            color: #6c757d;
            text-align: right;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
        }

        .mark-all-read-btn {
            background: var(--success);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        .mark-all-read-btn:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        /* Toast Notification */
        .custom-toast {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1050;
            background: var(--success);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateX(400px);
            transition: transform 0.3s ease;
            max-width: 300px;
        }

        .custom-toast.show {
            transform: translateX(0);
        }

        .custom-toast.error {
            background: var(--danger);
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        /* Mobile responsive styles */
        @media (max-width: 576px) {
            .notification-item {
                padding: 10px;
                margin-bottom: 6px;
                border-radius: 8px;
            }

            .notification-icon {
                width: 36px;
                height: 36px;
                font-size: 0.9rem;
                margin-right: 10px;
            }

            .message-status {
                width: 28px;
                height: 28px;
                font-size: 0.8rem;
            }

            .notification-header-text {
                font-size: 0.85rem;
            }

            .notification-subheader {
                font-size: 0.75rem;
            }

            .details-section {
                padding: 6px 8px;
                font-size: 0.7rem;
            }

            .detail-label {
                min-width: 60px;
                font-size: 0.7rem;
            }

            .detail-value {
                font-size: 0.7rem;
            }

            .date-group-header {
                padding: 8px 10px;
                font-size: 0.8rem;
                margin: 10px 0 4px 0;
            }

            .amount-highlight {
                font-size: 0.8rem;
            }

            .custom-toast {
                top: 70px;
                right: 10px;
                left: 10px;
                max-width: none;
                transform: translateY(-100px);
            }

            .custom-toast.show {
                transform: translateY(0);
            }
        }

        @media (max-width: 380px) {
            .notification-item {
                padding: 8px;
            }

            .notification-icon {
                width: 32px;
                height: 32px;
                margin-right: 8px;
            }

            .message-status {
                width: 26px;
                height: 26px;
            }

            .detail-label {
                min-width: 50px;
            }
        }
    </style>
</head>

<body>
    <!-- Toast Notification -->
    <div id="customToast" class="custom-toast" style="display: none;">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <span id="toastMessage"></span>
        </div>
    </div>

    <!-- Header -->
    <div class="notification-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="././index.php?section=dashboard" class="btn btn-light btn-sm me-2">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Alerts</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($unread_count > 0): ?>
                        <button class="mark-all-read-btn btn-sm" onclick="markAllAsRead()">
                            <i class="fas fa-check-double me-1"></i>Mark All
                        </button>
                    <?php endif; ?>
                    <span class="badge bg-light text-dark">
                        <?php echo $total_notifications; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="container">
        <?php if (empty($grouped_notifications)): ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h6>No notifications yet</h6>
                <p class="text-muted small">Your notifications will appear here</p>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12">
                    <?php foreach ($grouped_notifications as $date => $notifications): ?>
                        <?php
                        $today = date('Y-m-d');
                        $yesterday = date('Y-m-d', strtotime('-1 day'));
                        $display_date = $date === $today ? 'Today' : ($date === $yesterday ? 'Yesterday' : date('M j, Y', strtotime($date)));
                        $date_class = $date === $today ? 'today' : ($date === $yesterday ? 'yesterday' : '');
                        ?>

                        <div class="date-group-header <?php echo $date_class; ?>">
                            <i class="fas fa-calendar-day me-2"></i><?php echo $display_date; ?>
                            <span class="badge bg-primary ms-2" style="font-size: 0.6rem;">
                                <?php echo count($notifications); ?>
                            </span>
                        </div>

                        <?php foreach ($notifications as $notification): ?>
                            <?php
                            $type = $notification['type'];
                            $isUnread = !$notification['read_status'];
                            $date_display = date('d M Y', strtotime($notification['created_at']));
                            $time_display = date('g:i A', strtotime($notification['created_at']));

                            if ($type === 'transaction') {
                                $isSender = $notification['sender_id'] == $user_id;
                                $isReceiver = $notification['receiver_id'] == $user_id;
                                $icon = 'money-bill-wave';
                                $header = 'Payment ' . ($isSender ? 'Sent' : 'Received');

                                if ($isSender) {
                                    $subheader = "Sent BDT " . number_format($notification['amount'], 2) . " to " .
                                        htmlspecialchars($notification['receiver_name']);
                                } else {
                                    $subheader = "Received BDT " . number_format($notification['amount'], 2) . " from " .
                                        htmlspecialchars($notification['sender_name']);
                                }

                                $footer_note = "Balance updated";

                            } elseif ($type === 'bazar_request') {
                                $status = $notification['status'];
                                $icon = 'shopping-cart';
                                $header = 'Bazar Request ' . ucfirst($status);
                                $subheader = "BDT " . number_format($notification['amount'], 2) . " • " . ucfirst($status);

                                $footer_note = $status === 'approved' ?
                                    "Request approved" :
                                    ($status === 'pending' ? "Pending approval" : "Request rejected");

                            } else { // bazar_approved
                                $icon = 'check-circle';
                                $header = 'Bazar Added';
                                $subheader = "BDT " . number_format($notification['amount'], 2) . " • Approved";
                                $footer_note = "Added to your account";
                            }
                            ?>

                            <div class="notification-item <?php echo $type; ?> <?php echo $isUnread ? 'unread' : 'read'; ?>"
                                id="notification-<?php echo $notification['id']; ?>">

                                <div class="d-flex align-items-start">
                                    <div class="notification-icon">
                                        <i class="fas fa-<?php echo $icon; ?>"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <!-- Header Section -->
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div class="notification-header-text">
                                                <?php echo $header; ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="time-display">
                                                    <?php echo $time_display; ?>
                                                </div>
                                                <div class="message-status <?php echo $isUnread ? 'unread' : 'read'; ?>"
                                                    onclick="markAsRead('<?php echo $notification['id']; ?>', '<?php echo $type; ?>')"
                                                    title="<?php echo $isUnread ? 'Mark as read' : 'Read'; ?>">
                                                    <i class="fas fa-<?php echo $isUnread ? 'envelope' : 'envelope-open'; ?>"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Subheader Message -->
                                        <div class="notification-subheader">
                                            <?php echo $subheader; ?>
                                        </div>

                                        <!-- Details Section -->
                                        <div class="details-section">
                                            <div class="detail-item">
                                                <span class="detail-label">Amount:</span>
                                                <span class="detail-value amount-highlight">
                                                    BDT <?php echo number_format($notification['amount'], 2); ?>
                                                </span>
                                            </div>

                                            <?php if ($type === 'transaction'): ?>
                                                <div class="detail-item">
                                                    <span class="detail-label">Member:</span>
                                                    <span class="detail-value">
                                                        <?php echo $isSender
                                                            ? htmlspecialchars($notification['receiver_name']) . ' (ID: ' . $notification['receiver_member_id'] . ')'
                                                            : htmlspecialchars($notification['sender_name']) . ' (ID: ' . $notification['sender_member_id'] . ')'; ?>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <div class="detail-item">
                                                    <span class="detail-label">Member:</span>
                                                    <span class="detail-value">
                                                        <?php echo htmlspecialchars($notification['member_name']); ?> (ID:
                                                        <?php echo $notification['member_member_id']; ?>)
                                                    </span>
                                                </div>
                                                <?php if ($type === 'bazar_request'): ?>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Status:</span>
                                                        <span class="detail-value">
                                                            <span class="status-badge status-<?php echo $notification['status']; ?>">
                                                                <?php echo ucfirst($notification['status']); ?>
                                                            </span>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <div class="detail-item">
                                                <span class="detail-label">Date:</span>
                                                <span class="detail-value"><?php echo $date_display; ?></span>
                                            </div>

                                            <?php if ($notification['description']): ?>
                                                <div class="detail-item">
                                                    <span class="detail-label">Note:</span>
                                                    <span
                                                        class="detail-value"><?php echo htmlspecialchars($notification['description']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Footer Note -->
                                        <div class="footer-note">
                                            <?php echo $footer_note; ?> • Thank you
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Get the correct base path
        function getBasePath() {
            const currentPath = window.location.pathname;
            if (currentPath.includes('index.php')) {
                return currentPath.substring(0, currentPath.lastIndexOf('/') + 1);
            }
            return '';
        }

        function showToast(message, isSuccess = true) {
            const toast = document.getElementById('customToast');
            const toastMessage = document.getElementById('toastMessage');

            if (!toast || !toastMessage) {
                console.error('Toast elements not found');
                return;
            }

            toastMessage.textContent = message;
            toast.style.background = isSuccess ? '#28a745' : '#dc3545';
            toast.className = isSuccess ? 'custom-toast' : 'custom-toast error';
            toast.style.display = 'block';

            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 300);
            }, 3000);
        }

        function markAsRead(notificationId, notificationType) {
            console.log('Marking as read:', notificationId, notificationType);

            const formData = new FormData();
            formData.append('mark_as_read', 'true');
            formData.append('notification_id', notificationId);
            formData.append('notification_type', notificationType);

            // Use the correct path
            const ajaxUrl = 'user_section/notification_ajax.php';
            console.log('AJAX URL:', ajaxUrl);

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        const notificationElement = document.getElementById('notification-' + notificationId);
                        if (notificationElement) {
                            notificationElement.classList.remove('unread');
                            notificationElement.classList.add('read');

                            const messageStatus = notificationElement.querySelector('.message-status');
                            if (messageStatus) {
                                messageStatus.classList.remove('unread');
                                messageStatus.classList.add('read');
                                messageStatus.innerHTML = '<i class="fas fa-envelope-open"></i>';
                                messageStatus.title = 'Read';
                            }

                            // Update unread count
                            updateUnreadCount();

                            showToast('Notification marked as read!');
                        } else {
                            showToast('Notification updated but element not found', false);
                        }
                    } else {
                        showToast('Error: ' + (data.error || 'Failed to mark as read'), false);
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    showToast('Network error: ' + error.message, false);
                });
        }

        function markAllAsRead() {
            console.log('Marking all as read');

            const formData = new FormData();
            formData.append('mark_all_read', 'true');

            // Use the correct path
            const ajaxUrl = 'user_section/notification_ajax.php';
            console.log('AJAX URL:', ajaxUrl);

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        // Update all notifications visually
                        const unreadNotifications = document.querySelectorAll('.notification-item.unread');
                        console.log('Updating', unreadNotifications.length, 'notifications');

                        unreadNotifications.forEach(notification => {
                            notification.classList.remove('unread');
                            notification.classList.add('read');

                            const messageStatus = notification.querySelector('.message-status');
                            if (messageStatus) {
                                messageStatus.classList.remove('unread');
                                messageStatus.classList.add('read');
                                messageStatus.innerHTML = '<i class="fas fa-envelope-open"></i>';
                                messageStatus.title = 'Read';
                            }
                        });

                        // Remove mark all button
                        const markAllBtn = document.querySelector('.mark-all-read-btn');
                        if (markAllBtn) {
                            markAllBtn.remove();
                        }

                        showToast(data.message || 'All notifications marked as read!');
                    } else {
                        showToast('Error: ' + (data.error || 'Failed to mark all as read'), false);
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    showToast('Network error: ' + error.message, false);
                });
        }

        function updateUnreadCount() {
            const unreadCount = document.querySelectorAll('.notification-item.unread').length;
            console.log('Unread count:', unreadCount);

            if (unreadCount === 0) {
                const markAllBtn = document.querySelector('.mark-all-read-btn');
                if (markAllBtn) {
                    markAllBtn.remove();
                }
            }

            // Update the badge in header if it exists
            const unreadBadge = document.querySelector('.unread-count-badge');
            if (unreadBadge) {
                unreadBadge.textContent = unreadCount;
                if (unreadCount === 0) {
                    unreadBadge.style.display = 'none';
                }
            }
        }

        // Test function to verify AJAX endpoint
        function testAjaxEndpoint() {
            console.log('Testing AJAX endpoint...');
            const testUrl = 'user_section/notification_ajax.php';

            fetch(testUrl)
                .then(response => {
                    console.log('Test response status:', response.status);
                    console.log('Test response OK:', response.ok);
                    return response.text();
                })
                .then(text => {
                    console.log('Test response text:', text.substring(0, 100));
                })
                .catch(error => {
                    console.error('Test failed:', error);
                });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Notification page loaded');
            testAjaxEndpoint();
            updateUnreadCount();
        });
    </script>
</body>

</html>