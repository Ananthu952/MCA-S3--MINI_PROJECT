<!-- Welcome Section -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: 10vh;">
  <div class="welcome-section text-center">
    <h1>Welcome <span id="collectorName">Collector</span>! 👋</h1>
    <p>
      Manage your assigned pickups efficiently and help keep the environment clean.<br>
      EcoCycle values your efforts in recycling and sustainability.
    </p>
    <a href="collector/collector_home.php?page=assigned" class="btn btn-success text-white mt-3">
      View Assigned Pickups
    </a>
  </div>
</div>

<div class="dashboard-container">
<!-- Dashboard Header -->
<div class="dashboard-header">
<h1><i class="fas fa-truck me-3"></i>Collector Dashboard</h1>
</div>

<!-- Navigation Tabs -->
<ul class="nav nav-tabs" id="collectorTabs" role="tablist">
<li class="nav-item" role="presentation">
<button class="nav-link active" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assigned" type="button" role="tab">
<i class="fas fa-clipboard-list me-2"></i>Assigned
</button>
</li>
<li class="nav-item" role="presentation">
<button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
<i class="fas fa-clock me-2"></i>Pending
</button>
</li>
<li class="nav-item" role="presentation">
<button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
<i class="fas fa-check-circle me-2"></i>Completed
</button>
</li>
<li class="nav-item" role="presentation">
<button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
<i class="fas fa-history me-2"></i>History
</button>
</li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="collectorTabContent">
<!-- Assigned Pickups -->
<div class="tab-pane fade show active" id="assigned" role="tabpanel">
<div class="loading-spinner" id="assignedLoading">
<div class="spinner-border text-success" role="status">
<span class="visually-hidden">Loading...</span>
</div>
<p class="mt-3">Loading assigned pickups...</p>
</div>

<div id="assignedContent" class="fade-in" style="display:none;">
<div class="row" id="assignedRequests">
<!-- Assigned request cards will be loaded here -->
</div>
</div>
</div>

<!-- Pending Pickups -->
<div class="tab-pane fade" id="pending" role="tabpanel">
<div class="loading-spinner" id="pendingLoading">
<div class="spinner-border text-warning" role="status">
<span class="visually-hidden">Loading...</span>
</div>
<p class="mt-3">Loading pending pickups...</p>
</div>

<div id="pendingContent" class="fade-in" style="display:none;">
<div class="row" id="pendingRequests"></div>
</div>
</div>

<!-- Completed Pickups -->
<div class="tab-pane fade" id="completed" role="tabpanel">
<div class="loading-spinner" id="completedLoading">
<div class="spinner-border text-primary" role="status">
<span class="visually-hidden">Loading...</span>
</div>
<p class="mt-3">Loading completed pickups...</p>
</div>

<div id="completedContent" class="fade-in" style="display:none;">
<div class="row" id="completedRequests"></div>
</div>
</div>

<!-- History -->
<div class="tab-pane fade" id="history" role="tabpanel">
<div class="loading-spinner" id="historyLoading">
<div class="spinner-border text-secondary" role="status">
<span class="visually-hidden">Loading...</span>
</div>
<p class="mt-3">Loading history...</p>
</div>

<div id="historyContent" class="fade-in" style="display:none;">
<div class="table-responsive">
<table class="table table-hover">
<thead>
<tr>
<th>Date</th>
<th>Request ID</th>
<th>User</th>
<th>Scrap Type</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody id="historyTable"></tbody>
</table>
</div>
</div>
</div>
</div>
</div>

<!-- Request Details Modal -->
<div class="modal fade" id="pickupDetailsModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Pickup Request Details</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body" id="pickupModalBody">
<!-- Dynamic content -->
</div>
</div>
</div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-user me-2"></i>User Detai           ls</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Name:</strong> <span id="modalUserName"></span></p>
        <p><strong>Phone:</strong> <span id="modalUserPhone"></span></p>
        <p><strong>Pincode:</strong> <span id="modalUserPincode"></span></p>
        <p><strong>Request ID:</strong> <span id="modalRequestId"></span></p>
        <p><strong>Scrap Type:</strong> <span id="modalScrapType"></span></p>
        <p><strong>Weight:</strong> <span id="modalWeight"></span> kg</p>
        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
        <p><strong>Request Submitted On</strong> <span id="modalDate"></span></p>
        <p><strong>Scheduled Pickup</strong> <span id="modalTime"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


