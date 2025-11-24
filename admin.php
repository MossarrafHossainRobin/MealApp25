<?php
// admin.php - Complete Admin Panel
require_once 'config/auth.php';
require_once 'config/database.php';

$auth = new Auth();

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($auth->login($email, $password)) {
        header("Location: admin.php");
        exit;
    } else {
        $login_error = 'Invalid credentials!';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Check if user is logged in
$is_logged_in = $auth->isLoggedIn();

// If not logged in, show login page
if (!$is_logged_in) {
    showLoginPage($login_error ?? '');
    exit;
}

// User is logged in - Show Admin Panel
$auth->checkSession();
$current_section = $_GET['section'] ?? 'home';
showAdminPanel($current_section, $_SESSION['admin_email'], $_SESSION['admin_name']);
exit;

// ==================== FUNCTIONS ====================

function showLoginPage($error = '')
{
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Meal App</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                height: 100vh;
                display: flex;
                align-items: center;
            }

            .login-card {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            }

            .login-header {
                background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
                color: white;
                border-radius: 15px 15px 0 0;
                padding: 2rem;
                text-align: center;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="login-card">
                        <div class="login-header">
                            <i class="fas fa-utensils fa-3x mb-3"></i>
                            <h2 class="fw-bold">Admin Login</h2>
                            <p class="mb-0">Meal Management System</p>
                        </div>

                        <div class="p-5">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form method="POST">
                                <input type="hidden" name="login" value="1">
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" name="email" placeholder="Enter your email"
                                            required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" name="password"
                                            placeholder="Enter your password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Admin Panel
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
    <?php
}

function showAdminPanel($current_section, $admin_email, $admin_name)
{
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Panel - Meal App</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

            .stat-card.secondary {
                border-left-color: var(--secondary-color);
            }

            .stat-card.danger {
                border-left-color: var(--danger-color);
            }

            .stat-card.dark {
                border-left-color: #5a5c69;
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
                    <a class="nav-link <?php echo $current_section === 'home' ? 'active' : ''; ?>"
                        href="admin.php?section=home">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                    <a class="nav-link <?php echo $current_section === 'members' ? 'active' : ''; ?>"
                        href="admin.php?section=members">
                        <i class="fas fa-users"></i>Members
                    </a>
                    <a class="nav-link <?php echo $current_section === 'bazar' ? 'active' : ''; ?>"
                        href="admin.php?section=bazar">
                        <i class="fas fa-shopping-cart"></i>Bazar
                    </a>
                    <a class="nav-link <?php echo $current_section === 'bazar_requests' ? 'active' : ''; ?>"
                        href="admin.php?section=bazar_requests">
                        <i class="fas fa-clock"></i>Bazar Requests
                    </a>
                    <a class="nav-link <?php echo $current_section === 'meal' ? 'active' : ''; ?>"
                        href="admin.php?section=meal">
                        <i class="fas fa-utensils"></i>Meal Count
                    </a>
                    <a class="nav-link <?php echo $current_section === 'water' ? 'active' : ''; ?>"
                        href="admin.php?section=water">
                        <i class="fas fa-tint"></i>Water Duty
                    </a>
                    <a class="nav-link <?php echo $current_section === 'settlement' ? 'active' : ''; ?>"
                        href="admin.php?section=settlement">
                        <i class="fas fa-calculator"></i>Settlement
                    </a>
                    <a class="nav-link <?php echo $current_section === 'flat' ? 'active' : ''; ?>"
                        href="admin.php?section=flat">
                        <i class="fas fa-home"></i>Flat Rent & Costs
                    </a>

                    <div class="mt-4 pt-3 border-top">
                        <a class="nav-link text-warning" href="admin.php?logout=1">
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
                                <i class="fas fa-user me-1"></i><?php echo $admin_name; ?> (<?php echo $admin_email; ?>)
                            </span>
                            <a href="admin.php?logout=1" class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Are you sure you want to logout?')">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid py-4">
                    <?php
                    // Load the appropriate section
                    switch ($current_section) {
                        case 'home':
                            include 'admin_section/home.php';
                            break;
                        case 'members':
                            include 'admin_section/members.php';
                            break;
                        case 'bazar':
                            include 'admin_section/bazar.php';
                            break;
                        case 'bazar_requests':
                            include 'admin_section/bazar_requests.php';
                            break;
                        case 'meal':
                            include 'admin_section/meal.php';
                            break;
                        case 'water':
                            include 'admin_section/water.php';
                            break;
                        case 'settlement':
                            include 'admin_section/settlement.php';
                            break;
                        case 'flat':
                            include 'admin_section/flat.php';
                            break;
                        default:
                            include 'admin_section/home.php';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Mobile Bottom Navigation -->
        <nav class="mobile-bottom-nav">
            <a href="admin.php?section=home"
                class="flex-fill text-center py-3 text-decoration-none <?php echo $current_section === 'home' ? 'text-primary' : 'text-muted'; ?>">
                <div class="nav-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="nav-text">Dashboard</div>
            </a>
            <a href="admin.php?section=members"
                class="flex-fill text-center py-3 text-decoration-none <?php echo $current_section === 'members' ? 'text-primary' : 'text-muted'; ?>">
                <div class="nav-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="nav-text">Members</div>
            </a>
            <a href="admin.php?section=bazar"
                class="flex-fill text-center py-3 text-decoration-none <?php echo $current_section === 'bazar' ? 'text-primary' : 'text-muted'; ?>">
                <div class="nav-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="nav-text">Bazar</div>
            </a>
            <a href="admin.php?section=meal"
                class="flex-fill text-center py-3 text-decoration-none <?php echo $current_section === 'meal' ? 'text-primary' : 'text-muted'; ?>">
                <div class="nav-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="nav-text">Meals</div>
            </a>
            <div class="dropdown flex-fill text-center">
                <a href="#"
                    class="py-3 text-decoration-none <?php echo in_array($current_section, ['water', 'settlement', 'flat']) ? 'text-primary' : 'text-muted'; ?> dropdown-toggle"
                    data-bs-toggle="dropdown">
                    <div class="nav-icon">
                        <i class="fas fa-ellipsis-h"></i>
                    </div>
                    <div class="nav-text">More</div>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="admin.php?section=water"><i class="fas fa-tint me-2"></i>Water Duty</a>
                    <a class="dropdown-item" href="admin.php?section=settlement"><i
                            class="fas fa-calculator me-2"></i>Settlement</a>
                    <a class="dropdown-item" href="admin.php?section=flat"><i class="fas fa-home me-2"></i>Flat Rent</a>
                    <a class="dropdown-item" href="admin.php?section=bazar_requests"><i class="fas fa-clock me-2"></i>Bazar
                        Requests</a>
                </div>
            </div>
        </nav>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Toast notification function
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
                toast.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 4000);
            }

            // AJAX helper function
            async function makeRequest(action, data = {}) {
                try {
                    const formData = new FormData();
                    formData.append('ajax_action', action);
                    for (const key in data) {
                        formData.append(key, data[key]);
                    }

                    const response = await fetch('admin_process/api.php', {
                        method: 'POST',
                        body: formData
                    });

                    return await response.json();
                } catch (error) {
                    console.error('Request failed:', error);
                    return { status: 'error', message: 'Network error occurred' };
                }
            }
        </script>
    </body>

    </html>
    <?php
}
?>