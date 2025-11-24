<div class="row">
    <div class="col-12">
        <div class="card mb-4shadow-sm">
            <div class="card-header bg-primary text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview
                    </h5>
                    <div class="d-flex align-items-center">
                        <label class="text-white me-2 mb-0 d-none d-sm-block">View Month:</label>
                        <select class="form-select form-select-sm" id="monthSelector" style="width: 150px;">
                            <?php
                            // Generate last 12 months
                            for ($i = 0; $i < 12; $i++) {
                                $month = date('Y-m', strtotime("-$i months"));
                                $month_name = date('F Y', strtotime($month . '-01'));
                                $selected = $i === 0 ? 'selected' : '';
                                echo "<option value='{$month}' {$selected}>{$month_name}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <h6 class="text-muted border-bottom pb-2 mb-3">
                    <i class="fas fa-chart-bar me-1"></i>
                    <span id="currentMonthDisplay"><?php echo date('F Y'); ?></span> Statistics
                </h6>
                <div class="row" id="dashboardStats">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card primary h-100 border-start border-5 border-primary">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Members</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMembers">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-primary opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card success h-100 border-start border-5 border-success">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Bazar</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBazar">৳0</div>
                                        <div class="small text-muted" id="bazarEntries">0 entries</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-success opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card info h-100 border-start border-5 border-info">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Meals</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMeals">0</div>
                                        <div class="small text-muted" id="avgMealsPerDayText">0 per day</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-utensils fa-2x text-info opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card warning h-100 border-start border-5 border-warning">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Meal Rate</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="mealRate">৳0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calculator fa-2x text-warning opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card secondary h-100 border-start border-5 border-secondary">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                            Monthly Flat Cost</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthlyFlatCost">৳0
                                        </div>
                                        <div class="small text-muted" id="perMemberCost">Per member: ৳0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-home fa-2x text-secondary opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card danger h-100 border-start border-5 border-danger">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Pending Water Duties</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingWaterDuties">0
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tint fa-2x text-danger opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card dark h-100 border-start border-5 border-dark">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                            Total Bazar Entries</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBazarEntries">0
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-receipt fa-2x text-dark opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card info h-100 border-start border-5 border-info">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Avg Meals per Day</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgMealsPerDay">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-info opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

---

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2 text-primary"></i>Monthly Trends (Last 6 Months)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Bazar</th>
                                <th class="text-end">Meals</th>
                                <th class="text-end">Flat Cost</th>
                            </tr>
                        </thead>
                        <tbody id="monthlyTrendsTable">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    <div class="spinner-border spinner-border-sm me-2"></div>
                                    Loading trends...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2 text-primary"></i>Recent Activities
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="recentActivities">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    <div class="spinner-border spinner-border-sm me-2"></div>
                                    Loading activities...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

---

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="fas fa-bolt me-2 text-primary"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="admin.php?section=members" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-users me-2"></i>Manage Members
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="admin.php?section=bazar" class="btn btn-success w-100 py-2">
                            <i class="fas fa-shopping-cart me-2"></i>Add Bazar
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="admin.php?section=meal" class="btn btn-info w-100 py-2">
                            <i class="fas fa-utensils me-2"></i>Add Meal Count
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="admin.php?section=flat" class="btn btn-secondary w-100 py-2">
                            <i class="fas fa-home me-2"></i>Manage Flat Rent
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="admin.php?section=water" class="btn btn-warning w-100 py-2">
                            <i class="fas fa-tint me-2"></i>Water Duty
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="admin.php?section=settlement" class="btn btn-danger w-100 py-2">
                            <i class="fas fa-calculator me-2"></i>Calculate Settlement
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <button class="btn btn-dark w-100 py-2" onclick="generateMonthlyReport()">
                            <i class="fas fa-file-pdf me-2"></i>Generate Report
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <button class="btn btn-outline-primary w-100 py-2" onclick="refreshDashboard()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initial load
        loadDashboardData();

        // Month selector change event
        document.getElementById('monthSelector').addEventListener('change', function () {
            // Re-load data based on selected month (assuming loadDashboardData handles the month parameter)
            loadDashboardData(this.value);
        });

        // Initialize with current month for selector (optional, based on your backend logic)
        // const currentMonth = document.getElementById('monthSelector').value;
        // loadDashboardData(currentMonth); 
    });

    /**
     * Loads dashboard data for a specific month.
     * @param {string} monthYear - The month and year in YYYY-MM format (e.g., '2025-11').
     */
    async function loadDashboardData(monthYear = document.getElementById('monthSelector').value) {
        // Update display to show which month's data is loading
        const selectedMonthText = document.getElementById('monthSelector').options[document.getElementById('monthSelector').selectedIndex].text;
        document.getElementById('currentMonthDisplay').textContent = selectedMonthText;
        
        try {
            // Assuming your makeRequest function sends the monthYear to the backend
            const result = await makeRequest('get_dashboard_data', { month: monthYear });
            
            if (result.status === 'success') {
                const data = result.data;

                // Update main stats
                document.getElementById('totalMembers').textContent = data.total_members || '0';
                document.getElementById('totalBazar').textContent = '৳' + parseFloat(data.total_bazar || 0).toFixed(2);
                document.getElementById('totalMeals').textContent = parseFloat(data.total_meals || 0).toFixed(2);
                document.getElementById('mealRate').textContent = '৳' + parseFloat(data.meal_rate || 0).toFixed(2);
                document.getElementById('monthlyFlatCost').textContent = '৳' + parseFloat(data.monthly_flat_cost || 0).toFixed(2);
                document.getElementById('perMemberCost').textContent = 'Per member: ৳' + parseFloat(data.per_member_cost || 0).toFixed(2);
                document.getElementById('pendingWaterDuties').textContent = data.pending_water_duties || '0';
                document.getElementById('totalBazarEntries').textContent = data.total_bazar_entries || '0';
                document.getElementById('avgMealsPerDay').textContent = parseFloat(data.avg_meals_per_day || 0).toFixed(1);

                // Update additional text fields
                document.getElementById('bazarEntries').textContent = (data.total_bazar_entries || '0') + ' entries';
                document.getElementById('avgMealsPerDayText').textContent = parseFloat(data.avg_meals_per_day || 0).toFixed(1) + ' per day';

                // Update monthly trends table (Note: This should be an aggregate of last 6 months, independent of monthSelector)
                // Assuming backend sends a separate list for this
                updateMonthlyTrendsTable(data.monthly_trends);

                // Load recent activities (Note: Should also be for the selected month or general recent activity)
                loadRecentActivities(monthYear);

            } else {
                showToast('Error loading dashboard data', 'error');
            }
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            showToast('Error loading dashboard data', 'error');
        }
    }

    function updateMonthlyTrendsTable(monthlyTrends) {
        const trendsTable = document.getElementById('monthlyTrendsTable');

        if (!monthlyTrends || monthlyTrends.length === 0) {
            trendsTable.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No trend data available</td></tr>';
            return;
        }

        trendsTable.innerHTML = monthlyTrends.map(trend => `
            <tr>
                <td class="fw-semibold">${trend.month}</td>
                <td class="text-end">
                    <span class="text-success fw-semibold">৳${parseFloat(trend.bazar || 0).toFixed(2)}</span>
                </td>
                <td class="text-end">
                    <span class="text-info fw-semibold">${parseFloat(trend.meals || 0).toFixed(2)}</span>
                </td>
                <td class="text-end">
                    <span class="text-secondary fw-semibold">৳${parseFloat(trend.flat_cost || 0).toFixed(2)}</span>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Loads recent activities.
     * @param {string} monthYear - The month and year in YYYY-MM format to filter activities (optional).
     */
    async function loadRecentActivities(monthYear = null) {
        try {
            // Pass the monthYear to the backend for optional filtering
            const result = await makeRequest('get_recent_activities', monthYear ? { month: monthYear } : {});
            
            const tbody = document.getElementById('recentActivities');
            if (result.status === 'success') {
                
                if (!result.data || result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No recent activities found</td></tr>';
                    return;
                }

                tbody.innerHTML = result.data.map(activity => {
                    let badgeClass = 'bg-primary';
                    let icon = 'fas fa-info-circle';
                    let typeDisplay = activity.type.charAt(0).toUpperCase() + activity.type.slice(1); // Capitalize

                    if (activity.type === 'bazar') {
                        badgeClass = 'bg-success';
                        icon = 'fas fa-shopping-cart';
                    } else if (activity.type === 'meal') {
                        badgeClass = 'bg-info';
                        icon = 'fas fa-utensils';
                    } else if (activity.type === 'member') {
                        badgeClass = 'bg-warning';
                        icon = 'fas fa-user-plus';
                        typeDisplay = 'New Member';
                    } else if (activity.type === 'flat') {
                        badgeClass = 'bg-secondary';
                        icon = 'fas fa-home';
                        typeDisplay = 'Flat Cost';
                    } else if (activity.type === 'settlement') {
                        badgeClass = 'bg-danger';
                        icon = 'fas fa-handshake';
                        typeDisplay = 'Settlement';
                    }

                    const amountDisplay = activity.amount ?
                        `<span class="text-success fw-semibold">৳${parseFloat(activity.amount).toFixed(2)}</span>` :
                        '<span class="text-muted">-</span>';

                    const memberInfo = activity.member_name ?
                        `<div class="small text-muted">${activity.member_name}</div>` : '';

                    return `
                        <tr>
                            <td>
                                <div class="small">${new Date(activity.date).toLocaleDateString()}</div>
                            </td>
                            <td>
                                <span class="badge ${badgeClass}">
                                    <i class="${icon} me-1"></i>${typeDisplay}
                                </span>
                            </td>
                            <td>
                                <div class="fw-medium">${activity.description}</div>
                                ${memberInfo}
                            </td>
                            <td class="text-end">
                                ${amountDisplay}
                            </td>
                        </tr>
                    `;
                }).join('');
            } else {
                showToast('Error loading recent activities', 'error');
            }
        } catch (error) {
            console.error('Error loading recent activities:', error);
            showToast('Error loading recent activities', 'error');
        }
    }

    function refreshDashboard() {
        // Show loading states
        document.getElementById('monthlyTrendsTable').innerHTML =
            '<tr><td colspan="4" class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm me-2"></div>Loading trends...</td></tr>';

        document.getElementById('recentActivities').innerHTML =
            '<tr><td colspan="4" class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm me-2"></div>Loading activities...</td></tr>';

        // Load data for the currently selected month
        const selectedMonth = document.getElementById('monthSelector').value;
        loadDashboardData(selectedMonth);
        showToast('Dashboard data refreshed', 'success');
    }

    function generateMonthlyReport() {
        showToast('Monthly report generation feature coming soon!', 'info');
    }

    /**
     * Placeholder for your actual AJAX/fetch function. 
     * Assumes it returns a promise that resolves to an object like: 
     * { status: 'success'/'error', data: {...} }
     */
    async function makeRequest(endpoint, params = {}) {
        // *** IMPORTANT: Replace with your actual backend API call (fetch, axios, jQuery.ajax) ***
        console.log(`Making request to: ${endpoint} with params:`, params);
        
        // --- Mock Data for Demonstration ---
        await new Promise(resolve => setTimeout(resolve, 800)); // Simulate network delay

        if (endpoint === 'get_dashboard_data') {
            const data = {
                current_month: document.getElementById('monthSelector').options[document.getElementById('monthSelector').selectedIndex].text,
                total_members: 5,
                total_bazar: 15450.75,
                total_meals: 450,
                meal_rate: 34.33,
                monthly_flat_cost: 7500.00,
                per_member_cost: 1500.00,
                pending_water_duties: 2,
                total_bazar_entries: 12,
                avg_meals_per_day: 15.0,
                monthly_trends: [
                    { month: 'Nov 2025', bazar: 15450.75, meals: 450, flat_cost: 7500.00 },
                    { month: 'Oct 2025', bazar: 18000.00, meals: 520, flat_cost: 7500.00 },
                    { month: 'Sep 2025', bazar: 16500.50, meals: 480, flat_cost: 7000.00 },
                ]
            };
            return { status: 'success', data: data };
        } else if (endpoint === 'get_recent_activities') {
            const data = [
                { date: '2025-11-24', type: 'bazar', description: 'Grocery shopping', amount: 1500.50, member_name: 'Mr. X' },
                { date: '2025-11-24', type: 'meal', description: 'Added 5 meals', amount: null, member_name: 'Mr. Y' },
                { date: '2025-11-23', type: 'flat', description: 'Rent paid for Nov', amount: 7500.00, member_name: 'Manager' },
                { date: '2025-11-22', type: 'member', description: 'New member joined', amount: null, member_name: 'Mr. Z' },
            ];
            return { status: 'success', data: data };
        }
        // --- End Mock Data ---
        
        return { status: 'error', message: 'Unknown endpoint' };
    }

    /**
     * Placeholder for a notification/toast function.
     * @param {string} message - The message to display.
     * @param {string} type - 'success', 'error', 'info'.
     */
    function showToast(message, type) {
        console.log(`[TOAST - ${type.toUpperCase()}] ${message}`);
        // In a real application, you'd use a library like Bootstrap Toast, SweetAlert, etc.
        // For simplicity, this is just a console log.
    }
</script>