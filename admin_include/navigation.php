<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav">
    <a href="?section=home"
        class="flex-fill text-center py-3 text-decoration-none <?php echo $current_section === 'home' ? 'text-primary' : 'text-muted'; ?>">
        <div class="nav-icon">
            <i class="fas fa-tachometer-alt"></i>
        </div>
        <div class="nav-text">Dashboard</div>
    </a>
    <a href="?section=members"
        class="flex-fill text-center py-3 text-decoration-none <?php echo $current_section === 'members' ? 'text-primary' : 'text-muted'; ?>">
        <div class="nav-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="nav-text">Members</div>
    </a>
    <a href="?section=bazar"
        class="flex-fill text-center py-3 text-decoration-none <?php echo $current_section === 'bazar' ? 'text-primary' : 'text-muted'; ?>">
        <div class="nav-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="nav-text">Bazar</div>
    </a>
    <a href="?section=meal"
        class="flex-fill text-center py-3 text-decoration-none <?php echo $current_section === 'meal' ? 'text-primary' : 'text-muted'; ?>">
        <div class="nav-icon">
            <i class="fas fa-utensils"></i>
        </div>
        <div class="nav-text">Meals</div>
    </a>
    <a href="?section=more"
        class="flex-fill text-center py-3 text-decoration-none <?php echo in_array($current_section, ['water', 'settlement']) ? 'text-primary' : 'text-muted'; ?>"
        data-bs-toggle="dropdown">
        <div class="nav-icon">
            <i class="fas fa-ellipsis-h"></i>
        </div>
        <div class="nav-text">More</div>
    </a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="?section=water"><i class="fas fa-tint me-2"></i>Water Duty</a>
        <a class="dropdown-item" href="?section=settlement"><i class="fas fa-calculator me-2"></i>Settlement</a>
    </div>
</nav>