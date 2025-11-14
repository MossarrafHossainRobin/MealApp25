<div class="container mt-4">
    <h2 class="text-success mb-4">Member Management</h2>
    
    <!-- Add Member Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0"><i class="bi bi-person-plus"></i> Add New Member</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="process/actions.php" onsubmit="showLoading()">
                <input type="hidden" name="action" value="add_member">
                <input type="hidden" name="current_section" value="members">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Member Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter member name" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-person-plus"></i> Add Member
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Members List Card -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0"><i class="bi bi-people"></i> All Members</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Joined Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_members as $member): ?>
                                    <?php $is_present = in_array($member['id'], array_column($present_members, 'id')); ?>
                                <tr>
                                    <td><?php echo $member['id']; ?></td>
                                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($member['created_at'])); ?></td>
                                    <td>
                                        <?php if ($is_present): ?>
                                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Present</span>
                                        <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Absent</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="process/actions.php" onsubmit="showLoading()" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_member">
                                            <input type="hidden" name="current_section" value="members">
                                            <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($member['name']); ?>? This will also delete all their records.')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>