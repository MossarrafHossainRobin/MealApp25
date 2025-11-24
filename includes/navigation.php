<style>
        /* ========= GLOBAL VARIABLES (Professional/Minimal) ========= */
        :root {
                /* Primary/Accent Colors (Deep Teal/Green) */
                --primary: #059669;
                /* Deep Professional Green */
                --primary-dark: #064E3B;
                /* Very dark accent */
                --primary-light: #34D399;
                --secondary: #1F2937;
                /* Deep Slate Gray for dark themes */
                --secondary-light: #374151;
                --secondary-translucent: rgba(31, 41, 55, 0.95);
                /* New: Slightly transparent dark bar */
                --text-light: #F9FAFB;
                --text-dim: #9CA3AF;
                /* Subtle inactive text */
                --text-dark: #111827;

                /* Shadows and Effects (Subtle Complexity) */
                --shadow-desktop: 0 4px 15px rgba(0, 0, 0, 0.05);
                /* Clean, subtle desktop shadow */
                --shadow-mobile-bar: 0 0 20px rgba(0, 0, 0, 0.5);
                /* Softened shadow */
                --active-mobile-bg: #065F46;
                /* Subtle dark green active state (kept for reference, but not used as background) */
        }

        /* Base reset for professionalism */
        *,
        *::before,
        *::after {
                box-sizing: border-box;
        }

        /* ========= DESKTOP NAVIGATION (Clean Professional Light) >= 1200px ========= */
        @media (min-width: 1200px) {
                .desktop-nav {
                        background: #FFFFFF;
                        border-bottom: 1px solid #E5E7EB;
                        box-shadow: var(--shadow-desktop);
                        padding: 1rem 0;
                        transition: all 0.4s ease-in-out;
                        position: sticky;
                        top: 0;
                        z-index: 900;
                }

                .nav-container {
                        max-width: 1400px;
                        margin: 0 auto;
                        padding: 0 2.5rem;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                }

                .navbar-brand {
                        color: var(--primary-dark) !important;
                        font-weight: 800;
                        font-size: 2rem;
                        letter-spacing: -0.8px;
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        text-decoration: none;
                }

                .brand-icon {
                        width: 40px;
                        height: 40px;
                        background: var(--primary);
                        border-radius: 8px;
                        /* Sharper corners */
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
                }

                .brand-icon i {
                        color: white;
                        font-size: 1.4rem;
                }

                .nav-menu {
                        display: flex;
                        gap: 0.25rem;
                        align-items: center;
                }

                .nav-link {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        padding: 0.75rem 1.25rem;
                        border-radius: 8px;
                        color: var(--text-dark) !important;
                        font-weight: 600;
                        font-size: 1.0rem;
                        transition: all 0.2s ease-in-out;
                        background: transparent;
                        min-width: 120px;
                        justify-content: center;
                        text-decoration: none;
                        cursor: pointer;
                        border: 2px solid transparent;
                }

                /* Hover: Subtle color shift */
                .nav-link:hover {
                        background: #F3F4F6;
                        color: var(--primary-dark) !important;
                }

                /* Active State: Clean Underline/Fill */
                .nav-link.active {
                        color: var(--primary-dark) !important;
                        font-weight: 700;
                        background: #F3F4F6;
                        border-bottom: 2px solid var(--primary);
                        /* Professional active indicator */
                        box-shadow: inset 0 -2px 0 var(--primary);
                }

                .nav-link i {
                        font-size: 1.1rem;
                        color: var(--primary);
                        /* Icon color */
                }

                .nav-link.active i {
                        color: var(--primary-dark);
                }
        }

        /* ========= TABLET NAVIGATION (Clean Professional Light) 992px-1199px ========= */
        @media (max-width: 1199px) and (min-width: 992px) {
                .desktop-nav {
                        padding: 0.8rem 0;
                }

                .nav-container {
                        padding: 0 2rem;
                }

                .navbar-brand {
                        font-size: 1.8rem;
                }

                .brand-icon {
                        width: 35px;
                        height: 35px;
                }

                .brand-icon i {
                        font-size: 1.2rem;
                }

                .nav-link {
                        padding: 0.6rem 1rem;
                        font-size: 0.95rem;
                        min-width: 100px;
                }

                .nav-link i {
                        font-size: 1.0rem;
                }
        }

        /* ========= DESKTOP ONLY (992px and above) ========= */
        @media (min-width: 992px) {
                .iphone-nav {
                        display: none !important;
                }
        }

        /* ========= MOBILE NAVIGATION (MODERN DARK BOTTOM BAR) < 992px ========= */
        @media (max-width: 991px) {
                .desktop-nav {
                        display: none !important;
                }

                .iphone-nav {
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        width: 100%;
                        height: 60px;
                        /* Use translucent background for modern effect */
                        background: var(--secondary-translucent);
                        border-top: 1px solid rgba(255, 255, 255, 0.05);
                        /* Subtle border */
                        box-shadow: var(--shadow-mobile-bar);
                        /* Softened shadow */
                        z-index: 1000;
                        display: flex;
                        align-items: center;
                        justify-content: space-around;
                        padding: 0 10px;
                        backdrop-filter: blur(5px);
                        /* Optional: Frosted glass effect */
                }

                /* Added padding for safe area on bottom of iPhones */
                @supports (padding: max(0px)) {
                        .iphone-nav {
                                padding-bottom: env(safe-area-inset-bottom);
                                height: calc(60px + env(safe-area-inset-bottom));
                        }
                }

                .iphone-nav-item {
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        padding: 8px 0;
                        border-radius: 4px;
                        transition: all 0.2s ease-in-out;
                        position: relative;
                        min-height: 50px;
                }

                /* Hover/Click Effect: Subtle ripple of dark white */
                .iphone-nav-item:hover,
                .iphone-nav-item:active {
                        background: rgba(255, 255, 255, 0.1);
                        border-radius: 6px;
                }

                .iphone-nav-link {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 2px;
                        color: var(--text-dim);
                        text-decoration: none;
                        font-size: 0.7rem;
                        font-weight: 500;
                        transition: all 0.2s ease-in-out;
                        width: 100%;
                }

                /* Active State: REMOVED BACKGROUND. Focusing on color shift and weight change. */
                .iphone-nav-item.active {
                        /* background: var(--active-mobile-bg); <-- REMOVED */
                        border-radius: 6px;
                }

                .iphone-nav-item.active .iphone-nav-link {
                        color: var(--text-light);
                        font-weight: 600;
                }

                .iphone-nav-icon {
                        font-size: 1.2rem;
                        transition: all 0.2s ease-in-out;
                        font-weight: normal;
                }

                /* Active Icon: More prominence (color, size, weight) */
                .iphone-nav-item.active .iphone-nav-icon {
                        color: var(--primary-light);
                        font-weight: 700;
                        /* Bolder icon */
                        transform: scale(1.15);
                        /* More pronounced scale */
                }
        }

        /* ========= SMALL MOBILE ADJUSTMENTS < 480px ========= */
        @media (max-width: 480px) {
                .iphone-nav {
                        height: 55px;
                }

                .iphone-nav-item {
                        min-height: 45px;
                        padding: 6px 0;
                }

                .iphone-nav-link {
                        font-size: 0.65rem;
                }

                .iphone-nav-icon {
                        font-size: 1.1rem;
                }
        }

        /* ========= HIDE ON SCROLL ANIMATION (Professional Speed) ========= */
        .nav-hidden {
                transform: translateY(-100%);
                opacity: 0;
                pointer-events: none;
        }

        .mobile-nav-hidden {
                transform: translateY(100%);
                opacity: 0;
                pointer-events: none;
        }

        /* Consistent Transitions */
        .desktop-nav,
        .iphone-nav {
                transition: all 0.3s ease-in-out;
        }

        @media (prefers-reduced-motion: reduce) {

                .desktop-nav,
                .iphone-nav,
                .nav-link,
                .iphone-nav-item {
                        transition: none !important;
                }
        }
</style>
<nav id="mainNav" class="navbar navbar-expand-lg desktop-nav sticky-top">
        <div class="nav-container">
                <a class="navbar-brand" href="#" onclick="showSection('home')">
                        <div class="brand-icon">
                                <i class="bi bi-wallet2"></i>
                        </div>
                        MealApp25
                </a>

                <ul class="navbar-nav ms-auto nav-menu">
                        <li class="nav-item">
                                <a class="nav-link <?php echo $current_section === 'home' ? 'active' : ''; ?>"
                                        onclick="showSection('home')">
                                        <i class="bi bi-house-door"></i>
                                        Dashboard
                                </a>
                        </li>

                        <li class="nav-item">
                                <a class="nav-link <?php echo $current_section === 'bazar' ? 'active' : ''; ?>"
                                        onclick="showSection('bazar')">
                                        <i class="bi bi-cart-fill"></i>
                                        Add Bazar
                                </a>
                        </li>

                        <li class="nav-item">
                                <a class="nav-link <?php echo $current_section === 'members' ? 'active' : ''; ?>"
                                        onclick="showSection('members')">
                                        <i class="bi bi-people-fill"></i>
                                        Members
                                </a>
                        </li>

                        <li class="nav-item">
                                <a class="nav-link <?php echo $current_section === 'settlement' ? 'active' : ''; ?>"
                                        onclick="showSection('settlement')">
                                        <i class="bi bi-currency-dollar"></i>
                                        Settlement
                                </a>
                        </li>

                        <li class="nav-item">
                                <a class="nav-link <?php echo $current_section === 'water' ? 'active' : ''; ?>"
                                        onclick="showSection('water')">
                                        <i class="bi bi-droplet-fill"></i>
                                        Water Duty
                                </a>
                        </li>

                        <li class="nav-item">
                                <a class="nav-link <?php echo $current_section === 'mealcount' ? 'active' : ''; ?>"
                                        onclick="showSection('mealcount')">
                                        <i class="bi bi-calendar-check-fill"></i>
                                        Meal Count
                                </a>
                        </li>
                </ul>
        </div>
</nav>

<nav id="mobileNav" class="iphone-nav">
        <div class="iphone-nav-item <?php echo $current_section === 'home' ? 'active' : ''; ?>">
                <a class="iphone-nav-link" onclick="showSection('home')">
                        <i class="bi bi-house-door iphone-nav-icon"></i>
                        Home
                </a>
        </div>

        <div class="iphone-nav-item <?php echo $current_section === 'bazar' ? 'active' : ''; ?>">
                <a class="iphone-nav-link" onclick="showSection('bazar')">
                        <i class="bi bi-cart-fill iphone-nav-icon"></i>
                        Bazar
                </a>
        </div>

        <div class="iphone-nav-item <?php echo $current_section === 'members' ? 'active' : ''; ?>">
                <a class="iphone-nav-link" onclick="showSection('members')">
                        <i class="bi bi-people-fill iphone-nav-icon"></i>
                        Members
                </a>
        </div>

        <div class="iphone-nav-item <?php echo $current_section === 'settlement' ? 'active' : ''; ?>">
                <a class="iphone-nav-link" onclick="showSection('settlement')">
                        <i class="bi bi-currency-dollar iphone-nav-icon"></i>
                        Settlement
                </a>
        </div>

        <div class="iphone-nav-item <?php echo $current_section === 'mealcount' ? 'active' : ''; ?>">
                <a class="iphone-nav-link" onclick="showSection('mealcount')">
                        <i class="bi bi-calendar-check-fill iphone-nav-icon"></i>
                        Meals
                </a>
        </div>
</nav>
<script>
        let lastScroll = 0;
        const desktopNav = document.getElementById("mainNav");
        const mobileNav = document.getElementById("mobileNav");
        let scrollTimeout;
        const scrollThreshold = 50;

        function handleScroll() {
                const current = window.pageYOffset;
                const scrollDelta = Math.abs(current - lastScroll);

                if (scrollDelta > 3) {
                        if (current > lastScroll && current > scrollThreshold) {
                                // Scrolling down
                                desktopNav?.classList.add("nav-hidden");
                                mobileNav?.classList.add("mobile-nav-hidden");
                        } else {
                                // Scrolling up
                                desktopNav?.classList.remove("nav-hidden");
                                mobileNav?.classList.remove("mobile-nav-hidden");
                        }
                }

                lastScroll = current;

                // Debounce: Show the navbars if scrolling stops
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                        desktopNav?.classList.remove("nav-hidden");
                        mobileNav?.classList.remove("mobile-nav-hidden");
                }, 1500); // Shorter timeout for professional feel
        }

        // Throttled scroll event
        let ticking = false;
        window.addEventListener('scroll', function () {
                if (!ticking) {
                        requestAnimationFrame(function () {
                                handleScroll();
                                ticking = false;
                        });
                        ticking = true;
                }
        });

        // Removed touchstart/touchend effects for clean, professional look

        // Handle resize events
        window.addEventListener('resize', function () {
                desktopNav?.classList.remove("nav-hidden");
                mobileNav?.classList.remove("mobile-nav-hidden");
        });
</script>