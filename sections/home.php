<div class="container mt-4">
    <div class="text-center py-4 px-4 bg-light rounded shadow">
        <h1 class="display-4 text-success mb-4">Welcome to MealApp25</h1>
        <p class="lead text-muted mb-4">Complete PHP/MySQL solution for tracking flatmates' expenses, meals, and duties.
        </p>
        <div class="row justify-content-center mt-4">
            <div class="col-md-3 mb-3">
                <div class="p-3 bg-white shadow rounded">
                    <h5 class="text-success">Total Members</h5>
                    <div class="h4 text-dark mt-2"><?php echo $total_members; ?></div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3 bg-white shadow rounded">
                    <h5 class="text-success">Total Bazar</h5>
                    <div class="h4 text-dark mt-2">BDT <?php echo number_format($total_bazar_all, 2); ?></div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3 bg-white shadow rounded">
                    <h5 class="text-success">Total Meals</h5>
                    <div class="h4 text-dark mt-2"><?php echo $total_meals_all; ?></div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3 bg-white shadow rounded">
                    <h5 class="text-success">Avg Meal Cost</h5>
                    <div class="h4 text-dark mt-2">BDT <?php echo number_format($avg_meal_cost, 2); ?></div>
                </div>
            </div>
        </div>
        <div class="chart-container mt-4" style="max-width: 600px; margin: 0 auto;">
            <canvas id="monthlyBazarChart"></canvas>
        </div>
    </div>
</div>