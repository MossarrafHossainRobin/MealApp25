<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0">Meal Count Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMealModal">
        <i class="fas fa-plus me-2"></i>Add Meal Count
    </button>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Meal Counts</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Member</th>
                        <th>Meal Count</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="mealTableBody">
                    <tr>
                        <td colspan="5" class="text-center text-muted">Loading meal counts...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Meal Modal -->
<div class="modal fade" id="addMealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Meal Count</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMealForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Member *</label>
                        <select class="form-select" name="member_id" id="mealMemberSelect" required>
                            <option value="">Loading members...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meal Count *</label>
                        <input type="number" step="0.5" class="form-control" name="meal_count" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date *</label>
                        <input type="date" class="form-control" name="meal_date" value="<?php echo date('Y-m-d'); ?>"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Meal Count</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        loadMealCounts();
        loadMembersForMeal();

        // Add meal form submission
        document.getElementById('addMealForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const result = await makeRequest('add_meal', Object.fromEntries(formData));
            if (result.status === 'success') {
                showToast(result.message, 'success');
                $('#addMealModal').modal('hide');
                this.reset();
                loadMealCounts();
            } else {
                showToast(result.message, 'error');
            }
        });
    });

    async function loadMealCounts() {
        try {
            const result = await makeRequest('get_meals');
            if (result.status === 'success') {
                const tbody = document.getElementById('mealTableBody');
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No meal counts found</td></tr>';
                    return;
                }

                tbody.innerHTML = result.data.map(meal => `
                <tr>
                    <td>${new Date(meal.meal_date).toLocaleDateString()}</td>
                    <td>${meal.member_name || 'Unknown'}</td>
                    <td class="fw-bold text-primary">${parseFloat(meal.meal_count).toFixed(2)}</td>
                    <td>${meal.description || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteMeal(${meal.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            } else {
                showToast('Error loading meal counts', 'error');
            }
        } catch (error) {
            console.error('Error loading meal counts:', error);
            showToast('Error loading meal counts', 'error');
        }
    }

    async function loadMembersForMeal() {
        try {
            const result = await makeRequest('get_members');
            if (result.status === 'success') {
                const select = document.getElementById('mealMemberSelect');
                select.innerHTML = '<option value="">Select Member</option>';
                result.data.forEach(member => {
                    select.innerHTML += `<option value="${member.id}">${member.name}</option>`;
                });
            }
        } catch (error) {
            console.error('Error loading members:', error);
        }
    }

    async function deleteMeal(id) {
        if (confirm('Are you sure you want to delete this meal count?')) {
            const result = await makeRequest('delete_meal', { id });
            if (result.status === 'success') {
                showToast(result.message, 'success');
                loadMealCounts();
            } else {
                showToast(result.message, 'error');
            }
        }
    }
</script>