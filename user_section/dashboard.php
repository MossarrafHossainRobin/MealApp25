<?php
// user_section/dashboard.php - Dashboard Overview

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
    echo "<div style='padding: 20px; text-align: center; color: red;'>Please login first.</div>";
    return;
}

// Get current month and year for display
$current_month_name = date('F');
$current_year = date('Y');
$current_month_year = $current_month_name . ' ' . $current_year;

// Include and use the DashboardAction class
require_once __DIR__ . '/../user_process/dashboard_action.php';
$dashboardAction = new DashboardAction();

// Get all dashboard data
$dashboardData = $dashboardAction->getDashboardData($_SESSION['user_id']);

// Check if data is valid
if (!$dashboardData || !isset($dashboardData['display_data'])) {
    echo "<div style='padding: 20px; text-align: center; color: red;'>Error loading dashboard data</div>";
    return;
}

$display = $dashboardData['display_data'];

// Load members for transaction dropdown - FIXED: Changed 'status' to 'is_active'
require_once __DIR__ . '/../config/database.php';
$db_instance = new Database();
$conn = $db_instance->getConnection();

$members = [];
try {
    $stmt = $conn->prepare("
        SELECT id, name, email 
        FROM members 
        WHERE id != ? AND is_active = 1
        ORDER BY name
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error loading members: " . $e->getMessage());
}
?>

<style>
    /* Reset body overflow for desktop */
    body {
        overflow: auto;
    }

    /* Mobile First Design - Show by default */
    .mobile-dashboard {
        display: block;
    }

    .desktop-dashboard {
        display: none;
    }

    /* Mobile Dashboard Styles */
    .mobile-dashboard {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 0;
        margin: 0;
    }

    .dashboard-content {
        padding-bottom: 70px;
    }

    /* Balance Section - Slightly Larger */
    .balance-section {
        background: linear-gradient(135deg, #8e44ad, #9b59b6);
        padding: 20px 15px;
        position: relative;
    }

    .balance-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px);
        border-radius: 14px;
        padding: 18px 20px;
        color: white;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
        transition: all 0.3s ease;
        max-width: 300px;
        margin: 0 auto;
    }

    .balance-card:active {
        transform: scale(0.98);
        background: rgba(255, 255, 255, 0.2);
    }

    .balance-label {
        font-size: 0.75rem;
        opacity: 0.9;
        margin-bottom: 6px;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .balance-amount {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 6px 0;
        transition: all 0.3s ease;
        letter-spacing: -0.5px;
    }

    .balance-card.hidden-balance .balance-amount {
        filter: blur(10px);
        user-select: none;
    }

    .tap-hint {
        font-size: 0.7rem;
        opacity: 0.7;
        margin-top: 6px;
    }

    /* Stats Grid - Slightly Larger */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        padding: 20px 15px;
        background: white;
    }

    .stat-item {
        background: white;
        border-radius: 12px;
        padding: 16px 10px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #f0f0f0;
        transition: all 0.2s ease;
    }

    .stat-item:active {
        transform: scale(0.96);
        background: #fafafa;
    }

    .stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        margin: 0 auto 10px auto;
    }

    .stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 3px;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #666;
        font-weight: 500;
        line-height: 1.2;
    }

    /* Overview Section - Slightly Larger */
    .overview-section {
        background: white;
        padding: 18px 15px 20px 15px;
        border-top: 1px solid #f0f0f0;
    }

    .overview-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .overview-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .overview-item {
        text-align: center;
        padding: 14px 8px;
        border-radius: 10px;
        background: #fafafa;
        transition: all 0.2s ease;
        border: 1px solid #f5f5f5;
    }

    .overview-item:active {
        background: #f0f0f0;
        transform: scale(0.98);
    }

    .overview-value {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 3px;
        line-height: 1;
    }

    .overview-label {
        font-size: 0.7rem;
        color: #666;
        font-weight: 500;
        line-height: 1.2;
    }

    /* Color Coding */
    .text-spent {
        color: #e74c3c;
    }

    .text-due {
        color: #e67e22;
    }

    .text-meals {
        color: #27ae60;
    }

    .text-rent {
        color: #3498db;
    }

    .text-bazar {
        color: #9b59b6;
    }

    /* Desktop Dashboard Styles - FIXED DISPLAY */
    @media (min-width: 992px) {
        .mobile-dashboard {
            display: none !important;
        }

        .desktop-dashboard {
            display: block !important;
            padding: 30px 0;
            background: #f8f9fa;
            min-height: calc(100vh - 80px);
        }

        .desktop-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .desktop-balance-card {
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
            border-radius: 20px;
            padding: 30px;
            color: white;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            grid-column: 1 / -1;
        }

        .desktop-balance-amount {
            font-size: 3rem;
            font-weight: 800;
            margin: 15px 0;
        }

        .desktop-balance-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        .desktop-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .desktop-stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .desktop-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .desktop-stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .desktop-stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .desktop-stat-label {
            font-size: 1rem;
            color: #666;
            font-weight: 500;
        }

        .desktop-overview {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
        }

        .desktop-overview-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .desktop-overview-item {
            text-align: center;
            padding: 20px 10px;
            border-radius: 12px;
            background: #fafafa;
            transition: all 0.2s ease;
        }

        .desktop-overview-item:hover {
            background: #f0f0f0;
        }

        .desktop-overview-value {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .desktop-overview-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .desktop-features {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
            background: var(--gradient);
            color: white;
        }

        .feature-card:hover .feature-icon,
        .feature-card:hover .feature-title {
            color: white;
        }

        .feature-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .feature-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #2c3e50;
            transition: all 0.3s ease;
        }
    }

    /* Animation for balance reveal */
    @keyframes balanceReveal {
        0% {
            filter: blur(10px);
            opacity: 0.7;
        }

        100% {
            filter: blur(0);
            opacity: 1;
        }
    }

    .balance-reveal {
        animation: balanceReveal 0.4s ease-out;
    }

    /* Transaction Modal Styles */
    .transaction-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        justify-content: center;
        align-items: center;
        padding: 20px;
        box-sizing: border-box;
    }

    .transaction-modal-content {
        background: white;
        border-radius: 20px;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px) scale(0.9);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .transaction-modal-header {
        background: linear-gradient(135deg, #8e44ad, #9b59b6);
        color: white;
        padding: 25px;
        border-radius: 20px 20px 0 0;
        text-align: center;
        position: relative;
    }

    .transaction-modal-header h3 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 700;
    }

    .transaction-close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .transaction-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .transaction-modal-body {
        padding: 25px;
    }

    .transaction-step {
        display: none;
    }

    .transaction-step.active {
        display: block;
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .transaction-form-group {
        margin-bottom: 20px;
    }

    .transaction-form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
    }

    .transaction-form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .transaction-form-control:focus {
        outline: none;
        border-color: #8e44ad;
        box-shadow: 0 0 0 3px rgba(142, 68, 173, 0.1);
    }

    .transaction-form-control.select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 12px center;
        background-repeat: no-repeat;
        background-size: 16px;
        padding-right: 40px;
        appearance: none;
    }

    .transaction-btn {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .transaction-btn-primary {
        background: linear-gradient(135deg, #8e44ad, #9b59b6);
        color: white;
    }

    .transaction-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(142, 68, 173, 0.3);
    }

    .transaction-btn-success {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
    }

    .transaction-btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(39, 174, 96, 0.3);
    }

    .transaction-btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .transaction-btn-secondary:hover {
        background: #7f8c8d;
        transform: translateY(-2px);
    }

    .transaction-btn-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .transaction-btn-group .transaction-btn {
        flex: 1;
    }

    .otp-input {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        letter-spacing: 10px;
        padding: 15px;
    }

    .loading-spinner {
        display: none;
        text-align: center;
        padding: 30px;
    }

    .loading-spinner i {
        font-size: 2.5rem;
        color: #8e44ad;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .success-icon {
        text-align: center;
        font-size: 4rem;
        color: #27ae60;
        margin-bottom: 20px;
        animation: bounce 0.6s ease-in-out;
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-10px);
        }

        60% {
            transform: translateY(-5px);
        }
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border: 1px solid #f5c6cb;
        font-size: 0.9rem;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .transaction-modal {
            padding: 10px;
        }

        .transaction-modal-content {
            border-radius: 15px;
            max-height: 95vh;
        }

        .transaction-modal-header {
            padding: 20px 15px;
        }

        .transaction-modal-header h3 {
            font-size: 1.2rem;
        }

        .transaction-modal-body {
            padding: 20px 15px;
        }

        .transaction-form-control {
            padding: 15px;
            font-size: 16px;
        }

        .transaction-btn {
            padding: 16px;
        }

        .otp-input {
            font-size: 1.3rem;
            letter-spacing: 8px;
            padding: 18px;
        }
    }

    /* Desktop Enhancements */
    @media (min-width: 769px) {
        .transaction-modal-content {
            max-width: 450px;
        }
    }

    /* Prevent body scroll when modal is open */
    body.modal-open {
        overflow: hidden;
    }
</style>

<!-- Mobile Dashboard -->
<div class="mobile-dashboard">
    <div class="dashboard-content">
        <!-- Balance Section -->
        <div class="balance-section">
            <div class="balance-card hidden-balance" id="balanceCard">
                <div class="balance-label">CURRENT BALANCE (<?php echo $current_month_name . ' ' . $current_year; ?>)
                </div>
                <div class="balance-amount" id="balanceAmount">
                    ৳ <?php echo $display['current_balance']; ?>
                </div>
                <div class="tap-hint">Tap to show/hide balance</div>
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value text-bazar">
                    ৳ <?php echo $display['total_bazar_current_month']; ?>
                </div>
                <div class="stat-label">Bazar (<?php echo $current_month_name; ?>)</div>
            </div>

            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value text-due">
                    ৳ <?php echo $display['total_due']; ?>
                </div>
                <div class="stat-label">Total Due</div>
            </div>

            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="stat-value text-meals">
                    <?php echo $display['total_meals_current_month']; ?>
                </div>
                <div class="stat-label">Meals (<?php echo $current_month_name; ?>)</div>
            </div>

            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="stat-value text-rent">
                    ৳ <?php echo $display['monthly_base_rent']; ?>
                </div>
                <div class="stat-label">Monthly Rent</div>
            </div>
        </div>

        <!-- Monthly Overview -->
        <div class="overview-section">
            <div class="overview-title">
                <i class="fas fa-chart-pie text-primary"></i>
                MONTHLY OVERVIEW - <?php echo strtoupper($current_month_year); ?> (ALL MEMBERS)
            </div>

            <div class="overview-grid">
                <div class="overview-item">
                    <div class="overview-value text-meals">
                        <?php echo $display['total_meals_all_members']; ?>
                    </div>
                    <div class="overview-label">Total Meals</div>
                </div>
                <div class="overview-item">
                    <div class="overview-value" style="color: #3498db;">
                        ৳ <?php echo $display['current_meal_rate']; ?>
                    </div>
                    <div class="overview-label">Meal Rate</div>
                </div>
                <div class="overview-item">
                    <div class="overview-value text-bazar">
                        ৳ <?php echo $display['total_bazar_all_members']; ?>
                    </div>
                    <div class="overview-label">Total Bazar</div>
                </div>
            </div>

            <!-- Due Calculation Section -->
            <div
                style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px; border: 1px solid #e9ecef;">
                <div
                    style="font-size: 0.8rem; font-weight: 600; color: #2c3e50; margin-bottom: 10px; text-align: center;">
                    <i class="fas fa-calculator me-1"></i> <?php echo $current_month_name; ?> DUE CALCULATION
                </div>
                <div style="text-align: center; margin-bottom: 10px;">
                    <span style="font-size: 1.1rem; font-weight: 700; color: <?php echo $display['due_color']; ?>;">
                        ৳ <?php echo $display['due_amount_abs']; ?>
                    </span>
                    <div style="font-size: 0.7rem; color: <?php echo $display['due_color']; ?>; font-weight: 500;">
                        <?php echo $display['due_message']; ?>
                    </div>
                </div>
                <div style="font-size: 0.65rem; color: #666;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                        <span>Total Meals: <?php echo $display['total_meals_current_month']; ?></span>
                        <span>× Meal Rate: ৳<?php echo $display['current_meal_rate']; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                        <span>Total Meal Cost:</span>
                        <span>৳<?php echo $display['total_meal_cost']; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                        <span>Total Bazar:</span>
                        <span>৳<?php echo $display['total_bazar_current_month']; ?></span>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; font-weight: 600; border-top: 1px solid #ddd; padding-top: 5px; margin-top: 5px;">
                        <span>Net Amount:</span>
                        <span style="color: <?php echo $display['due_color']; ?>;">
                            ৳<?php echo $display['due_amount']; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Send Money Button -->
            <div style="margin-top: 20px; text-align: center;">
                <button onclick="openTransactionModal()" style="background: linear-gradient(135deg, #27ae60, #2ecc71); 
                               color: white; 
                               border: none; 
                               padding: 12px 24px; 
                               border-radius: 25px; 
                               font-weight: 600; 
                               cursor: pointer;
                               font-size: 0.9rem;
                               width: 100%;
                               max-width: 200px;">
                    <i class="fas fa-paper-plane me-2"></i>Send Money
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Desktop Dashboard -->
<div class="desktop-dashboard">
    <div class="desktop-grid">
        <!-- Balance Card -->
        <div class="desktop-balance-card">
            <div class="desktop-balance-label">CURRENT BALANCE
                (<?php echo $current_month_name . ' ' . $current_year; ?>)</div>
            <div class="desktop-balance-amount">
                ৳ <?php echo $display['current_balance']; ?>
            </div>
            <div class="desktop-balance-label">Available for <?php echo $current_month_name . ' ' . $current_year; ?>
            </div>
        </div>

        <!-- Statistics -->
        <div class="desktop-stats">
            <div class="desktop-stat-card">
                <div class="desktop-stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="desktop-stat-value text-bazar">
                    ৳ <?php echo $display['total_bazar_current_month']; ?>
                </div>
                <div class="desktop-stat-label">Bazar (<?php echo $current_month_name; ?>)</div>
            </div>

            <div class="desktop-stat-card">
                <div class="desktop-stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="desktop-stat-value text-due">
                    ৳ <?php echo $display['total_due']; ?>
                </div>
                <div class="desktop-stat-label">Total Due Amount</div>
            </div>

            <div class="desktop-stat-card">
                <div class="desktop-stat-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="desktop-stat-value text-meals">
                    <?php echo $display['total_meals_current_month']; ?>
                </div>
                <div class="desktop-stat-label">Meals (<?php echo $current_month_name; ?>)</div>
            </div>

            <div class="desktop-stat-card">
                <div class="desktop-stat-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="desktop-stat-value text-rent">
                    ৳ <?php echo $display['monthly_base_rent']; ?>
                </div>
                <div class="desktop-stat-label">Monthly Base Rent</div>
            </div>
        </div>

        <!-- Overview -->
        <div class="desktop-overview">
            <h3 style="color: #2c3e50; margin-bottom: 20px;">
                <i class="fas fa-chart-line me-2"></i><?php echo $current_month_name; ?> Overview (All Members)
            </h3>

            <div class="desktop-overview-grid">
                <div class="desktop-overview-item">
                    <div class="desktop-overview-value text-meals">
                        <?php echo $display['total_meals_all_members']; ?>
                    </div>
                    <div class="desktop-overview-label">Total Meals</div>
                </div>
                <div class="desktop-overview-item">
                    <div class="desktop-overview-value" style="color: #3498db;">
                        ৳ <?php echo $display['current_meal_rate']; ?>
                    </div>
                    <div class="desktop-overview-label">Meal Rate</div>
                </div>
                <div class="desktop-overview-item">
                    <div class="desktop-overview-value text-bazar">
                        ৳ <?php echo $display['total_bazar_all_members']; ?>
                    </div>
                    <div class="desktop-overview-label">Total Bazar</div>
                </div>
            </div>

            <!-- Due Calculation Section for Desktop -->
            <div
                style="margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 12px; border: 1px solid #e9ecef;">
                <h4 style="color: #2c3e50; margin-bottom: 15px; text-align: center;">
                    <i class="fas fa-calculator me-2"></i><?php echo $current_month_name; ?> Due Calculation
                </h4>
                <div style="text-align: center; margin-bottom: 15px;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: <?php echo $display['due_color']; ?>;">
                        ৳ <?php echo $display['due_amount_abs']; ?>
                    </span>
                    <div
                        style="font-size: 0.9rem; color: <?php echo $display['due_color']; ?>; font-weight: 500; margin-top: 5px;">
                        <?php echo $display['due_message']; ?>
                    </div>
                </div>
                <div style="font-size: 0.8rem; color: #666; max-width: 400px; margin: 0 auto;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; padding: 3px 0;">
                        <span>Total Meals: <?php echo $display['total_meals_current_month']; ?></span>
                        <span>× Meal Rate: ৳<?php echo $display['current_meal_rate']; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; padding: 3px 0;">
                        <span>Total Meal Cost:</span>
                        <span>৳<?php echo $display['total_meal_cost']; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; padding: 3px 0;">
                        <span>Total Bazar:</span>
                        <span>৳<?php echo $display['total_bazar_current_month']; ?></span>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; font-weight: 600; border-top: 1px solid #ddd; padding-top: 8px; margin-top: 8px;">
                        <span>Net Amount:</span>
                        <span style="color: <?php echo $display['due_color']; ?>;">
                            ৳<?php echo $display['due_amount']; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Send Money Button -->
            <div style="margin-top: 25px; text-align: center;">
                <button onclick="openTransactionModal()" style="background: linear-gradient(135deg, #27ae60, #2ecc71); 
                               color: white; 
                               border: none; 
                               padding: 15px 30px; 
                               border-radius: 25px; 
                               font-weight: 600; 
                               cursor: pointer;
                               font-size: 1rem;
                               transition: all 0.3s ease;">
                    <i class="fas fa-paper-plane me-2"></i>Send Money to Member
                </button>
            </div>
        </div>

        <!-- Quick Features -->
        <div class="desktop-features">
            <div class="feature-card" onclick="window.location.href='index.php?section=bazar'">
                <div class="feature-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="feature-title">Add Bazar</div>
            </div>
            <div class="feature-card" onclick="window.location.href='index.php?section=meal'">
                <div class="feature-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="feature-title">Log Meal</div>
            </div>
            <div class="feature-card" onclick="window.location.href='index.php?section=water'">
                <div class="feature-icon">
                    <i class="fas fa-tint"></i>
                </div>
                <div class="feature-title">Water Duty</div>
            </div>
            <div class="feature-card" onclick="window.location.href='index.php?section=cost'">
                <div class="feature-icon">
                    <i class="fas fa-money-bill"></i>
                </div>
                <div class="feature-title">Pay Cost</div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Modal -->
<div id="transactionModal" class="transaction-modal">
    <div class="transaction-modal-content">
        <!-- Modal Header -->
        <div class="transaction-modal-header">
            <h3><i class="fas fa-paper-plane me-2"></i>Send Money</h3>
            <button class="transaction-close-btn" onclick="closeTransactionModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="transaction-modal-body">
            <!-- Step 1: Transaction Details -->
            <div id="step1" class="transaction-step active">
                <div class="transaction-form-group">
                    <label for="receiverSelect"><i class="fas fa-user me-2"></i>Select Member</label>
                    <select id="receiverSelect" class="transaction-form-control transaction-form-control select">
                        <option value="">Choose a member...</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?php echo $member['id']; ?>">
                                <?php echo htmlspecialchars($member['name']) . ' (' . htmlspecialchars($member['email']) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="transaction-form-group">
                    <label for="amountInput"><i class="fas fa-money-bill-wave me-2"></i>Amount (৳)</label>
                    <input type="number" id="amountInput" step="0.01" min="1" class="transaction-form-control"
                        placeholder="Enter amount">
                </div>

                <div class="transaction-form-group">
                    <label for="descriptionInput"><i class="fas fa-file-alt me-2"></i>Description</label>
                    <textarea id="descriptionInput" class="transaction-form-control"
                        placeholder="Why are you sending this money? (Required)" rows="3"></textarea>
                </div>

                <button onclick="initiateTransaction()" class="transaction-btn transaction-btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Send OTP
                </button>
            </div>

            <!-- Step 2: OTP Verification -->
            <div id="step2" class="transaction-step">
                <div style="text-align: center; margin-bottom: 25px;">
                    <div style="font-size: 3rem; color: #8e44ad; margin-bottom: 15px;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4 style="color: #2c3e50; margin-bottom: 10px;">OTP Verification</h4>
                    <p style="color: #666; line-height: 1.5;">We've sent a 6-digit OTP to your email address. Please
                        enter it below to complete the transaction.</p>
                </div>

                <div class="transaction-form-group">
                    <label for="otpInput">Enter OTP Code</label>
                    <input type="text" id="otpInput" maxlength="6" class="transaction-form-control otp-input"
                        placeholder="000000">
                </div>

                <div class="transaction-btn-group">
                    <button onclick="goToStep1()" class="transaction-btn transaction-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </button>
                    <button onclick="verifyOTP()" class="transaction-btn transaction-btn-success">
                        <i class="fas fa-check me-2"></i>Confirm
                    </button>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <button onclick="resendOTP()"
                        style="background: none; border: none; color: #3498db; cursor: pointer; font-size: 0.9rem;">
                        <i class="fas fa-redo me-2"></i>Didn't receive OTP? Resend
                    </button>
                </div>
            </div>

            <!-- Step 3: Success Message -->
            <div id="step3" class="transaction-step">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div style="text-align: center;">
                    <h4 style="color: #27ae60; margin-bottom: 10px;">Transaction Successful!</h4>
                    <p style="color: #666; line-height: 1.5; margin-bottom: 25px;">
                        Your money has been transferred successfully. Confirmation emails have been sent to both
                        parties.
                    </p>
                    <button onclick="closeTransactionModal()" class="transaction-btn transaction-btn-success">
                        <i class="fas fa-check me-2"></i>Done
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="loading-spinner">
                <i class="fas fa-spinner"></i>
                <p style="margin-top: 15px; color: #666;">Processing your transaction...</p>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="error-message" style="display: none;"></div>
        </div>
    </div>
</div>

<script>
    // Mobile balance functionality
    const balanceCard = document.getElementById('balanceCard');
    const balanceAmount = document.getElementById('balanceAmount');
    let balanceVisible = false;

    if (balanceCard) {
        // Initialize with balance hidden
        balanceCard.classList.add('hidden-balance');

        balanceCard.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            balanceVisible = !balanceVisible;

            if (balanceVisible) {
                // Reveal balance
                balanceCard.classList.remove('hidden-balance');
                balanceAmount.classList.add('balance-reveal');

                setTimeout(() => {
                    balanceAmount.classList.remove('balance-reveal');
                }, 400);
            } else {
                // Hide balance
                balanceCard.classList.add('hidden-balance');
            }
        });

        // Touch feedback
        balanceCard.addEventListener('touchstart', function () {
            this.style.opacity = '0.9';
        });

        balanceCard.addEventListener('touchend', function () {
            this.style.opacity = '1';
        });
    }

    // Add touch feedback to all interactive elements
    document.querySelectorAll('.stat-item, .overview-item').forEach(item => {
        item.addEventListener('touchstart', function () {
            this.style.opacity = '0.8';
        });

        item.addEventListener('touchend', function () {
            this.style.opacity = '1';
        });
    });

    // Fix for desktop - ensure body scroll is enabled
    if (window.innerWidth >= 992) {
        document.body.style.overflow = 'auto';
        document.documentElement.style.overflow = 'auto';
    }

    // Transaction Modal Variables
    let currentTransactionId = null;
    let currentStep = 1;

    // Modal Functions
    function openTransactionModal() {
        document.getElementById('transactionModal').style.display = 'flex';
        document.body.classList.add('modal-open');
        resetTransactionForm();
    }

    function closeTransactionModal() {
        document.getElementById('transactionModal').style.display = 'none';
        document.body.classList.remove('modal-open');
        resetTransactionForm();
    }

    function resetTransactionForm() {
        currentStep = 1;
        currentTransactionId = null;
        showStep(1);
        document.getElementById('receiverSelect').value = '';
        document.getElementById('amountInput').value = '';
        document.getElementById('descriptionInput').value = '';
        document.getElementById('otpInput').value = '';
        hideError();
        hideLoading();
    }

    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.transaction-step').forEach(el => {
            el.classList.remove('active');
        });
        document.getElementById('loadingState').style.display = 'none';

        // Show selected step
        document.getElementById(`step${step}`).classList.add('active');
        currentStep = step;
    }

    function showLoading() {
        document.querySelectorAll('.transaction-step').forEach(el => {
            el.classList.remove('active');
        });
        document.getElementById('loadingState').style.display = 'block';
    }

    function hideLoading() {
        const loadingElement = document.getElementById('loadingState');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
        showStep(currentStep);
    }

    function showError(message) {
        const errorDiv = document.getElementById('errorMessage');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    function hideError() {
        document.getElementById('errorMessage').style.display = 'none';
    }

    // Transaction Functions - FIXED API PATH
    function initiateTransaction() {
        const receiverId = document.getElementById('receiverSelect').value;
        const amount = document.getElementById('amountInput').value;
        const description = document.getElementById('descriptionInput').value.trim();

        // Validation
        if (!receiverId) {
            showError('Please select a member');
            return;
        }

        if (!amount || amount <= 0) {
            showError('Please enter a valid amount');
            return;
        }

        if (!description) {
            showError('Please enter a description');
            return;
        }

        showLoading();
        hideError();

        const formData = new FormData();
        formData.append('action', 'initiate_transaction');
        formData.append('receiver_id', receiverId);
        formData.append('amount', amount);
        formData.append('description', description);

        console.log('🚀 Initiating transaction with:', {
            receiverId,
            amount,
            description
        });

        // FIXED: Correct API path - using the correct path in user_section folder
        fetch('user_section/transaction_api.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                console.log('📨 Response received:', response);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Data received:', data);
                if (data.success) {
                    currentTransactionId = data.transaction_id;
                    showStep(2);
                } else {
                    showError(data.message || 'Transaction failed');
                    hideLoading();
                }
            })
            .catch(error => {
                console.error('❌ Fetch error:', error);
                showError('Transaction failed: ' + error.message);
                hideLoading();
            });
    }

    function verifyOTP() {
        const otp = document.getElementById('otpInput').value.trim();

        if (!otp || otp.length !== 6) {
            showError('Please enter a valid 6-digit OTP');
            return;
        }

        showLoading();
        hideError();

        const formData = new FormData();
        formData.append('action', 'verify_otp');
        formData.append('transaction_id', currentTransactionId);
        formData.append('otp', otp);

        console.log('🔐 Verifying OTP:', {
            transaction_id: currentTransactionId,
            otp
        });

        // FIXED: Correct API path - using the correct path in user_section folder
        fetch('user_section/transaction_api.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ OTP verification response:', data);
                if (data.success) {
                    showStep(3);
                } else {
                    showError(data.message || 'OTP verification failed');
                    hideLoading();
                }
            })
            .catch(error => {
                console.error('❌ OTP verification error:', error);
                showError('Verification failed: ' + error.message);
                hideLoading();
            });
    }

    // REMOVE THE DUPLICATE verifyOTP() FUNCTION THAT APPEARS TWICE
    function goToStep1() {
        showStep(1);
        hideError();
    }

    function resendOTP() {
        showError('Please initiate the transaction again to receive a new OTP.');
        setTimeout(() => {
            closeTransactionModal();
            setTimeout(openTransactionModal, 300);
        }, 2000);
    }

    // Close modal when clicking outside
    document.getElementById('transactionModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeTransactionModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('transactionModal').style.display === 'flex') {
            closeTransactionModal();
        }
    });

    // Auto-advance OTP input
    document.getElementById('otpInput').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 6) {
            verifyOTP();
        }
    });
</script>