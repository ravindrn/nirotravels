<?php
require_once 'admin/includes/db.php';

// Fetch accommodations grouped by destination
$query = "SELECT 
            d.id AS destination_id, 
            d.popular_title AS destination_name,
            a.id AS accommodation_id,
            a.name AS accommodation_name,
            a.type,
            a.price_range,
            a.image_path,
            a.contact_number,
            a.email,
            a.website,
            a.is_active
          FROM destinations d
          JOIN accommodations a ON d.id = a.destination_id
          WHERE a.is_active = 1
          ORDER BY d.popular_title, a.price_range";

$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Group accommodations by destination
$groupedAccommodations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $destinationId = $row['destination_id'];
    if (!isset($groupedAccommodations[$destinationId])) {
        $groupedAccommodations[$destinationId] = [
            'name' => $row['destination_name'],
            'accommodations' => []
        ];
    }
    $groupedAccommodations[$destinationId]['accommodations'][] = $row;
}

// Check if we got any results
if (empty($groupedAccommodations)) {
    echo "<div class='alert alert-warning'>No accommodations found in the database.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
      /* General Styling */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
    color: #333;
    margin: 0;
    padding: 0;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(to right, #1d3557, #457b9d);
    color: white;
    padding: 80px 0;
    text-align: center;
}
.accommodations-title {
    font-size: 3rem;
    margin-bottom: 15px;
}
.accommodations-subtitle {
    font-size: 1.2rem;
    margin-bottom: 25px;
}

/* Destination Heading */
.destination-section {
    margin-bottom: 60px;
}
.destination-heading h2 {
    font-size: 2rem;
    margin-bottom: 20px;
    color: #1d3557;
    border-bottom: 3px solid #a8dadc;
    display: inline-block;
    padding-bottom: 8px;
}

/* Accommodation Grid */
.accommodations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}

/* Accommodation Card */
.accommodation-card {
    background-color: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.3s ease;
}
.accommodation-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
.accommodation-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
}
.no-image {
    width: 100%;
    height: 180px;
    background-color: #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

/* Card Content */
.accommodation-details {
    padding: 20px;
}
.accommodation-name {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 5px;
}
.accommodation-type {
    font-size: 0.95rem;
    color: #555;
    background-color: #e9ecef;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-block;
    margin-bottom: 10px;
}
.accommodation-price {
    font-weight: bold;
    font-size: 1.1rem;
    color: #2a9d8f;
    margin-bottom: 15px;
}

/* Contact Info */
.accommodation-contact {
    font-size: 0.9rem;
}
.contact-item {
    margin-bottom: 6px;
    color: #444;
}
.contact-item i {
    margin-right: 8px;
    color: #1d3557;
}
.contact-item a {
    color: #0077b6;
    text-decoration: none;
}
.contact-item a:hover {
    text-decoration: underline;
}

/* Responsive Padding */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Buttons */
.btn-primary {
    background-color: #e63946;
    border-color: #e63946;
}
.btn-primary:hover {
    background-color: #d62839;
    border-color: #d62839;
}

    </style>
</head>
<body>
  <?php include 'components/header.php' ?>
  
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="accommodations-title">Our Premium Accommodations</h1>
            <p class="accommodations-subtitle">Discover comfortable stays at our carefully selected accommodation partners in each destination we visit.</p>
            <a href="#accommodations" class="btn btn-primary btn-lg px-4 py-2">Explore Stays</a>
        </div>
    </section>

    <!-- Accommodations Section -->
    <section id="accommodations" class="container">
        <div class="text-center mb-5">
            <h2 class="display-4">Where You'll Stay</h2>
            <p class="lead">Quality accommodations for every budget and preference</p>
        </div>

        <?php if (!empty($groupedAccommodations)): ?>
            <?php foreach ($groupedAccommodations as $destination): ?>
                <div class="destination-section">
                    <div class="destination-heading">
                        <h2><?php echo htmlspecialchars($destination['name']); ?></h2>
                    </div>
                    
                    <div class="accommodations-grid">
                        <?php foreach ($destination['accommodations'] as $acc): ?>
                            <div class="accommodation-card">
                                <?php 
                                // Modified image path handling
                                $imagePath = '';
                                if (!empty($acc['image_path'])) {
                                    // If image_path contains full path, extract just the filename
                                    $imageFile = basename($acc['image_path']);
                                    $imagePath = 'uploads/accommodations/' . $imageFile;
                                }
                                ?>
                                
                                <?php if (!empty($imagePath) && file_exists($imagePath)): ?>
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                         class="accommodation-image" 
                                         alt="<?php echo htmlspecialchars($acc['accommodation_name']); ?>">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="bi bi-image" style="font-size: 2rem;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="accommodation-details">
                                    <h3 class="accommodation-name"><?php echo htmlspecialchars($acc['accommodation_name']); ?></h3>
                                    <span class="accommodation-type"><?php echo htmlspecialchars($acc['type']); ?></span>
                                    <div class="accommodation-price"><?php echo htmlspecialchars($acc['price_range']); ?></div>
                                    
                                    <div class="accommodation-contact">
                                        <?php if (!empty($acc['contact_number'])): ?>
                                            <div class="contact-item">
                                                <i class="bi bi-telephone"></i>
                                                <?php echo htmlspecialchars($acc['contact_number']); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($acc['email'])): ?>
                                            <div class="contact-item">
                                                <i class="bi bi-envelope"></i>
                                                <a href="mailto:<?php echo htmlspecialchars($acc['email']); ?>">
                                                    <?php echo htmlspecialchars($acc['email']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($acc['website'])): ?>
                                            <div class="contact-item">
                                                <i class="bi bi-globe"></i>
                                                <a href="<?php echo htmlspecialchars($acc['website']); ?>" target="_blank" rel="noopener noreferrer">
                                                    <?php 
                                                        // Display a cleaned up version of the URL
                                                        $website = str_replace(['http://', 'https://', 'www.'], '', $acc['website']);
                                                        echo htmlspecialchars($website); 
                                                    ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No accommodations available at the moment. Please check back later.</div>
        <?php endif; ?>
    </section>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" 
        crossorigin="anonymous"></script>
</body>
</html>