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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accommodations - Tranel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Mozilla+Headline:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
        
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Playfair Display';
            line-height: 1.6;
            color: #333;
            background-color: #fff;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('assets/img/1.jpg') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero-content h1 {
            font-family: 'Playfair Display';
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -2px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
        }

        .hero-content p {
            font-family: 'Urbanist';
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0.9;
        }

        /* Main Content */
        .main-content {
            padding: 80px 0;
            background-color: #f8f9fa;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-family: 'Playfair Display';
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .section-title p {
            font-family: 'Urbanist';
            font-size: 1.1rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Destination Section */
        .destination-section {
            margin-bottom: 80px;
        }

        .destination-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .destination-header h3 {
            font-family: 'Playfair Display';
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }

        .destination-header h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
        }

        /* Card Grid */
        .accommodations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Accommodation Cards */
        .accommodation-card {
            background: white;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .accommodation-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .card-image-container {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .accommodation-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .accommodation-card:hover .accommodation-image {
            transform: scale(1.05);
        }

        .no-image {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.3));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .accommodation-card:hover .card-overlay {
            opacity: 1;
        }

        .card-content {
            padding: 1.5rem;
        }

        .accommodation-name {
            font-family: 'Urbanist';
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .accommodation-type {
            display: inline-block;
            font-family: 'Urbanist';
            border-radius: 25px;
            background: linear-gradient(135deg, #2c3e50);
            color: white;
            padding: 4px 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .accommodation-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: black;
            margin-bottom: 1rem;
        }

        .contact-info {
            space-y: 0.5rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .contact-item i {
            width: 20px;
            margin-right: 10px;
            color: #2c3e50;
            flex-shrink: 0;
        }

        .contact-item a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-item a:hover {
            color: #2c3e50;
        }

        /* Pagination Style */
        .pagination-section {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 4rem;
            gap: 1rem;
        }

        .page-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid #dee2e6;
            background: white;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .page-btn:hover, .page-btn.active {
            background: linear-gradient(135deg, #2c3e50, #2c3e50);
            color: white;
            border-color: transparent;
            transform: translateY(-1px);
        }

        /* Newsletter Section */
        .newsletter-section {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            padding: 60px 0;
            color: white;
            text-align: center;
        }

        .newsletter-content h3 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .newsletter-form {
            max-width: 400px;
            margin: 2rem auto 0;
            display: flex;
            gap: 1rem;
        }

        .newsletter-input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            font-size: 1rem;
        }

        .newsletter-btn {
            background: linear-gradient(135deg, #2c3e50, #2c3e50);
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .newsletter-btn:hover {
            transform: translateY(-1px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .accommodations-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
            
            .newsletter-input,
            .newsletter-btn {
                border-radius: 8px;
            }
        }

        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .hero-content p {
                font-size: 1rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
        }

        /* Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .accommodation-card {
            animation-delay: calc(var(--index) * 0.1s);
        }
    </style>
</head>
<body>
    
    <?php include 'components/header.php' ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>ACCOMMODATIONS</h1>
            <p>Discover premium stays and comfortable accommodations at our carefully selected partner locations across beautiful destinations.</p>
        </div>
    </section>

    <!-- Main Content -->
    <section id="accommodations" class="main-content">
        <div class="container-fluid">
            <div class="section-title fade-in">
                <h2>Where You'll Stay</h2>
                <p>Premium accommodations carefully selected for comfort, quality, and authentic local experiences</p>
            </div>

            <?php if (!empty($groupedAccommodations)): ?>
                <?php $cardIndex = 0; ?>
                <?php foreach ($groupedAccommodations as $destination): ?>
                    <div class="destination-section fade-in">
                        <div class="destination-header">
                            <h3><?php echo htmlspecialchars($destination['name']); ?></h3>
                        </div>
                        
                        <div class="accommodations-grid">
                            <?php foreach ($destination['accommodations'] as $acc): ?>
                                <div class="accommodation-card fade-in" style="--index: <?php echo $cardIndex++; ?>">
                                    <div class="card-image-container">
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
                                                <i class="bi bi-image" style="font-size: 3rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-overlay"></div>
                                    </div>
                                    
                                    <div class="card-content">
                                        <h4 class="accommodation-name"><?php echo htmlspecialchars($acc['accommodation_name']); ?></h4>
                                        <span class="accommodation-type"><?php echo htmlspecialchars($acc['type']); ?></span>
                                        <div class="accommodation-price"><?php echo htmlspecialchars($acc['price_range']); ?></div>
                                        
                                        <div class="contact-info">
                                            <?php if (!empty($acc['contact_number'])): ?>
                                                <div class="contact-item">
                                                    <i class="bi bi-telephone-fill"></i>
                                                    <span><?php echo htmlspecialchars($acc['contact_number']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($acc['email'])): ?>
                                                <div class="contact-item">
                                                    <i class="bi bi-envelope-fill"></i>
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

                <!-- Pagination Section (matching the blog design) -->
            
            <?php else: ?>
                <div class="container">
                    <div class="alert alert-info text-center" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        No accommodations available at the moment. Please check back later.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

     <?php include 'components/footer.php' ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>

        // Observe all fade-in elements
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

   

        // Add loading animation for images
        document.querySelectorAll('.accommodation-image').forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero-section');
            if (hero) {
                hero.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
    </script>
</body>
</html>