<?php
// user_section/cost.php - Cost Allocation Section

// Get user data
$userData = $action->getUserData($_SESSION['user_id']);

// Calculate totals
$total_due = 0;
$pending_costs = 0;

foreach ($userData['flat_costs'] as $cost) {
    if ($cost['status'] === 'Pending') {
        $total_due += $cost['amount_due'];
        $pending_costs++;
    }
}

$total_costs = count($userData['flat_costs']);
?>

<style>
    .cost-section {
        padding: 0;
        margin: 0;
    }

    /* Mobile First Design */
    .mobile-cost {
        display: block;
    }

    .desktop-cost {
        display: none;
    }

    /* Mobile Cost Styles */
    .mobile-cost {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 0;
        margin: 0;
    }

    .cost-header {
        background: linear-gradient(135deg, #d63384, #e91e63);
        color: white;
        padding: 20px 15px;
    }

    .cost-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .cost-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .cost-content {
        padding: 20px 15px;
        padding-bottom: 80px;
    }

    /* Summary Cards */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: white;
        border-radius: 16px;
        padding: 15px 10px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .summary-card:active {
        transform: scale(0.95);
    }

    .summary-value {
        font-size: 1.4rem;
        font-weight: 800;
        margin-bottom: 4px;
        line-height: 1;
    }

    .summary-label {
        font-size: 0.75rem;
        color: #666;
        font-weight: 600;
        line-height: 1;
    }

    /* Color Coding */
    .text-due {
        color: #e74c3c;
    }

    .text-pending {
        color: #e67e22;
    }

    .text-total {
        color: #3498db;
    }

    /* Cost History Card */
    .history-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: #d63384;
    }

    /* Cost Items */
    .cost-list {
        margin-top: 15px;
    }

    .cost-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
        border-left: 4px solid #e74c3c;
    }

    .cost-item.paid {
        border-left-color: #27ae60;
    }

    .cost-item:active {
        transform: scale(0.98);
        background: #e9ecef;
    }

    .cost-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .cost-amount {
        font-weight: 800;
        font-size: 1.1rem;
    }

    .cost-amount.due {
        color: #e74c3c;
    }

    .cost-amount.paid {
        color: #27ae60;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-badge.pending {
        background: #ffeaa7;
        color: #e67e22;
    }

    .status-badge.paid {
        background: #55efc4;
        color: #00b894;
    }

    .cost-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 10px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
    }

    .detail-label {
        font-size: 0.7rem;
        color: #666;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .detail-value {
        font-size: 0.85rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .cost-note {
        padding-top: 10px;
        border-top: 1px solid #e9ecef;
        font-size: 0.8rem;
        color: #666;
        font-style: italic;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 20px;
    }

    .action-btn {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 15px 10px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .action-btn:active {
        transform: scale(0.95);
        background: #f8f9fa;
    }

    .action-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
    }

    .pay-now .action-icon {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
    }

    .view-breakdown .action-icon {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }

    .payment-history .action-icon {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
    }

    .support .action-icon {
        background: linear-gradient(135deg, #e67e22, #d35400);
    }

    .action-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #2c3e50;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-icon {
        font-size: 3rem;
        color: #d63384;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .empty-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .empty-text {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    /* Desktop Cost Styles */
    @media (min-width: 992px) {
        .mobile-cost {
            display: none;
        }

        .desktop-cost {
            display: block;
            padding: 30px 0;
            background: #f8f9fa;
            min-height: calc(100vh - 80px);
        }

        .desktop-cost-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .desktop-cost-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .desktop-cost-title {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #d63384, #e91e63);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .desktop-cost-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        .desktop-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .desktop-summary-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .desktop-summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        }

        .desktop-summary-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .desktop-summary-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 600;
        }

        .desktop-history-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .desktop-cost-list {
            margin-top: 20px;
        }

        .desktop-cost-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border-left: 4px solid #e74c3c;
        }

        .desktop-cost-item.paid {
            border-left-color: #27ae60;
        }

        .desktop-cost-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .desktop-cost-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .desktop-cost-amount {
            font-weight: 800;
            font-size: 1.3rem;
        }

        .desktop-cost-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .desktop-detail-item {
            display: flex;
            flex-direction: column;
        }

        .desktop-detail-label {
            font-size: 0.8rem;
            color: #666;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .desktop-detail-value {
            font-size: 1rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .desktop-cost-note {
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            font-size: 0.9rem;
            color: #666;
            font-style: italic;
        }

        .desktop-quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 30px;
        }

        .desktop-action-btn {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 25px 15px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .desktop-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #d63384;
        }

        .desktop-action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto 12px;
        }

        .desktop-action-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .desktop-empty-state {
            text-align: center;
            padding: 60px 40px;
        }

        .desktop-empty-icon {
            font-size: 4rem;
            color: #d63384;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .desktop-empty-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
    }

    /* Responsive Design */
    @media (max-width: 480px) {
        .cost-content {
            padding: 15px 12px;
        }

        .summary-grid {
            gap: 8px;
        }

        .summary-card {
            padding: 12px 8px;
        }

        .summary-value {
            font-size: 1.2rem;
        }

        .history-card {
            padding: 15px;
        }

        .cost-item {
            padding: 12px;
        }

        .quick-actions {
            gap: 8px;
        }

        .action-btn {
            padding: 12px 8px;
        }

        .action-icon {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
    }
</style>

<!-- Mobile Cost Section -->
<div class="mobile-cost">
    <div class="cost-header">
        <div class="cost-title">Cost Management</div>
        <div class="cost-subtitle">Track your payments and dues</div>
    </div>

    <div class="cost-content">
        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-value text-due">৳ <?php echo number_format($total_due, 2); ?></div>
                <div class="summary-label">Total Due</div>
            </div>
            <div class="summary-card">
                <div class="summary-value text-pending"><?php echo $pending_costs; ?></div>
                <div class="summary-label">Pending Bills</div>
            </div>
            <div class="summary-card">
                <div class="summary-value text-total"><?php echo $total_costs; ?></div>
                <div class="summary-label">Total Bills</div>
            </div>
        </div>

        <!-- Cost History -->
        <div class="history-card">
            <div class="card-title">
                <i class="fas fa-file-invoice-dollar"></i>
                Cost History
            </div>

            <?php if (!empty($userData['flat_costs'])): ?>
                <div class="cost-list">
                    <?php foreach ($userData['flat_costs'] as $cost): ?>
                        <div class="cost-item <?php echo strtolower($cost['status']); ?>">
                            <div class="cost-header-row">
                                <div class="cost-amount <?php echo strtolower($cost['status']); ?>">
                                    ৳ <?php echo number_format($cost['amount_due'], 2); ?>
                                </div>
                                <span class="status-badge <?php echo strtolower($cost['status']); ?>">
                                    <?php echo htmlspecialchars($cost['status']); ?>
                                </span>
                            </div>

                            <div class="cost-details">
                                <div class="detail-item">
                                    <span class="detail-label">Rent</span>
                                    <span class="detail-value">৳ <?php echo number_format($cost['rent_amount'], 2); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Utility</span>
                                    <span class="detail-value">৳ <?php echo number_format($cost['utility_share'], 2); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Adjustment</span>
                                    <span class="detail-value">৳
                                        <?php echo number_format($cost['special_adjustment'] ?? 0, 2); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Due Date</span>
                                    <span class="detail-value"><?php echo date('M d', strtotime($cost['created_at'])); ?></span>
                                </div>
                            </div>

                            <?php if (!empty($cost['note'])): ?>
                                <div class="cost-note">
                                    <?php echo htmlspecialchars($cost['note']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="empty-title">No Cost Records</div>
                    <div class="empty-text">No cost allocation records found for your account.</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="action-btn pay-now">
                <div class="action-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="action-label">Pay Now</div>
            </div>
            <div class="action-btn view-breakdown">
                <div class="action-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="action-label">Breakdown</div>
            </div>
            <div class="action-btn payment-history">
                <div class="action-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="action-label">History</div>
            </div>
            <div class="action-btn support">
                <div class="action-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="action-label">Support</div>
            </div>
        </div>
    </div>
</div>

<!-- Desktop Cost Section -->
<div class="desktop-cost">
    <div class="desktop-cost-container">
        <div class="desktop-cost-header">
            <h1 class="desktop-cost-title">Cost Management</h1>
            <p class="desktop-cost-subtitle">Track your payments, dues, and financial breakdown</p>
        </div>

        <!-- Summary Cards -->
        <div class="desktop-summary-grid">
            <div class="desktop-summary-card">
                <div class="desktop-summary-value text-due">৳ <?php echo number_format($total_due, 2); ?></div>
                <div class="desktop-summary-label">Total Due</div>
            </div>
            <div class="desktop-summary-card">
                <div class="desktop-summary-value text-pending"><?php echo $pending_costs; ?></div>
                <div class="desktop-summary-label">Pending Bills</div>
            </div>
            <div class="desktop-summary-card">
                <div class="desktop-summary-value text-total"><?php echo $total_costs; ?></div>
                <div class="desktop-summary-label">Total Bills</div>
            </div>
        </div>

        <!-- Cost History -->
        <div class="desktop-history-card">
            <div class="card-title">
                <i class="fas fa-file-invoice-dollar"></i>
                Cost Breakdown & History
            </div>

            <?php if (!empty($userData['flat_costs'])): ?>
                <div class="desktop-cost-list">
                    <?php foreach ($userData['flat_costs'] as $cost): ?>
                        <div class="desktop-cost-item <?php echo strtolower($cost['status']); ?>">
                            <div class="desktop-cost-header-row">
                                <div class="desktop-cost-amount <?php echo strtolower($cost['status']); ?>">
                                    ৳ <?php echo number_format($cost['amount_due'], 2); ?>
                                </div>
                                <span class="status-badge <?php echo strtolower($cost['status']); ?>">
                                    <?php echo htmlspecialchars($cost['status']); ?>
                                </span>
                            </div>

                            <div class="desktop-cost-details">
                                <div class="desktop-detail-item">
                                    <span class="desktop-detail-label">Base Rent</span>
                                    <span class="desktop-detail-value">৳
                                        <?php echo number_format($cost['rent_amount'], 2); ?></span>
                                </div>
                                <div class="desktop-detail-item">
                                    <span class="desktop-detail-label">Utility Share</span>
                                    <span class="desktop-detail-value">৳
                                        <?php echo number_format($cost['utility_share'], 2); ?></span>
                                </div>
                                <div class="desktop-detail-item">
                                    <span class="desktop-detail-label">Adjustments</span>
                                    <span class="desktop-detail-value">৳
                                        <?php echo number_format($cost['special_adjustment'] ?? 0, 2); ?></span>
                                </div>
                                <div class="desktop-detail-item">
                                    <span class="desktop-detail-label">Billing Date</span>
                                    <span
                                        class="desktop-detail-value"><?php echo date('M d, Y', strtotime($cost['created_at'])); ?></span>
                                </div>
                            </div>

                            <?php if (!empty($cost['note'])): ?>
                                <div class="desktop-cost-note">
                                    <strong>Note:</strong> <?php echo htmlspecialchars($cost['note']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="desktop-empty-state">
                    <div class="desktop-empty-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="desktop-empty-title">No Cost Records Found</div>
                    <div class="empty-text">Your cost allocation records will appear here once available</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="desktop-quick-actions">
            <div class="desktop-action-btn pay-now">
                <div class="desktop-action-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="desktop-action-label">Make Payment</div>
            </div>
            <div class="desktop-action-btn view-breakdown">
                <div class="desktop-action-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="desktop-action-label">View Breakdown</div>
            </div>
            <div class="desktop-action-btn payment-history">
                <div class="desktop-action-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="desktop-action-label">Payment History</div>
            </div>
            <div class="desktop-action-btn support">
                <div class="desktop-action-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="desktop-action-label">Get Support</div>
            </div>
        </div>
    </div>
</div>

<script>
    // Add click handlers for quick action buttons
    document.addEventListener('DOMContentLoaded', function () {
        // Mobile action buttons
        const mobileActions = document.querySelectorAll('.mobile-cost .action-btn');
        mobileActions.forEach(btn => {
            btn.addEventListener('click', function () {
                const actionType = this.classList[1]; // pay-now, view-breakdown, etc.
                handleCostAction(actionType);
            });
        });

        // Desktop action buttons
        const desktopActions = document.querySelectorAll('.desktop-cost .desktop-action-btn');
        desktopActions.forEach(btn => {
            btn.addEventListener('click', function () {
                const actionType = this.classList[1];
                handleCostAction(actionType);
            });
        });

        function handleCostAction(type) {
            // Add visual feedback
            const event = new CustomEvent('costAction', {
                detail: { type: type }
            });
            document.dispatchEvent(event);

            // Show action-specific feedback
            const actions = {
                'pay-now': 'Opening payment gateway...',
                'view-breakdown': 'Showing cost breakdown...',
                'payment-history': 'Loading payment history...',
                'support': 'Connecting to support...'
            };

            if (actions[type]) {
                alert(actions[type]);
            }
        }
    });
</script>