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
        --primary: #4361ee;
        --primary-light: #4895ef;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --success-dark: #3a86ff;
        --danger: #f72585;
        --warning: #f8961e;
        --light: #f8f9fa;
        --dark: #212529;
        --gray: #6c757d;
        --gray-light: #e9ecef;
        --border-radius: 12px;
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
        color: var(--dark);
        line-height: 1.6;
        min-height: 100vh;
    }

    .water-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
    }

    .header {
        text-align: center;
        margin-bottom: 30px;
    }

    .header h1 {
        color: var(--primary);
        font-size: 1.8rem;
        margin-bottom: 8px;
        font-weight: 700;
    }

    .header p {
        color: var(--gray);
        font-size: 0.95rem;
    }

    /* Card Styles */
    .card {
        background: white;
        border-radius: var(--border-radius);
        padding: 25px;
        box-shadow: var(--shadow);
        margin-bottom: 25px;
        transition: var(--transition);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .card:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: var(--primary);
    }

    /* User Info Section */
    .user-info {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.4rem;
        box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
    }

    .user-details {
        flex: 1;
    }

    .user-name {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 5px;
    }

    .user-status {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .status-indicator.present {
        background: var(--success);
        box-shadow: 0 0 8px rgba(76, 201, 240, 0.6);
    }

    .status-indicator.absent {
        background: var(--danger);
        box-shadow: 0 0 8px rgba(247, 37, 133, 0.4);
    }

    .status-text {
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Toggle Switch */
    .toggle-container {
        margin-top: 15px;
    }

    .toggle-label {
        display: block;
        font-size: 0.9rem;
        color: var(--gray);
        margin-bottom: 8px;
        font-weight: 500;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
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
        background-color: var(--gray-light);
        transition: var(--transition);
        border-radius: 34px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: var(--transition);
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    input:checked+.toggle-slider {
        background: linear-gradient(to right, var(--success), var(--success-dark));
    }

    input:checked+.toggle-slider:before {
        transform: translateX(30px);
    }

    .toggle-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
    }

    .toggle-label-text {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gray);
    }

    .toggle-label-text.active {
        color: var(--success-dark);
    }

    /* Button Styles */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 24px;
        border: none;
        border-radius: 30px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
        width: 100%;
    }

    .btn-primary {
        background: linear-gradient(to right, var(--primary), var(--primary-light));
        color: white;
        box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
    }

    .btn-success {
        background: linear-gradient(to right, var(--success), var(--success-dark));
        color: white;
        box-shadow: 0 4px 10px rgba(76, 201, 240, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(76, 201, 240, 0.4);
    }

    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Duty Items */
    .duty-list {
        margin-top: 20px;
    }

    .duty-item {
        background: var(--light);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 4px solid var(--primary);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .duty-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    }

    .duty-date {
        font-weight: 600;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .duty-time {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--gray);
        font-size: 0.9rem;
    }

    /* Completed State */
    .completed-state {
        text-align: center;
        padding: 30px 20px;
    }

    .completed-icon {
        font-size: 3.5rem;
        color: var(--success);
        margin-bottom: 15px;
    }

    .completed-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 10px;
    }

    .completed-text {
        color: var(--gray);
        margin-bottom: 20px;
    }

    /* Alert Messages */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin: 15px 0;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: fadeIn 0.3s ease;
    }

    .alert-success {
        background: rgba(76, 201, 240, 0.1);
        color: var(--success-dark);
        border-left: 4px solid var(--success);
    }

    .alert-error {
        background: rgba(247, 37, 133, 0.1);
        color: var(--danger);
        border-left: 4px solid var(--danger);
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
        0% {
            left: -100%;
        }

        100% {
            left: 100%;
        }
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

    /* Responsive Design */
    @media (max-width: 576px) {
        .water-container {
            padding: 15px;
        }

        .card {
            padding: 20px;
        }

        .user-info {
            flex-direction: column;
            text-align: center;
            gap: 12px;
        }

        .header h1 {
            font-size: 1.5rem;
        }

        .card-title {
            font-size: 1.2rem;
        }

        .btn {
            padding: 14px 20px;
        }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        :root {
            --light: #2d3748;
            --dark: #f7fafc;
            --gray: #a0aec0;
            --gray-light: #4a5568;
        }

        body {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
        }

        .card {
            background: #2d3748;
            color: var(--dark);
        }

        .duty-item {
            background: #4a5568;
        }
    }
</style>

<div class="water-container">
    <div class="header">
        <h1>Water Duty System</h1>
        <p>Manage your presence and confirm water duties</p>
    </div>

    <!-- Presence Card -->
    <div class="card">
        <div class="card-title">
            <i class="fas fa-user"></i>
            Your Status
        </div>

        <div class="user-info">
            <div class="user-avatar">
                <?php echo strtoupper(substr($user_name, 0, 1)); ?>
            </div>
            <div class="user-details">
                <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="user-status">
                    <div class="status-indicator <?php echo $user_present ? 'present' : 'absent'; ?>"></div>
                    <div class="status-text">
                        <?php echo $user_present ? 'Currently Present' : 'Currently Absent'; ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="presenceMessageContainer"></div>

        <div class="toggle-container">
            <div class="toggle-label">Update Your Presence Status</div>
            <label class="toggle-switch">
                <input type="checkbox" id="presenceToggle" <?php echo $user_present ? 'checked' : ''; ?>
                    onchange="togglePresence()">
                <span class="toggle-slider"></span>
            </label>
            <div class="toggle-labels">
                <span class="toggle-label-text <?php echo $user_present ? '' : 'active'; ?>">Absent</span>
                <span class="toggle-label-text <?php echo $user_present ? 'active' : ''; ?>">Present</span>
            </div>
        </div>
    </div>

    <!-- Water Duties Card -->
    <div class="card">
        <div class="card-title">
            <i class="fas fa-tint"></i>
            Water Duties
        </div>

        <div id="dutyMessageContainer"></div>

        <?php if (!empty($pending_duties)): ?>
            <p style="margin-bottom: 20px; color: var(--gray); text-align: center;">You have
                <?php echo count($pending_duties); ?> pending water
                duty<?php echo count($pending_duties) > 1 ? 'ies' : ''; ?>
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

                        <button class="btn btn-success" data-duty-id="<?php echo $duty['id']; ?>"
                            onclick="confirmWaterDuty(this)">
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

<script>
    let isUpdating = false;

    // Presence Toggle Function
    function togglePresence() {
        if (isUpdating) return;

        const toggle = document.getElementById('presenceToggle');
        const newStatus = toggle.checked;

        // Show loading state
        isUpdating = true;
        toggle.disabled = true;

        // Clear previous messages
        document.getElementById('presenceMessageContainer').innerHTML = '';

        // Send request
        fetch('user_process/user_water_duty.php?action=update_presence', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'is_present=' + (newStatus ? '1' : '0')
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network error: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update UI
                    const statusIndicator = document.querySelector('.status-indicator');
                    const statusText = document.querySelector('.status-text');
                    const toggleLabels = document.querySelectorAll('.toggle-label-text');

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
                    // Revert toggle state on error
                    toggle.checked = !newStatus;
                    showMessage('presenceMessageContainer', 'Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                // Revert toggle state on error
                toggle.checked = !newStatus;
                showMessage('presenceMessageContainer', 'Network error. Please try again.', 'error');
            })
            .finally(() => {
                isUpdating = false;
                toggle.disabled = false;
            });
    }

    // Water Duty Confirmation Function
    function confirmWaterDuty(button) {
        const dutyId = button.getAttribute('data-duty-id');

        // Show loading state
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Confirming...';
        button.classList.add('loading');
        button.disabled = true;

        // Send request to confirm water duty
        fetch('user_process/user_water_duty.php?action=confirm_duty', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'duty_id=' + dutyId
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network error: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remove the duty item from UI
                    const dutyItem = document.getElementById('duty-' + dutyId);
                    dutyItem.style.opacity = '0';
                    dutyItem.style.transform = 'translateY(-10px)';

                    setTimeout(() => {
                        dutyItem.remove();

                        showMessage('dutyMessageContainer', data.message, 'success');

                        // Check if all duties are completed
                        const remainingDuties = document.querySelectorAll('.duty-item');
                        if (remainingDuties.length === 0) {
                            // Show completion message after a delay
                            setTimeout(() => {
                                location.reload(); // Reload to show "All Done" message
                            }, 1500);
                        }
                    }, 300);
                } else {
                    showMessage('dutyMessageContainer', 'Error: ' + data.message, 'error');
                    // Reset button if failed
                    button.innerHTML = '<i class="fas fa-check"></i> Confirm Water Given';
                    button.classList.remove('loading');
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                showMessage('dutyMessageContainer', 'Network error. Please try again.', 'error');
                // Reset button if failed
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

        // Auto remove after 4 seconds
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