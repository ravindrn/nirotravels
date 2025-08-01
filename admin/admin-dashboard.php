<?php
require_once 'includes/auth_check.php'; // Ensure admin authentication
require_once 'includes/db.php';

$admin_id = $_SESSION['admin_id'];
$admin_query = "SELECT * FROM admin_users WHERE id = $admin_id LIMIT 1";
$admin_result = mysqli_query($conn, $admin_query);
$admin = mysqli_fetch_assoc($admin_result);




// Get counts for dashboard
$destinations_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM destinations"));
$testimonials_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM testimonials"));
$offers_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM special_offers"));
$unread_feedback = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM customer_feedback WHERE is_read = 0"));
$gallery_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM gallery")); // Add this line
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .dashboard-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        .card-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .bg-custom-1 { background-color: #4e73df; }
        .bg-custom-2 { background-color: #1cc88a; }
        .bg-custom-3 { background-color: #36b9cc; }
        .bg-custom-4 { background-color: #f6c23e; }
    </style>
</head>
<body>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <div class="mb-3">
                <i class="bi bi-person-circle text-white" style="font-size: 3rem;"></i>
            </div>
            <h5 class="text-white mb-1"><?= htmlspecialchars($admin['full_name']) ?></h5>
            <small class="text-muted"><?= htmlspecialchars($admin['email']) ?></small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="admin-dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage-packages.php">
                    <i class="bi bi-briefcase me-2"></i> Tour Packages
                </a>
</li>
 <li class="nav-item">
        <a class="nav-link" href="gallery.php">  <!-- Add this line -->
            <i class="bi bi-images me-2"></i> Gallery Management
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="manage-offers.php">  <!-- Add this line -->
            <i class="bi bi-images me-2"></i> Manage Offers
        </a>
    </li>
        <li class="nav-item">
    <a class="nav-link" href="customer_inquiries.php">
        <i class="bi bi-envelope me-2"></i> Trip Inquiries
    </a>
    <li class="nav-item">
    <a class="nav-link" href="package_inquiries.php">
        <i class="bi bi-envelope"></i>
        Package Inquiries
    </a>
</li>
 <li class="nav-item">
    <a class="nav-link" href="manage-accommodations.php">
        <i class="bi bi-envelope"></i>
        Manage Accomodations
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="manage-activities.php">
        <i class="bi bi-envelope"></i>
        Manage Activities
    </a>
</li>
</li>
            <!-- Other menu items remain the same -->
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard Overview</h1>
                </div>

                <!-- Cards Row -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card bg-custom-1 text-white h-100">
                            <div class="card-body text-center py-4">
                                <div class="card-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <h5 class="card-title">Destinations</h5>
                                <h2 class="mb-0"><?= $destinations_count ?></h2>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="edit-homepage.php#destination" class="text-white stretched-link">Manage Destinations</a>
                            </div>
                        </div>
                    </div>
                    <!-- Add this card after the existing cards -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card text-white h-100" style="background-color: #6f42c1;">
                            <div class="card-body text-center py-4">
                                <div class="card-icon">
                                    <i class="bi bi-images"></i>
                                </div>
                                <h5 class="card-title">Gallery Images</h5>
                                <h2 class="mb-0"><?= $gallery_count ?></h2>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="gallery.php" class="text-white stretched-link">Manage Gallery</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card bg-custom-2 text-white h-100">
                            <div class="card-body text-center py-4">
                                <div class="card-icon">
                                    <i class="bi bi-stars"></i>
                                </div>
                                <h5 class="card-title">Special Offers</h5>
                                <h2 class="mb-0"><?= $offers_count ?></h2>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="edit-homepage.php#offers-section" class="text-white stretched-link">Manage Offers</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card bg-custom-3 text-white h-100">
                            <div class="card-body text-center py-4">
                                <div class="card-icon">
                                    <i class="bi bi-chat-square-quote"></i>
                                </div>
                                <h5 class="card-title">Testimonials</h5>
                                <h2 class="mb-0"><?= $testimonials_count ?></h2>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="edit-homepage.php#testimonials-section" class="text-white stretched-link">Manage Testimonials</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card bg-custom-4 text-dark h-100">
                            <div class="card-body text-center py-4">
                                <div class="card-icon">
                                    <i class="bi bi-chat-left-text"></i>
                                </div>
                                <h5 class="card-title">New Feedback</h5>
                                <h2 class="mb-0"><?= $unread_feedback ?></h2>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="view-feedbacks.php" class="text-dark stretched-link">View Feedback</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="edit-homepage.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="bi bi-house-door-fill me-2"></i> Edit Homepage
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="edit-footer.php" class="btn btn-outline-success w-100 py-3">
                                    <i class="bi bi-window-dock me-2"></i> Edit Footer
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="view-feedbacks.php" class="btn btn-outline-info w-100 py-3">
                                    <i class="bi bi-chat-left-text-fill me-2"></i> View Feedback
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="edit-aboutus.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="bi bi-house-door-fill me-2"></i> Edit About Us
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Info -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">System Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Last Login:</strong> <?= date('F j, Y, g:i a', strtotime($_SESSION['last_login'])) ?></p>
                                <p><strong>PHP Version:</strong> <?= phpversion() ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Database:</strong> MySQL</p>
                                <p><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>