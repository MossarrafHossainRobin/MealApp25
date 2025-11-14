<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center bg-white p-4 rounded shadow">
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 mb-0">Updating data, please wait...</p>
    </div>
</div>

<!-- Notifications -->
<?php if ($success_message): ?>
    <div class="notification-toast">
        <div class="toast show align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body"><?php echo htmlspecialchars($success_message); ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="notification-toast">
        <div class="toast show align-items-center text-white bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body"><?php echo htmlspecialchars($error_message); ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
<?php endif; ?>