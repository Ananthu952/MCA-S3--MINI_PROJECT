<div class="container d-flex justify-content-center align-items-center" style="min-height: 10vh;">
    <div class="welcome-section text-center">
        <h1>Welcome <span id="userName">User</span>! 👋</h1>
        <p>
            Request a pickup for your scrap materials and contribute to a cleaner environment.<br>
            EcoCycle makes scrap collection simple and rewarding.
        </p>
        <!-- Button added -->
<a href="user/homes.php?page=scraprequest"  class="btn btn-warning text-white mt-3" >
            Request Scrap Pickup
        </a>
    </div>
</div>

<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1><i class="fas fa-tachometer-alt me-3"></i>Dashboard</h1>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests" type="button" role="tab">
                Requests
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="wallet-tab" data-bs-toggle="tab" data-bs-target="#wallet" type="button" role="tab">
                Wallet
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="dashboardTabContent">
        <!-- Requests Section -->
    <div class="tab-pane fade show active" id="requests" role="tabpanel">
        <div class="loading-spinner" id="requestsLoading">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading requests...</p>
        </div>

        <div id="requestsContent" class="fade-in">
            <!-- Make it a flex column so each request is a full row -->
            <div class="d-flex flex-column gap-3" id="scrapRequests">
                <!-- Scrap Request Rows will be injected here by initDashboard() -->
            </div>
        </div>
    </div>
<!-- Payment Section -->
<div class="tab-pane fade" id="wallet" role="tabpanel">
    <div class="loading-spinner" id="walletLoading">
        <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Loading payment details...</p>
    </div>

    <div id="walletContent" class="fade-in" style="display: none;">
        <!-- UPI Setup Section -->
        <div class="row mb-5">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm border-0 p-4">
                    <h4 class="mb-3 text-success"><i class="fas fa-university me-2"></i>Setup Your UPI ID</h4>
                    <p class="text-muted mb-4">
                        Add your UPI ID to receive payments directly from EcoCycle. Please ensure your UPI ID is valid (example: <b>username@okaxis</b> or <b>mobile@upi</b>).
                    </p>

                    <!-- Wrapper for conditional UPI display -->
                    <div id="upiContainer">
                        <!-- JS will inject first-time input form or existing UPI + change button here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 p-4">
                    <h5 class="mb-4 text-start text-success"><i class="fas fa-history me-2"></i>Recent Transactions</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th>Date</th>
                                    <th>Request ID</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="walletTransactions">
                                <!-- Transactions will be loaded here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>

<!-- Request Details Modal -->
<div class="modal fade" id="requestDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Request Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Content will be injected here -->
      </div>
    </div>
  </div>
</div>

