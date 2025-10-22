<?php include 'admin_manager.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>EcoCycle - Admin Home</title>

<!-- Bootstrap & Font Awesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- Custom CSS -->
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/admincss/dashboard.css" />
<link rel="stylesheet" href="../css/admincss/user.css" />
<link rel="stylesheet" href="../css/admincss/collector.css" />
<link rel="stylesheet" href="../css/admincss/request.css" />
<link rel="stylesheet" href="../css/admincss/payment.css" />
<link rel="stylesheet" href="../css/admincss/scrap.css" />
<link rel="stylesheet" href="../css/admincss/notifications.css" />
<link rel="stylesheet" href="../css/admincss/feedback.css" />
<base href="http://localhost/ecocycle/">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
html, body { margin:0; padding:0; min-height:100vh; display:flex; flex-direction:column;background:#f8f9fa; }
main { flex:1; }
footer { margin-top:auto; }
</style>
</head>
<body>

<!-- Navbar -->
<header class="navbar navbar-expand-lg bg-white px-4 py-0">
  <div class="container-fluid">
    <a class="navbar-brand ajax-link" href="admin/admin_home.php?page=dashboard">
      <img src="images/logo.svg" alt="EcoCycle Logo" class="logo-icon" />
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto me-3">
        <li class="nav-item mx-2"><a class="nav-link ajax-link" href="admin/admin_home.php?page=dashboard">Dashboard</a></li>
        <li class="nav-item mx-2"><a class="nav-link ajax-link" href="admin/admin_home.php?page=user">Manage Users</a></li>
        <li class="nav-item mx-2"><a class="nav-link ajax-link" href="admin/admin_home.php?page=collector">Manage Collectors</a></li>
        <li class="nav-item mx-2"><a class="nav-link ajax-link" href="admin/admin_home.php?page=request">Scrap Requests</a></li>
        <li class="nav-item mx-2"><a class="nav-link ajax-link" href="admin/admin_home.php?page=payment">Payments</a></li>
      </ul>
      <button class="btn btn-outline-danger" onclick="confirmLogout()">Logout</button>
    </div>
  </div>
</header>

<!-- Dynamic Content -->
<main id="dynamicContent" class="container py-5">
  <!-- Initial content will be loaded via AJAX -->
</main>

<!-- Footer -->
<footer class="bg-success text-white py-4">
  <div class="container d-flex flex-wrap justify-content-between">
    <div class="footer-left">
      <h4>EcoCycle Admin</h4>
      <p>Manage users, collectors, scrap requests, and payments efficiently.</p>
    </div>
    <div class="footer-right">
      <ul class="list-unstyled">
        <li><a href="admin/admin_home.php?page=dashboard" class="text-white text-decoration-none ajax-link">Dashboard</a></li>
        <li><a href="admin/admin_home.php?page=user" class="text-white text-decoration-none ajax-link">Users</a></li>
        <li><a href="admin/admin_home.php?page=payments" class="text-white text-decoration-none ajax-link">Payments</a></li>
        <li><a href="#" class="text-white text-decoration-none" onclick="confirmLogout()">Logout</a></li>
      </ul>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// =======================
// SPA AJAX Loader
// =======================
function loadPage(page, addToHistory = true) {
  const url = "admin/partials/" + page + ".php";
  const content = document.getElementById("dynamicContent");
  content.innerHTML = "<p>Loading...</p>";

  fetch(url)
    .then(res => res.ok ? res.text() : Promise.reject())
    .then(html => {
      content.innerHTML = html;

      if (addToHistory) {
        history.pushState({ page }, "", `admin/admin_home.php?page=${page}`);
      }

      // Initialize JS for loaded page
      if (page === "user") {
        // Fetch users from backend on first load
        fetch("admin/admin_home_manager.php?action=get_users")
          .then(r => r.json())
          .then(res => {
            if (res.status === "success") initUserPage(res.data);
            else console.error("Failed to load users:", res.message);
          })
          .catch(err => console.error("Error fetching users:", err));
      }

      if (page === "collector") initCollectorPage();
      if (page === "request") initRequestPage();
      if (page === "scrap") initScrapPage();
      if (page === "notifications") initNotifications();
      //if (page === "feedback") initFeedback();
      if (page === "settings") initSettings();
      if (page === "payment") initPayment();

    })
    .catch(() => {
      content.innerHTML = '<div class="alert alert-danger">⚠ Page not found.</div>';
    });

  console.log("Fetching page:", url);
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".ajax-link").forEach(link => {
    link.addEventListener("click", function(e) {
      e.preventDefault();
      const page = new URL(this.href).searchParams.get("page") || "dashboard";
      loadPage(page);
    });
  });

  const params = new URLSearchParams(window.location.search);
  const initialPage = params.get("page") || "dashboard";
  loadPage(initialPage, false);

  window.onpopstate = e => {
    const page = e.state?.page || "dashboard";
    loadPage(page, false);
  };
});

// =======================
// Helper Functions
// =======================

function generateAvatar(name) {
    if (!name) return "?";
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
}

function getStatusBadge(status) {
    const cls = status === 'Active' ? 'success' : 'danger';
    return `<span class="badge bg-${cls}">${status}</span>`;
}

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

// =======================
// User Page Initialization
// =======================
function initUserPage(users) {
    if (!users || !users.length) return console.warn('No users to display.');

    let currentPage = 1;
    const rowsPerPage = 10;
    let filteredUsers = [...users];

    const searchInput = document.getElementById('searchInput');
    const tbody = document.getElementById("userTableBody");
    const pagination = document.getElementById("pagination");

    if (!tbody || !pagination) {
        console.error("Table body or pagination container not found.");
        return;
    }

    // ---- Search ----
    searchInput?.addEventListener('input', function () {
        const val = this.value.toLowerCase();
        filteredUsers = users.filter(u =>
            u.name.toLowerCase().includes(val) ||
            u.email.toLowerCase().includes(val) ||
            u.phone.includes(val) ||
            u.address.toLowerCase().includes(val)
        );
        currentPage = 1;
        renderTable();
    });

    // ---- Render Table ----
    function renderTable() {
        const start = (currentPage - 1) * rowsPerPage;
        const paginated = filteredUsers.slice(start, start + rowsPerPage);

        tbody.innerHTML = paginated.map(u => `
            <tr>
                <td>
                    <div class="user-info">
                        <div class="user-avatar">${u.avatar || generateAvatar(u.name)}</div>
                        <div class="user-details">
                            <h6><a href="#" onclick="viewUser(${u.id});return false;" class="text-decoration-none">${u.name}</a></h6>
                            <small>ID: ${u.id}</small>
                        </div>
                    </div>
                </td>
                <td>${u.phone}</td>
                <td>${u.email}</td>
                <td>${u.address}</td>
                <td>${u.pincode || ''}</td>
                <td>${getStatusBadge(u.status)}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="openNotifyModal(${u.id})">Notify</button>
                </td>
            </tr>
        `).join('');

        renderPagination();
    }

    // ---- Render Pagination ----
    function renderPagination() {
        const totalPages = Math.ceil(filteredUsers.length / rowsPerPage);
        pagination.innerHTML = '';

        if (totalPages <= 1) return;

        pagination.innerHTML += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1});return false;">&lt;</a></li>`;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                pagination.innerHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i});return false;">${i}</a></li>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                pagination.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        pagination.innerHTML += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1});return false;">&gt;</a></li>`;
    }

    // ---- Global Handlers ----
    window.changePage = function (page) {
        const totalPages = Math.ceil(filteredUsers.length / rowsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    }

    window.toggleStatus = function (id) {
        fetch('admin/admin_home_manager.php?action=toggle_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ id })
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                const u = users.find(x => x.id === id);
                if (u) u.status = res.newStatus;
                renderTable();
                showToast(res.message);
            } else showToast(res.message, true);
        })
        .catch(err => console.error('Toggle status error:', err));
    }

    // ---- Notify User Modal ----
    window.openNotifyModal = function(id) {
        const modalEl = document.getElementById('notifyUserModal');
        const modal = new bootstrap.Modal(modalEl);
        document.getElementById('notifyUserId').value = id;
        document.getElementById('notifyMessage').value = "";
        modal.show();
    }

    document.getElementById('sendNotification').addEventListener('click', function() {
        const id = document.getElementById('notifyUserId').value;
        const message = document.getElementById('notifyMessage').value.trim();

        if (!message) {
            showToast('Please enter a message', true);
            return;
        }

        const formData = new FormData();
        formData.append('id', id);
        formData.append('message', message);

        fetch('admin/admin_home_manager.php?action=notify_user', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('Notification sent successfully');
                bootstrap.Modal.getInstance(document.getElementById('notifyUserModal')).hide();
            } else {
                showToast(res.message, true);
            }
        })
        .catch(err => console.error('Notify user error:', err));
    });

    // ---- Toast Notifications ----
    function showToast(msg, isError = false) {
        const t = document.createElement('div');
        t.className = `position-fixed top-0 end-0 m-3 alert alert-${isError ? 'danger' : 'success'} alert-dismissible fade show`;
        t.style.zIndex = '9999';
        t.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 4000);
    }

    // ---- Add New User ----
    document.getElementById('saveNewUser').addEventListener('click', function () {
        const name     = document.getElementById('newUserName').value.trim();
        const email    = document.getElementById('newUserEmail').value.trim();
        const phone    = document.getElementById('newUserPhone').value.trim();
        const address  = document.getElementById('newUserAddress').value.trim();
        const pincode  = document.getElementById('newUserPincode').value.trim();
        const password = document.getElementById('newUserPassword').value.trim();

        if (!name || !email || !phone || !address || !pincode || !password) {
            showToast('All fields are required', true);
            return;
        }

        const formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('phone', phone);
        formData.append('address', address);
        formData.append('pincode', pincode);
        formData.append('password', password);

        fetch('admin/admin_home_manager.php?action=add_user', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                const newUser = { 
                    id: res.user_id, 
                    name, email, phone, address, pincode, password, status: 'Active' 
                };
                users.push(newUser);
                filteredUsers = [...users];
                renderTable();

                bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                document.getElementById('addUserForm').reset();
                showToast('User added successfully');
            } else {
                showToast(res.message, true);
            }
        })
        .catch(err => console.error('Add user error:', err));
    });

    // ---- Initial Render ----
    renderTable();
}

// =======================
// FETCH USERS FROM BACKEND
// =======================
fetch('admin/admin_home_manager.php?action=get_users')
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            initUserPage(res.data);
        } else {
            console.error('Backend error:', res.message);
        }
    })
    .catch(err => console.error('Failed to load users:', err));

// =======================
// Collector Page
// =======================
function initCollectorPage() {
    const form = document.getElementById("addCollectorForm");
    const collectorForm = document.getElementById("collectorForm");
    const searchInput = document.getElementById("collectorSearch");
    const collectorTable = document.getElementById("collectorTable");
    const collectorCount = document.getElementById("collectorCount");
    const passwordField = document.getElementById("passwordField");
    let collectors = [];

    if (!form || !collectorForm || !collectorTable) return;

    // --- Form toggle ---
    document.getElementById("addCollectorBtn")?.addEventListener("click", () => {
        form.style.display = "block";
        generatePassword();
    });
    document.getElementById("cancelAdd")?.addEventListener("click", hideForm);
    document.getElementById("cancelAddBtn")?.addEventListener("click", hideForm);
    function hideForm() {
        form.style.display = "none";
        collectorForm.reset();
        collectorForm.onsubmit = submitNewCollector;
    }

    // --- Password generation ---
    function generatePassword() {
        const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const lower = 'abcdefghijklmnopqrstuvwxyz';
        const numbers = '0123456789';
        const special = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        let password = '';
        password += upper[Math.floor(Math.random() * upper.length)];
        password += lower[Math.floor(Math.random() * lower.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += special[Math.floor(Math.random() * special.length)];
        const allChars = upper + lower + numbers + special;
        for (let i = 4; i < 12; i++) {
            password += allChars[Math.floor(Math.random() * allChars.length)];
        }
        passwordField.value = password;
    }
    document.getElementById("generatePassword")?.addEventListener("click", generatePassword);

    async function fetchCollectors() {
    try {
        const res = await fetch('admin/admin_home_manager.php?action=get_collectors');
        const result = await res.json();
        if (result.status === 'success') {
            collectors = result.data; // assign the array from data
            renderCollectors();
        } else {
            console.error(result.message);
        }
    } catch (err) {
        console.error('Failed to fetch collectors:', err);
    }
}

function renderCollectors(list = collectors) {
    if (!list || !Array.isArray(list)) list = [];

    let html = '';

    list.forEach(c => {
        const initials = c.name
            ? c.name.split(' ').map(n => n[0]).join('').toUpperCase()
            : '';

        html += `
            <tr class="collector-row">
                <td class="fw-semibold">${c.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            <span class="text-white fw-bold">${initials}</span>
                        </div>
                        ${c.name || ''}
                    </div>
                </td>
                <td>${c.email || ''}</td>
                <td>
                    <span class="badge bg-light text-dark">
                        <i class="bi bi-geo-alt-fill me-1"></i>${c.area || 'N/A'}
                    </span>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-warning" onclick="editCollector('${c.id}')">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    collectorTable.innerHTML = html;
    collectorCount.textContent = `${list.length} collector${list.length !== 1 ? 's' : ''}`;
}

    // --- Add new collector ---
async function submitNewCollector(e) {
    e.preventDefault();
    const formData = new FormData(collectorForm);

    try {
        const res = await fetch('admin/admin_home_manager.php?action=add_collector', {
            method: 'POST',
            body: formData
        });

        const text = await res.text(); // read once
        let result;

        try {
            result = JSON.parse(text); // parse manually
        } catch (jsonErr) {
            console.error("Invalid JSON response:", text);
            showToast('Server error: invalid response');
            return;
        }

        if (result.status === 'success') {
            showToast(`Collector added successfully! Email sent to ${formData.get('email')}`);
            hideForm();
            fetchCollectors(); // refresh table
        } else {
            showToast(result.message || 'Failed to add collector');
        }
    } catch (err) {
        console.error(err);
        showToast('Error adding collector');
    }
}
collectorForm.onsubmit = submitNewCollector;


    // --- Edit collector ---
    window.editCollector = function(id) {
        const collector = collectors.find(c => c.id === id);
        if (!collector) return;
        form.style.display = "block";
        collectorForm.name.value = collector.name;
        collectorForm.email.value = collector.email;
        collectorForm.area.value = collector.area;
        collectorForm.password.value = "";

        collectorForm.onsubmit = async function(e) {
            e.preventDefault();
            const formData = new FormData(collectorForm);
            formData.append('id', id);
            if (!formData.get('password')) formData.delete('password');

            try {
                const res = await fetch('admin/admin_home_manager.php?action=update_collector', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.status === 'success') {
                    showToast(`Collector ${collectorForm.name.value} updated successfully!`);
                    hideForm();
                    fetchCollectors();
                } else {
                    showToast(result.message || 'Failed to update collector');
                }
            } catch (err) {
                console.error(err);
                showToast('Error updating collector');
            }
        };
    };

    // --- Search collectors ---
    searchInput?.addEventListener("input", function() {
        const term = this.value.toLowerCase();
        renderCollectors(collectors.filter(c =>
            c.name.toLowerCase().includes(term) ||
            c.email.toLowerCase().includes(term) ||
            c.area.toLowerCase().includes(term)
        ));
    });

    // --- Toast notification ---
    function showToast(msg) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 m-3 alert alert-success alert-dismissible fade show';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // --- Initial fetch and render ---
    fetchCollectors();
}

// =======================
// Request Page  (renamed!)
// =======================
function initRequestPage() {
    let requests = [];
    let currentRequest = null;

    function getStatusBadge(status) {
        if (!status) return '<span class="badge bg-secondary">Pending</span>';
        const s = status.toLowerCase();
        switch(s) {
            case 'pending':   return '<span class="badge status-pending"><i class="bi bi-clock me-1"></i>Pending</span>';
            case 'accepted':
            case 'assigned':  return '<span class="badge status-accepted"><i class="bi bi-check-circle me-1"></i>Assigned</span>';
            case 'collected': return '<span class="badge status-collected"><i class="bi bi-check-circle me-1"></i>Collected</span>';
            case 'completed': return '<span class="badge status-completed"><i class="bi bi-check-all me-1"></i>Completed</span>';
            case 'cancelled': return '<span class="badge status-cancelled"><i class="bi bi-x-circle me-1"></i>Cancelled</span>';
            default:          return '<span class="badge bg-secondary">Pending</span>';
        }
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
    }

    function getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = now - date;
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        if(diffDays === 0) return 'Today';
        if(diffDays === 1) return 'Yesterday';
        if(diffDays < 7) return `${diffDays} days ago`;
        return `${Math.ceil(diffDays/7)} weeks ago`;
    }

    function showToast(message, type='success') {
        const toastClass = type==='success'?'alert-success':'alert-danger';
        const toast = document.createElement('div');
        toast.className = `position-fixed top-0 end-0 m-3 alert ${toastClass} alert-dismissible fade show`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `<i class="bi bi-check-circle me-2"></i>${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(toast);
        setTimeout(()=>{ if(toast.parentNode) toast.remove(); }, 4000);
    }

    // ---------------- Fetch Requests ----------------
    function fetchRequests() {
        fetch('admin/admin_home_manager.php?action=fetch_requests')
            .then(res => res.json())
            .then(data => {
                requests = data.data;
                // Pre-calculate estimated values for items
                requests.forEach(r => {
                    r.items = (r.items || []).map(item => {
                        // Use collector_weight if exists, else user quantity
                        const weight = item.collector_weight ?? item.quantity ?? 0;
                        const value = (item.price_per_unit ?? 0) * weight;
                        return {...item, computed_weight: weight, computed_value: value};
                    });
                });
                renderRequests();
                updateStats();
            })
            .catch(err => showToast('Failed to fetch requests', 'danger'));
    }

    // ---------------- Render Requests ----------------
    function renderRequests() {
        const tbody = document.getElementById('requestTable');
        tbody.innerHTML = '';

        requests.forEach(request => {
            const statusBadge = getStatusBadge(request.status);
            const statusLower = (request.status || '').toLowerCase();

            const row = `
                <tr data-status="${request.status}">
                    <td>
                        <div class="fw-semibold text-primary">${request.id}</div>
                        <small class="text-muted">${formatDate(request.date)}</small>
                    </td>
                    <td>
                        <div class="fw-semibold">${request.user}</div>
                        <small class="text-muted">${request.phone}</small>
                    </td>
                    <td>${request.collector || '-'}</td>
                    <td>
                        ${(request.items || []).map(item => `
                            <div class="fw-semibold"> ${item.scrap_name}</div>
                            <small class="text-muted">
                                User: ${item.quantity || '-'} ${item.unit} 
                                ${item.collector_weight ? `• Collected: ${item.collector_weight} ${item.unit}` : ''}
                                • Value: ₹${item.computed_value.toFixed(2)}
                            </small>
                        `).join('')}
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width:150px;" title="${request.address}">
                            <i class="bi bi-geo-alt me-1"></i>${request.address.split(',')[0]}
                        </div>
                        <small class="text-muted">${getTimeAgo(request.date)}</small>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-info d-flex align-items-center" onclick="viewRequest('${request.id}')">
                                <i class="bi bi-eye me-1"></i> View
                            </button>
                            ${(statusLower !== 'completed' && statusLower !== 'cancelled' && statusLower !== 'collected') ? `
                            <button class="btn btn-sm btn-warning d-flex align-items-center" onclick="showReassignModal('${request.id}')">
                                <i class="bi bi-arrow-repeat me-1"></i> Reassign
                            </button>` : ''}
                        </div>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    // ---------------- Reassign Collector ----------------
    let reassignRequestId = null;
    window.showReassignModal = function(requestId){
        reassignRequestId = requestId;
        new bootstrap.Modal(document.getElementById('reassignModal')).show();
    };

    document.getElementById('confirmReassign').addEventListener('click', ()=>{
        const collectorId = document.getElementById('newCollector').value;
        if(!collectorId) { showToast('Select a collector', 'warning'); return; }

        fetch('admin/admin_home_manager.php?action=reassign', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${reassignRequestId}&collector_id=${collectorId}`
        })
        .then(res => res.json())
        .then(resp=>{
            if(resp.status === 'success'){
                const req = requests.find(r => r.id === reassignRequestId);
                if(req) req.collector = document.getElementById('newCollector').selectedOptions[0].text;
                renderRequests();
                showToast('Collector reassigned');
                bootstrap.Modal.getInstance(document.getElementById('reassignModal')).hide();
            } else showToast(resp.error || 'Failed to reassign', 'danger');
        })
        .catch(err=>showToast('Error reassigning', 'danger'));
    });

    function loadCollectors() {
        fetch('admin/admin_home_manager.php?action=choose_collectors')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('newCollector');
                select.innerHTML = '<option value="">Choose a collector</option>';
                data.forEach(c => {
                    const option = document.createElement('option');
                    option.value = c.collector_id;
                    option.textContent = c.name;
                    select.appendChild(option);
                });
            })
            .catch(err => showToast('Failed to load collectors', 'danger'));
    }
    loadCollectors();

    // ---------------- Stats ----------------
    function updateStats() {
        document.getElementById('totalRequests').textContent     = requests.length;
        document.getElementById('pendingRequests').textContent   = requests.filter(r => r.status==='pending').length;
        document.getElementById('acceptedRequests').textContent  = requests.filter(r => r.status==='accepted').length;
        document.getElementById('completedRequests').textContent = requests.filter(r => r.status==='completed').length;
        document.getElementById('todayRequests').textContent     = requests.filter(r => new Date(r.date).toDateString() === new Date().toDateString()).length;
    }

    // ---------------- View Modal ----------------
    window.viewRequest = function(requestId) {
        currentRequest = requests.find(r => r.id===requestId);
        if(!currentRequest) return;

        const modal = new bootstrap.Modal(document.getElementById('viewModal'));
        document.getElementById('modalRequestId').textContent = currentRequest.id;
        document.getElementById('modalUser').textContent = currentRequest.user;
        document.getElementById('modalPhone').textContent = currentRequest.phone;
        document.getElementById('modalEmail').textContent = currentRequest.email;
        document.getElementById('modalCollector').textContent = currentRequest.collector || '-';

        const itemsContainer = document.getElementById('modalScrapType');
        itemsContainer.innerHTML = (currentRequest.items || []).map(item => `
            <div>
                <strong>${item.scrap_name}</strong><br>
                User Qty: ${item.quantity || '-'} ${item.unit} <br>
                Collected: ${item.collector_weight || '-'} ${item.unit} <br>
                Value: ₹${item.computed_value.toFixed(2)}
            </div>
        `).join('');

        // Total weight & value
        const totalWeight = (currentRequest.items || []).reduce((sum, i)=>sum+(i.computed_weight||0),0);
        const totalValue = (currentRequest.items || []).reduce((sum, i)=>sum+(i.computed_value||0),0);

        document.getElementById('modalWeight').textContent = totalWeight ? `${totalWeight} kg` : '-';
        document.getElementById('modalValue').textContent = totalValue ? `₹${totalValue.toFixed(2)}` : '-';

        document.getElementById('modalAddress').textContent = currentRequest.address;

        const imagesContainer = document.getElementById('modalImages');
        imagesContainer.innerHTML = '';
        (currentRequest.images || []).forEach(url=>{
            const img = document.createElement('img');
            img.src = url;
            img.className = 'image-thumbnail';
            img.onclick = ()=>window.open(url,'_blank');
            imagesContainer.appendChild(img);
        });

        modal.show();
    };

    function showModalTimeline(pickupId) {
    const pickup = assignedPickups[pickupId];
    if (!pickup) return;

    const timeline = document.getElementById('modalTimeline');
    timeline.innerHTML = ''; // clear previous content

    // Event 1: Request made
    const requestEvent = document.createElement('div');
    requestEvent.className = 'timeline-event';
    requestEvent.innerHTML = `
        <div class="timeline-dot bg-primary"></div>
        <div class="timeline-content">
            <strong>Request Made</strong><br>
            Date: ${pickup.request_date} <br>
            Time: ${pickup.request_time || 'N/A'}
        </div>
    `;
    timeline.appendChild(requestEvent);

    // Event 2: Scheduled Pickup
    const pickupEvent = document.createElement('div');
    pickupEvent.className = 'timeline-event';
    pickupEvent.innerHTML = `
        <div class="timeline-dot bg-warning"></div>
        <div class="timeline-content">
            <strong>Scheduled Pickup</strong><br>
            Date: ${pickup.pickup_date} <br>
            Time Slot: ${pickup.pickup_slot || 'N/A'}
        </div>
    `;
    timeline.appendChild(pickupEvent);

    // Optionally: Collected
    if (pickup.status === 'collected') {
        const collectedEvent = document.createElement('div');
        collectedEvent.className = 'timeline-event';
        collectedEvent.innerHTML = `
            <div class="timeline-dot bg-success"></div>
            <div class="timeline-content">
                <strong>Collected</strong><br>
                Date: ${pickup.pickup_date} <br>
                Time Slot: ${pickup.pickup_slot || 'N/A'}
            </div>
        `;
        timeline.appendChild(collectedEvent);
    }
}
 
    // ---------------- Quick Actions ----------------
    document.getElementById('modalReassignBtn').addEventListener('click', ()=>{
        if(!currentRequest) return;
        bootstrap.Modal.getInstance(document.getElementById('viewModal')).hide();
        showReassignModal(currentRequest.id);
    });

    document.getElementById('modalCancelBtn').addEventListener('click', ()=>{
        if(!currentRequest) return;
        if(!confirm(`Are you sure you want to cancel request #${currentRequest.id}?`)) return;

        fetch('admin/admin_home_manager.php?action=update',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:`id=${currentRequest.id}&status=cancelled`
        })
        .then(res=>res.json())
        .then(resp=>{
            if(resp.status==='success'){
                currentRequest.status='cancelled';
                renderRequests();
                updateStats();
                showToast('Request cancelled','info');
                bootstrap.Modal.getInstance(document.getElementById('viewModal')).hide();
            } else showToast(resp.error || 'Failed to cancel request','danger');
        })
        .catch(err=>showToast('Error cancelling request','danger'));
    });

    // ---------------- Initialize ----------------
    fetchRequests();
}


let scrapCategories = [];
let originalPrices = {};

// -------------------------
// Init Scrap Page (includes table, modal, add/update/delete)
// -------------------------
function initScrapPage() {

    // -------------------------
    // Toast helper
    // -------------------------
    function showToast(message, type = 'success') {
        const toastClass = type === 'success' ? 'alert-success' :
                           type === 'warning' ? 'alert-warning' :
                           type === 'info' ? 'alert-info' : 'alert-danger';
        const toast = document.createElement('div');
        toast.className = `position-fixed top-0 end-0 m-3 alert ${toastClass} alert-dismissible fade show`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // -------------------------
    // Load scrap categories from backend
    // -------------------------
    fetch('admin/admin_home_manager.php?action=get_scrap_types')
        .then(res => res.json())
        .then(resp => {
            if (resp.status === 'success') {
                scrapCategories = resp.data;
                renderPriceTable();
            } else {
                showToast('Failed to load scrap types', 'danger');
            }
        })
        .catch(err => showToast('Error loading scrap types', 'danger'));

    // -------------------------
    // Modal initialization and Add New Scrap
    // -------------------------
    const addNewBtn = document.getElementById('addNewScrap');
    const addModalEl = document.getElementById('addScrapModal');
    const addModal = new bootstrap.Modal(addModalEl);

    if(addNewBtn) {
        addNewBtn.addEventListener('click', () => addModal.show());
    }

    const saveNewBtn = document.getElementById('saveNewScrap');
    if(saveNewBtn) {
        saveNewBtn.addEventListener('click', () => {
            const name  = document.getElementById('newScrapName').value.trim();
            const unit  = document.getElementById('newScrapUnit').value.trim();
            const price = parseFloat(document.getElementById('newScrapPrice').value);

            if (!name || !unit || isNaN(price)) {
                showToast('Please fill all fields', 'warning');
                return;
            }

            fetch('admin/admin_home_manager.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `name=${encodeURIComponent(name)}&unit=${encodeURIComponent(unit)}&price=${price}`
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.status === 'success') {
                    showToast('Scrap type added', 'success');
                    document.getElementById('addScrapForm').reset();
                    addModal.hide();
                    initScrapPage(); // reload table
                } else {
                    showToast(resp.message, resp.status === 'warning' ? 'warning' : 'danger');
                }
            })
            .catch(err => showToast('Error adding scrap type', 'danger'));
        });
    }

    // -------------------------
    // Render Price Table
    // -------------------------
    function renderPriceTable(categories = scrapCategories) {
        const tbody = document.getElementById('priceTable');
        tbody.innerHTML = '';

        categories.forEach(cat => {
            const tr = document.createElement('tr');
            tr.dataset.categoryId = cat.id;
            tr.innerHTML = `
                <td>
                    <div class="fw-semibold">${cat.name}</div>
                    <small class="text-muted">Unit: ${cat.unit}</small>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="me-2">₹</span>
                        <input type="number" class="form-control price-input" 
                            value="${cat.price.toFixed(2)}" step="0.01" min="0"
                            data-category-id="${cat.id}">
                    </div>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary save-price-btn" 
                            data-category-id="${cat.id}" style="display: none;">
                            <i class="bi bi-check-circle"></i> Save
                        </button>
                        <button class="btn btn-sm btn-danger delete-category-btn" 
                            data-category-id="${cat.id}">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // -------------------------
        // Track price input changes
        // -------------------------
        document.querySelectorAll('.price-input').forEach(input => {
            const id = input.dataset.categoryId;
            originalPrices[id] = parseFloat(input.value);

            input.addEventListener('input', function () {
                const saveBtn = document.querySelector(`.save-price-btn[data-category-id="${id}"]`);
                saveBtn.style.display = (parseFloat(this.value) !== originalPrices[id]) ? 'inline-block' : 'none';
            });
        });

        // -------------------------
        // Save price updates
        // -------------------------
        document.querySelectorAll('.save-price-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.categoryId;
                const input = document.querySelector(`.price-input[data-category-id="${id}"]`);
                const newPrice = parseFloat(input.value);

                fetch('admin/admin_home_manager.php?action=updatePrice', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&price=${newPrice}`
                })
                .then(res => res.json())
                .then(resp => {
                    if (resp.status === 'success') {
                        originalPrices[id] = newPrice;
                        showToast('Price updated successfully', 'success');
                        initScrapPage();
                    } else showToast(resp.message, 'danger');
                })
                .catch(err => showToast('Error updating price', 'danger'));
            });
        });

        // -------------------------
        // Delete scrap category
        // -------------------------
        document.querySelectorAll('.delete-category-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.categoryId;
                if (!confirm('Are you sure you want to delete this scrap type?')) return;

                fetch('admin/admin_home_manager.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                })
                .then(res => res.json())
                .then(resp => {
                    if (resp.status === 'success') {
                        showToast('Scrap type deleted', 'warning');
                        initScrapPage();
                    } else showToast(resp.message, 'danger');
                })
                .catch(err => showToast('Error deleting scrap type', 'danger'));
            });
        });
    }

}

function initNotifications() {
    let notifications = []; // will be loaded from backend

    // -------------------------
    // Toast Helper
    // -------------------------
    function showToast(message, type = 'success') {
        const toastClass = type === 'success' ? 'alert-success' :
                           type === 'warning' ? 'alert-warning' :
                           type === 'info' ? 'alert-info' : 'alert-danger';
        const toast = document.createElement('div');
        toast.className = `position-fixed top-0 end-0 m-3 alert ${toastClass} alert-dismissible fade show`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // -------------------------
    // Form Elements
    // -------------------------
    const messageInput = document.getElementById('notificationMessage');
    const targetSelect = document.getElementById('notificationTarget');
    const form = document.getElementById('createNotificationForm');

    if (!messageInput || !targetSelect || !form) {
        console.error("Notification form elements not found!");
        return;
    }

    // -------------------------
    // Character Counter
    // -------------------------
    messageInput.addEventListener('input', () => {
        document.getElementById('messageCount').textContent = messageInput.value.length;
    });

    // -------------------------
    // Form Submission
    // -------------------------
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!messageInput.value.trim() || !targetSelect.value) {
            showToast('Please fill all fields', 'warning');
            return;
        }

        const payload = new URLSearchParams();
        payload.append('message', messageInput.value.trim());
        payload.append('target', targetSelect.value);

        // optional: add user_id or collector_id if single
        if (targetSelect.value === 'user') {
            payload.append('user_id', form.dataset.userId || 0);
        } else if (targetSelect.value === 'collector') {
            payload.append('collector_id', form.dataset.collectorId || 0);
        }

        fetch('admin/admin_home_manager.php?action=create_notification', {
            method: 'POST',
            body: payload
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.status === 'success') {
                showToast('Notification sent successfully!', 'success');
                form.reset();
                document.getElementById('messageCount').textContent = '0';
                loadNotifications(); // refresh table
            } else {
                showToast(resp.message, 'danger');
            }
        })
        .catch(err => showToast('Error sending notification', 'danger'));
    });

    // -------------------------
    // Load Notifications
    // -------------------------
    function loadNotifications() {
        fetch('admin/admin_home_manager.php?action=get_notifications')
            .then(res => res.json())
            .then(resp => {
                if (resp.status === 'success' && Array.isArray(resp.data)) {
                    notifications = resp.data;
                    renderNotifications();
                } else {
                    showToast('Error loading notifications', 'danger');
                }
            })
            .catch(err => showToast('Error loading notifications', 'danger'));
    }

    // -------------------------
    // Render Notifications Table
    // -------------------------
    function renderNotifications() {
    const tbody = document.getElementById('notificationsTable');
    if (!tbody) {
        console.error("Table body not found!");
        return;
    }

    tbody.innerHTML = '';

    notifications.forEach(n => {
        // Use the display_target from backend for consistent labeling
        const targetText = n.display_target || 'Unknown';

        const statusBadge = n.status === 'read' 
            ? '<span class="badge bg-success">Read</span>' 
            : '<span class="badge bg-warning">Unread</span>';

        const row = `
            <tr>
                <td>#${String(n.id).padStart(3, '0')}</td>
                <td>${n.message}</td>
                <td>${targetText}</td>
                <td>${formatDate(n.date)}</td>
                <td>${statusBadge}</td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row); // safer than innerHTML +=
    });
}

    // -------------------------
    // Format Date
    // -------------------------
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    // Load notifications initially
    loadNotifications();
}

function initFeedback() {
    let feedbacks = [];
    let currentFeedbackId = null;

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    // Calculate time ago
    function getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) return `${diffDays} days ago`;
        if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
        return `${Math.ceil(diffDays / 30)} months ago`;
    }

    // Generate stars HTML
    function generateStars(rating) {
        return '★'.repeat(rating) + '☆'.repeat(5 - rating);
    }

    // Open modal to reply
    function replyFeedback(id) {
        currentFeedbackId = id; // store id for submit later
        const replyInput = document.getElementById("replyText");
        if (replyInput) replyInput.value = ""; // clear old text
        const modalEl = document.getElementById("replyModal");
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    // Handle modal submit safely
    const sendReplyBtn = document.getElementById("sendReplyBtn");
    if (sendReplyBtn) {
        sendReplyBtn.addEventListener("click", () => {
            const replyInput = document.getElementById("replyText");
            const reply = replyInput ? replyInput.value.trim() : "";
            if (!reply) {
                alert("Please enter a reply before sending.");
                return;
            }

            fetch("admin/admin_home_manager.php?action=reply_feedback", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${currentFeedbackId}&reply=${encodeURIComponent(reply)}`
            })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === "success") {
                        const modalEl = document.getElementById("replyModal");
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                        loadFeedbacks(); // refresh list
                    }
                })
                .catch(err => console.error("Reply error:", err));
        });
    }

    // Render feedbacks
    function renderFeedbacks() {
        const tbody = document.getElementById('feedbackTable');
        if (!tbody) return;
        let rows = "";

        feedbacks.forEach(fb => {
            const statusBadge = fb.replied
                ? `<span class="badge bg-success">Replied</span><br><small>${fb.reply}</small>`
                : `<span class="badge bg-warning">Pending</span>
                   <button class="btn btn-sm btn-primary ms-2" data-id="${fb.id}">Reply</button>`;

            rows += `
                <tr>
                    <td>${fb.user}</td>
                    <td>${generateStars(fb.rating)} (${fb.rating}/5)</td>
                    <td>${fb.message}</td>
                    <td>${formatDate(fb.date)}<br><small>${getTimeAgo(fb.date)}</small></td>
                    <td>${statusBadge}</td>
                </tr>
            `;
        });

        tbody.innerHTML = rows;

        // Attach click listeners to all reply buttons
        tbody.querySelectorAll('button[data-id]').forEach(btn => {
            btn.addEventListener('click', () => {
                replyFeedback(btn.getAttribute('data-id'));
            });
        });
    }

    // Update stats (average rating)
    function updateStats() {
        const avgEl = document.getElementById('avgRating');
        if (!avgEl) return;
        if (!feedbacks.length) {
            avgEl.textContent = "-";
            return;
        }
        const avg = (feedbacks.reduce((sum, f) => sum + f.rating, 0) / feedbacks.length).toFixed(1);
        avgEl.textContent = avg;
    }

    // Fetch feedbacks from backend
    function loadFeedbacks() {
        fetch("admin/admin_home_manager.php?action=get_feedbacks")
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    feedbacks = data;
                    renderFeedbacks();
                    updateStats();
                } else {
                    console.error("Invalid feedback response:", data);
                    const tbody = document.getElementById('feedbackTable');
                    if (tbody) {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center">No feedbacks found</td></tr>`;
                    }
                    const avgEl = document.getElementById('avgRating');
                    if (avgEl) avgEl.textContent = "-";
                }
            })
            .catch(err => {
                console.error("Load feedbacks error:", err);
                const tbody = document.getElementById('feedbackTable');
                if (tbody) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error loading feedbacks</td></tr>`;
                }
                const avgEl = document.getElementById('avgRating');
                if (avgEl) avgEl.textContent = "-";
            });
    }

    // Expose replyFeedback if needed externally
    const init = { replyFeedback };

    // Load data initially
    loadFeedbacks();

    return init; // returns namespace with functions
}

// ✅ Initialize once DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    window.feedbackModule = initFeedback(); // expose globally if needed
});


function initSettings() {
    // --------------------------
    // Toggle password visibility
    // --------------------------
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.parentElement.querySelector('.password-toggle i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // --------------------------
    // Inline edit buttons
    // --------------------------
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const fieldId = btn.closest('.input-group').querySelector('input, textarea').id;
            toggleEdit(fieldId); // Assumes toggleEdit is defined elsewhere
        });
    });

    // --------------------------
    // Password toggle buttons
    // --------------------------
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const fieldId = btn.closest('.input-group').querySelector('input').id;
            togglePassword(fieldId);
        });
    });

    // --------------------------
    // Password change form submission
    // --------------------------
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const current = document.getElementById('currentPassword').value.trim();
            const newPass = document.getElementById('newPassword').value.trim();
            const confirmPass = document.getElementById('confirmPassword').value.trim();

            if (!current || !newPass || !confirmPass) return showError('All password fields are required');
            if (newPass !== confirmPass) return showError('Passwords do not match!');
            if (!validatePassword(newPass)) return showError('Password must be ≥6 chars and include uppercase, lowercase, number & special char');

            const formData = new FormData();
            formData.append('action', 'change_password');
            formData.append('currentPassword', current);
            formData.append('newPassword', newPass);

            fetch('/ecocycle/user/privatehomepagemanager.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        passwordForm.reset();
                        document.getElementById('confirmPassword').parentElement.querySelector('.validation-message').textContent = '';
                        showSuccess(data.message);
                    } else {
                        showError(data.message || 'Failed to change password');
                    }
                });
        });
    }

    // --------------------------
    // SweetAlert helpers
    // --------------------------
    function showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    }

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: message
        });
    }
}

function initPayment() {
    let selectedPayments = new Set();
    let currentPayment = null;
    let payments = []; // will hold fetched payments

    const backendUrl = 'admin/admin_home_manager.php'; // adjust if in subfolder

    // --------------------------
    // Format date
    // --------------------------
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    // --------------------------
    // Get status badge
    // --------------------------
    function getStatusBadge(status) {
        switch(status) {
            case 'Pending':
                return '<span class="badge status-pending"><i class="bi bi-clock me-1"></i>Pending</span>';
            case 'Credited_to_wallet':
                return '<span class="badge status-paid"><i class="bi bi-wallet2 me-1"></i>Wallet</span>';
            case 'Processing':
                return '<span class="badge status-processing"><i class="bi bi-arrow-repeat me-1"></i>Processing</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }

    // --------------------------
    // Fetch payments from backend
    // --------------------------
    async function fetchPayments() {
        try {
            const res = await fetch(`${backendUrl}?action=get_payments`);
            const data = await res.json();
            if(data.status === 'success') {
                payments = data.data;
                renderPayments();
                updateStats();
            } else {
                showToast(data.message, 'danger');
            }
        } catch(e) {
            console.error(e);
            showToast('Failed to fetch payments', 'danger');
        }
    }

    // --------------------------
    // Render payments table
    // --------------------------
    function renderPayments(paymentsToRender = payments) {
    const tbody = document.getElementById('paymentsTable');
    if (!tbody) return;
    tbody.innerHTML = '';

    paymentsToRender.forEach(payment => {
        const statusBadge = getStatusBadge(payment.status);

        const row = `
            <tr class="payment-row">
    <td>${payment.user}</td>
    <td>${payment.upi_id || '-'}</td> <!-- ✅ show UPI ID -->
    <td>
        <span class="fw-semibold text-primary">${payment.request_id}</span>
        <div class="payment-details">${payment.scrapType}</div>
    </td>
    <td>
        <span class="amount-display text-success">₹${payment.amount.toLocaleString()}</span>
        <div class="payment-details">${payment.weight}</div>
    </td>
    <td>
        <div>${formatDate(payment.date)}</div>
        <small class="text-muted">${getTimeAgo(payment.date)}</small>
    </td>
    <td>${statusBadge}</td>
    <td>
        <div class="btn-group" role="group">
            <button class="btn btn-sm btn-success" onclick="viewPaymentDetails(${payment.payment_id})">
                Details <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-warning" onclick="prepareRazorpay(${payment.payment_id})">Pay</button>
        </div>
    </td>
</tr>
        `;
        tbody.innerHTML += row;
    });
}
    // --------------------------
    // Mark payment as credited to wallet
    // --------------------------
    async function markAsPaid(paymentId) {
        try {
            const formData = new FormData();
            formData.append('id', paymentId);



            const data = await res.json();
            if(data.status === 'success') {
                showToast(data.message, 'success');
                await fetchPayments(); // refresh table
            } else {
                showToast(data.message, 'danger');
            }
        } catch(e) {
            console.error(e);
            showToast('Failed to update payment', 'danger');
        }
    }

    // --------------------------
    // Get time ago
    // --------------------------
    function getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) return `${diffDays} days ago`;
        return `${Math.ceil(diffDays / 7)} weeks ago`;
    }

    // --------------------------
    // View payment details modal
    // --------------------------
function viewPaymentDetails(paymentId) {
    // ✅ Use payment_id instead of id
    currentPayment = payments.find(p => p.payment_id == paymentId);
    if (!currentPayment) return;

    const modalEl = document.getElementById('paymentModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);

    // Populate modal safely
    const fields = {
        UserName: currentPayment.user,
        UserEmail: currentPayment.email,
        RequestId: currentPayment.request_id,
        Amount: `₹${currentPayment.amount.toLocaleString()}`,
        Date: formatDate(currentPayment.date),
        ScrapType: currentPayment.scrapType,
        Weight: currentPayment.weight,
        Rate: currentPayment.rate,
        CollectionDate: formatDate(currentPayment.collectionDate),
        UpiId: currentPayment.upi_id || '-' // ✅ new UPI field
    };

    Object.keys(fields).forEach(id => {
        const el = document.getElementById('modal' + id);
        if (el) el.textContent = fields[id];
    });

    modal.show(); // ✅ open the modal
}


    // --------------------------
    // Update statistics
    // --------------------------
    function updateStats() {
        const totalWallet = payments.filter(p => p.status === 'Credited_to_wallet').reduce((sum, p) => sum + p.amount, 0);
        const totalPending = payments.filter(p => p.status === 'Pending').reduce((sum, p) => sum + p.amount, 0);
        const pendingCount = payments.filter(p => p.status === 'Pending').length;
        const avgAmount = payments.length ? Math.round(payments.reduce((sum, p) => sum + p.amount, 0) / payments.length) : 0;

        const elTotalPaid = document.getElementById('totalPaid');
        const elTotalPending = document.getElementById('totalPending');
        const elPendingCount = document.getElementById('pendingCount');
        const elAvgAmount = document.getElementById('avgAmount');

        if(elTotalPaid) elTotalPaid.textContent = `₹${totalWallet.toLocaleString()}`;
        if(elTotalPending) elTotalPending.textContent = `₹${totalPending.toLocaleString()}`;
        if(elPendingCount) elPendingCount.textContent = pendingCount;
        if(elAvgAmount) elAvgAmount.textContent = `₹${avgAmount.toLocaleString()}`;
    }

    // --------------------------
    // Show toast notification
    // --------------------------
    function showToast(message, type = 'success') {
        const toastClass = type === 'success' ? 'alert-success' : 
                          type === 'warning' ? 'alert-warning' : 
                          type === 'info' ? 'alert-info' : 'alert-danger';
        
        const toast = document.createElement('div');
        toast.className = `position-fixed top-0 end-0 m-3 alert ${toastClass} alert-dismissible fade show`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 4000);
    }
function payment(payment) {
    const amount = Math.round(parseFloat(payment.amount) * 100); // paise
    const email = payment.email;

    const options = {
        "key": "7HGxXhNw1LxclH7uolQRLC6n",   // replace with real key
        "amount": amount,
        "currency": "INR",
        "name": "EcoCycle",
        "description": `Payment for Request #${payment.request_id}`,
        "handler": function (response) {
            alert("Payment successful! Payment ID: " + response.razorpay_payment_id);

            // Update backend after success
            fetch('admin/admin_home_manager.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=razorpay_payment&payment_id=${payment.payment_id}&razorpay_payment_id=${response.razorpay_payment_id}`
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success'){
                    alert('Payment updated!');
                    location.reload();
                } else {
                    alert(res.message);
                }
            });
        },
        "prefill": { "email": email },
        "theme": { "color": "#2e7d32" }
    };

    const rzp = new Razorpay(options);
    rzp.open();
}
    // --------------------------
    // Initialize
    // --------------------------
    fetchPayments();

    // Expose globally
    window.viewPaymentDetails = viewPaymentDetails;
    window.markAsPaid = markAsPaid;
}
function prepareRazorpay(paymentId) {
    fetch('admin/admin_home_manager.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=prepare_razorpay&payment_id=${paymentId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status !== 'success') {
            alert(data.message);
            return;
        }

        const payment = data.data;

        if (!payment.upi_id) {
            alert("User UPI ID not found!");
            return;
        }

        const options = {
            key: "rzp_test_RPpwSDkRZ0GDh5", // Razorpay Key ID
            amount: Math.round(payment.amount * 100), // paise
            currency: "INR",
            name: "EcoCycle",
            description: `Scrap Payment #${payment.payment_id}`,
            handler: function(response) {
                // Send payment info to backend
                fetch('admin/admin_home_manager.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=razorpay_payment&payment_id=${payment.payment_id}&razorpay_payment_id=${response.razorpay_payment_id}`
                })
                .then(res => res.json())
                .then(res => {
                    alert(res.message);
                    if (res.status === 'success') fetchPayments();
                });
            },
            prefill: { 
                name: payment.name, 
                email: payment.email,
                contact: payment.contact || '', // optional
                vpa: payment.upi_id             // direct UPI ID
            },
            method: {
                card: true,
                netbanking: true,
                upi: true,
                wallet: true,
                paylater: true
            },
            theme: { color: "#2e7d32" }
        };

        const rzp = new Razorpay(options);
        rzp.open();
    })
    .catch(err => {
        console.error(err);
        alert("Something went wrong while preparing payment!");
    });
}


function openPaymentHistory() {
    fetch('admin/admin_home_manager.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get_payment_history'
    })
    .then(res => res.json())
    .then(data => {
        if (data.status !== 'success') {
            alert(data.message);
            return;
        }

        const tableBody = document.getElementById('paymentHistoryTable');
        tableBody.innerHTML = '';

        data.data.forEach((p, i) => {
            tableBody.innerHTML += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${p.name}</td>
                    <td>${p.email}</td>
                    <td>${p.request_id}</td>
                    <td>${p.scrapType}</td>
                    <td>${p.weight}</td>
                    <td>₹${p.amount}</td>
                    <td><span class="badge bg-${p.status === 'Earned' ? 'success' : 'warning'}">${p.status}</span></td>
                    <td>${formatDate(p.payment_date)}</td>
                </tr>
            `;
        });

        const modal = new bootstrap.Modal(document.getElementById('paymentHistoryModal'));
        modal.show();
    })
    .catch(err => {
        console.error(err);
        alert('Failed to load payment history');
    });
}

// =======================
// Logout
// =======================
function confirmLogout(){
  Swal.fire({
    title:'Are you sure?', text:"You will be logged out.", icon:'warning',
    showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#3085d6',
    confirmButtonText:'Yes, logout', cancelButtonText:'Cancel'
  }).then(result=>{if(result.isConfirmed) window.location.href='admin/logout.php';});
}
</script>
</body>
</html>
