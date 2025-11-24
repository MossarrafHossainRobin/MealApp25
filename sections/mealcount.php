<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2c5aa0;
            --primary-light: #e8f0fe;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --border: #dee2e6;
            --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            line-height: 1.5;
        }

        .app-container {
            max-width: 100%;
            padding: 15px;
        }

        /* Header Styles */
        .app-header {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        .app-title {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .app-subtitle {
            color: var(--secondary);
            font-size: 0.875rem;
        }

        .month-indicator {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
        }

        /* Control Panel */
        .control-panel {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        .control-panel .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .control-panel .form-select,
        .control-panel .form-control {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .control-panel .form-select:focus,
        .control-panel .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.1);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #244a8a;
            border-color: #244a8a;
            transform: translateY(-1px);
            box-shadow: var(--hover-shadow);
        }

        /* Stats Cards */
        .stats-card {
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            border: none;
            transition: all 0.3s;
            margin-bottom: 20px;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--hover-shadow);
        }

        .stats-card .card-body {
            padding: 20px;
        }

        .stats-card .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stats-card .card-text {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0;
        }

        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.2;
            position: absolute;
            right: 20px;
            bottom: 20px;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .table-responsive {
            max-height: 70vh;
            overflow: auto;
        }

        .table {
            margin-bottom: 0;
            font-size: 0.875rem;
        }

        .table thead th {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 8px;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table tbody td {
            padding: 12px 8px;
            vertical-align: middle;
            border-color: var(--border);
        }

        .name-col {
            background-color: #f0f4fc;
            font-weight: 600;
            position: sticky;
            left: 0;
            z-index: 5;
            min-width: 150px;
        }

        .total-col {
            background-color: #e9ecef;
            font-weight: 700;
            position: sticky;
            right: 0;
            z-index: 5;
            min-width: 80px;
        }

        .date-header {
            min-width: 70px;
            text-align: center;
        }

        .date-day {
            font-size: 1rem;
            font-weight: 700;
            display: block;
        }

        .date-weekday {
            font-size: 0.75rem;
            opacity: 0.9;
            display: block;
            margin-top: 2px;
        }

        .meal-cell {
            min-width: 70px;
            height: 50px;
            text-align: center;
            font-weight: 600;
            vertical-align: middle;
            transition: all 0.2s;
            cursor: pointer;
            position: relative;
        }

        .meal-cell:hover {
            background-color: var(--primary-light) !important;
        }

        /* Value-based cell coloring */
        .meal-cell.value-0 {
            background-color: #f8f9fa;
            color: var(--secondary);
        }

        .meal-cell.value-1 {
            background-color: #e8f5e8;
            color: #2d5a2d;
        }

        .meal-cell.value-2 {
            background-color: #e3f2fd;
            color: #0d47a1;
        }

        .meal-cell.value-3 {
            background-color: #fff3e0;
            color: #e65100;
        }

        .meal-cell.value-4 {
            background-color: #fce4ec;
            color: #880e4f;
        }

        .meal-cell.value-5-plus {
            background-color: #f3e5f5;
            color: #4a148c;
        }

        .meal-cell.weekend {
            opacity: 0.8;
        }

        .meal-cell.editing {
            background: white !important;
            border: 2px solid var(--primary) !important;
            z-index: 100;
            box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.2);
        }

        .meal-cell input {
            width: 100%;
            height: 100%;
            border: none;
            text-align: center;
            font-weight: bold;
            background: transparent;
            outline: none;
            font-size: inherit;
            color: inherit;
        }

        /* Alternating row colors */
        .member-row:nth-child(odd) .name-col {
            background-color: #f0f4fc;
        }

        .member-row:nth-child(even) .name-col {
            background-color: #e6ecf7;
        }

        .member-row:nth-child(odd) {
            background-color: #fafbfd;
        }

        .member-row:nth-child(even) {
            background-color: #f5f8fc;
        }

        .summary-row {
            background-color: #f8f9fa;
            font-weight: 700;
        }

        .summary-row .name-col {
            background-color: #e9ecef;
        }

        /* Legend */
        .legend-container {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: var(--card-shadow);
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
            margin-right: 5px;
            border: 1px solid var(--border);
        }

        /* Saving Indicator */
        .saving-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--success);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        /* Quick Actions */
        .quick-actions {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .floating-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .floating-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .app-container {
                padding: 10px;
            }

            .app-header {
                padding: 15px;
            }

            .app-title {
                font-size: 1.25rem;
            }

            .month-indicator {
                font-size: 0.9rem;
                padding: 8px 15px;
            }

            .control-panel {
                padding: 15px;
            }

            .stats-card .card-body {
                padding: 15px;
            }

            .stats-card .card-text {
                font-size: 1.5rem;
            }

            .table {
                font-size: 0.8rem;
            }

            .name-col {
                min-width: 120px;
            }

            .date-header {
                min-width: 60px;
            }

            .meal-cell {
                min-width: 60px;
                height: 45px;
            }

            .floating-btn {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
        }

        @media (max-width: 576px) {
            .app-title {
                font-size: 1.1rem;
            }

            .month-indicator {
                font-size: 0.85rem;
                padding: 6px 12px;
            }

            .name-col {
                min-width: 100px;
            }

            .date-header {
                min-width: 50px;
            }

            .meal-cell {
                min-width: 50px;
                height: 40px;
            }

            .date-day {
                font-size: 0.9rem;
            }

            .date-weekday {
                font-size: 0.7rem;
            }
        }

        /* Scrollbar Styling */
        .table-responsive::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body>
    <div class="saving-indicator" id="savingIndicator">
        <i class="bi bi-check-circle me-1"></i> Saved Successfully!
    </div>

    <div class="app-container">
        <div class="app-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="app-title">
                        <i class="bi bi-table me-2"></i>Meal Management
                    </h1>
                    <p class="app-subtitle">Click any cell to edit • Press Enter to save • Supports decimal values</p>
                </div>
                <div class="month-indicator">
                    <i class="bi bi-calendar3 me-2"></i>
                    <span id="currentMonthDisplay">Select Month</span>
                </div>
            </div>
        </div>

        <div class="control-panel">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">Select Reporting Period</label>
                    <div class="row g-2">
                        <div class="col-7">
                            <select id="filter_month" class="form-select">
                                <option value="">-- Select Month --</option>
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <div class="col-5">
                            <select id="filter_year" class="form-select">
                                <option value="2026">2026</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <button class="btn btn-primary w-100 fw-bold" onclick="loadMealSheet()">
                        <i class="bi bi-cloud-download me-2"></i>Load Data
                    </button>
                </div>

                <div class="col-lg-5 col-md-12">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-md-end gap-3 align-items-start align-items-md-center">
                        <div class="legend-container">
                            <span class="fw-bold me-2"><i
                                    class="bi bi-info-circle-fill text-primary me-1"></i>Legend:</span>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #f8f9fa;"></div>
                                    <span>0 Meals</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #e8f5e8;"></div>
                                    <span>1 Meal</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #e3f2fd;"></div>
                                    <span>2 Meals</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #fff3e0;"></div>
                                    <span>3 Meals</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #fce4ec;"></div>
                                    <span>4 Meals</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #f3e5f5;"></div>
                                    <span>5+ Meals</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4 g-3">
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card">
                    <div class="card-body position-relative">
                        <h5 class="card-title"><i class="bi bi-people me-2"></i>Total Members</h5>
                        <h2 id="totalMembers" class="card-text">0</h2>
                        <i class="bi bi-people-fill stats-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card">
                    <div class="card-body position-relative">
                        <h5 class="card-title"><i class="bi bi-egg-fried me-2"></i>Total Meals</h5>
                        <h2 id="totalMealsCard" class="card-text">0</h2>
                        <i class="bi bi-egg-fried stats-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card">
                    <div class="card-body position-relative">
                        <h5 class="card-title"><i class="bi bi-calendar-check me-2"></i>Days with Meals</h5>
                        <h2 id="daysWithMeals" class="card-text">0</h2>
                        <i class="bi bi-calendar-check stats-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card">
                    <div class="card-body position-relative">
                        <h5 class="card-title"><i class="bi bi-lightning me-2"></i>Avg per Day</h5>
                        <h2 id="avgPerDay" class="card-text">0</h2>
                        <i class="bi bi-lightning stats-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="excelMealTable">
                    <thead>
                        <tr>
                            <th class="name-col">Name</th>
                            <th class="total-col">Total</th>
                        </tr>
                    </thead>
                    <tbody id="excelMealBody">
                    </tbody>
                    <tfoot>
                        <tr class="summary-row">
                            <td class="name-col">Daily Total</td>
                            <td class="total-col" id="grandTotal">0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="quick-actions">
        <button class="btn floating-btn" title="Refresh Data" onclick="loadMealSheet()">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto" id="toastTitle">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Hello, world! This is a toast message.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // All JavaScript functionality remains exactly the same
        let members = [];
        let currentMonth = '';
        let currentYear = new Date().getFullYear().toString();
        let daysInMonth = [];
        let mealData = {};
        let currentEditingCell = null;

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function () {
            initializeApp();
        });

        async function initializeApp() {
            await loadMembers();
            setupEventListeners();
            setDefaultDate();
        }

        function setupEventListeners() {
            // Escape key to cancel editing
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && currentEditingCell) {
                    cancelEditing();
                }
            });
        }

        function setDefaultDate() {
            const now = new Date();
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            document.getElementById('filter_month').value = month;
            document.getElementById('filter_year').value = currentYear;
        }

        /* LOAD MEMBERS - ORIGINAL FETCH */
        async function loadMembers() {
            try {
                const response = await fetch("process/meal_actions.php", {
                    method: "POST",
                    body: new URLSearchParams({ action: "load_members" })
                });
                const data = await response.json();

                if (data.status === 'success') {
                    members = data.members;
                    updateMemberCount();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                showToast('Error', 'Failed to load members: ' + error.message, 'error');
            }
        }

        function updateMemberCount() {
            const totalMembersElement = document.getElementById('totalMembers');
            if (totalMembersElement) {
                totalMembersElement.textContent = members.length;
            }
        }

        /* LOAD MEAL SHEET - ORIGINAL FETCH */
        async function loadMealSheet() {
            const month = document.getElementById("filter_month").value;
            const year = document.getElementById("filter_year").value;

            if (!month) {
                showToast('Warning', 'Please select a month first!', 'warning');
                return;
            }

            currentMonth = month;
            currentYear = year;

            try {
                updateMonthDisplay(month, year);
                showToast('Loading', 'Loading meal data...', 'info');

                const response = await fetch("process/meal_actions.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: "load_meals_month",
                        month: month,
                        year: year
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    processMealData(data.meals);
                    renderExcelView();
                    showToast('Success', `Loaded data for ${getMonthName(month)} ${year}`, 'success');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                showToast('Error', 'Failed to load meal data: ' + error.message, 'error');
            }
        }

        function processMealData(meals) {
            // Initialize data structure
            mealData = {};
            members.forEach(member => {
                mealData[member.id] = {
                    name: member.name,
                    meals: {},
                    total: 0,
                    mealEntries: {} // Store actual meal entry IDs
                };
            });

            // Process meals
            let totalMeals = 0;
            let daysWithMeals = new Set();

            meals.forEach(meal => {
                const mealDate = new Date(meal.meal_date);
                const day = mealDate.getDate();

                if (!mealData[meal.member_id]) {
                    // Fallback in case member wasn't loaded (using real_name/member_name from meal record)
                    mealData[meal.member_id] = {
                        name: meal.real_name || meal.member_name || `Member ${meal.member_id}`,
                        meals: {},
                        total: 0,
                        mealEntries: {}
                    };
                }

                const mealCount = parseFloat(meal.meal_count) || 0;
                mealData[meal.member_id].meals[day] = mealCount;
                mealData[meal.member_id].mealEntries[day] = meal.id; // Store the meal ID
                mealData[meal.member_id].total += mealCount;

                totalMeals += mealCount;
                if (mealCount > 0) {
                    daysWithMeals.add(day);
                }
            });

            // Generate days array for the month
            generateDaysInMonth(parseInt(currentMonth), parseInt(currentYear));

            // Update statistics
            updateStatistics(totalMeals, daysWithMeals.size);
        }

        function generateDaysInMonth(month, year) {
            daysInMonth = [];
            const daysCount = new Date(year, month, 0).getDate();
            const today = new Date();

            for (let day = 1; day <= daysCount; day++) {
                const date = new Date(year, month - 1, day);
                daysInMonth.push({
                    day: day,
                    date: date,
                    isWeekend: date.getDay() === 0 || date.getDay() === 6,
                    isToday: false // Today indicator removed
                });
            }
        }

        function renderExcelView() {
            const tableHead = document.querySelector('#excelMealTable thead tr');
            const tableBody = document.querySelector('#excelMealTable tbody');
            const tableFoot = document.querySelector('#excelMealTable tfoot tr');

            if (!tableHead || !tableBody || !tableFoot) {
                console.error('Required table elements not found');
                return;
            }

            // Clear existing content (except first and last columns)
            // Keep the first (Name) and last (Total) column headers
            while (tableHead.children.length > 2) {
                tableHead.children[1].remove();
            }
            // Keep the first (Daily Total) and last (Grand Total) footer cells
            while (tableFoot.children.length > 2) {
                tableFoot.children[1].remove();
            }

            tableBody.innerHTML = '';

            const totalColHeader = tableHead.querySelector('.total-col');

            // Generate date headers with day and weekday
            daysInMonth.forEach(dayInfo => {
                const dateHeader = document.createElement('th');
                dateHeader.className = 'date-header';

                const dayNumber = document.createElement('span');
                dayNumber.className = 'date-day';
                dayNumber.textContent = dayInfo.day;

                const weekday = document.createElement('span');
                weekday.className = 'date-weekday';
                weekday.textContent = dayInfo.date.toLocaleDateString('en-US', { weekday: 'short' });

                dateHeader.appendChild(dayNumber);
                dateHeader.appendChild(weekday);

                dateHeader.title = dayInfo.date.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                tableHead.insertBefore(dateHeader, totalColHeader);

                const dayTotal = document.createElement('td');
                dayTotal.className = 'meal-cell fw-bold';
                dayTotal.textContent = '0';
                dayTotal.id = `day-total-${dayInfo.day}`;
                tableFoot.insertBefore(dayTotal, tableFoot.lastElementChild);
            });

            // Generate member rows
            let grandTotal = 0;
            const dailyTotals = Array(daysInMonth.length).fill(0);

            members.forEach((member) => {
                const row = document.createElement('tr');
                row.className = 'member-row';
                const memberData = mealData[member.id] || { meals: {}, total: 0, mealEntries: {} };

                // Member name column - FIRST COLUMN
                const nameCell = document.createElement('td');
                nameCell.className = 'name-col';
                nameCell.textContent = member.name;
                row.appendChild(nameCell);

                // Meal cells for each day - DATE COLUMNS
                let memberTotal = 0;

                daysInMonth.forEach((dayInfo, dayIndex) => {
                    const mealCell = document.createElement('td');
                    const mealCount = memberData.meals[dayInfo.day] || 0;

                    mealCell.className = 'meal-cell';
                    mealCell.textContent = formatMealValue(mealCount);
                    mealCell.setAttribute('data-member-id', member.id);
                    mealCell.setAttribute('data-day', dayInfo.day);
                    mealCell.setAttribute('data-date', dayInfo.date.toISOString().split('T')[0]);
                    mealCell.setAttribute('data-meal-id', memberData.mealEntries[dayInfo.day] || '');

                    // Store original value for cancel/revert
                    mealCell.setAttribute('data-original-value', formatMealValue(mealCount));

                    // Apply value-based coloring
                    applyValueBasedColoring(mealCell, mealCount);

                    if (dayInfo.isWeekend) {
                        mealCell.classList.add('weekend');
                    }

                    // Add click event for editing
                    mealCell.addEventListener('click', function () {
                        if (!currentEditingCell) {
                            startEditing(this);
                        }
                    });

                    row.appendChild(mealCell);

                    memberTotal += mealCount;
                    dailyTotals[dayIndex] += mealCount;
                });

                // Total column - LAST COLUMN
                const totalCell = document.createElement('td');
                totalCell.className = 'total-col';
                totalCell.textContent = formatMealValue(memberTotal);
                totalCell.id = `member-total-${member.id}`;
                row.appendChild(totalCell);

                grandTotal += memberTotal;
                tableBody.appendChild(row);
            });

            // Update daily totals and grand total
            dailyTotals.forEach((total, index) => {
                const dayTotalCell = document.getElementById(`day-total-${index + 1}`);
                if (dayTotalCell) {
                    dayTotalCell.textContent = formatMealValue(total);
                    // Apply value-based coloring to daily totals
                    applyValueBasedColoring(dayTotalCell, total);
                }
            });

            const grandTotalElement = document.getElementById('grandTotal');
            if (grandTotalElement) {
                grandTotalElement.textContent = formatMealValue(grandTotal);
            }
        }

        function applyValueBasedColoring(cell, value) {
            // Remove any existing value classes
            cell.classList.remove('value-0', 'value-1', 'value-2', 'value-3', 'value-4', 'value-5-plus');

            // Apply appropriate class based on value
            if (value === 0) {
                cell.classList.add('value-0');
            } else if (value === 1) {
                cell.classList.add('value-1');
            } else if (value === 2) {
                cell.classList.add('value-2');
            } else if (value === 3) {
                cell.classList.add('value-3');
            } else if (value === 4) {
                cell.classList.add('value-4');
            } else if (value >= 5) {
                cell.classList.add('value-5-plus');
            }
        }

        function formatMealValue(value) {
            if (value === 0) return '0';
            const num = parseFloat(value);
            if (Number.isInteger(num)) return num.toString();
            return num.toFixed(1);
        }

        function startEditing(cell) {
            if (currentEditingCell) {
                cancelEditing();
            }

            currentEditingCell = cell;
            cell.classList.add('editing');

            const currentValue = cell.textContent;
            const input = document.createElement('input');
            input.type = 'text';
            input.value = currentValue === '0' ? '' : currentValue; // Clear '0' for easier input
            input.style.fontSize = 'inherit';
            input.style.fontWeight = 'inherit';
            input.style.color = '#343a40'; // Ensure input text is dark

            // Set original value for cancel/blur check
            cell.setAttribute('data-original-value', currentValue);


            cell.innerHTML = '';
            cell.appendChild(input);
            input.focus();
            input.select();

            // Handle input events
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    saveCellValue(input.value.trim());
                } else if (e.key === 'Escape') {
                    cancelEditing();
                }
            });

            input.addEventListener('blur', function () {
                if (currentEditingCell === cell) {
                    const originalValue = cell.getAttribute('data-original-value');
                    const newValue = input.value.trim();
                    // Save if value changed, otherwise cancel to revert classes
                    if (newValue !== originalValue && !(newValue === '' && originalValue === '0')) {
                        saveCellValue(newValue);
                    } else {
                        cancelEditing();
                    }
                }
            });
        }

        function cancelEditing() {
            if (currentEditingCell) {
                const cell = currentEditingCell;
                const originalValue = cell.getAttribute('data-original-value') || '0';

                cell.textContent = originalValue;
                cell.classList.remove('editing');
                currentEditingCell = null;

                const mealCount = parseFloat(originalValue) || 0;
                // Reapply value-based coloring
                applyValueBasedColoring(cell, mealCount);
            }
        }

        /* SAVE CELL VALUE - ORIGINAL LOGIC RESTORED */
        async function saveCellValue(newValue) {
            if (!currentEditingCell) return;

            const cell = currentEditingCell;
            const memberId = cell.getAttribute('data-member-id');
            const day = parseInt(cell.getAttribute('data-day'));
            const date = cell.getAttribute('data-date');
            const mealId = cell.getAttribute('data-meal-id');
            const originalValue = cell.getAttribute('data-original-value');


            // Parse the value - allow floats and zero
            let mealCount = 0;
            if (newValue !== '' && !isNaN(newValue) && parseFloat(newValue) >= 0) {
                mealCount = parseFloat(newValue);
                if (mealCount < 0) mealCount = 0; // Prevent negative values
            }

            const formattedNewValue = formatMealValue(mealCount);

            // Revert to original if no actual change
            if (formattedNewValue === originalValue) {
                cell.classList.remove('editing');
                cell.textContent = originalValue;
                currentEditingCell = null;
                return;
            }


            // 1. Optimistic UI Update
            cell.classList.remove('editing');
            cell.textContent = formattedNewValue;

            cell.setAttribute('data-original-value', formattedNewValue);
            currentEditingCell = null; // Important: Clear editing state before fetch

            // Apply value-based coloring
            applyValueBasedColoring(cell, mealCount);

            // 2. Prepare Data and Call Backend
            const formData = {
                member_id: memberId,
                meal_count: mealCount,
                meal_date: date,
                description: 'Updated via Excel view'
            };

            let action = 'add_meal';
            if (mealId && mealId !== '') {
                action = 'update_meal';
                formData.id = mealId;
            }

            // Calculate old value from local data for total update
            const oldValue = mealData[memberId]?.meals[day] || 0;


            try {
                showSavingIndicator();

                const response = await fetch("process/meal_actions.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: action,
                        ...formData
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    // 3. Update Local Data Structure
                    if (!mealData[memberId]) {
                        // This case handles a member being loaded but not having mealData initialized
                        mealData[memberId] = { meals: {}, total: 0, mealEntries: {} };
                    }

                    mealData[memberId].meals[day] = mealCount;
                    cell.setAttribute('data-meal-id', result.id || mealId); // Update ID for new entry or keep existing
                    mealData[memberId].mealEntries[day] = result.id || mealId;

                    // Update totals based on the difference
                    const difference = mealCount - oldValue;
                    mealData[memberId].total += difference;


                    // 4. Update Totals in UI
                    updateMemberTotal(memberId);
                    updateDailyTotals();
                    updateOverallStatistics();

                    showToast('Success', 'Meal count updated successfully!', 'success');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast('Error', 'Failed to save: ' + error.message, 'error');
                // Revert cell value and local data on error
                cell.textContent = originalValue;
                cell.setAttribute('data-original-value', originalValue);

                // Revert value-based coloring
                const originalMealCount = parseFloat(originalValue) || 0;
                applyValueBasedColoring(cell, originalMealCount);

                // Re-calculate and update totals based on the error revert
                mealData[memberId].meals[day] = oldValue;
                const difference = originalMealCount - mealCount; // Revert difference
                mealData[memberId].total += difference;
                updateMemberTotal(memberId);
                updateDailyTotals();
                updateOverallStatistics();

            } finally {
                hideSavingIndicator();
            }
        }

        function updateMemberTotal(memberId) {
            const member = mealData[memberId];
            if (!member) return;

            let total = 0;
            // Recalculate total from meals array for robustness
            Object.values(member.meals).forEach(count => {
                total += count;
            });

            member.total = total;
            const totalCell = document.getElementById(`member-total-${memberId}`);
            if (totalCell) {
                totalCell.textContent = formatMealValue(total);
            }
        }

        function updateDailyTotals() {
            const dailyTotals = Array(daysInMonth.length).fill(0);

            // Calculate daily totals by iterating through all members' meal data
            members.forEach(member => {
                const memberData = mealData[member.id];
                if (memberData) {
                    Object.entries(memberData.meals).forEach(([day, count]) => {
                        const dayIndex = parseInt(day) - 1;
                        if (dayIndex >= 0 && dayIndex < dailyTotals.length) {
                            dailyTotals[dayIndex] += count;
                        }
                    });
                }
            });

            // Update UI for daily totals
            dailyTotals.forEach((total, index) => {
                const dayTotalCell = document.getElementById(`day-total-${index + 1}`);
                if (dayTotalCell) {
                    dayTotalCell.textContent = formatMealValue(total);
                    // Apply value-based coloring to daily totals
                    applyValueBasedColoring(dayTotalCell, total);
                }
            });

            // Update grand total
            const grandTotal = dailyTotals.reduce((sum, total) => sum + total, 0);
            const grandTotalElement = document.getElementById('grandTotal');
            if (grandTotalElement) {
                grandTotalElement.textContent = formatMealValue(grandTotal);
            }
        }

        function updateOverallStatistics() {
            let totalMeals = 0;
            let daysWithMeals = new Set();

            members.forEach(member => {
                const memberData = mealData[member.id];
                if (memberData) {
                    Object.entries(memberData.meals).forEach(([day, count]) => {
                        totalMeals += count;
                        if (count > 0) {
                            daysWithMeals.add(day);
                        }
                    });
                }
            });

            updateStatistics(totalMeals, daysWithMeals.size);
        }

        function showSavingIndicator() {
            const indicator = document.getElementById('savingIndicator');
            if (indicator) {
                indicator.textContent = 'Saving...';
                indicator.classList.remove('bg-success');
                indicator.classList.add('bg-warning');
                indicator.style.display = 'block';
            }
        }

        function hideSavingIndicator() {
            const indicator = document.getElementById('savingIndicator');
            if (indicator) {
                indicator.textContent = 'Saved Successfully!';
                indicator.classList.remove('bg-warning');
                indicator.classList.add('bg-success');
                setTimeout(() => {
                    indicator.style.display = 'none';
                }, 2000);
            }
        }

        /* UPDATE STATISTICS */
        function updateStatistics(totalMeals, daysCount) {
            const elements = {
                'totalMealsCard': formatMealValue(totalMeals),
                'daysWithMeals': daysCount,
                // Avg per day is calculated against the number of days in the month, not daysCount (days with meals)
                'avgPerDay': daysInMonth.length > 0 ? formatMealValue(totalMeals / daysInMonth.length) : '0'
            };

            Object.entries(elements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value;
                }
            });
        }

        /* UPDATE MONTH DISPLAY */
        function updateMonthDisplay(month, year) {
            const monthName = getMonthName(month);
            const monthDisplay = document.getElementById('currentMonthDisplay');
            if (monthDisplay) {
                monthDisplay.textContent = `${monthName} ${year}`;
            }
        }

        function getMonthName(month) {
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];
            return monthNames[parseInt(month) - 1] || 'Unknown Month';
        }

        /* TOAST NOTIFICATIONS */
        function showToast(title, message, type = 'info') {
            const toastEl = document.getElementById('liveToast');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');

            if (!toastEl || !toastTitle || !toastMessage) {
                console.error('Toast elements not found');
                return;
            }

            const typeClasses = {
                success: 'text-bg-success',
                error: 'text-bg-danger',
                warning: 'text-bg-warning',
                info: 'text-bg-primary'
            };

            toastEl.className = `toast ${typeClasses[type] || 'text-bg-info'}`;
            toastTitle.textContent = title;
            toastMessage.textContent = message;

            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // Auto-load current month on page load
        window.addEventListener('load', function () {
            setTimeout(() => {
                const filterMonth = document.getElementById('filter_month');
                if (filterMonth && filterMonth.value) {
                    loadMealSheet();
                }
            }, 1000);
        });
    </script>
</body>

</html>