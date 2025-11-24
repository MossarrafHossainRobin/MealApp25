<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0">Flat Rent & Cost Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMonthlyCostModal">
        <i class="fas fa-plus me-2"></i>Add Monthly Cost
    </button>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Monthly Flat Costs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>House Rent</th>
                                <th>Electricity</th>
                                <th>Gas</th>
                                <th>Internet</th>
                                <th>Other Bills</th>
                                <th>Total</th>
                                <th>Per Member</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="monthlyCostsTableBody">
                            <tr>
                                <td colspan="9" class="text-center text-muted">Loading monthly costs...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Allocation Details Modal -->
<div class="modal fade" id="allocationsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allocationsModalTitle">Member Allocations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Rent Amount</th>
                                <th>Utility Share</th>
                                <th>Adjustment</th>
                                <th>Total Due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="allocationsTableBody">
                            <!-- Dynamic content -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Allocation Modal -->
<div class="modal fade" id="editAllocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Allocation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAllocationForm">
                <input type="hidden" name="id" id="editAllocationId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rent Amount</label>
                        <input type="number" step="0.01" class="form-control" name="rent_amount" id="editRentAmount"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Utility Share</label>
                        <input type="number" step="0.01" class="form-control" name="utility_share" id="editUtilityShare"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Special Adjustment</label>
                        <input type="number" step="0.01" class="form-control" name="special_adjustment"
                            id="editSpecialAdjustment" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea class="form-control" name="note" id="editNote" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Allocation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Monthly Cost Modal -->
<div class="modal fade" id="addMonthlyCostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Monthly Flat Cost</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMonthlyCostForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Month *</label>
                        <input type="month" class="form-control" name="month_year" value="<?php echo date('Y-m'); ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">House Rent</label>
                        <input type="number" step="0.01" class="form-control" name="house_rent" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Electricity Bill</label>
                        <input type="number" step="0.01" class="form-control" name="electricity_bill" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gas Bill</label>
                        <input type="number" step="0.01" class="form-control" name="gas_bill" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Internet Bill</label>
                        <input type="number" step="0.01" class="form-control" name="internet_bill" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Aunty Bill</label>
                        <input type="number" step="0.01" class="form-control" name="aunty_bill" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dust Bill</label>
                        <input type="number" step="0.01" class="form-control" name="dust_bill" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Other Bills</label>
                        <input type="number" step="0.01" class="form-control" name="other_bills" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Monthly Cost</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentMonthlyCostId = null;

    document.addEventListener('DOMContentLoaded', function () {
        loadMonthlyCosts();

        // Add monthly cost form submission
        document.getElementById('addMonthlyCostForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const result = await makeRequest('add_monthly_cost', Object.fromEntries(formData));
            if (result.status === 'success') {
                showToast(result.message, 'success');
                $('#addMonthlyCostModal').modal('hide');
                this.reset();
                loadMonthlyCosts();
            } else {
                showToast(result.message, 'error');
            }
        });

        // Edit allocation form submission
        document.getElementById('editAllocationForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const result = await makeRequest('update_allocation', Object.fromEntries(formData));
            if (result.status === 'success') {
                showToast(result.message, 'success');
                $('#editAllocationModal').modal('hide');
                if (currentMonthlyCostId) {
                    loadAllocations(currentMonthlyCostId);
                }
            } else {
                showToast(result.message, 'error');
            }
        });
    });

    async function loadMonthlyCosts() {
        try {
            const result = await makeRequest('get_monthly_costs');
            if (result.status === 'success') {
                const tbody = document.getElementById('monthlyCostsTableBody');
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No monthly costs found</td></tr>';
                    return;
                }

                tbody.innerHTML = result.data.map(cost => `
                <tr>
                    <td>${new Date(cost.month_year + '-01').toLocaleDateString('en-US', { year: 'numeric', month: 'long' })}</td>
                    <td>৳${parseFloat(cost.house_rent).toFixed(2)}</td>
                    <td>৳${parseFloat(cost.electricity_bill).toFixed(2)}</td>
                    <td>৳${parseFloat(cost.gas_bill).toFixed(2)}</td>
                    <td>৳${parseFloat(cost.internet_bill).toFixed(2)}</td>
                    <td>৳${parseFloat(parseFloat(cost.aunty_bill) + parseFloat(cost.dust_bill) + parseFloat(cost.other_bills)).toFixed(2)}</td>
                    <td class="fw-bold text-primary">৳${parseFloat(cost.total_amount).toFixed(2)}</td>
                    <td class="fw-bold text-success">৳${parseFloat(cost.per_member_cost).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-info me-1" onclick="viewAllocations(${cost.id}, '${new Date(cost.month_year + '-01').toLocaleDateString('en-US', { year: 'numeric', month: 'long' })}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteMonthlyCost(${cost.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            } else {
                showToast('Error loading monthly costs', 'error');
            }
        } catch (error) {
            console.error('Error loading monthly costs:', error);
            showToast('Error loading monthly costs', 'error');
        }
    }

    async function viewAllocations(monthlyCostId, monthName) {
        currentMonthlyCostId = monthlyCostId;
        document.getElementById('allocationsModalTitle').textContent = `Member Allocations - ${monthName}`;

        try {
            const result = await makeRequest('get_allocations', { monthly_cost_id: monthlyCostId });
            if (result.status === 'success') {
                const tbody = document.getElementById('allocationsTableBody');
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No allocations found</td></tr>';
                } else {
                    tbody.innerHTML = result.data.map(alloc => `
                    <tr>
                        <td>${alloc.member_name}</td>
                        <td>৳${parseFloat(alloc.rent_amount).toFixed(2)}</td>
                        <td>৳${parseFloat(alloc.utility_share).toFixed(2)}</td>
                        <td>৳${parseFloat(alloc.special_adjustment || 0).toFixed(2)}</td>
                        <td class="fw-bold ${alloc.amount_due > 0 ? 'text-danger' : 'text-success'}">
                            ৳${Math.abs(parseFloat(alloc.amount_due)).toFixed(2)}
                        </td>
                        <td>
                            <span class="badge ${alloc.status === 'paid' ? 'bg-success' : alloc.status === 'partial' ? 'bg-warning' : 'bg-danger'}">
                                ${alloc.status || 'pending'}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editAllocation(${alloc.id}, ${alloc.rent_amount}, ${alloc.utility_share}, ${alloc.special_adjustment || 0}, '${alloc.note || ''}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success me-1" onclick="updateAllocationStatus(${alloc.id}, 'paid')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="updateAllocationStatus(${alloc.id}, 'pending')">
                                <i class="fas fa-clock"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
                }
                $('#allocationsModal').modal('show');
            } else {
                showToast('Error loading allocations', 'error');
            }
        } catch (error) {
            console.error('Error loading allocations:', error);
            showToast('Error loading allocations', 'error');
        }
    }

    function editAllocation(id, rentAmount, utilityShare, specialAdjustment, note) {
        document.getElementById('editAllocationId').value = id;
        document.getElementById('editRentAmount').value = rentAmount;
        document.getElementById('editUtilityShare').value = utilityShare;
        document.getElementById('editSpecialAdjustment').value = specialAdjustment;
        document.getElementById('editNote').value = note;
        $('#editAllocationModal').modal('show');
    }

    async function updateAllocationStatus(id, status) {
        const result = await makeRequest('update_allocation_status', { id, status });
        if (result.status === 'success') {
            showToast(result.message, 'success');
            if (currentMonthlyCostId) {
                loadAllocations(currentMonthlyCostId);
            }
        } else {
            showToast(result.message, 'error');
        }
    }

    async function deleteMonthlyCost(id) {
        if (confirm('Are you sure you want to delete this monthly cost? This will also delete all allocations.')) {
            const result = await makeRequest('delete_monthly_cost', { id });
            if (result.status === 'success') {
                showToast(result.message, 'success');
                loadMonthlyCosts();
            } else {
                showToast(result.message, 'error');
            }
        }
    }
</script>