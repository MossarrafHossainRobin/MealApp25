<?php
// index.php - Main Dashboard

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Paths are relative to this file (index.php)
require_once 'user_process/user_actions.php';

// Instantiate the action controller
$action = new UserActions();
$message = '';

// ----------------------
// 1. Handle Logout
// ----------------------
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

// ----------------------
// 2. Handle Login POST
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // Check if the user is attempting to log in as a regular user
    if ($action->loginUser($email)) {
        // Store user email in session for API use
        $_SESSION['user_email'] = $email;
        header("Location: index.php");
        exit;
    } else {
        $message = '<div class="alert alert-danger">Login failed. User not found or inactive.</div>';
    }
}

// ----------------------
// 3. Check if section parameter is set
// ----------------------
$current_section = 'dashboard';
$valid_sections = ['dashboard', 'bazar', 'water', 'meal', 'cost'];

if (isset($_GET['section']) && in_array($_GET['section'], $valid_sections)) {
    $current_section = $_GET['section'];
}

// ----------------------
// 4. Include Header
// ----------------------
require_once 'user_include/header.php';

// ----------------------
// 5. Display Content Based on Section
// ----------------------
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    // Include Navigation
    require_once 'user_include/navigation.php';

    // Display appropriate section
    switch ($current_section) {
        case 'bazar':
            require_once 'user_section/bazar.php';
            break;
        case 'water':
            require_once 'user_section/water.php';
            break;
        case 'meal':
            require_once 'user_section/meal.php';
            break;
        case 'cost':
            require_once 'user_section/cost.php';
            break;
        case 'dashboard':
        default:
            require_once 'user_section/dashboard.php';
            break;
    }
} else {
    // Login Form View - Modern Design
    ?>
    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: rotate(45deg) translateY(0px);
            }

            50% {
                transform: rotate(45deg) translateY(-20px);
            }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 2;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #8e44ad, #3498db);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(142, 68, 173, 0.3);
        }

        .login-logo i {
            font-size: 2rem;
            color: white;
        }

        .login-title {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #8e44ad, #3498db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #6c757d;
            font-size: 1rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e9ecef;
            border-radius: 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .form-input:focus {
            outline: none;
            border-color: #8e44ad;
            box-shadow: 0 5px 20px rgba(142, 68, 173, 0.2);
            transform: translateY(-2px);
        }

        .form-input::placeholder {
            color: #adb5bd;
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #8e44ad, #3498db);
            border: none;
            border-radius: 16px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(142, 68, 173, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(142, 68, 173, 0.4);
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-features {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e9ecef;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .feature-item i {
            color: #8e44ad;
            margin-right: 12px;
            font-size: 1rem;
            width: 20px;
        }

        .alert {
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 25px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .alert-danger {
            background: linear-gradient(135deg, #ffeaea, #ffcccc);
            color: #d63031;
            border-left: 4px solid #d63031;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: floatShape 15s infinite linear;
        }

        .shape-1 {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: -10s;
        }

        @keyframes floatShape {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-30px) rotate(120deg);
            }

            66% {
                transform: translateY(15px) rotate(240deg);
            }
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 25px;
                margin: 20px;
            }

            .login-title {
                font-size: 1.8rem;
            }

            .login-logo {
                width: 60px;
                height: 60px;
            }

            .login-logo i {
                font-size: 1.5rem;
            }

            .form-input {
                padding: 14px 16px;
            }

            .login-btn {
                padding: 14px;
            }
        }

        /* Loading animation */
        .btn-loading {
            position: relative;
            color: transparent;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-right-color: transparent;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* New Style for Admin Link */
        .admin-link-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }

        .admin-link-section a {
            color: #8e44ad;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .admin-link-section a:hover {
            color: #3498db;
            text-decoration: underline;
        }
    </style>

    <div class="login-container">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>

        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-utensils"></i>
                </div>
                <h1 class="login-title">MealApp25</h1>
                <p class="login-subtitle">Welcome back! Please sign in to continue</p>
            </div>

            <?php echo $message; ?>

            <form action="index.php" method="POST" id="loginForm">
                <div class="form-group">
                    <input type="email" id="email" name="email" class="form-input" required
                        placeholder="Enter your registered email" autocomplete="email">
                </div>

                <button type="submit" class="login-btn" id="loginButton">
                    <span id="buttonText">Sign In to Dashboard</span>
                </button>
            </form>

            <div class="admin-link-section">
                <a href="admin.php">
                    <i class="fas fa-user-shield me-1"></i> Admin Login
                </a>
            </div>
            <div class="login-features">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Track meals and expenses in real-time</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Manage water duties efficiently</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Monitor cost allocations and payments</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            const buttonText = document.getElementById('buttonText');
            const emailInput = document.getElementById('email');

            // Auto-focus email field
            emailInput.focus();

            // Add loading state on form submission
            loginForm.addEventListener('submit', function () {
                if (emailInput.value.trim()) {
                    loginButton.classList.add('btn-loading');
                    buttonText.style.opacity = '0';

                    // Remove loading state after 2 seconds (fallback)
                    setTimeout(() => {
                        loginButton.classList.remove('btn-loading');
                        buttonText.style.opacity = '1';
                    }, 2000);
                }
            });

            // Add input focus effects
            emailInput.addEventListener('focus', function () {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            emailInput.addEventListener('blur', function () {
                this.parentElement.style.transform = 'scale(1)';
            });

            // Add floating label effect
            emailInput.addEventListener('input', function () {
                if (this.value) {
                    this.style.background = '#f8f9ff';
                } else {
                    this.style.background = 'white';
                }
            });
        });
    </script>
    <?php
}

// ----------------------
// 6. Include Footer
// ----------------------
require_once 'user_include/footer.php';
?>