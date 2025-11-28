<?php
// user_include/navigation.php

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

// Determine current section for active state
$current_section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

// Check for unread notifications from all sources
$unread_count = 0;
try {
    $db_instance = new Database();
    $conn = $db_instance->getConnection();

    // Count unread transaction notifications
    $transaction_stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM transaction_logs tl
        INNER JOIN transactions t ON tl.transaction_id = t.id
        WHERE (t.sender_id = ? OR t.receiver_id = ?) 
        AND tl.read_status = FALSE
        AND tl.action = 'completed'
    ");
    $transaction_stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
    $transaction_count = $transaction_stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Count unread bazar request notifications
    $bazar_request_stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM bazar_requests 
        WHERE member_id = ? 
        AND read_status = FALSE
        AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $bazar_request_stmt->execute([$_SESSION['user_id']]);
    $bazar_request_count = $bazar_request_stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Count unread bazar approved notifications
    $bazar_approved_stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM bazar 
        WHERE member_id = ? 
        AND notification_read = FALSE
        AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $bazar_approved_stmt->execute([$_SESSION['user_id']]);
    $bazar_approved_count = $bazar_approved_stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    // Count unread water duty notifications

    $water_duty_stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM water_duties 
    WHERE member_id = ? 
    AND notification_read = FALSE
    AND assigned_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
    $water_duty_stmt->execute([$_SESSION['user_id']]);
    $water_duty_count = $water_duty_stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $unread_count = $transaction_count + $bazar_request_count + $bazar_approved_count + $water_duty_count;

    $unread_count = $transaction_count + $bazar_request_count + $bazar_approved_count + $water_duty_count;

} catch (PDOException $e) {
    error_log("Error checking unread notifications: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MealApp25 - User Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #d63384;
            --primary-dark: #c2185b;
            --primary-light: #f8bbd9;
            --gradient: linear-gradient(135deg, #d63384, #e91e63);
            --shadow: 0 2px 10px rgba(214, 51, 132, 0.2);
            --transition: all 0.2s ease;
        }

        body {
            background: #fafafa;
            font-family: 'Segoe UI', system-ui, -apple-system, 'Roboto', sans-serif;
            min-height: 100vh;
            padding-bottom: 60px;
        }

        /* Compact Navigation Bar */
        .main-navbar {
            background: var(--gradient);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0.8rem 0;
        }

        .nav-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-brand:hover {
            color: white;
            text-decoration: none;
            opacity: 0.9;
        }

        .nav-brand-icon {
            background: rgba(255, 255, 255, 0.15);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        /* Desktop Navigation */
        .desktop-nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .nav-link-compact {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 10px 20px;
            border-radius: 20px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 110px;
            justify-content: center;
            position: relative;
        }

        .nav-link-compact.active {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .nav-link-compact:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        /* Desktop Notification Badge - GREEN Color */
        .notification-badge {
            position: absolute;
            top: 6px;
            right: 10px;
            /* Right side of icon */
            background: #00ff00;
            /* GREEN color */
            border-radius: 50%;
            width: 14px;
            height: 14px;
            border: 2px solid var(--primary-dark);
            animation: pulse 2s infinite;
            box-shadow: 0 0 8px rgba(0, 255, 0, 0.5);
            /* GREEN glow */
        }

        .notification-count {
            position: absolute;
            top: 4px;
            right: 8px;
            /* Right side of icon */
            background: #00ff00;
            /* GREEN color */
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.65rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--primary-dark);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* User Section */
        .user-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-info {
            color: white;
            text-align: right;
        }

        .user-welcome {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            transform: translateY(-1px);
        }

        /* Compact Mobile Bottom Navigation */
        .mobile-bottom-nav {
            background: white;
            box-shadow: 0 -2px 12px rgba(0, 0, 0, 0.08);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 8px 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .mobile-nav-item {
            color: #666;
            text-decoration: none;
            text-align: center;
            padding: 4px 2px;
            border-radius: 10px;
            transition: var(--transition);
            flex: 1;
            position: relative;
        }

        .mobile-nav-item.active {
            color: var(--primary);
        }

        .mobile-nav-item.active::before {
            content: '';
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 24px;
            height: 3px;
            background: var(--primary);
            border-radius: 2px;
        }

        .mobile-nav-icon {
            font-size: 1.2rem;
            margin-bottom: 3px;
            transition: var(--transition);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-nav-item.active .mobile-nav-icon {
            transform: scale(1.1);
        }

        .mobile-nav-label {
            font-size: 0.7rem;
            font-weight: 500;
            line-height: 1.1;
        }

        /* Mobile Notification Badge - RED Color */
        .mobile-notification-badge {
            position: absolute;
            top: -2px;
            left: 2px;
            /* LEFT side of icon */
            background: #ff4444;
            /* RED color */
            border-radius: 50%;
            width: 12px;
            height: 12px;
            border: 2px solid white;
            animation: pulse 2s infinite;
            box-shadow: 0 0 6px rgba(255, 68, 68, 0.5);
            /* RED glow */
        }

        .mobile-notification-count {
            position: absolute;
            top: -4px;
            left: 0;
            /* LEFT side of icon */
            background: #ff0095ff;
            /* RED color */
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 0.6rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            animation: pulse 2s infinite;
            box-shadow: 0 0 6px rgba(243, 47, 47, 0.5);
            /* RED glow */
        }

        /* For larger numbers on mobile - show dot only for 10+ */
        .mobile-notification-dot {
            position: absolute;
            top: -2px;
            right: 2px;
            background: #00ff00;
            border-radius: 50%;
            width: 10px;
            height: 10px;
            border: 2px solid white;
            animation: pulse 2s infinite;
            box-shadow: 0 0 6px rgba(0, 255, 0, 0.5);
        }

        /* Main Content */
        .main-content {
            padding: 1.5rem 0;
            min-height: calc(100vh - 80px);
        }

        /* Responsive Design */
        @media (max-width: 1199px) {
            .nav-link-compact {
                min-width: 100px;
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 991px) {
            .nav-link-compact {
                min-width: 90px;
                padding: 8px 12px;
                font-size: 0.85rem;
            }

            .nav-link-compact i {
                margin-right: 4px;
            }

            .nav-link-compact span {
                display: none;
            }

            .notification-badge {
                top: 4px;
                right: 6px;
                width: 12px;
                height: 12px;
            }

            .notification-count {
                top: 2px;
                right: 4px;
                width: 16px;
                height: 16px;
                font-size: 0.6rem;
            }
        }

        @media (max-width: 768px) {
            .main-navbar {
                padding: 0.6rem 0;
            }

            .nav-brand {
                font-size: 1.2rem;
            }

            .nav-brand-icon {
                width: 32px;
                height: 32px;
            }

            .user-name,
            .user-welcome {
                display: none;
            }

            .logout-btn span {
                display: none;
            }

            .mobile-bottom-nav {
                padding: 7px 0;
            }

            .mobile-notification-count {
                width: 16px;
                height: 16px;
                font-size: 0.6rem;
                top: -4px;
                right: 0;
            }

            .mobile-notification-dot {
                width: 10px;
                height: 10px;
                top: -2px;
                right: 2px;
            }
        }

        @media (max-width: 576px) {
            .nav-brand {
                font-size: 1.1rem;
            }

            .nav-brand-icon {
                width: 30px;
                height: 30px;
            }

            .mobile-bottom-nav {
                padding: 6px 0;
            }

            .mobile-nav-icon {
                font-size: 1.1rem;
            }

            .mobile-nav-label {
                font-size: 0.65rem;
            }

            .mobile-notification-count {
                width: 14px;
                height: 14px;
                font-size: 0.55rem;
                top: -3px;
                right: 0;
            }

            .mobile-notification-dot {
                width: 8px;
                height: 8px;
                top: -1px;
                right: 1px;
            }

            body {
                padding-bottom: 60px;
            }
        }

        @media (max-width: 380px) {
            .mobile-nav-label {
                font-size: 0.6rem;
            }

            .mobile-nav-icon {
                font-size: 1rem;
            }

            .mobile-bottom-nav {
                padding: 5px 0;
            }

            .mobile-notification-count {
                width: 12px;
                height: 12px;
                font-size: 0.5rem;
                top: -3px;
                right: -1px;
            }

            .mobile-notification-dot {
                width: 6px;
                height: 6px;
                top: 0;
                right: 1px;
            }
        }

        .water_duty .notification-icon {
            background: #17a2b8;
            /* Blue color for water duties */
        }

        .water_duty .amount-highlight {
            color: #17a2b8;
        }
    </style>
</head>

<body>
    <!-- Compact Navigation Bar -->
    <nav class="main-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Brand Logo -->
                <a href="dashboard.php" class="nav-brand">
                    <div class="nav-brand-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    MealApp25
                </a>

                <!-- Desktop Navigation -->
                <div class="desktop-nav-links d-none d-lg-flex">
                    <a href="index.php?section=dashboard"
                        class="nav-link-compact <?php echo $current_section === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="index.php?section=bazar"
                        class="nav-link-compact <?php echo $current_section === 'bazar' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Bazar</span>
                    </a>
                    <a href="index.php?section=water"
                        class="nav-link-compact <?php echo $current_section === 'water' ? 'active' : ''; ?>">
                        <i class="fas fa-tint"></i>
                        <span>Water</span>
                    </a>
                    <a href="index.php?section=meal"
                        class="nav-link-compact <?php echo $current_section === 'meal' ? 'active' : ''; ?>">
                        <i class="fas fa-utensils"></i>
                        <span>Meals</span>
                    </a>
                    <a href="index.php?section=cost"
                        class="nav-link-compact <?php echo $current_section === 'cost' ? 'active' : ''; ?>">
                        <i class="fas fa-money-bill"></i>
                        <span>Costs</span>
                    </a>
                    <a href="index.php?section=notification"
                        class="nav-link-compact <?php echo $current_section === 'notification' ? 'active' : ''; ?>">
                        <i class="fas fa-bell"></i>
                        <span>Alerts</span>
                        <?php if ($unread_count > 0): ?>
                            <?php if ($unread_count > 9): ?>
                                <span class="notification-badge" title="<?php echo $unread_count; ?> new notifications"></span>
                            <?php else: ?>
                                <span class="notification-count" title="<?php echo $unread_count; ?> new notifications">
                                    <?php echo $unread_count; ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- User Section -->
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <div class="user-section">
                        <div class="user-info d-none d-md-block">
                            <div class="user-welcome">Welcome</div>
                            <div class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                        </div>
                        <a href="index.php?logout=true" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Compact Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center">
                <div class="col">
                    <a href="index.php?section=dashboard"
                        class="mobile-nav-item <?php echo $current_section === 'dashboard' ? 'active' : ''; ?>">
                        <div class="mobile-nav-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="mobile-nav-label">Home</div>
                    </a>
                </div>
                <div class="col">
                    <a href="index.php?section=bazar"
                        class="mobile-nav-item <?php echo $current_section === 'bazar' ? 'active' : ''; ?>">
                        <div class="mobile-nav-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="mobile-nav-label">Bazar</div>
                    </a>
                </div>
                <div class="col">
                    <a href="index.php?section=water"
                        class="mobile-nav-item <?php echo $current_section === 'water' ? 'active' : ''; ?>">
                        <div class="mobile-nav-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div class="mobile-nav-label">Water</div>
                    </a>
                </div>
                <div class="col">
                    <a href="index.php?section=meal"
                        class="mobile-nav-item <?php echo $current_section === 'meal' ? 'active' : ''; ?>">
                        <div class="mobile-nav-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="mobile-nav-label">Meals</div>
                    </a>
                </div>
                <div class="col">
                    <a href="index.php?section=cost"
                        class="mobile-nav-item <?php echo $current_section === 'cost' ? 'active' : ''; ?>">
                        <div class="mobile-nav-icon">
                            <i class="fas fa-money-bill"></i>
                        </div>
                        <div class="mobile-nav-label">Costs</div>
                    </a>
                </div>
                <div class="col">
                    <a href="index.php?section=notification"
                        class="mobile-nav-item <?php echo $current_section === 'notification' ? 'active' : ''; ?>">
                        <div class="mobile-nav-icon">
                            <i class="fas fa-bell"></i>
                            <?php if ($unread_count > 0): ?>
                                <?php if ($unread_count > 9): ?>
                                    <span class="mobile-notification-dot"
                                        title="<?php echo $unread_count; ?> new notifications"></span>
                                <?php else: ?>
                                    <span class="mobile-notification-count"
                                        title="<?php echo $unread_count; ?> new notifications">
                                        <?php echo $unread_count; ?>
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="mobile-nav-label">Alerts</div>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="container main-content">
        <div class="row">
            <div class="col-12">