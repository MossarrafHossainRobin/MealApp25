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
// 4. Display Content Based on Section
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
        case 'notification':
            require_once 'user_section/notification.php';
            break;
        case 'dashboard':
        default:
            require_once 'user_section/dashboard.php';
            break;
    }

    // Include Footer only for logged-in users
    require_once 'user_include/footer.php';
} else {
    // Login Form View - Firebase Inspired Modern Design with White Background
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MealApp25 - Login</title>

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
                --shadow: 0 10px 40px rgba(214, 51, 132, 0.15);
                --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                background: #ffffff;
                font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
                min-height: 100vh;
                overflow-x: hidden;
            }

            .firebase-login-container {
                min-height: 100vh;
                background: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                position: relative;
                overflow: hidden;
            }

            /* Minimal Background Design */
            .firebase-login-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background:
                    radial-gradient(circle at 10% 20%, rgba(214, 51, 132, 0.03) 0%, transparent 50%),
                    radial-gradient(circle at 90% 80%, rgba(233, 30, 99, 0.03) 0%, transparent 50%);
            }

            /* Main Login Card - Clean White Design */
            .firebase-login-card {
                background: #ffffff;
                border-radius: 24px;
                padding: 48px 40px;
                box-shadow:
                    0 20px 60px rgba(0, 0, 0, 0.08),
                    0 0 0 1px rgba(0, 0, 0, 0.02);
                width: 100%;
                max-width: 440px;
                position: relative;
                z-index: 10;
                border: 1px solid rgba(0, 0, 0, 0.05);
                transform: translateY(0);
                transition: var(--transition);
            }

            .firebase-login-card:hover {
                transform: translateY(-5px);
                box-shadow:
                    0 25px 80px rgba(0, 0, 0, 0.12),
                    0 0 0 1px rgba(0, 0, 0, 0.03);
            }

            /* Header Section */
            .firebase-header {
                text-align: center;
                margin-bottom: 48px;
            }

            .firebase-logo {
                width: 80px;
                height: 80px;
                background: var(--gradient);
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 24px;
                box-shadow:
                    0 12px 30px rgba(214, 51, 132, 0.3),
                    inset 0 1px 1px rgba(255, 255, 255, 0.2);
                position: relative;
                overflow: hidden;
            }

            .firebase-logo::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transform: rotate(45deg);
                animation: shine 3s ease-in-out infinite;
            }

            @keyframes shine {
                0% {
                    transform: rotate(45deg) translateX(-100%);
                }

                100% {
                    transform: rotate(45deg) translateX(100%);
                }
            }

            .firebase-logo i {
                font-size: 32px;
                color: white;
                filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            }

            .firebase-title {
                font-size: 32px;
                font-weight: 700;
                background: linear-gradient(135deg, #d63384, #e91e63);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin-bottom: 8px;
                letter-spacing: -0.5px;
            }

            .firebase-subtitle {
                color: #6c757d;
                font-size: 16px;
                font-weight: 500;
                line-height: 1.5;
            }

            /* Form Elements */
            .firebase-form-group {
                margin-bottom: 24px;
                position: relative;
            }

            .firebase-input {
                width: 100%;
                padding: 18px 20px;
                border: 2px solid #e9ecef;
                border-radius: 16px;
                font-size: 16px;
                font-weight: 500;
                transition: var(--transition);
                background: #ffffff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
                color: #2d3748;
            }

            .firebase-input:focus {
                outline: none;
                border-color: var(--primary);
                background: #ffffff;
                box-shadow:
                    0 8px 25px rgba(214, 51, 132, 0.15),
                    0 0 0 4px rgba(214, 51, 132, 0.1);
                transform: translateY(-2px);
            }

            .firebase-input::placeholder {
                color: #a0aec0;
                font-weight: 400;
            }

            /* Firebase Style Button */
            .firebase-btn {
                width: 100%;
                padding: 18px 24px;
                background: var(--gradient);
                border: none;
                border-radius: 16px;
                color: white;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: var(--transition);
                box-shadow:
                    0 8px 25px rgba(214, 51, 132, 0.4),
                    0 2px 4px rgba(0, 0, 0, 0.1);
                position: relative;
                overflow: hidden;
                letter-spacing: 0.5px;
            }

            .firebase-btn:hover {
                transform: translateY(-3px);
                box-shadow:
                    0 15px 35px rgba(214, 51, 132, 0.5),
                    0 4px 8px rgba(0, 0, 0, 0.15);
            }

            .firebase-btn:active {
                transform: translateY(-1px);
            }

            .firebase-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                transition: left 0.6s ease;
            }

            .firebase-btn:hover::before {
                left: 100%;
            }

            /* Features Section */
            .firebase-features {
                margin-top: 32px;
                padding-top: 24px;
                border-top: 1px solid rgba(0, 0, 0, 0.08);
            }

            .firebase-feature-item {
                display: flex;
                align-items: center;
                margin-bottom: 16px;
                color: #6c757d;
                font-size: 14px;
                font-weight: 500;
            }

            .firebase-feature-item i {
                color: var(--primary);
                margin-right: 12px;
                font-size: 16px;
                width: 20px;
                text-align: center;
            }

            /* Admin Link */
            .firebase-admin-section {
                text-align: center;
                margin-top: 24px;
                padding-top: 20px;
                border-top: 1px solid rgba(0, 0, 0, 0.08);
            }

            .firebase-admin-link {
                color: var(--primary);
                font-weight: 600;
                text-decoration: none;
                font-size: 14px;
                transition: var(--transition);
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                border-radius: 12px;
                background: rgba(214, 51, 132, 0.1);
            }

            .firebase-admin-link:hover {
                color: white;
                background: var(--gradient);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(214, 51, 132, 0.3);
                text-decoration: none;
            }

            /* Alert Messages */
            .firebase-alert {
                border-radius: 12px;
                padding: 16px 20px;
                margin-bottom: 24px;
                border: none;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                font-weight: 500;
            }

            .firebase-alert-danger {
                background: linear-gradient(135deg, #ffeaea, #ffcccc);
                color: #d63031;
                border-left: 4px solid #d63031;
            }

            /* Subtle Background Elements */
            .background-elements {
                position: absolute;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                z-index: 1;
                pointer-events: none;
            }

            .bg-element {
                position: absolute;
                border-radius: 50%;
                background: linear-gradient(135deg, rgba(214, 51, 132, 0.03), rgba(233, 30, 99, 0.03));
                animation: floatElement 20s infinite linear;
            }

            .bg-element-1 {
                width: 200px;
                height: 200px;
                top: 10%;
                left: 5%;
                animation-delay: 0s;
            }

            .bg-element-2 {
                width: 150px;
                height: 150px;
                top: 70%;
                right: 8%;
                animation-delay: -7s;
            }

            .bg-element-3 {
                width: 100px;
                height: 100px;
                bottom: 15%;
                left: 15%;
                animation-delay: -14s;
            }

            @keyframes floatElement {
                0% {
                    transform: translateY(0px) rotate(0deg) scale(1);
                }

                33% {
                    transform: translateY(-30px) rotate(120deg) scale(1.1);
                }

                66% {
                    transform: translateY(15px) rotate(240deg) scale(0.9);
                }

                100% {
                    transform: translateY(0px) rotate(360deg) scale(1);
                }
            }

            /* Loading Animation */
            .firebase-btn-loading {
                position: relative;
                color: transparent;
            }

            .firebase-btn-loading::after {
                content: '';
                position: absolute;
                width: 22px;
                height: 22px;
                top: 50%;
                left: 50%;
                margin-left: -11px;
                margin-top: -11px;
                border: 3px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top-color: white;
                animation: firebaseSpin 0.8s linear infinite;
            }

            @keyframes firebaseSpin {
                to {
                    transform: rotate(360deg);
                }
            }

            /* Mobile Responsive */
            @media (max-width: 480px) {
                .firebase-login-card {
                    padding: 32px 24px;
                    margin: 16px;
                    border-radius: 20px;
                }

                .firebase-title {
                    font-size: 28px;
                }

                .firebase-logo {
                    width: 64px;
                    height: 64px;
                    border-radius: 18px;
                }

                .firebase-logo i {
                    font-size: 24px;
                }

                .firebase-input {
                    padding: 16px 18px;
                    font-size: 16px;
                }

                .firebase-btn {
                    padding: 16px 20px;
                    font-size: 16px;
                }

                .firebase-feature-item {
                    font-size: 13px;
                }
            }

            /* Tablet Responsive */
            @media (min-width: 768px) and (max-width: 1024px) {
                .firebase-login-card {
                    max-width: 400px;
                    padding: 40px 32px;
                }
            }
        </style>
    </head>

    <body>
        <div class="firebase-login-container">
            <div class="background-elements">
                <div class="bg-element bg-element-1"></div>
                <div class="bg-element bg-element-2"></div>
                <div class="bg-element bg-element-3"></div>
            </div>

            <div class="firebase-login-card">
                <div class="firebase-header">
                    <div class="firebase-logo">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h1 class="firebase-title">MealApp25</h1>
                    <p class="firebase-subtitle">Sign in to manage your meals and expenses</p>
                </div>

                <?php
                if (!empty($message)) {
                    echo str_replace('alert alert-danger', 'firebase-alert firebase-alert-danger', $message);
                }
                ?>

                <form action="index.php" method="POST" id="firebaseLoginForm">
                    <div class="firebase-form-group">
                        <input type="email" id="firebaseEmail" name="email" class="firebase-input" required
                            placeholder="Enter your registered email" autocomplete="email">
                    </div>

                    <button type="submit" class="firebase-btn" id="firebaseLoginButton">
                        <span id="firebaseButtonText">Sign In to Dashboard</span>
                    </button>
                </form>

                <div class="firebase-admin-section">
                    <a href="admin.php" class="firebase-admin-link">
                        <i class="fas fa-user-shield"></i>
                        Admin Login
                    </a>
                </div>


            </div>
        </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const loginForm = document.getElementById('firebaseLoginForm');
                const loginButton = document.getElementById('firebaseLoginButton');
                const buttonText = document.getElementById('firebaseButtonText');
                const emailInput = document.getElementById('firebaseEmail');

                // Auto-focus with delay for better UX
                setTimeout(() => {
                    emailInput.focus();
                }, 500);

                // Enhanced input interactions
                emailInput.addEventListener('focus', function () {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });

                emailInput.addEventListener('blur', function () {
                    this.parentElement.style.transform = 'translateY(0)';
                });

                // Real-time validation feedback
                emailInput.addEventListener('input', function () {
                    if (this.validity.valid && this.value) {
                        this.style.borderColor = '#10b981';
                        this.style.boxShadow = '0 4px 15px rgba(16, 185, 129, 0.15)';
                    } else {
                        this.style.borderColor = '#e9ecef';
                        this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.04)';
                    }
                });

                // Form submission with enhanced loading state
                loginForm.addEventListener('submit', function (e) {
                    if (emailInput.value.trim()) {
                        // Add loading state
                        loginButton.classList.add('firebase-btn-loading');
                        buttonText.style.opacity = '0';
                        loginButton.disabled = true;

                        // Add ripple effect
                        const ripple = document.createElement('span');
                        const rect = loginButton.getBoundingClientRect();
                        const size = Math.max(rect.width, rect.height);
                        const x = e.clientX - rect.left - size / 2;
                        const y = e.clientY - rect.top - size / 2;

                        ripple.style.cssText = `
                            position: absolute;
                            border-radius: 50%;
                            background: rgba(255, 255, 255, 0.6);
                            transform: scale(0);
                            animation: ripple 0.6s linear;
                            width: ${size}px;
                            height: ${size}px;
                            left: ${x}px;
                            top: ${y}px;
                        `;

                        loginButton.appendChild(ripple);

                        setTimeout(() => {
                            ripple.remove();
                        }, 600);

                        // Fallback to remove loading state after 3 seconds
                        setTimeout(() => {
                            if (loginButton.classList.contains('firebase-btn-loading')) {
                                loginButton.classList.remove('firebase-btn-loading');
                                buttonText.style.opacity = '1';
                                loginButton.disabled = false;
                            }
                        }, 3000);
                    }
                });

                // Add ripple animation style
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes ripple {
                        to {
                            transform: scale(4);
                            opacity: 0;
                        }
                    }
                `;
                document.head.appendChild(style);
            });
        </script>
    </body>

    </html>
    <?php
    // No footer included for login page
}
?>