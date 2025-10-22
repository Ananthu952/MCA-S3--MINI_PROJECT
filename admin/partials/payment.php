<div class="container mt-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-1" style="color: var(--primary-dark);">
                <i class="bi bi-credit-card-fill me-2"></i>Payments Management
            </h2>
            <p class="text-muted mb-0">Process and track all payment transactions</p>
        </div>
    </div>
   <div>
    <button class="btn btn-success" onclick="openPaymentHistory()">View Payment History</button>
    </div>
    <!-- Payments Table -->
    <div class="card shadow-lg">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0" style="color: var(--primary-dark);">
                    <i class="bi bi-list-ul me-2"></i>Payment Transactions
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="bi bi-person me-1"></i>User</th>
                            <th><i class="bi bi-person me-1"></i>UPI ID</th>
                            <th><i class="bi bi-hash me-1"></i>Request ID</th>
                            <th><i class="bi bi-currency-rupee me-1"></i>Amount</th>
                            <th><i class="bi bi-calendar me-1"></i>Date</th>
                            <th><i class="bi bi-check-circle me-1"></i>Status</th>
                            <th><i class="bi bi-gear me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="paymentsTable">
                        <!-- Payment rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Payment History Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Payment History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
        <div class="modal-body">
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-striped table-hover align-middle text-center border">
            <thead class="table-success">
                <tr>
                <th scope="col">#</th>
                <th scope="col">User Name</th>
                <th scope="col">Email</th>
                <th scope="col">Request ID</th>
                <th scope="col">Scrap Type</th>
                <th scope="col">Weight (kg)</th>
                <th scope="col">Amount (₹)</th>
                <th scope="col">Status</th>
                <th scope="col">Payment Date</th>
                </tr>
            </thead>
            <tbody id="paymentHistoryTable" class="table-group-divider"></tbody>
            </table>
        </div>
        </div>
    </div>
  </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="bi bi-credit-card me-2"></i>Payment Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- User Information -->
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">User Information</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <div id="modalUserAvatar" class="user-avatar me-2"></div>
                                    <div>
                                        <strong id="modalUserName"></strong>
                                        <div class="text-muted" id="modalUserEmail"></div>
                                        <div class="text-muted"><strong>UPI ID:</strong> <span id="modalUpiId">-</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Payment Information</h6>
                                <p class="mb-1"><strong>Request ID:</strong> <span id="modalRequestId"></span></p>
                                <p class="mb-1"><strong>Scrap Details:</strong> <span id="modalScrapType"></span></p>
                                <p class="mb-1"><strong>Amount:</strong> <span id="modalAmount" class="amount-display text-success"></span></p>
                                <p class="mb-1"><strong>Date:</strong> <span id="modalDate"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details Table -->
                <div class="mt-3">
                    <h6>Transaction Details</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Scrap Type:</strong></td>
                                <td id="modalScrapTypeTable"></td>
                            </tr>
                            <tr>
                                <td><strong>Total Weight:</strong></td>
                                <td id="modalWeight"></td>
                            </tr>
                            <tr>
                                <td><strong>Collection Date:</strong></td>
                                <td id="modalCollectionDate"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

