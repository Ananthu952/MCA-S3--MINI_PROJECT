<main class="container my-4 my-md-5">
  <div class="cc-card">
    <div class="cc-card-head">
      <h2 class="h4 mb-1">Scrap Pickup Request</h2>
      <div class="text-sub">Share your details and we’ll handle the rest.</div>
    </div>

    <div class="cc-card-body">
      <form id="scrapRequestForm" enctype="multipart/form-data">
        <!-- Pickup details -->
        <div class="col-12">
          <label for="pickupAddress" class="form-label mb-1">Pickup Address & Pincode</label>
          <input type="text" id="pickupAddress" name="address" class="form-control" placeholder="Your address with pincode" required readonly>
        </div>
        <div class="row g-3 align-items-end mt-3">
          <div class="col-md-4">
            <label for="pickup_date" class="form-label mb-1">Preferred Pickup Date</label>
            <input type="date" id="pickup_date" name="pickup_date" class="form-control" required />
          </div>
        <div class="col-md-8">
          <label for="pickup_slot" class="form-label mb-1">Preferred Time Slot</label>
            <select id="pickup_slot" name="pickup_slot" class="form-select" required>
              <option value="">Select a slot</option>
              <option value="9AM - 11AM">9AM - 11AM</option>
              <option value="11AM - 1PM">11AM - 1PM</option>
              <option value="2PM - 4PM">2PM - 4PM</option>
              <option value="4PM - 6PM">4PM - 6PM</option>
            </select>
        </div>
      </div>
        <!-- Scrap items -->
        <div class="mt-4 p-3 p-md-4 border rounded-3" style="border-color: var(--brand-line)!important;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="h6 mb-0">Scrap Items</h3>
            <button type="button" id="addScrapBtn" class="btn btn-green">
              <i class="fa-solid fa-plus me-2"></i> Add More Scrap
            </button>
          </div>

          <div id="scrapItemsContainer" class="d-grid gap-3">
            <div class="scrap-item scrap-row">
              <div>
                <label class="form-label mb-1 small text-sub">Type</label>
                <select name="scrap_type[]" class="form-select scrap-type" required>
                  <option value="">Loading types...</option>
                </select>
              </div>
              <div>
                <label class="form-label mb-1 small text-sub">Quantity (Kg or Count)</label>
                <input type="number" name="quantity[]" min="1" step="0.01" placeholder="e.g., 5" class="form-control" />
              </div>
              <div class="d-flex">
                <button type="button" class="btn btn-danger w-100 delete-btn">
                  <i class="fa-solid fa-trash me-2"></i> Delete
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Upload -->
        <div class="d-flex align-items-center gap-3 mt-4">
          <label for="scrap_images" class="upload-label">
            <i class="fa-regular fa-image"></i>
            <span>Upload photos</span>
          </label>
          <input type="file" name="scrap_images[]" id="scrap_images" multiple accept="image/*"/>
          <span id="fileCount" class="small text-sub">No files selected</span>
        </div>

        <!-- Submit -->
        <div class="pt-3 text-center">
          <button type="submit" class="btn btn-yellow submit-btn">
            <i class="fa-solid fa-paper-plane me-2"></i> Submit Request
          </button>
        </div>

        <p id="responseMsg" class="small mt-2 text-center"></p>
        <p class="small text-sub mb-0 text-center">
          Addresses and scrap types load from your server, and requests submit to your endpoint.
        </p>
      </form>
    </div>
  </div>
</main>

