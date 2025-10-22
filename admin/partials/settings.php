<div class="settings-container d-flex justify-content-center">
    <div class="col-lg-6">
        <div class="card settings-card">
            <div class="card-header">
                <h5><i class="fas fa-lock me-2"></i>Change Password</h5>
            </div>
            <div class="card-body">
                <form id="passwordForm">
                    <!-- Current Password -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password">
                            <span class="input-group-text">
                                <button type="button" class="password-toggle" onclick="togglePassword('currentPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newPassword" placeholder="Enter new password">
                            <span class="input-group-text">
                                <button type="button" class="password-toggle" onclick="togglePassword('newPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password">
                            <span class="input-group-text">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </span>
                            <div class="match-indicator" id="matchIndicator"></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-key me-2"></i>Save Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Alert -->
<div class="alert alert-success alert-dismissible fade" role="alert" id="successAlert">
    <i class="fas fa-check-circle me-2"></i>
    <span id="successMessage">Changes saved successfully!</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
