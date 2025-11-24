<div class="notification-container" id="notification-container"></div>

<div class="main-container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1 fw-bold">
                            <i class="bi bi-people-fill me-2 text-primary"></i>Member Management
                        </h1>
                        <p class="text-muted mb-0">Manage your team members efficiently</p>
                    </div>
                    <button class="btn btn-outline-primary" onclick="refreshData()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <div class="col-md-3">
            <div class="stats-card bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 opacity-75 small">Total Members</p>
                        <h3 class="mb-0 fw-bold" id="total-members">0</h3>
                    </div>
                    <i class="bi bi-people display-6 opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 opacity-75 small">Active</p>
                        <h3 class="mb-0 fw-bold" id="active-members">0</h3>
                    </div>
                    <i class="bi bi-person-check display-6 opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-warning text-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 opacity-75 small">Suspended</p>
                        <h3 class="mb-0 fw-bold" id="suspended-members">0</h3>
                    </div>
                    <i class="bi bi-person-x display-6 opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-info text-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 opacity-75 small">Today's Actions</p>
                        <h3 class="mb-0 fw-bold" id="total-actions">0</h3>
                    </div>
                    <i class="bi bi-activity display-6 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="mb-3">
                    <i class="bi bi-person-plus me-2 text-primary"></i>Add New Member
                </h5>
                <form id="add-member-form">
                    <div class="input-group">
                        <input type="text" name="name" class="form-control" placeholder="Enter member name" required>
                        <input type="email" name="email" class="form-control" placeholder="Email (optional)">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Add
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="mb-3">
                    <i class="bi bi-funnel me-2 text-primary"></i>Search & Filter
                </h5>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="search-input"
                                placeholder="Search members by name or email...">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-sm btn-outline-primary filter-btn active"
                                onclick="setFilter('all', this)">All Members</button>
                            <button class="btn btn-sm btn-outline-primary filter-btn"
                                onclick="setFilter('active', this)">Active Only</button>
                            <button class="btn btn-sm btn-outline-primary filter-btn"
                                onclick="setFilter('suspended', this)">Suspended</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" id="members-grid">
    </div>
</div>

<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Member Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-member-form">
                <input type="hidden" id="edit-member-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit-name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="edit-email">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="edit-is-active">
                        <label class="form-check-label" for="edit-is-active">Is Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<button class="btn btn-primary rounded-circle shadow floating-btn" onclick="scrollToTop()"
    style="display: none; position: fixed; bottom: 20px; right: 20px; width: 45px; height: 45px; z-index: 1000; padding: 0;">
    <i class="bi bi-arrow-up fs-5"></i>
</button>

<script>
    // --- JAVASCRIPT FOR FUNCTIONALITY ---

    const API_BASE = 'process/members_action.php';
    let currentFilter = 'all';
    let allMembers = [];
    let actionCount = 0;
    let editMemberModal = null;

    // Use window.onload or a simpler document.ready check if your main page loads sections via AJAX
    // For a simple include/require, this DOMContentLoaded is fine, assuming Bootstrap JS is loaded on the main page.
    document.addEventListener('DOMContentLoaded', function () {
        // Ensure you check if the element exists before initializing the modal
        const modalElement = document.getElementById('editMemberModal');
        if (typeof bootstrap !== 'undefined' && modalElement) {
            editMemberModal = new bootstrap.Modal(modalElement);
        }
        loadMembers();

        setTimeout(() => {
            // Check if showNotification function exists (it should, as it's defined below)
            if (typeof showNotification === 'function') {
                showNotification('Welcome to Simple Member Management!');
            }
        }, 1000);
    });

    function showNotification(message, type = 'success') {
        const container = document.getElementById('notification-container');
        if (!container) {
            console.error('Notification container not found. Ensure it is in your main layout.');
            return;
        }

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1 ${type === 'error' ? 'text-danger' : type === 'warning' ? 'text-warning' : 'text-success'}">
                        <i class="bi ${type === 'error' ? 'bi-exclamation-triangle' : type === 'warning' ? 'bi-info-circle' : 'bi-check-circle'} me-2"></i>
                        ${type === 'error' ? 'Error' : type === 'warning' ? 'Warning' : 'Success'}
                    </h6>
                    <p class="mb-0 text-dark">${message}</p>
                </div>
                <button type="button" class="btn-close ms-3" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

        container.appendChild(notification);

        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    async function loadMembers() {
        try {
            const searchTerm = document.getElementById('search-input').value;
            let statusParam = '';

            if (currentFilter === 'active') {
                statusParam = '1';
            } else if (currentFilter === 'suspended') {
                statusParam = '0';
            }

            const params = new URLSearchParams({
                action: 'get_members',
                search: searchTerm,
                status: statusParam
            });

            const response = await fetch(`${API_BASE}?${params}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                allMembers = data.data || [];
                renderMembers();
                updateStats();
            } else {
                showNotification(data.error || 'Failed to load members', 'error');
                allMembers = [];
                renderMembers();
                updateStats();
            }
        } catch (error) {
            console.error('Error loading members:', error);
            showNotification('Failed to load members. Check API path and server status.', 'error');
            initializeWithSampleData();
        }
    }

    function initializeWithSampleData() {
        allMembers = [
            { id: 1, name: 'John Doe', email: 'john@example.com', is_active: 1 },
            { id: 2, name: 'Jane Smith', email: 'jane@example.com', is_active: 1 },
            { id: 3, name: 'Robert Johnson', email: 'robert@example.com', is_active: 0 },
            { id: 4, name: 'Emily Davis', email: 'emily@example.com', is_active: 1 }
        ];
        renderMembers();
        updateStats();
        showNotification('Using demo data. API endpoint not accessible.', 'warning');
    }

    function renderMembers() {
        const grid = document.getElementById('members-grid');

        if (allMembers.length === 0) {
            grid.innerHTML = `
                <div class="col-12">
                    <div class="card p-5 text-center">
                        <i class="bi bi-people display-1 text-muted mb-3"></i>
                        <h4 class="text-muted">No members found</h4>
                        <p class="text-muted">Add your first member to get started</p>
                    </div>
                </div>
            `;
            return;
        }

        grid.innerHTML = allMembers.map(member => `
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="member-card card p-3">
                    <div class="d-flex align-items-start mb-3">
                        <div class="member-avatar me-3">
                            ${member.name.charAt(0).toUpperCase()}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">${escapeHtml(member.name)}</h6>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-envelope me-1"></i>
                                ${member.email || 'No email provided'}
                            </p>
                            <span class="status-badge ${member.is_active ? 'bg-success text-white' : 'bg-warning text-dark'}">
                                <i class="bi ${member.is_active ? 'bi-check-circle' : 'bi-pause-circle'} me-1"></i>
                                ${member.is_active ? 'Active' : 'Suspended'}
                            </span>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-warning flex-grow-1" onclick="toggleMemberStatus(${member.id})">
                            <i class="bi ${member.is_active ? 'bi-pause' : 'bi-play'} me-1"></i>
                            ${member.is_active ? 'Suspend' : 'Activate'}
                        </button>
                        <button class="btn btn-sm btn-primary flex-grow-1" onclick="openEditModal(${member.id})">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteMember(${member.id}, '${escapeHtml(member.name)}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function updateStats() {
        const total = allMembers.length;
        const active = allMembers.filter(m => m.is_active).length;
        const suspended = allMembers.filter(m => !m.is_active).length;

        document.getElementById('total-members').textContent = total;
        document.getElementById('active-members').textContent = active;
        document.getElementById('suspended-members').textContent = suspended;
        document.getElementById('total-actions').textContent = actionCount;
    }

    document.getElementById('add-member-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const name = formData.get('name').trim();
        const email = formData.get('email').trim();

        if (!name) {
            showNotification('Name is required', 'error');
            return;
        }

        const data = {
            action: 'add_member',
            name: name,
            email: email
        };

        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showNotification(result.message || 'Member added successfully');
                this.reset();
                actionCount++;
                loadMembers();
            } else {
                showNotification(result.error || 'Failed to add member', 'error');
            }
        } catch (error) {
            showNotification('Error adding member: ' + error.message, 'error');
            const newMember = {
                id: Date.now(),
                name: name,
                email: email,
                is_active: 1
            };
            allMembers.push(newMember);
            renderMembers();
            updateStats();
            actionCount++;
            showNotification('Member added locally (demo mode)', 'warning');
        }
    });

    async function toggleMemberStatus(memberId) {
        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'toggle_member_status', id: memberId })
            });

            const result = await response.json();

            if (result.success) {
                showNotification(result.message || 'Status updated successfully');
                actionCount++;
                loadMembers();
            } else {
                showNotification(result.error || 'Failed to update status', 'error');
            }
        } catch (error) {
            showNotification('Error updating status: ' + error.message, 'error');
            const member = allMembers.find(m => m.id === memberId);
            if (member) {
                member.is_active = member.is_active ? 0 : 1;
                renderMembers();
                updateStats();
                actionCount++;
                showNotification('Status updated locally (demo mode)', 'warning');
            }
        }
    }

    function openEditModal(memberId) {
        const member = allMembers.find(m => m.id === memberId);
        if (!member) {
            showNotification('Member data not found!', 'error');
            return;
        }

        document.getElementById('edit-member-id').value = member.id;
        document.getElementById('edit-name').value = member.name;
        document.getElementById('edit-email').value = member.email || '';
        document.getElementById('edit-is-active').checked = member.is_active;

        if (editMemberModal) editMemberModal.show();
    }

    document.getElementById('edit-member-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const memberId = parseInt(document.getElementById('edit-member-id').value);
        const newName = document.getElementById('edit-name').value.trim();
        const newEmail = document.getElementById('edit-email').value.trim();
        const newIsActive = document.getElementById('edit-is-active').checked ? 1 : 0;

        if (!newName) {
            showNotification('Name cannot be empty.', 'error');
            return;
        }

        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_member',
                    id: memberId,
                    name: newName,
                    email: newEmail,
                    is_active: newIsActive
                })
            });

            const result = await response.json();

            if (result.success) {
                showNotification(result.message || 'Member updated successfully');
                actionCount++;
                loadMembers();
                if (editMemberModal) editMemberModal.hide();
            } else {
                showNotification(result.error || 'Failed to update member', 'error');
            }
        } catch (error) {
            showNotification('Error updating member: ' + error.message, 'error');
            const member = allMembers.find(m => m.id === memberId);
            if (member) {
                member.name = newName;
                member.email = newEmail;
                member.is_active = newIsActive;
                renderMembers();
                updateStats();
                actionCount++;
                if (editMemberModal) editMemberModal.hide();
                showNotification('Member updated locally (demo mode)', 'warning');
            }
        }
    });

    async function deleteMember(memberId, memberName) {
        if (!confirm(`Are you sure you want to delete "${memberName}"? This action cannot be undone.`)) {
            return;
        }

        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_member', id: memberId })
            });

            const result = await response.json();

            if (result.success) {
                showNotification(result.message || 'Member deleted successfully');
                actionCount++;
                loadMembers();
            } else {
                showNotification(result.error || 'Failed to delete member', 'error');
            }
        } catch (error) {
            showNotification('Error deleting member: ' + error.message, 'error');
            allMembers = allMembers.filter(m => m.id !== memberId);
            renderMembers();
            updateStats();
            actionCount++;
            showNotification('Member deleted locally (demo mode)', 'warning');
        }
    }

    function setFilter(filter, button) {
        currentFilter = filter;
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.add('btn-outline-primary'));
        button.classList.add('active');
        button.classList.remove('btn-outline-primary');
        loadMembers();
    }

    function refreshData() {
        actionCount = 0;
        loadMembers();
        showNotification('Data refreshed successfully');
    }

    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadMembers(), 500);
        });
    }


    window.addEventListener('scroll', function () {
        const floatingBtn = document.querySelector('.floating-btn');
        if (floatingBtn) {
            if (window.scrollY > 300) {
                floatingBtn.style.display = 'flex';
            } else {
                floatingBtn.style.display = 'none';
            }
        }
    });
</script>
<style>
    /* -------------------------------------- */
    /* 1. Notification Container & Styles     */
    /* -------------------------------------- */
    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        width: 350px;
        /* Standard toast width */
    }

    .notification {
        background-color: #fff;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-left: 5px solid;
        animation: fadeInRight 0.5s ease-out;
        transition: opacity 0.3s ease-in-out;
    }

    .notification.success {
        border-color: #198754;
        /* Bootstrap success green */
    }

    .notification.warning {
        border-color: #ffc107;
        /* Bootstrap warning yellow */
    }

    .notification.error {
        border-color: #dc3545;
        /* Bootstrap danger red */
    }

    .notification h6,
    .notification p {
        color: #343a40;
        /* Dark text for readability */
    }

    .notification .btn-close {
        font-size: 0.75rem;
        padding: 0;
        margin-top: 2px;
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* -------------------------------------- */
    /* 2. Custom Card/Element Styles          */
    /* -------------------------------------- */

    /* Stats Cards */
    .stats-card {
        color: white;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .stats-card:hover {
        transform: translateY(-3px);
    }

    .stats-card.bg-warning {
        /* Fixes the warning card text color */
        color: #212529 !important;
    }

    /* Member Card */
    .member-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.3s;
    }

    .member-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .member-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background-color: #0d6efd;
        /* Primary color fallback */
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: bold;
        flex-shrink: 0;
    }

    .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 5px;
    }

    .filter-btn {
        transition: all 0.2s;
    }

    .filter-btn.active {
        background-color: #0d6efd !important;
        color: white !important;
    }
</style>