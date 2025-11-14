<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold fs-3 text-success" href="#" onclick="showSection('home')"><i
                class="bi bi-house-fill"></i> MealApp25</a>
        <div class="d-flex justify-content-end w-100">
            <ul class="navbar-nav d-flex flex-row gap-3">
                <li class="nav-item"><a
                        class="nav-link text-success fw-semibold px-3 py-2 rounded hover-effect <?php echo $current_section === 'home' ? 'active' : ''; ?>"
                        onclick="showSection('home')"><i class="bi bi-house"></i> Home</a></li>
                <li class="nav-item"><a
                        class="nav-link text-success fw-semibold px-3 py-2 rounded hover-effect <?php echo $current_section === 'bazar' ? 'active' : ''; ?>"
                        onclick="showSection('bazar')"><i class="bi bi-cart-fill"></i> Add Bazar</a></li>
                <li class="nav-item"><a
                        class="nav-link text-success fw-semibold px-3 py-2 rounded hover-effect <?php echo $current_section === 'summary' ? 'active' : ''; ?>"
                        onclick="showSection('summary')"><i class="bi bi-clipboard-data-fill"></i> Bazar Log</a></li>
                <li class="nav-item"><a
                        class="nav-link text-success fw-semibold px-3 py-2 rounded hover-effect <?php echo $current_section === 'mealcount' ? 'active' : ''; ?>"
                        onclick="showSection('mealcount')"><i class="bi bi-calendar3"></i> Meal Count</a></li>
                <li class="nav-item"><a
                        class="nav-link text-success fw-semibold px-3 py-2 rounded hover-effect <?php echo $current_section === 'settlement' ? 'active' : ''; ?>"
                        onclick="showSection('settlement')"><i class="bi bi-currency-dollar"></i> Settlement</a></li>
                <li class="nav-item"><a
                        class="nav-link text-success fw-semibold px-3 py-2 rounded hover-effect <?php echo $current_section === 'water' ? 'active' : ''; ?>"
                        onclick="showSection('water')"><i class="bi bi-droplet-half"></i> Water Duty</a></li>
                <li class="nav-item"><a
                        class="nav-link text-success fw-semibold px-3 py-2 rounded hover-effect <?php echo $current_section === 'members' ? 'active' : ''; ?>"
                        onclick="showSection('members')"><i class="bi bi-person-gear"></i> Members</a></li>
            </ul>
        </div>
    </div>
</nav>