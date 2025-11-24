<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0">Bazar Requests</h2>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Pending Bazar Requests</h5>
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
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="bazarRequestsTableBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted">Loading bazar requests...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Bazar Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                <input type="hidden" name="id" id="rejectRequestId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejectReason" name="reason" rows="3" required
                            placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <span class="submit-text">Reject Request</span>
                        <span class="loading-text d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>Rejecting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Declare modal variable globally
    let rejectModal = null;

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize the modal
        const modalElement = document.getElementById('rejectModal');
        rejectModal = new bootstrap.Modal(modalElement);

        loadBazarRequests();

        // Reject form submission
        document.getElementById('rejectForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            await handleRejectSubmit();
        });
    });

    async function handleRejectSubmit() {
        const form = document.getElementById('rejectForm');
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const submitText = submitBtn.querySelector('.submit-text');
        const loadingText = submitBtn.querySelector('.loading-text');

        // Show loading state
        submitBtn.disabled = true;
        submitText.classList.add('d-none');
        loadingText.classList.remove('d-none');

        try {
            const result = await makeRequest('reject_bazar_request', Object.fromEntries(formData));
            if (result.status === 'success') {
                showToast(result.message, 'success');
                rejectModal.hide();
                form.reset();
                // Update the specific row instead of reloading all
                updateRequestRow(parseInt(formData.get('id')), 'rejected');
            } else {
                showToast(result.message, 'error');
            }
        } catch (error) {
            console.error('Reject error:', error);
            showToast('Error rejecting request', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitText.classList.remove('d-none');
            loadingText.classList.add('d-none');
        }
    }

    async function loadBazarRequests() {
        try {
            const result = await makeRequest('get_bazar_requests');
            if (result.status === 'success') {
                const tbody = document.getElementById('bazarRequestsTableBody');
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No bazar requests found</td></tr>';
                    return;
                }

                tbody.innerHTML = result.data.map(request => {
                    let statusBadge = 'bg-secondary';
                    let statusText = request.status;

                    if (request.status === 'approved') {
                        statusBadge = 'bg-success';
                    } else if (request.status === 'rejected') {
                        statusBadge = 'bg-danger';
                    } else if (request.status === 'pending') {
                        statusBadge = 'bg-warning';
                    }

                    // Format dates properly
                    const bazarDate = new Date(request.bazar_date);
                    const createdAt = new Date(request.created_at);

                    return `
                    <tr id="request-${request.id}">
                        <td>${bazarDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                        <td>${request.member_name || 'Unknown'}</td>
                        <td>${request.description || '-'}</td>
                        <td class="fw-bold text-success">৳${parseFloat(request.amount).toFixed(2)}</td>
                        <td>
                            <span class="badge ${statusBadge}">${statusText}</span>
                        </td>
                        <td>${createdAt.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</td>
                        <td>
                            ${request.status === 'pending' ? `
                                <button class="btn btn-sm btn-outline-success me-1" onclick="approveBazarRequest(${request.id})">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="showRejectModal(${request.id})">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            ` : `
                                <span class="text-muted small">Processed</span>
                            `}
                        </td>
                    </tr>
                `;
                }).join('');
            } else {
                showToast('Error loading bazar requests', 'error');
            }
        } catch (error) {
            console.error('Error loading bazar requests:', error);
            showToast('Error loading bazar requests', 'error');
        }
    }

    async function approveBazarRequest(id) {
        if (confirm('Are you sure you want to approve this bazar request?')) {
            try {
                const result = await makeRequest('approve_bazar_request', { id });
                if (result.status === 'success') {
                    showToast(result.message, 'success');
                    // Update the row without reloading the page
                    updateRequestRow(id, 'approved');
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                console.error('Error approving bazar request:', error);
                showToast('Error approving bazar request', 'error');
            }
        }
    }

    function updateRequestRow(id, status) {
        const row = document.getElementById(`request-${id}`);
        if (row) {
            const statusCell = row.querySelector('td:nth-child(5)');
            const actionsCell = row.querySelector('td:nth-child(7)');

            if (status === 'approved') {
                statusCell.innerHTML = '<span class="badge bg-success">approved</span>';
                actionsCell.innerHTML = '<span class="text-muted small">Processed</span>';
            } else if (status === 'rejected') {
                statusCell.innerHTML = '<span class="badge bg-danger">rejected</span>';
                actionsCell.innerHTML = '<span class="text-muted small">Processed</span>';
            }
        }
    }

    function showRejectModal(id) {
        document.getElementById('rejectRequestId').value = id;
        if (rejectModal) {
            rejectModal.show();
        }
    }

    // Toast notification function
    function showToast(message, type = 'info') {
        // Check if toast container exists, if not create one
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
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        // Remove toast from DOM after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    }
</script>

<style>
    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }
</style>