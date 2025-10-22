<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1" style="color: var(--primary-dark);">
                <i class="bi bi-people-fill me-2"></i>Manage Collectors
            </h2>
            <p class="text-muted mb-0">Add and manage waste collection agents</p>
        </div>
        <button id="addCollectorBtn" class="btn btn-success btn-lg">
            <i class="bi bi-plus-circle me-2"></i>Add Collector
        </button>
    </div>

    <!-- Add Collector Form (hidden by default) -->
    <div id="addCollectorForm" class="card shadow-lg p-4 mb-4" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0" style="color: var(--primary-dark);">
                <i class="bi bi-person-plus me-2"></i>Add New Collector
            </h4>
            <button type="button" id="cancelAdd" class="btn-close"></button>
        </div>
        <form id="collectorForm">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-person me-1"></i>Full Name
                    </label>
                    <input type="text" class="form-control" name="name" placeholder="Enter full name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-envelope me-1"></i>Email Address
                    </label>
                    <input type="email" class="form-control" name="email" placeholder="Enter email" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-geo-alt me-1"></i>Collection Area
                    </label>
                    <input type="text" class="form-control" name="area" placeholder="e.g., Kottayam, Kochi" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-key me-1"></i>Password
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="password" placeholder="Auto-generated password" id="passwordField">
                        <button type="button" class="btn btn-outline-secondary" id="generatePassword">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>Password will be sent to collector via email
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Save & Send Email
                </button>
                <button type="button" id="cancelAddBtn" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- Collectors Table -->
    <div class="card shadow-lg">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0" style="color: var(--primary-dark);">
                    <i class="bi bi-table me-2"></i>Collectors List
                </h5>
                <span class="badge bg-light text-dark fs-6" id="collectorCount">2 collectors</span>
            </div>
            
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="collectorSearch" class="form-control" placeholder="Search collectors by name, email, or area...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash me-1"></i>ID</th>
                            <th><i class="bi bi-person me-1"></i>Name</th>
                            <th><i class="bi bi-envelope me-1"></i>Email</th>
                            <th><i class="bi bi-geo-alt me-1"></i>Area</th>
                            <th><i class="bi bi-gear me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="collectorTable">
                        <tr class="collector-row">
                            <td class="fw-semibold">001</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <span class="text-white fw-bold">JM</span>
                                    </div>
                                    John Mathew
                                </div>
                            </td>
                            <td>john@example.com</td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-geo-alt-fill me-1"></i>Kochi
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-ban"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" title="remove">
                                        <i class="bi bi-bell"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="collector-row">
                            <td class="fw-semibold">002</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <span class="text-white fw-bold">AR</span>
                                    </div>
                                    Akhil Raj
                                </div>
                            </td>
                            <td>akhil@example.com</td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-geo-alt-fill me-1"></i>Kottayam
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-success" title="Unblock Collector">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" title="Send Notification">
                                        <i class="bi bi-bell"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>