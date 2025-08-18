<?php
include 'admin/includes/db.php'; // adjust path if needed

$sql = "SELECT * FROM destinations WHERE is_active = 1 ORDER BY sort_order ASC, id ASC";
$result = mysqli_query($conn, $sql);
$first = true;

$popularResult = mysqli_query($conn, "SELECT * FROM destinations WHERE is_popular = 1 ORDER BY sort_order ASC");
$popularDestinations = [];
while ($row = mysqli_fetch_assoc($popularResult)) {
    $popularDestinations[] = $row;
}



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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>Niro SL Travels</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    /* Custom Styles */
    :root {
      --primary-color: #0c2261;
      --secondary-color: #1a3a8f;
      --text-light: #ffffff;
      --text-dark: #333333;
    }
    
    /* Navbar Styles */
    .navbar {
      padding: 8px 0;
      transition: all 0.3s ease;
      background-color: transparent !important;
      position: absolute;
      width: 100%;
      top: 0;
      left: 0;
      z-index: 1050;
      min-height: 50px;
    }

    .navbar .container-fluid {
      padding-right: 12px;
      padding-left: 12px;
    }
 
    .navbar.scrolled {
      background-color: rgba(255, 255, 255, 0.95) !important;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }
    
    .navbar-brand img {
      height: 32px;
      transition: all 0.3s ease;
    }

    /* Nav Items */
    .nav-item {
      background-color: transparent;
      border: 1px solid white;
      margin-right: 8px;
      border-radius: 15px;
      transition: all 0.3s ease;
    }

    .navbar.scrolled .nav-item {
      border-color: rgba(0, 0, 0, 0.1);
    }

    /* Nav Container */
    #mainNav {
      display: flex;
      justify-content: flex-end;
    }
    
    /* Nav Links */
    .nav-link {
      color: var(--text-light) !important;
      font-weight: 500;
      padding: 6px 12px !important;
      position: relative;
      transition: all 0.3s ease;
      border-radius: 15px;
    }
    
    .navbar.scrolled .nav-link {
      color: var(--text-dark) !important;
    }
    
    .nav-link:hover {
      color: var(--text-light) !important;
      background-color: rgba(255, 255, 255, 0.2);
    }
    
    .navbar.scrolled .nav-link:hover {
      color: var(--primary-color) !important;
      background-color: rgba(12, 34, 97, 0.05);
    }
    
    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 5px;
      left: 12px;
      background-color: var(--text-light);
      transition: width 0.3s ease;
    }
    
    .navbar.scrolled .nav-link::after {
      background-color: var(--primary-color);
    }
    
    .nav-link:hover::after {
      width: calc(100% - 24px);
    }
    
    /* Book Now Button */
    .book-now-btn {
      background-color: var(--text-light) !important;
      color: var(--primary-color) !important;
      border-radius: 50px;
      padding: 6px 16px !important;
      font-weight: 500;
      transition: all 0.3s ease;
      border: none;
      font-size: 0.9rem;
    }
    
    .book-now-btn:hover {
      background-color: var(--text-light) !important;
      color: var(--secondary-color) !important;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Dropdown Styles */
    .dropdown-menu {
      border: none;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      margin-top: 5px;
    }
    
    .dropdown-item {
      padding: 8px 16px;
      transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
      background-color: rgba(12, 34, 97, 0.05);
      color: var(--primary-color);
    }
    
    /* Mobile Menu Styles */
    .navbar-toggler {
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 4px;
      padding: 0.25rem 0.4rem !important;
      margin-left: auto;
      margin-right: 0;
      border: none;
    }

    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
      width: 1.1rem;
      height: 1.1rem;
    }

    .navbar.scrolled .navbar-toggler {
      background-color: rgba(12, 34, 97, 0.1);
    }
    
    .navbar.scrolled .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(12, 34, 97, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    
    .navbar-toggler:focus {
      box-shadow: none;
    }

    /* Sidebar Styles */
    .offcanvas {
      background-color: rgba(255, 255, 255, 0.98);
      transition: transform 0.3s ease-in-out;
      visibility: hidden;
    }
    
    .offcanvas.show {
      visibility: visible;
    }
    
    .offcanvas-header {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .offcanvas-body {
      padding: 0.75rem 1rem;
    }
    
    .offcanvas .nav-item {
      margin-right: 0;
      margin-bottom: 6px;
      border-color: rgba(0, 0, 0, 0.1);
      width: 100%;
    }
    
    .offcanvas .nav-link {
      color: var(--text-dark) !important;
      padding: 8px 12px !important;
    }
    
    .offcanvas .nav-link::after {
      background-color: var(--primary-color) !important;
      bottom: 6px;
    }
    
    .offcanvas .book-now-btn {
      margin-top: 8px;
      width: 100%;
      text-align: center;
    }
    
    .offcanvas .dropdown-menu {
      position: static !important;
      transform: none !important;
      border: none;
      box-shadow: none;
      margin-top: 0;
      padding-left: 12px;
    }
    
    .offcanvas .dropdown-item {
      padding: 6px 12px;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
      #mainNav {
        display: none;
      }
      
      .navbar-toggler {
        display: block;
      }
      
      .offcanvas {
        visibility: hidden;
      }
      
      .offcanvas.show {
        visibility: visible;
      }
    }

    @media (min-width: 993px) {
      .navbar-toggler {
        display: none;
      }
      
      #mainNav {
        display: flex;
      }
      
      .offcanvas {
        visibility: hidden !important;
      }
    }
    
    @media (max-width: 768px) {
      .navbar-brand img {
        height: 30px;
      }
      
      .hero-title {
        font-size: 2rem;
      }
      
      .hero-subtitle {
        font-size: 1.5rem;
      }
      
      .hero-book-btn {
        padding: 10px 20px;
        font-size: 1rem;
      }
    }

    @media (max-width: 375px) {
      .navbar {
        padding: 6px 0;
        min-height: 44px;
      }
      
      .navbar .container-fluid {
        padding-right: 8px;
        padding-left: 8px;
      }
      
      .navbar-brand img {
        height: 28px;
      }
      
      .navbar-toggler {
        padding: 0.2rem 0.35rem !important;
      }
      
      .navbar-toggler-icon {
        width: 1rem;
        height: 1rem;
      }
    }

    /* Hero Section Styles */
    .hero-section {
      position: relative;
      height: 100vh;
      min-height: 650px;
      overflow: hidden;
    }
    
    .carousel-item {
      height: 100%;
    }
    
    .carousel-item img {
      height: 100%;
      width: 100%;
      object-fit: cover;
    }
    
    .hero-content {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: flex-start;
      padding: 0 5%;
      background: rgba(0, 0, 0, 0.4);
      color: var(--text-light);
      z-index: 2;
    }
    
    .hero-title {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .hero-subtitle {
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .hero-text {
      font-size: 1.2rem;
      max-width: 600px;
      margin-bottom: 2rem;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }
    
    .hero-book-btn {
      background: var(--primary-color);
      border-radius: 50px;
      padding: 12px 30px;
      font-size: 1.1rem;
      font-weight: 500;
      color: var(--text-light);
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .hero-book-btn:hover {
      background: var(--secondary-color);
      transform: translateY(-2px);
      color: var(--text-light);
    }
    
    /* Responsive Adjustments */
    @media (max-width: 992px) {
      .hero-title {
        font-size: 2.5rem;
      }
      
      .hero-subtitle {
        font-size: 1.8rem;
      }
      
      .hero-text {
        font-size: 1rem;
      }
    }
    
    @media (max-width: 768px) {
      .hero-title {
        font-size: 2rem;
      }
      
      .hero-subtitle {
        font-size: 1.5rem;
      }
      
      .hero-book-btn {
        padding: 10px 20px;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container-fluid d-flex align-items-center">
      <!-- Logo -->
      <a class="navbar-brand" href="index.php">
        <img src="assets/img/logo/1.png" alt="Niro SL Travels">
      </a>

      <!-- Hamburger (mobile only) -->
      <button class="navbar-toggler d-lg-none" type="button" 
        data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" 
        aria-controls="mobileMenu" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Desktop nav -->
      <div class="collapse navbar-collapse d-none d-lg-block" id="mainNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>

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

          <li class="nav-item"><a class="nav-link" href="accomodations.php">Accommodations</a></li>
          <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
          <li class="nav-item"><a class="nav-link" href="aboutus.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
          
          <!-- Book Now Button -->
          <li class="nav-item dropdown ms-lg-3 mt-3 mt-lg-0">
            <a class="btn book-now-btn dropdown-toggle" href="#" id="bookNowDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Book Now
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bookNowDropdown">
              <li><h6 class="dropdown-header">Booking Options</h6></li>
              <li><a class="dropdown-item" href="packages.php"><i class="bi bi-briefcase me-2"></i>Browse Packages</a></li>
              <li><a class="dropdown-item" href="custom-trip.php" onclick="startCustomTrip()"><i class="bi bi-pencil-square me-2"></i>Custom Trip</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="contact.php"><i class="bi bi-headset me-2"></i>Contact Agent</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Mobile offcanvas -->
  <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">
        <img src="assets/img/logo/1.png" alt="Niro SL Travels" height="32">
      </h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>

        <!-- Dynamic Tour Packages Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="mobileTourPackagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Tour Packages
          </a>
          <ul class="dropdown-menu" aria-labelledby="mobileTourPackagesDropdown">
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
            <li><a class="dropdown-item" href="packages.php"><i class="bi bi-briefcase me-2"></i>View All Packages</a></li>
          </ul>
        </li>

        <li class="nav-item"><a class="nav-link" href="accomodations.php">Accommodations</a></li>
        <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="aboutus.php">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>

        <!-- Book Now Button -->
        <li class="nav-item dropdown mt-3">
          <a class="btn book-now-btn dropdown-toggle w-100" href="#" id="mobileBookNowDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Book Now
          </a>
          <ul class="dropdown-menu w-100" aria-labelledby="mobileBookNowDropdown">
            <li><h6 class="dropdown-header">Booking Options</h6></li>
            <li><a class="dropdown-item" href="packages.php"><i class="bi bi-briefcase me-2"></i>Browse Packages</a></li>
            <li><a class="dropdown-item" href="custom-trip.php" onclick="startCustomTrip()"><i class="bi bi-pencil-square me-2"></i>Custom Trip</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="contact.php"><i class="bi bi-headset me-2"></i>Contact Agent</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>

  <!-- Hero Section -->
  <section class="hero-section">
    <div id="destinationCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="5000">
      <div class="carousel-inner h-100">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <div class="carousel-item h-100 <?= $first ? 'active' : '' ?>">
            <img src="assets/img/destinations/<?= htmlspecialchars($row['image']) ?>" class="d-block w-100 h-100" alt="<?= htmlspecialchars($row['title_line2']) ?>">
            <div class="hero-content">
              <h1 class="hero-title" data-aos="fade-right"><?= htmlspecialchars($row['title_line1']) ?></h1>
              <h2 class="hero-subtitle" data-aos="fade-right" data-aos-delay="100"><?= htmlspecialchars($row['title_line2']) ?></h2>
              <p class="hero-text" data-aos="fade-up" data-aos-delay="200">
                <?= nl2br(htmlspecialchars($row['description'])) ?>
              </p>
              <div class="dropdown" data-aos="fade-up" data-aos-delay="300">
                <a class="hero-book-btn dropdown-toggle" href="#" role="button" id="heroBookDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  Book Now
                </a>
                <ul class="dropdown-menu" aria-labelledby="heroBookDropdown">
                  <li><h6 class="dropdown-header">Booking Options</h6></li>
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
              </div>
            </div>
          </div>
          <?php $first = false; ?>
        <?php } ?>
      </div>
      <!-- Carousel Controls -->
      <button class="carousel-control-prev" type="button" data-bs-target="#destinationCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#destinationCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </section>


  <!-- Hero Section -->
  <section class="hero-section">
    <div id="destinationCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="5000">
      <div class="carousel-inner h-100">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <div class="carousel-item h-100 <?= $first ? 'active' : '' ?>">
            <img src="assets/img/destinations/<?= htmlspecialchars($row['image']) ?>" class="d-block w-100 h-100" alt="<?= htmlspecialchars($row['title_line2']) ?>">
            <div class="hero-content">
              <h1 class="hero-title" data-aos="fade-right"><?= htmlspecialchars($row['title_line1']) ?></h1>
              <h2 class="hero-subtitle" data-aos="fade-right" data-aos-delay="100"><?= htmlspecialchars($row['title_line2']) ?></h2>
              <p class="hero-text" data-aos="fade-up" data-aos-delay="200">
                <?= nl2br(htmlspecialchars($row['description'])) ?>
              </p>
              <div class="dropdown" data-aos="fade-up" data-aos-delay="300">
                <a class="hero-book-btn dropdown-toggle" href="#" role="button" id="heroBookDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  Book Now
                </a>
                <ul class="dropdown-menu" aria-labelledby="heroBookDropdown">
                  <li><h6 class="dropdown-header">Booking Options</h6></li>
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
              </div>
            </div>
          </div>
          <?php $first = false; ?>
        <?php } ?>
      </div>
      <!-- Carousel Controls -->
      <button class="carousel-control-prev" type="button" data-bs-target="#destinationCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#destinationCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </section>







<!-- Service Section -->
<section class="section section-2">
  <div class="container">
    <div class="row justify-content-between align-items-center gy-5">

      <!-- Text Content -->
      <div class="col-12 col-md-5 order-1 text-center text-md-start" data-aos="fade-down">

        <h2 class="heading mb-3">Explore All Corners of The World With Us</h2>

        <!-- Learn More Link -->
        <a href="service.html" class="d-inline-block mb-4 fw-medium learn-more-link">
          Learn more Our Service &gt;
        </a>

        <!-- Mobile Image Stack -->
        <div class="d-md-none mb-4" data-aos="fade-up">
          <div class="position-relative w-100 mobile-image-stack">
            <img src="assets/img/destinations/8.png" alt="Airplane" class="img-fluid w-100 d-block airplane-img">
            <img src="assets/img/destinations/7.png" alt="Traveler" class="img-fluid position-absolute traveler-img-mobile">
          </div>
        </div>

        <!-- Paragraph -->
        <p class="mb-3">
          Far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.
          They live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.
        </p>

        <!-- Features Grid -->
        <div class="row features" data-aos="fade-up" data-aos-delay="200">
          <!-- Feature 1 -->
          <div class="col-4 feature-item">
            <img src="assets/img/logo/globe-asia-australia-fill.svg" alt="Over 50 Years" class="feature-icon">
            <p class="small">50 Years<br>In Business</p>
          </div>
          <!-- Feature 2 -->
          <div class="col-4 feature-item">
            <img src="assets/img/logo/globe-asia-australia-fill.svg" alt="Own Hotels" class="feature-icon">
            <p class="small">Our Own Hotels<br>Across The Island</p>
          </div>
          <!-- Feature 3 -->
          <div class="col-4 feature-item">
            <img src="assets/img/logo/globe-asia-australia-fill.svg" alt="Specialist Teams" class="feature-icon">
            <p class="small">Specialist<br>Teams</p>
          </div>
          <!-- Feature 4 -->
          <div class="col-4 feature-item">
            <img src="assets/img/logo/globe-asia-australia-fill.svg" alt="Best Guides" class="feature-icon">
            <p class="small">Best<br>Guides</p>
          </div>
          <!-- Feature 5 -->
          <div class="col-4 feature-item">
            <img src="assets/img/logo/globe-asia-australia-fill.svg" alt="Sustainability" class="feature-icon">
            <p class="small">Sustainability</p>
          </div>
          <!-- Feature 6 -->
          <div class="col-4 feature-item">
            <img src="assets/img/logo/globe-asia-australia-fill.svg" alt="Fleet of Vehicles" class="feature-icon">
            <p class="small">Our Own Fleet<br>of Vehicles</p>
          </div>
        </div>
      </div>

      <!-- Desktop Image Stack -->
      <div class="col-12 col-md-6 order-md-2 d-none d-md-flex justify-content-center" data-aos="fade-up">
        <div class="position-relative w-100 desktop-image-stack">
          <img src="assets/img/destinations/8.png" alt="Airplane" class="img-fluid w-100 d-block airplane-img">
          <img src="assets/img/destinations/7.png" alt="Traveler" class="img-fluid position-absolute traveler-img-desktop">
        </div>
      </div>

    </div>
  </div>
</section>










<!-- Popular Destinations Section -->
<section id="destination" class="section popular-destinations">
  <div class="container">
    <!-- Heading -->
    <div class="text-center mb-5">
      <h1 class="section-title" style="font-family: 'Playfair Display', serif; color: #0c2261;">
        Popular Destinations
      </h1>
      <p>Discover the most loved destinations handpicked for your perfect getaway.</p>
      <a href="destination.html" class="view-more-link">View More Locations &gt;</a>
    </div>

    <!-- Desktop Carousel -->
    <div class="d-none d-md-block">
      <div id="desktopCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php
          $chunks = array_chunk($popularDestinations, 3);
          foreach ($chunks as $i => $slide) {
            echo '<div class="carousel-item ' . ($i === 0 ? 'active' : '') . '"><div class="row gx-1">';
            foreach ($slide as $index => $d) {
              // Generate page link from popular_title
              $pageLink = strtolower(str_replace(' ', '', $d['popular_title'])) . '.html';
              echo '
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="' . ($index * 100 + 100) . '">
                  <div class="destination-box position-relative overflow-hidden">
                    <img src="assets/img/destinations/' . htmlspecialchars($d['image']) . '" class="destination-img img-fluid w-100" alt="' . htmlspecialchars($d['popular_title']) . '">
                    <div class="destination-overlay text-center px-3 py-4">
                      <h3>' . htmlspecialchars($d['popular_title']) . '</h3>
                      <p class="small">' . htmlspecialchars($d['description']) . '</p>
                      <a href="' . htmlspecialchars($pageLink) . '" class="read-more">Read More</a>
                    </div>
                  </div>
                </div>';
            }
            echo '</div></div>';
          }
          ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#desktopCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#desktopCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>

    <!-- Mobile Carousel -->
    <div class="d-md-none">
      <div id="mobileCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php foreach ($popularDestinations as $i => $d): 
            $pageLink = strtolower(str_replace(' ', '', $d['popular_title'])) . '.html'; ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
              <div class="destination-box position-relative overflow-hidden">
                <img src="assets/img/destinations/<?= htmlspecialchars($d['image']) ?>" class="destination-img img-fluid w-100" alt="<?= htmlspecialchars($d['popular_title']) ?>">
                <div class="destination-overlay text-center px-3 py-4">
                  <h3><?= htmlspecialchars($d['popular_title']) ?></h3>
                  <p class="small"><?= htmlspecialchars($d['description']) ?></p>
                  <a href="<?= htmlspecialchars($pageLink) ?>" class="read-more">Read More</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#mobileCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mobileCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>
  </div>
</section>







<!-- Special Offers Section -->
<section class="bg-light special-offers-section py-5" data-aos="fade-up">
  <div class="container">
    <!-- Heading -->
    <div class="text-center mb-5">
      <h1 class="special-heading" data-aos="fade-down">Special Offers</h1>
      <p class="special-description" data-aos="fade-up" data-aos-delay="100">
        Limited-time discounts on our most popular packages. Book now to secure these exclusive deals!
      </p>
    </div>

    <?php
    $current_date = date('Y-m-d');
    $offers_query = "SELECT o.*, p.title, p.description, p.image, p.duration 
                    FROM special_offers o
                    JOIN tour_packages p ON o.package_id = p.id
                    WHERE o.is_active = 1 
                    AND o.start_date <= '$current_date' 
                    AND o.end_date >= '$current_date'
                    ORDER BY o.end_date ASC
                    LIMIT 4";
    $offers = mysqli_query($conn, $offers_query);
    $offer_count = mysqli_num_rows($offers);
    ?>

    <?php if ($offer_count > 0): ?>
    <div class="row g-4">
      <?php while ($offer = mysqli_fetch_assoc($offers)): 
        $days_left = floor((strtotime($offer['end_date']) - time()) / (60 * 60 * 24));
        $discount_text = $offer['discount_type'] === 'percentage' 
            ? $offer['discount_value'] . '% OFF' 
            : '$' . $offer['discount_value'] . ' OFF';
      ?>
      <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= ($offer['id'] % 4) * 100 ?>">
        <div class="card h-100 shadow-sm border-0 overflow-hidden offer-card">
          <div class="position-relative">
            <img src="uploads/packages/<?= htmlspecialchars($offer['image']); ?>" 
                 class="card-img-top" 
                 alt="<?= htmlspecialchars($offer['title']); ?>"
                 style="height: 200px; object-fit: cover;">
            <div class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1">
              <?= $discount_text; ?>
            </div>
            <?php if ($days_left <= 7): ?>
            <div class="position-absolute bottom-0 start-0 w-100 bg-warning text-dark text-center py-1">
              <small><?= $days_left === 1 ? 'Last day!' : ($days_left . ' days left') ?></small>
            </div>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($offer['title']); ?></h5>
            <p class="card-text text-muted"><?= htmlspecialchars($offer['duration']); ?></p>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-decoration-line-through text-muted me-2">
                  $<?= number_format($offer['original_price'], 2); ?>
                </span>
                <span class="fw-bold text-danger">
                  $<?= number_format($offer['offer_price'], 2); ?>
                </span>
              </div>
              <a href="packages.php?id=<?= $offer['package_id']; ?>" class="btn btn-sm btn-primary">
                Book Now
              </a>
            </div>
          </div>
          <div class="card-footer bg-transparent border-top-0">
            <small class="text-muted">
              Offer ends <?= date('M j', strtotime($offer['end_date'])); ?>
            </small>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    
    <div class="text-center mt-4">
      <a href="packages.php" class="btn btn-outline-primary">View All Packages</a>
    </div>
    <?php else: ?>
    <div class="alert alert-info text-center">
      No special offers available at the moment. Check out our <a href="packages.php">regular packages</a>!
    </div>
    <?php endif; ?>
  </div>
</section>







<!-- Testimonials Section -->
<section id="testimonials" class="testimonials-section" style="padding-bottom: 110px;">
  <div class="container">
    <!-- Heading -->
    <h1 class="heading text-center mb-3" data-aos="fade-down">Client Review</h1>
    <p class="subheading text-center mb-5" data-aos="fade-up" data-aos-delay="100">
      We pride ourselves on unforgettable journeys and exceptional service.
      Here's what some of our delighted travelers have to say about their experiences.
    </p>

    <?php
    $active_testimonials = mysqli_query($conn, "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC");
    $testimonial_count = mysqli_num_rows($active_testimonials);
    ?>

    <!-- Carousel -->
    <?php if ($testimonial_count > 0): ?>
    <div class="testimonial-carousel-container" data-aos="zoom-in-up" data-aos-delay="200">
      <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000" data-bs-pause="false">
        <div class="carousel-inner">
          <?php 
          $first = true;
          while ($testimonial = mysqli_fetch_assoc($active_testimonials)): 
          ?>
          <div class="carousel-item <?= $first ? 'active' : '' ?>">
            <div class="testimonial-slide d-flex align-items-center justify-content-center gap-4 flex-wrap">
              <div class="image-box" data-aos="fade-right" data-aos-delay="300">
                <img src="assets/img/destinations/<?= htmlspecialchars($testimonial['image']) ?>" alt="<?= htmlspecialchars($testimonial['reviewer_name']) ?>" />
              </div>
              <div class="review-box" data-aos="fade-left" data-aos-delay="400">
                <p class="review-text">"<?= htmlspecialchars($testimonial['review_text']) ?>"</p>
                <p class="reviewer-name"><?= htmlspecialchars($testimonial['reviewer_name']) ?></p>
                <p class="reviewer-title"><?= htmlspecialchars($testimonial['reviewer_title']) ?></p>
              </div>
            </div>
          </div>
          <?php 
          $first = false;
          endwhile; 
          ?>
        </div>

        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev" aria-label="Previous">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next" aria-label="Next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info text-center">No testimonials available at the moment.</div>
    <?php endif; ?>
  </div>
</section>













<!-- Footer -->
<?php include 'components/footer.php' ?>








<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" 
        crossorigin="anonymous"></script>

<!-- AOS JS (Load only ONCE) -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    offset: 100,
    once: true
  });
</script>

<!-- Destination Carousel Auto-slide -->
<script>
  const carousel = document.querySelector('#destinationCarousel');
  if (carousel) {
    const bsCarousel = new bootstrap.Carousel(carousel, {
      interval: 2000,
      ride: 'carousel',
      pause: false,
      wrap: true
    });
    carousel.addEventListener('slid.bs.carousel', () => bsCarousel.cycle());
  }




  document.addEventListener('DOMContentLoaded', function () {
    const carouselElement = document.querySelector('#offerCarousel');
    if (carouselElement) {
      const carousel = new bootstrap.Carousel(carouselElement, {
        interval: 2000,
        pause: false,
        wrap: true,
        touch: true,
        keyboard: true
      });

      carouselElement.addEventListener('mouseenter', () => carousel.pause());
      carouselElement.addEventListener('mouseleave', () => carousel.cycle());
    }
  });




  window.addEventListener('scroll', function () {
    const navbar = document.getElementById('mainNavbar');
    if (navbar) {
      if (window.scrollY > 50) {
        navbar.classList.add('fixed-nav');
      } else {
        navbar.classList.remove('fixed-nav');
      }
    }
  });




  document.querySelectorAll('.destination-box').forEach(box => {
    const img = box.querySelector('.destination-img');
    if (img) {
      box.addEventListener('mouseenter', () => img.style.transform = 'scale(1.4)');
      box.addEventListener('mouseleave', () => img.style.transform = 'scale(1)');
    }
  });



  const testimonialCarousel = document.querySelector('#testimonialCarousel');
  if (testimonialCarousel) {
    const bsTestimonialCarousel = new bootstrap.Carousel(testimonialCarousel, {
      interval: 3000,
      ride: 'carousel',
      pause: false,
      wrap: true
    });

    testimonialCarousel.addEventListener('slid.bs.carousel', () => bsTestimonialCarousel.cycle());
  }





  // Auto-advance desktop carousel (optional)
  document.addEventListener('DOMContentLoaded', function() {
    if(window.innerWidth >= 768) {
      new bootstrap.Carousel(document.getElementById('desktopCarousel'), {
        interval: 5000,
        wrap: true
      });
    }
  });

   // Navbar scroll effect
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
    
    // Initialize AOS (if you're using it)
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
      });
    }
    

</script>

