<?php
// user_section/meal.php - Meal Counts Section

// Get user data
$userData = $action->getUserData($_SESSION['user_id']);

$total_meals = 0;
$days_tracked = count($userData['meal_counts']);
foreach ($userData['meal_counts'] as $meal) {
    $total_meals += (int) $meal['meal_count'];
}
$average_meals = $days_tracked > 0 ? number_format($total_meals / $days_tracked, 1) : 0;
?>

<style>
    .meal-section {
        padding: 0;
        margin: 0;
    }

    /* Mobile First Design */
    .mobile-meal {
        display: block;
    }
    
    .desktop-meal {
        display: none;
    }

    /* Mobile Meal Styles */
    .mobile-meal {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 0;
        margin: 0;
    }

    .meal-header {
        background: linear-gradient(135deg, #d63384, #e91e63);
        color: white;
        padding: 20px 15px;
    }

    .meal-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .meal-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .meal-content {
        padding: 20px 15px;
        padding-bottom: 80px;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 15px 10px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .stat-card:active {
        transform: scale(0.95);
    }

    .stat-value {
        font-size: 1.4rem;
        font-weight: 800;
        margin-bottom: 4px;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #666;
        font-weight: 600;
        line-height: 1;
    }

    /* Color Coding */
    .text-total { color: #d63384; }
    .text-average { color: #27ae60; }
    .text-days { color: #3498db; }

    /* Meal History Card */
    .history-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
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
        color: #d63384;
    }

    /* Meal Items */
    .meal-list {
        margin-top: 15px;
    }

    .meal-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        border-left: 4px solid #d63384;
        transition: all 0.2s ease;
    }

    .meal-item:active {
        transform: scale(0.98);
        background: #e9ecef;
    }

    .meal-date {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }

    .meal-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .meal-count {
        background: linear-gradient(135deg, #d63384, #e91e63);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 2px 8px rgba(214, 51, 132, 0.3);
    }

    .meal-description {
        color: #666;
        font-size: 0.8rem;
        font-weight: 500;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 20px;
    }

    .action-btn {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 15px 10px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .action-btn:active {
        transform: scale(0.95);
        background: #f8f9fa;
    }

    .action-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
    }

    .breakfast .action-icon { background: linear-gradient(135deg, #ff6b6b, #ee5a52); }
    .lunch .action-icon { background: linear-gradient(135deg, #1dd1a1, #10ac84); }
    .dinner .action-icon { background: linear-gradient(135deg, #feca57, #ff9f43); }
    .custom .action-icon { background: linear-gradient(135deg, #54a0ff, #2e86de); }

    .action-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #2c3e50;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-icon {
        font-size: 3rem;
        color: #d63384;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .empty-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .empty-text {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    /* Add Meal Button */
    .add-meal-btn {
        background: linear-gradient(135deg, #d63384, #e91e63);
        color: white;
        border: none;
        border-radius: 25px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 4px 15px rgba(214, 51, 132, 0.3);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .add-meal-btn:active {
        transform: scale(0.95);
    }

    /* Desktop Meal Styles */
    @media (min-width: 992px) {
        .mobile-meal {
            display: none;
        }
        
        .desktop-meal {
            display: block;
            padding: 30px 0;
            background: #f8f9fa;
            min-height: calc(100vh - 80px);
        }

        .desktop-meal-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .desktop-meal-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .desktop-meal-title {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #d63384, #e91e63);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .desktop-meal-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        .desktop-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .desktop-stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .desktop-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        }

        .desktop-stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .desktop-stat-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 600;
        }

        .desktop-history-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .desktop-meal-list {
            margin-top: 20px;
        }

        .desktop-meal-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #d63384;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .desktop-meal-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .desktop-meal-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .desktop-meal-date {
            font-weight: 700;
            color: #2c3e50;
            font-size: 1rem;
            min-width: 120px;
        }

        .desktop-meal-description {
            color: #666;
            font-size: 0.9rem;
        }

        .desktop-meal-count {
            background: linear-gradient(135deg, #d63384, #e91e63);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(214, 51, 132, 0.3);
        }

        .desktop-quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 30px;
        }

        .desktop-action-btn {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 25px 15px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .desktop-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #d63384;
        }

        .desktop-action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto 12px;
        }

        .desktop-action-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .desktop-empty-state {
            text-align: center;
            padding: 60px 40px;
        }

        .desktop-empty-icon {
            font-size: 4rem;
            color: #d63384;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .desktop-empty-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .desktop-add-meal-btn {
            background: linear-gradient(135deg, #d63384, #e91e63);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 40px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 8px 25px rgba(214, 51, 132, 0.3);
            transition: all 0.3s ease;
        }

        .desktop-add-meal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(214, 51, 132, 0.4);
        }
    }

    /* Responsive Design */
    @media (max-width: 480px) {
        .meal-content {
            padding: 15px 12px;
        }

        .stats-grid {
            gap: 8px;
        }

        .stat-card {
            padding: 12px 8px;
        }

        .stat-value {
            font-size: 1.2rem;
        }

        .history-card {
            padding: 15px;
        }

        .meal-item {
            padding: 12px;
        }

        .quick-actions {
            gap: 8px;
        }

        .action-btn {
            padding: 12px 8px;
        }

        .action-icon {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
    }
</style>

<!-- Mobile Meal Section -->
<div class="mobile-meal">
    <div class="meal-header">
        <div class="meal-title">Meal Tracker</div>
        <div class="meal-subtitle">Track your daily meal consumption</div>
    </div>

    <div class="meal-content">
        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value text-total"><?php echo $total_meals; ?></div>
                <div class="stat-label">Total Meals</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-average"><?php echo $average_meals; ?></div>
                <div class="stat-label">Avg/Day</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-days"><?php echo $days_tracked; ?></div>
                <div class="stat-label">Days Tracked</div>
            </div>
        </div>

        <!-- Meal History -->
        <div class="history-card">
            <div class="card-title">
                <i class="fas fa-history"></i>
                Meal History
            </div>

            <?php if (!empty($userData['meal_counts'])): ?>
                    <div class="meal-list">
                        <?php foreach ($userData['meal_counts'] as $meal): ?>
                                <div class="meal-item">
                                    <div class="meal-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo date('M d, Y', strtotime($meal['meal_date'])); ?>
                                    </div>
                                    <div class="meal-details">
                                        <div class="meal-description">
                                            <?php echo htmlspecialchars($meal['description'] ?? 'No description'); ?>
                                        </div>
                                        <div class="meal-count">
                                            <?php echo htmlspecialchars($meal['meal_count']); ?> meals
                                        </div>
                                    </div>
                                </div>
                        <?php endforeach; ?>
                    </div>
            <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="empty-title">No Meal Records</div>
                        <div class="empty-text">You haven't recorded any meals yet.</div>
                        <button class="add-meal-btn">
                            <i class="fas fa-plus"></i>
                            Add First Meal
                        </button>
                    </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="action-btn breakfast">
                <div class="action-icon">
                    <i class="fas fa-sun"></i>
                </div>
                <div class="action-label">Breakfast</div>
            </div>
            <div class="action-btn lunch">
                <div class="action-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="action-label">Lunch</div>
            </div>
            <div class="action-btn dinner">
                <div class="action-icon">
                    <i class="fas fa-moon"></i>
                </div>
                <div class="action-label">Dinner</div>
            </div>
            <div class="action-btn custom">
                <div class="action-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="action-label">Custom</div>
            </div>
        </div>
    </div>
</div>

<!-- Desktop Meal Section -->
<div class="desktop-meal">
    <div class="desktop-meal-container">
        <div class="desktop-meal-header">
            <h1 class="desktop-meal-title">Meal Tracker</h1>
            <p class="desktop-meal-subtitle">Track and manage your daily meal consumption</p>
        </div>

        <!-- Stats Overview -->
        <div class="desktop-stats-grid">
            <div class="desktop-stat-card">
                <div class="desktop-stat-value text-total"><?php echo $total_meals; ?></div>
                <div class="desktop-stat-label">Total Meals</div>
            </div>
            <div class="desktop-stat-card">
                <div class="desktop-stat-value text-average"><?php echo $average_meals; ?></div>
                <div class="desktop-stat-label">Average Per Day</div>
            </div>
            <div class="desktop-stat-card">
                <div class="desktop-stat-value text-days"><?php echo $days_tracked; ?></div>
                <div class="desktop-stat-label">Days Tracked</div>
            </div>
        </div>

        <!-- Meal History -->
        <div class="desktop-history-card">
            <div class="card-title">
                <i class="fas fa-history"></i>
                Meal History
            </div>

            <?php if (!empty($userData['meal_counts'])): ?>
                    <div class="desktop-meal-list">
                        <?php foreach ($userData['meal_counts'] as $meal): ?>
                                <div class="desktop-meal-item">
                                    <div class="desktop-meal-info">
                                        <div class="desktop-meal-date">
                                            <?php echo date('M d, Y', strtotime($meal['meal_date'])); ?>
                                        </div>
                                        <div class="desktop-meal-description">
                                            <?php echo htmlspecialchars($meal['description'] ?? 'No description provided'); ?>
                                        </div>
                                    </div>
                                    <div class="desktop-meal-count">
                                        <?php echo htmlspecialchars($meal['meal_count']); ?> meals
                                    </div>
                                </div>
                        <?php endforeach; ?>
                    </div>
            <?php else: ?>
                    <div class="desktop-empty-state">
                        <div class="desktop-empty-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="desktop-empty-title">No Meal Records Found</div>
                        <div class="empty-text">Start tracking your meals to see your consumption patterns</div>
                        <button class="desktop-add-meal-btn">
                            <i class="fas fa-plus me-2"></i>
                            Add Your First Meal
                        </button>
                    </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="desktop-quick-actions">
            <div class="desktop-action-btn breakfast">
                <div class="desktop-action-icon">
                    <i class="fas fa-sun"></i>
                </div>
                <div class="desktop-action-label">Breakfast</div>
            </div>
            <div class="desktop-action-btn lunch">
                <div class="desktop-action-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="desktop-action-label">Lunch</div>
            </div>
            <div class="desktop-action-btn dinner">
                <div class="desktop-action-icon">
                    <i class="fas fa-moon"></i>
                </div>
                <div class="desktop-action-label">Dinner</div>
            </div>
            <div class="desktop-action-btn custom">
                <div class="desktop-action-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="desktop-action-label">Custom Meal</div>
            </div>
        </div>
    </div>
</div>

<script>
    // Add click handlers for quick action buttons
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile action buttons
        const mobileActions = document.querySelectorAll('.mobile-meal .action-btn');
        mobileActions.forEach(btn => {
            btn.addEventListener('click', function() {
                const mealType = this.classList[1]; // breakfast, lunch, dinner, custom
                addMeal(mealType);
            });
        });

        // Desktop action buttons
        const desktopActions = document.querySelectorAll('.desktop-meal .desktop-action-btn');
        desktopActions.forEach(btn => {
            btn.addEventListener('click', function() {
                const mealType = this.classList[1];
                addMeal(mealType);
            });
        });

        // Add meal buttons
        const addMealBtns = document.querySelectorAll('.add-meal-btn, .desktop-add-meal-btn');
        addMealBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                addMeal('custom');
            });
        });

        function addMeal(type) {
            // Add visual feedback
            const event = new CustomEvent('mealAction', { 
                detail: { type: type } 
            });
            document.dispatchEvent(event);
            
            // Show temporary feedback
            alert(`Adding ${type} meal...`);
            // In real implementation, this would open a modal or form
        }
    });
</script>