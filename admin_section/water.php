<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0">Water Duty Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWaterDutyModal">
        <i class="fas fa-plus me-2"></i>Assign Water Duty
    </button>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Water Duty Schedule</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Member</th>
                        <th>Status</th>
                        <th>Assigned At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="waterDutyTableBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted">Loading water duties...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Water Duty Modal -->
<div class="modal fade" id="addWaterDutyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Water Duty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addWaterDutyForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Member *</label>
                        <select class="form-select" name="member_id" id="waterDutyMemberSelect" required>
                            <option value="">Loading members...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date *</label>
                        <input type="date" class="form-control" name="duty_date" value="<?php echo date('Y-m-d'); ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Time *</label>
                        <input type="time" class="form-control" name="duty_time" value="09:00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Duty</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        loadWaterDuties();
        loadMembersForWaterDuty();

        // Add water duty form submission
        document.getElementById('addWaterDutyForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const result = await makeRequest('add_water_duty', Object.fromEntries(formData));
            if (result.status === 'success') {
                showToast(result.message, 'success');
                $('#addWaterDutyModal').modal('hide');
                this.reset();
                loadWaterDuties();
            } else {
                showToast(result.message, 'error');
            }
        });
    });

    async function loadWaterDuties() {
        try {
            const result = await makeRequest('get_water_duties');
            if (result.status === 'success') {
                const tbody = document.getElementById('waterDutyTableBody');
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No water duties found</td></tr>';
                    return;
                }

                tbody.innerHTML = result.data.map(duty => `
                <tr>
                    <td>${new Date(duty.duty_date).toLocaleDateString()}</td>
                    <td>${duty.duty_time}</td>
                    <td>${duty.member_name || 'Unknown'}</td>
                    <td>
                        <span class="badge ${duty.status === 'completed' ? 'bg-success' : 'bg-warning'}">
                            ${duty.status}
                        </span>
                    </td>
                    <td>${new Date(duty.assigned_at).toLocaleString()}</td>
                    <td>
                        ${duty.status === 'pending' ? `
                            <button class="btn btn-sm btn-outline-success me-1" onclick="updateWaterDutyStatus(${duty.id}, 'completed')">
                                <i class="fas fa-check"></i>
                            </button>
                        ` : ''}
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteWaterDuty(${duty.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            } else {
                showToast('Error loading water duties', 'error');
            }
        } catch (error) {
            console.error('Error loading water duties:', error);
            showToast('Error loading water duties', 'error');
        }
    }

    async function loadMembersForWaterDuty() {
        try {
            const result = await makeRequest('get_members');
            if (result.status === 'success') {
                const select = document.getElementById('waterDutyMemberSelect');
                select.innerHTML = '<option value="">Select Member</option>';
                result.data.forEach(member => {
                    select.innerHTML += `<option value="${member.id}">${member.name}</option>`;
                });
            }
        } catch (error) {
            console.error('Error loading members:', error);
        }
    }

    async function deleteWaterDuty(id) {
        if (confirm('Are you sure you want to delete this water duty?')) {
            const result = await makeRequest('delete_water_duty', { id });
            if (result.status === 'success') {
                showToast(result.message, 'success');
                loadWaterDuties();
            } else {
                showToast(result.message, 'error');
            }
        }
    }

    async function updateWaterDutyStatus(id, status) {
        const result = await makeRequest('update_water_duty_status', { id, status });
        if (result.status === 'success') {
            showToast(result.message, 'success');
            loadWaterDuties();
        } else {
            showToast(result.message, 'error');
        }
    }
</script>