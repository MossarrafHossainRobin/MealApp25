<?php
// user_include/footer.php
?>
</div> <!-- col-12 -->
</div> <!-- row -->
</div> <!-- container -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script>
    // Mobile bottom nav behavior
    document.addEventListener('DOMContentLoaded', function () {
        const mobileNav = document.getElementById('mobileNav');
        let lastScrollTop = 0;
        let scrollTimeout;

        // Hide/show nav on scroll
        window.addEventListener('scroll', function () {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            clearTimeout(scrollTimeout);

            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down - hide nav
                mobileNav.style.transform = 'translateY(100%)';
            } else {
                // Scrolling up - show nav
                mobileNav.style.transform = 'translateY(0)';
            }

            lastScrollTop = scrollTop;

            // Show nav when scrolling stops
            scrollTimeout = setTimeout(function () {
                mobileNav.style.transform = 'translateY(0)';
            }, 500);
        });

        // Add touch feedback
        const statCards = document.querySelectorAll('.stat-card, .nav-item');
        statCards.forEach(card => {
            card.addEventListener('touchstart', function () {
                this.style.transform = 'scale(0.95)';
            });

            card.addEventListener('touchend', function () {
                this.style.transform = 'scale(1)';
            });
        });

        // Smooth animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all fade-in elements
        document.querySelectorAll('.fade-in-up').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    });
</script>

<footer class="bg-light py-4 mt-5">
    <div class="container text-center">
        <p class="text-muted mb-0">
            &copy; <?php echo date('Y'); ?> MealApp25 - All rights reserved.
        </p>
    </div>
</footer>

</body>

</html>