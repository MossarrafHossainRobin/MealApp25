<?php
require_once '../config/database.php';

// Initialize Database
$db = new Database();
$connection = $db->getConnection();

header('Content-Type: application/json');

try {
    $year = $_GET['year'] ?? date('Y');
    $month = $_GET['month'] ?? date('m');

    $months = [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December'
    ];

    $start_date = "$year-$month-01";
    $end_date = date('Y-m-t', strtotime($start_date));

    // Generate dates
    $dates = [];
    $days_in_month = date('t', strtotime($start_date));
    for ($day = 1; $day <= $days_in_month; $day++) {
        $date = sprintf("%s-%02d", $year . '-' . $month, $day);
        $dates[] = $date;
    }

    // Get all members
    $members_stmt = $connection->query("SELECT id, name FROM members ORDER BY name");
    $members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get meal data
    $meals_stmt = $connection->prepare("
        SELECT member_id, meal_date, meal_count 
        FROM meal_counts 
        WHERE meal_date BETWEEN ? AND ?
    ");
    $meals_stmt->execute([$start_date, $end_date]);
    $meal_data = $meals_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organize meal data
    $meals = [];
    foreach ($meal_data as $meal) {
        if (!isset($meals[$meal['member_id']])) {
            $meals[$meal['member_id']] = [];
        }
        $meals[$meal['member_id']][$meal['meal_date']] = $meal['meal_count'];
    }

    // Calculate totals
    $total_meals = 0;
    foreach ($meals as $member_meals) {
        foreach ($member_meals as $count) {
            $total_meals += $count;
        }
    }

    // Get bazar total for average calculation
    $bazar_stmt = $connection->prepare("SELECT SUM(amount) as total_bazar FROM bazar WHERE DATE(created_at) BETWEEN ? AND ?");
    $bazar_stmt->execute([$start_date, $end_date]);
    $bazar_result = $bazar_stmt->fetch(PDO::FETCH_ASSOC);
    $total_bazar = $bazar_result['total_bazar'] ?? 0;

    $avg_meal_cost = ($total_meals > 0) ? $total_bazar / $total_meals : 0;

    echo json_encode([
        'success' => true,
        'year' => $year,
        'month' => $month,
        'month_name' => $months[$month] ?? 'Unknown',
        'dates' => $dates,
        'members' => $members,
        'meals' => $meals,
        'total_meals' => $total_meals,
        'total_bazar' => $total_bazar,
        'avg_meal_cost' => $avg_meal_cost
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>