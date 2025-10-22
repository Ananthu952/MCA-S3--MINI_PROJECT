<div class="container mt-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-1" style="color: var(--primary-dark);">
                <i class="bi bi-bell-fill me-2"></i>Manage Notifications
            </h2>
            <p class="text-muted mb-0">Send messages and alerts to users and collectors</p>
        </div>
    </div>

    <!-- Create Notification Form -->
    <div class="card shadow-lg mb-4">
        <div class="card-header create-notification-card">
            <h4 class="mb-0">
                <i class="bi bi-plus-circle me-2"></i>Create New Notification
            </h4>
        </div>
        <div class="card-body">
            <form id="createNotificationForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-bullseye me-1"></i>Target Audience
                        </label>
                        <select id="notificationTarget" class="form-select" required>
                            <option value="">Select Target Audience</option>
                            <option value="user">👥 Users Only</option>
                            <option value="collector">🚛 Collectors Only</option>
                            <option value="all">📢 All (Broadcast)</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-chat-text me-1"></i>Message Content
                        </label>
                        <textarea id="notificationMessage" class="form-control" rows="4" placeholder="Enter your message here..." required maxlength="500"></textarea>
                        <div class="character-count">
                            <span id="messageCount">0</span>/500 characters
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-send me-2"></i>Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card shadow-lg">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0" style="color: var(--primary-dark);">
                    <i class="bi bi-list-ul me-2"></i>Notifications History
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash me-1"></i>ID</th>
                            <th><i class="bi bi-chat-text me-1"></i>Message</th>
                            <th><i class="bi bi-bullseye me-1"></i>Target</th>
                            <th><i class="bi bi-calendar me-1"></i>Date</th>
                            <th><i class="bi bi-calendar me-1"></i>Status</th>
                        </tr>
                    </thead>
                    <tbody id="notificationsTable">
                        <!-- Notifications will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

