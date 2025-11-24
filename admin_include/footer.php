</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mobile navigation active state
    document.addEventListener('DOMContentLoaded', function () {
        // Add active class to current section
        const currentSection = '<?php echo $current_section; ?>';
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            if (link.getAttribute('href') === `?section=${currentSection}`) {
                link.classList.add('active');
            }
        });

        // Handle logout confirmation
        const logoutLinks = document.querySelectorAll('a[href*="logout"]');
        logoutLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                if (!confirm('Are you sure you want to logout?')) {
                    e.preventDefault();
                }
            });
        });
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        // Simple toast implementation
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
        toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 4000);
    }

    // AJAX helper function
    async function makeRequest(action, data = {}) {
        try {
            const formData = new FormData();
            formData.append('ajax_action', action);
            for (const key in data) {
                formData.append(key, data[key]);
            }

            const response = await fetch('', {
                method: 'POST',
                body: formData
            });

            return await response.json();
        } catch (error) {
            console.error('Request failed:', error);
            return { status: 'error', message: 'Network error occurred' };
        }
    }
</script>
</body>

</html>