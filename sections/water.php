<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Duty Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #224abe;
            --secondary: #858796;
            --success: #1cc88a;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #3a3b45;
            --border-radius: 16px;
            --shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: var(--light);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Performance Optimizations */
        .card,
        .btn,
        .badge {
            will-change: transform;
        }

        /* Modern Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
            overflow: hidden;
            background: white;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #f1f1f1;
            padding: 1.25rem;
            font-weight: 700;
            color: var(--dark);
        }

        /* Enhanced Alerts with Gradients */
        .duty-alert {
            border-radius: 12px;
            border: none;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            transition: var(--transition);
        }

        .alert-today {
            background: linear-gradient(120deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .alert-tomorrow {
            background: linear-gradient(120deg, var(--warning) 0%, #e0a800 100%);
            color: #fff;
        }

        .alert-reminder {
            background: linear-gradient(120deg, var(--danger) 0%, #be2617 100%);
            color: white;
        }

        /* Enhanced Timer Bar */
        .timer-container {
            height: 6px;
            background: rgba(255, 255, 255, 0.3);
            margin-top: 10px;
            border-radius: 3px;
            overflow: hidden;
        }

        .timer-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 1s linear;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .timer-normal {
            background: #fff;
        }

        .timer-warning {
            background: var(--warning);
        }

        .timer-danger {
            background: var(--danger);
        }

        /* Enhanced Status Badges */
        .badge-custom {
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-reminder {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Admin Actions */
        .admin-actions {
            display: none;
        }

        .show-admin .admin-actions {
            display: inline-block;
        }

        /* Enhanced Icon Buttons */
        .btn-icon {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: var(--transition);
        }

        .btn-icon:hover {
            transform: scale(1.1);
            background-color: #f8f9fa;
        }

        /* Enhanced Toast & Tooltips */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
            pointer-events: none;
            max-width: 350px;
        }

        .toast {
            pointer-events: auto;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .toast-success {
            background: linear-gradient(135deg, var(--success) 0%, #17a673 100%);
        }

        .toast-warning {
            background: linear-gradient(135deg, var(--warning) 0%, #e0a800 100%);
        }

        .toast-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #be2617 100%);
        }

        .toast-info {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        /* Enhanced Form Inputs */
        .form-control-soft {
            background-color: #f8f9fa;
            border: 1px solid #e3e6f0;
            transition: var(--transition);
        }

        .form-control-soft:focus {
            background-color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
            border-color: var(--primary);
        }

        /* Enhanced Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            transform: translateY(-50%);
            z-index: 1;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            position: relative;
            z-index: 2;
            transition: var(--transition);
        }

        .step.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        .step.completed {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        /* Enhanced Member Stats */
        .member-stat-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid var(--primary);
            transition: var(--transition);
        }

        .member-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .member-next {
            border-left-color: var(--success);
            background: #f8fff9;
        }

        .member-last {
            border-left-color: var(--warning);
        }

        /* Enhanced Automation Status */
        .automation-status {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .reminder-timeline {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid var(--primary);
        }

        /* Auto-Pilot Controls */
        .auto-pilot-controls {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--success);
        }

        .timer-controls {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--warning);
        }

        /* Loading States */
        .skeleton-loader {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn-icon {
                width: 40px;
                height: 40px;
            }
            
            #toast-container {
                left: 10px;
                right: 10px;
                max-width: none;
            }
        }

        /* Print Styles */
        @media print {
            .no-print, .btn, .admin-actions {
                display: none !important;
            }
            
            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }

        /* Focus Styles for Accessibility */
        .btn:focus, .form-control:focus, .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            :root {
                --light: #1a1a1a;
                --dark: #f8f9fa;
            }
            
            body {
                background-color: var(--light);
                color: #e9ecef;
            }
            
            .card {
                background: #2d3748;
                color: #e9ecef;
            }
            
            .card-header {
                background: #2d3748;
                border-bottom-color: #4a5568;
            }
            
            .form-control-soft {
                background-color: #4a5568;
                border-color: #4a5568;
                color: #e9ecef;
            }
            
            .form-control-soft:focus {
                background-color: #4a5568;
                color: #e9ecef;
            }
        }
    </style>
</head>

<body>
    <!-- Notification Toast Container -->
    <div id="toast-container"></div>

    <!-- Navbar -->
    <div class="bg-white shadow-sm mb-4 py-3 sticky-top no-print">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle p-2 me-3 shadow-sm d-flex justify-content-center align-items-center"
                    style="width:45px;height:45px;">
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
                    <label class="form-check-label small fw-bold text-secondary" for="adminToggle"
                        style="cursor:pointer;">ADMIN MODE</label>
                    <input class="form-check-input m-0 cursor-pointer" type="checkbox" id="adminToggle"
                        style="width: 2.5em; height: 1.25em;">
                </div>
                <span class="badge bg-light text-dark border shadow-sm px-3 py-2 rounded-pill"
                    id="header-date">...</span>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Automation Status -->
        <div class="automation-status">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-2"><i class="fas fa-robot me-2"></i>Smart Automation Active</h5>
                    <p class="mb-0 small opacity-90">Automatic reminders & sequential assignment enabled</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="progress-steps">
                        <div class="step completed" title="Duty Assigned">✓</div>
                        <div class="step" id="step-reminder1" title="30 Min Reminder">1</div>
                        <div class="step" id="step-reminder2" title="1.5 Hour Warning">2</div>
                        <div class="step" id="step-reassign" title="4 Hour Reassign">3</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto-Pilot Controls -->
        <div class="auto-pilot-controls">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-2"><i class="fas fa-cogs me-2"></i>Auto-Pilot Controls</h5>
                    <p class="mb-0 small text-muted">Configure automatic email timing and assignment rules</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="form-check form-switch d-flex justify-content-end">
                        <input class="form-check-input me-2" type="checkbox" id="autoPilotToggle" checked>
                        <label class="form-check-label fw-bold" for="autoPilotToggle">Auto-Pilot Enabled</label>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3" id="autoPilotSettings">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">First Reminder</label>
                    <div class="input-group">
                        <input type="number" class="form-control form-control-soft" id="firstReminderTime" value="30" min="1" max="120">
                        <span class="input-group-text">minutes</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Final Warning</label>
                    <div class="input-group">
                        <input type="number" class="form-control form-control-soft" id="finalWarningTime" value="90" min="1" max="240">
                        <span class="input-group-text">minutes</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Auto-Reassign</label>
                    <div class="input-group">
                        <input type="number" class="form-control form-control-soft" id="autoReassignTime" value="240" min="1" max="480">
                        <span class="input-group-text">minutes</span>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-success btn-sm" id="saveAutoPilotSettings">
                        <i class="fas fa-save me-1"></i> Save Auto-Pilot Settings
                    </button>
                    <button class="btn btn-outline-secondary btn-sm ms-2" id="resetAutoPilotSettings">
                        <i class="fas fa-undo me-1"></i> Reset to Default
                    </button>
                </div>
            </div>
        </div>

        <!-- Timer Controls -->
        <div class="timer-controls">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-2"><i class="fas fa-clock me-2"></i>Timer Controls</h5>
                    <p class="mb-0 small text-muted">Manually control reminder timing for today's duty</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary" id="timerStatus">Timer: Active</span>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-uppercase text-muted">Delay Timer</label>
                    <div class="input-group">
                        <input type="number" class="form-control form-control-soft" id="delayTimer" value="15" min="1" max="120">
                        <span class="input-group-text">minutes</span>
                        <button class="btn btn-warning" id="delayTimerBtn">
                            <i class="fas fa-pause me-1"></i> Delay
                        </button>
                    </div>
                    <small class="text-muted">Postpone next reminder by specified minutes</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-uppercase text-muted">Manual Actions</label>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="btn btn-info btn-sm" id="sendReminderNow">
                            <i class="fas fa-bell me-1"></i> Send Reminder Now
                        </button>
                        <button class="btn btn-danger btn-sm" id="forceReassignNow">
                            <i class="fas fa-sync-alt me-1"></i> Force Reassign Now
                        </button>
                    </div>
                    <small class="text-muted d-block text-end">Manually trigger actions for today's duty</small>
                </div>
            </div>
        </div>

        <!-- Dashboard Alerts -->
        <div class="row mb-4" id="alerts-container">
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-primary"></div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Assignment Panel -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center bg-white">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-2"><i
                                class="fas fa-calendar-plus"></i></div>
                        <span class="fw-bold">Schedule Duty</span>
                    </div>
                    <div class="card-body">
                        <form id="assignForm">
                            <input type="hidden" name="assign_duty" value="1">

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase text-muted">Assignment
                                    Type</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="assignment_type" id="manual_assign"
                                        value="manual" checked>
                                    <label class="btn btn-outline-primary" for="manual_assign">
                                        <i class="fas fa-user me-1"></i> Manual
                                    </label>

                                    <input type="radio" class="btn-check" name="assignment_type" id="auto_assign"
                                        value="sequential">
                                    <label class="btn btn-outline-success" for="auto_assign">
                                        <i class="fas fa-robot me-1"></i> Auto
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3" id="manual-assign-section">
                                <label class="form-label small fw-bold text-uppercase text-muted">Member</label>
                                <select class="form-select form-control-soft" name="member_id" id="member-select"
                                    required>
                                    <option value="">Loading...</option>
                                </select>
                            </div>

                            <div class="mb-3" id="auto-assign-section" style="display: none;">
                                <div class="alert alert-info py-2">
                                    <small>
                                        <i class="fas fa-info-circle me-1"></i>
                                        Will automatically assign to next available member based on duty history
                                    </small>
                                </div>
                                <input type="hidden" name="assign_sequentially" value="1">
                                <input type="hidden" name="member_id" id="sequential_member" value="sequential">
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-7">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Date</label>
                                    <input type="date" class="form-control form-control-soft" name="duty_date"
                                        id="duty_date" required>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Time</label>
                                    <input type="time" class="form-control form-control-soft" name="duty_time"
                                        id="duty_time" value="09:00">
                                </div>
                            </div>

                            <div class="form-check mb-4 p-3 bg-light rounded border">
                                <input class="form-check-input" type="checkbox" name="send_email_now" id="sendEmail"
                                    checked>
                                <label class="form-check-label small text-dark fw-semibold" for="sendEmail">
                                    <i class="fas fa-paper-plane text-secondary me-1"></i> Send email notification
                                    immediately
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-check me-2"></i>Assign Duty
                            </button>
                        </form>

                        <!-- Reminder Timeline -->
                        <div class="reminder-timeline mt-4">
                            <h6 class="fw-bold mb-3"><i class="fas fa-clock me-2"></i>Automation Timeline</h6>
                            <div class="timeline-steps">
                                <div class="step-item d-flex mb-2">
                                    <div class="step-icon bg-success rounded-circle d-flex align-items-center justify-content-center text-white me-3"
                                        style="width: 24px; height: 24px; font-size: 12px;">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-content">
                                        <small class="fw-bold">Duty Assigned</small>
                                        <div class="text-muted" style="font-size: 0.75rem;">Member receives assignment
                                            email</div>
                                    </div>
                                </div>
                                <div class="step-item d-flex mb-2">
                                    <div class="step-icon bg-warning rounded-circle d-flex align-items-center justify-content-center text-white me-3"
                                        style="width: 24px; height: 24px; font-size: 12px;">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    <div class="step-content">
                                        <small class="fw-bold" id="first-reminder-time">30 Minutes</small>
                                        <div class="text-muted" style="font-size: 0.75rem;">First reminder if not
                                            completed</div>
                                    </div>
                                </div>
                                <div class="step-item d-flex mb-2">
                                    <div class="step-icon bg-danger rounded-circle d-flex align-items-center justify-content-center text-white me-3"
                                        style="width: 24px; height: 24px; font-size: 12px;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="step-content">
                                        <small class="fw-bold" id="final-warning-time">1.5 Hours</small>
                                        <div class="text-muted" style="font-size: 0.75rem;">Final warning email</div>
                                    </div>
                                </div>
                                <div class="step-item d-flex">
                                    <div class="step-icon bg-dark rounded-circle d-flex align-items-center justify-content-center text-white me-3"
                                        style="width: 24px; height: 24px; font-size: 12px;">
                                        <i class="fas fa-sync-alt"></i>
                                    </div>
                                    <div class="step-content">
                                        <small class="fw-bold" id="reassign-time">4 Hours</small>
                                        <div class="text-muted" style="font-size: 0.75rem;">Auto-reassign to next member
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Member Statistics -->
                <div class="card mt-4">
                    <div class="card-header d-flex align-items-center bg-white">
                        <div class="bg-info bg-opacity-10 text-info rounded p-2 me-2">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <span class="fw-bold">Member Rotation</span>
                    </div>
                    <div class="card-body">
                        <div id="member-stats-container">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                                <div class="small text-muted mt-2">Loading statistics...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule List -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success rounded p-2 me-2">
                                <i class="fas fa-list-ul"></i>
                            </div>
                            <span class="fw-bold">Upcoming Schedule</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill" id="duties-count">0</span>
                            <button class="btn btn-sm btn-outline-secondary" onclick="loadInitialData()"
                                title="Refresh">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="dutiesTable">
                                <thead class="table-light small text-uppercase text-muted">
                                    <tr>
                                        <th class="ps-4 py-3">Date & Time</th>
                                        <th>Member</th>
                                        <th>Status</th>
                                        <th>Reminders</th>
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
                            <select class="form-select form-control-soft" name="member_id" id="edit_member_id"
                                required></select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small text-muted fw-bold">DATE</label>
                                <input type="date" class="form-control form-control-soft" name="duty_date"
                                    id="edit_duty_date" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small text-muted fw-bold">TIME</label>
                                <input type="time" class="form-control form-control-soft" name="duty_time"
                                    id="edit_duty_time" required>
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

    <!-- Reminder Modal -->
    <div class="modal fade" id="reminderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold ms-2">Send Reminder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="reminderForm">
                        <input type="hidden" name="send_manual_reminder" value="1">
                        <input type="hidden" name="duty_id" id="reminder_duty_id">

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">REMINDER TYPE</label>
                            <select class="form-select form-control-soft" name="reminder_type" id="reminder_type"
                                required>
                                <option value="first_reminder">First Reminder (30 mins)</option>
                                <option value="final_warning">Final Warning (1.5 hours)</option>
                                <option value="assignment">Assignment Email</option>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                This will send an email reminder to the assigned member
                            </small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning rounded-pill">
                                <i class="fas fa-bell me-2"></i>Send Reminder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Performance optimization variables
        let memberList = [];
        let memberStats = [];
        let lastDataLoad = 0;
        const DATA_CACHE_TIME = 30000; // 30 seconds
        let dataCache = null;
        let autoPilotSettings = {
            firstReminder: 30,
            finalWarning: 90,
            autoReassign: 240,
            enabled: true
        };
        let timerInterval = null;

        // Load auto-pilot settings from localStorage
        function loadAutoPilotSettings() {
            const saved = localStorage.getItem('autoPilotSettings');
            if (saved) {
                autoPilotSettings = JSON.parse(saved);
            }
            updateAutoPilotUI();
        }

        // Save auto-pilot settings to localStorage
        function saveAutoPilotSettings() {
            localStorage.setItem('autoPilotSettings', JSON.stringify(autoPilotSettings));
            updateAutoPilotUI();
            showToast('Auto-pilot settings saved successfully', 'success');
        }

        // Update UI with current auto-pilot settings
        function updateAutoPilotUI() {
            document.getElementById('autoPilotToggle').checked = autoPilotSettings.enabled;
            document.getElementById('firstReminderTime').value = autoPilotSettings.firstReminder;
            document.getElementById('finalWarningTime').value = autoPilotSettings.finalWarning;
            document.getElementById('autoReassignTime').value = autoPilotSettings.autoReassign;
            
            // Update timeline display
            document.getElementById('first-reminder-time').textContent = `${autoPilotSettings.firstReminder} Minutes`;
            document.getElementById('final-warning-time').textContent = `${Math.floor(autoPilotSettings.finalWarning / 60)} Hours ${autoPilotSettings.finalWarning % 60} Minutes`;
            document.getElementById('reassign-time').textContent = `${Math.floor(autoPilotSettings.autoReassign / 60)} Hours`;
        }

        // Reset to default settings
        function resetAutoPilotSettings() {
            autoPilotSettings = {
                firstReminder: 30,
                finalWarning: 90,
                autoReassign: 240,
                enabled: true
            };
            updateAutoPilotUI();
            showToast('Auto-pilot settings reset to default', 'info');
        }

        // Debounce function for performance
        function debounce(func, wait, immediate) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    timeout = null;
                    if (!immediate) func(...args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func(...args);
            };
        }

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            loadAutoPilotSettings();
            loadInitialData();

            // Assignment type toggle
            document.querySelectorAll('input[name="assignment_type"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    const manualSection = document.getElementById('manual-assign-section');
                    const autoSection = document.getElementById('auto-assign-section');

                    if (this.value === 'manual') {
                        manualSection.style.display = 'block';
                        autoSection.style.display = 'none';
                    } else {
                        manualSection.style.display = 'none';
                        autoSection.style.display = 'block';
                    }
                });
            });

            // Auto-pilot controls
            document.getElementById('autoPilotToggle').addEventListener('change', function() {
                autoPilotSettings.enabled = this.checked;
                saveAutoPilotSettings();
            });

            document.getElementById('saveAutoPilotSettings').addEventListener('click', function() {
                autoPilotSettings.firstReminder = parseInt(document.getElementById('firstReminderTime').value) || 30;
                autoPilotSettings.finalWarning = parseInt(document.getElementById('finalWarningTime').value) || 90;
                autoPilotSettings.autoReassign = parseInt(document.getElementById('autoReassignTime').value) || 240;
                saveAutoPilotSettings();
            });

            document.getElementById('resetAutoPilotSettings').addEventListener('click', resetAutoPilotSettings);

            // Timer controls
            document.getElementById('delayTimerBtn').addEventListener('click', function() {
                const delayMinutes = parseInt(document.getElementById('delayTimer').value) || 15;
                delayTimer(delayMinutes);
            });

            document.getElementById('sendReminderNow').addEventListener('click', sendReminderNow);
            document.getElementById('forceReassignNow').addEventListener('click', forceReassignNow);
        });

        // Admin Toggle Logic
        document.getElementById('adminToggle').addEventListener('change', function () {
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
            const now = Date.now();
            
            // Use cached data if available and not expired
            if (dataCache && (now - lastDataLoad) < DATA_CACHE_TIME) {
                processData(dataCache);
                return;
            }
            
            try {
                const response = await fetch('process/water_actions.php');
                const result = await response.json();

                if (result.status === 'success') {
                    // Cache the data
                    dataCache = result;
                    lastDataLoad = now;
                    
                    processData(result);
                } else {
                    showToast('Failed to load data: ' + (result.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                console.error(error);
                showToast('Failed to connect to server', 'danger');
            }
        }

        function processData(result) {
            const data = result.data;
            memberList = data.members;
            memberStats = data.member_stats || [];

            // Update Header
            document.getElementById('header-date').textContent = data.formatted_date;
            document.getElementById('duty_date').min = data.min_date;
            document.getElementById('duty_date').value = data.min_date;

            // Last Duty Logic
            if (data.last_duty) {
                const dt = formatDateTime(data.last_duty.duty_date, data.last_duty.duty_time);
                document.getElementById('last-duty-info').innerHTML =
                    `<span class="text-success"><i class="fas fa-check-circle"></i> Last: <strong>${data.last_duty.name}</strong> (${dt.date})</span>`;
            } else {
                document.getElementById('last-duty-info').innerHTML = 'No history found';
            }

            renderAlerts(data.today_duty, data.tomorrow_duty, data.timer);
            renderMemberSelect(document.getElementById('member-select'), data.members);
            renderMemberStats();
            renderTable(data.upcoming_duties);
            document.getElementById('duties-count').textContent = data.upcoming_duties.length;

            // Update automation steps
            updateAutomationSteps(data.today_duty, data.timer);
            
            // Start/update timer if there's a pending duty today
            if (data.today_duty && data.today_duty.status === 'pending') {
                startTimer(data.today_duty, data.timer);
            } else {
                stopTimer();
            }
        }

        // Timer management functions
        function startTimer(todayDuty, timer) {
            stopTimer(); // Clear any existing timer
            
            if (!autoPilotSettings.enabled) {
                document.getElementById('timerStatus').textContent = 'Timer: Disabled (Auto-Pilot Off)';
                document.getElementById('timerStatus').className = 'badge bg-secondary';
                return;
            }
            
            document.getElementById('timerStatus').textContent = 'Timer: Active';
            document.getElementById('timerStatus').className = 'badge bg-primary';
            
            // Simulate timer functionality (in a real implementation, this would integrate with backend)
            timerInterval = setInterval(() => {
                // This would check current time and send reminders based on autoPilotSettings
                console.log('Timer tick - checking for reminders...');
            }, 60000); // Check every minute
        }
        
        function stopTimer() {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
        }
        
        function delayTimer(minutes) {
            if (!autoPilotSettings.enabled) {
                showToast('Cannot delay timer when Auto-Pilot is disabled', 'warning');
                return;
            }
            
            // In a real implementation, this would communicate with the backend
            // to adjust the reminder timing for the current duty
            showToast(`Timer delayed by ${minutes} minutes`, 'info');
            
            // Update UI to reflect the delay
            document.getElementById('timerStatus').textContent = `Timer: Delayed by ${minutes}min`;
            document.getElementById('timerStatus').className = 'badge bg-warning';
        }
        
        function sendReminderNow() {
            // In a real implementation, this would trigger an immediate reminder
            showToast('Reminder sent manually', 'info');
        }
        
        function forceReassignNow() {
            // In a real implementation, this would force immediate reassignment
            if (confirm('Are you sure you want to force reassign the current duty?')) {
                showToast('Duty reassigned manually', 'info');
            }
        }

        function renderMemberSelect(select, members) {
            select.innerHTML = '<option value="">Select Member...</option>';
            members.forEach(m => select.innerHTML += `<option value="${m.id}">${m.name}</option>`);
        }

        // Render Member Statistics
        function renderMemberStats() {
            const container = document.getElementById('member-stats-container');

            if (memberStats.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-3">No member data available</div>';
                return;
            }

            let html = '';
            memberStats.slice(0, 5).forEach((member, index) => {
                const lastDuty = member.last_duty_date && member.last_duty_date !== '2000-01-01'
                    ? new Date(member.last_duty_date).toLocaleDateString()
                    : 'Never';

                const isNext = index === 0;
                const cardClass = isNext ? 'member-stat-card member-next' : 'member-stat-card';

                html += `
                    <div class="${cardClass}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">${member.name}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-history me-1"></i>Last: ${lastDuty}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">${member.total_duties} duties</span>
                                ${isNext ? '<span class="badge bg-success mt-1">Next</span>' : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // Update Automation Steps
        function updateAutomationSteps(todayDuty, timer) {
            if (!todayDuty || todayDuty.status !== 'pending') return;

            const step1 = document.getElementById('step-reminder1');
            const step2 = document.getElementById('step-reminder2');
            const step3 = document.getElementById('step-reassign');

            // Reset all steps
            [step1, step2, step3].forEach(step => {
                step.className = 'step';
                step.innerHTML = step.id.split('-')[1].charAt(0).toUpperCase();
            });

            // Update based on reminder status
            if (todayDuty.last_reminder_sent === 'first_reminder') {
                step1.className = 'step completed';
                step1.innerHTML = '✓';
                step2.className = 'step active';
            } else if (todayDuty.last_reminder_sent === 'final_warning') {
                step1.className = 'step completed';
                step1.innerHTML = '✓';
                step2.className = 'step completed';
                step2.innerHTML = '✓';
                step3.className = 'step active';
            } else if (timer.reminder_status === 'overdue') {
                step1.className = 'step completed';
                step1.innerHTML = '✓';
                step2.className = 'step completed';
                step2.innerHTML = '✓';
                step3.className = 'step completed';
                step3.innerHTML = '✓';
            }
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
                let alertClass = 'alert-today';
                let timerClass = 'timer-normal';

                // Determine alert type based on reminder status
                if (timer.reminder_status === 'final_warning') {
                    alertClass = 'alert-reminder';
                    timerClass = 'timer-danger';
                } else if (timer.reminder_status === 'first_reminder') {
                    timerClass = 'timer-warning';
                } else if (timer.reminder_status === 'overdue') {
                    alertClass = 'alert-reminder';
                    timerClass = 'timer-danger';
                }

                // Automation Timer Logic
                if (today.status === 'pending') {
                    if (timer.hours_left > 0) {
                        timerHtml = `
                            <div class="mt-3 small text-white text-opacity-75">
                                <div class="d-flex justify-content-between mb-1 fw-bold">
                                    <span><i class="fas fa-stopwatch"></i> Auto-Reassign Timer</span>
                                    <span>${timer.hours_left}h left</span>
                                </div>
                                <div class="timer-container">
                                    <div class="timer-fill ${timerClass}" style="width: ${timer.percent}%"></div>
                                </div>
                            </div>`;
                    } else {
                        timerHtml = `<div class="mt-2 badge bg-danger text-white shadow-sm">Deadline Passed - Processing...</div>`;
                    }
                }

                // Reminder status badge
                let reminderBadge = '';
                if (today.last_reminder_sent === 'first_reminder') {
                    reminderBadge = '<span class="badge bg-warning ms-2">1st Reminder Sent</span>';
                } else if (today.last_reminder_sent === 'final_warning') {
                    reminderBadge = '<span class="badge bg-danger ms-2">Final Warning Sent</span>';
                }

                html += `
                <div class="card duty-alert ${alertClass} h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="badge bg-white text-primary bg-opacity-75 mb-2 shadow-sm">TODAY • ${dt.time} ${reminderBadge}</div>
                                <h3 class="mb-0 fw-bold">${today.name}</h3>
                                <small class="text-white text-opacity-75">Scheduled Duty</small>
                            </div>
                            ${today.status === 'pending' ? `
                            <form class="ajax-form" data-action="complete">
                                <input type="hidden" name="duty_id" value="${today.id}">
                                <input type="hidden" name="mark_completed" value="1">
                                <button class="btn btn-light text-primary fw-bold btn-sm rounded-pill px-4 shadow-lg">
                                    <i class="fas fa-check"></i> Mark Done
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
                            <small class="text-white text-opacity-75">Upcoming Duty</small>
                        </div>
                        <div>
                            <form class="ajax-form d-inline" data-action="remind">
                                <input type="hidden" name="duty_id" value="${tomorrow.id}">
                                <input type="hidden" name="send_reminder" value="1">
                                <button class="btn btn-light text-warning fw-bold btn-sm rounded-pill px-3 shadow-sm me-2">
                                    <i class="fas fa-bell"></i> Remind
                                </button>
                            </form>
                        </div>
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
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted">No upcoming duties found</td></tr>`;
                return;
            }

            duties.forEach(duty => {
                const dt = formatDateTime(duty.duty_date, duty.duty_time);

                // Status badge
                let statusBadge = '';
                if (duty.status === 'pending') {
                    if (duty.last_reminder_sent === 'final_warning') {
                        statusBadge = `<span class="badge-custom status-overdue"><i class="fas fa-exclamation-triangle me-1"></i> Final Warning</span>`;
                    } else if (duty.last_reminder_sent === 'first_reminder') {
                        statusBadge = `<span class="badge-custom status-warning"><i class="fas fa-bell me-1"></i> Reminder Sent</span>`;
                    } else {
                        statusBadge = `<span class="badge-custom status-pending"><i class="fas fa-clock me-1"></i> Pending</span>`;
                    }
                } else {
                    statusBadge = `<span class="badge-custom status-completed"><i class="fas fa-check me-1"></i> Done</span>`;
                }

                // Reminder info
                let reminderInfo = '-';
                if (duty.last_reminder_sent) {
                    const reminderText = duty.last_reminder_sent === 'first_reminder' ? '1st Reminder' : 'Final Warning';
                    reminderInfo = `<span class="badge bg-warning">${reminderText}</span>`;
                }

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
                    <td>${reminderInfo}</td>
                    <td class="pe-4 text-end">
                        <div class="d-flex justify-content-end align-items-center gap-1">
                            <!-- General Actions -->
                            ${duty.status === 'pending' ? `
                            <form class="d-inline ajax-form" data-action="complete">
                                <input type="hidden" name="duty_id" value="${duty.id}">
                                <input type="hidden" name="mark_completed" value="1">
                                <button class="btn btn-sm btn-outline-success btn-icon border-0" title="Mark Completed" data-bs-toggle="tooltip"><i class="fas fa-check"></i></button>
                            </form>
                            
                            <button class="btn btn-sm btn-outline-warning btn-icon border-0" onclick="openReminderModal(${duty.id})" title="Send Reminder">
                                <i class="fas fa-bell"></i>
                            </button>
                            
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
                                            <button class="btn btn-sm w-100 text-start btn-light"><i class="fas fa-bell text-warning me-2"></i> Send Tomorrow Reminder</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form class="ajax-form px-2 py-1" data-action="manual-email">
                                            <input type="hidden" name="duty_id" value="${duty.id}">
                                            <input type="hidden" name="send_manual_email" value="1">
                                            <button class="btn btn-sm w-100 text-start btn-light"><i class="fas fa-envelope text-primary me-2"></i> Send Assignment Email</button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form class="ajax-form px-2 py-1" data-action="force_reassign">
                                            <input type="hidden" name="duty_id" value="${duty.id}">
                                            <input type="hidden" name="force_reassign" value="1">
                                            <button class="btn btn-sm w-100 text-start btn-light text-danger"><i class="fas fa-sync-alt text-danger me-2"></i> Force Reassign</button>
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
        window.openEditModal = function (id, memberId, date, time) {
            document.getElementById('edit_duty_id').value = id;
            document.getElementById('edit_duty_date').value = date;
            document.getElementById('edit_duty_time').value = time || '09:00';

            const select = document.getElementById('edit_member_id');
            renderMemberSelect(select, memberList);
            select.value = memberId;

            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        };

        // Open Reminder Modal
        window.openReminderModal = function (dutyId) {
            document.getElementById('reminder_duty_id').value = dutyId;
            const modal = new bootstrap.Modal(document.getElementById('reminderModal'));
            modal.show();
        };

        // Delete Confirmation
        window.deleteDuty = async function (id) {
            if (!confirm('Are you sure you want to permanently delete this scheduled duty?')) return;

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

                if (data.reload || data.status === 'success') {
                    // Invalidate cache
                    dataCache = null;
                    loadInitialData();
                }

                // Close modals if open
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                if (editModal) editModal.hide();

                const reminderModal = bootstrap.Modal.getInstance(document.getElementById('reminderModal'));
                if (reminderModal) reminderModal.hide();

            } catch (e) {
                showToast('Connection error occurred', 'danger');
                console.error(e);
            }
        }

        // Global Form Submit Handler
        document.addEventListener('submit', function (e) {
            if (e.target.tagName === 'FORM') {
                e.preventDefault();
                const formData = new FormData(e.target);
                processRequest(formData);
                if (e.target.id === 'assignForm') e.target.reset();
            }
        });

        // Enhanced Toast Notification Helper
        function showToast(msg, type) {
            const icons = {
                success: 'fa-check-circle',
                warning: 'fa-exclamation-triangle',
                danger: 'fa-exclamation-circle',
                info: 'fa-info-circle'
            };
            
            const el = document.createElement('div');
            el.className = `toast show align-items-center text-white toast-${type} border-0 mb-2 shadow-lg`;
            el.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas ${icons[type]} me-2 fa-lg"></i>
                        <span class="fw-semibold">${msg}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>`;
            document.getElementById('toast-container').appendChild(el);
            setTimeout(() => el.remove(), 5000);
        }

        // Auto-refresh data every 2 minutes with debouncing
        const debouncedLoadData = debounce(loadInitialData, 500);
        setInterval(debouncedLoadData, 120000);
    </script>
</body>

</html>