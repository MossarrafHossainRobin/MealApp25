<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0">Bazar Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBazarModal">
        <i class="fas fa-plus me-2"></i>Add Bazar Entry
    </button>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Bazar Entries</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Member</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="bazarTableBody">
                    <tr>
                        <td colspan="5" class="text-center text-muted">Loading bazar entries...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Bazar Modal -->
<div class="modal fade" id="addBazarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Bazar Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addBazarForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Member *</label>
                        <select class="form-select" name="member_id" id="bazarMemberSelect" required>
                            <option value="">Loading members...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount *</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date *</label>
                        <input type="date" class="form-control" name="bazar_date" value="<?php echo date('Y-m-d'); ?>"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        loadBazarEntries();
        loadMembersForBazar();

        // Add bazar form submission
        document.getElementById('addBazarForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const result = await makeRequest('add_bazar', Object.fromEntries(formData));
            if (result.status === 'success') {
                showToast(result.message, 'success');
                $('#addBazarModal').modal('hide');
                this.reset();
                loadBazarEntries();
            } else {
                showToast(result.message, 'error');
            }
        });
    });

    async function loadBazarEntries() {
        try {
            const result = await makeRequest('get_bazar');
            if (result.status === 'success') {
                const tbody = document.getElementById('bazarTableBody');
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No bazar entries found</td></tr>';
                    return;
                }

                tbody.innerHTML = result.data.map(entry => `
                <tr>
                    <td>${new Date(entry.bazar_date).toLocaleDateString()}</td>
                    <td>${entry.member_name || 'Unknown'}</td>
                    <td>${entry.description || '-'}</td>
                    <td class="fw-bold text-success">৳${parseFloat(entry.amount).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteBazar(${entry.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            } else {
                showToast('Error loading bazar entries', 'error');
            }
        } catch (error) {
            console.error('Error loading bazar entries:', error);
            showToast('Error loading bazar entries', 'error');
        }
    }

    async function loadMembersForBazar() {
        try {
            const result = await makeRequest('get_members');
            if (result.status === 'success') {
                const select = document.getElementById('bazarMemberSelect');
                select.innerHTML = '<option value="">Select Member</option>';
                result.data.forEach(member => {
                    select.innerHTML += `<option value="${member.id}">${member.name}</option>`;
                });
            }
        } catch (error) {
            console.error('Error loading members:', error);
        }
    }

    async function deleteBazar(id) {
        if (confirm('Are you sure you want to delete this bazar entry?')) {
            const result = await makeRequest('delete_bazar', { id });
            if (result.status === 'success') {
                showToast(result.message, 'success');
                loadBazarEntries();
            } else {
                showToast(result.message, 'error');
            }
        }
    }
</script>