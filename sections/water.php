<div class="container mt-4">
    <h2 class="text-success mb-4">Water Duty Management</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-success">Give Water</h5>
                    <form method="POST" action="process/actions.php" onsubmit="showLoading()">
                        <input type="hidden" name="action" value="give_water">
                        <input type="hidden" name="current_section" value="water">
                        <div class="mb-3">
                            <label class="form-label">Select Member Giving Water</label>
                            <select name="member_id" class="form-select" required>
                                <option value="">Choose member...</option>
                                <?php foreach ($present_members as $member): ?>
                                        <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                                <?php endforeach; ?>
                                </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-droplet-half"></i> Mark Water Given
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-body">
                    <h5 class="card-title text-success">Present Members</h5>
                    <p class="text-muted small mb-3">Select members who are currently present:</p>
                    <form method="POST" id="presentMembersForm" action="process/actions.php" onsubmit="showLoading()">
                        <input type="hidden" name="action" value="update_present_members">
                        <input type="hidden" name="current_section" value="water">
                        <?php foreach ($all_members as $member): ?>
                                <?php $is_present = in_array($member['id'], array_column($present_members, 'id')); ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input water-check" 
                                           type="checkbox" 
                                           name="member_ids[]" 
                                           value="<?php echo $member['id']; ?>"
                                           id="member-<?php echo $member['id']; ?>"
                                           <?php echo $is_present ? 'checked' : ''; ?>>
                                    <label class="form-check-label ms-2" for="member-<?php echo $member['id']; ?>">
                                        <?php echo htmlspecialchars($member['name']); ?>
                                    </label>
                                </div>
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-outline-success w-100 mt-3">
                            <i class="bi bi-check-circle"></i> Update Present Members
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-success">Water Duty History</h5>
                    <div class="mt-3">
                        <?php if (!empty($water_history)): ?>
                                <?php foreach ($water_history as $record): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                            <div>
                                                <i class="bi bi-droplet text-primary me-2"></i>
                                                <strong><?php echo htmlspecialchars($record['member_name']); ?></strong>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo date('M j, Y g:i A', strtotime($record['duty_date'])); ?>
                                            </small>
                                        </div>
                                <?php endforeach; ?>
                        <?php else: ?>
                                <p class="text-muted">No water duty history yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-body">
                    <h5 class="card-title text-success">Last Water Given</h5>
                    <div class="text-center py-4">
                        <?php if ($last_water): ?>
                                <div class="text-success">
                                    <i class="bi bi-droplet-fill display-4"></i>
                                    <h4 class="mt-2"><?php echo htmlspecialchars($last_water['name']); ?></h4>
                                    <p class="text-muted mb-0">Last gave water</p>
                                </div>
                        <?php else: ?>
                                <div class="text-muted">
                                    <i class="bi bi-droplet display-4"></i>
                                    <p class="mt-2">No water duty recorded yet</p>
                                </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>