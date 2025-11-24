<div class="bazar-section fade-in-up">
    <div class="d-flex justify-content-between align-items-start mb-4 flex-column flex-md-row gap-3">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-shopping-cart text-primary me-2"></i>
                Bazar Contributions
            </h4>
            <p class="text-muted mb-0">Your approved and pending shopping expenses</p>
        </div>
        <button class="btn btn-primary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#addBazarRequestModal">
            <i class="fas fa-plus me-2"></i>
            <span>Request Bazar</span>
        </button>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="app-card shadow-sm border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase mb-1 small fw-bold">Approved Spending</p>
                            <h4 class="text-success mb-0" id="approvedSpending">৳0.00</h4>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="app-card shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase mb-1 small fw-bold">Pending Requests</p>
                            <h4 class="text-warning mb-0" id="pendingAmount">৳0.00</h4>
                            <small class="text-muted" id="pendingCount">0 pending item(s)</small>
                        </div>
                        <i class="fas fa-clock fa-2x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="app-card shadow-sm border-start border-4 border-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase mb-1 small fw-bold">Total Records</p>
                            <h4 class="text-info mb-0" id="totalRecords">0</h4>
                            <small class="text-muted">Approved entries</small>
                        </div>
                        <i class="fas fa-list-alt fa-2x text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All Bazar Requests Section -->
    <div class="app-card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>
                All Bazar Requests
            </h5>
            <div class="d-flex gap-2">
                <span class="badge bg-warning" id="pendingBadge">0 Pending</span>
                <span class="badge bg-success" id="processedBadge">0 Processed</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th class="d-none d-md-table-cell">Description</th>
                            <th>Status</th>
                            <th class="d-none d-lg-table-cell">Requested</th>
                        </tr>
                    </thead>
                    <tbody id="bazarRequestsTable">
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>
                                Loading bazar requests...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Approved Contributions -->
    <div class="app-card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="card-title mb-0">
                <i class="fas fa-list-check text-success me-2"></i>
                Approved Contributions
            </h5>
            <span class="badge bg-success" id="approvedRecords">0 records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th class="d-none d-md-table-cell">Description</th>
                            <th class="d-none d-sm-table-cell">Month</th>
                            <th class="d-none d-lg-table-cell">Items</th>
                        </tr>
                    </thead>
                    <tbody id="approvedContributionsTable">
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>
                                Loading approved contributions...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mt-3" id="statisticsCards" style="display: none;">
        <div class="col-md-4">
            <div class="app-card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-calendar-alt text-primary fa-2x mb-3"></i>
                    <h5 class="text-primary" id="approvedEntries">0</h5>
                    <p class="text-muted mb-0">Approved Entries</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="app-card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-money-bill-wave text-success fa-2x mb-3"></i>
                    <h5 class="text-success" id="totalApprovedAmount">৳0.00</h5>
                    <p class="text-muted mb-0">Total Approved Amount</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="app-card text-center shadow-sm">
                <div class="card-body">
                    <i class="fas fa-calculator text-info fa-2x mb-3"></i>
                    <h5 class="text-info" id="averagePerEntry">৳0.00</h5>
                    <p class="text-muted mb-0">Average per Entry</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Bazar Request Modal -->
<div class="modal fade" id="addBazarRequestModal" tabindex="-1" aria-labelledby="addBazarRequestModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addBazarRequestModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Request Bazar Addition
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="addBazarRequestForm">
                <div class="modal-body">
                    <p class="text-muted small mb-3">Your request will be added to the pending list and reviewed by the
                        Admin.</p>

                    <div class="mb-3">
                        <label for="bazarAmount" class="form-label">Amount Spent (৳) <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="bazarAmount" name="amount"
                            required placeholder="0.00">
                        <div class="form-text">Enter the total amount spent on bazar</div>
                    </div>

                    <div class="mb-3">
                        <label for="bazarDate" class="form-label">Date of Expense <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="bazarDate" name="bazar_date" required>
                    </div>

                    <div class="mb-3">
                        <label for="bazarDescription" class="form-label">Description <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="bazarDescription" name="description" rows="3" required
                            placeholder="Please describe what you purchased (e.g., Grocery, Meat, Vegetables, etc.)"></textarea>
                        <div class="form-text">Required: Please provide details of your purchase</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBazarRequestBtn">
                        <i class="fas fa-paper-plane me-2"></i>
                        <span class="submit-text">Submit Request</span>
                        <span class="loading-text d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>Submitting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Global variables
    let bazarModal = null;

    // MakeRequest function - complete implementation
    async function makeRequest(url, data = {}) {
        try {
            console.log('Making request to:', url, 'with data:', data);

            const formData = new FormData();
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    formData.append(key, data[key]);
                }
            }

            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('Request successful:', result);
            return result;

        } catch (error) {
            console.error('Request failed:', error);
            throw error;
        }
    }

    // ShowToast function - complete implementation
    function showToast(message, type = 'info') {
        console.log('Showing toast:', message, type);

        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';

        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Bazar section initialized');

        // Initialize modal
        const modalElement = document.getElementById('addBazarRequestModal');
        if (modalElement && typeof bootstrap !== 'undefined') {
            bazarModal = new bootstrap.Modal(modalElement);
        }

        // Set default date to today
        const bazarDate = document.getElementById('bazarDate');
        if (bazarDate) {
            const today = new Date();
            bazarDate.value = today.toISOString().split('T')[0];
            bazarDate.max = today.toISOString().split('T')[0]; // Prevent future dates
        }

        // Form event listeners
        const form = document.getElementById('addBazarRequestForm');
        if (form) {
            form.addEventListener('submit', handleBazarRequestSubmit);
            form.addEventListener('input', validateForm);
        }

        // Initial validation
        validateForm();

        // Load initial data
        loadAllData();

        // Auto-refresh every 30 seconds
        setInterval(loadAllData, 30000);
    });

    function validateForm() {
        const amount = document.getElementById('bazarAmount')?.value;
        const date = document.getElementById('bazarDate')?.value;
        const description = document.getElementById('bazarDescription')?.value.trim();
        const submitBtn = document.getElementById('submitBazarRequestBtn');

        if (submitBtn) {
            const isValid = amount && parseFloat(amount) > 0 && date && description && description.length > 0;
            submitBtn.disabled = !isValid;
        }
    }

    async function loadAllData() {
        try {
            console.log('Loading all bazar data...');
            await Promise.all([
                loadBazarRequests(),
                loadApprovedContributions(),
                loadStatistics()
            ]);
        } catch (error) {
            console.error('Error loading all data:', error);
            showToast('Error loading data. Please refresh the page.', 'error');
        }
    }

    async function handleBazarRequestSubmit(e) {
        e.preventDefault();
        console.log('Form submission started');

        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitBazarRequestBtn');

        if (!submitBtn) {
            console.error('Submit button not found');
            return;
        }

        const submitText = submitBtn.querySelector('.submit-text');
        const loadingText = submitBtn.querySelector('.loading-text');

        // Get form values
        const amount = parseFloat(formData.get('amount'));
        const date = formData.get('bazar_date');
        const description = formData.get('description').trim();

        // Client-side validation
        if (!amount || amount <= 0) {
            showToast('Please enter a valid amount greater than 0.', 'error');
            return;
        }

        if (!date) {
            showToast('Please select a date.', 'error');
            return;
        }

        if (!description) {
            showToast('Please provide a description of your purchase.', 'error');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        if (submitText) submitText.classList.add('d-none');
        if (loadingText) loadingText.classList.remove('d-none');

        try {
            console.log('Submitting bazar request:', { amount, date, description });

            const result = await makeRequest('user_process/bazar_api.php', {
                amount: amount,
                bazar_date: date,
                description: description
            });

            console.log('Submission result:', result);

            if (result.status === 'success') {
                showToast(result.message, 'success');

                // Close modal and reset form
                if (bazarModal) {
                    bazarModal.hide();
                }
                form.reset();

                // Reset date to today
                const bazarDate = document.getElementById('bazarDate');
                if (bazarDate) {
                    const today = new Date();
                    bazarDate.value = today.toISOString().split('T')[0];
                }

                // Reload all data
                await loadAllData();

            } else {
                showToast(result.message || 'Failed to submit bazar request.', 'error');
            }

        } catch (error) {
            console.error('Bazar Request Error:', error);
            showToast('An unexpected error occurred. Please try again.', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            if (submitText) submitText.classList.remove('d-none');
            if (loadingText) loadingText.classList.add('d-none');
            validateForm();
        }
    }

    async function loadBazarRequests() {
        try {
            console.log('Loading bazar requests...');
            const result = await makeRequest('user_process/bazar_api.php', { action: 'get_bazar_requests' });

            if (result.status === 'success') {
                updateBazarRequestsTable(result.data);
                updateRequestStats(result.data);
            } else {
                console.error('Failed to load bazar requests:', result.message);
                showToast('Failed to load bazar requests', 'error');
            }
        } catch (error) {
            console.error('Error loading bazar requests:', error);
            showToast('Error loading bazar requests', 'error');
        }
    }

    async function loadApprovedContributions() {
        try {
            console.log('Loading approved contributions...');
            const result = await makeRequest('user_process/bazar_api.php', { action: 'get_approved_contributions' });

            if (result.status === 'success') {
                updateApprovedContributionsTable(result.data);
            } else {
                console.error('Failed to load approved contributions:', result.message);
            }
        } catch (error) {
            console.error('Error loading approved contributions:', error);
        }
    }

    async function loadStatistics() {
        try {
            console.log('Loading statistics...');
            const result = await makeRequest('user_process/bazar_api.php', { action: 'get_statistics' });

            if (result.status === 'success') {
                updateStatistics(result.data);
            } else {
                console.error('Failed to load statistics:', result.message);
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }

    function updateRequestStats(requests) {
        const pendingRequests = requests.filter(req => req.status === 'pending');
        const processedRequests = requests.filter(req => req.status !== 'pending');
        const totalPendingAmount = pendingRequests.reduce((sum, req) => sum + parseFloat(req.amount), 0);

        document.getElementById('pendingAmount').textContent = `৳${totalPendingAmount.toFixed(2)}`;
        document.getElementById('pendingCount').textContent = `${pendingRequests.length} pending item(s)`;
        document.getElementById('pendingBadge').textContent = `${pendingRequests.length} Pending`;
        document.getElementById('processedBadge').textContent = `${processedRequests.length} Processed`;
    }

    function updateBazarRequestsTable(requests) {
        const tbody = document.getElementById('bazarRequestsTable');
        if (!tbody) return;

        if (requests.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        No bazar requests found
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = requests.map(request => {
            let status_class = '';
            let is_processed = false;
            let show_rejection = false;

            if (request.status === 'approved') {
                status_class = 'bg-success';
            } else if (request.status === 'rejected') {
                status_class = 'bg-danger';
                is_processed = true;
                show_rejection = request.rejection_reason && request.rejection_reason.trim().length > 0;
            } else if (request.status === 'pending') {
                status_class = 'bg-warning';
            }

            const bazarDate = new Date(request.bazar_date);
            const createdAt = new Date(request.created_at);

            return `
                <tr class="${is_processed ? 'request-processed' : ''}" id="request-${request.id}">
                    <td>${bazarDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                        <span class="fw-bold ${request.status === 'rejected' ? 'text-danger' : 'text-success'}">
                            ৳${parseFloat(request.amount).toFixed(2)}
                        </span>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <div>
                            ${request.description || 'No description provided'}
                            ${show_rejection ? `
                                <div class="rejection-reason mt-1">
                                    <small class="text-danger">
                                        <i class="fas fa-times-circle me-1"></i>
                                        <strong>Rejection Reason:</strong> ${request.rejection_reason}
                                    </small>
                                </div>
                            ` : ''}
                        </div>
                    </td>
                    <td>
                        <span class="badge ${status_class}">${request.status.charAt(0).toUpperCase() + request.status.slice(1)}</span>
                    </td>
                    <td class="d-none d-lg-table-cell">
                        <small class="text-muted">${createdAt.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</small>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updateApprovedContributionsTable(contributions) {
        const tbody = document.getElementById('approvedContributionsTable');
        if (!tbody) return;

        if (contributions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="fas fa-list-alt fa-3x mb-3 d-block"></i>
                        <h5>No Approved Contributions</h5>
                        <p class="text-muted">Your approved bazar entries will appear here.</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = contributions.map(contribution => {
            const bazarDate = new Date(contribution.bazar_date);
            return `
                <tr>
                    <td>
                        <div>
                            <strong>${bazarDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</strong>
                            <small class="text-muted d-block">${bazarDate.getFullYear()}</small>
                        </div>
                    </td>
                    <td>
                        <span class="fw-bold text-success">৳${parseFloat(contribution.amount).toFixed(2)}</span>
                    </td>
                    <td class="d-none d-md-table-cell">
                        ${contribution.description || 'N/A'}
                    </td>
                    <td class="d-none d-sm-table-cell">
                        <span class="badge bg-light text-dark">${contribution.month || 'N/A'}</span>
                    </td>
                    <td class="d-none d-lg-table-cell">
                        <small class="text-muted">${contribution.bazar_count || 'N/A'} items</small>
                    </td>
                </tr>
            `;
        }).join('');

        document.getElementById('approvedRecords').textContent = `${contributions.length} records`;
    }

    function updateStatistics(stats) {
        document.getElementById('approvedSpending').textContent = `৳${parseFloat(stats.total_bazar).toFixed(2)}`;
        document.getElementById('totalRecords').textContent = stats.total_records;
        document.getElementById('approvedEntries').textContent = stats.total_records;
        document.getElementById('totalApprovedAmount').textContent = `৳${parseFloat(stats.total_bazar).toFixed(2)}`;
        document.getElementById('averagePerEntry').textContent = `৳${parseFloat(stats.average_per_entry).toFixed(2)}`;

        // Show statistics cards if there are records
        if (stats.total_records > 0) {
            document.getElementById('statisticsCards').style.display = 'flex';
        }
    }
</script>

<style>
    .bazar-section .app-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .bazar-section .app-card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .bazar-section .app-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .bazar-section .app-table th {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-bottom: 2px solid #dee2e6;
        padding: 1rem;
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bazar-section .app-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.2s ease;
    }

    .bazar-section .app-table tr:hover td {
        background-color: #f8f9fa;
    }

    .bazar-section .request-processed {
        opacity: 0.7;
        background-color: #f8f9fa !important;
    }

    .bazar-section .request-processed:hover td {
        background-color: #e9ecef !important;
    }

    .bazar-section .rejection-reason {
        border-left: 3px solid #dc3545;
        padding-left: 0.75rem;
        margin-top: 0.5rem;
    }

    .bazar-section .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: #6c757d;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .bazar-section .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .bazar-section .btn-lg {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        .bazar-section .app-card {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
        }

        .bazar-section .app-table th,
        .bazar-section .app-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.875rem;
        }
    }

    @media (max-width: 576px) {
        .bazar-section .modal-dialog {
            margin: 0.5rem;
        }

        .bazar-section .app-table {
            font-size: 0.8rem;
        }

        .bazar-section .badge {
            font-size: 0.7rem;
        }

        .bazar-section .rejection-reason {
            font-size: 0.75rem;
        }
    }

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }
</style>