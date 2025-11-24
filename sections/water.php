<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Duty Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        
        /* Modern Cards */
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 25px rgba(0,0,0,0.04); transition: transform 0.2s; overflow: hidden; }
        .card-header { background: white; border-bottom: 1px solid #f1f1f1; padding: 1.25rem; font-weight: 700; color: #3a3b45; }
        
        /* Alerts with Gradients */
        .duty-alert { border-radius: 12px; border: none; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .alert-today { background: linear-gradient(120deg, #4e73df 0%, #224abe 100%); color: white; }
        .alert-tomorrow { background: linear-gradient(120deg, #f6c23e 0%, #e0a800 100%); color: #fff; }
        
        /* Timer Bar */
        .timer-container { height: 6px; background: rgba(255,255,255,0.3); margin-top: 10px; border-radius: 3px; overflow: hidden; }
        .timer-fill { height: 100%; background: #fff; width: 0%; border-radius: 3px; transition: width 1s linear; box-shadow: 0 0 10px rgba(255,255,255,0.5); }
        
        /* Status Badges */
        .badge-custom { padding: 6px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-completed { background-color: #d4edda; color: #155724; }

        /* Admin Actions */
        .admin-actions { display: none; } 
        .show-admin .admin-actions { display: inline-block; }
        
        /* Icon Buttons */
        .btn-icon { width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s; }
        .btn-icon:hover { transform: scale(1.1); background-color: #f8f9fa; }

        /* Toast & Tooltips */
        #toast-container { position: fixed; top: 20px; right: 20px; z-index: 1060; pointer-events: none; }
        .toast { pointer-events: auto; }
        
        /* Form Inputs */
        .form-control-soft { background-color: #f8f9fa; border: 1px solid #e3e6f0; }
        .form-control-soft:focus { background-color: #fff; box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15); }
    </style>
</head>

<body>
    <!-- Notification Toast Container -->
    <div id="toast-container"></div>

    <!-- Navbar -->
    <div class="bg-white shadow-sm mb-4 py-3 sticky-top">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle p-2 me-3 shadow-sm d-flex justify-content-center align-items-center" style="width:45px;height:45px;">
                    <i class="fas fa-hand-holding-water fa-lg"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Water Duty Manager</h5>
                    <div id="last-duty-info" class="small text-muted" style="font-size: 0.8rem;">
                        <div class="spinner-border spinner-border-sm text-secondary"></div> Loading history...
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="form-check form-switch d-flex align-items-center gap-2 p-0 m-0">
                    <label class="form-check-label small fw-bold text-secondary" for="adminToggle" style="cursor:pointer;">ADMIN MODE</label>
                    <input class="form-check-input m-0 cursor-pointer" type="checkbox" id="adminToggle" style="width: 2.5em; height: 1.25em;">
                </div>
                <span class="badge bg-light text-dark border shadow-sm px-3 py-2 rounded-pill" id="header-date">...</span>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Dashboard Alerts -->
        <div class="row mb-4" id="alerts-container">
            <div class="col-12 text-center py-4"><div class="spinner-border text-primary"></div></div>
        </div>

        <div class="row g-4">
            <!-- Assignment Panel -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center bg-white">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-2"><i class="fas fa-calendar-plus"></i></div>
                        <span class="fw-bold">Schedule Duty</span>
                    </div>
                    <div class="card-body">
                        <form id="assignForm">
                            <input type="hidden" name="assign_duty" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase text-muted">Member</label>
                                <select class="form-select form-control-soft" name="member_id" id="member-select" required>
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-7">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Date</label>
                                    <input type="date" class="form-control form-control-soft" name="duty_date" id="duty_date" required>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Time</label>
                                    <input type="time" class="form-control form-control-soft" name="duty_time" id="duty_time" value="09:00">
                                </div>
                            </div>

                            <div class="form-check mb-4 p-3 bg-light rounded border">
                                <input class="form-check-input" type="checkbox" name="send_email_now" id="sendEmail" checked>
                                <label class="form-check-label small text-dark fw-semibold" for="sendEmail">
                                    <i class="fas fa-paper-plane text-secondary me-1"></i> Send email notification immediately
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-check me-2"></i>Assign Duty
                            </button>
                        </form>
                        
                        <div class="mt-4 p-3 bg-info bg-opacity-10 rounded-3 small text-dark border border-info border-opacity-25">
                            <strong><i class="fas fa-robot text-info"></i> Auto-Pilot Active:</strong><br>
                            System will automatically reassign this duty if not marked completed <strong>4 hours</strong> after the scheduled time.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule List -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success rounded p-2 me-2"><i class="fas fa-list-ul"></i></div>
                            <span class="fw-bold">Upcoming Schedule</span>
                        </div>
                        <span class="badge bg-primary rounded-pill" id="duties-count">0</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="dutiesTable">
                                <thead class="table-light small text-uppercase text-muted">
                                    <tr>
                                        <th class="ps-4 py-3">Time</th>
                                        <th>Member</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="duties-table-body">
                                    <!-- JS injects rows here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold ms-2">Edit Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editForm">
                        <input type="hidden" name="edit_duty" value="1">
                        <input type="hidden" name="duty_id" id="edit_duty_id">
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">MEMBER</label>
                            <select class="form-select form-control-soft" name="member_id" id="edit_member_id" required></select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small text-muted fw-bold">DATE</label>
                                <input type="date" class="form-control form-control-soft" name="duty_date" id="edit_duty_date" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small text-muted fw-bold">TIME</label>
                                <input type="time" class="form-control form-control-soft" name="duty_time" id="edit_duty_time" required>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let memberList = []; 

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            loadInitialData();
        });

        // Admin Toggle Logic
        document.getElementById('adminToggle').addEventListener('change', function() {
            document.body.classList.toggle('show-admin', this.checked);
            showToast(this.checked ? "Admin Controls Enabled" : "View Mode Only", "info");
        });

        // Date/Time Formatter
        function formatDateTime(dateStr, timeStr) {
            const d = new Date(`${dateStr}T${timeStr || '09:00:00'}`);
            return {
                date: d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
                time: d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }),
                fullDate: d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })
            };
        }

        // --- Main Data Loader ---
        async function loadInitialData() {
            try {
                const response = await fetch('process/water_actions.php');
                const result = await response.json();

                if (result.status === 'success') {
                    const data = result.data;
                    memberList = data.members; 

                    // Update Header
                    document.getElementById('header-date').textContent = data.formatted_date;
                    document.getElementById('duty_date').min = data.min_date;
                    document.getElementById('duty_date').value = data.min_date;
                    
                    // Last Duty Logic
                    if(data.last_duty) {
                        const dt = formatDateTime(data.last_duty.duty_date, data.last_duty.duty_time);
                        document.getElementById('last-duty-info').innerHTML = 
                            `<span class="text-success"><i class="fas fa-check-circle"></i> Last: <strong>${data.last_duty.name}</strong> (${dt.date})</span>`;
                    } else {
                        document.getElementById('last-duty-info').innerHTML = 'No history found';
                    }

                    renderAlerts(data.today_duty, data.tomorrow_duty, data.timer);
                    renderMemberSelect(document.getElementById('member-select'), data.members);
                    renderTable(data.upcoming_duties);
                    document.getElementById('duties-count').textContent = data.upcoming_duties.length;
                }
            } catch (error) {
                console.error(error);
                showToast('Failed to connect to server', 'danger');
            }
        }

        function renderMemberSelect(select, members) {
            select.innerHTML = '<option value="">Select Member...</option>';
            members.forEach(m => select.innerHTML += `<option value="${m.id}">${m.name}</option>`);
        }

        // --- Render Alerts (Cards) ---
        function renderAlerts(today, tomorrow, timer) {
            const container = document.getElementById('alerts-container');
            let html = '';

            // Today Card
            html += '<div class="col-md-6 mb-3 mb-md-0">';
            if (today) {
                const dt = formatDateTime(today.duty_date, today.duty_time);
                let timerHtml = '';
                
                // Automation Timer Logic
                if(today.status === 'pending') {
                    if (timer.hours_left > 0) {
                        timerHtml = `
                            <div class="mt-3 small text-white text-opacity-75">
                                <div class="d-flex justify-content-between mb-1 fw-bold">
                                    <span><i class="fas fa-stopwatch"></i> Auto-Reassign Timer</span>
                                    <span>${timer.hours_left}h left</span>
                                </div>
                                <div class="timer-container">
                                    <div class="timer-fill" style="width: ${timer.percent}%"></div>
                                </div>
                            </div>`;
                    } else {
                        timerHtml = `<div class="mt-2 badge bg-danger text-white shadow-sm">Deadline Passed - Processing...</div>`;
                    }
                }

                html += `
                <div class="card duty-alert alert-today h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="badge bg-white text-primary bg-opacity-75 mb-2 shadow-sm">TODAY • ${dt.time}</div>
                                <h3 class="mb-0 fw-bold">${today.name}</h3>
                                <small class="text-white text-opacity-75">Scheduled Duty</small>
                            </div>
                            ${today.status === 'pending' ? `
                            <form class="ajax-form" data-action="complete">
                                <input type="hidden" name="duty_id" value="${today.id}">
                                <input type="hidden" name="mark_completed" value="1">
                                <button class="btn btn-light text-primary fw-bold btn-sm rounded-pill px-4 shadow-lg">
                                    <i class="fas fa-check"></i> Done
                                </button>
                            </form>` : '<div class="bg-white text-success rounded-circle p-3 shadow-lg"><i class="fas fa-check fa-2x"></i></div>'}
                        </div>
                        ${timerHtml}
                    </div>
                </div>`;
            } else {
                html += `<div class="card h-100 border-2 border-dashed bg-light text-center py-4 d-flex align-items-center justify-content-center">
                            <div class="text-muted"><i class="fas fa-coffee fa-2x mb-2 opacity-50"></i><br>No duty today</div>
                         </div>`;
            }
            html += '</div>';

            // Tomorrow Card
            html += '<div class="col-md-6">';
            if (tomorrow) {
                const dt = formatDateTime(tomorrow.duty_date, tomorrow.duty_time);
                html += `
                <div class="card duty-alert alert-tomorrow h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="badge bg-white text-warning bg-opacity-50 mb-2 shadow-sm">TOMORROW • ${dt.time}</div>
                            <h4 class="mb-0 fw-bold">${tomorrow.name}</h4>
                            <small class="text-white text-opacity-75">Upcoming</small>
                        </div>
                        <form class="ajax-form" data-action="remind">
                            <input type="hidden" name="duty_id" value="${tomorrow.id}">
                            <input type="hidden" name="send_reminder" value="1">
                            <button class="btn btn-light text-warning fw-bold btn-sm rounded-pill px-3 shadow-sm">
                                <i class="fas fa-bell"></i> Remind
                            </button>
                        </form>
                    </div>
                </div>`;
            } else {
                html += `<div class="card h-100 border-2 border-dashed bg-light text-center py-4 d-flex align-items-center justify-content-center">
                            <div class="text-muted"><i class="fas fa-calendar-day fa-2x mb-2 opacity-50"></i><br>No duty tomorrow</div>
                         </div>`;
            }
            html += '</div>';
            container.innerHTML = html;
        }

        // --- Render Table ---
        function renderTable(duties) {
            const tbody = document.getElementById('duties-table-body');
            tbody.innerHTML = '';
            
            if (duties.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center py-5 text-muted">No upcoming duties found</td></tr>`;
                return;
            }

            duties.forEach(duty => {
                const dt = formatDateTime(duty.duty_date, duty.duty_time);
                const statusBadge = duty.status === 'pending' 
                    ? `<span class="badge-custom status-pending"><i class="fas fa-clock me-1"></i> Pending</span>` 
                    : `<span class="badge-custom status-completed"><i class="fas fa-check me-1"></i> Done</span>`;
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="ps-4">
                        <div class="fw-bold text-dark">${dt.fullDate}</div>
                        <small class="text-muted"><i class="far fa-clock"></i> ${dt.time}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-2 me-3" style="width:35px;height:35px;display:flex;justify-content:center;align-items:center;">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="fw-semibold text-dark">${duty.name}</span>
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td class="pe-4 text-end">
                        <div class="d-flex justify-content-end align-items-center gap-1">
                            <!-- General Actions -->
                            ${duty.status === 'pending' ? `
                            <form class="d-inline ajax-form" data-action="complete">
                                <input type="hidden" name="duty_id" value="${duty.id}">
                                <input type="hidden" name="mark_completed" value="1">
                                <button class="btn btn-sm btn-outline-success btn-icon border-0" title="Mark Completed" data-bs-toggle="tooltip"><i class="fas fa-check"></i></button>
                            </form>
                            
                            <div class="dropdown d-inline">
                                <button class="btn btn-sm btn-outline-secondary btn-icon border-0" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu shadow border-0 dropdown-menu-end">
                                    <li><h6 class="dropdown-header">Options</h6></li>
                                    <li>
                                        <form class="ajax-form px-2 py-1" data-action="remind">
                                            <input type="hidden" name="duty_id" value="${duty.id}">
                                            <input type="hidden" name="send_reminder" value="1">
                                            <button class="btn btn-sm w-100 text-start btn-light"><i class="fas fa-bell text-warning me-2"></i> Send Reminder</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form class="ajax-form px-2 py-1" data-action="manual-email">
                                            <input type="hidden" name="duty_id" value="${duty.id}">
                                            <input type="hidden" name="send_manual_email" value="1">
                                            <button class="btn btn-sm w-100 text-start btn-light"><i class="fas fa-envelope text-primary me-2"></i> Send Email Now</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            ` : ''}
                            
                            <!-- Admin Actions (Hidden unless enabled) -->
                            <span class="admin-actions border-start ps-2 ms-1">
                                <button class="btn btn-sm btn-outline-primary btn-icon me-1" onclick="openEditModal(${duty.id}, '${duty.member_id}', '${duty.duty_date}', '${duty.duty_time}')"><i class="fas fa-pen"></i></button>
                                <button class="btn btn-sm btn-outline-danger btn-icon" onclick="deleteDuty(${duty.id})"><i class="fas fa-trash"></i></button>
                            </span>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // --- Interaction Logic ---
        
        // Open Edit Modal
        window.openEditModal = function(id, memberId, date, time) {
            document.getElementById('edit_duty_id').value = id;
            document.getElementById('edit_duty_date').value = date;
            document.getElementById('edit_duty_time').value = time || '09:00';
            
            const select = document.getElementById('edit_member_id');
            renderMemberSelect(select, memberList);
            select.value = memberId;
            
            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        };

        // Delete Confirmation
        window.deleteDuty = async function(id) {
            if(!confirm('Are you sure you want to permanently delete this scheduled duty?')) return;
            
            const formData = new FormData();
            formData.append('delete_duty', '1');
            formData.append('duty_id', id);
            await processRequest(formData);
        };

        // Unified Request Processor
        async function processRequest(formData) {
            try {
                const res = await fetch('process/water_actions.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                showToast(data.message, data.status === 'success' ? 'success' : 'danger');
                
                if(data.reload || data.status === 'success') {
                    loadInitialData();
                }
                
                // Close modal if open
                const modalEl = document.getElementById('editModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if(modal) modal.hide();

            } catch (e) { 
                showToast('Connection error occurred', 'danger'); 
                console.error(e);
            }
        }

        // Global Form Submit Handler
        document.addEventListener('submit', function(e) {
            if(e.target.tagName === 'FORM') {
                e.preventDefault();
                const formData = new FormData(e.target);
                processRequest(formData);
                if(e.target.id === 'assignForm') e.target.reset();
            }
        });

        // Toast Notification Helper
        function showToast(msg, type) {
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            const el = document.createElement('div');
            el.className = `toast show align-items-center text-white bg-${type} border-0 mb-2 shadow-lg`;
            el.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${icon} me-2"></i>${msg}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>`;
            document.getElementById('toast-container').appendChild(el);
            setTimeout(() => el.remove(), 4000);
        }
    </script>
</body>
</html>