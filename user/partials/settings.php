
<div class="settings-container">
    <h1 class="page-title">
        <i class="fas fa-cog me-3"></i>User Settings
    </h1>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-6">
            <div class="card settings-card">
                <div class="card-header">
                    <h5><i class="fas fa-user me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="username" value="john_doe_2025" readonly>
                                <span class="input-group-text">
                                    <button type="button" class="edit-btn" onclick="toggleEdit('username')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </span>
                            </div>
                        </div>

                        <!-- Email (Non-editable) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <input type="email" class="form-control" value="john.doe@example.com" readonly>
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Address</label>
                            <div class="input-group">
                                <textarea class="form-control" id="address" rows="3" readonly>123 Innovation Street, Tech District, Silicon Valley</textarea>
                                <span class="input-group-text align-items-start pt-3">
                                    <button type="button" class="edit-btn" onclick="toggleEdit('address')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </span>
                            </div>
                        </div>

                        <!-- Pincode -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pincode</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="pincode" value="94025" readonly>
                                <span class="input-group-text">
                                    <button type="button" class="edit-btn" onclick="toggleEdit('pincode')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </span>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <div class="input-group">
                                <input type="tel" class="form-control" id="phone" value="+1 (555) 123-4567" readonly>
                                <span class="input-group-text">
                                    <button type="button" class="edit-btn" onclick="toggleEdit('phone')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Change -->
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
</div>
