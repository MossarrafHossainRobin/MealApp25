<?php
// Note: You must ensure 'config/database.php' exists and provides a PDO connection.
require_once '../config/database.php';

// Initialize Database
$db = new Database();
$connection = $db->getConnection();

// Helper to send JSON and exit
function json_exit($arr, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($arr);
    exit;
}

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With'); // Added common headers

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Determine input source (JSON body or POST data)
    $input = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $contentType = trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0]);
        if ($contentType === 'application/json') {
            $input = json_decode(file_get_contents('php://input'), true);
        } else {
            $input = $_POST;
        }
    }

    $action = $input['action'] ?? '';

    switch ($action) {
        case 'add_member':
            $name = trim($input['name'] ?? '');
            $email = trim($input['email'] ?? '');

            if (!$name) {
                json_exit(['success' => false, 'error' => 'Member name is required']);
            }

            // Check if member already exists (simple check by name for demonstration)
            $check_stmt = $connection->prepare("SELECT id FROM members WHERE name = ?");
            $check_stmt->execute([$name]);
            $existing = $check_stmt->fetch();

            if ($existing) {
                json_exit(['success' => false, 'error' => 'Member with this name already exists']);
            }

            try {
                // Insert new member
                $stmt = $connection->prepare("INSERT INTO members (name, email, is_active, created_at) VALUES (?, ?, 1, NOW())");
                $stmt->execute([$name, $email]);
                $memberId = $connection->lastInsertId();

                // Get created member data (optional, but good practice)
                $stmt = $connection->prepare("SELECT * FROM members WHERE id = ?");
                $stmt->execute([$memberId]);
                $memberData = $stmt->fetch(PDO::FETCH_ASSOC);

                json_exit([
                    'success' => true,
                    'message' => 'Member added successfully!',
                    'data' => $memberData
                ]);

            } catch (Exception $e) {
                json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'update_member':
            $memberId = intval($input['id'] ?? 0);
            $name = trim($input['name'] ?? '');
            $email = trim($input['email'] ?? '');
            $is_active = intval($input['is_active'] ?? 1);

            if (!$memberId || !$name) {
                json_exit(['success' => false, 'error' => 'Member ID and name are required']);
            }

            // Check if another member has the same name
            $check_stmt = $connection->prepare("SELECT id FROM members WHERE name = ? AND id != ?");
            $check_stmt->execute([$name, $memberId]);
            $existing = $check_stmt->fetch();

            if ($existing) {
                json_exit(['success' => false, 'error' => 'Another member with this name already exists']);
            }

            try {
                $stmt = $connection->prepare("UPDATE members SET name = ?, email = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $email, $is_active, $memberId]);

                if ($stmt->rowCount() > 0) {
                    // Get updated member data (optional)
                    $stmt = $connection->prepare("SELECT id, name, email, is_active, created_at FROM members WHERE id = ?");
                    $stmt->execute([$memberId]);
                    $memberData = $stmt->fetch(PDO::FETCH_ASSOC);

                    json_exit([
                        'success' => true,
                        'message' => 'Member updated successfully!',
                        'data' => $memberData
                    ]);
                } else {
                    json_exit(['success' => false, 'error' => 'Member not found or no changes made']);
                }

            } catch (Exception $e) {
                json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'delete_member':
            $memberId = intval($input['id'] ?? 0);

            if (!$memberId) {
                json_exit(['success' => false, 'error' => 'No member ID provided']);
            }

            try {
                $stmt = $connection->prepare("DELETE FROM members WHERE id = ?");
                $stmt->execute([$memberId]);

                if ($stmt->rowCount() > 0) {
                    json_exit(['success' => true, 'message' => 'Member deleted successfully!']);
                } else {
                    json_exit(['success' => false, 'error' => 'Member not found']);
                }

            } catch (Exception $e) {
                json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'toggle_member_status':
            $memberId = intval($input['id'] ?? 0);

            if (!$memberId) {
                json_exit(['success' => false, 'error' => 'No member ID provided']);
            }

            try {
                // Get current status
                $stmt = $connection->prepare("SELECT is_active FROM members WHERE id = ?");
                $stmt->execute([$memberId]);
                $member = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$member) {
                    json_exit(['success' => false, 'error' => 'Member not found']);
                }

                $newStatus = $member['is_active'] ? 0 : 1;
                $action = $newStatus ? 'activated' : 'suspended';

                $stmt = $connection->prepare("UPDATE members SET is_active = ? WHERE id = ?");
                $stmt->execute([$newStatus, $memberId]);

                json_exit([
                    'success' => true,
                    'message' => "Member {$action} successfully!",
                    'data' => ['is_active' => $newStatus]
                ]);

            } catch (Exception $e) {
                json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        default:
            // This is for POST only, GET handled below
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                json_exit(['success' => false, 'error' => 'Invalid POST action']);
            }
            break;
    }

    // Handle GET requests
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'get_members':
                try {
                    $search = $_GET['search'] ?? '';
                    // Convert 'status' to an explicit integer or keep as empty string if not set
                    $status = isset($_GET['status']) && $_GET['status'] !== '' ? intval($_GET['status']) : '';

                    $whereConditions = [];
                    $params = [];

                    if ($search) {
                        $whereConditions[] = "(name LIKE ? OR email LIKE ?)";
                        $searchTerm = "%{$search}%";
                        $params[] = $searchTerm;
                        $params[] = $searchTerm;
                    }

                    if ($status !== '') {
                        $whereConditions[] = "is_active = ?";
                        $params[] = $status;
                    }

                    $whereClause = $whereConditions ? "WHERE " . implode(" AND ", $whereConditions) : "";

                    $stmt = $connection->prepare("
                        SELECT id, name, email, is_active, created_at 
                        FROM members 
                        {$whereClause}
                        ORDER BY created_at DESC
                    ");
                    $stmt->execute($params);
                    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Convert is_active from DB integer (0/1) to boolean for JavaScript consumption
                    foreach ($members as &$member) {
                        $member['is_active'] = (bool) $member['is_active'];
                    }
                    unset($member); // Unset reference

                    json_exit([
                        'success' => true,
                        'data' => $members,
                        'count' => count($members)
                    ]);
                } catch (Exception $e) {
                    json_exit(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
                }
                break;
            default:
                json_exit(['success' => false, 'error' => 'Invalid GET action']);
                break;
        }
    }


} catch (Exception $e) {
    json_exit(['success' => false, 'error' => 'A critical error occurred: ' . $e->getMessage()], 500);
}
// End of PHP file