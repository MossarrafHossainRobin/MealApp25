<div class="container-fluid px-0">
    <!-- Enhanced Header with History Controls -->
    <div class="glass-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <h1 class="display-6 fw-bold text-gradient mb-1">Meal Analytics Dashboard</h1>
                        <p class="text-muted mb-0" id="current-month-display">
                            Advanced meal tracking & analytics 
                            <span class="badge bg-info ms-2" id="history-status">Ready</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-3 justify-content-end flex-wrap">
                    <!-- History Controls -->
                    <div class="btn-group btn-group-sm me-2" role="group">
                        <button class="btn btn-outline-secondary" onclick="undoAction()" id="undo-btn" disabled>
                            <i class="bi bi-arrow-counterclockwise"></i> Undo
                        </button>
                        <button class="btn btn-outline-secondary" onclick="redoAction()" id="redo-btn" disabled>
                            <i class="bi bi-arrow-clockwise"></i> Redo
                        </button>
                    </div>
                    
                    <!-- Date Controls -->
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 140px;">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="bi bi-calendar3"></i>
                            </span>
                            <select name="year" id="yearSelect" class="form-select border-start-0">
                                <?php
                                $current_year = date('Y');
                                $selected_year = $_GET['year'] ?? $current_year;
                                for ($y = $current_year; $y >= 2020; $y--): ?>
                                        <option value="<?php echo $y; ?>" <?php echo $y == $selected_year ? 'selected' : ''; ?>>
                                            <?php echo $y; ?>
                                        </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="input-group input-group-sm" style="width: 160px;">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="bi bi-filter"></i>
                            </span>
                            <select name="month" id="monthSelect" class="form-select border-start-0">
                                <?php
                                $months = [
                                    '01' => 'January',
                                    '02' => 'February',
                                    '03' => 'March',
                                    '04' => 'April',
                                    '05' => 'May',
                                    '06' => 'June',
                                    '07' => 'July',
                                    '08' => 'August',
                                    '09' => 'September',
                                    '10' => 'October',
                                    '11' => 'November',
                                    '12' => 'December'
                                ];
                                $selected_month = $_GET['month'] ?? date('m');
                                foreach ($months as $num => $name): ?>
                                        <option value="<?php echo $num; ?>" <?php echo $num == $selected_month ? 'selected' : ''; ?>>
                                            <?php echo $name; ?>
                                        </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="button" class="btn btn-primary btn-sm px-3" onclick="loadMonthData()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Load
                        </button>
                    </div>
                    
                    <!-- View Toggle -->
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="viewMode" id="gridView" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="gridView" onclick="switchView('grid')">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </label>
                        
                        <input type="radio" class="btn-check" name="viewMode" id="tableView" autocomplete="off">
                        <label class="btn btn-outline-primary" for="tableView" onclick="switchView('table')">
                            <i class="bi bi-table"></i>
                        </label>
                        
                        <input type="radio" class="btn-check" name="viewMode" id="analyticsView" autocomplete="off">
                        <label class="btn btn-outline-primary" for="analyticsView" onclick="switchView('analytics')">
                            <i class="bi bi-bar-chart"></i>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- History Timeline -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="history-timeline">
                    <div class="history-track">
                        <div class="history-progress" id="history-progress"></div>
                        <div class="history-points" id="history-points"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted" id="history-info">No actions recorded</small>
                        <small class="text-muted" id="history-count">History: 0/50</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced KPI Dashboard -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card primary">
                <div class="kpi-icon">
                    <i class="bi bi-egg-fried"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="stats-total-meals">0</div>
                    <div class="kpi-label">Total Meals</div>
                    <div class="kpi-trend" id="meal-trend">0% vs prev</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card success">
                <div class="kpi-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="stats-avg-cost">৳0.00</div>
                    <div class="kpi-label">Avg Meal Cost</div>
                    <div class="kpi-trend" id="cost-trend">0% vs prev</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card info">
                <div class="kpi-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="stats-total-members">0</div>
                    <div class="kpi-label">Active Members</div>
                    <div class="kpi-trend" id="member-trend">100% active</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card warning">
                <div class="kpi-icon">
                    <i class="bi bi-cart"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="stats-total-bazar">৳0.00</div>
                    <div class="kpi-label">Total Bazar</div>
                    <div class="kpi-trend" id="bazar-trend">0% vs prev</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Control Panel -->
    <div class="glass-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-primary" onclick="showQuickFillModal()">
                        <i class="bi bi-lightning"></i> Quick Fill
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="applyPattern('breakfast')">
                        <i class="bi bi-sun"></i> Breakfast Pattern
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="applyPattern('lunch')">
                        <i class="bi bi-sun-high"></i> Lunch Pattern
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="applyPattern('dinner')">
                        <i class="bi bi-moon"></i> Dinner Pattern
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="showAdvancedSettings()">
                        <i class="bi bi-gear"></i> Settings
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2 justify-content-end">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <span class="input-group-text bg-transparent">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search meals..." id="mealSearch">
                    </div>
                    <button class="btn btn-success btn-sm px-3" onclick="saveAllMeals()">
                        <i class="bi bi-cloud-arrow-up"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Multi-View Container -->
    <div id="view-container">
        <!-- Grid View -->
        <div id="grid-view" class="view-content active">
            <div class="row g-3" id="grid-container">
                <!-- Grid cards will be loaded here -->
            </div>
        </div>

        <!-- Table View -->
        <div id="table-view" class="view-content">
            <div class="glass-card">
                <div class="table-responsive" id="table-container">
                    <!-- Table will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Analytics View -->
        <div id="analytics-view" class="view-content">
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="glass-card">
                        <div class="card-header">
                            <h6 class="mb-0">Meal Distribution Analytics</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="mealChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="glass-card">
                        <div class="card-header">
                            <h6 class="mb-0">Member Performance</h6>
                        </div>
                        <div class="card-body">
                            <div id="performance-container">
                                <!-- Performance metrics will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-actions">
        <button class="fab-main" onclick="toggleFAB()">
            <i class="bi bi-plus"></i>
        </button>
        <div class="fab-menu">
            <button class="fab-item" onclick="quickFill(1)" title="Fill with 1">
                <i class="bi bi-1-circle"></i>
            </button>
            <button class="fab-item" onclick="quickFill(2)" title="Fill with 2">
                <i class="bi bi-2-circle"></i>
            </button>
            <button class="fab-item" onclick="clearAllMeals()" title="Clear All">
                <i class="bi bi-x-circle"></i>
            </button>
            <button class="fab-item" onclick="saveAllMeals()" title="Save All">
                <i class="bi bi-check-circle"></i>
            </button>
        </div>
    </div>
</div>

<!-- Quick Fill Modal -->
<div class="modal fade" id="quickFillModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h6 class="modal-title">Quick Fill Options</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn btn-outline-primary w-100" onclick="fillAllWithValue(1)">Fill: 1</button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-outline-success w-100" onclick="fillAllWithValue(2)">Fill: 2</button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-outline-warning w-100" onclick="fillWeekendsWithValue(1)">Weekends: 1</button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-outline-info w-100" onclick="fillWeekdaysWithValue(2)">Weekdays: 2</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Advanced Glass Morphism Design */
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 2px 8px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.6);
    padding: 1.5rem;
    margin-bottom: 1rem;
}

/* KPI Cards */
.kpi-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    padding: 1.25rem;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
}

.kpi-card.primary {
    --gradient-start: #4f46e5;
    --gradient-end: #7c3aed;
}

.kpi-card.success {
    --gradient-start: #059669;
    --gradient-end: #10b981;
}

.kpi-card.info {
    --gradient-start: #0369a1;
    --gradient-end: #0ea5e9;
}

.kpi-card.warning {
    --gradient-start: #d97706;
    --gradient-end: #f59e0b;
}

.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.kpi-icon {
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
    opacity: 0.8;
}

.kpi-value {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.kpi-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.kpi-trend {
    font-size: 0.75rem;
    font-weight: 600;
}

/* Header Icon */
.header-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
    font-size: 1.5rem;
}

/* Text Gradient */
.text-gradient {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* View Management */
.view-content {
    display: none;
}

.view-content.active {
    display: block;
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Grid View Styles */
.meal-grid-card {
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    padding: 1rem;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.meal-grid-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.meal-input-advanced {
    width: 60px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
    transition: all 0.2s ease;
    background: rgba(255, 255, 255, 0.9);
}

.meal-input-advanced:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    background: white;
}

/* Floating Action Button */
.floating-actions {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 1000;
}

.fab-main {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 8px 24px rgba(79, 70, 229, 0.3);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.fab-main:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 32px rgba(79, 70, 229, 0.4);
}

.fab-menu {
    position: absolute;
    bottom: 70px;
    right: 0;
    display: none;
    flex-direction: column;
    gap: 0.5rem;
}

.fab-menu.show {
    display: flex;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fab-item {
    width: 44px;
    height: 44px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 50%;
    color: #4f46e5;
    font-size: 1.25rem;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.fab-item:hover {
    background: #4f46e5;
    color: white;
    transform: scale(1.1);
}

/* History Timeline Styles */
.history-timeline {
    background: rgba(255, 255, 255, 0.5);
    border-radius: 10px;
    padding: 1rem;
    backdrop-filter: blur(10px);
}

.history-track {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    position: relative;
    margin: 0.5rem 0;
}

.history-progress {
    height: 100%;
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
    border-radius: 3px;
    width: 0%;
    transition: width 0.3s ease;
}

.history-points {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    display: flex;
    justify-content: space-between;
}

.history-point {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #9ca3af;
    border: 2px solid white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.history-point.active {
    background: #4f46e5;
    transform: scale(1.2);
}

.history-point.past {
    background: #10b981;
}

/* Keyboard Shortcut Hints */
.keyboard-shortcut {
    font-size: 0.75rem;
    color: #6b7280;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    padding: 0.125rem 0.375rem;
    background: rgba(255, 255, 255, 0.5);
}

/* Enhanced button states */
.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Animation for history actions */
@keyframes historyFlash {
    0% { background-color: rgba(79, 70, 229, 0.1); }
    100% { background-color: transparent; }
}

.history-flash {
    animation: historyFlash 0.6s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .glass-card {
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .kpi-card {
        padding: 1rem;
    }
    
    .kpi-value {
        font-size: 1.5rem;
    }
    
    .header-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
    
    .floating-actions {
        bottom: 1rem;
        right: 1rem;
    }
    
    .meal-input-advanced {
        width: 50px;
        font-size: 0.8rem;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .glass-card {
        background: rgba(30, 30, 30, 0.8);
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    .kpi-card {
        background: rgba(40, 40, 40, 0.8);
    }
}
</style>

<script>
// Enhanced History Management System
class MealHistory {
    constructor(maxSize = 50) {
        this.history = [];
        this.currentIndex = -1;
        this.maxSize = maxSize;
        this.batchTimeout = null;
        this.batchActions = [];
    }

    // Add action to history
    addAction(action) {
        // Clear redo history if we're not at the end
        if (this.currentIndex < this.history.length - 1) {
            this.history = this.history.slice(0, this.currentIndex + 1);
        }

        // Add to batch for grouped actions
        this.batchActions.push(action);
        
        clearTimeout(this.batchTimeout);
        this.batchTimeout = setTimeout(() => {
            this.commitBatch();
        }, 1000);
    }

    // Commit batched actions
    commitBatch() {
        if (this.batchActions.length === 0) return;

        const batch = {
            type: 'batch',
            actions: [...this.batchActions],
            timestamp: new Date(),
            description: this.batchActions.length === 1 ? 
                `${this.batchActions[0].memberName}: ${this.batchActions[0].oldValue} → ${this.batchActions[0].newValue}` :
                `${this.batchActions.length} changes`
        };

        this.history.push(batch);
        
        // Limit history size
        if (this.history.length > this.maxSize) {
            this.history.shift();
        }

        this.currentIndex = this.history.length - 1;
        this.batchActions = [];
        this.updateUI();
    }

    // Undo last action
    undo() {
        if (this.currentIndex < 0) return null;

        const action = this.history[this.currentIndex];
        this.currentIndex--;
        this.updateUI();
        return action;
    }

    // Redo next action
    redo() {
        if (this.currentIndex >= this.history.length - 1) return null;

        this.currentIndex++;
        const action = this.history[this.currentIndex];
        this.updateUI();
        return action;
    }

    // Clear history
    clear() {
        this.history = [];
        this.currentIndex = -1;
        this.batchActions = [];
        this.updateUI();
    }

    // Update UI elements
    updateUI() {
        const undoBtn = document.getElementById('undo-btn');
        const redoBtn = document.getElementById('redo-btn');
        const historyStatus = document.getElementById('history-status');
        const historyInfo = document.getElementById('history-info');
        const historyCount = document.getElementById('history-count');
        const historyProgress = document.getElementById('history-progress');
        const historyPoints = document.getElementById('history-points');

        // Update button states
        undoBtn.disabled = this.currentIndex < 0;
        redoBtn.disabled = this.currentIndex >= this.history.length - 1;

        // Update status
        const canUndo = this.currentIndex >= 0;
        const canRedo = this.currentIndex < this.history.length - 1;
        
        if (canUndo || canRedo) {
            historyStatus.textContent = `History: ${this.currentIndex + 1}/${this.history.length}`;
            historyStatus.className = 'badge bg-warning ms-2';
        } else {
            historyStatus.textContent = 'Ready';
            historyStatus.className = 'badge bg-info ms-2';
        }

        // Update progress
        const progress = this.history.length > 0 ? 
            ((this.currentIndex + 1) / this.history.length) * 100 : 0;
        historyProgress.style.width = `${progress}%`;

        // Update info
        if (this.history.length === 0) {
            historyInfo.textContent = 'No actions recorded';
        } else {
            const lastAction = this.history[this.currentIndex];
            historyInfo.textContent = `Last: ${lastAction.description}`;
        }

        historyCount.textContent = `History: ${this.history.length}/${this.maxSize}`;

        // Update timeline points
        this.updateTimelinePoints();
    }

    // Update timeline visualization
    updateTimelinePoints() {
        const historyPoints = document.getElementById('history-points');
        const pointsCount = Math.min(10, this.history.length);
        
        let pointsHTML = '';
        for (let i = 0; i < pointsCount; i++) {
            let pointClass = 'history-point';
            if (i === this.currentIndex) {
                pointClass += ' active';
            } else if (i < this.currentIndex) {
                pointClass += ' past';
            }
            
            pointsHTML += `<div class="${pointClass}" onclick="jumpToHistory(${i})" 
                            title="${this.history[i]?.description || 'Action ' + (i + 1)}"></div>`;
        }
        
        historyPoints.innerHTML = pointsHTML;
    }

    // Get current state info
    getState() {
        return {
            canUndo: this.currentIndex >= 0,
            canRedo: this.currentIndex < this.history.length - 1,
            historySize: this.history.length,
            currentPosition: this.currentIndex + 1
        };
    }
}

// Global variables
let currentMonthData = {};
let currentView = 'grid';
let autoSaveTimeout = null;
let mealChart = null;
const mealHistory = new MealHistory(50);

// Enhanced meal update with history tracking
function updateMeal(input) {
    const memberId = input.dataset.member;
    const date = input.dataset.date;
    const newValue = parseFloat(input.value) || 0;
    const oldValue = parseFloat(input.getAttribute('data-old-value') || input.value) || 0;
    
    // Store old value for history
    if (!input.hasAttribute('data-old-value')) {
        input.setAttribute('data-old-value', oldValue);
    }

    // Only record if value actually changed
    if (newValue !== oldValue) {
        const memberName = getMemberName(memberId);
        
        // Create history action
        const action = {
            type: 'meal_update',
            memberId: memberId,
            memberName: memberName,
            date: date,
            oldValue: oldValue,
            newValue: newValue,
            input: input,
            timestamp: new Date()
        };

        // Add to history
        mealHistory.addAction(action);

        // Update old value reference
        input.setAttribute('data-old-value', newValue);
    }

    // Update local data
    if (!currentMonthData.meals[memberId]) {
        currentMonthData.meals[memberId] = {};
    }
    currentMonthData.meals[memberId][date] = newValue;

    // Update all calculations
    updateAllCalculations();

    // Auto-save
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        saveMeal(memberId, date, newValue);
    }, 1000);

    console.log('📊 Meal updated:', { memberId, date, newValue, oldValue });
}

// Undo functionality
function undoAction() {
    const action = mealHistory.undo();
    if (!action) return;

    if (action.type === 'batch') {
        // Reverse all actions in the batch
        action.actions.reverse().forEach(singleAction => {
            revertMealChange(singleAction);
        });
    } else {
        revertMealChange(action);
    }

    showNotification(`↩️ Undo: ${action.description}`, 'info');
    flashHistoryEffect();
}

// Redo functionality
function redoAction() {
    const action = mealHistory.redo();
    if (!action) return;

    if (action.type === 'batch') {
        // Re-apply all actions in the batch
        action.actions.forEach(singleAction => {
            applyMealChange(singleAction);
        });
    } else {
        applyMealChange(action);
    }

    showNotification(`↪️ Redo: ${action.description}`, 'info');
    flashHistoryEffect();
}

// Revert a meal change (for undo)
function revertMealChange(action) {
    const input = findMealInput(action.memberId, action.date);
    if (input) {
        input.value = action.oldValue;
        input.setAttribute('data-old-value', action.oldValue);
        
        // Update local data
        if (!currentMonthData.meals[action.memberId]) {
            currentMonthData.meals[action.memberId] = {};
        }
        currentMonthData.meals[action.memberId][action.date] = action.oldValue;
        
        // Flash effect
        input.classList.add('history-flash');
        setTimeout(() => input.classList.remove('history-flash'), 600);
    }
    
    updateAllCalculations();
}

// Apply a meal change (for redo)
function applyMealChange(action) {
    const input = findMealInput(action.memberId, action.date);
    if (input) {
        input.value = action.newValue;
        input.setAttribute('data-old-value', action.newValue);
        
        // Update local data
        if (!currentMonthData.meals[action.memberId]) {
            currentMonthData.meals[action.memberId] = {};
        }
        currentMonthData.meals[action.memberId][action.date] = action.newValue;
        
        // Flash effect
        input.classList.add('history-flash');
        setTimeout(() => input.classList.remove('history-flash'), 600);
    }
    
    updateAllCalculations();
}

// Find meal input element
function findMealInput(memberId, date) {
    const selector = `input[data-member="${memberId}"][data-date="${date}"]`;
    return document.querySelector(selector);
}

// Get member name from ID
function getMemberName(memberId) {
    if (!currentMonthData.members) return 'Unknown';
    const member = currentMonthData.members.find(m => m.id == memberId);
    return member ? member.name : 'Unknown';
}

// Jump to specific history point
function jumpToHistory(index) {
    const currentIndex = mealHistory.currentIndex;
    
    if (index < currentIndex) {
        // Need to undo to reach this point
        const steps = currentIndex - index;
        for (let i = 0; i < steps; i++) {
            undoAction();
        }
    } else if (index > currentIndex) {
        // Need to redo to reach this point
        const steps = index - currentIndex;
        for (let i = 0; i < steps; i++) {
            redoAction();
        }
    }
}

// Flash effect for history actions
function flashHistoryEffect() {
    const container = document.getElementById('view-container');
    container.classList.add('history-flash');
    setTimeout(() => container.classList.remove('history-flash'), 300);
}

// Keyboard shortcut handler
function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl+Z or Cmd+Z for Undo
        if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
            e.preventDefault();
            if (mealHistory.getState().canUndo) {
                undoAction();
            }
        }
        
        // Ctrl+Y or Ctrl+Shift+Z for Redo
        if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) {
            e.preventDefault();
            if (mealHistory.getState().canRedo) {
                redoAction();
            }
        }
        
        // Escape to clear selection
        if (e.key === 'Escape') {
            document.activeElement.blur();
        }
    });

    // Show keyboard shortcuts help
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === '/') {
            e.preventDefault();
            showKeyboardShortcuts();
        }
    });
}

// Show keyboard shortcuts modal
function showKeyboardShortcuts() {
    const shortcuts = [
        { key: 'Ctrl+Z', action: 'Undo last action' },
        { key: 'Ctrl+Y', action: 'Redo last action' },
        { key: 'Ctrl+Shift+Z', action: 'Redo last action' },
        { key: 'Escape', action: 'Clear input focus' },
        { key: 'Ctrl+/', action: 'Show this help' }
    ];

    let helpHTML = `
        <div class="glass-card">
            <div class="card-header">
                <h6 class="mb-0">Keyboard Shortcuts</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
    `;

    shortcuts.forEach(shortcut => {
        helpHTML += `
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <kbd class="keyboard-shortcut">${shortcut.key}</kbd>
                    <span class="text-muted">${shortcut.action}</span>
                </div>
            </div>
        `;
    });

    helpHTML += `
                </div>
            </div>
        </div>
    `;

    // Create and show modal
    const modal = document.createElement('div');
    modal.className = 'modal fade show d-block';
    modal.style.background = 'rgba(0,0,0,0.5)';
    modal.innerHTML = `
        <div class="modal-dialog modal-sm">
            ${helpHTML}
            <div class="text-center mt-3">
                <button class="btn btn-sm btn-primary" onclick="this.closest('.modal').remove()">Close</button>
            </div>
        </div>
    `;

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });

    document.body.appendChild(modal);
}

// View management
function switchView(view) {
    currentView = view;
    
    // Hide all views
    document.querySelectorAll('.view-content').forEach(view => {
        view.classList.remove('active');
    });
    
    // Show selected view
    document.getElementById(`${view}-view`).classList.add('active');
    
    // Re-render based on view
    if (currentMonthData.members) {
        switch(view) {
            case 'grid':
                renderGridView(currentMonthData);
                break;
            case 'table':
                renderTableView(currentMonthData);
                break;
            case 'analytics':
                renderAnalyticsView(currentMonthData);
                break;
        }
    }
}

// Enhanced load function
function loadMonthData() {
    const year = document.getElementById('yearSelect').value;
    const month = document.getElementById('monthSelect').value;
    
    showLoading();
    
    fetch(`ajax/load_meals.php?year=${year}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentMonthData = data;
                updateAllViews(data);
                console.log('🚀 Advanced meal data loaded:', data);
            } else {
                showNotification('Error loading data: ' + data.error, 'error');
            }
            hideLoading();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading meal data', 'error');
            hideLoading();
        });
}

// Update all views
function updateAllViews(data) {
    updateStats(data);
    renderGridView(data);
    renderTableView(data);
    renderAnalyticsView(data);
}

// Enhanced grid view renderer
function renderGridView(data) {
    const container = document.getElementById('grid-container');
    
    let html = '';
    
    data.members.forEach((member, memberIndex) => {
        let memberTotal = 0;
        const weeks = chunkArray(data.dates, 7);
        
        html += `
            <div class="col-12 col-md-6 col-xl-4">
                <div class="meal-grid-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-bold">${member.name}</h6>
                        <span class="badge bg-primary" id="grid-member-total-${member.id}">0</span>
                    </div>
                    <div class="row g-1">`;
        
        weeks.forEach((week, weekIndex) => {
            html += `<div class="col-12">`;
            html += `<div class="row g-1 mb-2">`;
            
            week.forEach(date => {
                const dateObj = new Date(date);
                const meal_count = data.meals[member.id]?.[date] || 0;
                memberTotal += parseFloat(meal_count);
                const is_weekend = (dateObj.getDay() === 0 || dateObj.getDay() === 6);
                const dayClass = is_weekend ? 'bg-warning bg-opacity-25' : '';
                
                html += `
                    <div class="col">
                        <div class="text-center ${dayClass} p-1 rounded">
                            <div class="small text-muted">${dateObj.toLocaleDateString('en', {weekday: 'narrow'})}</div>
                            <div class="small fw-bold mb-1">${dateObj.getDate()}</div>
                            <input type="number" 
                                   data-member="${member.id}" 
                                   data-date="${date}"
                                   class="form-control form-control-sm meal-input-advanced" 
                                   value="${meal_count}"
                                   min="0" 
                                   max="10"
                                   step="0.5"
                                   onchange="updateMeal(this)"
                                   onfocus="this.select()">
                        </div>
                    </div>`;
            });
            
            html += `</div></div>`;
        });
        
        html += `
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <small class="text-muted">Avg: <strong>${(memberTotal / data.dates.length).toFixed(1)}</strong> meals/day</small>
                    </div>
                </div>
            </div>`;
    });
    
    container.innerHTML = html;
    updateGridMemberTotals();
}

// Enhanced table view renderer
function renderTableView(data) {
    const container = document.getElementById('table-container');
    
    let html = `
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="fw-semibold">Date</th>`;
    
    data.members.forEach(member => {
        html += `<th class="text-center fw-semibold">${member.name}</th>`;
    });
    
    html += `
                    <th class="text-center fw-semibold">Daily Total</th>
                </tr>
            </thead>
            <tbody>`;
    
    data.dates.forEach(date => {
        const dateObj = new Date(date);
        const is_weekend = (dateObj.getDay() === 0 || dateObj.getDay() === 6);
        const rowClass = is_weekend ? 'table-warning' : '';
        let dailyTotal = 0;
        
        html += `<tr class="${rowClass}">`;
        html += `<td class="fw-semibold">
                    <div class="small">${dateObj.toLocaleDateString('en', {weekday: 'short'})}</div>
                    <div class="fw-bold">${dateObj.getDate()}</div>
                 </td>`;
        
        data.members.forEach(member => {
            const meal_count = data.meals[member.id]?.[date] || 0;
            dailyTotal += parseFloat(meal_count);
            
            html += `
                <td class="text-center">
                    <input type="number" 
                           data-member="${member.id}" 
                           data-date="${date}"
                           class="form-control form-control-sm meal-input-advanced" 
                           value="${meal_count}"
                           min="0" 
                           max="10"
                           step="0.5"
                           style="width: 70px; margin: 0 auto;"
                           onchange="updateMeal(this)"
                           onfocus="this.select()">
                </td>`;
        });
        
        html += `<td class="text-center fw-bold">${dailyTotal.toFixed(1)}</td>`;
        html += `</tr>`;
    });
    
    html += `</tbody></table>`;
    container.innerHTML = html;
}

// Analytics view renderer
function renderAnalyticsView(data) {
    renderMealChart(data);
    renderPerformanceMetrics(data);
}

// Chart.js implementation
function renderMealChart(data) {
    const ctx = document.getElementById('mealChart').getContext('2d');
    
    if (mealChart) {
        mealChart.destroy();
    }
    
    const memberTotals = data.members.map(member => {
        let total = 0;
        data.dates.forEach(date => {
            total += parseFloat(data.meals[member.id]?.[date] || 0);
        });
        return total;
    });
    
    mealChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.members.map(m => m.name),
            datasets: [{
                label: 'Total Meals',
                data: memberTotals,
                backgroundColor: 'rgba(79, 70, 229, 0.6)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Meal Distribution by Member'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Performance metrics
function renderPerformanceMetrics(data) {
    const container = document.getElementById('performance-container');
    
    let html = '';
    
    data.members.forEach((member, index) => {
        let total = 0;
        let daysWithMeals = 0;
        
        data.dates.forEach(date => {
            const count = parseFloat(data.meals[member.id]?.[date] || 0);
            total += count;
            if (count > 0) daysWithMeals++;
        });
        
        const avgPerDay = (total / data.dates.length).toFixed(1);
        const percentage = data.total_meals > 0 ? ((total / data.total_meals) * 100).toFixed(1) : '0';
        const efficiency = (daysWithMeals / data.dates.length * 100).toFixed(1);
        
        html += `
            <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                <div>
                    <div class="fw-bold">${member.name}</div>
                    <small class="text-muted">${avgPerDay} avg/day</small>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-primary">${total.toFixed(1)}</div>
                    <small class="text-muted">${efficiency}% active</small>
                </div>
            </div>`;
    });
    
    container.innerHTML = html;
}

// Enhanced calculations
function updateAllCalculations() {
    updateGridMemberTotals();
    updateStats(currentMonthData);
    
    if (currentView === 'analytics') {
        renderAnalyticsView(currentMonthData);
    }
}

// Update grid member totals
function updateGridMemberTotals() {
    if (!currentMonthData.members) return;
    
    currentMonthData.members.forEach(member => {
        let memberTotal = 0;
        currentMonthData.dates.forEach(date => {
            const count = parseFloat(currentMonthData.meals[member.id]?.[date] || 0);
            memberTotal += count;
        });
        
        const totalElement = document.getElementById(`grid-member-total-${member.id}`);
        if (totalElement) {
            totalElement.textContent = memberTotal.toFixed(1);
        }
    });
}

// Update stats display
function updateStats(data) {
    document.getElementById('stats-total-meals').textContent = parseFloat(data.total_meals).toFixed(1);
    document.getElementById('stats-avg-cost').textContent = '৳' + parseFloat(data.avg_meal_cost).toFixed(2);
    document.getElementById('stats-total-members').textContent = data.members.length;
    document.getElementById('stats-total-bazar').textContent = '৳' + parseFloat(data.total_bazar).toFixed(2);
    document.getElementById('current-month-display').textContent = `Tracking ${data.month_name} ${data.year}`;
    
    const displayGrandTotal = document.getElementById('display-grand-total');
    if (displayGrandTotal) {
        displayGrandTotal.textContent = parseFloat(data.total_meals).toFixed(1);
    }
    
    console.log('📈 Stats Updated:', {
        total_meals: data.total_meals,
        avg_meal_cost: data.avg_meal_cost,
        total_members: data.members.length,
        total_bazar: data.total_bazar
    });
}

// Save single meal via AJAX
function saveMeal(memberId, date, count) {
    const formData = new FormData();
    formData.append('action', 'save_single_meal');
    formData.append('member_id', memberId);
    formData.append('meal_date', date);
    formData.append('meal_count', count);
    
    fetch('process/actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Meal saved:', { memberId, date, count });
        } else {
            console.error('❌ Save failed:', data.error);
        }
    })
    .catch(error => {
        console.error('❌ Save error:', error);
    });
}

// Save all meals
function saveAllMeals() {
    const meals = {};
    const desktopInputs = document.querySelectorAll('.meal-input-advanced');
    const inputs = [...desktopInputs];
    
    inputs.forEach(input => {
        const memberId = input.dataset.member;
        const date = input.dataset.date;
        const count = parseFloat(input.value) || 0;
        
        if (!meals[memberId]) meals[memberId] = {};
        meals[memberId][date] = count;
    });
    
    const formData = new FormData();
    formData.append('action', 'save_all_meals');
    formData.append('meals', JSON.stringify(meals));
    formData.append('year', document.getElementById('yearSelect').value);
    formData.append('month', document.getElementById('monthSelect').value);
    
    showLoading();
    
    fetch('process/actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showNotification('✅ All meals saved successfully!', 'success');
            console.log('✅ All meals saved:', data);
        } else {
            showNotification('❌ Error saving meals: ' + data.error, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('❌ Save error:', error);
        showNotification('Error saving meals', 'error');
    });
}

// Quick action functions
function fillAllWithValue(value) {
    if (confirm(`Fill all meal counts with ${value}?`)) {
        const inputs = document.querySelectorAll('.meal-input-advanced');
        
        inputs.forEach(input => {
            input.value = value;
            updateMeal(input);
        });
        showNotification(`✅ All meals filled with ${value}`, 'success');
    }
}

function fillWeekendsWithValue(value) {
    if (confirm(`Fill only weekend meal counts with ${value}?`)) {
        const inputs = document.querySelectorAll('.meal-input-advanced');
        let updatedCount = 0;
        
        inputs.forEach(input => {
            const date = input.dataset.date;
            const dayOfWeek = new Date(date).getDay();
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                input.value = value;
                updateMeal(input);
                updatedCount++;
            }
        });
        
        showNotification(`✅ ${updatedCount} weekend meals filled with ${value}`, 'success');
    }
}

function fillWeekdaysWithValue(value) {
    if (confirm(`Fill only weekday meal counts with ${value}?`)) {
        const inputs = document.querySelectorAll('.meal-input-advanced');
        let updatedCount = 0;
        
        inputs.forEach(input => {
            const date = input.dataset.date;
            const dayOfWeek = new Date(date).getDay();
            if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                input.value = value;
                updateMeal(input);
                updatedCount++;
            }
        });
        
        showNotification(`✅ ${updatedCount} weekday meals filled with ${value}`, 'success');
    }
}

function clearAllMeals() {
    if (confirm('Clear all meal counts?')) {
        const inputs = document.querySelectorAll('.meal-input-advanced');
        
        inputs.forEach(input => {
            input.value = 0;
            updateMeal(input);
        });
        showNotification('✅ All meals cleared', 'success');
    }
}

// Pattern applications
function applyPattern(type) {
    const patterns = {
        breakfast: { weekdays: 1, weekends: 1 },
        lunch: { weekdays: 2, weekends: 2 },
        dinner: { weekdays: 1, weekends: 2 }
    };
    
    const pattern = patterns[type];
    const inputs = document.querySelectorAll('.meal-input-advanced');
    
    inputs.forEach(input => {
        const date = input.dataset.date;
        const dayOfWeek = new Date(date).getDay();
        const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
        input.value = isWeekend ? pattern.weekends : pattern.weekdays;
        updateMeal(input);
    });
    
    showNotification(`✅ Applied ${type} pattern`, 'success');
}

// Floating Action Button
function toggleFAB() {
    const fabMenu = document.querySelector('.fab-menu');
    fabMenu.classList.toggle('show');
}

function quickFill(value) {
    fillAllWithValue(value);
    toggleFAB();
}

// Modal functions
function showQuickFillModal() {
    const modal = new bootstrap.Modal(document.getElementById('quickFillModal'));
    modal.show();
}

function showAdvancedSettings() {
    showNotification('⚙️ Advanced settings coming soon!', 'info');
}

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('mealSearch');
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        // Implement search logic here
    });
}

// View toggle initialization
function initializeViewToggle() {
    const viewRadios = document.querySelectorAll('input[name="viewMode"]');
    viewRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            switchView(this.id.replace('View', ''));
        });
    });
}

// Utility functions
function chunkArray(array, size) {
    const chunks = [];
    for (let i = 0; i < array.length; i += size) {
        chunks.push(array.slice(i, i + size));
    }
    return chunks;
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.alert.position-fixed').forEach(alert => alert.remove());
    
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Utility functions
function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

// Enhanced initialization
document.addEventListener('DOMContentLoaded', function() {
    loadMonthData();
    initializeSearch();
    initializeViewToggle();
    setupKeyboardShortcuts();
    
    // Add keyboard shortcut hint to header
    const headerSubtitle = document.getElementById('current-month-display');
    if (headerSubtitle) {
        const hint = document.createElement('span');
        hint.className = 'keyboard-shortcut ms-2';
        hint.textContent = 'Ctrl+/ for shortcuts';
        hint.style.fontSize = '0.7rem';
        headerSubtitle.appendChild(hint);
    }
});

// Clear history when loading new month
function loadMonthData() {
    mealHistory.clear();
    
    const year = document.getElementById('yearSelect').value;
    const month = document.getElementById('monthSelect').value;
    
    showLoading();
    
    fetch(`ajax/load_meals.php?year=${year}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentMonthData = data;
                updateAllViews(data);
                console.log('🚀 Advanced meal data loaded:', data);
            } else {
                showNotification('Error loading data: ' + data.error, 'error');
            }
            hideLoading();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading meal data', 'error');
            hideLoading();
        });
}
</script>