<?php
// settlement_actions.php

header('Content-Type: application/json');
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Database connection
// REQUIREMENT: Ensure db_connection.php exists and defines $pdo
require_once 'db_connection.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
    switch ($action) {
        case 'get_settlement_data':
            getSettlementData();
            break;
        case 'process_settlement':
            processSettlement();
            break;
        case 'get_recent_settlements':
            getRecentSettlements();
            break;
        case 'export_report':
            // Using POST data for month/year on export as expected by the JS
            exportSettlementReport($_POST['month'] ?? 0, $_POST['year'] ?? 0);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function getSettlementData()
{
    global $pdo;

    $month = intval($_GET['month'] ?? date('m'));
    $year = intval($_GET['year'] ?? date('Y'));

    // Validate input
    if ($month < 1 || $month > 12 || $year < 2020 || $year > 2030) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid month or year']);
        return;
    }

    // Check if settlement already exists for this month
    $stmt = $pdo->prepare("SELECT status FROM settlements WHERE month = ? AND year = ?");
    $stmt->execute([$month, $year]);
    $existingSettlement = $stmt->fetch(PDO::FETCH_ASSOC);

    $settlementStatus = $existingSettlement ? $existingSettlement['status'] : 'pending';

    // Get all active members
    $members = getActiveMembers();

    // Calculate member balances
    $balances = calculateMemberBalances($members, $month, $year);

    // Calculate overview
    $overview = calculateOverview($balances);

    // Calculate settlement transactions
    $transactions = calculateSettlementTransactions($balances);

    echo json_encode([
        'success' => true,
        'data' => [
            'overview' => $overview,
            'balances' => $balances,
            'transactions' => $transactions,
            'settlement_status' => $settlementStatus
        ]
    ]);
}

function getActiveMembers()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT id, name FROM members WHERE is_active = 1 ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function calculateMemberBalances($members, $month, $year)
{
    global $pdo;

    $balances = [];

    foreach ($members as $member) {
        // Get total meals for the member in the specified month
        $stmt = $pdo->prepare("
            SELECT SUM(meal_count) as total_meals 
            FROM meal_counts 
            WHERE member_id = ? AND month = ? AND year = ?
        ");
        $stmt->execute([$member['id'], $month, $year]);
        $mealData = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalMeals = floatval($mealData['total_meals'] ?? 0);

        // Get total bazar for the member in the specified month (Bazar Paid)
        $stmt = $pdo->prepare("
            SELECT SUM(amount) as total_bazar 
            FROM bazar 
            WHERE member_id = ? AND month = ? AND year = ?
        ");
        $stmt->execute([$member['id'], $month, $year]);
        $bazarData = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalBazar = floatval($bazarData['total_bazar'] ?? 0);

        $balances[] = [
            'member_id' => $member['id'],
            'name' => $member['name'],
            'total_meals' => $totalMeals,
            'total_bazar' => $totalBazar
        ];
    }

    // Calculate total meals and total bazar across all members
    $totalAllMeals = array_sum(array_column($balances, 'total_meals'));
    $totalAllBazar = array_sum(array_column($balances, 'total_bazar'));

    // Calculate average meal cost
    $avgMealCost = $totalAllMeals > 0 ? $totalAllBazar / $totalAllMeals : 0;

    // Calculate expected cost and balance for each member
    foreach ($balances as &$balance) {
        $expectedCost = $balance['total_meals'] * $avgMealCost;
        $balance['expected_cost'] = $expectedCost;

        // FIX: Balance = (Bazar Paid) - (Expected Cost). 
        // Positive means GETS (Creditor), Negative means OWES (Debtor).
        $balance['balance'] = $balance['total_bazar'] - $expectedCost;
    }
    // Unset the reference to avoid unexpected side effects
    unset($balance);

    return $balances;
}

function calculateOverview($balances)
{
    $totalBazar = array_sum(array_column($balances, 'total_bazar'));
    $totalMeals = array_sum(array_column($balances, 'total_meals'));
    $avgMealCost = $totalMeals > 0 ? $totalBazar / $totalMeals : 0;
    $totalMembers = count($balances);

    return [
        'total_bazar' => $totalBazar,
        'total_meals' => $totalMeals,
        'avg_meal_cost' => $avgMealCost,
        'total_members' => $totalMembers
    ];
}

function calculateSettlementTransactions($balances)
{
    // Separate members who owe money (Negative balance) and who should receive money (Positive balance)
    $debtors = [];
    $creditors = [];

    foreach ($balances as $balance) {
        $amount = floatval($balance['balance']);
        if ($amount < 0) {
            $debtors[] = [
                'member_id' => $balance['member_id'],
                'name' => $balance['name'],
                'amount' => abs($amount) // Amount owed
            ];
        } elseif ($amount > 0) {
            $creditors[] = [
                'member_id' => $balance['member_id'],
                'name' => $balance['name'],
                'amount' => $amount // Amount to receive
            ];
        }
    }

    // Sort by amount (descending)
    usort($debtors, function ($a, $b) {
        return $b['amount'] <=> $a['amount'];
    });

    usort($creditors, function ($a, $b) {
        return $b['amount'] <=> $a['amount'];
    });

    // Calculate transactions
    $transactions = [];
    $i = 0; // Debtor pointer
    $j = 0; // Creditor pointer

    while ($i < count($debtors) && $j < count($creditors)) {
        $debtor = &$debtors[$i];
        $creditor = &$creditors[$j];

        $amount = min($debtor['amount'], $creditor['amount']);

        if ($amount <= 0.01) { // Skip negligible amounts
            if ($debtor['amount'] < $creditor['amount']) {
                $i++;
            } else {
                $j++;
            }
            continue;
        }

        $transactions[] = [
            'from' => $debtor['name'],
            'from_id' => $debtor['member_id'],
            'to' => $creditor['name'],
            'to_id' => $creditor['member_id'],
            'amount' => round($amount, 2) // Round to 2 decimal places
        ];

        $debtor['amount'] -= $amount;
        $creditor['amount'] -= $amount;

        if (round($debtor['amount'], 2) <= 0)
            $i++;
        if (round($creditor['amount'], 2) <= 0)
            $j++;
    }

    return $transactions;
}

function processSettlement()
{
    global $pdo;

    // Validate input
    $month = intval($_POST['month'] ?? 0);
    $year = intval($_POST['year'] ?? 0);
    $notes = $_POST['notes'] ?? '';

    if ($month < 1 || $month > 12 || $year < 2020 || $year > 2030) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid month or year']);
        return;
    }

    // Check if settlement already exists
    $stmt = $pdo->prepare("SELECT id FROM settlements WHERE month = ? AND year = ? AND status = 'completed'");
    $stmt->execute([$month, $year]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Settlement already completed for this month']);
        return;
    }

    // Begin transaction
    $pdo->beginTransaction();

    try {
        // Get current settlement data
        $members = getActiveMembers();
        $balances = calculateMemberBalances($members, $month, $year);
        $overview = calculateOverview($balances);
        $transactions = calculateSettlementTransactions($balances);

        // Create settlement record
        $stmt = $pdo->prepare("
            INSERT INTO settlements (month, year, total_bazar, total_meals, avg_meal_cost, status, processed_by, notes) 
            VALUES (?, ?, ?, ?, ?, 'completed', ?, ?)
        ");

        $stmt->execute([
            $month,
            $year,
            $overview['total_bazar'],
            $overview['total_meals'],
            $overview['avg_meal_cost'],
            $_SESSION['user_id'],
            $notes
        ]);

        $settlementId = $pdo->lastInsertId();

        // Record transactions
        $stmt = $pdo->prepare("
            INSERT INTO settlement_transactions 
            (settlement_id, from_member_id, to_member_id, amount, status) 
            VALUES (?, ?, ?, ?, 'completed')
        ");

        foreach ($transactions as $transaction) {
            $stmt->execute([
                $settlementId,
                $transaction['from_id'],
                $transaction['to_id'],
                $transaction['amount']
            ]);
        }

        // Commit transaction
        $pdo->commit();

        // Log activity
        logActivity("Settlement processed for " . date('F Y', mktime(0, 0, 0, $month, 1, $year)), $_SESSION['user_id']);

        echo json_encode([
            'success' => true,
            'message' => 'Settlement processed successfully',
            'settlement_id' => $settlementId,
            'transaction_count' => count($transactions)
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function getRecentSettlements()
{
    global $pdo;

    $limit = intval($_GET['limit'] ?? 5);

    $stmt = $pdo->prepare("
        SELECT s.id, s.month, s.year, s.total_bazar, s.total_meals, 
               s.avg_meal_cost, s.processed_at, u.name as processed_by
        FROM settlements s
        LEFT JOIN users u ON s.processed_by = u.id
        WHERE s.status = 'completed'
        ORDER BY s.processed_at DESC
        LIMIT ?
    ");

    $stmt->execute([$limit]);
    $settlements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'settlements' => $settlements
    ]);
}

function exportSettlementReport($month, $year)
{
    // The month and year are passed as arguments now
    if ($month < 1 || $month > 12 || $year < 2020 || $year > 2030) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid month or year']);
        return;
    }

    // In a real implementation, you would generate a PDF report here
    // For now, we'll just return a success message with a dummy URL

    $filename = "settlement_report_{$month}_{$year}_" . time() . ".pdf";

    echo json_encode([
        'success' => true,
        'message' => 'Report generated successfully',
        'download_url' => 'reports/' . $filename,
        'filename' => $filename
    ]);
}

function logActivity($activity, $userId)
{
    global $pdo;

    // FIX: Added the function definition that was missing/incomplete in the original prompt
    $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, activity, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$userId, $activity]);
}
?>