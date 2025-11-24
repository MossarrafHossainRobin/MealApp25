<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $connection = $db->getConnection();

    $month = $_GET['month'] ?? date('n');
    $year = $_GET['year'] ?? date('Y');

    // Calculate date range
    $monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);
    $firstDay = "$year-$monthFormatted-01";
    $lastDay = date('Y-m-t', strtotime($firstDay));

    // Get bazar entries for selected month
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
        ORDER BY b.bazar_date DESC
    ");

    $stmt->execute([$firstDay, $lastDay]);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total amount (SUM of all amounts including negative)
    $totalAmount = 0;
    $totalEntries = count($entries);

    foreach ($entries as $entry) {
        $totalAmount += floatval($entry['amount']);
    }

    echo json_encode([
        'success' => true,
        'entries' => $entries,
        'month' => $month,
        'year' => $year,
        'total_entries' => $totalEntries,
        'total_amount' => $totalAmount, // This is the SUM of all amounts
        'first_day' => $firstDay,
        'last_day' => $lastDay
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>