  <div class="page-container">
    <div class="page-header">
      <h1><i class="fas fa-clipboard-list me-3"></i>Assigned Pickups</h1>
      <p class="mb-0 mt-2 opacity-75">Manage your assigned pickup requests</p>
    </div>

    <div id="pickupsContainer">
      <!-- Pickup cards will be loaded here -->
    </div>
  </div>

  <!-- User Details Modal -->
  <div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-user me-2"></i>User Details</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="userModalBody">
          <!-- Dynamic content -->
        </div>
      </div>
    </div>
  </div>

  <!-- Update Status Modal -->
  <div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Update Status</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="statusForm">
            <div class="mb-3">
              <label for="statusSelect" class="form-label">Select New Status</label>
              <select class="form-select" id="statusSelect" required>
                <option value="">Choose status...</option>
                <option value="assigned">Assigned</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="statusNotes" class="form-label">Notes (Optional)</label>
              <textarea class="form-control" id="statusNotes" rows="3" placeholder="Add any notes about this status change..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="saveStatusBtn">Update Status</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Update Weight Modal -->
  <div class="modal fade" id="weightModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-weight me-2"></i>Update Weight & Upload Images</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
  <div class="modal-body" id="weightModalBody"></div>
          <form id="weightForm">
            <div class="table-responsive">
              <table class="table table-bordered" id="scrapItemsTable">
                <thead>
                  <tr>
                    <th>Scrap Item</th>
                    <th>Current Quantity (kg)</th>
                    <th>New Quantity (kg)</th>
                    <th>User Image</th>
                    <th>Collector Image <small class="text-danger">*</small></th>
                  </tr>
                </thead>
                <tbody>
                  <!-- JS will populate rows here -->
                </tbody>
              </table>
            </div>
            <div class="mb-3">
              <label for="weightNotes" class="form-label">Notes (Optional)</label>
              <textarea class="form-control" id="weightNotes" rows="2" placeholder="Add any notes about the weight measurement..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="saveWeightBtn">Update Weight</button>
        </div>
      </div>
    </div>
  </div>
