<?php
require_once '../config/auth.php';
$auth = new Auth();
$auth->checkSession();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Meal App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Inter', sans-serif;
        }

        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .nav-icon {
            font-size: 1.2rem;
        }

        .nav-text {
            font-size: 0.75rem;
            margin-top: 2px;
        }

        @media (max-width: 768px) {
            .mobile-bottom-nav {
                display: flex;
            }

            .desktop-sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0 !important;
                padding-bottom: 80px;
            }
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 2px 0;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.35rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .stat-card {
            border-left: 4px solid;
        }

        .stat-card.primary {
            border-left-color: var(--primary-color);
        }

        .stat-card.success {
            border-left-color: var(--success-color);
        }

        .stat-card.info {
            border-left-color: var(--info-color);
        }

        .stat-card.warning {
            border-left-color: var(--warning-color);
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Desktop Sidebar -->
        <div class="desktop-sidebar sidebar">
            <div class="p-4 text-center border-bottom">
                <h4 class="mb-0"><i class="fas fa-utensils me-2"></i>MealApp</h4>
                <small class="text-white-50">Admin Panel</small>
            </div>
            <nav class="nav flex-column p-3">
                <a class="nav-link <?php echo $current_section === 'home' ? 'active' : ''; ?>" href="?section=home">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
                <a class="nav-link <?php echo $current_section === 'members' ? 'active' : ''; ?>"
                    href="?section=members">
                    <i class="fas fa-users"></i>Members
                </a>
                <a class="nav-link <?php echo $current_section === 'bazar' ? 'active' : ''; ?>" href="?section=bazar">
                    <i class="fas fa-shopping-cart"></i>Bazar
                </a>
                <a class="nav-link <?php echo $current_section === 'meal' ? 'active' : ''; ?>" href="?section=meal">
                    <i class="fas fa-utensils"></i>Meal Count
                </a>
                <a class="nav-link <?php echo $current_section === 'water' ? 'active' : ''; ?>" href="?section=water">
                    <i class="fas fa-tint"></i>Water Duty
                </a>
                <a class="nav-link <?php echo $current_section === 'settlement' ? 'active' : ''; ?>"
                    href="?section=settlement">
                    <i class="fas fa-calculator"></i>Settlement
                </a>
                <div class="mt-4 pt-3 border-top">
                    <a class="nav-link text-warning" href="?logout=1">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1" style="margin-left: 250px;">
            <!-- Top Navigation -->
            <nav class="navbar navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h6">
                        <i class="fas fa-user-shield me-2"></i>Admin Dashboard
                    </span>
                    <div class="d-flex align-items-center">
                        <span class="me-3 text-muted small d-none d-md-block">
                            <i class="fas fa-envelope me-1"></i>meal.query@gmail.com
                        </span>
                        <a href="?logout=1" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </div>
                </div>
            </nav>