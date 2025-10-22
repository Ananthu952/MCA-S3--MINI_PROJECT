<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EcoCycle - Smart Scrap Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php if (isset($_GET['logout']) && $_GET['logout'] == 'success'): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Logged Out',
      text: 'You have been logged out successfully!',
      confirmButtonColor: '#2e7d32'
    });
  </script>
<?php endif; ?>
<!-- Navbar -->
<header class="navbar navbar-expand-lg bg-white px-4 py-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="#home">
      <img src="images/logo.svg" alt="EcoCycle Logo" class="logo-icon">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto me-3">
        <li class="nav-item mx-2"><a class="nav-link" href="#areas"><h5>Our Services Areas</h5></a></li>
        <li class="nav-item mx-2"><a class="nav-link" href="#prices"><h5>Price</h5></a></li>
        <li class="nav-item mx-2"><a class="nav-link" href="#services"><h5>How it work</h5></a></li>
        <li class="nav-item mx-2"><a class="nav-link" href="#contact"><h5>Contact</h5></a></li>
      </ul>
      <button class="btn btn-outline-success" onclick="window.location.href='auth.php'">Sign In</button>
    </div>
  </div>
</header>

<!-- Hero Section -->
<section id="home" class="container py-5">
  <div class="row align-items-center">
    <div class="col-md-6">
      <h1 class="display-5 fw-bold">Promote<br>Smart Scrap Management</h1>
      <p class="lead">Request a pickup for your scrap materials and contribute to a cleaner environment. EcoCycle makes scrap collection simple and rewarding.</p>
      <button class="btn btn-warning" onclick="window.location.href='auth.php'">Request Pickup</button>
    </div>
    <div class="col-md-6 text-center">
      <div class="hero-image-container visible" id="heroImage">
        <img src="images/hero.png" alt="Hero Image" class="img-fluid">
      </div>
    </div>
  </div>
</section>

<!-- Scrap Prices of the Day -->
<section id="prices" class="py-5 text-center bg-light">
  <div class="container">
    <h2 class="mb-4">Scrap Prices of the Day</h2>
    <div id="scrap-prices" class="row g-4 justify-content-center">
      <!-- Dynamic data loads here -->
    </div>
  </div>
</section>

<!-- Service Areas Section -->
<section id="areas" class="py-5 text-center">
  <div class="container">
    <h2 class="mb-4">Our Service Areas</h2>
    <p class="text-muted mb-5">EcoCycle is available in the following locations. Enter your pincode during signup to check service availability.</p>
    <div id="service-areas" class="row g-3 justify-content-center">
      <?php
      require_once 'db.php'; // make sure this path is correct

      // Fetch distinct pincodes from tbl_collector_area
      $query = "SELECT DISTINCT pincode FROM tbl_collector_area ORDER BY pincode ASC";
      $result = $conn->query($query);

      if($result && $result->num_rows > 0){
          while($row = $result->fetch_assoc()){
              echo '<div class="col-6 col-md-3 col-lg-2">
                      <div class="p-3 bg-white rounded shadow-sm">
                        <h5>'.$row['pincode'].'</h5>
                      </div>
                    </div>';
          }
      } else {
          echo '<p class="text-muted">No service areas available yet.</p>';
      }
      ?>
    </div>
  </div>
</section>

<!-- How It Works Section -->
<section id="services" class="py-5 bg-light text-center">
  <div class="container">
    <h2 class="mb-3">How It Works</h2>
    <p class="text-muted mb-5">Simple steps to start recycling and help the environment</p>
    <div class="row g-4">
      <div class="col-6 col-md-4 col-lg step-card">
        <div class="p-3 bg-white rounded shadow-sm">
          <div class="icon mb-3 mx-auto">
            <img src="images/calender.png" class="img-fluid" alt="Schedule a Pickup">
          </div>
          <h5>Schedule a Pickup</h5>
          <p>Book a pickup time that suits you easily through our platform.</p>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg step-card">
        <div class="p-3 bg-white rounded shadow-sm">
          <div class="icon mb-3 mx-auto">
            <img src="images/scrap.png" class="img-fluid" alt="Sort Your Scrap">
          </div>
          <h5>Sort Your Scrap</h5>
          <p>Separate and prepare your scrap materials for pickup by our collector.</p>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg step-card">
        <div class="p-3 bg-white rounded shadow-sm">
          <div class="icon mb-3 mx-auto">
            <img src="images/truck.png" class="img-fluid" alt="Track Pickup Progress">
          </div>
          <h5>Track Pickup Progress</h5>
          <p>Track the status of your scrap pickup in real-time from your dashboard.</p>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg step-card">
        <div class="p-3 bg-white rounded shadow-sm">
          <div class="icon mb-3 mx-auto">
            <img src="images/reward.png" class="img-fluid" alt="Earn Rewards">
          </div>
          <h5>Earn Rewards</h5>
          <p>Get incentives based on the type and quantity of scrap you provide.</p>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg step-card">
        <div class="p-3 bg-white rounded shadow-sm">
          <div class="icon mb-3 mx-auto">
            <img src="images/notify.png" class="img-fluid" alt="Get Notified">
          </div>
          <h5>Get Notified</h5>
          <p>Receive updates and notifications throughout the scrap collection process.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer id="contact" class="bg-success text-white py-4">
  <div class="container d-flex flex-wrap justify-content-between">
    <div class="footer-left">
      <h4>About EcoCycle</h4>
      <p>EcoCycle is committed to making scrap collection accessible and transparent, promoting sustainability.</p>
    </div>
    <div class="footer-right">
      <h4>Contact</h4>
      <ul class="list-unstyled">
        <li>Email: support@ecocycle.com</li>
        <li>Phone: +91 12345 67890</li>
        <li>Address: 123 Green St, City, India</li>
      </ul>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Smooth scrolling for navbar links
document.querySelectorAll('a.nav-link').forEach(link => {
  link.addEventListener('click', function(e) {
    const targetId = this.getAttribute('href').substring(1);
    const target = document.getElementById(targetId);
    if(target) {
      e.preventDefault();
      window.scrollTo({
        top: target.offsetTop - 70, // adjust for navbar height
        behavior: 'smooth'
      });
    }
  });
});

// Fetch Scrap Prices
document.addEventListener("DOMContentLoaded", function () {
  fetch("fetch_data.php") // adjust path if necessary
    .then(response => response.json())
    .then(data => {
      const pricesContainer = document.getElementById("scrap-prices");
      pricesContainer.innerHTML = ""; // clear before adding
      if (data.prices.length > 0) {
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
      document.getElementById("scrap-prices").innerHTML = "<p>Error loading prices</p>";
    });
});



// Hero image animation
document.addEventListener('DOMContentLoaded', () => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target); // Run only once
      }
    });
  }, { threshold: 0.5 });

  const hero = document.getElementById('heroImage');
  if (hero) observer.observe(hero);
});
</script>
</body>
</html>
