<?php
require_once 'admin/includes/db.php';

// Fetch hero section content
require_once 'admin/includes/db.php';

// Fetch hero content with error handling
$hero = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_hero LIMIT 1")) ?: [
    'title' => 'Discover Ceylon Wonders',
    'description' => 'Your trusted local experts for authentic Sri Lankan travel experiences',
    'background_image' => null
];

$story = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_story LIMIT 1")) ?: [
    'title' => 'Our Sri Lankan Story',
    'content' => "Founded by three childhood friends from Kandy...\n\nWe're proud to be 100% Sri Lankan owned...",
    'image' => null
];

$stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_stats LIMIT 1")) ?: [
    'stat1_number' => '13+', 'stat1_text' => 'Years Showing Sri Lanka',
    'stat2_number' => '25', 'stat2_text' => 'Local Guides',
    'stat3_number' => '100+', 'stat3_text' => 'Happy Travelers',
    'stat4_number' => '100%', 'stat4_text' => 'Sri Lankan Team'
];

// Process story content
$content_paragraphs = !empty($story['content']) ? 
    array_filter(explode("\n\n", $story['content'])) : 
    ['Content coming soon...'];

$mission = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_mission LIMIT 1"));
if (!$mission) {
    $mission = [
        'heading' => 'Our Mission',
        'lead_text' => 'To provide authentic, responsible, and unforgettable Sri Lankan travel experiences...',
        'additional_text' => 'We measure our success by the smiles of our guests...'
    ];
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Ceylon Wonders - Sri Lanka's Premier Tour Guide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2a5a3c; /* Sri Lankan green */
            --secondary-color: #f9a825; /* Saffron yellow */
            --accent-color: #c62828; /* Deep red */
            --dark-color: #1a237e; /* Deep blue */
            --light-color: #f5f5f5;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Hero Section */
        .about-hero {
        position: relative;
        color: white;
        padding: 180px 0; /* Increased padding */
        text-align: center;
        overflow: hidden;
        min-height: 80vh; /* Minimum height of viewport */
    }
    
    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }
    
    .hero-content {
        position: relative;
        z-index: 2; /* Higher than overlay */
        text-shadow: 2px 2px 5px rgba(0,0,0,0.8);
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .about-hero h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
    }
    
    .about-hero p {
        font-size: 1.5rem;
        margin-bottom: 30px;
    }
    
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6));
        z-index: 1;
    }
    
    @media (max-width: 768px) {
        .about-hero {
            padding: 120px 0;
            min-height: 60vh;
        }
        
        .about-hero h1 {
            font-size: 2.5rem;
        }
        
        .about-hero p {
            font-size: 1.2rem;
        }
    }
        
        /* Section Styling */
        .about-section {
            padding: 80px 0;
        }
        
        .section-title {
            position: relative;
            margin-bottom: 50px;
            text-align: center;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: inline-block;
            padding-bottom: 15px;
        }
        
        .section-title h2:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background-color: var(--secondary-color);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
        
        /* Our Story Section */
        .story-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            margin-top: -80px;
            position: relative;
            z-index: 1;
        }
        
        .story-img {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 100%;
            min-height: 300px;
        }
        
        .story-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .story-img:hover img {
            transform: scale(1.05);
        }
        
        /* Team Section */
        .team-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }
        
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .team-img {
            height: 300px;
            overflow: hidden;
        }
        
        .team-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .team-card:hover .team-img img {
            transform: scale(1.1);
        }
        
        .team-info {
            padding: 20px;
            text-align: center;
        }
        
        .team-info h4 {
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .team-info p {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .social-links {
            margin-top: 15px;
        }
        
        .social-links a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background: var(--secondary-color);
            color: var(--dark-color);
            border-radius: 50%;
            line-height: 36px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: var(--accent-color);
            color: white;
            transform: translateY(-3px);
        }
        
        /* Stats Section */
        .stats-section {
            background: linear-gradient(rgba(42, 90, 60, 0.9), rgba(42, 90, 60, 0.9)), 
                        url('https://images.unsplash.com/photo-1562613521-9e9b4e0e9e8f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .stat-item {
            padding: 30px 15px;
        }
        
        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--secondary-color);
        }
        
        .stat-text {
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Values Section */
        .value-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            text-align: center;
        }
        
        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .value-icon {
            width: 80px;
            height: 80px;
            background: var(--secondary-color);
            color: var(--dark-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }
        
        .value-card:hover .value-icon {
            background: var(--accent-color);
            color: white;
        }
        
        .value-card h3 {
            color: var(--dark-color);
            margin-bottom: 15px;
        }
        
        /* Sri Lanka Special Section */
        .specialties-section {
            background-color: var(--light-color);
        }
        
        .specialty-item {
            display: flex;
            margin-bottom: 30px;
            align-items: center;
        }
        
        .specialty-icon {
            width: 60px;
            height: 60px;
            background: var(--secondary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 20px;
            flex-shrink: 0;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(to right, var(--primary-color), var(--dark-color));
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }
        
        .btn-cta {
            background: var(--secondary-color);
            color: var(--dark-color);
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-cta:hover {
            background: white;
            color: var(--accent-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 2.5rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .story-container {
                margin-top: 0;
            }
            
            .specialty-item {
                flex-direction: column;
                text-align: center;
            }
            
            .specialty-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }

       
    
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>

    <!-- Hero Section -->
    <section class="about-hero">
    <?php if (!empty($hero['background_image'])): ?>
        <img src="uploads/about/<?php echo htmlspecialchars($hero['background_image']); ?>" 
             alt="About Us Hero Image" 
             class="hero-background">
    <?php endif; ?>
    
    <div class="hero-overlay"></div>
    
    <div class="hero-content">
        <h1><?php echo htmlspecialchars($hero['title']); ?></h1>
        <p><?php echo htmlspecialchars($hero['description']); ?></p>
    </div>
</section>


    <!-- Our Story Section -->
   <section class="about-section">
    <div class="container">
        <div class="row story-container">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <?php if (!empty($story['image'])): ?>
                    <div class="story-img">
                        <img src="uploads/about/<?php echo htmlspecialchars($story['image']); ?>" 
                             alt="<?php echo htmlspecialchars($story['title']); ?>"
                             class="img-fluid rounded">
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <h2><?php echo htmlspecialchars($story['title']); ?></h2>
                <?php foreach ($content_paragraphs as $paragraph): ?>
                    <p><?php echo htmlspecialchars(trim($paragraph)); ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
    <!-- Mission Section -->
    <section class="about-section bg-light">
    <div class="container">
        <div class="section-title">
            <h2><?= htmlspecialchars($mission['heading']) ?></h2>
        </div>
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <p class="lead"><?= htmlspecialchars($mission['lead_text']) ?></p>
                <?php if (!empty($mission['additional_text'])): ?>
                    <p><?= htmlspecialchars($mission['additional_text']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

    <!-- Stats Section -->
    <section class="stats-section">
    <div class="container">
        <div class="row">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= htmlspecialchars($stats['stat'.$i.'_number']) ?></div>
                        <div class="stat-text"><?= htmlspecialchars($stats['stat'.$i.'_text']) ?></div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

    <!-- Sri Lanka Specialties Section -->
    <section class="about-section specialties-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Sri Lanka Expertise</h2>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="specialty-item">
                        <div class="specialty-icon">
                            <i class="bi bi-tree"></i>
                        </div>
                        <div>
                            <h3>Wildlife Safaris</h3>
                            <p>Leopards in Yala, elephants in Minneriya, whales in Mirissa - our naturalist guides know where and when to find Sri Lanka's incredible wildlife.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="specialty-item">
                        <div class="specialty-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <h3>Cultural Heritage</h3>
                            <p>From ancient Anuradhapura to the rock fortress of Sigiriya, we bring Sri Lanka's 2500+ years of history to life.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="specialty-item">
                        <div class="specialty-icon">
                            <i class="bi bi-cup-hot"></i>
                        </div>
                        <div>
                            <h3>Tea Country Experiences</h3>
                            <p>Private visits to tea estates, factory tours, and stays in colonial bungalows - we have special access throughout the hill country.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="specialty-item">
                        <div class="specialty-icon">
                            <i class="bi bi-umbrella"></i>
                        </div>
                        <div>
                            <h3>Beach Getaways</h3>
                            <p>Secret coves, surf spots, and the best seafood shacks - we know Sri Lanka's stunning coastline intimately.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="about-section">
        <div class="container">
            <div class="section-title">
                <h2>Meet Our Local Guides</h2>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="team-card">
                        <div class="team-img">
                            <img src="https://images.unsplash.com/photo-1580927752452-89d86da3fa0a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Nimal">
                        </div>
                        <div class="team-info">
                            <h4>Nimal Perera</h4>
                            <p>Founder & Lead Guide</p>
                            <p class="small">Specializes in cultural tours</p>
                            <div class="social-links">
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-card">
                        <div class="team-img">
                            <img src="https://images.unsplash.com/photo-1583864697784-a0efc8379f70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=688&q=80" alt="Priyanthi">
                        </div>
                        <div class="team-info">
                            <h4>Priyanthi Fernando</h4>
                            <p>Wildlife Expert</p>
                            <p class="small">Yala & Wilpattu specialist</p>
                            <div class="social-links">
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-card">
                        <div class="team-img">
                            <img src="https://images.unsplash.com/photo-1583864697784-a0efc8379f70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=688&q=80" alt="Sanath">
                        </div>
                        <div class="team-info">
                            <h4>Sanath Jayasuriya</h4>
                            <p>Adventure Guide</p>
                            <p class="small">Hiking & water sports</p>
                            <div class="social-links">
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="team-card">
                        <div class="team-img">
                            <img src="https://images.unsplash.com/photo-1580927752452-89d86da3fa0a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Kamala">
                        </div>
                        <div class="team-info">
                            <h4>Kamala Ratnayake</h4>
                            <p>Culinary Guide</p>
                            <p class="small">Food & market tours</p>
                            <div class="social-links">
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="about-section bg-light">
        <div class="container">
            <div class="section-title">
                <h2>Our Sri Lankan Values</h2>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3>Ayubowan Spirit</h3>
                        <p>We embody traditional Sri Lankan hospitality, treating every guest as family with warmth and respect.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-tree"></i>
                        </div>
                        <h3>Sustainable Tourism</h3>
                        <p>We're committed to protecting Sri Lanka's natural wonders and supporting local communities.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-stars"></i>
                        </div>
                        <h3>Authentic Experiences</h3>
                        <p>We take you beyond tourist sites to discover the real Sri Lanka known only to locals.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Explore Sri Lanka With Us?</h2>
            <p>Let our local experts create your perfect Sri Lankan adventure.</p>
            <a href="contact.php" class="btn btn-light btn-lg btn-cta">Plan Your Trip</a>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>