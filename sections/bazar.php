<div class="container mt-4">
    <h3 class="text-primary mb-4">🏪 Bazar Management System</h3>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">💰 Total Bazar</h6>
                            <h3 class="mb-0" id="totalBazarAmount">৳0.00</h3>
                            <small id="totalEntriesText">0 entries</small>
                        </div>
                        <div class="display-4">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">📅 Current Month</h6>
                            <h4 class="mb-0" id="currentMonthDisplay">January 2024</h4>
                            <small id="monthSummary">No data</small>
                        </div>
                        <div class="display-4">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Bazar Info -->
    <div class="alert alert-info mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>📅 Last Bazar:</strong>
                <span id="lastBazarInfo" class="ms-2">No bazar data yet</span>
            </div>
            <div class="text-muted small" id="lastBazarTime"></div>
        </div>
    </div>

    <!-- Month/Year Selector -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h6 class="card-title text-secondary mb-3">📊 Select Month & Year</h6>
            <div class="row g-3">
                <div class="col-md-5">
                    <select class="form-select form-select-sm" id="monthSelect">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <select class="form-select form-select-sm" id="yearSelect">
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-sm w-100" onclick="loadBazarData()">
                        <i class="bi bi-arrow-clockwise"></i> Load
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bazar Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h6 class="card-title text-success mb-3">➕ Add New Bazar Entry</h6>
            <form onsubmit="addBazar(event)">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="member_id" required>
                            <option value="">👤 Select Member</option>
                            <?php foreach ($all_members as $member): ?>
                                <option value="<?php echo $member['id']; ?>">
                                    <?php echo $member['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control form-control-sm" name="amount" step="0.01"
                            placeholder="💰 Amount" required>
                        <small class="text-muted">Use negative for refund</small>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control form-control-sm" name="bazar_count" value="1" min="0"
                            placeholder="🔢 Count">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" name="bazar_date" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-sm" name="description"
                            placeholder="📝 Description (Optional)">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-plus-circle"></i> Add
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bazar Data -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="card-title text-primary mb-0">
                    📋 Bazar Data for <span id="currentMonthYear" class="fw-bold"></span>
                </h6>
                <span class="badge bg-info" id="totalEntriesBadge">0 entries</span>
            </div>
            <div id="bazarData"></div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">✏️ Edit Bazar Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" name="bazar_id" id="editBazarId">
                    <input type="hidden" name="action" value="edit_bazar">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">👤 Member</label>
                        <select class="form-select" name="member_id" id="editMemberId" required>
                            <option value="">Select Member</option>
                            <?php foreach ($all_members as $member): ?>
                                <option value="<?php echo $member['id']; ?>">
                                    <?php echo $member['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">💰 Amount</label>
                            <input type="number" class="form-control" name="amount" id="editAmount" step="0.01"
                                required>
                            <small class="text-muted">Use negative for refund</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">🔢 Count</label>
                            <input type="number" class="form-control" name="bazar_count" id="editBazarCount" min="0"
                                value="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">📅 Date</label>
                        <input type="date" class="form-control" name="bazar_date" id="editBazarDate" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">📝 Description</label>
                        <textarea class="form-control" name="description" id="editDescription" rows="2"
                            placeholder="Enter description..."></textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">🗑️ Delete Bazar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <h6 class="fw-bold">Are you sure?</h6>
                <p class="text-muted mb-3" id="deleteConfirmText">This action cannot be undone.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables
    let currentBazarData = [];
    let bazarToDelete = null;

    // Set current month/year as default
    document.addEventListener('DOMContentLoaded', function () {
        const now = new Date();
        document.getElementById('monthSelect').value = now.getMonth() + 1;
        document.getElementById('yearSelect').value = now.getFullYear();
        document.querySelector('[name="bazar_date"]').value = now.toISOString().split('T')[0];
        updateCurrentMonthDisplay();
        loadBazarData();
    });

    // Update current month display
    function updateCurrentMonthDisplay() {
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearSelect').value;
        document.getElementById('currentMonthDisplay').textContent = `${getMonthName(month)} ${year}`;
    }

    // Load bazar data
    function loadBazarData() {
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearSelect').value;

        document.getElementById('currentMonthYear').textContent = `${getMonthName(month)} ${year}`;
        updateCurrentMonthDisplay();

        fetch(`process/get_bazar_data.php?month=${month}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                console.log('📊 Bazar Data:', data);

                if (data.success) {
                    currentBazarData = data.entries || [];
                    displayBazarData(currentBazarData);
                    updateLastBazarInfo(currentBazarData);
                    updateTotalBazarStats(data);
                    updateTotalEntriesBadge(data.total_entries || 0);
                } else {
                    document.getElementById('bazarData').innerHTML = `
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle"></i> Error: ${data.error}
                        </div>`;
                    resetStats();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('bazarData').innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-wifi-off"></i> Network error occurred
                    </div>`;
                resetStats();
            });
    }

    // Update total bazar statistics
    function updateTotalBazarStats(data) {
        const totalAmount = data.total_amount || 0;
        const totalEntries = data.total_entries || 0;

        // Update total bazar amount
        const totalBazarElement = document.getElementById('totalBazarAmount');
        totalBazarElement.textContent = `৳${Math.abs(totalAmount).toFixed(2)}`;

        // Color coding for positive/negative amounts
        if (totalAmount < 0) {
            totalBazarElement.className = 'mb-0 text-danger';
            totalBazarElement.innerHTML = `-৳${Math.abs(totalAmount).toFixed(2)}`;
        } else {
            totalBazarElement.className = 'mb-0 text-white';
            totalBazarElement.innerHTML = `৳${totalAmount.toFixed(2)}`;
        }

        // Update entries text
        document.getElementById('totalEntriesText').textContent = `${totalEntries} entries`;

        // Update month summary
        const monthSummary = document.getElementById('monthSummary');
        if (totalEntries > 0) {
            const avgAmount = totalAmount / totalEntries;
            monthSummary.textContent = `${totalEntries} entries • Avg: ৳${avgAmount.toFixed(2)}`;
        } else {
            monthSummary.textContent = 'No data available';
        }
    }

    // Reset stats when no data
    function resetStats() {
        document.getElementById('totalBazarAmount').textContent = '৳0.00';
        document.getElementById('totalBazarAmount').className = 'mb-0 text-white';
        document.getElementById('totalEntriesText').textContent = '0 entries';
        document.getElementById('monthSummary').textContent = 'No data';
    }

    // Update total entries badge
    function updateTotalEntriesBadge(count) {
        document.getElementById('totalEntriesBadge').textContent = `${count} entries`;
    }

    // Update last bazar info
    function updateLastBazarInfo(entries) {
        const lastBazarInfo = document.getElementById('lastBazarInfo');
        const lastBazarTime = document.getElementById('lastBazarTime');

        if (entries && entries.length > 0) {
            const lastEntry = entries[0];
            const description = lastEntry.description ? ` - ${lastEntry.description}` : '';
            const date = new Date(lastEntry.bazar_date);
            const amount = parseFloat(lastEntry.amount);

            // Color code based on amount
            const amountClass = amount < 0 ? 'text-danger' : 'text-success';
            const amountSign = amount < 0 ? '-' : '';

            lastBazarInfo.innerHTML = `
                <span class="fw-bold text-primary">${lastEntry.member_name}</span> 
                gave <span class="fw-bold ${amountClass}">${amountSign}৳${Math.abs(amount).toFixed(2)}</span>
                ${description}
            `;
            lastBazarTime.textContent = date.toLocaleDateString();
        } else {
            lastBazarInfo.textContent = 'No bazar data yet';
            lastBazarTime.textContent = '';
        }
    }

    // Display bazar data with edit buttons
    function displayBazarData(entries) {
        const container = document.getElementById('bazarData');

        if (!entries || entries.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                    <p class="mt-2">No bazar entries found for this month.</p>
                </div>`;
            return;
        }

        let html = `
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>📅 Date</th>
                        <th>👤 Member</th>
                        <th class="text-end">💰 Amount</th>
                        <th class="text-center">🔢 Count</th>
                        <th>📝 Description</th>
                        <th class="text-center">⚡ Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;

        entries.forEach(entry => {
            const amount = parseFloat(entry.amount);
            const amountClass = amount < 0 ? 'text-danger fw-bold' : 'text-success fw-bold';
            const amountSign = amount < 0 ? '-' : '';
            const amountDisplay = `${amountSign}৳${Math.abs(amount).toFixed(2)}`;

            html += `
            <tr>
                <td class="text-nowrap">
                    <small>${new Date(entry.bazar_date).toLocaleDateString()}</small>
                </td>
                <td class="fw-semibold">${entry.member_name}</td>
                <td class="text-end ${amountClass}">${amountDisplay}</td>
                <td class="text-center">
                    <span class="badge bg-secondary">${entry.bazar_count || 0}</span>
                </td>
                <td>
                    <small class="text-muted">${entry.description || 'No description'}</small>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editBazar(${entry.id})" 
                                title="Edit entry">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="showDeleteConfirm(${entry.id}, '${entry.member_name}', ${entry.amount})" 
                                title="Delete entry">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        });

        html += '</tbody></table></div>';
        container.innerHTML = html;
    }

    // Add new bazar entry
    function addBazar(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        formData.append('action', 'add_bazar');

        fetch('process/actions.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('✅ Bazar added successfully!', 'success');
                    form.reset();
                    document.querySelector('[name="bazar_date"]').value = new Date().toISOString().split('T')[0];
                    loadBazarData();
                } else {
                    showMessage('❌ Error: ' + data.error, 'error');
                }
            })
            .catch(error => {
                showMessage('❌ Network error occurred', 'error');
            });
    }

    // Edit bazar entry
    function editBazar(bazarId) {
        const entry = currentBazarData.find(item => item.id == bazarId);
        if (!entry) return;

        // Populate edit form
        document.getElementById('editBazarId').value = entry.id;
        document.getElementById('editMemberId').value = entry.member_id;
        document.getElementById('editAmount').value = entry.amount;
        document.getElementById('editBazarCount').value = entry.bazar_count || 0;
        document.getElementById('editBazarDate').value = entry.bazar_date;
        document.getElementById('editDescription').value = entry.description || '';

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    }

    // Update bazar entry
    document.getElementById('editForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('process/actions.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('✅ Bazar updated successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                    loadBazarData();
                } else {
                    showMessage('❌ Error: ' + data.error, 'error');
                }
            })
            .catch(error => {
                showMessage('❌ Network error occurred', 'error');
            });
    });

    // Show delete confirmation modal
    function showDeleteConfirm(bazarId, memberName, amount) {
        bazarToDelete = bazarId;
        const amountDisplay = amount < 0 ? `-৳${Math.abs(amount).toFixed(2)}` : `৳${amount.toFixed(2)}`;
        document.getElementById('deleteConfirmText').innerHTML = `
            Delete bazar entry for <strong>${memberName}</strong> 
            (${amountDisplay})?<br>
            <small class="text-muted">This action cannot be undone.</small>
        `;

        const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        modal.show();
    }

    // Confirm delete action
    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (!bazarToDelete) return;

        const formData = new FormData();
        formData.append('action', 'delete_bazar');
        formData.append('bazar_id', bazarToDelete);

        fetch('process/actions.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal')).hide();
                if (data.success) {
                    showMessage('✅ Bazar deleted successfully!', 'success');
                    loadBazarData();
                } else {
                    showMessage('❌ Error: ' + data.error, 'error');
                }
            })
            .catch(error => {
                bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal')).hide();
                showMessage('❌ Network error occurred', 'error');
            });

        bazarToDelete = null;
    });

    // Utility function
    function getMonthName(month) {
        const months = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
        return months[month - 1];
    }

    // Show message without alert
    function showMessage(message, type) {
        // Remove existing messages
        const existingMsg = document.querySelector('.alert-message');
        if (existingMsg) {
            existingMsg.remove();
        }

        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? '✅' : '❌';

        const messageDiv = document.createElement('div');
        messageDiv.className = `alert ${alertClass} alert-message position-fixed`;
        messageDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        messageDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <span class="me-2">${icon}</span>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(messageDiv);

        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 4000);
    }
</script>

<style>
    .card {
        border: none;
        border-radius: 12px;
    }

    .card-body {
        padding: 1.25rem;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .btn-group-sm>.btn {
        padding: 0.25rem 0.5rem;
    }

    .alert-message {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 8px;
    }

    .badge {
        font-size: 0.75rem;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .text-success {
        color: #198754 !important;
    }
</style>