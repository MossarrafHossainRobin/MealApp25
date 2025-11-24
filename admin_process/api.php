<?php
// admin_process/api.php
require_once '../config/auth.php';
require_once '../config/database.php';

$auth = new Auth();
$auth->checkSession();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    $db = new Database();
    $connection = $db->getConnection();

    try {
        switch ($_POST['ajax_action']) {
            case 'get_dashboard_data':
                $selected_month = $_POST['month'] ?? date('Y-m');

                $total_members = $connection->query("SELECT COUNT(*) as total FROM members WHERE is_active = 1")->fetch(PDO::FETCH_ASSOC)['total'];

                // Use selected month instead of current month
                $current_month = $selected_month;

                // Get selected month bazar data
                $bazar_stmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) as total, COUNT(*) as entries FROM bazar WHERE DATE_FORMAT(bazar_date, '%Y-%m') = ?");
                $bazar_stmt->execute([$current_month]);
                $bazar_data = $bazar_stmt->fetch(PDO::FETCH_ASSOC);
                $total_bazar = $bazar_data['total'];
                $total_bazar_entries = $bazar_data['entries'];

                // Get selected month meals data
                $meal_stmt = $connection->prepare("SELECT COALESCE(SUM(meal_count), 0) as total FROM meal_counts WHERE DATE_FORMAT(meal_date, '%Y-%m') = ?");
                $meal_stmt->execute([$current_month]);
                $total_meals = $meal_stmt->fetch(PDO::FETCH_ASSOC)['total'];

                $meal_rate = $total_meals > 0 ? $total_bazar / $total_meals : 0;

                // Get selected month flat cost
                $flat_cost_stmt = $connection->prepare("SELECT total_amount, per_member_cost FROM monthly_flat_costs WHERE month_year = ?");
                $flat_cost_stmt->execute([$current_month]);
                $flat_cost_data = $flat_cost_stmt->fetch(PDO::FETCH_ASSOC);
                $monthly_flat_cost = $flat_cost_data ? $flat_cost_data['total_amount'] : 0;
                $per_member_cost = $flat_cost_data ? $flat_cost_data['per_member_cost'] : 0;

                // Get pending water duties
                $water_duties_stmt = $connection->query("SELECT COUNT(*) as pending FROM water_duties WHERE status = 'pending' AND duty_date >= CURDATE()");
                $pending_water_duties = $water_duties_stmt->fetch(PDO::FETCH_ASSOC)['pending'];

                // Calculate average meals per day for selected month
                $days_in_month = date('t', strtotime($current_month . '-01'));
                $avg_meals_per_day = $days_in_month > 0 ? $total_meals / $days_in_month : 0;

                // Get monthly trends for charts (last 6 months)
                $monthly_trends = [];
                for ($i = 0; $i < 6; $i++) {
                    $month = date('Y-m', strtotime("-$i months", strtotime($current_month . '-01')));
                    $month_name = date('M Y', strtotime($month . '-01'));

                    // Bazar for this month
                    $month_bazar_stmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM bazar WHERE DATE_FORMAT(bazar_date, '%Y-%m') = ?");
                    $month_bazar_stmt->execute([$month]);
                    $month_bazar = $month_bazar_stmt->fetch(PDO::FETCH_ASSOC)['total'];

                    // Meals for this month
                    $month_meal_stmt = $connection->prepare("SELECT COALESCE(SUM(meal_count), 0) as total FROM meal_counts WHERE DATE_FORMAT(meal_date, '%Y-%m') = ?");
                    $month_meal_stmt->execute([$month]);
                    $month_meals = $month_meal_stmt->fetch(PDO::FETCH_ASSOC)['total'];

                    // Flat cost for this month
                    $month_flat_stmt = $connection->prepare("SELECT total_amount FROM monthly_flat_costs WHERE month_year = ?");
                    $month_flat_stmt->execute([$month]);
                    $month_flat = $month_flat_stmt->fetch(PDO::FETCH_ASSOC);
                    $month_flat_cost = $month_flat ? $month_flat['total_amount'] : 0;

                    $monthly_trends[] = [
                        'month' => $month_name,
                        'bazar' => $month_bazar,
                        'meals' => $month_meals,
                        'flat_cost' => $month_flat_cost
                    ];
                }

                // Reverse to show oldest first
                $monthly_trends = array_reverse($monthly_trends);

                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'total_members' => $total_members,
                        'total_bazar' => $total_bazar,
                        'total_meals' => $total_meals,
                        'meal_rate' => $meal_rate,
                        'monthly_flat_cost' => $monthly_flat_cost,
                        'per_member_cost' => $per_member_cost,
                        'pending_water_duties' => $pending_water_duties,
                        'total_bazar_entries' => $total_bazar_entries,
                        'avg_meals_per_day' => $avg_meals_per_day,
                        'monthly_trends' => $monthly_trends,
                        'current_month' => date('F Y', strtotime($current_month . '-01')),
                        'selected_month' => $current_month
                    ]
                ]);
                break;

            case 'get_recent_activities':
                // Get recent bazar entries
                $bazar_activities = $connection->query("
                    SELECT 'bazar' as type, b.bazar_date as date, m.name as member_name, b.description, b.amount 
                    FROM bazar b 
                    LEFT JOIN members m ON b.member_id = m.id 
                    ORDER BY b.created_at DESC 
                    LIMIT 10
                ")->fetchAll(PDO::FETCH_ASSOC);

                // Get recent meal counts
                $meal_activities = $connection->query("
                    SELECT 'meal' as type, mc.meal_date as date, m.name as member_name, CONCAT('Meal count: ', mc.meal_count) as description, NULL as amount 
                    FROM meal_counts mc 
                    LEFT JOIN members m ON mc.member_id = m.id 
                    ORDER BY mc.created_at DESC 
                    LIMIT 10
                ")->fetchAll(PDO::FETCH_ASSOC);

                // Get recent member additions
                $member_activities = $connection->query("
                    SELECT 'member' as type, created_at as date, name as member_name, 'New member added' as description, NULL as amount 
                    FROM members 
                    ORDER BY created_at DESC 
                    LIMIT 5
                ")->fetchAll(PDO::FETCH_ASSOC);

                // Get recent flat cost entries
                $flat_activities = $connection->query("
                    SELECT 'flat' as type, created_at as date, 'System' as member_name, CONCAT('Monthly cost: ', month_year) as description, total_amount as amount 
                    FROM monthly_flat_costs
                    ORDER BY created_at DESC 
                    LIMIT 5
                ")->fetchAll(PDO::FETCH_ASSOC);

                // Combine all activities and sort by date
                $all_activities = array_merge($bazar_activities, $meal_activities, $member_activities, $flat_activities);
                usort($all_activities, function ($a, $b) {
                    return strtotime($b['date']) - strtotime($a['date']);
                });

                // Take only the 10 most recent
                $recent_activities = array_slice($all_activities, 0, 10);

                echo json_encode(['status' => 'success', 'data' => $recent_activities]);
                break;

            case 'get_members':
                $stmt = $connection->query("SELECT * FROM members ORDER BY created_at DESC");
                $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $members]);
                break;

            case 'add_member':
                $name = htmlspecialchars($_POST['name']);
                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                $phone = htmlspecialchars($_POST['phone'] ?? '');
                $base_rent = floatval($_POST['base_rent'] ?? 0);

                $stmt = $connection->prepare("INSERT INTO members (name, email, phone, base_rent) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $phone, $base_rent]);

                echo json_encode(['status' => 'success', 'message' => 'Member added successfully']);
                break;

            case 'delete_member':
                $id = (int) $_POST['id'];
                $stmt = $connection->prepare("DELETE FROM members WHERE id = ?");
                $stmt->execute([$id]);

                echo json_encode(['status' => 'success', 'message' => 'Member deleted successfully']);
                break;

            case 'get_bazar':
                $stmt = $connection->query("
                    SELECT b.*, m.name as member_name 
                    FROM bazar b 
                    LEFT JOIN members m ON b.member_id = m.id 
                    ORDER BY b.bazar_date DESC 
                    LIMIT 50
                ");
                $bazar = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $bazar]);
                break;

            case 'add_bazar':
                $member_id = (int) $_POST['member_id'];
                $amount = (float) $_POST['amount'];
                $description = htmlspecialchars($_POST['description'] ?? '');
                $bazar_date = $_POST['bazar_date'];
                $year = date('Y', strtotime($bazar_date));
                $month = date('m', strtotime($bazar_date));

                $stmt = $connection->prepare("INSERT INTO bazar (member_id, amount, description, bazar_date, year, month) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$member_id, $amount, $description, $bazar_date, $year, $month]);

                echo json_encode(['status' => 'success', 'message' => 'Bazar entry added successfully']);
                break;

            case 'delete_bazar':
                $id = (int) $_POST['id'];
                $stmt = $connection->prepare("DELETE FROM bazar WHERE id = ?");
                $stmt->execute([$id]);

                echo json_encode(['status' => 'success', 'message' => 'Bazar entry deleted successfully']);
                break;

            case 'get_meals':
                $stmt = $connection->query("
                    SELECT mc.*, m.name as member_name 
                    FROM meal_counts mc 
                    LEFT JOIN members m ON mc.member_id = m.id 
                    ORDER BY mc.meal_date DESC 
                    LIMIT 50
                ");
                $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $meals]);
                break;

            case 'add_meal':
                $member_id = (int) $_POST['member_id'];
                $meal_count = (float) $_POST['meal_count'];
                $meal_date = $_POST['meal_date'];
                $description = htmlspecialchars($_POST['description'] ?? '');

                // Get member name
                $member_stmt = $connection->prepare("SELECT name FROM members WHERE id = ?");
                $member_stmt->execute([$member_id]);
                $member = $member_stmt->fetch(PDO::FETCH_ASSOC);
                $member_name = $member ? $member['name'] : '';

                $stmt = $connection->prepare("INSERT INTO meal_counts (member_id, member_name, meal_count, meal_date, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$member_id, $member_name, $meal_count, $meal_date, $description]);

                echo json_encode(['status' => 'success', 'message' => 'Meal count added successfully']);
                break;

            case 'delete_meal':
                $id = (int) $_POST['id'];
                $stmt = $connection->prepare("DELETE FROM meal_counts WHERE id = ?");
                $stmt->execute([$id]);

                echo json_encode(['status' => 'success', 'message' => 'Meal count deleted successfully']);
                break;

            case 'get_water_duties':
                $stmt = $connection->query("
                    SELECT wd.*, m.name as member_name 
                    FROM water_duties wd 
                    LEFT JOIN members m ON wd.member_id = m.id 
                    ORDER BY wd.duty_date DESC 
                    LIMIT 50
                ");
                $duties = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $duties]);
                break;

            case 'add_water_duty':
                $member_id = (int) $_POST['member_id'];
                $duty_date = $_POST['duty_date'];
                $duty_time = $_POST['duty_time'] ?? '09:00';

                $stmt = $connection->prepare("INSERT INTO water_duties (member_id, duty_date, duty_time, status, assigned_at) VALUES (?, ?, ?, 'pending', NOW())");
                $stmt->execute([$member_id, $duty_date, $duty_time]);

                echo json_encode(['status' => 'success', 'message' => 'Water duty assigned successfully']);
                break;

            case 'delete_water_duty':
                $id = (int) $_POST['id'];
                $stmt = $connection->prepare("DELETE FROM water_duties WHERE id = ?");
                $stmt->execute([$id]);

                echo json_encode(['status' => 'success', 'message' => 'Water duty deleted successfully']);
                break;

            case 'update_water_duty_status':
                $id = (int) $_POST['id'];
                $status = $_POST['status'];
                $completed_at = $status === 'completed' ? date('Y-m-d H:i:s') : null;

                $stmt = $connection->prepare("UPDATE water_duties SET status = ?, completed_at = ? WHERE id = ?");
                $stmt->execute([$status, $completed_at, $id]);

                echo json_encode(['status' => 'success', 'message' => 'Water duty status updated successfully']);
                break;

            case 'calculate_settlement':
                $month = $_POST['month'] ?? date('Y-m');

                // Get total bazar for the month
                $bazar_stmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM bazar WHERE DATE_FORMAT(bazar_date, '%Y-%m') = ?");
                $bazar_stmt->execute([$month]);
                $total_bazar = $bazar_stmt->fetch(PDO::FETCH_ASSOC)['total'];

                // Get total meals for the month
                $meal_stmt = $connection->prepare("SELECT COALESCE(SUM(meal_count), 0) as total FROM meal_counts WHERE DATE_FORMAT(meal_date, '%Y-%m') = ?");
                $meal_stmt->execute([$month]);
                $total_meals = $meal_stmt->fetch(PDO::FETCH_ASSOC)['total'];

                // Calculate meal rate
                $meal_rate = $total_meals > 0 ? $total_bazar / $total_meals : 0;

                // Get member-wise settlement
                $members_stmt = $connection->query("SELECT id, name FROM members WHERE is_active = 1");
                $members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);

                $settlement_data = [];
                foreach ($members as $member) {
                    // Get member's total meals
                    $member_meals_stmt = $connection->prepare("SELECT COALESCE(SUM(meal_count), 0) as total FROM meal_counts WHERE member_id = ? AND DATE_FORMAT(meal_date, '%Y-%m') = ?");
                    $member_meals_stmt->execute([$member['id'], $month]);
                    $member_meals = $member_meals_stmt->fetch(PDO::FETCH_ASSOC)['total'];

                    // Get member's total bazar
                    $member_bazar_stmt = $connection->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM bazar WHERE member_id = ? AND DATE_FORMAT(bazar_date, '%Y-%m') = ?");
                    $member_bazar_stmt->execute([$member['id'], $month]);
                    $member_bazar = $member_bazar_stmt->fetch(PDO::FETCH_ASSOC)['total'];

                    // Calculate member's meal cost and balance
                    $member_meal_cost = $member_meals * $meal_rate;
                    $balance = $member_bazar - $member_meal_cost;

                    $settlement_data[] = [
                        'member_id' => $member['id'],
                        'member_name' => $member['name'],
                        'total_meals' => $member_meals,
                        'total_bazar' => $member_bazar,
                        'meal_cost' => $member_meal_cost,
                        'balance' => $balance
                    ];
                }

                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'settlement' => $settlement_data,
                        'total_bazar' => $total_bazar,
                        'total_meals' => $total_meals,
                        'meal_rate' => $meal_rate
                    ]
                ]);
                break;

            case 'get_monthly_costs':
                $stmt = $connection->query("SELECT * FROM monthly_flat_costs ORDER BY month_year DESC");
                $costs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $costs]);
                break;

            case 'add_monthly_cost':
                $month_year = $_POST['month_year'];
                $house_rent = floatval($_POST['house_rent']);
                $electricity_bill = floatval($_POST['electricity_bill']);
                $gas_bill = floatval($_POST['gas_bill']);
                $internet_bill = floatval($_POST['internet_bill']);
                $aunty_bill = floatval($_POST['aunty_bill']);
                $dust_bill = floatval($_POST['dust_bill']);
                $other_bills = floatval($_POST['other_bills']);

                $total_amount = $house_rent + $electricity_bill + $gas_bill + $internet_bill + $aunty_bill + $dust_bill + $other_bills;

                // Get active members count
                $members_stmt = $connection->query("SELECT COUNT(*) as total FROM members WHERE is_active = 1");
                $active_members = $members_stmt->fetch(PDO::FETCH_ASSOC)['total'];
                $per_member_cost = $active_members > 0 ? $total_amount / $active_members : 0;

                $stmt = $connection->prepare("INSERT INTO monthly_flat_costs (month_year, house_rent, electricity_bill, gas_bill, internet_bill, aunty_bill, dust_bill, other_bills, total_amount, per_member_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$month_year, $house_rent, $electricity_bill, $gas_bill, $internet_bill, $aunty_bill, $dust_bill, $other_bills, $total_amount, $per_member_cost]);

                $monthly_cost_id = $connection->lastInsertId();

                // Create allocations for all active members
                $members_stmt = $connection->query("SELECT id, base_rent FROM members WHERE is_active = 1");
                $members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($members as $member) {
                    $utility_share = $per_member_cost - $member['base_rent'];
                    $amount_due = $per_member_cost;

                    $alloc_stmt = $connection->prepare("INSERT INTO member_flat_cost_allocations (monthly_cost_id, member_id, amount_due, rent_amount, utility_share, special_adjustment, status) VALUES (?, ?, ?, ?, ?, 0, 'pending')");
                    $alloc_stmt->execute([$monthly_cost_id, $member['id'], $amount_due, $member['base_rent'], $utility_share]);
                }

                echo json_encode(['status' => 'success', 'message' => 'Monthly cost added and allocations created successfully']);
                break;

            case 'get_allocations':
                $monthly_cost_id = (int) $_POST['monthly_cost_id'];

                $stmt = $connection->prepare("
                    SELECT mfca.*, m.name as member_name 
                    FROM member_flat_cost_allocations mfca 
                    JOIN members m ON mfca.member_id = m.id 
                    WHERE mfca.monthly_cost_id = ?
                ");
                $stmt->execute([$monthly_cost_id]);
                $allocations = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode(['status' => 'success', 'data' => $allocations]);
                break;

            case 'update_allocation':
                $id = (int) $_POST['id'];
                $rent_amount = floatval($_POST['rent_amount']);
                $utility_share = floatval($_POST['utility_share']);
                $special_adjustment = floatval($_POST['special_adjustment']);
                $note = htmlspecialchars($_POST['note'] ?? '');

                $amount_due = $rent_amount + $utility_share + $special_adjustment;

                $stmt = $connection->prepare("UPDATE member_flat_cost_allocations SET rent_amount = ?, utility_share = ?, special_adjustment = ?, amount_due = ?, note = ? WHERE id = ?");
                $stmt->execute([$rent_amount, $utility_share, $special_adjustment, $amount_due, $note, $id]);

                echo json_encode(['status' => 'success', 'message' => 'Allocation updated successfully']);
                break;

            case 'update_allocation_status':
                $id = (int) $_POST['id'];
                $status = $_POST['status'];

                $stmt = $connection->prepare("UPDATE member_flat_cost_allocations SET status = ? WHERE id = ?");
                $stmt->execute([$status, $id]);

                echo json_encode(['status' => 'success', 'message' => 'Status updated successfully']);
                break;

            case 'delete_monthly_cost':
                $id = (int) $_POST['id'];

                // Delete allocations first
                $alloc_stmt = $connection->prepare("DELETE FROM member_flat_cost_allocations WHERE monthly_cost_id = ?");
                $alloc_stmt->execute([$id]);

                // Then delete monthly cost
                $stmt = $connection->prepare("DELETE FROM monthly_flat_costs WHERE id = ?");
                $stmt->execute([$id]);

                echo json_encode(['status' => 'success', 'message' => 'Monthly cost deleted successfully']);
                break;
            // Add after the existing cases

            case 'suspend_member':
                $id = (int) $_POST['id'];
                $is_suspended = (bool) $_POST['is_suspended'];

                $stmt = $connection->prepare("UPDATE members SET is_suspended = ? WHERE id = ?");
                $stmt->execute([$is_suspended, $id]);

                $action = $is_suspended ? 'suspended' : 'activated';
                echo json_encode(['status' => 'success', 'message' => "Member {$action} successfully"]);
                break;

            case 'get_bazar_requests':
                $stmt = $connection->query("
        SELECT br.*, m.name as member_name 
        FROM bazar_requests br 
        LEFT JOIN members m ON br.member_id = m.id 
        ORDER BY br.created_at DESC 
        LIMIT 50
    ");
                $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $requests]);
                break;

            case 'add_bazar_request':
                $member_id = (int) $_POST['member_id'];
                $amount = (float) $_POST['amount'];
                $description = htmlspecialchars($_POST['description'] ?? '');
                $bazar_date = $_POST['bazar_date'];

                $stmt = $connection->prepare("INSERT INTO bazar_requests (member_id, amount, description, bazar_date, status) VALUES (?, ?, ?, ?, 'pending')");
                $stmt->execute([$member_id, $amount, $description, $bazar_date]);

                echo json_encode(['status' => 'success', 'message' => 'Bazar request submitted for approval']);
                break;

            case 'approve_bazar_request':
                $id = (int) $_POST['id'];

                // Get request details
                $request_stmt = $connection->prepare("SELECT * FROM bazar_requests WHERE id = ?");
                $request_stmt->execute([$id]);
                $request = $request_stmt->fetch(PDO::FETCH_ASSOC);

                if (!$request) {
                    echo json_encode(['status' => 'error', 'message' => 'Request not found']);
                    break;
                }

                if ($request['status'] !== 'pending') {
                    echo json_encode(['status' => 'error', 'message' => 'Request already processed']);
                    break;
                }

                // Set default bazar_count to 1 since it's required but not provided by user
                $bazar_count = 1;

                // Start transaction to ensure both operations complete
                $connection->beginTransaction();

                try {
                    // Add to main bazar table - REMOVE year and month columns as they are generated
                    $bazar_stmt = $connection->prepare("
            INSERT INTO bazar (member_id, amount, bazar_count, description, bazar_date, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
                    $bazar_stmt->execute([
                        $request['member_id'],
                        $request['amount'],
                        $bazar_count,
                        $request['description'],
                        $request['bazar_date']
                    ]);

                    // Update request status to approved
                    $update_stmt = $connection->prepare("UPDATE bazar_requests SET status = 'approved', updated_at = NOW() WHERE id = ?");
                    $update_stmt->execute([$id]);

                    // Commit transaction
                    $connection->commit();

                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Bazar request approved and added to records successfully'
                    ]);

                } catch (Exception $e) {
                    // Rollback transaction on error
                    $connection->rollBack();
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to process bazar request: ' . $e->getMessage()
                    ]);
                }
                break;
            case 'reject_bazar_request':
                $id = (int) $_POST['id'];
                $reason = htmlspecialchars($_POST['reason'] ?? '');

                try {
                    // Get current request to check status
                    $request_stmt = $connection->prepare("SELECT status, description FROM bazar_requests WHERE id = ?");
                    $request_stmt->execute([$id]);
                    $request = $request_stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$request) {
                        echo json_encode(['status' => 'error', 'message' => 'Request not found']);
                        break;
                    }

                    if ($request['status'] !== 'pending') {
                        echo json_encode(['status' => 'error', 'message' => 'Request already processed']);
                        break;
                    }

                    // Update the status to 'rejected' and store rejection reason separately
                    $stmt = $connection->prepare("
            UPDATE bazar_requests 
            SET status = 'rejected', 
                rejection_reason = ?,
                updated_at = NOW() 
            WHERE id = ?
        ");
                    $stmt->execute([$reason, $id]);

                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Bazar request rejected successfully'
                    ]);

                } catch (Exception $e) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to reject bazar request: ' . $e->getMessage()
                    ]);
                }
                break;

            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>