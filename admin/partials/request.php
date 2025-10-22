<div class="container mt-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-1" style="color: var(--primary-dark);">
                <i class="bi bi-recycle me-2"></i>Manage Scrap Requests
            </h2>
            <p class="text-muted mb-0">Monitor and manage all scrap collection requests</p>
        </div>
        <div class="col-md-4">
            <div class="stats-card p-3 text-center">
                <h4 class="mb-1" id="totalRequests">24</h4>
                <small>Active Requests</small>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5 class="text-warning mb-1" id="pendingRequests">8</h5>
                <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>Pending
                </small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5 class="text-info mb-1" id="acceptedRequests">12</h5>
                <small class="text-muted">
                    <i class="bi bi-check-circle me-1"></i>Accepted
                </small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5 class="text-success mb-1" id="completedRequests">15</h5>
                <small class="text-muted">
                    <i class="bi bi-check-all me-1"></i>Completed
                </small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h5 class="text-primary mb-1" id="todayRequests">6</h5>
                <small class="text-muted">
                    <i class="bi bi-calendar-day me-1"></i>Today
                </small>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="searchRequests" class="form-control" placeholder="Search by user, ID, or location...">
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card shadow-lg">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0" style="color: var(--primary-dark);">
                    <i class="bi bi-list-ul me-2"></i>Scrap Collection Requests
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash me-1"></i>Request ID</th>
                            <th><i class="bi bi-person me-1"></i>User</th>
                            <th><i class="bi bi-truck me-1"></i>Collector</th>
                            <th><i class="bi bi-recycle me-1"></i>Scrap Details</th>
                            <th><i class="bi bi-geo-alt me-1"></i>Location</th>
                            <th><i class="bi bi-check-circle me-1"></i>Status</th>
                            <th><i class="bi bi-gear me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requestTable">
                        <!-- Request rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">
                    <i class="bi bi-eye me-2"></i>Request Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-circle me-1"></i>Request Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Request ID:</strong> <span id="modalRequestId"></span></p>
                                        <p><strong>User:</strong> <span id="modalUser"></span></p>
                                        <p><strong>Phone:</strong> <span id="modalPhone"></span></p>
                                        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Collector:</strong> <span id="modalCollector"></span></p>
                                        <p><strong>Scrap Type:</strong> <span id="modalScrapType"></span></p>
                                        <p><strong>Estimated Weight:</strong> <span id="modalWeight"></span></p>
                                        <p><strong>Estimated Value:</strong> <span id="modalValue"></span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <p><strong>Address:</strong> <span id="modalAddress"></span></p>
                                        <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-images me-1"></i>Scrap Images
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="modalImages" class="image-gallery">
                                    <!-- Images will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-clock-history me-1"></i>Request Timeline
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="modalTimeline" class="timeline">
                                    <!-- Timeline will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-gear me-1"></i>Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button id="modalReassignBtn" class="btn btn-warning">
                                        <i class="bi bi-arrow-repeat me-1"></i>Reassign Collector
                                    </button>
                                    <button id="modalCancelBtn" class="btn btn-danger">
                                        <i class="bi bi-x-circle me-1"></i>Cancel Request
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reassign Modal -->
<div class="modal fade" id="reassignModal" tabindex="-1" aria-labelledby="reassignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reassignModalLabel">
                    <i class="bi bi-arrow-repeat me-2"></i>Reassign Request
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="newCollector" class="form-label fw-semibold">
                        <i class="bi bi-person me-1"></i>Select New Collector
                    </label>
                    <select id="newCollector" class="form-select">
                        <option value="">Choose a collector</option>
                        <option value="collector-a">Collector A - Available (5 active requests)</option>
                        <option value="collector-b">Collector B - Available (3 active requests)</option>
                        <option value="collector-c">Collector C - Busy (8 active requests)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" id="confirmReassign" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Reassign Request
                </button>
            </div>
        </div>
    </div>
</div>
