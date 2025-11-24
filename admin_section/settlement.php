<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0">Settlement & Reports</h2>
    <div>
        <select class="form-select me-2 d-inline-block w-auto" id="settlementMonth">
            <option value="<?php echo date('Y-m'); ?>"><?php echo date('F Y'); ?></option>
            <?php
            for ($i = 1; $i <= 12; $i++) {
                $month = date('Y-m', strtotime("-$i months"));
                echo '<option value="' . $month . '">' . date('F Y', strtotime($month)) . '</option>';
            }
            ?>
        </select>
        <button class="btn btn-primary" onclick="calculateSettlement()">
            <i class="fas fa-calculator me-2"></i>Calculate Settlement
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Settlement Results</h5>
    </div>
    <div class="card-body">
        <div id="settlementSummary" class="mb-4" style="display: none;">
            <div class="row">
                <div class="col-md-3">
                    <div class="card stat-card primary">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total Bazar</h6>
                            <h4 id="totalBazarSettlement" class="text-primary">৳0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card info">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total Meals</h6>
                            <h4 id="totalMealsSettlement" class="text-info">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card success">
                        <div class="card-body text-center">
                            <h6 class="card-title">Meal Rate</h6>
                            <h4 id="mealRateSettlement" class="text-success">৳0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card warning">
                        <div class="card-body text-center">
                            <h6 class="card-title">Members</h6>
                            <h4 id="totalMembersSettlement" class="text-warning">0</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Total Meals</th>
                        <th>Total Bazar</th>
                        <th>Meal Cost</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="settlementResults">
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Select a month and click "Calculate Settlement" to generate results
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    async function calculateSettlement() {
        const month = document.getElementById('settlementMonth').value;
        const resultsBody = document.getElementById('settlementResults');
        resultsBody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Calculating settlement...</div></td></tr>';

        try {
            const result = await makeRequest('calculate_settlement', { month });
            if (result.status === 'success') {
                const data = result.data;

                // Update summary
                document.getElementById('settlementSummary').style.display = 'block';
                document.getElementById('totalBazarSettlement').textContent = '৳' + parseFloat(data.total_bazar).toFixed(2);
                document.getElementById('totalMealsSettlement').textContent = parseFloat(data.total_meals).toFixed(2);
                document.getElementById('mealRateSettlement').textContent = '৳' + parseFloat(data.meal_rate).toFixed(2);
                document.getElementById('totalMembersSettlement').textContent = data.settlement.length;

                // Update results table
                if (data.settlement.length === 0) {
                    resultsBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No data found for selected month</td></tr>';
                    return;
                }

                resultsBody.innerHTML = data.settlement.map(item => {
                    const balance = parseFloat(item.balance);
                    const balanceClass = balance < 0 ? 'text-danger' : 'text-success';
                    const status = balance < 0 ? 'Due' : 'Receive';
                    const statusClass = balance < 0 ? 'bg-danger' : 'bg-success';

                    return `
                    <tr>
                        <td>${item.member_name}</td>
                        <td>${parseFloat(item.total_meals).toFixed(2)}</td>
                        <td>৳${parseFloat(item.total_bazar).toFixed(2)}</td>
                        <td>৳${parseFloat(item.meal_cost).toFixed(2)}</td>
                        <td class="fw-bold ${balanceClass}">৳${Math.abs(balance).toFixed(2)} ${status}</td>
                        <td><span class="badge ${statusClass}">${status}</span></td>
                    </tr>
                `;
                }).join('');

                showToast('Settlement calculated successfully', 'success');
            } else {
                showToast(result.message, 'error');
                resultsBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Error calculating settlement</td></tr>';
            }
        } catch (error) {
            showToast('Error calculating settlement', 'error');
            resultsBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Error calculating settlement</td></tr>';
        }
    }
</script>