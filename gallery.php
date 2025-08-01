<?php
require_once 'admin/includes/db.php';

// Fetch gallery images from database
$query = "SELECT * FROM gallery ORDER BY date_taken DESC";
$result = mysqli_query($conn, $query);
$images = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Group images by year for the timeline view
$groupedImages = [];
foreach ($images as $image) {
    $year = date('Y', strtotime($image['date_taken']));
    $groupedImages[$year][] = $image;
}
krsort($groupedImages); // Sort years in descending order
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journey Gallery | <?php echo $company_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/css/lightgallery.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('assets/gallery-hero.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
            margin-bottom: 50px;
        }
        
        .gallery-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .gallery-subtitle {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }
        
        .gallery-container {
            padding: 0 15px;
        }
        
        .year-heading {
            position: relative;
            margin: 60px 0 30px;
            text-align: center;
        }
        
        .year-heading h2 {
            display: inline-block;
            background-color: var(--secondary-color);
            color: white;
            padding: 10px 30px;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            aspect-ratio: 4/3;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        
        .gallery-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: white;
            padding: 20px;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        
        .gallery-item:hover .gallery-caption {
            transform: translateY(0);
        }
        
        .gallery-caption h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .gallery-caption p {
            font-size: 0.9rem;
            margin-bottom: 0;
            opacity: 0.9;
        }
        
        .view-more-btn {
            display: block;
            text-align: center;
            margin: 40px auto;
            padding: 12px 30px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            max-width: 200px;
        }
        
        .view-more-btn:hover {
            background-color: var(--secondary-color);
            color: white;
            transform: translateY(-3px);
        }
        
        .cta-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .cta-title {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .cta-subtitle {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }
        
        .btn-custom-trip {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-custom-trip:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        @media (max-width: 768px) {
            .gallery-title {
                font-size: 2.5rem;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .cta-buttons .btn {
                display: block;
                width: 100%;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
  <?php include 'components/header.php' ?>
    <!-- Hero Section -->
     <section class="hero-section">
        <div class="container">
            <h1 class="gallery-title">Our Journey Gallery</h1>
            <p class="gallery-subtitle">Explore breathtaking moments from our past adventures. Each photo tells a story of discovery, excitement, and unforgettable experiences.</p>
            <a href="#gallery" class="btn btn-primary btn-lg px-4 py-2">View Gallery</a>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="gallery-container">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4">Memories That Last Forever</h2>
                <p class="lead">Scroll through years of incredible journeys and shared moments</p>
            </div>

            <?php foreach ($groupedImages as $year => $yearImages): ?>
                <div class="year-heading">
                    <h2><?php echo $year; ?></h2>
                </div>
                
                <div class="gallery-grid lightgallery">
                    <?php foreach ($yearImages as $image): ?>
                        <a href="uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>" class="gallery-item" data-sub-html="<h4><?php echo htmlspecialchars($image['title']); ?></h4><p><?php echo htmlspecialchars($image['description']); ?></p>">
                            <img src="uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
                            <div class="gallery-caption">
                                <h3><?php echo htmlspecialchars($image['title']); ?></h3>
                                <p><?php echo htmlspecialchars($image['location']); ?> • <?php echo date('F Y', strtotime($image['date_taken'])); ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            
            <a href="contact.php" class="view-more-btn">Join Our Next Adventure</a>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready for Your Own Adventure?</h2>
            <p class="cta-subtitle">Browse our tour packages or create a custom trip tailored just for you.</p>
            <div class="cta-buttons">
                <a href="packages.php" class="btn btn-light btn-lg px-4 py-2 me-3">View Packages</a>
                <a href="custom-trip.php" class="btn btn-custom-trip btn-lg px-4 py-2">Create Your Custom Trip</a>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/lightgallery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lg-zoom/2.7.1/lg-zoom.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lg-fullscreen/2.7.1/lg-fullscreen.min.js"></script>
    <script>
        // Initialize lightGallery
        lightGallery(document.querySelector('.lightgallery'), {
            plugins: [lgZoom, lgFullscreen],
            speed: 500,
            download: false,
            zoom: true,
            thumbnail: true
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const galleryItems = document.querySelectorAll('.gallery-item');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            galleryItems.forEach((item, index) => {
                item.style.opacity = 0;
                item.style.transform = 'translateY(20px)';
                item.style.transition = `all 0.5s ease ${index * 0.1}s`;
                observer.observe(item);
            });
        });
    </script>
</body>
</html>