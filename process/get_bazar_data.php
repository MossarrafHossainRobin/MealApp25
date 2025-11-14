<?php
require_once '../config/database.php';

// Initialize Database
$db = new Database();
$connection = $db->getConnection();

header('Content-Type: application/json');

try {
    $month = $_GET['month'] ?? date('n');
    $year = $_GET['year'] ?? date('Y');

    // Validate month and year
    $month = intval($month);
    $year = intval($year);

    if ($month < 1 || $month > 12) {
        $month = date('n');
    }
    if ($year < 2000 || $year > 2100) {
        $year = date('Y');
    }

    // Format month with leading zero
    $monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);

    // Calculate date range for the selected month
    $firstDay = "$year-$monthFormatted-01";
    $lastDay = date('Y-m-t', strtotime($firstDay));

    // Get bazar entries with member names for selected month only using DATE range
    $stmt = $connection->prepare("
        SELECT 
            b.id, 
            b.member_id, 
            b.amount, 
            b.description, 
            b.bazar_count, 
            b.bazar_date,
            b.created_at,
            m.name as member_name
        FROM bazar b 
        JOIN members m ON b.member_id = m.id 
        WHERE b.bazar_date BETWEEN ? AND ?
        ORDER BY b.bazar_date DESC, b.created_at DESC
    ");

    $stmt->execute([$firstDay, $lastDay]);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'month' => $month,
        'year' => $year,
        'first_day' => $firstDay,
        'last_day' => $lastDay,
        'entries' => $entries,
        'total_entries' => count($entries)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>