<?php
session_start();

// Get messages from session
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
$current_section = $_SESSION['current_section'] ?? 'home';

// Clear session messages
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

require_once 'config/database.php';

// Initialize Database
$db = new Database();
$connection = $db->getConnection();

// Fetch data for display
try {
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

    // Get bazar entries
    $bazar_stmt = $connection->query("
        SELECT b.*, m.name as member_name, DATE(b.created_at) as date 
        FROM bazar b 
        JOIN members m ON b.member_id = m.id 
        ORDER BY b.created_at DESC
    ");
    $bazar_entries = $bazar_stmt->fetchAll(PDO::FETCH_ASSOC);

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

// Include header
include 'includes/header.php';
include 'includes/notifications.php';
include 'includes/navigation.php';
?>

<!-- Home Section -->
<div id="home" class="section <?php echo $current_section === 'home' ? 'active' : ''; ?>">
    <?php include 'sections/home.php'; ?>
</div>

<!-- Bazar Section -->
<div id="bazar" class="section <?php echo $current_section === 'bazar' ? 'active' : ''; ?>">
    <?php include 'sections/bazar.php'; ?>
</div>

<!-- Summary Section -->
<div id="summary" class="section <?php echo $current_section === 'summary' ? 'active' : ''; ?>">
    <?php include 'sections/summary.php'; ?>
</div>

<!-- Meal Count Section -->
<div id="mealcount" class="section <?php echo $current_section === 'mealcount' ? 'active' : ''; ?>">
    <?php include 'sections/mealcount.php'; ?>
</div>

<!-- Settlement Section -->
<div id="settlement" class="section <?php echo $current_section === 'settlement' ? 'active' : ''; ?>">
    <?php include 'sections/settlement.php'; ?>
</div>

<!-- Water Duty Section -->
<div id="water" class="section <?php echo $current_section === 'water' ? 'active' : ''; ?>">
    <?php include 'sections/water.php'; ?>
</div>

<!-- Members Section -->
<div id="members" class="section <?php echo $current_section === 'members' ? 'active' : ''; ?>">
    <?php include 'sections/members.php'; ?>
</div>

<?php include 'includes/footer.php'; ?>