<?php
require_once 'admin/includes/db.php';

// Check if package ID is provided
if (!isset($_GET['id'])) {
    header("Location: packages.php");
    exit();
}

$package_id = intval($_GET['id']);
$package_query = mysqli_query($conn, "SELECT * FROM tour_packages WHERE id = $package_id AND is_active = 1");

if (mysqli_num_rows($package_query) == 0) {
    header("Location: packages.php");
    exit();
}

$package = mysqli_fetch_assoc($package_query);

// Get gallery images
$gallery_query = mysqli_query($conn, "SELECT * FROM package_gallery WHERE package_id = $package_id ORDER BY sort_order");
$gallery_images = mysqli_fetch_all($gallery_query, MYSQLI_ASSOC);

// Get itinerary
$itinerary_query = mysqli_query($conn, "SELECT * FROM package_itinerary WHERE package_id = $package_id ORDER BY day_number");
$itinerary_days = mysqli_fetch_all($itinerary_query, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($package['title']) ?> | Niro SL Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('uploads/packages/<?= !empty($package['featured_image']) ? htmlspecialchars($package['featured_image']) : 'default-package.jpg' ?>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        .highlight-item {
            margin-bottom: 8px;
        }
        .highlight-item:before {
            content: "✓ ";
            color: #28a745;
        }
        .exclusion-item:before {
            content: "✗ ";
            color: #dc3545;
        }
        .itinerary-day {
            border-left: 3px solid #0c2261;
            padding-left: 15px;
            margin-bottom: 25px;
        }
        .gallery-img {
            height: 200px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .gallery-img:hover {
            transform: scale(1.03);
        }
        .price-badge {
            font-size: 1.5rem;
            padding: 15px 25px;
            background-color: #0c2261;
        }
        .modal-img {
            max-height: 80vh;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <?php include 'components/header.php' ?>
    
    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold"><?= htmlspecialchars($package['title']) ?></h1>
            <?php if (!empty($package['subtitle'])): ?>
                <p class="lead"><?= htmlspecialchars($package['subtitle']) ?></p>
            <?php endif; ?>
            <div class="d-flex justify-content-center gap-4 mt-4">
                <?php if (!empty($package['duration'])): ?>
                    <span class="badge bg-light text-dark fs-6">
                        <i class="bi bi-clock"></i> <?= htmlspecialchars($package['duration']) ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($package['price'])): ?>
                    <span class="badge bg-light text-dark fs-6">
                        <i class="bi bi-tag"></i> <?= htmlspecialchars($package['price']) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Package Details -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Overview -->
                    <div class="mb-5">
                        <h2 class="mb-4">Tour Overview</h2>
                        <div class="lead"><?= nl2br(htmlspecialchars($package['overview'])) ?></div>
                    </div>
                    
                    <!-- Highlights -->
                    <div class="mb-5">
                        <h2 class="mb-4">Tour Highlights</h2>
                        <div class="row">
                            <?php 
                            $highlights = explode("\n", $package['highlights']);
                            foreach ($highlights as $highlight): 
                                if (trim($highlight)): ?>
                                <div class="col-md-6">
                                    <div class="highlight-item"><?= htmlspecialchars(trim($highlight)) ?></div>
                                </div>
                                <?php endif;
                            endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Itinerary -->
                    <?php if (!empty($itinerary_days)): ?>
                    <div class="mb-5">
                        <h2 class="mb-4">Detailed Itinerary</h2>
                        <?php foreach ($itinerary_days as $day): ?>
                            <div class="itinerary-day">
                                <h5>Day <?= htmlspecialchars($day['day_number']) ?>: <?= htmlspecialchars($day['title']) ?></h5>
                                <p><?= nl2br(htmlspecialchars($day['description'])) ?></p>
                                <?php if (!empty($day['meals'])): ?>
                                    <p><strong>Meals:</strong> <?= htmlspecialchars($day['meals']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($day['accommodation'])): ?>
                                    <p><strong>Accommodation:</strong> <?= htmlspecialchars($day['accommodation']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Inclusions & Exclusions -->
                    <div class="row mb-5">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">What's Included</h5>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    $inclusions = explode("\n", $package['inclusions']);
                                    foreach ($inclusions as $inclusion): 
                                        if (trim($inclusion)): ?>
                                        <div class="highlight-item"><?= htmlspecialchars(trim($inclusion)) ?></div>
                                        <?php endif;
                                    endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($package['exclusions'])): ?>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">What's Excluded</h5>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    $exclusions = explode("\n", $package['exclusions']);
                                    foreach ($exclusions as $exclusion): 
                                        if (trim($exclusion)): ?>
                                        <div class="exclusion-item"><?= htmlspecialchars(trim($exclusion)) ?></div>
                                        <?php endif;
                                    endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Gallery -->
                    <?php if (!empty($gallery_images)): ?>
                    <div class="mb-5">
                        <h2 class="mb-4">Tour Gallery</h2>
                        <div class="row g-3">
                            <?php foreach ($gallery_images as $image): ?>
                            <div class="col-md-4 col-6">
                                <img src="uploads/packages/gallery/<?= htmlspecialchars($image['image_path']) ?>" 
                                     class="img-fluid rounded gallery-img" 
                                     alt="<?= htmlspecialchars($image['caption'] ?? 'Tour image') ?>"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#galleryModal"
                                     onclick="showImageInModal(this.src, '<?= htmlspecialchars($image['caption'] ?? '') ?>')">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 20px;">
                        <!-- Booking Box -->
                        <div class="card mb-4 shadow">
                            <div class="card-body text-center">
                                <h4 class="card-title">Package Price</h4>
                                <div class="my-4">
                                    <span class="badge price-badge rounded-pill text-white">
                                        <?= htmlspecialchars($package['price']) ?>
                                    </span>
                                </div>
                                <p class="text-muted">Per person</p>
                                <a href="contact.php?package=<?= urlencode($package['title']) ?>" 
                                   class="btn btn-primary btn-lg w-100 mb-2">
                                    <i class="bi bi-calendar-check"></i> Book Now
                                </a>
                                <a href="contact.php" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="bi bi-question-circle"></i> Enquire Now
                                </a>
                            </div>
                        </div>
                        
                        <!-- Quick Info -->
                        <div class="card shadow mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Quick Information</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php if (!empty($package['duration'])): ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="bi bi-clock"></i> Duration:</span>
                                        <span><?= htmlspecialchars($package['duration']) ?></span>
                                    </li>
                                    <?php endif; ?>
                                    <?php if (!empty($package['price'])): ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="bi bi-tag"></i> Price:</span>
                                        <span><?= htmlspecialchars($package['price']) ?></span>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Related Packages -->
    <?php
    $related_query = mysqli_query($conn, 
        "SELECT * FROM tour_packages 
         WHERE is_active = 1 AND id != $package_id 
         ORDER BY RAND() LIMIT 3");
    
    if (mysqli_num_rows($related_query) > 0): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">You Might Also Like</h2>
            <div class="row">
                <?php while ($related = mysqli_fetch_assoc($related_query)): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($related['featured_image'])): ?>
                        <img src="uploads/packages/<?= htmlspecialchars($related['featured_image']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($related['title']) ?>"
                             style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                        <img src="images/default-package.jpg" 
                             class="card-img-top" 
                             alt="Default package image"
                             style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($related['title']) ?></h5>
                            <?php if (!empty($related['subtitle'])): ?>
                                <p class="text-muted small"><?= htmlspecialchars($related['subtitle']) ?></p>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                                <?php if (!empty($related['duration'])): ?>
                                    <span class="badge bg-info"><?= htmlspecialchars($related['duration']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($related['price'])): ?>
                                    <span class="text-primary fw-bold"><?= htmlspecialchars($related['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-3">
                                <a href="package-single.php?id=<?= $related['id'] ?>" class="btn btn-primary w-100">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Gallery Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="galleryModalTitle">Tour Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid modal-img" alt="Tour image">
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showImageInModal(src, caption) {
            document.getElementById('modalImage').src = src;
            document.getElementById('galleryModalTitle').textContent = caption || 'Tour Image';
        }
    </script>
</body>
</html>