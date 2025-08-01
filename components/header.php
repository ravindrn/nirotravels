<?php
// Start session and connect to database
require_once 'admin/includes/db.php';

// Fetch active tour packages from database
$packages_query = "SELECT id, title FROM tour_packages WHERE is_active = 1 ORDER BY title ASC";
$packages_result = mysqli_query($conn, $packages_query);
$active_packages = [];
while ($row = mysqli_fetch_assoc($packages_result)) {
    $active_packages[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Niro SL Travels</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  
  <style>
    .navbar {
      padding: 15px 0;
      transition: all 0.3s ease;
    }
    .navbar-brand img {
      height: 40px;
      transition: all 0.3s ease;
    }
    .nav-link {
      color: #333;
      font-weight: 500;
      margin: 0 15px;
      position: relative;
    }
    .nav-link:hover {
      color: #0c2261;
    }
    .nav-link:after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 0;
      left: 0;
      background-color: #0c2261;
      transition: width 0.3s ease;
    }
    .nav-link:hover:after {
      width: 100%;
    }
    .dropdown-menu {
      border: none;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border-radius: 10px;
    }
    .dropdown-item {
      padding: 8px 20px;
    }
    .dropdown-item:hover {
      background-color: #f8f9fa;
      color: #0c2261;
    }
    .book-now-btn {
      background: #0c2261;
      border-radius: 50px;
      padding: 8px 20px;
      margin-left: 15px;
      transition: all 0.3s ease;
    }
    .book-now-btn:hover {
      background: #1a3a8f;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand" href="index.php">
      <img src="assets/img/logo/1.png" alt="Niro SL Travels">
    </a>
    
    <!-- Mobile Toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Navigation -->
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>
        
        <!-- Dynamic Tour Packages Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="packages.php" id="tourPackagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Tour Packages
          </a>
          <ul class="dropdown-menu" aria-labelledby="tourPackagesDropdown">
            <?php if (!empty($active_packages)): ?>
              <?php foreach ($active_packages as $package): ?>
                <li>
                  <a class="dropdown-item" href="packages.php?id=<?= $package['id'] ?>">
                    <i class="bi bi-geo-alt me-2"></i><?= htmlspecialchars($package['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
              <li><hr class="dropdown-divider"></li>
            <?php else: ?>
              <li><a class="dropdown-item disabled">No packages available</a></li>
            <?php endif; ?>
            <li><a class="dropdown-item" href="packages.php">
              <i class="bi bi-briefcase me-2"></i>View All Packages
            </a></li>
          </ul>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="accomodations.php">Accomodations</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="gallery.php">Gallery</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="aboutus.php">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contact.html">Contact</a>
        </li>
        
        <!-- Book Now Button with Dropdown -->
        <li class="nav-item dropdown ms-2">
          <a class="btn btn-primary book-now-btn dropdown-toggle" href="#" id="bookNowDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Book Now
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bookNowDropdown">
            <li><h6 class="dropdown-header">Booking Options</h6></li>
            <li>
              <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#quickBookModal">
                <i class="bi bi-lightning-charge me-2"></i>Quick Inquiry
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="packages.php">
                <i class="bi bi-briefcase me-2"></i>Browse Packages
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="custom-trip.php" onclick="startCustomTrip()">
                <i class="bi bi-pencil-square me-2"></i>Custom Trip
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="contact.html">
                <i class="bi bi-headset me-2"></i>Contact Agent
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Quick Book Modal -->
<div class="modal fade" id="quickBookModal" tabindex="-1" aria-labelledby="quickBookModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quickBookModalLabel">Quick Booking Inquiry</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="process_quick_inquiry.php" method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label for="quickPackage" class="form-label">Select Package</label>
            <select class="form-select" id="quickPackage" name="package_id" required>
              <option value="" selected disabled>Choose a package</option>
              <?php foreach ($active_packages as $package): ?>
                <option value="<?= $package['id'] ?>"><?= htmlspecialchars($package['title']) ?></option>
              <?php endforeach; ?>
              <option value="custom">Custom Trip</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="quickName" class="form-label">Your Name</label>
            <input type="text" class="form-control" id="quickName" name="name" required>
          </div>
          <div class="mb-3">
            <label for="quickEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="quickEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="quickPhone" class="form-label">Phone/WhatsApp</label>
            <input type="tel" class="form-control" id="quickPhone" name="phone" required>
          </div>
          <div class="mb-3">
            <label for="quickDates" class="form-label">Travel Dates</label>
            <input type="text" class="form-control" id="quickDates" name="travel_dates" 
                   placeholder="Preferred dates or flexible">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit Inquiry</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Load Bootstrap JS only once - should be in your main template, not header -->


<script>
  // Function to scroll to booking section and open custom trip builder
  function startCustomTrip() {
    // Close any open dropdowns
    var dropdowns = document.querySelectorAll('.dropdown-menu');
    dropdowns.forEach(function(dropdown) {
      dropdown.classList.remove('show');
    });
    
    // Scroll to booking section if it exists
    var bookingSection = document.getElementById('booking');
    if (bookingSection) {
      bookingSection.scrollIntoView({
        behavior: 'smooth'
      });
      
      // Trigger custom trip builder (if on same page)
      setTimeout(function() {
        var startBtn = document.getElementById('startBuildingBtn');
        if (startBtn) startBtn.click();
      }, 800);
    } else {
      window.location.href = 'index.php#booking';
    }
  }
</script>
