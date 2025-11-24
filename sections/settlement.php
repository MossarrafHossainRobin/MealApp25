<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Settlement - Financial System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-green: #198754;
            --secondary-green: #157347;
            --light-green: #d1e7dd;
            --primary-blue: #0d6efd;
            --light-blue: #cfe2ff;
            --danger-red: #dc3545;
            --warning-orange: #fd7e14;
            --success-green: #198754;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .card-title {
            font-weight: 600;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 12px;
        }

        .settlement-header {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .balance-gets {
            color: var(--success-green);
            font-weight: 600;
        }

        .balance-owes {
            color: var(--danger-red);
            font-weight: 600;
        }

        .transaction-item {
            border-left: 4px solid var(--primary-green);
            padding-left: 15px;
            transition: all 0.2s ease;
        }

        .transaction-item:hover {
            background-color: var(--light-green);
            transform: translateX(5px);
        }

        .member-balance-item {
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .member-balance-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        .progress {
            height: 8px;
            margin-top: 5px;
        }

        .btn-settle {
            background: linear-gradient(to right, var(--primary-green), var(--secondary-green));
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-settle:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background-color: var(--light-green);
            color: var(--success-green);
        }

        .amount-highlight {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .security-notice {
            background-color: #fef7ec;
            border-left: 4px solid var(--warning-orange);
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .confirmation-modal .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .confirmation-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .amount-input {
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .amount-input:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }

        .transaction-history {
            max-height: 400px;
            overflow-y: auto;
        }

        .summary-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }

        </style>
</head>
<body>
    <div class="container mt-4">
        <div class="settlement-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-1"><i class="bi bi-cash-coin me-2"></i>Monthly Settlement</h2>
                    <p class="mb-0"><?php echo $current_month . ' ' . $current_year; ?></p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="status-badge status-pending" id="settlementStatus">Pending Settlement</span>
                </div>
            </div>
        </div>

        <div class="security-notice">
            <i class="bi bi-shield-check me-2"></i>
            <strong>Security Notice:</strong> All settlement transactions are encrypted and processed with bank-level security.
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-6">
                <!-- Monthly Overview Card -->
                <div class="card mb-4 fade-in">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="bi bi-graph-up me-2"></i>Monthly Overview
                        </h5>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="summary-card">
                                    <div class="text-muted small">Total Bazar</div>
                                    <div class="amount-highlight text-primary">BDT <?php echo number_format($total_bazar, 2); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="summary-card">
                                    <div class="text-muted small">Total Meals</div>
                                    <div class="amount-highlight text-primary"><?php echo $total_meals; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="summary-card">
                                    <div class="text-muted small">Average Meal Cost</div>
                                    <div class="amount-highlight text-primary">BDT <?php echo number_format($avg_meal_cost, 2); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="summary-card">
                                    <div class="text-muted small">Members Count</div>
                                    <div class="amount-highlight text-info"><?php echo $total_members; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settlement Actions Card -->
                <div class="card mb-4 fade-in">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="bi bi-gear me-2"></i>Settlement Actions
                        </h5>
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-settle text-white" id="btnProcessSettlement">
                                <i class="bi bi-check-circle me-2"></i>Process Settlement
                            </button>
                            <button class="btn btn-outline-primary" id="btnExportReport">
                                <i class="bi bi-file-earmark-pdf me-2"></i>Export Settlement Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Settlement Transactions Card -->
                <div class="card mb-4 fade-in">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="bi bi-arrow-left-right me-2"></i>Settlement Transactions
                        </h5>
                        <div class="loading-spinner" id="transactionsLoading">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Calculating transactions...</p>
                        </div>
                        <div class="mt-3" id="transactionsContainer">
                            <?php if (!empty($transactions)): ?>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <small>To settle all balances, follow these transactions:</small>
                                    </div>
                                    <div class="transaction-history">
                                        <?php foreach ($transactions as $index => $transaction): ?>
                                                <div class="transaction-item mb-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-secondary me-2"><?php echo $index + 1; ?></span>
                                                            <div>
                                                                <strong class="text-danger"><?php echo htmlspecialchars($transaction['from']); ?></strong>
                                                                <i class="bi bi-arrow-right mx-2 text-muted"></i>
                                                                <strong class="text-success"><?php echo htmlspecialchars($transaction['to']); ?></strong>
                                                            </div>
                                                        </div>
                                                        <div class="text-success fw-bold">
                                                            BDT <?php echo number_format($transaction['amount'], 2); ?>
                                                        </div>
                                                    </div>
                                                    <div class="progress mt-2">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: 0%" 
                                                             data-amount="<?php echo $transaction['amount']; ?>"></div>
                                                    </div>
                                                </div>
                                        <?php endforeach; ?>
                                    </div>
                            <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No transactions needed. All balances are settled.</p>
                                    </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-6">
                <!-- Member Balances Card -->
                <div class="card mb-4 fade-in">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="bi bi-people me-2"></i>Member Balances
                        </h5>
                        <div class="loading-spinner" id="balancesLoading">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading member balances...</p>
                        </div>
                        <div class="mt-3" id="balancesContainer">
                            <?php foreach ($balances as $balance): ?>
                                    <?php
                                    $balance_amount = floatval($balance['balance']);
                                    $balance_class = $balance_amount > 0 ? 'balance-gets' : ($balance_amount < 0 ? 'balance-owes' : 'text-muted');
                                    $balance_text = $balance_amount > 0 ? "Gets: BDT " . number_format(abs($balance_amount), 2) :
                                        ($balance_amount < 0 ? "Owes: BDT " . number_format(abs($balance_amount), 2) : 'Settled');
                                    ?>
                                    <div class="member-balance-item mb-3 p-3 border rounded">
                                        <div class="d-flex justify-content-between align-items-center">
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
                                    </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Settlements Card -->
                <div class="card mb-4 fade-in">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="bi bi-clock-history me-2"></i>Recent Settlements
                        </h5>
                        <div id="recentSettlements">
                            <div class="text-center py-3">
                                <div class="spinner-border text-success spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="text-muted ms-2">Loading recent settlements...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settlement Confirmation Modal -->
    <div class="modal fade confirmation-modal" id="settlementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-shield-check me-2"></i>Confirm Settlement
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action will process financial transactions. Please verify all details before proceeding.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Settlement Month</label>
                        <input type="text" class="form-control" value="<?php echo $current_month . ' ' . $current_year; ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Total Amount to Settle</label>
                        <input type="text" class="form-control amount-input" value="BDT <?php echo number_format($total_bazar, 2); ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Number of Transactions</label>
                        <input type="text" class="form-control" value="<?php echo count($transactions); ?> transactions" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="settlementNotes" class="form-label">Settlement Notes (Optional)</label>
                        <textarea class="form-control" id="settlementNotes" rows="2" placeholder="Add any notes about this settlement..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmSettlement">
                        <i class="bi bi-check-circle me-2"></i>Confirm & Process
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle me-2"></i>
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Settlement processed successfully!
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Hide loading spinners initially
            document.getElementById('transactionsLoading').style.display = 'none';
            document.getElementById('balancesLoading').style.display = 'none';
            
            // Load recent settlements via AJAX
            loadRecentSettlements();
            
            // Process Settlement Button
            document.getElementById('btnProcessSettlement').addEventListener('click', function() {
                var settlementModal = new bootstrap.Modal(document.getElementById('settlementModal'));
                settlementModal.show();
            });
            
            // Confirm Settlement
            document.getElementById('confirmSettlement').addEventListener('click', function() {
                processSettlement();
            });
            
            // Export Report Button
            document.getElementById('btnExportReport').addEventListener('click', function() {
                exportSettlementReport();
            });
            
            // Animate progress bars
            animateProgressBars();
        });
        
        function loadRecentSettlements() {
            // Simulate API call to get recent settlements
            setTimeout(function() {
                document.getElementById('recentSettlements').innerHTML = `
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">June 2023</h6>
                                <small class="text-muted">Completed on 01 Jul 2023</small>
                            </div>
                            <span class="status-badge status-completed">Completed</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">May 2023</h6>
                                <small class="text-muted">Completed on 01 Jun 2023</small>
                            </div>
                            <span class="status-badge status-completed">Completed</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">April 2023</h6>
                                <small class="text-muted">Completed on 01 May 2023</small>
                            </div>
                            <span class="status-badge status-completed">Completed</span>
                        </div>
                    </div>
                `;
            }, 1500);
        }
        
        function animateProgressBars() {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const amount = parseFloat(bar.getAttribute('data-amount'));
                // Simple animation to fill progress bar based on amount
                setTimeout(() => {
                    bar.style.width = '100%';
                }, 300);
            });
        }
        
        function processSettlement() {
            // Show loading state
            document.getElementById('confirmSettlement').innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Processing...
            `;
            document.getElementById('confirmSettlement').disabled = true;
            
            // Get settlement notes
            const notes = document.getElementById('settlementNotes').value;
            
            // Simulate API call to settlement_actions.php
            setTimeout(function() {
                // Hide modal
                const settlementModal = bootstrap.Modal.getInstance(document.getElementById('settlementModal'));
                settlementModal.hide();
                
                // Show success toast
                const successToast = new bootstrap.Toast(document.getElementById('successToast'));
                successToast.show();
                
                // Update UI
                document.getElementById('settlementStatus').textContent = 'Settled';
                document.getElementById('settlementStatus').className = 'status-badge status-completed';
                
                // Reset button
                document.getElementById('confirmSettlement').innerHTML = `
                    <i class="bi bi-check-circle me-2"></i>Confirm & Process
                `;
                document.getElementById('confirmSettlement').disabled = false;
                
                // Refresh transactions (in a real app, this would be an API call)
                refreshSettlementData();
                
            }, 2000); // Simulate API processing time
        }
        
        function refreshSettlementData() {
            // In a real application, this would make an API call to get updated data
            document.getElementById('transactionsContainer').innerHTML = `
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">All balances have been settled for <?php echo $current_month . ' ' . $current_year; ?>.</p>
                </div>
            `;
        }
        
        function exportSettlementReport() {
            // Show loading state on button
            const exportBtn = document.getElementById('btnExportReport');
            const originalText = exportBtn.innerHTML;
            exportBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Generating PDF...
            `;
            exportBtn.disabled = true;
            
            // Simulate PDF generation
            setTimeout(function() {
                // Reset button
                exportBtn.innerHTML = originalText;
                exportBtn.disabled = false;
                
                // Show success message (in a real app, this would trigger a download)
                alert('Settlement report generated successfully!');
            }, 1500);
        }
    </script>
</body>
</html>