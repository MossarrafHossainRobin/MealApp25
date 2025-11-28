<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bazar Contributions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --app-primary: #f04e9c;
            --app-secondary: #008080;
            --app-success: #28a745;
            --app-danger: #dc3545;
            --app-info: #17a2b8;
            --app-gray: #f8f9fa;
            --app-bg: #f7f7f7;
            --app-card-bg: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: var(--app-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            color: #333;
            /* Prevent unwanted scrolling */
            overflow-x: hidden;
        }

        .bazar-section {
            max-width: 100%;
            overflow-x: hidden;
            padding-bottom: 1rem;
            /* Ensure no margin/padding issues */
            margin: 0;
        }

        /* App Header - FIXED POSITIONING */
        .app-header {
            background: linear-gradient(135deg, var(--app-primary), #e04590);
            color: white;
            padding: 1rem 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
            /* Ensure no margin collapse issues */
            margin: 0;
        }

        .app-header h4 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .app-header p {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        /* Stats Cards */
        .stats-container {
            margin-top: -1rem;
            padding: 0 1rem;
            position: relative;
            z-index: 20;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 0.75rem;
            margin-bottom: 1rem;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
        }

        .stat-item {
            text-align: center;
            flex: 1;
            padding: 0.5rem 0.25rem;
        }

        .stat-item:not(:last-child) {
            border-right: 1px solid #f0f0f0;
        }

        .stat-item i {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-item .stat-label {
            font-size: 0.7rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
            display: block;
        }

        .stat-item .stat-value {
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .stat-item .stat-subtext {
            font-size: 0.65rem;
            color: #888;
            margin-top: 0.25rem;
        }

        /* Action Button */
        .action-btn {
            background: var(--app-primary);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            padding: 0.875rem 1rem;
            width: calc(100% - 2rem);
            margin: 0.5rem 1rem 1rem;
            box-shadow: 0 4px 10px rgba(240, 78, 156, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .action-btn:active {
            transform: scale(0.98);
            box-shadow: 0 2px 5px rgba(240, 78, 156, 0.3);
        }

        .action-btn i {
            margin-right: 0.5rem;
        }

        /* Content Area */
        .content-area {
            padding: 0 1rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .badge-container {
            display: flex;
            gap: 0.5rem;
        }

        .status-badge {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-weight: 600;
        }

        /* Transaction List */
        .transaction-list {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .transaction-item {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        .transaction-details {
            flex: 1;
            margin-right: 0.75rem;
        }

        .transaction-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }

        .transaction-meta {
            display: flex;
            align-items: center;
            font-size: 0.75rem;
            color: #666;
        }

        .transaction-meta i {
            margin-right: 0.25rem;
            font-size: 0.7rem;
        }

        .transaction-amount {
            text-align: right;
            min-width: 70px;
        }

        .amount-value {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        /* Status Badges */
        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-badge.approved {
            background-color: #d1edff;
            color: #0c5460;
        }

        .status-badge.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: var(--app-primary);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 1.25rem 1.5rem;
            border-bottom: none;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .input-group-text {
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .form-text {
            font-size: 0.75rem;
        }

        .modal-footer {
            border-top: 1px solid #eee;
            padding: 1rem 1.5rem;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.25rem;
        }

        /* Toast Styling */
        .toast-container {
            z-index: 9999;
        }

        /* Loading States */
        .loading-placeholder {
            padding: 2rem 1rem;
            text-align: center;
            color: #888;
        }

        .loading-placeholder i {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        /* ========== DESKTOP STYLES ========== */
        @media (min-width: 992px) {
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
                min-height: 100vh;
                padding: 2rem 0;
            }

            .bazar-section {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                padding: 0;
            }

            .app-header {
                padding: 2rem 2.5rem 2.5rem;
                border-radius: 20px 20px 0 0;
                background: linear-gradient(135deg, var(--app-primary), #e04590);
                position: relative;
                overflow: hidden;
                margin: 0;
                /* Ensure no margin */
            }

            .app-header::before {
                content: "";
                position: absolute;
                top: 0;
                right: 0;
                width: 200px;
                height: 200px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                transform: translate(30%, -30%);
            }

            .app-header h4 {
                font-size: 1.75rem;
                position: relative;
                z-index: 2;
            }

            .app-header p {
                font-size: 1rem;
                position: relative;
                z-index: 2;
            }

            .stats-container {
                padding: 0 2.5rem;
                margin-top: -2rem;
            }

            .stats-card {
                padding: 1.5rem;
                border-radius: 16px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                /* Ensure this is the desired gap between stats card and action button on desktop */
                margin-bottom: 2rem;
                background: var(--app-card-bg);
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .stat-item {
                padding: 0.5rem 1rem;
            }

            .stat-item i {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                color: var(--app-primary);
            }

            .stat-item .stat-label {
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }

            .stat-item .stat-value {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }

            .stat-item .stat-subtext {
                font-size: 0.8rem;
            }

            .action-btn {
                margin: 0 2.5rem 2rem;
                /* Keeps 2rem gap below the button */
                padding: 1rem 1.5rem;
                font-size: 1.1rem;
                border-radius: 12px;
                transition: all 0.3s ease;
                width: calc(100% - 5rem);
            }

            .action-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(240, 78, 156, 0.4);
            }

            .content-area {
                /* Ensures content area doesn't get unnecessary top margin from mobile styles */
                padding: 0 2.5rem 2rem;
                margin-top: 0;
            }

            .section-header {
                margin-bottom: 1.5rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .status-badge {
                font-size: 0.85rem;
                padding: 0.5rem 0.75rem;
            }

            .transaction-list {
                border-radius: 16px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .transaction-item {
                padding: 1.25rem 1.5rem;
                transition: all 0.2s ease;
            }

            .transaction-item:hover {
                background-color: #f9f9f9;
                transform: translateX(5px);
            }

            .transaction-title {
                font-size: 1.1rem;
            }

            .transaction-meta {
                font-size: 0.9rem;
            }

            .transaction-meta i {
                font-size: 0.85rem;
            }

            .amount-value {
                font-size: 1.2rem;
            }

            /* Desktop Table View */
            .desktop-table {
                display: table;
                width: 100%;
                border-collapse: collapse;
            }

            .desktop-table thead {
                background: linear-gradient(to right, #f8f9fa, #e9ecef);
            }

            .desktop-table th {
                padding: 1rem 1.5rem;
                text-align: left;
                font-weight: 600;
                color: #495057;
                border-bottom: 2px solid #dee2e6;
                font-size: 0.9rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .desktop-table td {
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid #e9ecef;
                vertical-align: middle;
            }

            .desktop-table tbody tr {
                transition: all 0.2s ease;
            }

            .desktop-table tbody tr:hover {
                background-color: #f8f9fa;
            }

            .desktop-table .status-cell {
                min-width: 120px;
            }

            /* Modal adjustments for desktop */
            .modal-dialog {
                max-width: 600px;
            }

            .modal-header {
                padding: 1.5rem 2rem;
            }

            .modal-title {
                font-size: 1.3rem;
            }

            .modal-body {
                padding: 2rem;
            }

            .form-label {
                font-size: 1rem;
            }

            .form-control,
            .input-group-text {
                font-size: 1rem;
                padding: 0.75rem 1rem;
            }

            .form-text {
                font-size: 0.85rem;
            }

            .modal-footer {
                padding: 1.5rem 2rem;
            }

            .btn {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
            }
        }

        /* Extra small devices */
        @media (max-width: 360px) {

            .app-header,
            .stats-container,
            .content-area {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .action-btn {
                margin-left: 0.75rem;
                margin-right: 0.75rem;
                width: calc(100% - 1.5rem);
            }

            .transaction-item {
                padding: 0.75rem;
            }
        }

        /* Animation for new items */
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

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* Toggle between mobile and desktop views */
        .mobile-only {
            display: block;
        }

        .desktop-only {
            display: none;
        }

        @media (min-width: 992px) {
            .mobile-only {
                display: none;
            }

            .desktop-only {
                display: block;
            }
        }
    </style>
</head>

<body>
    <div class="bazar-section">
        <!-- App Header -->
        <div class="app-header">
            <h4>
                <i class="fas fa-shopping-cart me-2"></i>
                Bazar Contributions
            </h4>
            <p id="monthNameDisplay">Your Bazar History will appear here</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stats-card">
                <div class="stat-row">
                    <div class="stat-item text-success">
                        <i class="fas fa-check-circle"></i>
                        <span class="stat-label">Total Bazar</span>
                        <div class="stat-value" id="approvedSpending">৳0.00</div>
                    </div>
                    <div class="stat-item text-info">
                        <i class="fas fa-list-alt"></i>
                        <span class="stat-label">Total Count</span>
                        <div class="stat-value" id="totalRecords">0</div>
                        <div class="stat-subtext" id="monthName">Current Month</div>
                    </div>
                    <div class="stat-item text-danger">
                        <i class="fas fa-crown"></i>
                        <span class="stat-label">Highest Bearer</span>
                        <div class="stat-value" id="highestBearerName">-</div>
                        <div class="stat-value" id="highestBearerAmount">৳0.00</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <button class="action-btn" data-bs-toggle="modal" data-bs-target="#addBazarRequestModal">
            <i class="fas fa-plus"></i>
            Request New Bazar
        </button>

        <!-- Content Area -->
        <div class="content-area">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-history me-1"></i>
                    Transaction History
                </h2>
                <div class="badge-container">
                    <span class="status-badge pending" id="pendingBadge">0</span>
                    <span class="status-badge approved" id="processedBadge">0</span>
                </div>
            </div>

            <!-- Mobile Transaction List -->
            <div class="mobile-only">
                <div class="transaction-list">
                    <div id="bazarRequestsMobileList">
                        <div class="loading-placeholder" id="mobileListLoader">
                            <i class="fas fa-spinner fa-spin"></i>
                            Loading bazar requests...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="desktop-only">
                <div class="transaction-list">
                    <div class="table-responsive">
                        <table class="desktop-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th class="status-cell">Status</th>
                                    <th>Requested</th>
                                </tr>
                            </thead>
                            <tbody id="bazarRequestsDesktopTable">
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin fa-lg me-2"></i>
                                        Loading bazar requests...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addBazarRequestModal" tabindex="-1" aria-labelledby="addBazarRequestModalLabel"
            aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBazarRequestModalLabel">
                            <i class="fas fa-plus-circle me-2"></i>Request Bazar Addition
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="addBazarRequestForm">
                        <div class="modal-body">
                            <p class="text-muted small mb-3">Your request will be added to the pending list and reviewed
                                by the Admin.</p>

                            <div class="mb-3">
                                <label for="bazarAmount" class="form-label">Amount Spent (৳) <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0.01" class="form-control" id="bazarAmount"
                                        name="amount" required placeholder="0.00">
                                </div>
                                <div class="form-text">Enter the total amount spent on bazar.</div>
                            </div>

                            <div class="mb-3">
                                <label for="bazarDate" class="form-label">Date of Expense <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="bazarDate" name="bazar_date" required>
                            </div>

                            <div class="mb-3">
                                <label for="bazarDescription" class="form-label">Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="bazarDescription" name="description" rows="3"
                                    required
                                    placeholder="e.g., Grocery from Shwapno, Meat purchase, Vegetable market"></textarea>
                                <div class="form-text">Required: Please provide a clear description.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="submitBazarRequestBtn">
                                <i class="fas fa-paper-plane me-1"></i>
                                <span class="submit-text">Submit Request</span>
                                <span class="loading-text d-none">
                                    <span class="spinner-border spinner-border-sm me-1"></span>Submitting...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let bazarModal = null;

        // MakeRequest function
        async function makeRequest(url, data = {}) {
            try {
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
                return result;

            } catch (error) {
                console.error('Request failed:', error);
                throw error;
            }
        }

        // ShowToast function
        function showToast(message, type = 'info') {
            let toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toastContainer';
                toastContainer.className = 'toast-container position-fixed bottom-0 start-50 translate-middle-x p-3';
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
                delay: 4000
            });
            toast.show();

            toastElement.addEventListener('hidden.bs.toast', function () {
                toastElement.remove();
            });
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize modal
            const modalElement = document.getElementById('addBazarRequestModal');
            if (modalElement && typeof bootstrap !== 'undefined') {
                bazarModal = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
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
                await Promise.all([
                    loadBazarRequests(),
                    loadStatistics()
                ]);
            } catch (error) {
                console.error('Error loading all data:', error);
                showToast('Error loading data. Please refresh the page.', 'error');
            }
        }

        async function handleBazarRequestSubmit(e) {
            e.preventDefault();
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
                const result = await makeRequest('user_process/bazar_api.php', {
                    amount: amount,
                    bazar_date: date,
                    description: description
                });

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
                const result = await makeRequest('user_process/bazar_api.php', { action: 'get_bazar_requests' });

                if (result.status === 'success') {
                    updateBazarRequestsMobileList(result.data);
                    updateBazarRequestsDesktopTable(result.data);
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

        async function loadStatistics() {
            try {
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

            document.getElementById('pendingBadge').textContent = pendingRequests.length;
            document.getElementById('processedBadge').textContent = processedRequests.length;
        }

        function formatStatusBadge(status, rejectionReason) {
            let statusClass = '';
            let statusText = status.charAt(0).toUpperCase() + status.slice(1);
            let icon = '';

            if (status === 'approved') {
                statusClass = 'approved';
                icon = '<i class="fas fa-check-circle me-1"></i>';
            } else if (status === 'rejected') {
                statusClass = 'rejected';
                icon = '<i class="fas fa-times-circle me-1"></i>';
            } else if (status === 'pending') {
                statusClass = 'pending';
                icon = '<i class="fas fa-clock me-1"></i>';
            }

            let html = `<span class="status-badge ${statusClass}">${icon}${statusText}</span>`;

            if (status === 'rejected' && rejectionReason && rejectionReason.trim().length > 0) {
                html += `<div class="mt-1"><small class="text-danger">${rejectionReason}</small></div>`;
            }
            return html;
        }

        function updateBazarRequestsMobileList(requests) {
            const listContainer = document.getElementById('bazarRequestsMobileList');
            const loader = document.getElementById('mobileListLoader');
            if (!listContainer) return;

            if (loader) {
                loader.classList.add('d-none');
            }

            if (requests.length === 0) {
                listContainer.innerHTML = `
                    <div class="loading-placeholder">
                        <i class="fas fa-inbox"></i>
                        No bazar requests found
                    </div>
                `;
                return;
            }

            const listHTML = requests.map(request => {
                const bazarDate = new Date(request.bazar_date);
                const amountClass = request.status === 'rejected' ? 'text-danger' : 'text-success';

                return `
                    <div class="transaction-item fade-in">
                        <div class="transaction-details">
                            <div class="transaction-title">${request.description || 'No Description'}</div>
                            <div class="transaction-meta">
                                <i class="fas fa-calendar-alt"></i>
                                ${bazarDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                            </div>
                        </div>
                        <div class="transaction-amount">
                            <div class="amount-value ${amountClass}">৳${parseFloat(request.amount).toFixed(2)}</div>
                            ${formatStatusBadge(request.status, request.rejection_reason)}
                        </div>
                    </div>
                `;
            }).join('');

            listContainer.innerHTML = listHTML;
        }

        function updateBazarRequestsDesktopTable(requests) {
            const tableBody = document.getElementById('bazarRequestsDesktopTable');
            if (!tableBody) return;

            if (requests.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="fas fa-inbox fa-lg me-2"></i>
                            No bazar requests found
                        </td>
                    </tr>
                `;
                return;
            }

            const tableHTML = requests.map(request => {
                const bazarDate = new Date(request.bazar_date);
                const createdAt = new Date(request.created_at);
                const amountClass = request.status === 'rejected' ? 'text-danger' : 'text-success';

                return `
                    <tr class="fade-in">
                        <td>${bazarDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
                        <td><span class="fw-bold ${amountClass}">৳${parseFloat(request.amount).toFixed(2)}</span></td>
                        <td>${request.description || 'No description'}</td>
                        <td class="status-cell">${formatStatusBadge(request.status, request.rejection_reason)}</td>
                        <td><small class="text-muted">${createdAt.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</small></td>
                    </tr>
                `;
            }).join('');

            tableBody.innerHTML = tableHTML;
        }

        function updateStatistics(stats) {
            document.getElementById('approvedSpending').textContent = `৳${parseFloat(stats.total_bazar).toFixed(2)}`;
            document.getElementById('totalRecords').textContent = stats.total_records;

            // Update month name
            const currentMonth = new Date().toLocaleString('default', { month: 'long', year: 'numeric' });
            document.getElementById('monthName').textContent = currentMonth;
            document.getElementById('monthNameDisplay').textContent = `Your Bazar History for ${currentMonth}`;

            // Update highest bearer
            if (stats.highest_bearer && stats.highest_bearer.amount > 0) {
                document.getElementById('highestBearerName').textContent = stats.highest_bearer.name;
                document.getElementById('highestBearerAmount').textContent = `৳${parseFloat(stats.highest_bearer.amount).toFixed(2)}`;
            } else {
                document.getElementById('highestBearerName').textContent = 'No data';
                document.getElementById('highestBearerAmount').textContent = '৳0.00';
            }
        }
    </script>
</body>

</html>