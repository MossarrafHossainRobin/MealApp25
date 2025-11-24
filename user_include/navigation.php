<?php
// user_include/navigation.php

// Determine current section for active state
$current_section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
?>

<!-- Desktop Navigation -->
<nav class="desktop-nav d-none d-lg-block">
    <div class="container">
        <div class="d-flex justify-content-center">
            <a href="index.php?section=dashboard"
                class="desktop-nav-link <?php echo $current_section === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-home me-2"></i>Dashboard
            </a>
            <a href="index.php?section=bazar"
                class="desktop-nav-link <?php echo $current_section === 'bazar' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart me-2"></i>Bazar
            </a>
            <a href="index.php?section=water"
                class="desktop-nav-link <?php echo $current_section === 'water' ? 'active' : ''; ?>">
                <i class="fas fa-tint me-2"></i>Water Duties
            </a>
            <a href="index.php?section=meal"
                class="desktop-nav-link <?php echo $current_section === 'meal' ? 'active' : ''; ?>">
                <i class="fas fa-utensils me-2"></i>Meals
            </a>
            <a href="index.php?section=cost"
                class="desktop-nav-link <?php echo $current_section === 'cost' ? 'active' : ''; ?>">
                <i class="fas fa-money-bill me-2"></i>Costs
            </a>
        </div>
    </div>
</nav>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav d-lg-none" id="mobileNav">
    <div class="container">
        <div class="row text-center">
            <div class="col">
                <a href="index.php?section=dashboard"
                    class="nav-item <?php echo $current_section === 'dashboard' ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="nav-label">Dashboard</div>
                </a>
            </div>
            <div class="col">
                <a href="index.php?section=bazar"
                    class="nav-item <?php echo $current_section === 'bazar' ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="nav-label">Bazar</div>
                </a>
            </div>
            <div class="col">
                <a href="index.php?section=water"
                    class="nav-item <?php echo $current_section === 'water' ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="nav-label">Water</div>
                </a>
            </div>
            <div class="col">
                <a href="index.php?section=meal"
                    class="nav-item <?php echo $current_section === 'meal' ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="nav-label">Meals</div>
                </a>
            </div>
            <div class="col">
                <a href="index.php?section=cost"
                    class="nav-item <?php echo $current_section === 'cost' ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <i class="fas fa-money-bill"></i>
                    </div>
                    <div class="nav-label">Costs</div>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content Container -->
<div class="container main-content">
    <div class="row">
        <div class="col-12">