<div class="container mt-4">
    <h2 class="text-success mb-4">Monthly Settlement - <?php echo $current_month . ' ' . $current_year; ?></h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-success">Monthly Overview</h5>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Bazar:</span>
                            <strong class="text-primary">BDT <?php echo number_format($total_bazar, 2); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Meals:</span>
                            <strong class="text-primary"><?php echo $total_meals; ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Average Meal Cost:</span>
                            <strong class="text-primary">BDT <?php echo number_format($avg_meal_cost, 2); ?></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Members Count:</span>
                            <strong class="text-info"><?php echo $total_members; ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-body">
                    <h5 class="card-title text-success">Settlement Transactions</h5>
                    <div class="mt-3">
                        <?php if (!empty($transactions)): ?>
                            <div class="alert alert-info"><small>To settle all balances, follow these transactions:</small>
                            </div>
                            <?php foreach ($transactions as $index => $transaction): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-secondary me-2"><?php echo $index + 1; ?></span>
                                        <div>
                                            <strong
                                                class="text-danger"><?php echo htmlspecialchars($transaction['from']); ?></strong>
                                            <i class="bi bi-arrow-right mx-2 text-muted"></i>
                                            <strong
                                                class="text-success"><?php echo htmlspecialchars($transaction['to']); ?></strong>
                                        </div>
                                    </div>
                                    <div class="text-success fw-bold">
                                        BDT <?php echo number_format($transaction['amount'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No transactions needed. All balances are settled.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-success">Member Balances</h5>
                    <div class="mt-3">
                        <?php foreach ($balances as $balance): ?>
                            <?php
                            $balance_amount = floatval($balance['balance']);
                            $balance_class = $balance_amount > 0 ? 'gets' : ($balance_amount < 0 ? 'owes' : 'text-muted');
                            $balance_text = $balance_amount > 0 ? "Gets: BDT " . number_format(abs($balance_amount), 2) :
                                ($balance_amount < 0 ? "Owes: BDT " . number_format(abs($balance_amount), 2) : 'Settled');
                            ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($balance['name']); ?></h6>
                                    <small class="text-muted">
                                        Bazar: BDT <?php echo number_format($balance['total_bazar'], 2); ?> |
                                        Meals: <?php echo $balance['total_meals']; ?>
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="<?php echo $balance_class; ?>"><?php echo $balance_text; ?></div>
                                    <small class="text-muted">Expected: BDT
                                        <?php echo number_format($balance['expected_cost'], 2); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>