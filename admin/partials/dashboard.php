<section class="container py-5">
  <div class="row align-items-center">
    <div class="col-md-6">
      <h1 class="display-5 fw-bold">Welcome Admin!</h1>
      <p class="lead">Monitor users, collectors, scrap requests, and payments — everything you need to manage EcoCycle effectively.</p>
      
    </div>
    <div class="col-md-6 text-center">
      <img src="images/admin.png" class="img-fluid" alt="Admin Dashboard">
    </div>
  </div>
</section>

<!-- Dashboard Section -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="mb-4 text-center">Admin Control Panel</h2>
    <div class="row g-4">
      
      <!-- Manage Users -->
      <div class="col-md-3">
        <div class="dashboard-card" onclick="window.location.href='admin/admin_home.php?page=user'">
          <i class="fa-solid fa-users dashboard-icon text-primary"></i>
          <h5>Manage Users</h5>
        </div>
      </div>

      <!-- Manage Collectors -->
      <div class="col-md-3">
        <div class="dashboard-card" onclick="window.location.href='admin/admin_home.php?page=collector'">
          <i class="fa-solid fa-user-tie dashboard-icon text-success"></i>
          <h5>Manage Collectors</h5>
        </div>
      </div>

      <!-- Scrap Requests -->
      <div class="col-md-3">
        <div class="dashboard-card" onclick="window.location.href='admin/admin_home.php?page=request'">
          <i class="fa-solid fa-truck dashboard-icon text-warning"></i>
          <h5>Scrap Requests</h5>
        </div>
      </div>

      <!-- Payments -->
      <div class="col-md-3">
        <div class="dashboard-card" onclick="window.location.href='admin/admin_home.php?page=payment'">
          <i class="fa-solid fa-wallet dashboard-icon text-danger"></i>
          <h5>Payments</h5>
        </div>
      </div>

      <!-- Scrap Types -->
      <div class="col-md-3">
        <div class="dashboard-card" onclick="window.location.href='admin/admin_home.php?page=scrap'">
          <i class="fa-solid fa-recycle dashboard-icon text-info"></i>
          <h5>Scrap Types</h5>
        </div>
      </div>

      <!-- Notifications -->
      <div class="col-md-3">
        <div class="dashboard-card" onclick="window.location.href='admin/admin_home.php?page=notifications'">
          <i class="fa-solid fa-bell dashboard-icon text-secondary"></i>
          <h5>Notifications</h5>
        </div>
      </div>

      <!-- Feedback & Reports -->
      <!-- <div class="col-md-3">
        <div class="dashboard-card" onclick="window.location.href='admin/admin_home.php?page=feedback'">
          <i class="fa-solid fa-chart-line dashboard-icon text-dark"></i>
          <h5>Feedback & Reports</h5>
        </div>
      </div> -->

      <!-- Admin Settings -->
      <div class="col-md-3">
        <div class="dashboard-card" onclick="window.location.href='admin/admin_home.php?page=settings'">
          <i class="fa-solid fa-gear dashboard-icon text-muted"></i>
          <h5>Settings</h5>
        </div>
      </div>

    </div>
  </div>
</section>