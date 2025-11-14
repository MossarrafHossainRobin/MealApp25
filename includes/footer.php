<script>
    // Navigation function
    function showSection(section) {
        // Hide all sections
        document.querySelectorAll('.section').forEach(s => s.style.display = 'none');

        // Remove active class from all nav links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });

        // Add active class to clicked nav link
        event.target.classList.add('active');

        // Show selected section
        document.getElementById(section).style.display = 'block';

        // Auto-hide notifications after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.notification-toast .toast').forEach(toast => {
                toast.classList.remove('show');
            });
        }, 5000);
    }

    // Update meal totals
    function updateMealTotal(input) {
        const row = input.parentElement.parentElement;
        const totalCell = row.querySelector('td:last-child');
        const inputs = row.querySelectorAll('.meal-input');

        let total = 0;
        inputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });

        totalCell.textContent = total;
    }

    // Show loading overlay
    function showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    // Initialize chart on home page
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('monthlyBazarChart').getContext('2d');
        const members = <?php echo json_encode(array_column($bazar_summary, 'member')); ?>;
        const amounts = <?php echo json_encode(array_column($bazar_summary, 'bazarSpent')); ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: members,
                datasets: [{
                    label: 'Total Bazar Contribution (BDT)',
                    data: amounts,
                    backgroundColor: 'rgba(22,163,74,0.7)',
                    borderColor: 'rgba(22,163,74,1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Member Bazar Contribution', font: { size: 16 } },
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Amount (BDT)' }
                    }
                }
            }
        });
    });
</script>
</body>

</html>