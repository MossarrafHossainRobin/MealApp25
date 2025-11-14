<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="text-success fw-bold mb-0">📊 Bazar Summary</h2>
                <div class="badge bg-success fs-6 p-2"><?php echo date('F Y'); ?></div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number">BDT <?php echo number_format($total_bazar, 2); ?></div>
                <div class="stat-label">Total Bazar</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stat-number"><?php echo $total_meals; ?></div>
                <div class="stat-label">Total Meals</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stat-number">BDT <?php echo number_format($avg_meal_cost, 2); ?></div>
                <div class="stat-label">Avg Meal Cost</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="stat-number"><?php echo $total_members; ?></div>
                <div class="stat-label">Active Members</div>
            </div>
        </div>
    </div>

    <div class="mobile-grid">
        <!-- Bazar Log -->
        <div class="enhanced-card">
            <div class="enhanced-card-header">
                <h5 class="mb-0">🛒 Recent Bazar Entries</h5>
            </div>
            <div class="enhanced-card-body p-0">
                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($bazar_entries, 0, 8) as $entry): ?>
                                <tr>
                                    <td><small
                                            class="text-muted"><?php echo date('M j', strtotime($entry['date'])); ?></small>
                                    </td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($entry['member_name']); ?></td>
                                    <td class="fw-bold text-success">BDT <?php echo number_format($entry['amount'], 2); ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="process/actions.php" onsubmit="showLoading()"
                                            class="d-inline">
                                            <input type="hidden" name="action" value="delete_bazar">
                                            <input type="hidden" name="current_section" value="summary">
                                            <input type="hidden" name="bazar_id" value="<?php echo $entry['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger p-1"
                                                onclick="return confirm('Delete this entry?')" title="Delete">
                                                🗑️
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Member Contributions -->
        <div class="enhanced-card">
            <div class="enhanced-card-header">
                <h5 class="mb-0">👥 Member Contributions</h5>
            </div>
            <div class="enhanced-card-body">
                <?php
                $member_totals = [];
                foreach ($bazar_entries as $entry) {
                    $member_name = $entry['member_name'];
                    if (!isset($member_totals[$member_name])) {
                        $member_totals[$member_name] = 0;
                    }
                    $member_totals[$member_name] += $entry['amount'];
                }
                arsort($member_totals);
                ?>
                <?php foreach ($member_totals as $member => $total): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded-3 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <span class="text-white">👤</span>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-semibold"><?php echo htmlspecialchars($member); ?></h6>
                                <small
                                    class="text-muted"><?php echo count(array_filter($bazar_entries, fn($e) => $e['member_name'] === $member)); ?>
                                    entries</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success fs-5">BDT <?php echo number_format($total, 2); ?></div>
                            <small
                                class="text-muted"><?php echo number_format(($total / max($total_bazar, 1)) * 100, 1); ?>%</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>