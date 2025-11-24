<?php
session_start();
$current_section = $_POST['current_section'] ?? 'home';
// Get messages from session
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
$current_section = $_SESSION['current_section'] ?? 'home';



// Clear session messages
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// --- DATABASE CONNECTION & DATA FETCHING ---
require_once 'config/database.php';

// Initialize Database
$db = new Database();
$connection = $db->getConnection();

// Fetch data for display
try {
    // ... (All your existing data fetching code is here) ...

    // Get all members
    $members_stmt = $connection->query("SELECT * FROM members ORDER BY name");
    $all_members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get present members
    $present_stmt = $connection->query("
        SELECT m.id, m.name 
        FROM members m 
        JOIN present_members pm ON m.id = pm.member_id 
        ORDER BY m.name
    ");
    $present_members = $present_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get bazar entries for current month
    $current_month_start = date('Y-m-01');
    $current_month_end = date('Y-m-t');

    $bazar_stmt = $connection->prepare("
        SELECT b.*, m.name as member_name 
        FROM bazar b 
        JOIN members m ON b.member_id = m.id 
        WHERE b.bazar_date BETWEEN ? AND ?
        ORDER BY b.bazar_date DESC, b.created_at DESC
    ");
    $bazar_stmt->execute([$current_month_start, $current_month_end]);
    $bazar_entries = $bazar_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get last bazar entry
    $last_bazar_stmt = $connection->query("
        SELECT b.*, m.name as member_name 
        FROM bazar b 
        JOIN members m ON b.member_id = m.id 
        ORDER BY b.bazar_date DESC, b.created_at DESC 
        LIMIT 1
    ");
    $last_bazar = $last_bazar_stmt->fetch(PDO::FETCH_ASSOC);

    // Calculate total bazar for current month
    $total_bazar_stmt = $connection->prepare("
        SELECT COALESCE(SUM(amount), 0) as total_amount 
        FROM bazar 
        WHERE bazar_date BETWEEN ? AND ?
    ");
    $total_bazar_stmt->execute([$current_month_start, $current_month_end]);
    $total_bazar_data = $total_bazar_stmt->fetch(PDO::FETCH_ASSOC);
    $total_bazar_current_month = floatval($total_bazar_data['total_amount']);

    // Get water duty history
    $water_stmt = $connection->query("
        SELECT wd.*, m.name as member_name 
        FROM water_duty wd 
        JOIN members m ON wd.member_id = m.id 
        ORDER BY wd.duty_date DESC 
        LIMIT 15
    ");
    $water_history = $water_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get last water duty
    $last_water_stmt = $connection->query("
        SELECT m.id, m.name 
        FROM water_duty wd 
        JOIN members m ON wd.member_id = m.id 
        ORDER BY wd.duty_date DESC 
        LIMIT 1
    ");
    $last_water = $last_water_stmt->fetch(PDO::FETCH_ASSOC);

    // Get current month meals
    $current_month = date('F');
    $current_year = date('Y');
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');

    $meals_stmt = $connection->prepare("
        SELECT m.id as member_id, m.name, mc.meal_date, COALESCE(mc.meal_count, 0) as meal_count
        FROM members m
        LEFT JOIN meal_counts mc ON m.id = mc.member_id AND mc.meal_date BETWEEN ? AND ?
        ORDER BY m.name, mc.meal_date
    ");
    $meals_stmt->execute([$start_date, $end_date]);
    $current_meals = $meals_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total meals for current month
    $total_meals_stmt = $connection->prepare("
        SELECT COALESCE(SUM(meal_count), 0) as total_meals 
        FROM meal_counts 
        WHERE meal_date BETWEEN ? AND ?
    ");
    $total_meals_stmt->execute([$start_date, $end_date]);
    $total_meals_data = $total_meals_stmt->fetch(PDO::FETCH_ASSOC);
    $total_meals_current_month = floatval($total_meals_data['total_meals']);

    // Calculate settlement data
    $bazar_total_stmt = $connection->prepare("SELECT SUM(amount) as total_bazar FROM bazar WHERE DATE(created_at) BETWEEN ? AND ?");
    $bazar_total_stmt->execute([$start_date, $end_date]);
    $total_bazar = $bazar_total_stmt->fetch(PDO::FETCH_ASSOC)['total_bazar'] ?? 0;

    $meals_total_stmt = $connection->prepare("SELECT SUM(meal_count) as total_meals FROM meal_counts WHERE meal_date BETWEEN ? AND ?");
    $meals_total_stmt->execute([$start_date, $end_date]);
    $total_meals = $meals_total_stmt->fetch(PDO::FETCH_ASSOC)['total_meals'] ?? 0;

    $avg_meal_cost = $total_meals > 0 ? $total_bazar / $total_meals : 0;

    // Get member balances
    $balance_stmt = $connection->prepare("
        SELECT 
            m.id,
            m.name,
            COALESCE(SUM(b.amount), 0) as total_bazar,
            COALESCE(SUM(mc.meal_count), 0) as total_meals,
            (COALESCE(SUM(mc.meal_count), 0) * ?) as expected_cost,
            (COALESCE(SUM(b.amount), 0) - (COALESCE(SUM(mc.meal_count), 0) * ?)) as balance
        FROM members m
        LEFT JOIN bazar b ON m.id = b.member_id AND DATE(b.created_at) BETWEEN ? AND ?
        LEFT JOIN meal_counts mc ON m.id = mc.member_id AND mc.meal_date BETWEEN ? AND ?
        GROUP BY m.id, m.name
    ");
    $balance_stmt->execute([$avg_meal_cost, $avg_meal_cost, $start_date, $end_date, $start_date, $end_date]);
    $balances = $balance_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate transactions
    $transactions = [];
    $creditors = [];
    $debtors = [];

    foreach ($balances as $balance) {
        $balance_amount = floatval($balance['balance']);
        if ($balance_amount > 0.01) {
            $creditors[] = [
                'member' => $balance['name'],
                'amount' => $balance_amount
            ];
        } elseif ($balance_amount < -0.01) {
            $debtors[] = [
                'member' => $balance['name'],
                'amount' => abs($balance_amount)
            ];
        }
    }

    $i = $j = 0;
    while ($i < count($debtors) && $j < count($creditors)) {
        $debtor = $debtors[$i];
        $creditor = $creditors[$j];

        $settlement_amount = min($debtor['amount'], $creditor['amount']);

        if ($settlement_amount > 0.01) {
            $transactions[] = [
                'from' => $debtor['member'],
                'to' => $creditor['member'],
                'amount' => round($settlement_amount, 2)
            ];

            $debtor['amount'] -= $settlement_amount;
            $creditor['amount'] -= $settlement_amount;

            $debtors[$i]['amount'] = $debtor['amount'];
            $creditors[$j]['amount'] = $creditor['amount'];
        }

        if ($debtor['amount'] < 0.01)
            $i++;
        if ($creditor['amount'] < 0.01)
            $j++;
    }

    $transactions = array_filter($transactions, function ($t) {
        return $t['amount'] > 0.01;
    });

    // Get stats for home page
    $total_members = count($all_members);
    $total_bazar_all = $connection->query("SELECT SUM(amount) as total FROM bazar")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $total_meals_all = $connection->query("SELECT SUM(meal_count) as total FROM meal_counts")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $bazar_summary_stmt = $connection->query("
        SELECT m.name as member, COALESCE(SUM(b.amount), 0) as bazarSpent 
        FROM members m 
        LEFT JOIN bazar b ON m.id = b.member_id 
        GROUP BY m.id, m.name 
        ORDER BY bazarSpent DESC
    ");
    $bazar_summary = $bazar_summary_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate dates for current month (for meal count)
    $dates = [];
    $current_date = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
    while ($current_date <= $end_date_obj) {
        $dates[] = $current_date->format('Y-m-d');
        $current_date->modify('+1 day');
    }

} catch (Exception $e) {
    $error_message = "Error fetching data: " . $e->getMessage();
}

// =========================================================================
// 3. DASHBOARD RENDERING (Runs ONLY if user is logged in)
// =========================================================================

// Include header (assumed to contain opening <body> tag)
include 'includes/header.php';
include 'includes/notifications.php';
include 'includes/navigation.php';
?>

<div id="home" class="section <?php echo $current_section === 'home' ? 'active' : ''; ?>">
    <div class="container-fluid px-0">
        <div class="glass-card mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="header-icon">
                            <i class="bi bi-house-door"></i>
                        </div>
                        <div>
                            <h1 class="display-6 fw-bold text-gradient mb-1">Meal Management Dashboard</h1>
                            <p class="text-muted mb-0">Welcome to your meal tracking system</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-end">
                        <div class="text-muted small">Current Month</div>
                        <h4 class="text-primary fw-bold"><?php echo date('F Y'); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="kpi-card primary">
                    <div class="kpi-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value"><?php echo $total_members; ?></div>
                        <div class="kpi-label">Total Members</div>
                        <div class="kpi-trend"><?php echo count($present_members); ?> active</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card success">
                    <div class="kpi-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value">৳<?php echo number_format($total_bazar_current_month, 2); ?></div>
                        <div class="kpi-label">Total Bazar</div>
                        <div class="kpi-trend"><?php echo count($bazar_entries); ?> entries</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card info">
                    <div class="kpi-icon">
                        <i class="bi bi-egg-fried"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value"><?php echo number_format($total_meals_current_month, 1); ?></div>
                        <div class="kpi-label">Total Meals</div>
                        <div class="kpi-trend">This month</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card warning">
                    <div class="kpi-icon">
                        <i class="bi bi-calculator"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value">৳<?php echo number_format($avg_meal_cost, 2); ?></div>
                        <div class="kpi-label">Avg Meal Cost</div>
                        <div class="kpi-trend">Per meal</div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>


<div id="bazar" class="section <?php echo $current_section === 'bazar' ? 'active' : ''; ?>">
    <?php include 'sections/bazar.php'; ?>
</div>
<div id="members" class="section <?php echo $current_section === 'members' ? 'active' : ''; ?>">
    <?php include 'sections/members.php'; ?>
</div>

<div id="mealcount" class="section <?php echo $current_section === 'mealcount' ? 'active' : ''; ?>">
    <?php include 'sections/mealcount.php'; ?>
</div>

<div id="settlement" class="section <?php echo $current_section === 'settlement' ? 'active' : ''; ?>">
    <?php include 'sections/settlement.php'; ?>
</div>


<div id="water" class="section <?php echo $current_section === 'water' ? 'active' : ''; ?>">
    <?php include 'sections/water.php'; ?>
</div>


<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
    }

    .kpi-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 1.25rem;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        /* Added for ::before */
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .kpi-card.primary {
        --gradient-start: #4f46e5;
        --gradient-end: #7c3aed;
    }

    .kpi-card.success {
        --gradient-start: #059669;
        --gradient-end: #10b981;
    }

    .kpi-card.info {
        --gradient-start: #0369a1;
        --gradient-end: #0ea5e9;
    }

    .kpi-card.warning {
        --gradient-start: #d97706;
        --gradient-end: #f59e0b;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .kpi-icon {
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.8;
    }

    .kpi-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .kpi-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .kpi-trend {
        font-size: 0.75rem;
        font-weight: 600;
    }

    .header-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: white;
        font-size: 1.5rem;
    }

    .text-gradient {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .member-contribution-card {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
    }

    .member-contribution-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .activity-item:last-child {
        border-bottom: none !important;
    }

    @media (max-width: 768px) {
        .glass-card {
            padding: 1rem;
        }

        .kpi-card {
            padding: 1rem;
        }

        .kpi-value {
            font-size: 1.5rem;
        }
    }

    .section {
        display: none;
    }

    .section.active {
        display: block;
    }
</style>

<?php include 'includes/footer.php'; ?>