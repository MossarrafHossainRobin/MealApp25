<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0"><i class="fas fa-users me-2 text-primary"></i>Member Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
        <i class="fas fa-plus me-2"></i>Add Member
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">All Members</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Base Rent</th>
                        <th>Status</th>
                        <th>Join Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="membersTableBody">
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">
                            <div class="spinner-border spinner-border-sm me-2"></div>
                            Loading members...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addMemberModalLabel"><i class="fas fa-user-plus me-2"></i>Add New Member
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="addMemberForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addName" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="addName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="addEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="addEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="addPhone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="addPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="addBaseRent" class="form-label">Base Rent (৳)</label>
                        <input type="number" step="0.01" class="form-control" id="addBaseRent" name="base_rent"
                            value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editMemberModalLabel"><i class="fas fa-edit me-2"></i>Edit Member Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMemberForm">
                <input type="hidden" name="id" id="editId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="editPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="editBaseRent" class="form-label">Base Rent (৳)</label>
                        <input type="number" step="0.01" class="form-control" id="editBaseRent" name="base_rent">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initial load
        loadMembers();

        // 1. Add member form submission
        document.getElementById('addMemberForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const result = await makeRequest('add_member', Object.fromEntries(formData));
            if (result.status === 'success') {
                showToast(result.message, 'success');
                // Use Bootstrap JS methods to hide modal
                bootstrap.Modal.getInstance(document.getElementById('addMemberModal')).hide();
                this.reset();
                loadMembers();
            } else {
                showToast(result.message, 'error');
            }
        });

        // 2. Edit member form submission (New)
        document.getElementById('editMemberForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const result = await makeRequest('edit_member', Object.fromEntries(formData));
            if (result.status === 'success') {
                showToast(result.message, 'success');
                // Use Bootstrap JS methods to hide modal
                bootstrap.Modal.getInstance(document.getElementById('editMemberModal')).hide();
                loadMembers();
            } else {
                showToast(result.message, 'error');
            }
        });
    });

    /**
     * Loads members data and populates the table.
     */
    async function loadMembers() {
        try {
            const tbody = document.getElementById('membersTableBody');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm me-2"></div>Loading members...</td></tr>';

            const result = await makeRequest('get_members');
            if (result.status === 'success') {
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No members found</td></tr>';
                    return;
                }

                tbody.innerHTML = result.data.map(member => {
                    // Determine status badge
                    let statusText = member.is_active ? 'Active' : 'Inactive';
                    let statusClass = member.is_active ? 'bg-success' : 'bg-secondary';
                    if (member.is_suspended) {
                        statusText = 'Suspended';
                        statusClass = 'bg-danger';
                    }

                    // Determine Suspend/Activate button details
                    const isSuspended = member.is_suspended === 1 || member.is_suspended === true; // Handle potential boolean/integer difference
                    const toggleAction = isSuspended ? 'Activate' : 'Suspend';
                    const toggleIcon = isSuspended ? 'fa-play' : 'fa-pause';
                    const toggleClass = isSuspended ? 'btn-outline-success' : 'btn-outline-warning';

                    return `
                        <tr>
                            <td>${member.id}</td>
                            <td class="fw-semibold">${member.name}</td>
                            <td>${member.email || '-'}</td>
                            <td>${member.phone || '-'}</td>
                            <td>৳${parseFloat(member.base_rent || 0).toFixed(2)}</td>
                            <td>
                                <span class="badge ${statusClass}">${statusText}</span>
                            </td>
                            <td>${new Date(member.created_at).toLocaleDateString()}</td>
                            <td class="text-nowrap">
                                <button class="btn btn-sm btn-outline-info me-1" onclick="openEditModal(${member.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm ${toggleClass} me-1" onclick="toggleMemberSuspension(${member.id}, ${!isSuspended})">
                                    <i class="fas ${toggleIcon}"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteMember(${member.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            } else {
                showToast('Error loading members', 'error');
            }
        } catch (error) {
            console.error('Error loading members:', error);
            showToast('Error loading members', 'error');
        }
    }

    /**
     * Opens the Edit Modal and populates it with member data. (New)
     */
    async function openEditModal(id) {
        try {
            const result = await makeRequest('get_member_details', { id });
            if (result.status === 'success' && result.data) {
                const member = result.data;
                document.getElementById('editId').value = member.id;
                document.getElementById('editName').value = member.name;
                document.getElementById('editEmail').value = member.email || '';
                document.getElementById('editPhone').value = member.phone || '';
                document.getElementById('editBaseRent').value = parseFloat(member.base_rent || 0).toFixed(2);

                const editModal = new bootstrap.Modal(document.getElementById('editMemberModal'));
                editModal.show();
            } else {
                showToast('Could not load member details for editing.', 'error');
            }
        } catch (error) {
            console.error('Error fetching member details:', error);
            showToast('Error fetching member details.', 'error');
        }
    }

    /**
     * Deletes a member.
     */
    async function deleteMember(id) {
        if (confirm('Are you sure you want to permanently delete this member? This action cannot be undone.')) {
            const result = await makeRequest('delete_member', { id });
            if (result.status === 'success') {
                showToast(result.message, 'success');
                loadMembers();
            } else {
                showToast(result.message, 'error');
            }
        }
    }

    /**
     * Suspends or activates a member.
     */
    async function toggleMemberSuspension(id, suspend) {
        const action = suspend ? 'suspend' : 'activate';
        if (confirm(`Are you sure you want to ${action} this member? This will prevent them from adding meals/bazar.`)) {
            // Note: I've updated the endpoint name to reflect its true purpose.
            const result = await makeRequest('toggle_suspension', { id: id, is_suspended: suspend ? 1 : 0 });
            if (result.status === 'success') {
                showToast(result.message, 'success');
                loadMembers();
            } else {
                showToast(result.message, 'error');
            }
        }
    }

    // --- Placeholder/Mock Functions ---

    /**
     * Placeholder for your actual AJAX/fetch function. 
     */
    async function makeRequest(endpoint, params = {}) {
        console.log(`Making request to: ${endpoint} with params:`, params);

        await new Promise(resolve => setTimeout(resolve, 500));

        if (endpoint === 'get_members') {
            const mockData = [
                { id: 1, name: 'John Doe', email: 'john@example.com', phone: '123-456-7890', base_rent: 1500.00, is_active: 1, is_suspended: 0, created_at: '2024-01-15' },
                { id: 2, name: 'Jane Smith', email: 'jane@example.com', phone: '987-654-3210', base_rent: 1500.00, is_active: 1, is_suspended: 0, created_at: '2024-02-20' },
                { id: 3, name: 'Mark Suspended', email: 'mark@example.com', phone: null, base_rent: 1000.00, is_active: 1, is_suspended: 1, created_at: '2024-03-01' },
            ];
            return { status: 'success', data: mockData };
        } else if (endpoint === 'get_member_details' && params.id === 1) {
            return { status: 'success', data: { id: 1, name: 'John Doe', email: 'john@example.com', phone: '123-456-7890', base_rent: 1500.00, is_active: 1, is_suspended: 0, created_at: '2024-01-15' } };
        } else if (['add_member', 'edit_member', 'delete_member', 'toggle_suspension'].includes(endpoint)) {
            return { status: 'success', message: `${endpoint.replace('_', ' ')} successful!` };
        }

        return { status: 'error', message: 'Unknown request or mock error.' };
    }

    // Ensure Bootstrap's Modal is available
    const bootstrap = window.bootstrap;

    /**
     * Placeholder for a notification/toast function.
     */
    function showToast(message, type) {
        console.log(`[TOAST - ${type.toUpperCase()}] ${message}`);
        // Implement actual toast UI here
    }
</script>