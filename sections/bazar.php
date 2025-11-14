<div class="container mt-4">
    <h2 class="text-success mb-4">Advanced Bazar Management</h2>
    
    <!-- Month/Year Selector -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title text-success">Select Month & Year</h5>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-select" id="monthSelect" onchange="loadBazarData()">
                                <?php
                                $currentMonth = date('n');
                                $months = [
                                    1 => 'January',
                                    2 => 'February',
                                    3 => 'March',
                                    4 => 'April',
                                    5 => 'May',
                                    6 => 'June',
                                    7 => 'July',
                                    8 => 'August',
                                    9 => 'September',
                                    10 => 'October',
                                    11 => 'November',
                                    12 => 'December'
                                ];
                                foreach ($months as $num => $name): ?>
                                        <option value="<?php echo $num; ?>" <?php echo $num == $currentMonth ? 'selected' : ''; ?>>
                                            <?php echo $name; ?>
                                        </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="yearSelect" onchange="loadBazarData()">
                                <?php
                                $currentYear = date('Y');
                                for ($year = $currentYear - 1; $year <= $currentYear + 1; $year++): ?>
                                        <option value="<?php echo $year; ?>" <?php echo $year == $currentYear ? 'selected' : ''; ?>>
                                            <?php echo $year; ?>
                                        </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Bazar</h5>
                    <h3 class="card-text" id="totalBazar">BDT 0.00</h3>
                    <small id="selectedMonthText"><?php echo $months[$currentMonth] . ' ' . $currentYear; ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Count</h5>
                    <h3 class="card-text" id="totalCount">0</h3>
                    <small>Entries this month</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Average Bazar</h5>
                    <h3 class="card-text" id="averageBazar">BDT 0.00</h3>
                    <small>Per entry</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Daily Average</h5>
                    <h3 class="card-text" id="dailyAverage">BDT 0.00</h3>
                    <small>Per day this month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bazar Form -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5 class="card-title text-success">Add Bazar Entry for <span id="currentMonthYear"><?php echo $months[$currentMonth] . ' ' . $currentYear; ?></span></h5>
            <form id="bazarForm" method="POST" onsubmit="submitBazar(event)">
                <input type="hidden" name="action" value="add_bazar">
                <input type="hidden" name="current_section" value="bazar">
                <input type="hidden" name="selected_month" id="selectedMonth" value="<?php echo $currentMonth; ?>">
                <input type="hidden" name="selected_year" id="selectedYear" value="<?php echo $currentYear; ?>">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Select Member</label>
                        <select name="member_id" class="form-select" required>
                            <option value="">Choose member...</option>
                            <?php foreach ($all_members as $member): ?>
                                    <option value="<?php echo $member['id']; ?>">
                                        <?php echo htmlspecialchars($member['name']); ?>
                                    </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount (BDT)</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                        <div class="form-text">Positive for contribution, negative for refund/expense</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bazar Count</label>
                        <input type="number" name="bazar_count" class="form-control" min="1" value="1" required>
                        <div class="form-text">Number of bazar entries to add</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Description (Optional)</label>
                        <input type="text" name="description" class="form-control" placeholder="Enter description...">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <input type="date" name="bazar_date" class="form-control" id="bazarDate" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Add Bazar
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </button>
            </form>
        </div>
    </div>

    <!-- Bazar Entries Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Bazar Entries for <span id="tableMonthYear"><?php echo $months[$currentMonth] . ' ' . $currentYear; ?></span></h5>
                <div>
                    <button class="btn btn-outline-primary btn-sm" onclick="loadBazarData()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="exportBazarData()">
                        <i class="bi bi-download"></i> Export
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="addSampleData()">
                        <i class="bi bi-plus-circle"></i> Add Sample Data
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bazarTableBody">
                        <!-- Data will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
            <div id="noDataMessage" class="text-center py-4" style="display: none;">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                <p class="text-muted mt-2">No bazar entries found for selected month</p>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script>
// Global variables
let bazarData = [];
let currentMonth = <?php echo $currentMonth; ?>;
let currentYear = <?php echo $currentYear; ?>;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today
    const today = new Date();
    document.getElementById('bazarDate').value = today.toISOString().split('T')[0];
    
    // Load initial data
    loadBazarData();
});

// Show loading overlay
function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

// Hide loading overlay
function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

// Load bazar data for selected month/year
function loadBazarData() {
    showLoading();
    
    const month = document.getElementById('monthSelect').value;
    const year = document.getElementById('yearSelect').value;
    
    currentMonth = parseInt(month);
    currentYear = parseInt(year);
    
    // Update hidden fields
    document.getElementById('selectedMonth').value = month;
    document.getElementById('selectedYear').value = year;
    
    // Update display texts
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];
    
    const monthYearText = `${monthNames[month-1]} ${year}`;
    document.getElementById('currentMonthYear').textContent = monthYearText;
    document.getElementById('tableMonthYear').textContent = monthYearText;
    document.getElementById('selectedMonthText').textContent = monthYearText;
    
    // Fetch data with month and year parameters
    fetch(`process/get_bazar_data.php?month=${month}&year=${year}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        console.log('API Response:', data);
        
        if (data.success) {
            bazarData = data.entries || [];
            updateBazarTable();
            updateStats();
            console.log(`Bazar data loaded for ${monthYearText}:`, bazarData);
        } else {
            showAlert(data.error || 'Error loading bazar data!', 'danger');
            bazarData = [];
            updateBazarTable();
            updateStats();
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showAlert('Network error occurred: ' + error.message, 'danger');
        bazarData = [];
        updateBazarTable();
        updateStats();
    });
}

// Submit bazar form via AJAX
function submitBazar(event) {
    event.preventDefault();
    showLoading();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validate date is within selected month
    const bazarDate = new Date(formData.get('bazar_date'));
    const selectedMonth = parseInt(formData.get('selected_month'));
    const selectedYear = parseInt(formData.get('selected_year'));
    
    if (bazarDate.getMonth() + 1 !== selectedMonth || bazarDate.getFullYear() !== selectedYear) {
        hideLoading();
        showAlert(`Please select a date within ${getMonthName(selectedMonth)} ${selectedYear}`, 'warning');
        return;
    }
    
    fetch('process/actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        console.log('Add bazar response:', data);
        
        if (data.success) {
            showAlert('Bazar entry added successfully!', 'success');
            resetForm();
            loadBazarData();
        } else {
            showAlert(data.error || 'Error adding bazar entry!', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showAlert('Network error occurred!', 'danger');
    });
}

// Delete bazar entry
function deleteBazar(bazarId) {
    if (!confirm('Are you sure you want to delete this bazar entry?')) {
        return;
    }
    
    showLoading();
    
    const formData = new FormData();
    formData.append('action', 'delete_bazar');
    formData.append('current_section', 'bazar');
    formData.append('bazar_id', bazarId);
    
    fetch('process/actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        console.log('Delete response:', data);
        
        if (data.success) {
            showAlert('Bazar entry deleted successfully!', 'success');
            loadBazarData();
        } else {
            showAlert(data.error || 'Error deleting bazar entry!', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showAlert('Network error occurred!', 'danger');
    });
}

// Add sample data for testing
function addSampleData() {
    if (!confirm('This will add sample bazar data for the selected month. Continue?')) {
        return;
    }
    
    showLoading();
    
    const month = document.getElementById('monthSelect').value;
    const year = document.getElementById('yearSelect').value;
    
    const formData = new FormData();
    formData.append('action', 'add_sample_bazar');
    formData.append('month', month);
    formData.append('year', year);
    
    fetch('process/actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        console.log('Sample data response:', data);
        
        if (data.success) {
            showAlert('Sample data added successfully!', 'success');
            loadBazarData();
        } else {
            showAlert(data.error || 'Error adding sample data!', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showAlert('Network error occurred!', 'danger');
    });
}

// Update bazar table
function updateBazarTable() {
    const tableBody = document.getElementById('bazarTableBody');
    const noDataMessage = document.getElementById('noDataMessage');
    
    if (bazarData.length === 0) {
        tableBody.innerHTML = '';
        noDataMessage.style.display = 'block';
        return;
    }
    
    noDataMessage.style.display = 'none';
    tableBody.innerHTML = '';
    
    bazarData.forEach(entry => {
        const row = document.createElement('tr');
        const amountClass = entry.amount < 0 ? 'negative-amount' : 'positive-amount';
        
        row.innerHTML = `
            <td>${formatDate(entry.bazar_date)}</td>
            <td>${escapeHtml(entry.member_name)}</td>
            <td>
                <span class="${amountClass}">
                    BDT ${formatCurrency(entry.amount)}
                </span>
            </td>
            <td>${escapeHtml(entry.description || '-')}</td>
            <td>
                <span class="badge bg-secondary bazar-count-badge">
                    ${entry.bazar_count || 1}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="deleteBazar(${entry.id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
}

// Update statistics
function updateStats() {
    const totalBazar = bazarData.reduce((sum, entry) => sum + parseFloat(entry.amount), 0);
    const totalCount = bazarData.reduce((sum, entry) => sum + (parseInt(entry.bazar_count) || 1), 0);
    const averageBazar = totalCount > 0 ? totalBazar / totalCount : 0;
    
    // Calculate daily average for the month
    const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();
    const dailyAverage = daysInMonth > 0 ? totalBazar / daysInMonth : 0;
    
    document.getElementById('totalBazar').textContent = `BDT ${formatCurrency(totalBazar)}`;
    document.getElementById('totalCount').textContent = totalCount;
    document.getElementById('averageBazar').textContent = `BDT ${formatCurrency(averageBazar)}`;
    document.getElementById('dailyAverage').textContent = `BDT ${formatCurrency(dailyAverage)}`;
}

// Reset form
function resetForm() {
    document.getElementById('bazarForm').reset();
    document.getElementById('bazarDate').value = new Date().toISOString().split('T')[0];
    document.querySelector('[name="bazar_count"]').value = 1;
}

// Export bazar data
function exportBazarData() {
    if (bazarData.length === 0) {
        showAlert('No data to export!', 'warning');
        return;
    }
    
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];
    
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Bazar Report - " + monthNames[currentMonth-1] + " " + currentYear + "\n";
    csvContent += "Date,Member,Amount,Description,Count\n";
    
    bazarData.forEach(entry => {
        const row = [
            formatDate(entry.bazar_date),
            entry.member_name,
            entry.amount,
            entry.description || '',
            entry.bazar_count || 1
        ].map(field => `"${field}"`).join(',');
        csvContent += row + "\n";
    });
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `bazar_${monthNames[currentMonth-1]}_${currentYear}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Utility functions
function formatCurrency(amount) {
    return Math.abs(amount).toFixed(2);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getMonthName(monthNumber) {
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];
    return monthNames[monthNumber - 1];
}

function showAlert(message, type) {
    // Remove existing alerts
    const existingAlert = document.querySelector('.alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to page
    document.querySelector('.container').insertBefore(alert, document.querySelector('.container').firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>

<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    display: none;
}
.stats-card {
    transition: transform 0.3s;
}
.stats-card:hover {
    transform: translateY(-5px);
}
.negative-amount {
    color: #dc3545;
    font-weight: bold;
}
.positive-amount {
    color: #198754;
    font-weight: bold;
}
.bazar-count-badge {
    font-size: 0.8rem;
}
.card-title {
    margin-bottom: 0.5rem;
}
</style>