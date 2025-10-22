<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1><i class="bi bi-people-fill me-3"></i>User Management</h1>
                <p>Manage and monitor all registered users</p>
            </div>
        </div>
    </div>

    <!-- Controls Section -->
    <div class="controls-section">
        <div class="row align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    <i class="bi bi-search me-1"></i>Search Users
                </label>
                <div class="search-box d-flex align-items-center w-100">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, or phone...">
                    <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus me-1"></i> Add User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Table -->
    <div class="table-container mt-3">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Pincode</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <!-- Users will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination" id="pagination"></ul>
    </nav>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-circle me-2"></i>User Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- User details populated by JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Add New User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="mb-3">
                        <label for="newUserName" class="form-label fw-semibold">
                            <i class="bi bi-person me-1"></i>Full Name
                        </label>
                        <input type="text" class="form-control" id="newUserName" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserEmail" class="form-label fw-semibold">
                            <i class="bi bi-envelope me-1"></i>Email Address
                        </label>
                        <input type="email" class="form-control" id="newUserEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserPhone" class="form-label fw-semibold">
                            <i class="bi bi-telephone me-1"></i>Phone Number
                        </label>
                        <input type="tel" class="form-control" id="newUserPhone" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserAddress" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt me-1"></i>Address
                        </label>
                        <input type="text" class="form-control" id="newUserAddress" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserPincode" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt me-1"></i>Pincode
                        </label>
                        <input type="text" class="form-control" id="newUserPincode" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserPassword" class="form-label fw-semibold">
                            <i class="bi bi-lock me-1"></i>Password
                        </label>
                        <input type="text" class="form-control" id="newUserPassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveNewUser">
                    <i class="bi bi-check-circle me-1"></i>Add User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notify User Modal -->
<div class="modal fade" id="notifyUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-bell me-2"></i>Send Notification
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="notifyUserForm">
          <input type="hidden" id="notifyUserId">
          <div class="mb-3">
            <label for="notifyMessage" class="form-label fw-semibold">
              <i class="bi bi-chat-dots me-1"></i>Message
            </label>
            <textarea class="form-control" id="notifyMessage" rows="4" placeholder="Enter your notification message..."></textarea>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="sendNotification">
          <i class="bi bi-send me-1"></i>Send
        </button>
      </div>

    </div>
  </div>
</div>
