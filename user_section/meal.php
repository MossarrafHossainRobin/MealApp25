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

<div class="meal-section fade-in-up">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-utensils text-warning me-2"></i>
                Meal Counts
            </h4>
            <p class="text-muted mb-0">Track your daily meal consumption</p>
        </div>
        <div class="text-end d-none d-md-block">
            <h5 class="text-warning mb-0"><?php echo $total_meals; ?> meals</h5>
            <small class="text-muted">Total Recorded</small>
        </div>
    </div>

    <!-- Mobile Total -->
    <div class="app-card d-md-none mb-3">
        <div class="card-body text-center">
            <h4 class="text-warning mb-0"><?php echo $total_meals; ?> meals</h4>
            <small class="text-muted">Total Meals Recorded</small>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="app-card text-center">
                <div class="card-body py-3">
                    <h3 class="text-primary mb-1"><?php echo $total_meals; ?></h3>
                    <small class="text-muted">Total Meals</small>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="app-card text-center">
                <div class="card-body py-3">
                    <h3 class="text-success mb-1"><?php echo $average_meals; ?></h3>
                    <small class="text-muted">Avg/Day</small>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="app-card text-center">
                <div class="card-body py-3">
                    <h3 class="text-info mb-1"><?php echo $days_tracked; ?></h3>
                    <small class="text-muted">Days Tracked</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Meal Records -->
    <div class="app-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-bar text-primary me-2"></i>
                Meal History
            </h5>
            <span class="badge bg-primary"><?php echo $days_tracked; ?> days</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($userData['meal_counts'])): ?>
                <div class="table-responsive">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Meals</th>
                                <th class="d-none d-md-table-cell">Description</th>
                                <th class="d-none d-sm-table-cell">Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userData['meal_counts'] as $meal): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?php echo date('M d', strtotime($meal['meal_date'])); ?></strong>
                                            <small
                                                class="text-muted d-block"><?php echo date('Y', strtotime($meal['meal_date'])); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-warning text-dark fs-6"><?php echo htmlspecialchars($meal['meal_count']); ?></span>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo htmlspecialchars($meal['description'] ?? 'No description'); ?>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <span
                                            class="badge bg-light text-dark"><?php echo date('D', strtotime($meal['meal_date'])); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state py-5">
                    <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                    <h5>No Meal Records</h5>
                    <p class="text-muted">You haven't recorded any meals yet.</p>
                    <a href="#" class="btn btn-warning mt-2">
                        <i class="fas fa-plus me-2"></i>Add First Meal
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Add Buttons -->
    <div class="row g-2 mt-3">
        <div class="col-6 col-md-3">
            <button class="btn btn-outline-primary w-100">
                <i class="fas fa-utensils me-1"></i> Breakfast
            </button>
        </div>
        <div class="col-6 col-md-3">
            <button class="btn btn-outline-success w-100">
                <i class="fas fa-utensils me-1"></i> Lunch
            </button>
        </div>
        <div class="col-6 col-md-3">
            <button class="btn btn-outline-warning w-100">
                <i class="fas fa-utensils me-1"></i> Dinner
            </button>
        </div>
        <div class="col-6 col-md-3">
            <button class="btn btn-outline-info w-100">
                <i class="fas fa-plus me-1"></i> Custom
            </button>
        </div>
    </div>
</div>