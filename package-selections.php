<?php
// Itinerary Section
if ($itinerary_result && mysqli_num_rows($itinerary_result) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Tour Itinerary</h2>
        <div class="itinerary-container">
            <?php while ($day = mysqli_fetch_assoc($itinerary_result)): ?>
            <div class="itinerary-day">
                <h4>Day <?= $day['day_number'] ?>: <?= htmlspecialchars($day['title']) ?></h4>
                <p><?= nl2br(htmlspecialchars($day['description'])) ?></p>
                <?php if (!empty($day['meals_included'])): ?>
                <p class="text-muted"><i class="bi bi-utensils me-2"></i> <?= htmlspecialchars($day['meals_included']) ?></p>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Destinations Section
if ($destinations_result && mysqli_num_rows($destinations_result) > 0): ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Destination Highlights</h2>
        <div class="row">
            <?php while ($destination = mysqli_fetch_assoc($destinations_result)): ?>
            <div class="col-md-4 mb-4">
                <div class="destination-card">
                    <img src="assets/img/destinations/<?= htmlspecialchars($destination['image']) ?>" 
                         class="destination-img w-100" 
                         alt="<?= htmlspecialchars($destination['title_line1'] . ' ' . $destination['title_line2']) ?>">
                    <div class="p-4">
                        <h5><?= htmlspecialchars($destination['title_line1']) ?> 
                            <span class="text-primary"><?= htmlspecialchars($destination['title_line2']) ?></span>
                        </h5>
                        <p><?= nl2br(htmlspecialchars($destination['description'])) ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Hotels Section
if ($hotels_result && mysqli_num_rows($hotels_result) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Featured Accommodations</h2>
        <div class="row">
            <?php while ($hotel = mysqli_fetch_assoc($hotels_result)): ?>
            <div class="col-lg-6 mb-4">
                <div class="hotel-card bg-white">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img src="assets/img/hotels/<?= htmlspecialchars($hotel['image']) ?>" 
                                 class="img-fluid h-100" 
                                 alt="<?= htmlspecialchars($hotel['name']) ?>">
                        </div>
                        <div class="col-md-7">
                            <div class="p-4">
                                <h4><?= htmlspecialchars($hotel['name']) ?></h4>
                                <div class="mb-3">
                                    <?php for ($i = 0; $i < $hotel['star_rating']; $i++): ?>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    <?php endfor; ?>
                                </div>
                                <p><?= htmlspecialchars($hotel['description']) ?></p>
                                <ul class="list-unstyled">
                                    <li><i class="bi bi-check-circle-fill text-primary me-2"></i> <?= htmlspecialchars($hotel['room_type']) ?></li>
                                    <li><i class="bi bi-check-circle-fill text-primary me-2"></i> <?= htmlspecialchars($hotel['amenities']) ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Gallery Section
if ($gallery_result && mysqli_num_rows($gallery_result) > 0): ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Gallery</h2>
        <div class="row g-3">
            <?php while ($image = mysqli_fetch_assoc($gallery_result)): ?>
            <div class="col-md-4 col-sm-6">
                <a href="assets/img/packages/gallery/<?= htmlspecialchars($image['image_path']) ?>" data-lightbox="package-gallery">
                    <img src="assets/img/packages/gallery/<?= htmlspecialchars($image['image_path']) ?>" 
                         alt="Package image" class="img-fluid rounded shadow-sm">
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>