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
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Niro SL Travels</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous" />
  <link href="https://fonts.googleapis.com/css2?family=Lato&family=Playfair+Display:wght@700&family=Roboto&family=Poppins&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>


<!-- Navbar -->
<?php include 'components/header.php' ?>









<!-- Hero Section -->
<div id="home" class="card mt-4">
  <div id="destinationCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-inner">

      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="carousel-item <?= $first ? 'active' : '' ?>">
          <img src="assets/img/destinations/<?= htmlspecialchars($row['image']) ?>" class="card-img-top d-block w-100" alt="<?= htmlspecialchars($row['title_line2']) ?>">
          <div class="card-img-overlay d-flex flex-column justify-content-center align-items-start p-4">
            <h1 class="card-title" data-aos="fade-right" data-aos-delay="100"><?= htmlspecialchars($row['title_line1']) ?></h1>
            <h1 class="card-title" data-aos="fade-right" data-aos-delay="200"><?= htmlspecialchars($row['title_line2']) ?></h1>
            <p class="card-text" data-aos="fade-up" data-aos-delay="300">
              <?= nl2br(htmlspecialchars($row['description'])) ?>
            </p>
          </div>
        </div>
        <?php $first = false; ?>
      <?php } ?>

    </div>
  </div>
</div>










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


</script>

