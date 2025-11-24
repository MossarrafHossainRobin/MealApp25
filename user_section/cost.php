<?php
// user_section/cost.php - Cost Allocation Section

// Get user data
$userData = $action->getUserData($_SESSION['user_id']);
?>
<style>
    .cost-section .card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: var(--card-shadow);
        margin-bottom: 20px;
    }

    .cost-section .card-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .cost-section .card-header i {
        font-size: 1.5rem;
        margin-right: 10px;
        color: var(--primary);
    }

    .cost-section .card-header h2 {
        font-size: 1.3rem;
        color: var(--dark);
    }

    .cost-section .table-responsive {
        overflow-x: auto;
    }

    .cost-section table {
        width: 100%;
        border-collapse: collapse;
    }

    .cost-section th,
    .cost-section td {
        padding: 12px 10px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .cost-section th {
        font-weight: 600;
        color: var(--gray);
        font-size: 0.9rem;
    }

    .cost-section .amount {
        font-weight: 600;
        color: var(--primary);
    }

    .cost-section .total-due {
        color: var(--danger);
        font-weight: 700;
    }

    .cost-section .paid {
        color: var(--success);
    }

    .cost-section .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .cost-section .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .cost-section .status-badge.paid {
        background: #d4edda;
        color: #155724;
    }

    .cost-section .cost-summary {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    @media (min-width: 768px) {
        .cost-section .cost-summary {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .cost-section .summary-item {
        text-align: center;
    }

    .cost-section .summary-item h3 {
        color: var(--dark);
        margin-bottom: 5px;
    }

    .cost-section .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--gray);
    }

    .cost-section .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dee2e6;
    }

    .cost-section .empty-state h3 {
        margin-bottom: 10px;
        color: var(--dark);
    }
</style>

<div class="cost-section">
    <div class="section-header">
        <h1><i class="fas fa-home"></i> Cost Allocation</h1>
        <p>Your flat cost breakdown and payment status</p>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-money-bill-wave"></i>
            <h2>Flat Cost Allocation</h2>
        </div>

        <?php if (!empty($userData['flat_costs'])): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Rent Amount</th>
                            <th>Utility Share</th>
                            <th>Special Adjustment</th>
                            <th>Total Due</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userData['flat_costs'] as $cost): ?>
                            <tr>
                                <td class="amount">$<?php echo number_format($cost['rent_amount'], 2); ?></td>
                                <td class="amount">$<?php echo number_format($cost['utility_share'], 2); ?></td>
                                <td class="amount">$<?php echo number_format($cost['special_adjustment'] ?? 0, 2); ?></td>
                                <td class="amount total-due">$<?php echo number_format($cost['amount_due'], 2); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($cost['status']); ?>">
                                        <?php echo htmlspecialchars($cost['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($cost['note'] ?? 'No notes'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cost-summary">
                <div class="summary-item">
                    <h3>Total Due</h3>
                    <span class="amount">$<?php
                    $total_due = 0;
                    foreach ($userData['flat_costs'] as $cost) {
                        if ($cost['status'] === 'Pending') {
                            $total_due += $cost['amount_due'];
                        }
                    }
                    echo number_format($total_due, 2);
                    ?></span>
                </div>
                <div class="summary-item">
                    <h3>Total Paid</h3>
                    <span class="amount paid">$<?php
                    $total_paid = 0;
                    foreach ($userData['flat_costs'] as $cost) {
                        if ($cost['status'] === 'Paid') {
                            $total_paid += $cost['amount_due'];
                        }
                    }
                    echo number_format($total_paid, 2);
                    ?></span>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-home"></i>
                <h3>No Cost Records</h3>
                <p>No cost allocation records found for your account.</p>
            </div>
        <?php endif; ?>
    </div>
</div>