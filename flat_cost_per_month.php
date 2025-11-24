<?php
// flat_cost.php - Complete Solution with Email
session_start();

// ==========================================================================
// 1. CONFIGURATION & LIBRARIES
// ==========================================================================

// A. LOAD PHPMAILER
// Update these paths if your PHPMailer folder is named differently
// If you used Composer, use: require 'vendor/autoload.php';
if (file_exists('PHPMailer/src/PHPMailer.php')) {
    require_once 'PHPMailer/src/Exception.php';
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
} else {
    // Fallback or error handling if paths are wrong
    // die('PHPMailer not found. Please check the folder path.');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// B. DATABASE CONNECTION
require_once 'config/database.php';

// Helper function for JSON response
function sendJson($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// ==========================================================================
// 2. BACKEND LOGIC (AJAX HANDLERS)
// ==========================================================================

// --- HANDLER: SEND EMAIL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_mail') {
    $recipientEmail = $_POST['email'] ?? '';
    $recipientName = $_POST['name'] ?? 'Member';
    $amount = $_POST['amount'] ?? '0';
    $monthRaw = $_POST['month'] ?? date('Y-m');
    $monthName = date('F Y', strtotime($monthRaw)); // e.g. "November 2025"

    if (empty($recipientEmail)) {
        sendJson(['status' => 'error', 'message' => 'Member does not have an email address saved.']);
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'meal.query@gmail.com';
        $mail->Password = 'ogjngrxxihxxtree';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('meal.query@gmail.com', 'Meal App25 Water Duty');
        $mail->addAddress($recipientEmail, $recipientName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Monthly Cost Update - $monthName";

        $bodyContent = "
        <div style='font-family: Arial, sans-serif; color: #333;'>
            <h3>Hello $recipientName,</h3>
            <p>Your month cost for this <strong>$monthName</strong> is <strong style='color: #d9534f; font-size: 1.2em;'>$amount</strong>.</p>
            <p>Please clear your dues soon.</p>
            <br>
            <p style='color: #777; font-size: 0.9em;'>Regards,<br>Meal App Manager</p>
        </div>";

        $mail->Body = $bodyContent;
        $mail->AltBody = strip_tags($bodyContent);

        $mail->send();
        sendJson(['status' => 'success', 'message' => "Email sent to $recipientName!"]);

    } catch (Exception $e) {
        sendJson(['status' => 'error', 'message' => "Mailer Error: {$mail->ErrorInfo}"]);
    }
}

// --- HANDLER: FETCH DATA ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch_data') {
    $month = $_GET['month'] ?? date('Y-m');

    try {
        // A. Get Master Bill Data
        $master_stmt = $conn->prepare("SELECT * FROM monthly_flat_costs WHERE month_year = ?");
        $master_stmt->execute([$month]);
        $master = $master_stmt->fetch(PDO::FETCH_ASSOC);

        // Defaults if new month
        if (!$master) {
            $master = [
                'id' => null,
                'month_year' => $month,
                'house_rent' => 0,
                'electricity_bill' => 1800,
                'gas_bill' => 0,
                'aunty_bill' => 5000,
                'dust_bill' => 200,
                'internet_bill' => 0,
                'other_bills' => 0,
                'total_amount' => 0
            ];
        }

        // B. Get Members & Allocations (Including Email)
        $sql = "
            SELECT 
                m.id as member_id, 
                m.name, 
                m.email, 
                m.base_rent as default_rent,
                alloc.id as alloc_id,
                COALESCE(alloc.rent_amount, m.base_rent) as rent_amount,
                COALESCE(alloc.utility_share, 0) as utility_share,
                COALESCE(alloc.special_adjustment, 0) as special_adjustment,
                COALESCE(alloc.amount_due, 0) as total_due,
                COALESCE(alloc.status, 'unpaid') as status,
                alloc.note
            FROM members m
            LEFT JOIN member_flat_cost_allocations alloc 
                ON m.id = alloc.member_id 
                AND alloc.monthly_cost_id = ?
            WHERE m.is_active = 1
            ORDER BY m.name ASC
        ";

        $monthly_id = $master['id'] ?? 0;
        $stmt = $conn->prepare($sql);
        $stmt->execute([$monthly_id]);
        $members_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendJson(['status' => 'success', 'master' => $master, 'members' => $members_data]);

    } catch (Exception $e) {
        sendJson(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// --- HANDLER: SAVE DATA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_all') {
    try {
        $conn->beginTransaction();
        $month_year = $_POST['month_year'];
        $elec = floatval($_POST['electricity_bill']);
        $gas = floatval($_POST['gas_bill']);
        $aunty = floatval($_POST['aunty_bill']);
        $dust = floatval($_POST['dust_bill']);
        $net = floatval($_POST['internet_bill']);
        $other = floatval($_POST['other_bills']);
        $member_rows = json_decode($_POST['member_data'], true);

        // Check master row
        $check = $conn->prepare("SELECT id FROM monthly_flat_costs WHERE month_year = ?");
        $check->execute([$month_year]);
        $exist = $check->fetch(PDO::FETCH_ASSOC);

        if ($exist) {
            $monthly_id = $exist['id'];
            $upd = $conn->prepare("UPDATE monthly_flat_costs SET electricity_bill=?, gas_bill=?, aunty_bill=?, dust_bill=?, internet_bill=?, other_bills=?, total_amount=0 WHERE id=?");
            $upd->execute([$elec, $gas, $aunty, $dust, $net, $other, $monthly_id]);
        } else {
            $ins = $conn->prepare("INSERT INTO monthly_flat_costs (month_year, electricity_bill, gas_bill, aunty_bill, dust_bill, internet_bill, other_bills, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
            $ins->execute([$month_year, $elec, $gas, $aunty, $dust, $net, $other]);
            $monthly_id = $conn->lastInsertId();
        }

        $total_rent_collected = 0;
        $grand_total = 0;

        foreach ($member_rows as $row) {
            $m_id = $row['member_id'];
            $rent = floatval($row['rent_amount']);
            $share = floatval($row['utility_share']);
            $adj = floatval($row['special_adjustment']);
            $note = $row['note'];
            $status = $row['status'];

            $member_total = $rent + $share + $adj;
            $total_rent_collected += $rent;
            $grand_total += $member_total;

            $check_alloc = $conn->prepare("SELECT id FROM member_flat_cost_allocations WHERE monthly_cost_id = ? AND member_id = ?");
            $check_alloc->execute([$monthly_id, $m_id]);
            $alloc_exist = $check_alloc->fetch();

            if ($alloc_exist) {
                $upd_alloc = $conn->prepare("UPDATE member_flat_cost_allocations SET rent_amount=?, utility_share=?, special_adjustment=?, amount_due=?, status=?, note=? WHERE id=?");
                $upd_alloc->execute([$rent, $share, $adj, $member_total, $status, $note, $alloc_exist['id']]);
            } else {
                $ins_alloc = $conn->prepare("INSERT INTO member_flat_cost_allocations (monthly_cost_id, member_id, rent_amount, utility_share, special_adjustment, amount_due, status, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $ins_alloc->execute([$monthly_id, $m_id, $rent, $share, $adj, $member_total, $status, $note]);
            }
        }

        $final_update = $conn->prepare("UPDATE monthly_flat_costs SET house_rent=?, total_amount=? WHERE id=?");
        $final_update->execute([$total_rent_collected, $grand_total, $monthly_id]);

        $conn->commit();
        sendJson(['status' => 'success', 'message' => 'Costs saved successfully!']);
    } catch (Exception $e) {
        $conn->rollBack();
        sendJson(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// --- HANDLER: TOGGLE STATUS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $alloc_id = $_POST['alloc_id'];
    $new_status = $_POST['status'];
    if ($alloc_id > 0) {
        $stmt = $conn->prepare("UPDATE member_flat_cost_allocations SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $alloc_id]);
        sendJson(['status' => 'success']);
    } else {
        sendJson(['status' => 'error', 'message' => 'Invalid ID']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flat Cost Manager - Meal App25</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .header-gradient {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            padding: 2rem 0 4rem;
            margin-bottom: -3rem;
            color: white;
        }

        .form-control-soft {
            background-color: #f8f9fa;
            border: 1px solid #e3e6f0;
            font-weight: 600;
        }

        .form-control-soft:focus {
            background-color: #fff;
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
        }

        .btn-status {
            width: 100%;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            transition: 0.2s;
        }

        .btn-status:hover {
            transform: scale(1.05);
        }

        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
            pointer-events: none;
        }

        .toast {
            pointer-events: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div id="toast-container"></div>

    <div class="header-gradient">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle p-3 me-3 shadow-sm d-flex justify-content-center align-items-center"
                        style="width:50px;height:50px;">
                        <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold">Flat Cost Manager</h3>
                        <p class="mb-0 opacity-75 small">Rent, Utilities & Allocations</p>
                    </div>
                </div>
                <div class="bg-white bg-opacity-25 p-2 rounded-pill d-flex align-items-center backdrop-blur">
                    <input type="month" id="cost_month" class="form-control border-0 bg-transparent text-white fw-bold"
                        style="width: auto; color-scheme: dark;" value="<?php echo date('Y-m'); ?>">
                    <button class="btn btn-light rounded-pill ms-2 text-primary fw-bold" onclick="loadFlatData()">
                        <i class="fas fa-sync-alt me-1"></i> Load
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5" style="margin-top: 1rem;">
        <form id="flatCostForm">
            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="card h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-plug me-2"></i>Monthly Utilities</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">👩‍🍳 Aunty Bill</label>
                                <div class="input-group"><span class="input-group-text bg-light border-0">৳</span>
                                    <input type="number" class="form-control form-control-soft utility-input"
                                        id="bill_aunty" value="5000">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">⚡ Current Bill</label>
                                <div class="input-group"><span class="input-group-text bg-light border-0">৳</span>
                                    <input type="number" class="form-control form-control-soft utility-input"
                                        id="bill_electricity" value="1800">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">🧹 Dust Bill</label>
                                <div class="input-group"><span class="input-group-text bg-light border-0">৳</span>
                                    <input type="number" class="form-control form-control-soft utility-input"
                                        id="bill_dust" value="200">
                                </div>
                            </div>
                            <hr class="my-3 opacity-25">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">🔥 Gas Bill</label>
                                <div class="input-group"><span class="input-group-text bg-light border-0">৳</span>
                                    <input type="number" class="form-control form-control-soft utility-input"
                                        id="bill_gas" value="0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">📡 Internet</label>
                                <div class="input-group"><span class="input-group-text bg-light border-0">৳</span>
                                    <input type="number" class="form-control form-control-soft utility-input"
                                        id="bill_net" value="0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">🛠 Other</label>
                                <div class="input-group"><span class="input-group-text bg-light border-0">৳</span>
                                    <input type="number" class="form-control form-control-soft utility-input"
                                        id="bill_other" value="0">
                                </div>
                            </div>
                            <div class="alert alert-primary border-0 bg-primary bg-opacity-10 small mb-0">
                                <div class="d-flex justify-content-between fw-bold mb-1"><span>Total
                                        Utility:</span><span id="display_total_utility">৳ 0</span></div>
                                <div class="d-flex justify-content-between text-muted"><span>Per Member:</span><span
                                        id="display_per_head">৳ 0</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="card h-100">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 fw-bold text-secondary">Cost Distribution Table</h6>
                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i>Save All
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small text-uppercase text-muted">
                                        <tr>
                                            <th class="ps-4">Member / Email</th>
                                            <th width="140">Rent</th>
                                            <th width="120">Utility</th>
                                            <th width="140">Special (+/-)</th>
                                            <th class="text-end">Total Due</th>
                                            <th width="130" class="text-center">Status</th>
                                            <th>Note</th>
                                        </tr>
                                    </thead>
                                    <tbody id="allocation_tbody"></tbody>
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td colspan="4" class="text-end pe-3">Grand Total Collection:</td>
                                            <td class="text-end text-primary fs-5" id="grand_total_display">৳ 0</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const CURRENT_URL = window.location.href.split('?')[0];
        let memberCount = 0;

        async function loadFlatData() {
            const month = document.getElementById('cost_month').value;
            const tbody = document.getElementById('allocation_tbody');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>';

            try {
                const res = await fetch(`${CURRENT_URL}?action=fetch_data&month=${month}`);
                const data = await res.json();

                if (data.status === 'success') {
                    const m = data.master;
                    document.getElementById('bill_electricity').value = parseFloat(m.electricity_bill) || 1800;
                    document.getElementById('bill_gas').value = parseFloat(m.gas_bill) || 0;
                    document.getElementById('bill_aunty').value = parseFloat(m.aunty_bill) || 5000;
                    document.getElementById('bill_dust').value = parseFloat(m.dust_bill) || 200;
                    document.getElementById('bill_net').value = parseFloat(m.internet_bill || 0);
                    document.getElementById('bill_other').value = parseFloat(m.other_bills || 0);

                    tbody.innerHTML = '';
                    memberCount = data.members.length;

                    data.members.forEach(mem => {
                        const isPaid = mem.status === 'paid';
                        const btnClass = isPaid ? 'btn-success' : 'btn-outline-danger';
                        const btnIcon = isPaid ? 'fa-check-circle' : 'fa-times-circle';
                        const btnText = isPaid ? 'PAID' : 'UNPAID';
                        const email = mem.email ? mem.email : '';

                        const row = `
                        <tr data-member-id="${mem.member_id}">
                            <td class="ps-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold text-dark">${mem.name}</div>
                                        <div class="badge bg-light text-secondary border px-2">Base: ৳${mem.default_rent}</div>
                                    </div>
                                    ${email ? `
                                    <button type="button" class="btn btn-link text-primary p-0 ms-2" 
                                            title="Send Bill to ${email}"
                                            onclick="sendMail('${email}', '${mem.name}', this)">
                                        <i class="fas fa-envelope fa-lg"></i>
                                    </button>` : '<span class="text-muted small ms-2" title="No Email"><i class="fas fa-envelope-open"></i></span>'}
                                </div>
                            </td>
                            <td><input type="number" class="form-control form-control-sm fw-bold rent-input" value="${parseFloat(mem.rent_amount)}"></td>
                            <td><input type="text" class="form-control form-control-sm bg-light border-0 utility-share-display" readonly value="${parseFloat(mem.utility_share)}"></td>
                            <td><input type="number" class="form-control form-control-sm text-danger special-input" placeholder="0" value="${parseFloat(mem.special_adjustment)}"></td>
                            <td class="text-end fw-bold text-primary row-total fs-6">৳ ${parseFloat(mem.total_due)}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm ${btnClass} btn-status" onclick="toggleStatus(this, ${mem.alloc_id || 0})">
                                    <i class="fas ${btnIcon} me-1"></i> ${btnText}
                                </button>
                                <input type="hidden" class="status-input" value="${mem.status}">
                            </td>
                            <td><input type="text" class="form-control form-control-sm note-input" placeholder="Add note..." value="${mem.note || ''}"></td>
                        </tr>`;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                    reCalculateAll();
                    showToast('Data loaded', 'success');
                }
            } catch (e) { showToast('Error loading data', 'danger'); }
        }

        // New Mail Function
        async function sendMail(email, name, btnElement) {
            if (!confirm(`Send email bill to ${name}?`)) return;

            const row = btnElement.closest('tr');
            const amountText = row.querySelector('.row-total').innerText; // Gets calculated amount
            const month = document.getElementById('cost_month').value;

            // visual feedback
            const originalIcon = btnElement.innerHTML;
            btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btnElement.disabled = true;

            const fd = new FormData();
            fd.append('action', 'send_mail');
            fd.append('email', email);
            fd.append('name', name);
            fd.append('amount', amountText);
            fd.append('month', month);

            try {
                const res = await fetch(CURRENT_URL, { method: 'POST', body: fd });
                const data = await res.json();
                showToast(data.message, data.status === 'success' ? 'success' : 'danger');
            } catch (e) {
                showToast('Error sending mail', 'danger');
            } finally {
                btnElement.innerHTML = originalIcon;
                btnElement.disabled = false;
            }
        }

        function reCalculateAll() {
            const inputs = document.querySelectorAll('.utility-input');
            let totalUtil = 0;
            inputs.forEach(inp => totalUtil += (parseFloat(inp.value) || 0));

            const perHead = memberCount > 0 ? (totalUtil / memberCount) : 0;
            document.getElementById('display_total_utility').innerText = '৳ ' + totalUtil.toFixed(0);
            document.getElementById('display_per_head').innerText = '৳ ' + perHead.toFixed(2);

            let grandTotal = 0;
            document.querySelectorAll('#allocation_tbody tr').forEach(row => {
                const rent = parseFloat(row.querySelector('.rent-input').value) || 0;
                const special = parseFloat(row.querySelector('.special-input').value) || 0;
                row.querySelector('.utility-share-display').value = perHead.toFixed(2);
                const rowTotal = rent + perHead + special;
                grandTotal += rowTotal;
                row.querySelector('.row-total').innerText = '৳ ' + rowTotal.toFixed(2);
            });
            document.getElementById('grand_total_display').innerText = '৳ ' + grandTotal.toFixed(2);
        }

        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('utility-input') || e.target.classList.contains('rent-input') || e.target.classList.contains('special-input')) {
                reCalculateAll();
            }
        });

        document.getElementById('flatCostForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const month = document.getElementById('cost_month').value;
            const memberData = [];
            document.querySelectorAll('#allocation_tbody tr').forEach(row => {
                memberData.push({
                    member_id: row.dataset.memberId,
                    rent_amount: row.querySelector('.rent-input').value,
                    utility_share: row.querySelector('.utility-share-display').value,
                    special_adjustment: row.querySelector('.special-input').value,
                    note: row.querySelector('.note-input').value,
                    status: row.querySelector('.status-input').value
                });
            });

            const formData = new FormData();
            formData.append('action', 'save_all');
            formData.append('month_year', month);
            formData.append('electricity_bill', document.getElementById('bill_electricity').value);
            formData.append('gas_bill', document.getElementById('bill_gas').value);
            formData.append('aunty_bill', document.getElementById('bill_aunty').value);
            formData.append('dust_bill', document.getElementById('bill_dust').value);
            formData.append('internet_bill', document.getElementById('bill_net').value);
            formData.append('other_bills', document.getElementById('bill_other').value);
            formData.append('member_data', JSON.stringify(memberData));

            try {
                const res = await fetch(CURRENT_URL, { method: 'POST', body: formData });
                const data = await res.json();
                showToast(data.message, data.status === 'success' ? 'success' : 'danger');
            } catch (e) { showToast('Failed to save data', 'danger'); }
        });

        window.toggleStatus = function (btn, allocId) {
            const input = btn.nextElementSibling;
            const newStatus = input.value === 'paid' ? 'unpaid' : 'paid';
            input.value = newStatus;

            if (newStatus === 'paid') {
                btn.className = 'btn btn-sm btn-success btn-status';
                btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> PAID';
            } else {
                btn.className = 'btn btn-sm btn-outline-danger btn-status';
                btn.innerHTML = '<i class="fas fa-times-circle me-1"></i> UNPAID';
            }

            if (allocId > 0) {
                const fd = new FormData();
                fd.append('action', 'toggle_status');
                fd.append('alloc_id', allocId);
                fd.append('status', newStatus);
                fetch(CURRENT_URL, { method: 'POST', body: fd });
            }
        };

        function showToast(msg, type) {
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            const color = type === 'success' ? 'text-success' : 'text-danger';
            const el = document.createElement('div');
            el.className = `toast show align-items-center border-0 mb-2`;
            el.innerHTML = `<div class="d-flex p-2"><div class="toast-body fw-bold ${color}"><i class="fas ${icon} me-2"></i>${msg}</div><button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
            document.getElementById('toast-container').appendChild(el);
            setTimeout(() => el.remove(), 3000);
        }

        window.onload = loadFlatData;
    </script>
</body>

</html>