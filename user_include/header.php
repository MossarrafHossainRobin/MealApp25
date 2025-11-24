<?php
// user_include/header.php
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
            --primary: #8e44ad;
            --primary-dark: #7d3c98;
            --secondary: #3498db;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #2c3e50;
            --gradient: linear-gradient(135deg, #8e44ad, #3498db);
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --card-radius: 16px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            padding-bottom: 80px;
        }

        .app-container {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Header Styles */
        .main-header {
            background: var(--gradient);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            padding: 12px 0;
        }

        .logo {
            font-weight: 700;
            font-size: 1.4rem;
            color: white;
        }

        .logo i {
            margin-right: 8px;
        }

        .user-info {
            color: white;
            font-weight: 500;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        /* Mobile Bottom Nav */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 8px 0;
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .mobile-bottom-nav.hidden {
            transform: translateY(100%);
        }

        .nav-item {
            text-align: center;
            text-decoration: none;
            color: #6c757d;
            transition: var(--transition);
            padding: 5px;
            border-radius: 12px;
        }

        .nav-item.active {
            color: var(--primary);
        }

        .nav-icon {
            font-size: 1.3rem;
            margin-bottom: 3px;
        }

        .nav-label {
            font-size: 0.7rem;
            font-weight: 500;
        }

        /* Desktop Navigation */
        .desktop-nav {
            background: white;
            box-shadow: var(--shadow);
            padding: 12px 0;
        }

        .desktop-nav-link {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            padding: 8px 20px;
            border-radius: 25px;
            transition: var(--transition);
            margin: 0 5px;
        }

        .desktop-nav-link.active,
        .desktop-nav-link:hover {
            background: var(--gradient);
            color: white;
        }

        /* Main Content */
        .main-content {
            padding: 20px 0;
        }

        /* Card Styles */
        .app-card {
            background: white;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
            border: none;
            transition: var(--transition);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .app-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .card-body {
            padding: 20px;
        }

        /* Welcome Section */
        .welcome-section {
            background: var(--gradient);
            color: white;
            border-radius: var(--card-radius);
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
        }

        /* Quick Stats */
        .stat-card {
            background: white;
            border-radius: var(--card-radius);
            padding: 20px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            margin-bottom: 15px;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-completed {
            background: #d1edff;
            color: var(--secondary);
        }

        .badge-paid {
            background: #d4edda;
            color: #155724;
        }

        .badge-active {
            background: #d4edda;
            color: #155724;
        }

        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        /* Buttons */
        .btn-app {
            background: var(--gradient);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            color: white;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-app:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        /* Table Styles */
        .app-table {
            width: 100%;
            border-collapse: collapse;
        }

        .app-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: var(--dark);
            padding: 12px 15px;
            border-bottom: 2px solid #dee2e6;
        }

        .app-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 15px;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Mobile Optimizations */
        @media (max-width: 768px) {
            body {
                padding-bottom: 70px;
            }

            .main-content {
                padding: 15px 0;
            }

            .welcome-section {
                padding: 20px;
                margin-bottom: 20px;
            }

            .card-header,
            .card-body {
                padding: 15px;
            }

            .stat-card {
                padding: 15px;
            }
        }

        /* Hide scrollbar but keep functionality */
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Header Section -->
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <header class="main-header">
                <div class="container">
                    <div class="header-content d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <i class="fas fa-utensils"></i>
                            MealApp25
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="user-name d-none d-md-block">Welcome,
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <a href="index.php?logout=true" class="logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="d-none d-sm-inline">Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
        <?php else: ?>
            <header class="main-header">
                <div class="container">
                    <div class="header-content">
                        <div class="logo">
                            <i class="fas fa-utensils"></i>
                            MealApp25
                        </div>
                    </div>
                </div>
            </header>
        <?php endif; ?>