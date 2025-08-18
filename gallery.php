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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Journey Gallery | <?php echo $company_name; ?></title>

  <!-- Fonts: Headings = Playfair Display, Paragraphs = Urbanist -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <!-- lightGallery -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/css/lightgallery.min.css"/>

  <style>
    :root{
      --primary-color:#0c2261;   /* Dark Blue */
      --secondary-color:#000000; /* Black */
      --accent-color:#ffffff;    /* White */
    }

    /* Typography */
    body{
      font-family:'Urbanist', sans-serif;
      background-color:#f8f9fa;
      color:#333;
    }
    h1,h2,h3,h4,h5{
      font-family:'Playfair Display', serif;
      color:var(--accent-color);
    }

    /* HERO — full image background with soft overlay */
    .hero-section {
    position: relative;
    background: url('assets/img/1.jpg') no-repeat center center/cover;
    height: 100vh;
    display: flex;                /* enable flexbox */
    justify-content: center;      /* center vertically */
    align-items: center;          /* center horizontally */
    text-align: center;
    color: #fff;
    margin-bottom: 60px;          /* already gives space below hero */
    overflow: hidden;
    }
    .hero-section::before {
    content: "";
    position: absolute; 
    inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.4), rgba(0,0,0,0.2)); /* transparent black gradient */
    z-index: 0;
    }
    .hero-inner{
      position:relative;
      max-width: 800px;  
      padding: 0 15px;
      z-index:1;
    }
    .gallery-title{
      font-size:4rem;
      font-weight:700;
      margin-bottom:16px;
      letter-spacing:.5px;
    }
    .gallery-subtitle{
      font-size:1.3rem;
      max-width:760px;
      margin:0 auto 28px;
      color:#f1f1f1;
      opacity:.95;
    }
    .hero-cta{
      background:#fff;
      color:var(--primary-color);
      border:none;
      border-radius:40px;
      padding:12px 28px;
      font-weight:700;
      transition:transform .2s ease, box-shadow .2s ease;
    }
    .hero-cta:hover{
      transform:translateY(-2px);
      box-shadow:0 10px 24px rgba(0,0,0,.18);
      color:var(--primary-color);
    }

    /* Section headings */
    .section-intro h2{
      font-size: 2.8rem;
      color:var(--secondary-color);
    }
    .section-intro p{
      color:#555;
    }

    /* Year chip */
    .year-heading{
      margin:60px 0 22px;
      text-align:center;
    }
    .year-heading h2{
      display:inline-block;
      font-size: 2.2rem;
      background:var(--primary-color);
      color:#fff;
      padding:10px 28px;
      border-radius:999px;
      font-weight:600;
      box-shadow:0 6px 18px rgba(12,34,97,.25);
      letter-spacing:.5px;
    }

    /* Gallery grid */
    .gallery-container{ padding:0 15px; }
    .gallery-grid{
      display:grid;
      grid-template-columns:repeat(auto-fill, minmax(280px,1fr));
      gap:20px;
      margin-bottom:44px;
    }

    .gallery-item{
      position:relative;
      overflow:hidden;
      box-shadow:0 8px 22px rgba(0,0,0,.12);
      transition:transform .25s ease, box-shadow .25s ease;
      aspect-ratio:4/3;
      background:#eef1f5;
      will-change:transform;
    }
    .gallery-item:hover{
      transform:translateY(-6px);
      box-shadow:0 16px 36px rgba(0,0,0,.18);
    }
    .gallery-item img{
      width:100%; height:100%;
      object-fit:cover; display:block;
      transition:transform .5s ease;
    }
    .gallery-item:hover img{ transform:scale(1.05); }

    .gallery-caption{
      position:absolute; left:0; right:0; bottom:0;
      background:linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,.7) 90%);
      color:#fff;
      padding:18px 16px;
      transform:translateY(100%);
      transition:transform .28s ease;
    }
    .gallery-item:hover .gallery-caption{ transform:translateY(0); }
    .gallery-caption h3{
      font-size:1.1rem; margin:0 0 4px;
      font-family:'Playfair Display', serif;
    }
    .gallery-caption p{
      margin:0; font-size:.9rem; opacity:.9;
    }

    /* CTA band */
    .cta-section {
    position: relative;
    background: url('assets/img/1.jpg') no-repeat center center/cover; /* Replace with your image */
    color: #fff;
    padding: 90px 0;
    text-align: center;
    margin-bottom: 100px; /* breathing room before footer */
    overflow: hidden;
    }
    .cta-section::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(12, 34, 97, 0.6); /* semi-transparent dark blue overlay */
    z-index: 0;
    }

    .cta-section .container {
    position: relative;
    z-index: 1; /* keeps text above overlay */
    }
    .cta-title{ font-size:2.8rem; margin-bottom:14px; }
    .cta-subtitle{
      font-size:1.2rem; max-width:760px; margin:0 auto 28px; color:#f3f5ff;
    }
    .btn-custom-trip{
      background:#fff; color:var(--primary-color);
      border-color:#fff; font-weight:700;
    }
    .btn-custom-trip:hover{
      background:#fff; color:var(--primary-color);
      border-color:#fff; font-weight:700;
    }
    .btn-outline-light{
      font-weight:700;
    }

    /* Utilities */
    .view-more-btn{
      display:block; text-align:center; margin:40px auto;
      padding:12px 30px; background-color:var(--primary-color);
      color:#fff; border:none; border-radius:30px; font-weight:600;
      transition:transform .2s ease, background .2s ease;
      text-decoration:none; max-width:240px;
    }
    .view-more-btn:hover{
      background-color:#081746;
      transform:translateY(-3px);
      color:#fff;
    }

    /* Responsive tweaks */
    @media (max-width: 992px){
      .gallery-title{ font-size:2.6rem; }
    }
    @media (max-width: 768px){
      .gallery-title{ font-size:2.2rem; }
      .gallery-grid{ grid-template-columns:repeat(auto-fill, minmax(240px,1fr)); }
      .cta-buttons .btn{ display:block; width:100%; margin-bottom:14px; }
      .hero-section{ padding:100px 0 90px; }
    }
  </style>
</head>
<body>
  <?php include 'components/header.php' ?>

  <!-- Hero Section (image background + overlay) -->
  <section class="hero-section">
    <div class="container hero-inner">
      <h1 class="gallery-title">Our Journey Gallery</h1>
      <p class="gallery-subtitle">
        Explore breathtaking moments from our past adventures. Each photo tells a story of discovery, excitement, and unforgettable experiences.
      </p>

    </div>
  </section>

  <!-- Gallery Section -->
  <section id="gallery" class="gallery-container">
    <div class="container">
      <div class="text-center mb-5 section-intro">
        <h2 class="display-6 fw-semibold">Memories That Last Forever</h2>
        <p class="lead mb-0">Scroll through years of incredible journeys and shared moments</p>
      </div>

      <?php foreach ($groupedImages as $year => $yearImages): ?>
        <div class="year-heading">
          <h2><?php echo $year; ?></h2>
        </div>

        <!-- Each year has its own lightgallery instance -->
        <div class="gallery-grid lightgallery" data-lg-group="<?php echo $year; ?>">
          <?php foreach ($yearImages as $image): ?>
            <a
              href="uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>"
              class="gallery-item"
              data-sub-html="<h4><?php echo htmlspecialchars($image['title']); ?></h4><p><?php echo htmlspecialchars($image['description']); ?></p>"
            >
              <img
                src="uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>"
                alt="<?php echo htmlspecialchars($image['title']); ?>"
                loading="lazy"
              />
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
      <div class="cta-buttons d-flex justify-content-center gap-3 flex-wrap">
        <a href="packages.php" class="btn btn-outline-light btn-lg px-4 py-2">View Packages</a>
        <a href="custom-trip.php" class="btn btn-outline-light btn-lg px-4 py-2">Create Your Custom Trip</a>
      </div>
    </div>
  </section>

  <!-- Footer (PHP include) -->
  <?php include 'components/footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.1/lightgallery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lg-zoom/2.7.1/lg-zoom.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lg-fullscreen/2.7.1/lg-fullscreen.min.js"></script>

  <script>
    // Initialize lightGallery for ALL yearly grids
    document.querySelectorAll('.lightgallery').forEach(function(grid){
      lightGallery(grid, {
        plugins:[lgZoom, lgFullscreen],
        speed:500,
        download:false,
        zoom:true,
        thumbnail:true
      });
    });

    // Smooth scrolling for on-page anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor=>{
      anchor.addEventListener('click', function(e){
        const target = document.querySelector(this.getAttribute('href'));
        if(!target) return;
        e.preventDefault();
        target.scrollIntoView({ behavior:'smooth' });
      });
    });

    // Fade/slide-in animation on scroll
    document.addEventListener('DOMContentLoaded', function(){
      const galleryItems = document.querySelectorAll('.gallery-item');
      const observer = new IntersectionObserver((entries)=>{
        entries.forEach(entry=>{
          if(entry.isIntersecting){
            entry.target.style.opacity = 1;
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, { threshold:0.12 });

      galleryItems.forEach((item, index)=>{
        item.style.opacity = 0;
        item.style.transform = 'translateY(18px)';
        item.style.transition = `all .5s ease ${index * 0.06}s`;
        observer.observe(item);
      });
    });
  </script>
</body>
</html>
