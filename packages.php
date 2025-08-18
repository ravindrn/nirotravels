<?php
// packages.php
require_once 'admin/includes/db.php';


$packageId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$current_date = date('Y-m-d');

// Fetch all active packages for listing
$packagesQuery = "SELECT p.*, 
                 (SELECT MIN(offer_price) FROM special_offers 
                  WHERE package_id = p.id 
                  AND start_date <= '$current_date' 
                  AND end_date >= '$current_date'
                  AND is_active = 1) AS best_offer_price
                 FROM tour_packages p 
                 WHERE p.is_active = 1 
                 ORDER BY p.title ASC";
$packagesResult = mysqli_query($conn, $packagesQuery);

// Fetch specific package details if ID is provided
$packageDetails = null;
$packageOffers = [];
if ($packageId > 0) {
    $packageQuery = "SELECT p.*, 
                    (SELECT COUNT(*) FROM special_offers 
                     WHERE package_id = p.id 
                     AND start_date <= '$current_date' 
                     AND end_date >= '$current_date'
                     AND is_active = 1) AS active_offers
                    FROM tour_packages p 
                    WHERE p.id = $packageId AND p.is_active = 1";
    $packageResult = mysqli_query($conn, $packageQuery);
    $packageDetails = mysqli_fetch_assoc($packageResult);
    
    if ($packageDetails && $packageDetails['active_offers'] > 0) {
        $offersQuery = "SELECT * FROM special_offers 
                       WHERE package_id = $packageId 
                       AND start_date <= '$current_date' 
                       AND end_date >= '$current_date'
                       AND is_active = 1
                       ORDER BY offer_price ASC";
        $offersResult = mysqli_query($conn, $offersQuery);
        while ($offer = mysqli_fetch_assoc($offersResult)) {
            $packageOffers[] = $offer;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $packageDetails ? htmlspecialchars($packageDetails['title']) : 'Travel Packages' ?> | Niro SL Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Mozilla+Headline:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <style>    
    :root {
            --primary: #1a1a1a;
            --secondary: #2d3748;
            --accent: #4a5568;
            --light: #f7fafc;
            --text-light: rgba(255, 255, 255, 0.9);
            --text-dark: #2d3748;
            --overlay: rgba(0, 0, 0, 0.6);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
         
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        p{
            font-family: 'Urbanist';
        }


        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), 
                        url('assets/img/1.jpg');
            background-size: cover;
            background-position: center;
            height: 103vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            margin-bottom: 0;
        }
        
        .hero-content h1 {
            font-family: 'Playfair Display';
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -2px;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
        }
        
        .hero-content .subtitle {
            font-size: 1.5rem;
            font-weight: 400;
            font-style: italic;
            opacity: 0.95;
            margin-bottom: 2rem;
        }
        
        .hero-content p {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.9;
        }
        
        /* Main Container */
        .packages-container {
            background: white;
            margin-top: -100px;
            position: relative;
            z-index: 10;
            padding: 80px 0 60px;
            min-height: 100vh;
        }
        
        /* Package Grid */
        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        
        /* Package Cards */
        .package-card {
            background: white;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            height: 500px;
            cursor: pointer;
        }
        
        .package-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
        }
        
        .package-card-image {
            position: relative;
            height: 60%;
            overflow: hidden;
        }
        
        .package-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        
        .package-card:hover .package-card-image img {
            transform: scale(1.1);
        }
        
        .package-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 30px;
            color: white;
        }
        
        .duration-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.95);
            color: var(--primary);
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        .package-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        
        .package-description {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .package-card-body {
            padding: 25px;
            height: 40%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .package-features {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: var(--accent);
        }
        
        .feature-item i {
            margin-right: 6px;
            font-size: 1rem;
        }
        
        .package-pricing {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .price-section {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .price-from {
            font-size: 0.8rem;
            color: var(--accent);
            margin-bottom: 2px;
        }
        
        .price-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 1rem;
            margin-right: 8px;
        }
        
        .offer-price {
            color: #e53e3e;
            font-weight: 700;
        }
        
        .learn-more-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .learn-more-btn:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Single Package View Styles */
        .single-package {
            background: white;
            margin-top: -100px;
            position: relative;
            z-index: 10;
            padding: 60px 0;
        }
        
        .package-hero-image {
            overflow: hidden;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            position: relative;
        }
        
        .package-hero-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        
        .package-info-card {
            background: white;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }
        
        .info-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .info-header h3 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .duration-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .duration-info {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            padding: 8px 15px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .duration-info i {
            margin-right: 8px;
            color: var(--accent);
        }
        
        .price-display {
            text-align: right;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hero-content .subtitle {
                font-size: 1.2rem;
            }
            
            .packages-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .package-card {
                height: auto;
                min-height: 450px;
            }
            
            .packages-container {
                padding: 60px 20px 40px;
                margin-top: -80px;
           
            }
        }
        
        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .package-overlay {
                padding: 20px;
            }
            
            .package-title {
                font-size: 1.5rem;
            }
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .package-card {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        .package-card:nth-child(1) { animation-delay: 0.1s; }
        .package-card:nth-child(2) { animation-delay: 0.2s; }
        .package-card:nth-child(3) { animation-delay: 0.3s; }
        .package-card:nth-child(4) { animation-delay: 0.4s; }
        .package-card:nth-child(5) { animation-delay: 0.5s; }
        .package-card:nth-child(6) { animation-delay: 0.6s; }
        
        /* Special Offers Alert */
        .special-offers-alert {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .special-offers-alert h4 {
            margin-bottom: 8px;
        }
        
        /* Inclusions/Exclusions Lists */
        .inclusion-item, .exclusion-item {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }
        
        .inclusion-item:last-child,
        .exclusion-item:last-child {
            border-bottom: none;
        }
        
        .inclusion-item i {
            color: #28a745;
        }
        
        .exclusion-item i {
            color: #dc3545;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 25px;
        }
        
        .action-btn {
            padding: 12px 20px;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-success {
            background: #25D366;
            color: white;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        /* Additional Floating Button Styles */
        .floating-btn {
            position: fixed;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 1000;
            border: none;
            animation: pulse 2s infinite;
        }

        .floating-btn.whatsapp {
            bottom: 30px;
            right: 30px;
            background: #25D366;
            color: white;
        }

        .floating-btn.back-to-top {
            bottom: 100px;
            right: 30px;
            background: var(--primary);
            color: white;
            display: none;
        }

        .floating-btn:hover {
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
        }

        @keyframes pulse {
            0% { 
                transform: scale(1);
                box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 12px 35px rgba(0,0,0,0.2);
            }
            100% { 
                transform: scale(1);
                box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            }
        }

        @media (max-width: 768px) {
            .floating-btn {
                width: 50px;
                height: 50px;
                bottom: 20px;
                right: 20px;
            }
            
            .floating-btn.back-to-top {
                bottom: 80px;
                right: 20px;
            }
            
            .floating-btn i {
                font-size: 1.4rem !important;
            }
        }

    </style>
</head>
<body>

<!-- Header -->
<?php include 'components/header.php' ?>

<?php if ($packageId > 0 && $packageDetails): ?>
    <!-- Single Package Hero -->
    <div class="hero-section" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), url('<?= !empty($packageDetails['hero_image']) ? 'uploads/hero_images/' . htmlspecialchars($packageDetails['hero_image']) : 'uploads/packages/' . htmlspecialchars($packageDetails['image']) ?>'); background-size: cover; background-position: center;">
        <div class="hero-content">
            <h1><?= htmlspecialchars($packageDetails['title']) ?></h1>
            <p class="subtitle">Crafted for You</p>
            <p><?= substr(htmlspecialchars($packageDetails['description']), 0, 150) ?>...</p>
        </div>
    </div>
    
    <!-- Single Package Content -->
    <div class="single-package">
        <div class="container">
            <?php if (!empty($packageOffers)): ?>
            <div class="alert special-offers-alert" role="alert">
                <h4><i class="bi bi-tag-fill me-2"></i>Special Offers Available!</h4>
                <p class="mb-0">Book now to take advantage of these limited-time discounts.</p>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="package-hero-image">
                        <img src="uploads/packages/<?= htmlspecialchars($packageDetails['image']) ?>" 
                             alt="<?= htmlspecialchars($packageDetails['title']) ?>" 
                             class="img-fluid">
                    </div>
                    
                    <div class="package-info-card mb-4">
                        <h3>About This Package</h3>
                        <div class="package-content">
                            <?= nl2br(htmlspecialchars($packageDetails['description'])) ?>
                        </div>
                    </div>
                    
                    <div class="package-info-card">
                        <h3>Detailed Itinerary</h3>
                        <div class="itinerary-content">
                            <?= nl2br(htmlspecialchars($packageDetails['itinerary'])) ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="package-info-card">
                        <div class="info-header">
                            <h3>Package Details</h3>
                            <div class="duration-price">
                                <div class="duration-info">
                                    <i class="bi bi-calendar-check"></i>
                                    <?= htmlspecialchars($packageDetails['duration']) ?> Days
                                </div>
                                <div class="price-display">
                                    <?php if (!empty($packageOffers)): ?>
                                        <div class="original-price">$<?= number_format(floatval(str_replace(['$', ','], '', $packageDetails['price_range'])), 2) ?></div>
                                        <div class="offer-price">$<?= number_format($packageOffers[0]['offer_price'], 2) ?></div>
                                        <small class="text-muted">per person</small>
                                    <?php else: ?>
                                        <div class="price-amount">$<?= number_format(floatval(str_replace(['$', ','], '', $packageDetails['price_range'])), 2) ?></div>
                                        <small class="text-muted">per person</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="mb-3">What's Included</h5>
                            <?php foreach (explode("\n", $packageDetails['inclusions']) as $inclusion): ?>
                                <?php if (!empty(trim($inclusion))): ?>
                                    <div class="inclusion-item">
                                        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars(trim($inclusion)) ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="mb-3">Not Included</h5>
                            <?php foreach (explode("\n", $packageDetails['exclusions']) as $exclusion): ?>
                                <?php if (!empty(trim($exclusion))): ?>
                                    <div class="exclusion-item">
                                        <i class="bi bi-x-circle me-2"></i><?= htmlspecialchars(trim($exclusion)) ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="action-buttons">
                            <button class="action-btn btn-primary"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#packageInquiryModal"
                                    data-package-id="<?= $packageDetails['id'] ?>"
                                    data-package-title="<?= htmlspecialchars($packageDetails['title']) ?>">
                                Learn More
                            </button>
                            
                            <a href="https://wa.me/94782024424?text=I'm%20interested%20in%20package%20<?= urlencode($packageDetails['title']) ?>" 
                               class="action-btn btn-success" target="_blank">
                                <i class="bi bi-whatsapp me-2"></i>Chat on WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php else: ?>
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1>Travel Packages</h1>
            <p class="subtitle">Crafted for You</p>
            <p>Discover stunning destinations, unique experiences, and custom itineraries that make your travel dreams real</p>
        </div>
    </div>

    <!-- Packages Grid -->
    <div class="packages-container">
        <div class="container">
            <?php if (mysqli_num_rows($packagesResult) > 0): ?>
            <div class="packages-grid">
                <?php while ($package = mysqli_fetch_assoc($packagesResult)): 
                    $hasOffer = !is_null($package['best_offer_price']);
                    $originalPrice = floatval(str_replace(['$', ','], '', $package['price_range']));
                ?>
                <div class="package-card" onclick="location.href='packages.php?id=<?= $package['id'] ?>'" data-hero-image="<?= !empty($package['hero_image']) ? 'uploads/hero_images/' . htmlspecialchars($package['hero_image']) : 'uploads/packages/' . htmlspecialchars($package['image']) ?>">
                    <div class="package-card-image">
                        <img src="uploads/packages/<?= htmlspecialchars($package['image']) ?>" 
                             alt="<?= htmlspecialchars($package['title']) ?>">
                        <div class="duration-badge"><?= htmlspecialchars($package['duration']) ?> Days Trip</div>
                        <div class="package-overlay">
                            <h3 class="package-title"><?= htmlspecialchars($package['title']) ?></h3>
                            <p class="package-description">We offer more than just trips. We craft journeys that immerse you in the culture, history of every destination.</p>
                        </div>
                    </div>
                    <div class="package-card-body">
                        <div class="package-features">
                            <div class="feature-item">
                                <i class="bi bi-geo-alt"></i>
                                Multiple Locations
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-people"></i>
                                All Group Sizes
                            </div>
                        </div>
                        
                        <div class="package-pricing">
                            <div class="price-section">
                                <div class="price-from">From</div>
                                <?php if ($hasOffer): ?>
                                    <div class="price-amount">
                                        <span class="original-price">$<?= number_format($originalPrice, 0) ?></span>
                                        <span class="offer-price">$<?= number_format($package['best_offer_price'], 0) ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="price-amount">$<?= number_format($originalPrice, 0) ?></div>
                                <?php endif; ?>
                                <small class="text-muted">per person</small>
                            </div>
                            <a href="packages.php?id=<?= $package['id'] ?>" class="learn-more-btn">Learn More</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <h3>No packages available at the moment</h3>
                <p class="text-muted">Please check back later for exciting travel packages.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Package Inquiry Modal -->
<div class="modal fade" id="packageInquiryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="process_inquiry.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Package Inquiry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="package_type" value="predefined">
                    <input type="hidden" name="package_id" id="modalPackageId">
                    <input type="hidden" name="offer_price" id="modalOfferPrice">
                    <input type="hidden" name="full_phone" id="fullPhoneNumber">
                    
                    <div class="mb-3">
                        <label class="form-label">Package</label>
                        <input type="text" class="form-control" id="modalPackageTitle" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Your Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Country</label>
                        <select class="form-select" id="country" name="country" required>
                            <option value="">Select Country</option>
                            <option value="Sri Lanka" data-code="94">Sri Lanka (+94)</option>
                            <option value="India" data-code="91">India (+91)</option>
                            <option value="United States" data-code="1">United States (+1)</option>
                            <option value="United Kingdom" data-code="44">United Kingdom (+44)</option>
                            <option value="Australia" data-code="61">Australia (+61)</option>
                            <option value="Germany" data-code="49">Germany (+49)</option>
                            <option value="France" data-code="33">France (+33)</option>
                            <option value="Japan" data-code="81">Japan (+81)</option>
                            <option value="China" data-code="86">China (+86)</option>
                            <option value="Other">Other (Custom Country Code)</option>
                        </select>
                        <div class="mt-2 d-none" id="customCountryContainer">
                            <label class="form-label">Custom Country Code</label>
                            <div class="input-group">
                                <span class="input-group-text">+</span>
                                <input type="text" class="form-control" id="customCountryCode" 
                                       name="custom_country_code" placeholder="Country code" pattern="[0-9]{1,4}">
                            </div>
                        </div>
                        <input type="hidden" id="countryCode" name="country_code">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone/WhatsApp</label>
                        <div class="input-group">
                            <span class="input-group-text" id="country-code-prefix">+</span>
                            <input type="tel" class="form-control" id="phoneNumber" 
                                   name="phone" placeholder="Phone number without country code" 
                                   pattern="[0-9]{8,15}" required>
                        </div>
                        <small class="text-muted">Enter your phone number without country code</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Travel Start Date (Expectation)</label>
                        <input type="date" class="form-control" name="travel_dates" placeholder="Preferred dates or flexible">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Number of Travelers</label>
                        <input type="number" class="form-control" name="travelers" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Special Requests</label>
                        <textarea class="form-control" name="special_requests" rows="3"></textarea>
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

<!-- Footer -->
<?php include 'components/footer.php' ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hero image hover effect for package cards
    const packageCards = document.querySelectorAll('.package-card');
    const heroSection = document.querySelector('.hero-section');
    
    if (packageCards.length && heroSection) {
        const defaultHero = heroSection.style.backgroundImage;
        
        packageCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                const heroImage = this.getAttribute('data-hero-image');
                if (heroImage) {
                    heroSection.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), url('${heroImage}')`;
                }
            });
            
            card.addEventListener('mouseleave', function() {
                heroSection.style.backgroundImage = defaultHero;
            });
        });
    }

    // Country code handling
    const countrySelect = document.getElementById('country');
    const countryCodePrefix = document.getElementById('country-code-prefix');
    const customCountryContainer = document.getElementById('customCountryContainer');
    const customCountryCode = document.getElementById('customCountryCode');
    const countryCodeInput = document.getElementById('countryCode');
    const form = document.querySelector('#packageInquiryModal form');
    
    countrySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value === 'Other') {
            customCountryContainer.classList.remove('d-none');
            countryCodePrefix.textContent = '+';
            customCountryCode.focus();
            countryCodeInput.value = '';
        } else {
            customCountryContainer.classList.add('d-none');
            const code = selectedOption.getAttribute('data-code') || '';
            countryCodePrefix.textContent = '+' + code;
            countryCodeInput.value = code;
        }
    });
    
    customCountryCode.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
        countryCodePrefix.textContent = '+' + this.value;
        countryCodeInput.value = this.value;
    });
    
    // Initialize with default country
    const sriLankaOption = countrySelect.querySelector('option[value="Sri Lanka"]');
    if (sriLankaOption) {
        const code = sriLankaOption.getAttribute('data-code');
        countryCodePrefix.textContent = '+' + code;
        countryCodeInput.value = code;
    }
    
    // Form validation
    form.addEventListener('submit', function(e) {
        if (!countryCodeInput.value) {
            e.preventDefault();
            alert('Please select or enter a valid country code');
            return;
        }
        
        const phoneNumber = document.getElementById('phoneNumber').value;
        document.getElementById('fullPhoneNumber').value = '+' + countryCodeInput.value + phoneNumber;
    });

    // Package inquiry modal
    var inquiryModal = document.getElementById('packageInquiryModal');
    if (inquiryModal) {
        inquiryModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var packageId = button.getAttribute('data-package-id');
            var packageTitle = button.getAttribute('data-package-title');
            var offerPrice = button.getAttribute('data-offer-price') || '';
            
            document.getElementById('modalPackageId').value = packageId;
            document.getElementById('modalPackageTitle').value = packageTitle;
            document.getElementById('modalOfferPrice').value = offerPrice;
        });
    }

    // Back to Top Button
    const backToTop = document.getElementById("backToTop");
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.style.display = "flex";
            } else {
                backToTop.style.display = "none";
            }
        });
        
        backToTop.addEventListener('click', function() {
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
    }

    // Smooth scrolling animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.package-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    // Loading states for buttons
    document.querySelectorAll('.learn-more-btn, .action-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (this.tagName === 'BUTTON' && this.type === 'submit') {
                return;
            }
            
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Loading...';
            this.disabled = true;
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 1000);
        });
    });

    // AJAX form submission
    const inquiryForm = document.querySelector('#packageInquiryModal form');
    if (inquiryForm) {
        inquiryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = inquiryForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            
            fetch('process_inquiry.php', {
                method: 'POST',
                body: new FormData(inquiryForm)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('packageInquiryModal'));
                    modal.hide();
                    
                    const successAlert = document.createElement('div');
                    successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed';
                    successAlert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
                    successAlert.innerHTML = `
                        <strong>Success!</strong> Your inquiry has been submitted successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(successAlert);
                    
                    setTimeout(() => {
                        if (successAlert.parentNode) {
                            successAlert.parentNode.removeChild(successAlert);
                        }
                    }, 5000);
                    
                    inquiryForm.reset();
                } else {
                    alert('Error: ' + (data.message || 'Something went wrong. Please try again.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Floating buttons animation
    const floatingBtns = document.querySelectorAll('.floating-btn');
    floatingBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.animation = 'none';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.animation = 'pulse 2s infinite';
        });
    });
});
</script>
</body>
</html>