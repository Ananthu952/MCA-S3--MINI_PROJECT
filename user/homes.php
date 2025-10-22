<?php include 'user_home_manager.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EcoCycle - User Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/dashboard.css" />
  <link rel="stylesheet" href="../css/scrap-request.css" />
  <link rel="stylesheet" href="../css/notification.css" />
  <link rel="stylesheet" href="../css/settings.css" />
  <base href="http://localhost/ecocycle/">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    html, body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    main { flex: 1; }
    footer { margin-top: auto; }
  </style>
</head>
<body>

<!-- Navbar -->
<header class="navbar navbar-expand-lg bg-white px-4 py-0">
  <div class="container-fluid">
    <a class="navbar-brand ajax-link" href="user/homes.php?page=home">
      <img src="images/logo.svg" alt="EcoCycle Logo" class="logo-icon" />
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto me-3">
        <li class="nav-item mx-2"><a class="nav-link ajax-link" href="user/homes.php?page=home">Scrap Prices</a></li>
        <li class="nav-item mx-2"><a class="nav-link ajax-link" href="user/homes.php?page=dashboard">User Dashboard</a></li>
        <li class="nav-item mx-2"><a class="nav-link ajax-link" href="user/homes.php?page=scraprequest">Add Request</a></li>
        <li class="nav-item mx-2"><a class="nav-link ajax-link" href="user/homes.php?page=settings">Profile</a></li>
        <li class="nav-item mx-2">
          <a href="user/homes.php?page=notification" class="nav-link ajax-link position-relative">
            <i class="fa-solid fa-bell"></i>
            <?php if ($unread_count > 0): ?>
              <span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $unread_count; ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
      </ul>
      <button class="btn btn-outline-danger" onclick="confirmLogout()">Logout</button>
    </div>
  </div>
</header>

<!-- Dynamic Content Container -->
<main id="dynamicContent" class="container py-5"></main>

<!-- Footer -->
<footer class="bg-success text-white py-4">
  <div class="container d-flex flex-wrap justify-content-between">
    <div class="footer-left">
      <h4>EcoCycle</h4>
      <p>EcoCycle is committed to making scrap collection accessible and transparent, promoting sustainability.</p>
    </div>
    <div class="footer-right">
      <ul class="list-unstyled">
        <li><a href="user/homes.php?page=home" class="text-white text-decoration-none ajax-link">Home</a></li>
        <li><a href="user/homes.php?page=dashboard" class="text-white text-decoration-none ajax-link">Dashboard</a></li>
        <li><a href="user/homes.php?page=scraprequest" class="text-white text-decoration-none ajax-link">Requests</a></li>
        <li><a href="#" class="text-white text-decoration-none" onclick="confirmLogout()">Logout</a></li>
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
  const url = "user/partials/" + page + ".php"; 
  console.log("Fetching URL:", url);

  document.getElementById("dynamicContent").innerHTML = "<p>Loading...</p>";

  fetch(url)
    .then(res => {
      if (!res.ok) throw new Error("Page not found");
      return res.text();
    })
    .then(html => {
      document.getElementById("dynamicContent").innerHTML = html;

      // Call page-specific initializers
      if (page === "home") loadScrapPrices();
      if (page === "scraprequest") initScrapRequestPage();
      if (page === "settings") initSettingsPage();
      if (page === "dashboard") initDashboard();
      if (page === "notification") initNotificationsPage();


      if (addToHistory) {
        const newURL = "user/homes.php?page=" + page;
        history.pushState({ page: page }, "", newURL);
      }
    })
    .catch(() => {
      document.getElementById("dynamicContent").innerHTML =
        '<div class="alert alert-danger">⚠ Page not found.</div>';
    });
}

// =======================
// Scrap Price Loader
// =======================
function loadScrapPrices() {
  fetch("fetch_data.php")
    .then(response => response.json())
    .then(data => {
      const pricesContainer = document.getElementById("scrapPriceContainer");
      if (!pricesContainer) return;
      pricesContainer.innerHTML = ""; 
      
      if (data.prices && data.prices.length > 0) {
        data.prices.forEach(item => {
          const card = `
            <div class="col-md-3">
              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body text-center">
                  <h5 class="card-title">${item.scrap_name}</h5>
                  <p class="card-text text-success fw-bold">
                    ₹${item.price_per_unit} / ${item.unit}
                  </p>
                </div>
              </div>
            </div>`;
          pricesContainer.innerHTML += card;
        });
      } else {
        pricesContainer.innerHTML = "<p>No prices available</p>";
      }
    })
    .catch(error => {
      console.error("Error loading scrap prices:", error);
      const pricesContainer = document.getElementById("scrapPriceContainer");
      if (pricesContainer) pricesContainer.innerHTML = "<p>Error loading prices</p>";
    });
}

// =======================
// Init on Page Load
// =======================
document.addEventListener("DOMContentLoaded", function () {
  function attachLinks() {
    document.querySelectorAll(".ajax-link").forEach(link => {
      link.onclick = function (e) {
        e.preventDefault();
        const page = new URL(this.href).searchParams.get("page") || "home";
        loadPage(page);
      };
    });
  }

  attachLinks();

  const params = new URLSearchParams(window.location.search);
  const initialPage = params.get("page") || "home";  
  loadPage(initialPage, false);

  window.onpopstate = function (e) {
    let page = e.state ? e.state.page : "home";  
    loadPage(page, false);
  };
});


function initScrapRequestPage() {
  function todayISO() {
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2,'0');
    const day = String(d.getDate()).padStart(2,'0');
    return `${y}-${m}-${day}`;
  }

  function next7DaysISO() {
    const d = new Date();
    d.setDate(d.getDate() + 7);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2,'0');
    const day = String(d.getDate()).padStart(2,'0');
    return `${y}-${m}-${day}`;
  }

  const pickupDateInput = document.getElementById('pickup_date');
  const pickupSlotSelect = document.getElementById('pickup_slot');
  pickupDateInput.setAttribute('min', todayISO());
  pickupDateInput.setAttribute('max', next7DaysISO());

  // Fetch available slots
  fetch('/ecocycle/user/privatehomepagemanager.php?action=get_time_slots')
    .then(res => res.json())
    .then(data => {
      const timeSlots = data.slots;
      const today = data.today;
      const currentHour = data.currentHour;

      pickupDateInput.addEventListener('change', updateSlots);
      updateSlots();

      function updateSlots() {
  const selectedDate = pickupDateInput.value;
  pickupSlotSelect.innerHTML = '<option value="">Select a slot</option>';

  timeSlots.forEach(slot => {
    const option = document.createElement('option');
    option.value = slot.value;
    option.textContent = slot.value;
    const slotEnd = slot.start + 2;

    if (selectedDate === today && currentHour >= slotEnd) {
      option.disabled = true;
      option.textContent += ' (unavailable)';
    }

    pickupSlotSelect.appendChild(option);
  });
      }
    });

  // Load address
  function loadAddresses() {
    fetch('/ecocycle/user/privatehomepagemanager.php?action=get_addresses')
      .then(res => res.json())
      .then(data => {
        if (data && data.length > 0) {
          const addr = data[0]; 
          document.getElementById('pickupAddress').value = `${addr.address} (Pincode: ${addr.pincode})`;
        }
      })
      .catch(err => {
        console.error('Error loading address:', err);
        document.getElementById('pickupAddress').value = 'Unable to load address';
      });
  }

  // Load scrap types
  function loadScrapTypes(select) {
    fetch('/ecocycle/user/privatehomepagemanager.php?action=get_scrap_types')
      .then(res => res.json())
      .then(data => {
        select.innerHTML = '<option value="">Select Type</option>';
        (data || []).forEach(type => {
          const opt = document.createElement('option');
          opt.value = type.scrap_id;
          opt.textContent = `${type.scrap_name} (${type.unit}) - ₹${type.price_per_unit}`;
          select.appendChild(opt);
        });

        // After filling options, update disabled states
        updateDisabledOptions();
      })
      .catch(() => {
        select.innerHTML = '<option value="">Unable to load types</option>';
      });
  }

  // Add scrap row
  function addScrapRow() {
    const container = document.createElement('div');
    container.className = 'scrap-item scrap-row';
    container.innerHTML = `
      <div>
        <label class="form-label mb-1 small text-sub">Type</label>
        <select name="scrap_type[]" class="form-select scrap-type" required>
          <option value="">Loading types...</option>
        </select>
      </div>
      <div>
        <label class="form-label mb-1 small text-sub">Quantity</label>
        <input type="number" name="quantity[]" min="1" placeholder="e.g., 5" class="form-control" />
      </div>
      <div class="d-flex">
        <button type="button" class="btn btn-danger w-100 delete-btn">
          <i class="fa-solid fa-trash me-2"></i> Delete
        </button>
      </div>
    `;
    document.getElementById('scrapItemsContainer').appendChild(container);
    const select = container.querySelector('.scrap-type');
    loadScrapTypes(select);

    // Add change event listener
    select.addEventListener('change', updateDisabledOptions);
  }

  // Disable already selected scrap types in other dropdowns
  function updateDisabledOptions() {
    const allSelects = document.querySelectorAll('.scrap-type');
    const selectedValues = Array.from(allSelects).map(s => s.value).filter(v => v);

    allSelects.forEach(select => {
      Array.from(select.options).forEach(opt => {
        if (opt.value && selectedValues.includes(opt.value) && opt.value !== select.value) {
          opt.disabled = true;
        } else {
          opt.disabled = false;
        }
      });
    });
  }

  // Validate quantity input based on selected unit
document.getElementById('scrapItemsContainer').addEventListener('input', function(e) {
  if (e.target.name === 'quantity[]') {
    const row = e.target.closest('.scrap-item');
    const select = row.querySelector('.scrap-type');
    const selectedText = select.options[select.selectedIndex]?.textContent || "";

    // Detect unit type (kg or piece)
    let unit = "kg";
    if (selectedText.toLowerCase().includes("(piece")) unit = "piece";

    const value = e.target.value.trim();

    if (unit === "kg") {
      // Must be > 0, decimals allowed
      if (parseFloat(value) <= 0) {
        e.target.value = "";
        Swal.fire({
          icon: "warning",
          title: "Invalid Weight",
          text: "Weight must be greater than 0 kg.",
          timer: 1800,
          showConfirmButton: false
        });
      }
    } else if (unit === "piece") {
      // Must be integer and > 0
      if (!/^\d+$/.test(value) || parseInt(value) <= 0) {
        e.target.value = "";
        Swal.fire({
          icon: "warning",
          title: "Invalid Quantity",
          text: "Please enter a valid whole number (no decimals) greater than 0.",
          timer: 1800,
          showConfirmButton: false
        });
      }
    }
  }
});


  // File count update
  function updateFileCount() {
    const input = document.getElementById('scrap_images');
    const count = input.files ? input.files.length : 0;
    document.getElementById('fileCount').textContent = count===0 ? 'No files selected' : `${count} file${count>1?'s':''} selected`;
  }

  // Init
  loadAddresses();
  loadScrapTypes(document.querySelector('.scrap-type'));
  updateFileCount();

  document.getElementById('addScrapBtn').addEventListener('click', addScrapRow);
  document.getElementById('scrapItemsContainer').addEventListener('click', function(e){
    const del = e.target.closest('.delete-btn');
    if(del){
      const row = del.closest('.scrap-item');
      const all = document.querySelectorAll('.scrap-item');
      if(all.length > 1) {
        row.remove();
        updateDisabledOptions();
      }
    }
  });
  document.getElementById('scrap_images').addEventListener('change', updateFileCount);

  // Submit form
  document.getElementById('scrapRequestForm').addEventListener('submit', function(e){
    e.preventDefault();
    const btn = this.querySelector('.submit-btn');
    const origHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Submitting...';

    const formData = new FormData(this);
    formData.append('action', 'submit_scrap_request');

    fetch('/ecocycle/user/privatehomepagemanager.php', {method:'POST', body: formData})
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
  Swal.fire({
    icon: 'success',
    title: 'Request Submitted',
    text: data.message || 'Your scrap pickup request has been submitted!',
    timer: 2000,
    showConfirmButton: false
  }).then(() => {
    window.location.href = 'user/homes.php?page=dashboard';
  });  
  this.reset();
  updateFileCount();
  updateDisabledOptions();
}
 else {
          Swal.fire({ icon: 'error', title: 'Submission Failed', text: data.message || 'Something went wrong. Please try again.' });
        }
      })
      .catch(()=>{ Swal.fire({ icon: 'error', title: 'Submission Failed', text: 'Unable to connect to server.' }); })
      .finally(()=>{ btn.disabled = false; btn.innerHTML = origHTML; });
  });
}
// =======================
// Settings Page
// =======================
function initSettingsPage() {
  // --------------------------
  // Toggle edit fields
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

  // --------------------------
  // Toggle password visibility
  // --------------------------
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

  // --------------------------
  // Validation helpers
  // --------------------------
  function validatePhone(phone) {
    return /^\d{10}$/.test(phone);
  }

  function validatePincode(pin) {
    return /^\d{6}$/.test(pin);
  }

  function validatePassword(password) {
    const minLength = 6;
    const hasLower = /[a-z]/.test(password);
    const hasUpper = /[A-Z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    return password.length >= minLength && hasLower && hasUpper && hasNumber && hasSpecial;
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
    indicator.style.color = type === 'error' ? '#dc3545' : '#28a745';
  }

  function clearInlineMessage(input) {
    const indicator = input.parentElement.querySelector('.validation-message');
    if (indicator) indicator.textContent = '';
  }

  // --------------------------
  // Load current profile info
  // --------------------------
  fetch('/ecocycle/user/privatehomepagemanager.php?action=get_profile')
    .then(res => res.json())
    .then(data => {
      if (data && data.status === 'success') {
        document.getElementById('username').value = data.data.name || '';
        document.getElementById('address').value = data.data.address || '';
        document.getElementById('pincode').value = data.data.pincode || '';
        document.getElementById('phone').value = data.data.phone || '';
        document.querySelector('#profileForm input[type="email"]').value = data.data.email || '';
      }
    });

  // --------------------------
  // Real-time field validations
  // --------------------------
  const usernameInput = document.getElementById('username');
  const phoneInput = document.getElementById('phone');
  const pincodeInput = document.getElementById('pincode');
  const newPasswordInput = document.getElementById('newPassword');
  const confirmPasswordInput = document.getElementById('confirmPassword');

  if (usernameInput) {
    usernameInput.addEventListener('input', () => {
      usernameInput.value.trim() === '' 
        ? showInlineMessage(usernameInput, 'Username cannot be empty') 
        : clearInlineMessage(usernameInput);
    });
  }

  if (phoneInput) {
    phoneInput.addEventListener('input', () => {
      validatePhone(phoneInput.value) 
        ? clearInlineMessage(phoneInput) 
        : showInlineMessage(phoneInput, 'Phone number must be 10 digits');
    });
  }

  if (pincodeInput) {
    pincodeInput.addEventListener('input', () => {
      validatePincode(pincodeInput.value) 
        ? clearInlineMessage(pincodeInput) 
        : showInlineMessage(pincodeInput, 'Pincode must be 6 digits');
    });
  }

  if (newPasswordInput && confirmPasswordInput) {
    newPasswordInput.addEventListener('input', () => {
      validatePassword(newPasswordInput.value) 
        ? clearInlineMessage(newPasswordInput) 
        : showInlineMessage(newPasswordInput, 'Password must be ≥6 chars, include uppercase, lowercase, number & special char');

      if (confirmPasswordInput.value) {
        newPasswordInput.value === confirmPasswordInput.value 
          ? clearInlineMessage(confirmPasswordInput) 
          : showInlineMessage(confirmPasswordInput, 'Passwords do not match');
      }
    });

    confirmPasswordInput.addEventListener('input', () => {
      newPasswordInput.value === confirmPasswordInput.value 
        ? clearInlineMessage(confirmPasswordInput) 
        : showInlineMessage(confirmPasswordInput, 'Passwords do not match');
    });
  }

  // --------------------------
  // Toggle edit & password buttons
  // --------------------------
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const fieldId = btn.closest('.input-group').querySelector('input, textarea').id;
      toggleEdit(fieldId);
    });
  });

  document.querySelectorAll('.password-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const fieldId = btn.closest('.input-group').querySelector('input').id;
      togglePassword(fieldId);
    });
  });

  // --------------------------
  // Submit handlers with SweetAlert
  // --------------------------
  const profileForm = document.getElementById('profileForm');
  if (profileForm) {
    profileForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const username = usernameInput.value.trim();
      const address = document.getElementById('address').value.trim();
      const pincode = pincodeInput.value.trim();
      const phone = phoneInput.value.trim();

      if (!username || !address || !pincode || !phone) return showError('All fields are required');
      if (!validatePhone(phone)) return showError('Phone number must be 10 digits');
      if (!validatePincode(pincode)) return showError('Pincode must be 6 digits');

      const formData = new FormData();
      formData.append('action','update_profile');
      formData.append('username', username);
      formData.append('address', address);
      formData.append('pincode', pincode);
      formData.append('phone', phone);

      fetch('/ecocycle/user/privatehomepagemanager.php', { method:'POST', body:formData })
        .then(res => res.json())
        .then(data => {
          data.status === 'success' ? showSuccess(data.message) : showError(data.message || 'Failed to update profile');
        });
    });
  }

  const passwordForm = document.getElementById('passwordForm');
  if (passwordForm) {
    passwordForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const current = document.getElementById('currentPassword').value.trim();
      const newPass = newPasswordInput.value.trim();
      const confirmPass = confirmPasswordInput.value.trim();

      if (!current || !newPass || !confirmPass) return showError('All password fields are required');
      if (newPass !== confirmPass) return showError('Passwords do not match!');
      if (!validatePassword(newPass)) return showError('Password must be ≥6 chars and include uppercase, lowercase, number & special char');

      const formData = new FormData();
      formData.append('action','change_password');
      formData.append('currentPassword', current);
      formData.append('newPassword', newPass);

      fetch('/ecocycle/user/privatehomepagemanager.php', { method:'POST', body:formData })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            passwordForm.reset();
            confirmPasswordInput.parentElement.querySelector('.validation-message').textContent = '';
            showSuccess(data.message);
          } else {
            showError(data.message || 'Failed to change password');
          }
        });
    });
  }
}
// =======================
// Dashboard Page
// =======================
function initDashboard() {
    const requestsContainer = document.getElementById('scrapRequests');
    const requestsLoading = document.getElementById('requestsLoading');
    const upiContainer = document.getElementById("upiContainer"); 
    const walletLoading = document.getElementById('walletLoading');
    const walletContent = document.getElementById('walletContent');
    const transactionsTable = document.getElementById("walletTransactions");

    const requestsPerPage = 5;
    let currentPage = 1;
    let requestsData = [];

    let paginationContainer = document.createElement('div');
    paginationContainer.className = 'd-flex justify-content-center mt-3';
    requestsContainer.parentNode.appendChild(paginationContainer);

    // Fetch username
    fetch('/ecocycle/user/privatehomepagemanager.php?action=get_username')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.username) {
                document.getElementById('userName').textContent = data.username;
            }
        });

    // Fetch dashboard data
    fetch("user/privatehomepagemanager.php?action=get_dashboard_data")
        .then(res => res.json())
        .then(data => {
            walletLoading.style.display = 'none';
            walletContent.style.display = 'block';
            if (!data.upi_id) {
                upiContainer.innerHTML = `
                    <form id="upiFormInline" class="d-flex align-items-center">
                        <input type="text" id="upiIdInline" class="form-control form-control-sm me-2" placeholder="Enter your UPI ID">
                        <button class="btn btn-sm btn-success" type="submit">Save</button>
                    </form>
                    <div id="upiMsgInline" class="mt-1 text-muted">Add your UPI ID to receive payments.</div>
                `;
                document.getElementById("upiFormInline").addEventListener("submit", function(e) {
                    e.preventDefault();
                    const newUpi = document.getElementById("upiIdInline").value.trim();
                    const upiMsgInline = document.getElementById("upiMsgInline");

                    if (!newUpi.match(/^[\w.\-]+@[\w]+$/)) {
                        upiMsgInline.innerHTML = `<span class='text-danger'>❌ Invalid UPI ID format</span>`;
                        return;
                    }

                    fetch("user/privatehomepagemanager.php?action=save_upi", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `upi_id=${encodeURIComponent(newUpi)}`
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.status === "success") {
                            upiMsgInline.innerHTML = `<span class='text-success'>✅ ${resp.message}</span>`;
                        } else {
                            upiMsgInline.innerHTML = `<span class='text-danger'>⚠ ${resp.message}</span>`;
                        }
                    })
                    .catch(err => {
                        console.error("UPI save error:", err);
                        upiMsgInline.innerHTML = `<span class='text-danger'>❌ Failed to save UPI ID</span>`;
                    });
                });

            } else {
                // Existing UPI: show current and change option
                upiContainer.innerHTML = `
                    <div class="mb-2">
                        <strong>UPI ID:</strong> <span id="currentUpi">${data.upi_id}</span>
                        <button class="btn btn-sm btn-outline-primary ms-2" id="changeUpiBtn">Change</button>
                    </div>
                    <div id="upiEditForm" style="display:none;" class="mt-2">
                        <form id="upiFormInlineEdit" class="d-flex align-items-center">
                            <input type="text" id="upiIdInlineEdit" class="form-control form-control-sm me-2" placeholder="Enter new UPI ID">
                            <button class="btn btn-sm btn-success" type="submit">Save</button>
                            <button class="btn btn-sm btn-secondary ms-2" type="button" id="cancelUpiBtn">Cancel</button>
                        </form>
                        <div id="upiMsgInlineEdit" class="mt-1"></div>
                    </div>
                `;

                const changeBtn = document.getElementById("changeUpiBtn");
                const cancelBtn = document.getElementById("cancelUpiBtn");
                const upiEditForm = document.getElementById("upiEditForm");
                const upiIdInlineEdit = document.getElementById("upiIdInlineEdit");
                const upiMsgInlineEdit = document.getElementById("upiMsgInlineEdit");

                changeBtn.addEventListener("click", () => {
                    upiEditForm.style.display = "block";
                    upiIdInlineEdit.value = data.upi_id;
                    changeBtn.style.display = "none";
                });

                cancelBtn.addEventListener("click", () => {
                    upiEditForm.style.display = "none";
                    changeBtn.style.display = "inline-block";
                    upiMsgInlineEdit.innerHTML = "";
                });

                document.getElementById("upiFormInlineEdit").addEventListener("submit", function(e) {
                    e.preventDefault();
                    const newUpi = upiIdInlineEdit.value.trim();

                    if (!newUpi.match(/^[\w.\-]+@[\w]+$/)) {
                        upiMsgInlineEdit.innerHTML = `<span class='text-danger'>❌ Invalid UPI ID format</span>`;
                        return;
                    }

                    fetch("user/privatehomepagemanager.php?action=save_upi", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `upi_id=${encodeURIComponent(newUpi)}`
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.status === "success") {
                            document.getElementById("currentUpi").textContent = newUpi;
                            upiMsgInlineEdit.innerHTML = `<span class='text-success'>✅ ${resp.message}</span>`;
                            upiEditForm.style.display = "none";
                            changeBtn.style.display = "inline-block";
                        } else {
                            upiMsgInlineEdit.innerHTML = `<span class='text-danger'>⚠ ${resp.message}</span>`;
                        }
                    })
                    .catch(err => {
                        console.error("UPI save error:", err);
                        upiMsgInlineEdit.innerHTML = `<span class='text-danger'>❌ Failed to save UPI ID</span>`;
                    });
                });
            }

            // --- Transactions ---
            if (Array.isArray(data.transactions) && data.transactions.length > 0) {
                transactionsTable.innerHTML = "";
                data.transactions.forEach(tx => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${tx.date}</td>
                        <td>${tx.request_code}</td>
                        <td><span class="badge bg-${tx.status === 'completed' ? 'success' : 'warning'}">${tx.status}</span></td>
                        <td class="text-end">₹${tx.amount}</td>
                    `;
                    transactionsTable.appendChild(row);
                });
            } else {
                transactionsTable.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No transactions found</td></tr>`;
            }

            // --- Scrap Requests ---
            requestsLoading.style.display = 'none';
            requestsContainer.innerHTML = "";

            if (!Array.isArray(data.requests) || data.requests.length === 0) {
                requestsContainer.innerHTML = `<p class="text-muted">No scrap requests found.</p>`;
                return;
            }

            requestsData = data.requests.map(r => ({
                id: parseInt(r.request_id),
                formattedId: "REQ" + r.request_id.toString().padStart(3, "0"),
                date: r.created_at,
                scrapType: r.scrap_name,
                status: r.status,
                reward: r.reward,
                pickupDate: r.pickup_date,
                pickupSlot: r.pickup_slot,
                address: r.address,
                scrap_items: [],
                collectorName: null,
                collectorPhone: null
            }));

            renderPage(currentPage);
        })
        .catch(err => {
            console.error("Error loading dashboard:", err);
            walletLoading.style.display = 'none';
            walletContent.style.display = 'block';
            transactionsTable.innerHTML = `<tr><td colspan="4" class="text-center text-danger">⚠ Failed to load data</td></tr>`;
            requestsLoading.style.display = 'none';
            requestsContainer.innerHTML = `<div class="alert alert-danger">⚠ Failed to load data.</div>`;
        });

    // --- Requests section functions (unchanged) ---
    function renderPage(page) {
        requestsContainer.innerHTML = "";
        const start = (page - 1) * requestsPerPage;
        const end = start + requestsPerPage;
        const pageRequests = requestsData.slice(start, end);

        pageRequests.forEach(req => {
            const statusClass = req.status === 'earned' ? 'success' :
                                req.status === 'collected' ? 'primary' : 'warning';

            const card = document.createElement('div');
            card.className = "card mb-3";
            card.dataset.requestId = req.id;

            card.innerHTML = `
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h6 class="mb-1">${req.scrapType}</h6>
                        <small class="text-muted">Request: ${req.date}</small><br>
                        <small class="text-muted">Pickup: ${req.pickupDate || '-'} | ${req.pickupSlot || '-'}</small>
                    </div>
                    <div class="mt-2 mt-sm-0 text-end">
                        <span class="badge bg-${statusClass} text-uppercase">${req.status}</span>
                        <button class="btn btn-sm btn-outline-success ms-2 details-btn">
                            Details
                        </button>
                    </div>
                </div>
            `;
            requestsContainer.appendChild(card);
        });

        renderPagination(page);
    }

    function renderPagination(page) {
        paginationContainer.innerHTML = "";
        const totalPages = Math.ceil(requestsData.length / requestsPerPage);
        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = 'btn btn-sm mx-1 ' + (i === page ? 'btn-success' : 'btn-outline-success');
            btn.textContent = i;
            btn.addEventListener('click', () => {
                currentPage = i;
                renderPage(i);
            });
            paginationContainer.appendChild(btn);
        }
    }

    requestsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('details-btn')) {
            const requestId = e.target.closest('.card').dataset.requestId;
            showRequestDetails(requestId);
        }
    });

    window.showRequestDetails = function(requestId) {
        const request = requestsData.find(r => r.id === parseInt(requestId));
        if (!request) return;

        fetch(`user/privatehomepagemanager.php?action=get_request_items&request_id=${requestId}`)
            .then(res => res.json())
            .then(data => {
                request.scrap_items = data.items || [];
                request.collectorName = data.collectorName || null;
                request.collectorPhone = data.collectorPhone || null;
                renderModal(request);
            })
            .catch(err => {
                console.error("Error loading items:", err);
                request.scrap_items = [];
                renderModal(request);
            });
    };

    function renderModal(request) {
        const modalBody = document.getElementById("modalBody");
        let reviewSection = "";

        if (request.status === "earned") {
            reviewSection = `
                <div class="mt-4 p-3 border rounded">
                    <h6 class="mb-3">Rate Your Experience (Optional)</h6>
                    <div class="review-stars mb-3" id="reviewStars">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                    <textarea class="form-control mb-3" placeholder="Share your feedback (optional)" rows="3" id="reviewMessage"></textarea>
                    <button class="btn btn-primary btn-sm" onclick="submitReview('${request.id}')">Submit Review</button>
                </div>
            `;
        }

        let scrapHtml = "";
        let totalReward = 0;

        if (request.scrap_items.length > 0) {
            request.scrap_items.forEach(item => {
                totalReward += parseFloat(item.reward) || 0;
                scrapHtml += `
                    <div class="mb-2">
                        <strong>${item.name || "N/A"}</strong><br>
                        Weight/Qty: ${item.quantity || "N/A"} ${item.unit || ""}<br>
                        ${request.status === "earned" ? `Reward: ₹${item.reward}` : ""}
                    </div>
                `;
            });
        } else {
            scrapHtml = "<p>No scrap items found.</p>";
        }
        
        modalBody.innerHTML = `
            <p><strong>Request ID:</strong> ${request.formattedId}</p>
            <p><strong>Request date:</strong> ${request.date}</p>
            <p><strong>Pickup date:</strong> ${request.pickupDate} | ${request.pickupSlot}</p>
            <p><strong>Address:</strong> ${request.address}</p>
            ${request.collectorName ? `<p><strong>Collector:</strong> ${request.collectorName} (${request.collectorPhone || 'N/A'})</p>` : ""}
            <p><strong>Status:</strong> <span class="badge bg-${request.status === 'earned' ? 'success' : request.status === 'collected' ? 'primary' : 'warning'} text-uppercase">${request.status}</span></p>
            
            
            <hr>
            <h6>Scrap Items:</h6>
            ${scrapHtml}
            <p><strong>Total Reward: ₹${totalReward}</strong></p>
            ${reviewSection}
        `;

        new bootstrap.Modal(document.getElementById("requestDetailsModal")).show();
    }

    window.submitReview = function(requestId) {
        const selectedStars = document.querySelectorAll("#reviewStars .star.selected");
        const rating = selectedStars.length ? selectedStars[selectedStars.length - 1].dataset.rating : 0;
        const message = document.getElementById("reviewMessage")?.value || "";

        fetch("user/privatehomepagemanager.php?action=submit_review", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `request_id=${requestId}&rating=${rating}&message=${encodeURIComponent(message)}`
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message || "Review submitted!");
            if (data.status === "success") {
                const modal = bootstrap.Modal.getInstance(document.getElementById('requestDetailsModal'));
                modal.hide();
            }
        })
        .catch(err => {
            console.error("Review submit error:", err);
            alert("Failed to submit review!");
        });
    };

    document.addEventListener("click", (e) => {
        if (e.target.classList.contains("star") && e.target.closest("#reviewStars")) {
            const value = e.target.dataset.rating;
            e.target.closest("#reviewStars").querySelectorAll(".star").forEach(s => {
                s.classList.toggle("selected", s.dataset.rating <= value);
            });
        }
    });
}
// =======================
function initNotificationsPage() {
    const notificationsList = document.getElementById('notificationsList');
    const markAllBtn = document.getElementById('markAllBtn');
    const countEl = document.getElementById('notificationCount');

    if (!notificationsList) return; // container must exist

    const baseURL = 'user/privatehomepagemanager.php'; // use consistent path

    // -------------------------- Fetch notifications --------------------------
    function getNotifications() {
    Promise.all([
        fetch(`${baseURL}?action=get_notifications`).then(res => res.ok ? res.json() : Promise.reject("Failed to load notifications")),
        fetch(`${baseURL}?action=get_user_register_date`).then(res => res.ok ? res.json() : Promise.reject("Failed to load register date"))
    ])
    .then(([notifications, userData]) => {
        const registerDate = userData.register_date ? new Date(userData.register_date) : null;

        // Filter notifications newer than register_date
        const filtered = notifications.filter(n => {
            const nDate = new Date(n.created_at);
            return !registerDate || nDate >= registerDate;
        });

        renderNotifications(filtered);
        updateNotificationCount();
    })
    .catch(err => {
        console.error(err);
        notificationsList.innerHTML = `<p class="text-danger text-center">⚠️ Error loading notifications</p>`;
    });
}


    // -------------------------- Render --------------------------
    function renderNotifications(data) {
        notificationsList.innerHTML = "";
        if (!data || data.length === 0) return showEmptyState();

        data.forEach(n => {
            const item = document.createElement("div");
            item.className = `notification-item list-group-item d-flex justify-content-between align-items-center ${n.is_read ? "" : "unread"}`;
            item.dataset.id = n.id;

            item.innerHTML = `
                <div><p class="mb-0 small text-muted">${n.message}</p></div>
                <div class="d-flex align-items-center">
                <small class="text-muted">${n.created_at}</small>
                </div>
                ${!n.is_read ? `<span class="unread-badge badge bg-success me-2">New</span>` : ""}
                </div>
            `;
            notificationsList.appendChild(item);
        });
    }

    // -------------------------- Update count --------------------------
    function updateNotificationCount() {
        const unreadCount = notificationsList.querySelectorAll('.notification-item.unread').length;
        countEl.textContent = unreadCount;
        countEl.style.display = unreadCount > 0 ? 'inline-block' : 'none';
        if (notificationsList.children.length === 0) showEmptyState();
    }

    // -------------------------- Empty state --------------------------
    function showEmptyState() {
        notificationsList.innerHTML = `
            <div class="empty-state text-center py-5">
                <i class="fas fa-bell-slash fa-2x mb-3"></i>
                <h5>All caught up!</h5>
                <p>You have no notifications.</p>
            </div>
        `;
    }

    // -------------------------- Mark all as read --------------------------
    if (markAllBtn) {
        markAllBtn.addEventListener('click', () => {
            const unreadItems = notificationsList.querySelectorAll('.notification-item.unread');
            if (unreadItems.length === 0) return;

            markAllBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>Marking as read...`;
            markAllBtn.disabled = true;

            fetch(`${baseURL}?action=mark_all_read`, { method: 'POST' })
                .then(res => res.ok ? res.json() : Promise.reject("Failed"))
                .then(() => {
                    unreadItems.forEach((item, i) => {
                        setTimeout(() => { 
                            const badge = item.querySelector('.unread-badge');
                            if (badge) badge.style.display = 'none'; 
                            item.classList.remove('unread'); 
                        }, i*100);
                    });
                    setTimeout(updateNotificationCount, unreadItems.length*100 + 300);
                    setTimeout(() => { 
                        markAllBtn.innerHTML = `<i class="fas fa-check-double me-2"></i>Mark All as Read`; 
                        markAllBtn.disabled = false; 
                    }, 1500);
                })
                .catch(err => { 
                    console.error(err); 
                    markAllBtn.innerHTML = `Try Again`; 
                    markAllBtn.disabled = false; 
                });
        });
    }

    // -------------------------- Mark single read --------------------------
    notificationsList.addEventListener('click', e => {
        const item = e.target.closest('.notification-item.unread');
        if (!item) return;
        const notificationId = item.dataset.id;

        fetch(`${baseURL}?action=mark_read`, {
            method: 'POST',
            headers: { 'Content-Type':'application/json' },
            body: JSON.stringify({ notification_id: notificationId })
        })
        .then(res => res.ok ? res.json() : Promise.reject("Failed"))
        .then(() => { 
            const badge = item.querySelector('.unread-badge'); 
            if (badge) badge.style.display='none'; 
            item.classList.remove('unread'); 
            updateNotificationCount(); 
        })
        .catch(err => console.error(err));
    });

    // -------------------------- Init --------------------------
    getNotifications();
}

// =======================
// Logout
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
      window.location.href = 'user/logout.php';
    }
  });
}
</script>
</body>
</html>
