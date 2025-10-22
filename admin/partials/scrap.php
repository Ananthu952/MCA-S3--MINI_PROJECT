<body>
<div class="page-block">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2><i class="bi bi-currency-rupee me-2"></i>Scrap Prices</h2>
                <p>Control daily scrap prices shown to users</p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section mb-3">
        <label class="form-label fw-semibold">
            <i class="bi bi-search me-1"></i>Search
        </label>
        <input type="text" id="searchCategories" class="form-control" placeholder="Search categories...">
    </div>

    <!-- Table & Add Button -->
    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h3><i class="bi bi-table me-2"></i>Scrap Categories</h3>
            <button class="btn btn-success" id="addNewScrap">
                <i class="bi bi-plus-circle me-1"></i>Add New Scrap Type
            </button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Current Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="priceTable"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add New Scrap Type Modal -->
<div class="modal fade" id="addScrapModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-plus-circle me-2"></i>Add New Scrap Type
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addScrapForm">
          <div class="mb-3">
            <label for="newScrapName" class="form-label fw-semibold">
              <i class="bi bi-tag me-1"></i>Category Name
            </label>
            <input type="text" class="form-control" id="newScrapName" placeholder="e.g., Glass, Cardboard" required>
          </div>
          <div class="mb-3">
            <label for="newScrapUnit" class="form-label fw-semibold">
              <i class="bi bi-box-seam me-1"></i>Unit
            </label>
            <select class="form-select" id="newScrapUnit" required>
              <option value="">Select Unit</option>
              <option value="Kg">Kg</option>
              <option value="Piece">Piece</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="newScrapPrice" class="form-label fw-semibold">
              <i class="bi bi-currency-rupee me-1"></i>Price per Unit
            </label>
            <input type="number" class="form-control" id="newScrapPrice" step="0.01" min="0" placeholder="0.00" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </button>
        <button type="button" class="btn btn-success" id="saveNewScrap">
          <i class="bi bi-check-circle me-1"></i>Add Category
        </button>
      </div>
    </div>
  </div>
</div>
