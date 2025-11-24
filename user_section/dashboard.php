<?php
// user_section/dashboard.php - Dashboard Overview

// Get user data from database
$userData = $action->getUserData($_SESSION['user_id']);
$user = $userData['member'];

// Calculate stats
$total_bazar = 0;
foreach ($userData['bazar'] as $bazar) {
    $total_bazar += $bazar['amount'];
}

$pending_duties = 0;
foreach ($userData['water_duties'] as $duty) {
    if ($duty['status'] === 'Pending')
        $pending_duties++;
}

$total_due = 0;
foreach ($userData['flat_costs'] as $cost) {
    if ($cost['status'] === 'Pending') {
        $total_due += $cost['amount_due'];
    }
}
?>

<style>
    .dashboard-section .balance-card {
        background: var(--gradient);
        color: white;
        border-radius: var(--card-radius);
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-section .balance-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(45deg);
    }
    
    .dashboard-section .quick-stats .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .dashboard-section .quick-stats .stat-card:active {
        transform: scale(0.95);
    }
    
    .dashboard-section .activity-item {
        border-left: 3px solid transparent;
        transition: var(--transition);
    }
    
    .dashboard-section .activity-item:hover {
        border-left-color: var(--primary);
        background: #f8f9fa;
    }
    
    .dashboard-section .user-avatar {
        width: 60px;
        height: 60px;
        background: var(--gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
    }
</style>

<div class="dashboard-section fade-in-up">
    <!-- Welcome & Balance Card -->
    <div class="balance-card">
        <div class="row align-items-center">
            <div class="col-8">
                <h4 class="mb-2">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 👋</h4>
                <p class="mb-0 opacity-75">Your meal management dashboard</p>
            </div>
            <div class="col-4 text-end">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Balance Overview -->
        <div class="row mt-4">
            <div class="col-6">
                <small class="opacity-75">Total Spent</small>
                <h5 class="mb-0">$<?php echo number_format($total_bazar, 2); ?></h5>
            </div>
            <div class="col-6 text-end">
                <small class="opacity-75">Amount Due</small>
                <h5 class="mb-0">$<?php echo number_format($total_due, 2); ?></h5>
            </div>
        </div>
    </div>

    <!-- Alert Message -->
    <?php if (!empty($message)): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
    <?php endif; ?>

    <!-- Quick Stats -->
    <div class="row quick-stats g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto">
                    <i class="fas fa-utensils"></i>
                </div>
                <h4 class="text-primary mb-1"><?php echo count($userData['meal_counts']); ?></h4>
                <p class="text-muted small mb-0">Recent Meals</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h4 class="text-primary mb-1">$<?php echo number_format($total_bazar, 2); ?></h4>
                <p class="text-muted small mb-0">Bazar Spent</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto">
                    <i class="fas fa-tint"></i>
                </div>
                <h4 class="text-primary mb-1"><?php echo $pending_duties; ?></h4>
                <p class="text-muted small mb-0">Pending Duties</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card text-center">
                <div class="stat-icon mx-auto">
                    <i class="fas fa-home"></i>
                </div>
                <h4 class="text-primary mb-1">$<?php echo number_format($total_due, 2); ?></h4>
                <p class="text-muted small mb-0">Amount Due</p>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Recent Activities -->
            <div class="app-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-primary me-2"></i>
                        Recent Activities
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($userData['bazar']) || !empty($userData['water_duties']) || !empty($userData['meal_counts'])): ?>
                            <div class="activity-list">
                                <?php
                                $activities = [];

                                // Add bazar activities
                                foreach (array_slice($userData['bazar'], 0, 3) as $bazar) {
                                    $activities[] = [
                                        'type' => 'bazar',
                                        'icon' => 'shopping-cart',
                                        'color' => 'success',
                                        'title' => 'Bazar Contribution',
                                        'date' => $bazar['bazar_date'],
                                        'amount' => '$' . number_format($bazar['amount'], 2),
                                        'description' => $bazar['description'] ?? 'Shopping'
                                    ];
                                }

                                // Add water duties
                                foreach (array_slice($userData['water_duties'], 0, 2) as $duty) {
                                    $activities[] = [
                                        'type' => 'water',
                                        'icon' => 'tint',
                                        'color' => 'primary',
                                        'title' => 'Water Duty - ' . $duty['status'],
                                        'date' => $duty['duty_date'],
                                        'amount' => '',
                                        'description' => $duty['status']
                                    ];
                                }

                                // Add meals
                                foreach (array_slice($userData['meal_counts'], 0, 2) as $meal) {
                                    $activities[] = [
                                        'type' => 'meal',
                                        'icon' => 'utensils',
                                        'color' => 'warning',
                                        'title' => 'Meal Recorded',
                                        'date' => $meal['meal_date'],
                                        'amount' => $meal['meal_count'] . ' meals',
                                        'description' => $meal['description'] ?? 'Daily meal'
                                    ];
                                }

                                // Sort by date
                                usort($activities, function ($a, $b) {
                                    return strtotime($b['date']) - strtotime($a['date']);
                                });

                                // Display activities
                                foreach (array_slice($activities, 0, 5) as $activity):
                                    ?>
                                        <div class="activity-item p-3 mb-2 rounded">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="bg-<?php echo $activity['color']; ?> bg-opacity-10 rounded-circle p-2">
                                                        <i class="fas fa-<?php echo $activity['icon']; ?> text-<?php echo $activity['color']; ?>"></i>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <h6 class="mb-1"><?php echo $activity['title']; ?></h6>
                                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($activity['date'])); ?></small>
                                                </div>
                                                <div class="col-auto">
                                                    <span class="text-<?php echo $activity['color']; ?> fw-bold">
                                                        <?php echo $activity['amount']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                <?php endforeach; ?>
                            </div>
                    <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>No Activities Yet</h5>
                                <p class="text-muted">Your recent activities will appear here</p>
                            </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Personal Information -->
            <div class="app-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-circle text-primary me-2"></i>
                        Personal Info
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($user): ?>
                            <div class="row g-2">
                                <div class="col-5"><small class="text-muted">ID:</small></div>
                                <div class="col-7"><small class="fw-bold"><?php echo htmlspecialchars($user['id']); ?></small></div>
                            
                                <div class="col-5"><small class="text-muted">Email:</small></div>
                                <div class="col-7"><small class="fw-bold"><?php echo htmlspecialchars($user['email']); ?></small></div>
                            
                                <div class="col-5"><small class="text-muted">Base Rent:</small></div>
                                <div class="col-7"><small class="fw-bold text-success">$<?php echo number_format($user['base_rent'], 2); ?></small></div>
                            
                                <div class="col-5"><small class="text-muted">Status:</small></div>
                                <div class="col-7">
                                    <span class="status-badge badge-<?php echo $user['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                            
                                <div class="col-5"><small class="text-muted">Joined:</small></div>
                                <div class="col-7"><small class="fw-bold"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></small></div>
                            </div>
                    <?php else: ?>
                            <div class="alert alert-warning mb-0">
                                <small>Could not retrieve member details.</small>
                            </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="app-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="index.php?section=bazar" class="btn btn-outline-primary w-100 btn-sm">
                                <i class="fas fa-plus me-1"></i> Add Bazar
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="index.php?section=meal" class="btn btn-outline-success w-100 btn-sm">
                                <i class="fas fa-utensils me-1"></i> Log Meal
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="index.php?section=water" class="btn btn-outline-info w-100 btn-sm">
                                <i class="fas fa-tint me-1"></i> Water Duty
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="index.php?section=cost" class="btn btn-outline-warning w-100 btn-sm">
                                <i class="fas fa-money-bill me-1"></i> Pay Cost
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Mobile bottom nav hide/show on scroll
    let lastScrollTop = 0;
    const mobileNav = document.getElementById('mobileNav');
    let isScrolling;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Clear our timeout throughout the scroll
        window.clearTimeout(isScrolling);
        
        // Show/hide nav based on scroll direction
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scrolling down
            mobileNav.classList.add('hidden');
        } else {
            // Scrolling up
            mobileNav.classList.remove('hidden');
        }
        
        lastScrollTop = scrollTop;
        
        // Set a timeout to run after scrolling ends
        isScrolling = setTimeout(function() {
            mobileNav.classList.remove('hidden');
        }, 150);
    }, false);

    // Add touch feedback for mobile
    document.addEventListener('touchstart', function() {}, { passive: true });
</script>