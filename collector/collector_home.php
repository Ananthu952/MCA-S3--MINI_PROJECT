<?php include 'collector_manager.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EcoCycle - Collector Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/collectorcss/dashboard.css" />
  <link rel="stylesheet" href="../css/collectorcss/notification.css" />
  <link rel="stylesheet" href="../css/collectorcss/assigned.css" />
  <link rel="stylesheet" href="../css/collectorcss/settings.css" />
  <base href="http://localhost/ecocycle/">
  <style>
    .requests-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      padding: 20px;
    }
    .tab-btn { margin: 0 5px; }
    .hidden { display: none; }
    table th, table td { font-size: 0.9rem; }
    /* Remove fixed positioning */
.fixed-footer {
  position: relative; /* not fixed */
  width: 100vw;       /* full viewport width */
  left: auto;
  bottom: auto;
  margin: 0;          /* remove any margin */
  padding: 0;         /* remove internal padding if not needed */
  box-sizing: border-box; /* ensure padding/border won't add extra width */
}


/* Remove extra bottom margin on body */
body {
  margin-bottom: 0;
}

  </style>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- Navbar -->
<header class="navbar navbar-expand-lg bg-white px-4 py-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="collector/collector_home.php?page=dashboard">
      <img src="images/logo.svg" alt="EcoCycle Logo" class="logo-icon" />
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto me-3">
        <li class="nav-item mx-2"><a class="nav-link" href="collector/collector_home.php?page=dashboard">Dashboard</a></li>
        <li class="nav-item mx-2"><a class="nav-link" href="collector/collector_home.php?page=assigned">Assigned Requests</a></li>
        <li class="nav-item mx-2"><a class="nav-link" href="collector/collector_home.php?page=settings">Settings</a></li>
        <li class="nav-item mx-2">
          <a href="collector/collector_home.php?page=notification" class="notification-btn position-relative btn btn-light">
            <i class="fa-solid fa-bell"></i>
            <span id="notifCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
          </a>
        </li>
      </ul>
      <button class="btn btn-outline-danger" onclick="confirmLogout()">Logout</button>
    </div>
  </div>
</header>

<!-- Dynamic Content -->
<main id="dynamicContent" class="container py-5"></main>

<!-- Footer -->
<footer class="bg-success text-white py-4 mt-auto fixed-footer">
  <div class="container d-flex flex-wrap justify-content-between">
    <div class="footer-left">
      <h4>EcoCycle</h4>
      <p>Collectors play a key role in making recycling successful and sustainable.</p>
    </div>
    <div class="footer-right">
      <ul class="list-unstyled">
        <li><a href="collector/collector_home.php?page=dashboard" class="text-white text-decoration-none">Dashboard</a></li>
        <li><a href="collector/collector_home.php?page=assigned" class="text-white text-decoration-none">Requests</a></li>
        <li><a href="collector/collector_home.php?page=settings" class="text-white text-decoration-none">Settings</a></li>
        <li><a href="collector/logout.php" class="text-white text-decoration-none">Logout</a></li>
      </ul>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// =======================
// SPA AJAX Page Loader
// =======================
function loadPage(page, addToHistory = true) {
  const url = "collector/partials/" + page + ".php"; 
  const content = document.getElementById("dynamicContent");
  content.innerHTML = "<div class='text-center py-5'><div class='spinner-border text-success'></div><p>Loading...</p></div>";

  fetch(url)
    .then(res => {
      if (!res.ok) throw new Error("Page not found");
      return res.text();
    })
    .then(html => {
      content.innerHTML = html;

      // Call page-specific JS initializers (optional stubs)
      if (page === "assigned" && typeof initAssigned === "function") initAssigned();
      if (page === "settings" && typeof initSettingsPage === "function") initSettingsPage();
      if (page === "dashboard" && typeof initDashboard === "function") initDashboard();
      if (page === "notification" && typeof initNotificationPage === "function") initNotificationPage();

      // Update URL
      if (addToHistory) {
        const newURL = "collector/collector_home.php?page=" + page;
        history.pushState({ page: page }, "", newURL);
      }
    })
    .catch(() => {
      content.innerHTML = '<div class="alert alert-danger">⚠ Page not found.</div>';
    });
}

// Handle back/forward browser buttons
window.addEventListener("popstate", (e) => {
  if (e.state && e.state.page) {
    loadPage(e.state.page, false);
  }
});

// Load default page (dashboard) or ?page=...
document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const page = params.get("page") || "dashboard";
  loadPage(page, false);
});

let pickups = []; // global array

// -----------------------------
// Render pickups by status
// -----------------------------
function loadPickups(type, containerId, loaderId, contentId) {
    const loader = document.getElementById(loaderId);
    const content = document.getElementById(contentId);
    const container = document.getElementById(containerId);
    if (!container) return;

    if (loader) loader.style.display = 'none';
    if (content) content.style.display = 'block';

    container.innerHTML = '';

    pickups.forEach(req => {
        if (req.status === type) {
            container.innerHTML += `
            <div class="col-12 mb-3">
                <div class="scrap-request-card" onclick="showPickupDetails('${req.id}')">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h6 class="mb-1 text-muted"><i class="fas fa-calendar me-2"></i>Request Submitted On</h6>
                            <p class="mb-0 fw-bold">${req.date || 'N/A'}</p>
                            <br>
                        </div>
                        <div class="col-md-3">
                            <h6 class="mb-1 text-muted"><i class="fas fa-truck"></i> Scheduled Pickup</h6>
                            <p class="mb-0 fw-bold">${req.pickup_date|| 'N/A'}</p>
                            <small class="mb-0 fw-bold">${req.pickup_slot || 'N/A'}</small>
                        </div>
                        <div class="col-md-3">
                            <h6 class="mb-1 text-muted"><i class="fas fa-user me-2"></i>User</h6>
                            <p class="mb-0 fw-bold">${req.user || 'N/A'}</p>
                            <small class="mb-0 fw-bold">${req.scrapType || 'N/A'}</small>
                        </div>
                        <div class="col-md-3">
                            <h6 class="mb-1 text-muted"><i class="fas fa-map-marker-alt me-2"></i>Location</h6>
                            <p class="mb-0 fw-bold">${req.pincode || 'N/A'}</p>
                            <small class="mb-0 fw-bold">Weight: ${req.weight || 0} kg</small>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="card-actions">
                                <button class="btn btn-view" onclick="event.stopPropagation(); showUserDetails('${req.id}')">
                                    <i class="fas fa-eye me-1"></i>View User
                                </button>
                                ${type === 'pending' ? `<button class="btn btn-success" onclick="event.stopPropagation(); assignPickup('${req.id}', this)">
                                    <i class="fas fa-user-check me-1"></i>Assign
                                </button>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        }
    });
}

// -----------------------------
// Render history table
// -----------------------------
function loadHistory() {
    const loader = document.getElementById('historyLoading');
    const content = document.getElementById('historyContent');
    const table = document.getElementById('historyTable');
    if (!table) return;

    if (loader) loader.style.display = 'none';
    if (content) content.style.display = 'block';

    table.innerHTML = '';

    pickups.forEach(req => {
        table.innerHTML += `
        <tr>
            <td>${req.date || 'N/A'}</td>
            <td><strong>${req.id}</strong></td>
            <td>${req.user || 'N/A'}</td>
            <td>${req.scrapType || 'N/A'}</td>
            <td><span class="status-badge status-${req.status}">${req.status}</span></td>
            <td>
                <button class="btn btn-view btn-sm" onclick="showUserDetails('${req.id}')">
                    <i class="fas fa-eye me-1"></i>View User
                </button>
            </td>
        </tr>`;
    });
}

// -----------------------------
// Initialize dashboard
// -----------------------------
function initDashboard() {
    fetch('/ecocycle/collector/collector_home_manager.php?action=get_dashboard_data')
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') return console.error(data.message);

            pickups = data.data || [];

            // ✅ Update Pending tab count dynamically
            // ✅ Update Pending tab count with styled yellow circle
const pendingTab = document.getElementById('pending-tab');
if (pendingTab && data.pending_count !== undefined) {
    pendingTab.innerHTML = `
        <i class="fas fa-clock me-2"></i>
        Pending 
        <span class="badge rounded-circle bg-warning text-dark ms-1" 
              style="min-width: 24px; height: 24px; display: inline-flex; 
                     align-items: center; justify-content: center; font-size: 0.85rem;">
            ${data.pending_count}
        </span>
    `;
}

            // Load data into each section
            loadPickups('assigned', 'assignedRequests', 'assignedLoading', 'assignedContent');
            loadPickups('pending', 'pendingRequests', 'pendingLoading', 'pendingContent');
            loadPickups('collected', 'completedRequests', 'completedLoading', 'completedContent');
            loadHistory();
        })
        .catch(err => console.error("Dashboard error:", err));
}

  (function fetchCollectorName() {
      const collectorEl = document.getElementById('collectorName');
      if (!collectorEl) return;

      // Check localStorage first
      const storedName = localStorage.getItem('collectorName');
      if (storedName && storedName.trim() !== '') {
          collectorEl.textContent = storedName;
          return;
      }

      fetch('/ecocycle/collector/collector_home_manager.php?action=get_collector_name')
          .then(res => res.json())
          .then(data => {
              if (data.status === 'success' && data.name && data.name.trim() !== '') {
                  collectorEl.textContent = data.name.trim();
                  localStorage.setItem('collectorName', data.name.trim());
              } else {
                  console.warn('Collector name not found in response');
              }
          })
          .catch(err => console.error('Error fetching collector name:', err));
  })();


// -----------------------------
// Show User Details
// -----------------------------
function showUserDetails(requestId) {
    const req = pickups.find(r => r.id == requestId);
    if (!req) return;

    // Format time (hide 00:00:00)
    const requestedDateTime = req.time && req.time !== "00:00:00"
        ? `${req.date} ${req.time}`
        : req.date;

    // Format pickup date and slot
    const pickupSchedule = (req.pickup_date && req.pickup_slot)
        ? `${req.pickup_date} (${req.pickup_slot})`
        : 'Not Scheduled';

    // Fill modal fields
    document.getElementById('modalUserName').textContent = req.user || 'N/A';
    document.getElementById('modalUserPhone').textContent = req.phone || 'N/A';
    document.getElementById('modalUserPincode').textContent = req.pincode || 'N/A';
    document.getElementById('modalRequestId').textContent = req.id;
    document.getElementById('modalScrapType').textContent = req.scrapType || 'N/A';
    document.getElementById('modalWeight').textContent = req.weight || 0;
    document.getElementById('modalStatus').textContent = req.status || 'N/A';
    
    // ✅ Show formatted date/time
    document.getElementById('modalDate').textContent = requestedDateTime;
    document.getElementById('modalTime').textContent = pickupSchedule;

    new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
}


// -----------------------------
// Assign Pickup
// -----------------------------
function assignPickup(requestId, btn) {
    Swal.fire({
        title: 'Assign Pickup',
        text: "Do you want to assign this pickup to yourself?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Assign',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch('/ecocycle/collector/collector_home_manager.php?action=assign_pickup', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request_id: requestId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Assigned!',
                    text: 'Pickup assigned to you.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Redirect to Assigned Requests page
                    window.location.href = '/ecocycle/collector/collector_home.php?page=assigned';
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to assign pickup', 'error');
            }
        })
        .catch(err => Swal.fire('Error', 'Something went wrong!', 'error'));
    });
}

document.addEventListener('DOMContentLoaded', initDashboard);

function initAssigned() {
    let assignedPickups = {};
    let currentPickupId = null;

    const content = document.getElementById('dynamicContent');
    content.innerHTML = `
    <div class="row" id="pickupsContainer"></div>

    <!-- User Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">User Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="userModalBody"></div>
        </div>
      </div>
    </div>

    <!-- Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <select id="statusSelect" class="form-select mb-3">
              <option value="assigned">Assigned</option>
              <option value="cancelled">Cancelled</option>
              <option value="collected">Collected</option>
            </select>
            <textarea id="statusNotes" class="form-control" placeholder="Notes (optional)"></textarea>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success" id="saveStatusBtn">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Weight Modal -->
    <div class="modal fade" id="weightModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-weight me-2"></i>Update Weight & Upload Images</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="weightForm">
              <table class="table">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>User Image</th>
                    <th>Weight (kg)</th>
                    <th>Updated Weight (kg)</th>
                    <th>Collector Image <small class="text-danger">*</small></th>
                  </tr>
                </thead>
                <tbody id="scrapItemsTbody"></tbody>
              </table>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveWeightBtn">Update Weight</button>
          </div>
        </div>
      </div>
    </div>
    `;

    // ---------------------------
    // Fetch assigned pickups
    // ---------------------------
    fetch('/ecocycle/collector/collector_home_manager.php?action=get_assigned_pickups')
      .then(res => res.json())
      .then(data => {
        if (data.status !== 'success') {
          document.getElementById('pickupsContainer').innerHTML = 
            `<div class="text-center text-muted my-5"><i class="fas fa-exclamation-circle fa-2x mb-2"></i><p>${data.message || 'Failed to load pickups'}</p></div>`;
          return;
        }

        assignedPickups = data.data.reduce((map, p) => {
            map[p.request_id] = {
                id: p.request_id,
                user_name: p.user_name || 'N/A',
                phone: p.phone || 'N/A',
                email: p.email || 'N/A',
                address: p.address || 'N/A',
                pincode: p.pincode || 'N/A',
                scrap_details: p.items && p.items.length ? p.items.map(i => i.scrap_name).join(', ') : 'N/A',
                items: p.items || [],
                status: p.status || 'N/A',
                request_date: p.request_date || 'N/A',
                request_time: p.request_time || 'N/A',
                pickupslot: p.pickup_slot || 'N/A',
                pickup_date: p.pickup_date || 'N/A'
            };
            return map;
        }, {});

        loadPickups();
      });

    // ---------------------------
    // Render pickups
    // ---------------------------
    function loadPickups() {
        const container = document.getElementById('pickupsContainer');
        container.innerHTML = '';
        if (!Object.keys(assignedPickups).length) {
            container.innerHTML = `<div class="text-center text-muted my-5"><i class="fas fa-clipboard-list fa-2x mb-2"></i><p>No Assigned Pickups</p></div>`;
            return;
        }
        Object.values(assignedPickups).forEach(p => container.appendChild(createPickupCard(p)));
    }

    function createPickupCard(pickup) {
        const card = document.createElement('div');
        card.className = 'requests-card mb-3';
        card.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h6>${pickup.user_name} (${pickup.scrap_details})</h6>
            <small>${pickup.pickup_date} at ${pickup.pickupslot}</small><br>
            <small>${pickup.address}</small><br>
            <small>Items: ${pickup.scrap_details}</small>
          </div>
          <div class="text-end">
            <span class="status-badge status-${pickup.status}">${pickup.status.replace('-', ' ')}</span><br>
            <button class="btn btn-sm btn-light mt-2" onclick="event.stopPropagation(); showUserDetails('${pickup.id}')"><i class="fas fa-eye"></i></button>
            <button class="btn btn-sm btn-success mt-2" onclick="event.stopPropagation(); showStatusModal('${pickup.id}')"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-warning mt-2" onclick="event.stopPropagation(); showWeightModal('${pickup.id}')"><i class="fas fa-weight"></i></button>
          </div>
        </div>`;
        return card;
    }

    // ---------------------------
    // User Details Modal
    // ---------------------------
    window.showUserDetails = function(pickupId) {
        const pickup = assignedPickups[pickupId];
        if (!pickup) return;
        document.getElementById('userModalBody').innerHTML = `
            <div>
                <h5>${pickup.user_name}</h5>
                <p>${pickup.phone} | ${pickup.email}</p>
                <p>${pickup.address}</p>
                <p>${pickup.pincode}</p>
                <p>Items: ${pickup.scrap_details}</p>
            </div>
        `;
        new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
    };

    // ---------------------------
    // Status Modal
    // ---------------------------
    window.showStatusModal = function(pickupId) {
    currentPickupId = pickupId;
    const pickup = assignedPickups[pickupId];

    const statusSelect = document.getElementById('statusSelect');
    const notes = document.getElementById('statusNotes');
    statusSelect.value = pickup.status;
    notes.value = '';

    // Check dynamically if collected can be allowed
    const allUpdated = pickup.items.every(item => 
        item.collector_weight && item.collector_weight > 0 &&
        item.collector_images && item.collector_images.length > 0
    );

    const collectedOption = statusSelect.querySelector('option[value="collected"]');
    if (collectedOption) {
        collectedOption.disabled = !allUpdated;
        collectedOption.title = allUpdated ? '' : 'Update weight & images first';
    }

    new bootstrap.Modal(document.getElementById('statusModal')).show();
};


// -----------------------------
// Save Status Button
// -----------------------------
document.getElementById('saveStatusBtn').addEventListener('click', () => {
    const newStatus = document.getElementById('statusSelect').value;
    const pickup = assignedPickups[currentPickupId];

    // ✅ Prevent collected if weight/images not updated
    if (newStatus === 'collected') {
        const allUpdated = pickup.items.every(item => 
            item.collector_weight && item.collector_weight > 0 &&
            item.collector_images && item.collector_images.length > 0
        );
        if (!allUpdated) {
            Swal.fire('Error!', 'You must update weight & images for all items before marking as collected.', 'error');
            return;
        }
    }

    fetch('/ecocycle/collector/collector_home_manager.php?action=update_pickup_status', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ request_id: currentPickupId, status: newStatus })
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            assignedPickups[currentPickupId].status = newStatus;
            loadPickups();
            bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
            Swal.fire('Updated!', `Status set to "${newStatus.replace('-', ' ')}"`, 'success');
        } else {
            Swal.fire('Error!', res.message, 'error');
        }
    });
});

    // ---------------------------
    // Weight Modal
    // ---------------------------
    window.showWeightModal = function(pickupId) {
        const pickup = assignedPickups[pickupId];
        if (!pickup || !pickup.items) return;

        currentPickupId = pickupId;
        const tbody = document.getElementById('scrapItemsTbody');
        tbody.innerHTML = '';

        pickup.items.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.scrap_name}</td>
                <td>${item.user_image ? `<img src="/ecocycle/${item.user_image}" width="50">` : 'No image'}</td>
                <td>
                    <input type="number" class="form-control" value="${item.quantity || 0}" step="0.1" min="0" disabled>
                    <small class="text-muted">User Quantity</small>
                </td>
                <td>
                    <input type="number" name="weight[${item.item_id}]" class="form-control" value="${item.collector_weight || item.quantity || 0}" step="0.1" min="0" required>
                    <small class="text-muted">Collector Weight</small>
                </td>
                <td>
                    <input type="file" name="collector_images[${item.item_id}]" class="form-control" required>
                    <small class="text-muted text-danger">Required</small>
                </td>
            `;
            tbody.appendChild(row);
        });

        new bootstrap.Modal(document.getElementById('weightModal')).show();
    };

document.getElementById('saveWeightBtn').addEventListener('click', () => {
    const form = document.getElementById('weightForm');
    const formData = new FormData(form);
    formData.append('pickup_id', currentPickupId);

    // ---------------------------
    // Validate all file inputs
    // ---------------------------
    const fileInputs = form.querySelectorAll('input[type="file"]');
    for (let input of fileInputs) {
        if (!input.files || input.files.length === 0) {
            Swal.fire('Error', 'Please upload images for all items!', 'error');
            return;
        }
    }

    fetch('/ecocycle/collector/collector_home_manager.php?action=update_pickup_weight', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire('Success', 'Collector weight & images updated!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('weightModal')).hide();

            // ✅ Update local data: store weight AND images flag
            assignedPickups[currentPickupId].items.forEach(item => {
                // Update weight
                const weightInput = form.querySelector(`input[name="weight[${item.item_id}]"]`);
                if (weightInput) item.collector_weight = parseFloat(weightInput.value);

                // Update images (store a simple flag to indicate uploaded)
                const fileInput = form.querySelector(`input[name="collector_images[${item.item_id}]"]`);
                if (fileInput && fileInput.files.length > 0) {
                    item.collector_images = Array.from(fileInput.files); // store File objects
                }
            });

        } else {
            Swal.fire('Error', res.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Something went wrong!', 'error');
    });
});

}

function initNotificationPage() {
    const listContainer = document.getElementById('notificationsList');
    const notificationCountEl = document.getElementById('notificationCount');
    const markAllBtn = document.getElementById('markAllBtn');

    let notifications = [];

    // Fetch both notifications and collector register_date
    Promise.all([
        fetch('/ecocycle/collector/collector_home_manager.php?action=get_notifications').then(res => res.json()),
        fetch('/ecocycle/collector/collector_home_manager.php?action=get_register_date').then(res => res.json())
    ])
    .then(([notifData, regData]) => {
        if (notifData.status !== 'success') {
            listContainer.innerHTML = `<div class="text-center text-muted py-3">${notifData.message || 'Failed to load notifications.'}</div>`;
            return;
        }

        const registerDate = regData.register_date ? new Date(regData.register_date) : null;

        // Filter notifications: show only those created after collector’s register_date
        notifications = notifData.data.filter(n => {
            const notifDate = new Date(`${n.date} ${n.time}`);
            return !registerDate || notifDate >= registerDate;
        });

        renderNotifications();
    })
    .catch(err => {
        console.error(err);
        listContainer.innerHTML = `<div class="text-danger text-center py-3">⚠️ Error loading notifications</div>`;
    });

    // ---------------------- Render notifications ----------------------
    function renderNotifications() {
        listContainer.innerHTML = '';
        const unreadCount = notifications.filter(n => n.unread == 1).length;
        notificationCountEl.textContent = unreadCount;

        if (!notifications.length) {
            listContainer.innerHTML = `
                <div class="empty-state text-center py-5">
                    <i class="fas fa-bell-slash fa-2x mb-2"></i>
                    <h5>All caught up!</h5>
                    <p>You have no notifications.</p>
                </div>
            `;
            return;
        }

        notifications.forEach(notif => {
            const item = document.createElement('div');
            item.className = `notification-item position-relative ${notif.unread == 1 ? 'unread' : ''}`;
            item.dataset.id = notif.id;
            item.innerHTML = `
                ${notif.unread == 1 ? '<div class="unread-badge"></div>' : ''}
                <div class="d-flex">
                    <div class="notification-icon ${notif.unread == 1 ? 'icon-success' : 'icon-info'}">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="notification-content flex-grow-1">
                        <h6>${notif.message}</h6>
                        <div class="notification-time"><b>${notif.date+' '+notif.time}</b></div>
                    </div>
                </div>
            `;
            listContainer.appendChild(item);

            // Click to mark single notification as read
            item.addEventListener('click', () => {
                if (notif.unread == 0) return;

                fetch('/ecocycle/collector/collector_home_manager.php?action=mark_notification_read', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${notif.id}`
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        notif.unread = 0;
                        renderNotifications();
                    }
                });
            });
        });
    }

    // ---------------------- Mark all notifications as read ----------------------
    markAllBtn.addEventListener('click', () => {
        const unreadCount = notifications.filter(n => n.unread == 1).length;
        if (unreadCount === 0) return;

        markAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Marking as read...';
        markAllBtn.disabled = true;

        fetch('/ecocycle/collector/collector_home_manager.php?action=mark_all_notifications', { method: 'POST' })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    notifications.forEach(n => n.unread = 0);
                    renderNotifications();
                }
                markAllBtn.innerHTML = '<i class="fas fa-check-double me-2"></i>Mark All as Read';
                markAllBtn.disabled = false;
            });
    });
}

function initSettingsPage() {
  // --------------------------
  // Helpers
  // --------------------------
  function toggleEdit(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.parentElement.querySelector('.edit-btn i');
    const isReadonly = field.hasAttribute('readonly');
    if (isReadonly) {
      field.removeAttribute('readonly');
      field.classList.add('editing');
      field.focus(); field.select();
      button.classList.replace('fa-pen','fa-check');
      button.style.color = '#2e7d32';
    } else {
      field.setAttribute('readonly', true);
      field.classList.remove('editing');
      button.classList.replace('fa-check','fa-pen');
      button.style.color = '';
    }
  }

  function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.parentElement.querySelector('.password-toggle i');
    if (field.type === 'password') {
      field.type = 'text';
      icon.classList.replace('fa-eye','fa-eye-slash');
    } else {
      field.type = 'password';
      icon.classList.replace('fa-eye-slash','fa-eye');
    }
  }

  function showSuccess(message) {
    Swal.fire({ icon:'success', title:'Success', text:message, timer:2000, showConfirmButton:false });
  }

  function showError(message) {
    Swal.fire({ icon:'error', title:'Oops!', text:message });
  }

  function validatePhone(phone) { return /^\d{10}$/.test(phone); }
  function validatePincode(pin) { return /^\d{6}$/.test(pin); }
  function validatePassword(pw) {
    return pw.length >=6 && /[a-z]/.test(pw) && /[A-Z]/.test(pw) && /\d/.test(pw) && /[!@#$%^&*(),.?":{}|<>]/.test(pw);
  }

  function showInlineMessage(input, message, type='error') {
    let indicator = input.parentElement.querySelector('.validation-message');
    if (!indicator) {
      indicator = document.createElement('div');
      indicator.className = 'validation-message';
      indicator.style.fontSize = '0.85rem';
      indicator.style.minHeight = '1.2em';
      input.parentElement.appendChild(indicator);
    }
    indicator.textContent = message;
    indicator.style.color = type==='error' ? '#dc3545' : '#28a745';
  }

  function clearInlineMessage(input) {
    const indicator = input.parentElement.querySelector('.validation-message');
    if (indicator) indicator.textContent = '';
  }

  // --------------------------
  // Load profile info
  // --------------------------
  fetch('/ecocycle/collector/collector_home_manager.php?action=get_profile')
  .then(res => res.json())
  .then(data => {
    if (data?.status === 'success') {
      const d = data.data;
      document.getElementById('username').value = d.name || '';
      document.getElementById('phone').value = d.phone || '';
      document.querySelector('#profileForm input[type="email"]').value = d.email || '';
    } else {
      console.error('Profile load error:', data.message);
    }
  })
  .catch(err => console.error('Fetch error:', err));

  // --------------------------
  // Field validation
  // --------------------------
  const usernameInput = document.getElementById('username');
  const phoneInput = document.getElementById('phone');
  const newPasswordInput = document.getElementById('newPassword');
  const confirmPasswordInput = document.getElementById('confirmPassword');

  usernameInput?.addEventListener('input', ()=> {
    usernameInput.value.trim() === '' 
      ? showInlineMessage(usernameInput, 'Username cannot be empty') 
      : clearInlineMessage(usernameInput);
  });

  phoneInput?.addEventListener('input', ()=> {
    validatePhone(phoneInput.value) ? clearInlineMessage(phoneInput) : showInlineMessage(phoneInput,'Phone number must be 10 digits');
  });

  if (newPasswordInput && confirmPasswordInput) {
    newPasswordInput.addEventListener('input', ()=> {
      validatePassword(newPasswordInput.value) ? clearInlineMessage(newPasswordInput) 
        : showInlineMessage(newPasswordInput,'Password must ≥6 chars & include upper, lower, number & special char');
      if (confirmPasswordInput.value) {
        newPasswordInput.value === confirmPasswordInput.value ? clearInlineMessage(confirmPasswordInput) 
          : showInlineMessage(confirmPasswordInput,'Passwords do not match');
      }
    });
    confirmPasswordInput.addEventListener('input', ()=> {
      newPasswordInput.value === confirmPasswordInput.value ? clearInlineMessage(confirmPasswordInput) 
        : showInlineMessage(confirmPasswordInput,'Passwords do not match');
    });
  }

  // --------------------------
  // Edit & password toggle buttons
  // --------------------------
  document.querySelectorAll('.edit-btn').forEach(btn=>{
    btn.addEventListener('click', ()=> {
      const fieldId = btn.closest('.input-group').querySelector('input,textarea').id;
      toggleEdit(fieldId);
    });
  });

  document.querySelectorAll('.password-toggle').forEach(btn=>{
    btn.addEventListener('click', ()=> {
      const fieldId = btn.closest('.input-group').querySelector('input').id;
      togglePassword(fieldId);
    });
  });

  // --------------------------
  // Form submissions
  // --------------------------
// Profile form
const profileForm = document.getElementById('profileForm');
profileForm?.addEventListener('submit', (e) => {
  e.preventDefault();
  const username = usernameInput.value.trim();
  const phone = phoneInput.value.trim();

  if (!username || !phone) return showError('All fields required');
  if (!validatePhone(phone)) return showError('Phone number must be 10 digits');

  const fd = new FormData();
  fd.append('action', 'update_profile');
  fd.append('username', username);
  fd.append('phone', phone);

  fetch('/ecocycle/collector/collector_home_manager.php', { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => data.status === 'success' ? showSuccess(data.message) : showError(data.message || 'Failed to update profile'))
    .catch(err => showError('Network error: ' + err));
});

// Password form
const passwordForm = document.getElementById('passwordForm');
passwordForm?.addEventListener('submit', (e) => {
  e.preventDefault();
  const current = document.getElementById('currentPassword').value.trim();
  const newPass = newPasswordInput.value.trim();
  const confirmPass = confirmPasswordInput.value.trim();

  if (!current || !newPass || !confirmPass) return showError('All password fields required');
  if (newPass !== confirmPass) return showError('Passwords do not match!');
  if (!validatePassword(newPass)) return showError('Password must ≥6 chars & include upper, lower, number & special char');

  const fd = new FormData();
  fd.append('action', 'change_password');
  fd.append('currentPassword', current);
  fd.append('newPassword', newPass);

  fetch('/ecocycle/collector/collector_home_manager.php', { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        passwordForm.reset();
        confirmPasswordInput.parentElement.querySelector('.validation-message').textContent = '';
        showSuccess(data.message);
      } else {
        showError(data.message || 'Failed to change password');
      }
    })
    .catch(err => showError('Network error: ' + err));
});
}

// =======================
// Logout Confirmation
// =======================
function confirmLogout() {
  Swal.fire({
    title: 'Are you sure?',
    text: "You will be logged out of your account.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, logout',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = 'collector/logout.php';
    }
  });
}
</script>
</body>
</html>

