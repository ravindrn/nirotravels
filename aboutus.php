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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-black: #000000;
            --primary-blue: #0c2261;
            --primary-white: #ffffff;
            --text-gray: #6c757d;
            --light-gray: #f8f9fa;
            --darker-blue: #081744;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Urbanist', sans-serif;
            color: var(--primary-black);
            line-height: 1.7;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
        }
        
        /* Smooth scrolling and animations */
        html {
            scroll-behavior: smooth;
        }
        
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Hero Section */
        .about-hero {
            position: relative;
            height: 100vh;
            min-height: 700px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-white);
            overflow: hidden;
        }
        
        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
            transition: transform 20s ease-in-out;
        }
        
        .hero-background:hover {
            transform: scale(1.05);
        }
        

        
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 900px;
            padding: 0 20px;
            animation: heroContentFloat 3s ease-out;
        }
        
        @keyframes heroContentFloat {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .about-hero h1 {
            font-size: 4.5rem;
            font-weight: 700;
            margin-bottom: 24px;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.8);
            letter-spacing: -1px;
        }
        
        .about-hero p {
            font-size: 1.4rem;
            margin-bottom: 40px;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.7);
            font-weight: 400;
        }
        
        /* Floating elements */
        .floating-element {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        /* Story Section */
        .story-section {
            padding: 120px 0;
            background: var(--primary-white);
            position: relative;
        }
        
        .story-container {
            background: var(--primary-white);
            box-shadow: 0 30px 60px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: -100px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(12, 34, 97, 0.1);
            margin-bottom:0;
        }
        
        .story-image-container {
            position: relative;
            overflow: hidden;
            height: 500px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .story-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }
        
        .story-image-container:hover img {
            transform: scale(1.08);
        }
        
        .story-content h2 {
            font-size: 3rem;
            color: black;
            margin-bottom: 30px;
            position: relative;
        }
        
        .story-content h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 4px;
      
            border-radius: 2px;
        }
        
        .story-content p {
            font-size: 1.1rem;
            color: var(--text-gray);
            margin-bottom: 25px;
            line-height: 1.8;
        }

       /* Mission Section */
    .mission-section {
        padding: 20px 0;
        background: url('assets/img/mission-bg.jpg') no-repeat center center/cover; /* your image */
        position: relative;
        margin-bottom:50px;
    }

    .mission-section::before {
        content: none; /* remove any overlay */
    }

    .section-title {
        text-align: center;
        margin-bottom: 60px;
        position: relative;
        z-index: 1;
        color:black;
    }

    .section-title h2 {
        font-size: 3.2rem;
        color: black; /* visible on image depending on brightness */
        margin-bottom: 20px;
        position: relative;
    }

    .mission-content {
        position: relative;
        z-index: 1;
        background: var(--primary-white); /* fully opaque */
        padding: 50px;
        box-shadow: 0 20px 50px rgba(12, 34, 97, 0.1);
        border: 1px solid rgba(12, 34, 97, 0.05);
    }

    .mission-content .lead {
        font-size: 1.3rem;
        color: var(--primary-blue);
        font-weight: 500;
        margin-bottom: 25px;
    }

    .mission-content p {
        font-size: 1.1rem;
        color: var(--text-gray);
    }


        
        /* Stats Section */
        .stats-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--darker-blue) 100%);
            color: var(--primary-white);
            position: relative;
            overflow: hidden;
        }
        
        .stats-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveStars 20s linear infinite;
            z-index: 0;
        }
        
        @keyframes moveStars {
            0% { transform: translate(0, 0); }
            100% { transform: translate(-50px, -50px); }
        }
        
        .stat-item {
            text-align: center;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }
        
        .stat-item:hover {
            transform: translateY(-10px);
        }
        
        .stat-number {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 15px;
            background: linear-gradient(45deg, var(--primary-white), #e3f2fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: 'Playfair Display', serif;
        }
        
        .stat-text {
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 500;
            opacity: 0.9;
        }
        
        /* Specialties Section */
        .specialties-section {
            padding: 120px 0;
            background: var(--light-gray);
            position: relative;
        }
        
        .specialty-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }
        
        .specialty-card {
            background: var(--primary-white);
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(12, 34, 97, 0.08);
            transition: all 0.4s ease;
            border: 1px solid rgba(12, 34, 97, 0.05);
            position: relative;
            overflow: hidden;
        }
        
        .specialty-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(12, 34, 97, 0.05), transparent);
            transition: left 0.6s ease;
        }
        
        .specialty-card:hover::before {
            left: 100%;
        }
        
        .specialty-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(12, 34, 97, 0.15);
        }
        
        .specialty-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary-blue), var(--darker-blue));
            color: var(--primary-white);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2.2rem;
            transition: all 0.4s ease;
            position: relative;
            z-index: 1;
        }
        
        .specialty-card:hover .specialty-icon {
            background: linear-gradient(135deg, var(--primary-black), var(--primary-blue));
            transform: scale(1.1) rotate(360deg);
        }
        
        .specialty-card h3 {
            font-size: 1.6rem;
            color: var(--primary-blue);
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .specialty-card p {
            color: var(--text-gray);
            font-size: 1rem;
            line-height: 1.7;
        }
        
        /* Team Section */
        .team-section {
            padding: 120px 0;
            background: var(--primary-white);
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }
        
        .team-card {
            background: var(--primary-white);
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            position: relative;
            border: 1px solid rgba(12, 34, 97, 0.05);
        }
        
        .team-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }
        
        .team-image {
            height: 320px;
            overflow: hidden;
            position: relative;
        }
        
        .team-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        
        .team-card:hover .team-image img {
            transform: scale(1.1);
        }
        
        .team-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(12, 34, 97, 0.8), rgba(0, 0, 0, 0.6));
            opacity: 0;
            transition: opacity 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .team-card:hover .team-overlay {
            opacity: 1;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-links a {
            width: 45px;
            height: 45px;
            background: var(--primary-white);
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }
        
        .social-links a:hover {
            background: var(--primary-blue);
            color: var(--primary-white);
            transform: scale(1.1);
        }
        
        .team-info {
            padding: 30px 25px;
            text-align: center;
        }
        
        .team-info h4 {
            font-size: 1.4rem;
            color: var(--primary-blue);
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .team-info .role {
            color: var(--primary-black);
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 1rem;
        }
        
        .team-info .specialty {
            color: var(--text-gray);
            font-size: 0.9rem;
            font-style: italic;
        }
        
        /* Values Section */
        .values-section {
            padding: 120px 0;
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--primary-white) 100%);
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }
        
        .value-card {
            background: var(--primary-white);
            padding: 45px 35px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(12, 34, 97, 0.08);
            transition: all 0.4s ease;
            border: 1px solid rgba(12, 34, 97, 0.05);
            position: relative;
            overflow: hidden;
        }
        
        .value-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-black));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .value-card:hover::before {
            transform: scaleX(1);
        }
        
        .value-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(12, 34, 97, 0.15);
        }
        
        .value-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary-blue), var(--darker-blue));
            color: var(--primary-white);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2.5rem;
            transition: all 0.4s ease;
            position: relative;
        }
        
        .value-card:hover .value-icon {
            background: linear-gradient(135deg, var(--primary-black), var(--primary-blue));
            transform: scale(1.1) rotate(360deg);
        }
        
        .value-card h3 {
            font-size: 1.6rem;
            color: var(--primary-blue);
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .value-card p {
            color: var(--text-gray);
            font-size: 1rem;
            line-height: 1.7;
        }
        
        /* CTA Section */
.cta-section {
    padding: 100px 0;
    background: url('assets/img/1.jpg') no-repeat center center/cover;
    color: var(--primary-white);
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 100px;
}

.cta-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0, 27, 68, 0.65); /* dark blue with 65% transparency */
    z-index: 0;
}

.cta-section .container {
    position: relative;
    z-index: 1;
}

.cta-title {
    font-size: 3rem;
    margin-bottom: 25px;
    font-weight: 700;
}

.cta-subtitle {
    font-size: 1.3rem;
    max-width: 800px;
    margin: 0 auto 40px;
    opacity: 0.9;
    line-height: 1.6;
}

.cta-buttons .btn {
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

/* Button hover effect */
.cta-buttons .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(12, 34, 97, 0.1), transparent);
    transition: left 0.6s ease;
}

.cta-buttons .btn:hover::before {
    left: 100%;
}

.cta-buttons .btn:hover {
    background: var(--primary-black) !important;
    color: var(--primary-white) !important;
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.3);
}

        
        /* Responsive Design */
        @media (max-width: 768px) {
            .about-hero {
                min-height: 70vh;
                padding: 80px 0;
            }
            
            .about-hero h1 {
                font-size: 2.8rem;
            }
            
            .about-hero p {
                font-size: 1.2rem;
            }
            
            .story-container {
                margin-top: 0;
                padding: 40px 30px;
            }
            
            .story-content h2 {
                font-size: 2.2rem;
            }
            
            .section-title h2 {
                font-size: 2.5rem;
            }
            
            .stat-number {
                font-size: 3rem;
            }
            
            .cta-section h2 {
                font-size: 2.2rem;
            }
            
            .specialty-grid,
            .values-grid,
            .team-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
        
        @media (max-width: 576px) {
            .story-container,
            .mission-content {
                padding: 30px 20px;
            }
            
            .about-hero h1 {
                font-size: 2.2rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
        }
        
        /* Scroll animations */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s ease;
        }
        
        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Background pattern for sections */
        .pattern-bg {
            background-image: 
                radial-gradient(circle at 25px 25px, rgba(12, 34, 97, 0.05) 2px, transparent 0),
                radial-gradient(circle at 75px 75px, rgba(0, 0, 0, 0.05) 2px, transparent 0);
            background-size: 100px 100px;
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
        <?php else: ?>
            <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" 
                 alt="Sri Lanka Landscape" 
                 class="hero-background">
        <?php endif; ?>
        
        <div class="hero-overlay"></div>
        
        <!-- Floating decorative elements -->
        <div class="floating-element" style="top: 20%; left: 10%; font-size: 2rem;">
            <i class="bi bi-geo-alt"></i>
        </div>
        <div class="floating-element" style="top: 70%; right: 15%; font-size: 1.5rem; animation-delay: -2s;">
            <i class="bi bi-camera"></i>
        </div>
        
        <div class="hero-content">
            <h1 class="fade-in"><?php echo htmlspecialchars($hero['title']); ?></h1>
            <p class="fade-in"><?php echo htmlspecialchars($hero['description']); ?></p>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="story-section pattern-bg">
        <div class="container">
            <div class="row story-container animate-on-scroll">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="story-image-container">
                        <?php if (!empty($story['image'])): ?>
                            <img src="uploads/about/<?php echo htmlspecialchars($story['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($story['title']); ?>">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1584463618523-1e06beb4cdd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" 
                                 alt="Sri Lankan Culture">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="story-content">
                        <h2><?php echo htmlspecialchars($story['title']); ?></h2>
                        <?php foreach ($content_paragraphs as $index => $paragraph): ?>
                            <p class="animate-on-scroll" style="animation-delay: <?php echo $index * 0.2; ?>s;">
                                <?php echo htmlspecialchars(trim($paragraph)); ?>
                            </p>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
        <div class="container">
            <div class="section-title animate-on-scroll">
                <h2><?= htmlspecialchars($mission['heading']) ?></h2>
            </div>
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="mission-content animate-on-scroll">
                        <p class="lead"><?= htmlspecialchars($mission['lead_text']) ?></p>
                        <?php if (!empty($mission['additional_text'])): ?>
                            <p><?= htmlspecialchars($mission['additional_text']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <div class="stat-item animate-on-scroll" style="animation-delay: <?php echo $i * 0.2; ?>s;">
                            <div class="stat-number"><?= htmlspecialchars($stats['stat'.$i.'_number']) ?></div>
                            <div class="stat-text"><?= htmlspecialchars($stats['stat'.$i.'_text']) ?></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- Sri Lanka Specialties Section -->
    <section class="specialties-section">
        <div class="container">
            <div class="section-title animate-on-scroll">
                <h2>Our Sri Lanka Expertise</h2>
            </div>
            <div class="specialty-grid">
                <div class="specialty-card animate-on-scroll" style="animation-delay: 0.1s;">
                    <div class="specialty-icon">
                        <i class="bi bi-tree"></i>
                    </div>
                    <h3>Wildlife Safaris</h3>
                    <p>Leopards in Yala, elephants in Minneriya, whales in Mirissa - our naturalist guides know where and when to find Sri Lanka's incredible wildlife.</p>
                </div>
                <div class="specialty-card animate-on-scroll" style="animation-delay: 0.2s;">
                    <div class="specialty-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <h3>Cultural Heritage</h3>
                    <p>From ancient Anuradhapura to the rock fortress of Sigiriya, we bring Sri Lanka's 2500+ years of history to life.</p>
                </div>
                <div class="specialty-card animate-on-scroll" style="animation-delay: 0.3s;">
                    <div class="specialty-icon">
                        <i class="bi bi-cup-hot"></i>
                    </div>
                    <h3>Tea Country Experiences</h3>
                    <p>Private visits to tea estates, factory tours, and stays in colonial bungalows - we have special access throughout the hill country.</p>
                </div>
                <div class="specialty-card animate-on-scroll" style="animation-delay: 0.4s;">
                    <div class="specialty-icon">
                        <i class="bi bi-umbrella"></i>
                    </div>
                    <h3>Beach Getaways</h3>
                    <p>Secret coves, surf spots, and the best seafood shacks - we know Sri Lanka's stunning coastline intimately.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-title animate-on-scroll">
                <h2>Meet Our Local Guides</h2>
            </div>
            <div class="team-grid">
                <div class="team-card animate-on-scroll" style="animation-delay: 0.1s;">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1580927752452-89d86da3fa0a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Nimal Perera">
                        <div class="team-overlay">
                            <div class="social-links">
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-info">
                        <h4>Nimal Perera</h4>
                        <p class="role">Founder & Lead Guide</p>
                        <p class="specialty">Specializes in cultural tours</p>
                    </div>
                </div>
                
                <div class="team-card animate-on-scroll" style="animation-delay: 0.2s;">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1583864697784-a0efc8379f70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=688&q=80" alt="Priyanthi Fernando">
                        <div class="team-overlay">
                            <div class="social-links">
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-info">
                        <h4>Priyanthi Fernando</h4>
                        <p class="role">Wildlife Expert</p>
                        <p class="specialty">Yala & Wilpattu specialist</p>
                    </div>
                </div>
                
                <div class="team-card animate-on-scroll" style="animation-delay: 0.3s;">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1583864697784-a0efc8379f70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=688&q=80" alt="Sanath Jayasuriya">
                        <div class="team-overlay">
                            <div class="social-links">
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-info">
                        <h4>Sanath Jayasuriya</h4>
                        <p class="role">Adventure Guide</p>
                        <p class="specialty">Hiking & water sports</p>
                    </div>
                </div>
                
                <div class="team-card animate-on-scroll" style="animation-delay: 0.4s;">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1580927752452-89d86da3fa0a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Kamala Ratnayake">
                        <div class="team-overlay">
                            <div class="social-links">
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-info">
                        <h4>Kamala Ratnayake</h4>
                        <p class="role">Culinary Guide</p>
                        <p class="specialty">Food & market tours</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <div class="section-title animate-on-scroll">
                <h2>Our Sri Lankan Values</h2>
            </div>
            <div class="values-grid">
                <div class="value-card animate-on-scroll" style="animation-delay: 0.1s;">
                    <div class="value-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3>Ayubowan Spirit</h3>
                    <p>We embody traditional Sri Lankan hospitality, treating every guest as family with warmth and respect.</p>
                </div>
                
                <div class="value-card animate-on-scroll" style="animation-delay: 0.2s;">
                    <div class="value-icon">
                        <i class="bi bi-tree"></i>
                    </div>
                    <h3>Sustainable Tourism</h3>
                    <p>We're committed to protecting Sri Lanka's natural wonders and supporting local communities.</p>
                </div>
                
                <div class="value-card animate-on-scroll" style="animation-delay: 0.3s;">
                    <div class="value-icon">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h3>Authentic Experiences</h3>
                    <p>We take you beyond tourist sites to discover the real Sri Lanka known only to locals.</p>
                </div>
            </div>
        </div>
    </section>

<!-- CTA Section -->
<section class="cta-section">
  <div class="container">
    <h2 class="cta-title">Ready to Explore Sri Lanka With Us?</h2>
    <p class="cta-subtitle">Let our local experts create your perfect Sri Lankan adventure.</p>
    <div class="cta-buttons d-flex justify-content-center gap-3 flex-wrap">
      <a href="contact.php" class="btn btn-outline-light btn-lg px-4 py-2">Plan Your Trip</a>
    </div>
  </div>
</section>


    <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        // Observe all elements with animate-on-scroll class
        document.addEventListener('DOMContentLoaded', () => {
            const animateElements = document.querySelectorAll('.animate-on-scroll');
            animateElements.forEach(el => observer.observe(el));
            
            // Initial fade-in for hero elements
            setTimeout(() => {
                const fadeInElements = document.querySelectorAll('.fade-in');
                fadeInElements.forEach((el, index) => {
                    setTimeout(() => {
                        el.classList.add('visible');
                    }, index * 300);
                });
            }, 500);
        });

        // Smooth parallax effect for hero background
  
        // Counter animation for stats
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/\D/g, ''));
                const suffix = counter.textContent.replace(/\d/g, '');
                let current = 0;
                const increment = target / 100;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target + suffix;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current) + suffix;
                    }
                }, 20);
            });
        }

        // Trigger counter animation when stats section is visible
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.addEventListener('DOMContentLoaded', () => {
            const statsSection = document.querySelector('.stats-section');
            if (statsSection) {
                statsObserver.observe(statsSection);
            }
        });

        // Add subtle hover effects to cards
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.team-card, .value-card, .specialty-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-15px) rotateX(5deg)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) rotateX(0deg)';
                });
            });
        });

        // Add loading animation
        window.addEventListener('load', () => {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });
    </script>
</body>
</html>