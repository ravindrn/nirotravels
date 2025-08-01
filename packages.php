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
    <title><?= $packageDetails ? htmlspecialchars($packageDetails['title']) : 'Tour Packages' ?> | Niro SL Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .price-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.9rem;
        }
        .original-price {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .offer-price {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .offer-card {
            border-left: 4px solid #dc3545;
        }
        .days-left-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: #ffc107;
            color: #000;
        }
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 5px;
            border: 1px solid #ced4da;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
</head>
<body>

<!-- Header -->
<?php include 'components/header.php' ?>

<main class="container my-5">
    <?php if ($packageId > 0 && $packageDetails): ?>
        <!-- Single Package View -->
        <div class="row">
            <div class="col-md-8">
                <h1><?= htmlspecialchars($packageDetails['title']) ?></h1>
                
                <?php if (!empty($packageOffers)): ?>
                <div class="alert alert-success mb-4">
                    <h4><i class="bi bi-tag-fill"></i> Special Offers Available!</h4>
                    <p class="mb-0">Book now to take advantage of these limited-time discounts.</p>
                </div>
                <?php endif; ?>
                
                <div class="package-image mb-4 position-relative">
                    <img src="uploads/packages/<?= htmlspecialchars($packageDetails['image']) ?>" 
                         alt="<?= htmlspecialchars($packageDetails['title']) ?>" 
                         class="img-fluid rounded">
                    <?php if (!empty($packageOffers)): ?>
                    <span class="badge bg-danger price-badge">
                        Save up to <?= 
                            $packageDetails['price_range'] && $packageOffers[0]['offer_price'] 
                            ? round(100 - (($packageOffers[0]['offer_price'] / 
                              floatval(str_replace(['$', ','], '', $packageDetails['price_range']))) * 100) )
                            : '0' 
                        ?>%
                    </span>
                    <?php endif; ?>
                </div>
                
                <div class="package-description mb-4">
                    <?= nl2br(htmlspecialchars($packageDetails['description'])) ?>
                </div>
                
                <div class="package-itinerary mb-4">
                    <h3>Itinerary</h3>
                    <?= nl2br(htmlspecialchars($packageDetails['itinerary'])) ?>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Package Details</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item">
                                <strong>Duration:</strong> <?= htmlspecialchars($packageDetails['duration']) ?> days
                            </li>
                            <li class="list-group-item">
                                <strong>Price:</strong> 
                                <?php if (!empty($packageOffers)): ?>
                                    <span class="original-price">$<?= number_format(floatval(str_replace(['$', ','], '', $packageDetails['price_range'])), 2) ?> per person</span>
                                    <span class="text-danger fw-bold"> $<?= number_format($packageOffers[0]['offer_price'], 2) ?> per person</span>
                                <?php else: ?>
                                    $<?= number_format(floatval(str_replace(['$', ','], '', $packageDetails['price_range'])), 2) ?> per person
                                <?php endif; ?>
                            </li>
                        </ul>
                        
                        <div class="mb-4">
                            <h5>Inclusions</h5>
                            <ul class="list-unstyled">
                                <?php foreach (explode("\n", $packageDetails['inclusions']) as $inclusion): ?>
                                    <?php if (!empty(trim($inclusion))): ?>
                                        <li><i class="bi bi-check-circle text-success me-2"></i><?= htmlspecialchars(trim($inclusion)) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <h5>Exclusions</h5>
                            <ul class="list-unstyled">
                                <?php foreach (explode("\n", $packageDetails['exclusions']) as $exclusion): ?>
                                    <?php if (!empty(trim($exclusion))): ?>
                                        <li><i class="bi bi-x-circle text-danger me-2"></i><?= htmlspecialchars(trim($exclusion)) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($packageOffers)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">Special Offers</h4>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($packageOffers as $offer): 
                            $days_left = floor((strtotime($offer['end_date']) - time()) / (60 * 60 * 24));
                            $discount_text = $offer['discount_type'] === 'percentage' 
                                ? $offer['discount_value'] . '% OFF' 
                                : '$' . $offer['discount_value'] . ' OFF';
                        ?>
                        <div class="p-3 offer-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1"><?= $discount_text ?></h5>
                                    <p class="mb-1">
                                        <span class="original-price">$<?= number_format($offer['original_price'], 2) ?> per person</span>
                                        <span class="offer-price"> $<?= number_format($offer['offer_price'], 2) ?> per person</span>
                                    </p>
                                </div>
                                <?php if ($days_left <= 7): ?>
                                <span class="badge bg-warning text-dark">
                                    <?= $days_left === 1 ? 'Last day!' : ($days_left . ' days left') ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted d-block">
                                Valid until <?= date('M j, Y', strtotime($offer['end_date'])) ?>
                            </small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <button class="btn btn-primary w-100 mb-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#packageInquiryModal"
                                data-package-id="<?= $packageDetails['id'] ?>"
                                data-package-title="<?= htmlspecialchars($packageDetails['title']) ?>">
                            Inquire About This Package
                        </button>
                        
                        <a href="https://wa.me/94782024424?text=I'm%20interested%20in%20package%20<?= urlencode($packageDetails['title']) ?>" 
                           class="btn btn-success w-100" target="_blank">
                            <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- All Packages Listing -->
        <h1 class="mb-4">Our Tour Packages</h1>
        
        <?php if (mysqli_num_rows($packagesResult) > 0): ?>
        <div class="row">
            <?php while ($package = mysqli_fetch_assoc($packagesResult)): 
                $hasOffer = !is_null($package['best_offer_price']);
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm position-relative">
                    <img src="uploads/packages/<?= htmlspecialchars($package['image']) ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($package['title']) ?>"
                         style="height: 200px; object-fit: cover;">
                    
                    <?php if ($hasOffer): ?>
                    <span class="badge bg-danger price-badge">
                        Special Offer
                    </span>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h3 class="card-title h5"><?= htmlspecialchars($package['title']) ?></h3>
                        <p class="card-text text-muted small">
                            <?= substr(htmlspecialchars($package['description']), 0, 100) ?>...
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="badge bg-primary"><?= htmlspecialchars($package['duration']) ?> days</span>
                            
                            <div class="text-end">
                                <?php if ($hasOffer): ?>
                                <div>
                                    <span class="original-price"><?= htmlspecialchars($package['price_range']) ?></span>
                                    <span class="offer-price">$<?= number_format($package['best_offer_price'], 2) ?></span>
                                </div>
                                <?php else: ?>
                                <span><?= htmlspecialchars($package['price_range']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="packages.php?id=<?= $package['id'] ?>" class="btn btn-sm btn-outline-primary w-100">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            No packages available at the moment. Please check back later.
        </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

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
    const countrySelect = document.getElementById('country');
    const countryCodePrefix = document.getElementById('country-code-prefix');
    const customCountryContainer = document.getElementById('customCountryContainer');
    const customCountryCode = document.getElementById('customCountryCode');
    const countryCodeInput = document.getElementById('countryCode');
    const form = document.querySelector('#packageInquiryModal form');
    
    // Update country code prefix and hidden field when country changes
    countrySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value === 'Other') {
            customCountryContainer.classList.remove('d-none');
            countryCodePrefix.textContent = '+';
            customCountryCode.focus();
            countryCodeInput.value = ''; // Clear until user inputs
        } else {
            customCountryContainer.classList.add('d-none');
            const code = selectedOption.getAttribute('data-code') || '';
            countryCodePrefix.textContent = '+' + code;
            countryCodeInput.value = code; // Set the predefined code
        }
    });
    
    // Update hidden field when custom country code changes
    customCountryCode.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
        countryCodePrefix.textContent = '+' + this.value;
        countryCodeInput.value = this.value;
    });
    
    // Initialize with default country if needed
    const sriLankaOption = countrySelect.querySelector('option[value="Sri Lanka"]');
    if (sriLankaOption) {
        const code = sriLankaOption.getAttribute('data-code');
        countryCodePrefix.textContent = '+' + code;
        countryCodeInput.value = code;
    }
    
    // Before form submission, validate country code
    form.addEventListener('submit', function(e) {
        if (!countryCodeInput.value) {
            e.preventDefault();
            alert('Please select or enter a valid country code');
            return;
        }
        
        // Combine country code and phone number for full_phone
        const phoneNumber = document.getElementById('phoneNumber').value;
        document.getElementById('fullPhoneNumber').value = '+' + countryCodeInput.value + phoneNumber;
    });

    // Initialize package inquiry modal with package data
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
});

// AJAX form submission
document.addEventListener('DOMContentLoaded', function() {
    const inquiryForm = document.querySelector('#packageInquiryModal form');
    
    if (inquiryForm) {
        inquiryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = inquiryForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            
            // Submit via AJAX
            fetch('process_inquiry.php', {
                method: 'POST',
                body: new FormData(inquiryForm)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('packageInquiryModal'));
                    modal.hide();
                    
                    // Redirect to thank-you page
                    window.location.href = 'thank-you.php?inquiry_id=' + data.inquiry_id;
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Inquiry';
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Inquiry';
            });
        });
    }
});
</script>
</body>
</html>