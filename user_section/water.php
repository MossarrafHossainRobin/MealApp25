<?php
// user_section/water.php - Modern Water Duty Confirmation System

// Get only pending water duties for this user
$user_id = $_SESSION['user_id'];

// Get database connection
require_once __DIR__ . '/../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Fetch only pending water duties
$stmt = $db->prepare("SELECT id, duty_date, duty_time FROM water_duties WHERE member_id = ? AND status = 'Pending' ORDER BY duty_date ASC");
$stmt->execute([$user_id]);
$pending_duties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user presence status from members table
$user_stmt = $db->prepare("SELECT is_active, name FROM members WHERE id = ?");
$user_stmt->execute([$user_id]);
$user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

$user_present = $user_data['is_active'] ?? 1;
$user_name = $user_data['name'] ?? $_SESSION['user_name'] ?? 'User';
?>

<style>
    :root {
        --primary: #d63384;
        --primary-dark: #c2185b;
        --primary-light: #f8bbd9;
        --gradient: linear-gradient(135deg, #d63384, #e91e63);
        --shadow: 0 2px 15px rgba(214, 51, 132, 0.15);
        --transition: all 0.3s ease;
    }

    .water-section {
        padding: 0;
        margin: 0;
    }

    /* Mobile First Design */
    .mobile-water {
        display: block;
    }
    
    .desktop-water {
        display: none;
    }

    /* Mobile Water Styles */
    .mobile-water {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 0;
        margin: 0;
    }

    .water-header {
        background: var(--gradient);
        color: white;
        padding: 15px 20px;
        text-align: center;
    }

    .water-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .water-subtitle {
        font-size: 0.8rem;
        opacity: 0.9;
    }

    .water-content {
        padding: 20px 15px;
        padding-bottom: 80px;
    }

    /* Status Card */
    .status-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: var(--primary);
    }

    /* User Info */
    .user-info-compact {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .user-avatar-small {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: var(--gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .user-details-compact {
        flex: 1;
    }

    .user-name-compact {
        font-size: 1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .user-status-compact {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .status-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .status-indicator.present {
        background: #27ae60;
    }

    .status-indicator.absent {
        background: #e74c3c;
    }

    .status-text {
        font-size: 0.8rem;
        color: #666;
        font-weight: 500;
    }

    /* Toggle Switch */
    .toggle-container {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .toggle-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
        text-align: center;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
        margin: 0 auto;
        display: block;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: var(--transition);
        border-radius: 34px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: var(--transition);
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    input:checked + .toggle-slider {
        background: var(--gradient);
    }

    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    .toggle-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
        max-width: 200px;
        margin-left: auto;
        margin-right: auto;
    }

    .toggle-label-text {
        font-size: 0.75rem;
        font-weight: 600;
        color: #999;
    }

    .toggle-label-text.active {
        color: var(--primary);
    }

    /* Duties Card */
    .duties-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--shadow);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .duty-list {
        margin-top: 15px;
    }

    .duty-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        border-left: 4px solid var(--primary);
        transition: var(--transition);
    }

    .duty-item:active {
        transform: scale(0.98);
    }

    .duty-date {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }

    .duty-time {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #666;
        font-size: 0.8rem;
        margin-bottom: 12px;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        border: none;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        width: 100%;
    }

    .btn-primary {
        background: var(--gradient);
        color: white;
        box-shadow: 0 2px 8px rgba(214, 51, 132, 0.3);
    }

    .btn-primary:active {
        transform: scale(0.95);
    }

    .btn-success {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
        box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
    }

    .btn-success:active {
        transform: scale(0.95);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Completed State */
    .completed-state {
        text-align: center;
        padding: 30px 20px;
    }

    .completed-icon {
        font-size: 3rem;
        color: #27ae60;
        margin-bottom: 15px;
    }

    .completed-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .completed-text {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    /* Alert Messages */
    .alert {
        padding: 12px 15px;
        border-radius: 10px;
        margin: 15px 0;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: fadeIn 0.3s ease;
    }

    .alert-success {
        background: rgba(39, 174, 96, 0.1);
        color: #27ae60;
        border-left: 4px solid #27ae60;
    }

    .alert-error {
        background: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
        border-left: 4px solid #e74c3c;
    }

    /* Loading Animation */
    .loading {
        position: relative;
        overflow: hidden;
    }

    .loading::after {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Desktop Water Styles */
    @media (min-width: 992px) {
        .mobile-water {
            display: none;
        }
        
        .desktop-water {
            display: block;
            padding: 30px 0;
            background: #f8f9fa;
            min-height: calc(100vh - 80px);
        }

        .desktop-water-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .desktop-water-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .desktop-water-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .desktop-water-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        .desktop-status-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .desktop-duties-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .desktop-user-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .desktop-user-avatar {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .desktop-user-details {
            flex: 1;
        }

        .desktop-user-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .desktop-toggle-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            border: 1px solid #e9ecef;
        }

        .desktop-toggle-label {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
        }

        .desktop-toggle-switch {
            width: 60px;
            height: 30px;
        }

        .desktop-toggle-slider:before {
            height: 22px;
            width: 22px;
        }

        input:checked + .desktop-toggle-slider:before {
            transform: translateX(30px);
        }

        .desktop-duty-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary);
            transition: var(--transition);
        }

        .desktop-duty-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .desktop-btn {
            padding: 15px 25px;
            font-size: 1rem;
        }

        .desktop-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(214, 51, 132, 0.4);
        }
    }

    /* Responsive Design */
    @media (max-width: 480px) {
        .water-content {
            padding: 15px 12px;
        }

        .status-card, .duties-card {
            padding: 15px;
        }

        .user-avatar-small {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .user-name-compact {
            font-size: 0.9rem;
        }

        .btn {
            padding: 10px 15px;
            font-size: 0.85rem;
        }
    }
</style>

<!-- Mobile Water Section -->
<div class="mobile-water">
    <div class="water-header">
        <div class="water-title">Water Duty</div>
        <div class="water-subtitle">Manage your water duties</div>
    </div>

    <div class="water-content">
        <!-- Status Card -->
        <div class="status-card">
            <div class="card-title">
                <i class="fas fa-user"></i>
                Your Status
            </div>

            <div class="user-info-compact">
                <div class="user-avatar-small">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <div class="user-details-compact">
                    <div class="user-name-compact"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="user-status-compact">
                        <div class="status-indicator <?php echo $user_present ? 'present' : 'absent'; ?>"></div>
                        <div class="status-text">
                            <?php echo $user_present ? 'Currently Present' : 'Currently Absent'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="presenceMessageContainer"></div>

            <div class="toggle-container">
                <div class="toggle-label">Update Presence Status</div>
                <label class="toggle-switch">
                    <input type="checkbox" id="presenceToggle" <?php echo $user_present ? 'checked' : ''; ?> onchange="togglePresence()">
                    <span class="toggle-slider"></span>
                </label>
                <div class="toggle-labels">
                    <span class="toggle-label-text <?php echo $user_present ? '' : 'active'; ?>">Absent</span>
                    <span class="toggle-label-text <?php echo $user_present ? 'active' : ''; ?>">Present</span>
                </div>
            </div>
        </div>

        <!-- Duties Card -->
        <div class="duties-card">
            <div class="card-title">
                <i class="fas fa-tint"></i>
                Water Duties
            </div>

            <div id="dutyMessageContainer"></div>

            <?php if (!empty($pending_duties)): ?>
                    <p style="margin-bottom: 15px; color: #666; text-align: center; font-size: 0.9rem;">
                        You have <?php echo count($pending_duties); ?> pending water duty<?php echo count($pending_duties) > 1 ? 'ies' : ''; ?>
                    </p>

                    <div class="duty-list">
                        <?php foreach ($pending_duties as $duty): ?>
                                <div class="duty-item" id="duty-<?php echo $duty['id']; ?>">
                                    <div class="duty-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo date('M d, Y', strtotime($duty['duty_date'])); ?>
                                    </div>

                                    <?php if (!empty($duty['duty_time'])): ?>
                                            <div class="duty-time">
                                                <i class="fas fa-clock"></i>
                                                <?php echo htmlspecialchars($duty['duty_time']); ?>
                                            </div>
                                    <?php endif; ?>

                                    <button class="btn btn-success" data-duty-id="<?php echo $duty['id']; ?>" onclick="confirmWaterDuty(this)">
                                        <i class="fas fa-check"></i>
                                        Confirm Water Given
                                    </button>
                                </div>
                        <?php endforeach; ?>
                    </div>

            <?php else: ?>
                    <div class="completed-state">
                        <div class="completed-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="completed-title">All Duties Completed!</h3>
                        <p class="completed-text">You have successfully completed all your water duties.</p>
                        <button class="btn btn-primary" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                            Refresh Status
                        </button>
                    </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Desktop Water Section -->
<div class="desktop-water">
    <div class="desktop-water-container">
        <div class="desktop-water-header">
            <h1 class="desktop-water-title">Water Duty Management</h1>
            <p class="desktop-water-subtitle">Manage your presence and water duties</p>
        </div>

        <!-- Status Card -->
        <div class="desktop-status-card">
            <div class="card-title">
                <i class="fas fa-user"></i>
                Your Status
            </div>

            <div class="desktop-user-info">
                <div class="desktop-user-avatar">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <div class="desktop-user-details">
                    <div class="desktop-user-name"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="user-status-compact">
                        <div class="status-indicator <?php echo $user_present ? 'present' : 'absent'; ?>"></div>
                        <div class="status-text">
                            <?php echo $user_present ? 'Currently Present' : 'Currently Absent'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="desktopPresenceMessageContainer"></div>

            <div class="desktop-toggle-container">
                <div class="desktop-toggle-label">Update Your Presence Status</div>
                <label class="toggle-switch desktop-toggle-switch">
                    <input type="checkbox" id="desktopPresenceToggle" <?php echo $user_present ? 'checked' : ''; ?> onchange="toggleDesktopPresence()">
                    <span class="toggle-slider desktop-toggle-slider"></span>
                </label>
                <div class="toggle-labels">
                    <span class="toggle-label-text <?php echo $user_present ? '' : 'active'; ?>">Absent</span>
                    <span class="toggle-label-text <?php echo $user_present ? 'active' : ''; ?>">Present</span>
                </div>
            </div>
        </div>

        <!-- Duties Card -->
        <div class="desktop-duties-card">
            <div class="card-title">
                <i class="fas fa-tint"></i>
                Water Duties
            </div>

            <div id="desktopDutyMessageContainer"></div>

            <?php if (!empty($pending_duties)): ?>
                    <p style="margin-bottom: 20px; color: #666; text-align: center; font-size: 1rem;">
                        You have <?php echo count($pending_duties); ?> pending water duty<?php echo count($pending_duties) > 1 ? 'ies' : ''; ?>
                    </p>

                    <div class="duty-list">
                        <?php foreach ($pending_duties as $duty): ?>
                                <div class="desktop-duty-item" id="desktop-duty-<?php echo $duty['id']; ?>">
                                    <div class="duty-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo date('M d, Y', strtotime($duty['duty_date'])); ?>
                                    </div>

                                    <?php if (!empty($duty['duty_time'])): ?>
                                            <div class="duty-time">
                                                <i class="fas fa-clock"></i>
                                                <?php echo htmlspecialchars($duty['duty_time']); ?>
                                            </div>
                                    <?php endif; ?>

                                    <button class="btn btn-success desktop-btn" data-duty-id="<?php echo $duty['id']; ?>" onclick="confirmDesktopWaterDuty(this)">
                                        <i class="fas fa-check"></i>
                                        Confirm Water Given
                                    </button>
                                </div>
                        <?php endforeach; ?>
                    </div>

            <?php else: ?>
                    <div class="completed-state">
                        <div class="completed-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="completed-title">All Duties Completed!</h3>
                        <p class="completed-text">You have successfully completed all your water duties.</p>
                        <button class="btn btn-primary desktop-btn" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                            Refresh Status
                        </button>
                    </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    let isUpdating = false;

    // Mobile Presence Toggle Function
    function togglePresence() {
        if (isUpdating) return;

        const toggle = document.getElementById('presenceToggle');
        const newStatus = toggle.checked;

        isUpdating = true;
        toggle.disabled = true;

        document.getElementById('presenceMessageContainer').innerHTML = '';

        fetch('user_process/user_water_duty.php?action=update_presence', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'is_present=' + (newStatus ? '1' : '0')
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusIndicator = document.querySelector('.mobile-water .status-indicator');
                const statusText = document.querySelector('.mobile-water .status-text');
                const toggleLabels = document.querySelectorAll('.mobile-water .toggle-label-text');

                if (newStatus) {
                    statusIndicator.className = 'status-indicator present';
                    statusText.textContent = 'Currently Present';
                    toggleLabels[0].classList.remove('active');
                    toggleLabels[1].classList.add('active');
                    showMessage('presenceMessageContainer', data.message, 'success');
                } else {
                    statusIndicator.className = 'status-indicator absent';
                    statusText.textContent = 'Currently Absent';
                    toggleLabels[0].classList.add('active');
                    toggleLabels[1].classList.remove('active');
                    showMessage('presenceMessageContainer', data.message, 'success');
                }
            } else {
                toggle.checked = !newStatus;
                showMessage('presenceMessageContainer', 'Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            toggle.checked = !newStatus;
            showMessage('presenceMessageContainer', 'Network error. Please try again.', 'error');
        })
        .finally(() => {
            isUpdating = false;
            toggle.disabled = false;
        });
    }

    // Desktop Presence Toggle Function
    function toggleDesktopPresence() {
        if (isUpdating) return;

        const toggle = document.getElementById('desktopPresenceToggle');
        const newStatus = toggle.checked;

        isUpdating = true;
        toggle.disabled = true;

        document.getElementById('desktopPresenceMessageContainer').innerHTML = '';

        fetch('user_process/user_water_duty.php?action=update_presence', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'is_present=' + (newStatus ? '1' : '0')
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusIndicator = document.querySelector('.desktop-water .status-indicator');
                const statusText = document.querySelector('.desktop-water .status-text');
                const toggleLabels = document.querySelectorAll('.desktop-water .toggle-label-text');

                if (newStatus) {
                    statusIndicator.className = 'status-indicator present';
                    statusText.textContent = 'Currently Present';
                    toggleLabels[0].classList.remove('active');
                    toggleLabels[1].classList.add('active');
                    showMessage('desktopPresenceMessageContainer', data.message, 'success');
                } else {
                    statusIndicator.className = 'status-indicator absent';
                    statusText.textContent = 'Currently Absent';
                    toggleLabels[0].classList.add('active');
                    toggleLabels[1].classList.remove('active');
                    showMessage('desktopPresenceMessageContainer', data.message, 'success');
                }
            } else {
                toggle.checked = !newStatus;
                showMessage('desktopPresenceMessageContainer', 'Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            toggle.checked = !newStatus;
            showMessage('desktopPresenceMessageContainer', 'Network error. Please try again.', 'error');
        })
        .finally(() => {
            isUpdating = false;
            toggle.disabled = false;
        });
    }

    // Mobile Water Duty Confirmation
    function confirmWaterDuty(button) {
        const dutyId = button.getAttribute('data-duty-id');

        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Confirming...';
        button.classList.add('loading');
        button.disabled = true;

        fetch('user_process/user_water_duty.php?action=confirm_duty', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'duty_id=' + dutyId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const dutyItem = document.getElementById('duty-' + dutyId);
                dutyItem.style.opacity = '0';
                dutyItem.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    dutyItem.remove();
                    showMessage('dutyMessageContainer', data.message, 'success');

                    const remainingDuties = document.querySelectorAll('.mobile-water .duty-item');
                    if (remainingDuties.length === 0) {
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                }, 300);
            } else {
                showMessage('dutyMessageContainer', 'Error: ' + data.message, 'error');
                button.innerHTML = '<i class="fas fa-check"></i> Confirm Water Given';
                button.classList.remove('loading');
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            showMessage('dutyMessageContainer', 'Network error. Please try again.', 'error');
            button.innerHTML = '<i class="fas fa-check"></i> Confirm Water Given';
            button.classList.remove('loading');
            button.disabled = false;
        });
    }

    // Desktop Water Duty Confirmation
    function confirmDesktopWaterDuty(button) {
        const dutyId = button.getAttribute('data-duty-id');

        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Confirming...';
        button.classList.add('loading');
        button.disabled = true;

        fetch('user_process/user_water_duty.php?action=confirm_duty', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'duty_id=' + dutyId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const dutyItem = document.getElementById('desktop-duty-' + dutyId);
                dutyItem.style.opacity = '0';
                dutyItem.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    dutyItem.remove();
                    showMessage('desktopDutyMessageContainer', data.message, 'success');

                    const remainingDuties = document.querySelectorAll('.desktop-water .desktop-duty-item');
                    if (remainingDuties.length === 0) {
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                }, 300);
            } else {
                showMessage('desktopDutyMessageContainer', 'Error: ' + data.message, 'error');
                button.innerHTML = '<i class="fas fa-check"></i> Confirm Water Given';
                button.classList.remove('loading');
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            showMessage('desktopDutyMessageContainer', 'Network error. Please try again.', 'error');
            button.innerHTML = '<i class="fas fa-check"></i> Confirm Water Given';
            button.classList.remove('loading');
            button.disabled = false;
        });
    }

    // Show message function
    function showMessage(containerId, message, type) {
        const container = document.getElementById(containerId);
        const alertDiv = document.createElement('div');
        alertDiv.className = type === 'success' ? 'alert alert-success' : 'alert alert-error';

        const icon = document.createElement('i');
        icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        alertDiv.appendChild(icon);

        const text = document.createTextNode(message);
        alertDiv.appendChild(text);

        container.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.style.opacity = '0';
                alertDiv.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 300);
            }
        }, 4000);
    }
</script>