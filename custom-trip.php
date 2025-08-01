<?php
include 'admin/includes/db.php';

// Fetch all active destinations
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all active destinations with your actual column names
$destinationsQuery = "SELECT id, title_line1, title_line2, description, image, is_popular, popular_title 
                     FROM destinations 
                     WHERE is_active = 1 
                     ORDER BY sort_order ASC, title_line1 ASC";
$destinationsResult = mysqli_query($conn, $destinationsQuery);

if (!$destinationsResult) {
    die("Destinations query failed: " . mysqli_error($conn));
}

// Fetch all active activities (assuming this table exists with your structure)
$activitiesQuery = "SELECT a.*, d.title_line1 AS destination_name 
                   FROM activities a 
                   LEFT JOIN destinations d ON a.destination_id = d.id 
                   WHERE a.is_active = 1 
                   ORDER BY a.name ASC";
$activitiesResult = mysqli_query($conn, $activitiesQuery);

if (!$activitiesResult) {
    die("Activities query failed: " . mysqli_error($conn));
}

// Fetch all active accommodations (assuming this table exists with your structure)
$accommodationsQuery = "SELECT a.*, d.title_line1 AS destination_name 
                       FROM accommodations a 
                       LEFT JOIN destinations d ON a.destination_id = d.id 
                       WHERE a.is_active = 1 
                       ORDER BY a.name ASC";
$accommodationsResult = mysqli_query($conn, $accommodationsQuery);

if (!$accommodationsResult) {
    die("Accommodations query failed: " . mysqli_error($conn));
}
                                     
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Build Your Custom Trip | Niro SL Travels</title>
    <?php include 'components/header.php' ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <style>
        .trip-builder-section {
            padding: 60px 0;
        }
        .builder-card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        .builder-card-header {
            background-color: #0c2261;
            color: white;
            padding: 15px 20px;
        }
        .selected-items-container {
            min-height: 150px;
            border: 2px dashed #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            background-color: #f8fafc;
        }
        .selected-item {
            background-color: white;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        .selected-item:hover {
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .remove-item {
            background: none;
            border: none;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }
        .remove-item:hover {
            background-color: #f8d7da;
            color: #dc3545;
        }
        .trip-summary {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            position: sticky;
            top: 20px;
        }
        .bi {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <section class="trip-builder-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="mb-4">Build Your Custom Sri Lanka Trip</h1>
                    <p class="lead mb-5">Select destinations, activities, and accommodations to create your perfect itinerary.</p>
                    
                    <!-- Destinations Selection -->
                    <div class="builder-card">
                        <div class="builder-card-header">
                            <h3 class="mb-0">1. Choose Destinations</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php 
                                if (mysqli_num_rows($destinationsResult) > 0) {
                                    while($destination = mysqli_fetch_assoc($destinationsResult)): 
                                        $destinationName = !empty($destination['popular_title']) ? $destination['popular_title'] : $destination['title_line1'];
                                ?>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input destination-checkbox" type="checkbox" 
                                               id="dest-<?= $destination['id'] ?>" 
                                               value="<?= $destination['id'] ?>"
                                               data-name="<?= htmlspecialchars($destinationName) ?>">
                                        <label class="form-check-label" for="dest-<?= $destination['id'] ?>">
                                            <?= htmlspecialchars($destinationName) ?>
                                        </label>
                                    </div>
                                </div>
                                <?php 
                                    endwhile;
                                    mysqli_data_seek($destinationsResult, 0);
                                } else {
                                    echo '<div class="col-12"><p>No destinations available</p></div>';
                                }
                                ?>
                            </div>
                            <div class="selected-items-container" id="selected-destinations">
                                <div class="text-center py-4">
                                    <i class="bi bi-pin-map fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">No destinations selected yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activities Selection -->
                    <div class="builder-card">
                        <div class="builder-card-header">
                            <h3 class="mb-0">2. Choose Activities</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Filter by Destination:</label>
                                <select class="form-select" id="activity-destination-filter">
                                    <option value="">All Destinations</option>
                                    <?php 
                                    if (mysqli_num_rows($destinationsResult) > 0) {
                                        mysqli_data_seek($destinationsResult, 0);
                                        while($destination = mysqli_fetch_assoc($destinationsResult)): 
                                            $destinationDisplayName = !empty($destination['popular_title']) ? 
                                                                  $destination['popular_title'] : 
                                                                  'Destination ' . $destination['id'];
                                    ?>
                                    <option value="<?= $destination['id'] ?>">
                                        <?= htmlspecialchars($destinationDisplayName) ?>
                                    </option>
                                    <?php 
                                        endwhile;
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="row" id="activities-container">
                                <?php 
                                if (mysqli_num_rows($activitiesResult) > 0) {
                                    while($activity = mysqli_fetch_assoc($activitiesResult)): 
                                        $destinationDisplayName = '';
                                        if ($activity['destination_id']) {
                                            $destQuery = "SELECT popular_title FROM destinations WHERE id = " . $activity['destination_id'];
                                            $destResult = mysqli_query($conn, $destQuery);
                                            if ($destRow = mysqli_fetch_assoc($destResult)) {
                                                $destinationDisplayName = !empty($destRow['popular_title']) ? 
                                                                       $destRow['popular_title'] : 
                                                                       'Destination ' . $activity['destination_id'];
                                            }
                                        }
                                ?>
                                <div class="col-md-6 mb-3 activity-item" data-destination="<?= $activity['destination_id'] ?>">
                                    <div class="form-check">
                                        <input class="form-check-input activity-checkbox" type="checkbox" 
                                               id="activity-<?= $activity['id'] ?>" 
                                               value="<?= $activity['id'] ?>"
                                               data-name="<?= htmlspecialchars($activity['name']) ?>"
                                               data-duration="<?= htmlspecialchars($activity['duration'] ?? 'N/A') ?>"
                                               data-price="<?= htmlspecialchars($activity['price_range'] ?? 'N/A') ?>">
                                        <label class="form-check-label" for="activity-<?= $activity['id'] ?>">
                                            <strong><?= htmlspecialchars($activity['name']) ?></strong>
                                            <?php if(!empty($destinationDisplayName)): ?>
                                            <br><small>at <?= htmlspecialchars($destinationDisplayName) ?></small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Duration: <?= htmlspecialchars($activity['duration'] ?? 'N/A') ?> | 
                                        Price: <?= htmlspecialchars($activity['price_range'] ?? 'N/A') ?>
                                    </small>
                                </div>
                                <?php 
                                    endwhile;
                                } else {
                                    echo '<div class="col-12"><p>No activities available</p></div>';
                                }
                                ?>
                            </div>
                            <div class="selected-items-container" id="selected-activities">
                                <div class="text-center py-4">
                                    <i class="bi bi-activity fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">No activities selected yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Accommodations Selection -->
                    <div class="builder-card">
                        <div class="builder-card-header">
                            <h3 class="mb-0">3. Choose Accommodations</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Filter by Destination:</label>
                                <select class="form-select" id="accommodation-destination-filter">
                                    <option value="">All Destinations</option>
                                    <?php 
                                    if (mysqli_num_rows($destinationsResult) > 0) {
                                        mysqli_data_seek($destinationsResult, 0);
                                        while($destination = mysqli_fetch_assoc($destinationsResult)): 
                                            $destinationDisplayName = !empty($destination['popular_title']) ? 
                                                                  $destination['popular_title'] : 
                                                                  'Destination ' . $destination['id'];
                                    ?>
                                    <option value="<?= $destination['id'] ?>">
                                        <?= htmlspecialchars($destinationDisplayName) ?>
                                    </option>
                                    <?php 
                                        endwhile;
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="row" id="accommodations-container">
                                <?php 
                                if (mysqli_num_rows($accommodationsResult) > 0) {
                                    while($accommodation = mysqli_fetch_assoc($accommodationsResult)): 
                                        $destinationDisplayName = '';
                                        if ($accommodation['destination_id']) {
                                            $destQuery = "SELECT popular_title FROM destinations WHERE id = " . $accommodation['destination_id'];
                                            $destResult = mysqli_query($conn, $destQuery);
                                            if ($destRow = mysqli_fetch_assoc($destResult)) {
                                                $destinationDisplayName = !empty($destRow['popular_title']) ? 
                                                                       $destRow['popular_title'] : 
                                                                       'Destination ' . $accommodation['destination_id'];
                                            }
                                        }
                                ?>
                                <div class="col-md-6 mb-3 accommodation-item" data-destination="<?= $accommodation['destination_id'] ?>">
                                    <div class="form-check">
                                        <input class="form-check-input accommodation-checkbox" type="radio" 
                                               name="accommodation" 
                                               id="accom-<?= $accommodation['id'] ?>" 
                                               value="<?= $accommodation['id'] ?>"
                                               data-name="<?= htmlspecialchars($accommodation['name']) ?>"
                                               data-type="<?= htmlspecialchars($accommodation['type'] ?? 'N/A') ?>"
                                               data-price="<?= htmlspecialchars($accommodation['price_range'] ?? 'N/A') ?>">
                                        <label class="form-check-label" for="accom-<?= $accommodation['id'] ?>">
                                            <strong><?= htmlspecialchars($accommodation['name']) ?></strong>
                                            <?php if(!empty($destinationDisplayName)): ?>
                                            <br><small>at <?= htmlspecialchars($destinationDisplayName) ?></small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Type: <?= htmlspecialchars($accommodation['type'] ?? 'N/A') ?> | 
                                        Price: <?= htmlspecialchars($accommodation['price_range'] ?? 'N/A') ?>
                                    </small>
                                </div>
                                <?php 
                                    endwhile;
                                } else {
                                    echo '<div class="col-12"><p>No accommodations available</p></div>';
                                }
                                ?>
                            </div>
                            <div class="selected-items-container" id="selected-accommodations">
                                <div class="text-center py-4">
                                    <i class="bi bi-house fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">No accommodation selected yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Trip Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="trip-summary">
                        <h3 class="mb-4">Your Trip Summary</h3>
                        
                        <div class="mb-4">
                            <h5>Destinations</h5>
                            <ul id="summary-destinations" class="list-unstyled">
                                <li class="text-muted">None selected</li>
                            </ul>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Activities</h5>
                            <ul id="summary-activities" class="list-unstyled">
                                <li class="text-muted">None selected</li>
                            </ul>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Accommodation</h5>
                            <ul id="summary-accommodation" class="list-unstyled">
                                <li class="text-muted">None selected</li>
                            </ul>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-lg" id="submit-trip-btn" disabled>
                                Submit Trip Inquiry
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Inquiry Modal -->
<div class="modal fade" id="inquiryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Custom Trip Inquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tripInquiryForm" action="process_custom_trip.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="destinations" id="form-destinations">
                    <input type="hidden" name="activities" id="form-activities">
                    <input type="hidden" name="accommodation" id="form-accommodation">
                    <input type="hidden" name="full_phone" id="fullPhoneNumber">
                    <input type="hidden" name="country_code" id="countryCode">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name*</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email*</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country*</label>
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
                                <label class="form-label">Custom Country Code*</label>
                                <div class="input-group">
                                    <span class="input-group-text">+</span>
                                    <input type="text" class="form-control" id="customCountryCode" 
                                           name="custom_country_code" placeholder="Country code" pattern="[0-9]{1,4}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone/WhatsApp*</label>
                            <div class="input-group">
                                <span class="input-group-text" id="country-code-prefix">+</span>
                                <input type="tel" class="form-control" id="phoneNumber" 
                                       name="phone" placeholder="Phone number without country code" 
                                       pattern="[0-9]{8,15}" required>
                            </div>
                            <small class="text-muted">Enter your phone number without country code</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Travel Start Date*</label>
                            <input type="date" class="form-control" name="travel_start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Travel End Date*</label>
                            <input type="date" class="form-control" name="travel_end_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Number of Travelers</label>
                            <input type="number" class="form-control" name="travelers" min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Special Requests</label>
                            <textarea class="form-control" name="special_requests" rows="3"></textarea>
                        </div>
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

    <?php include 'components/footer.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Track selected items
        const selectedDestinations = [];
        const selectedActivities = [];
        let selectedAccommodation = null;
        
        // Get DOM elements
        const activityFilter = document.getElementById('activity-destination-filter');
        const accommodationFilter = document.getElementById('accommodation-destination-filter');
        
        // Function to update destination filters based on selected destinations
        function updateDestinationFilters() {
            // Clear existing options (keep the first "All Destinations" option)
            while (activityFilter.options.length > 1) activityFilter.remove(1);
            while (accommodationFilter.options.length > 1) accommodationFilter.remove(1);
            
            // Add only selected destinations to the filters
            selectedDestinations.forEach(destination => {
                const option = new Option(destination.name, destination.id);
                activityFilter.add(option);
                
                const option2 = new Option(destination.name, destination.id);
                accommodationFilter.add(option2);
            });
            
            // Automatically filter activities and accommodations to show only those from selected destinations
            filterActivitiesBySelectedDestinations();
            filterAccommodationsBySelectedDestinations();
        }
        
        // Filter activities to show only those from selected destinations
        function filterActivitiesBySelectedDestinations() {
            const activityItems = document.querySelectorAll('.activity-item');
            
            activityItems.forEach(item => {
                const destinationId = item.dataset.destination;
                
                if (selectedDestinations.length === 0) {
                    // If no destinations selected, hide all activities
                    item.style.display = 'none';
                } else if (!destinationId || selectedDestinations.some(d => d.id === destinationId)) {
                    // Show if no destination specified or matches a selected destination
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        // Filter accommodations to show only those from selected destinations
        function filterAccommodationsBySelectedDestinations() {
            const accommodationItems = document.querySelectorAll('.accommodation-item');
            
            accommodationItems.forEach(item => {
                const destinationId = item.dataset.destination;
                
                if (selectedDestinations.length === 0) {
                    // If no destinations selected, hide all accommodations
                    item.style.display = 'none';
                } else if (!destinationId || selectedDestinations.some(d => d.id === destinationId)) {
                    // Show if no destination specified or matches a selected destination
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        // Destination selection
        document.querySelectorAll('.destination-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const destinationId = this.value;
                const destinationName = this.dataset.name;
                
                if (this.checked) {
                    selectedDestinations.push({
                        id: destinationId,
                        name: destinationName
                    });
                } else {
                    const index = selectedDestinations.findIndex(d => d.id === destinationId);
                    if (index !== -1) {
                        selectedDestinations.splice(index, 1);
                    }
                }
                
                updateSelectedDestinations();
                updateDestinationFilters();
                updateSubmitButton();
            });
        });
        
        // Filter activities by destination dropdown
        activityFilter.addEventListener('change', function() {
            const destinationId = this.value;
            const activityItems = document.querySelectorAll('.activity-item');
            
            activityItems.forEach(item => {
                if (!destinationId || item.dataset.destination === destinationId) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Filter accommodations by destination dropdown
        accommodationFilter.addEventListener('change', function() {
            const destinationId = this.value;
            const accommodationItems = document.querySelectorAll('.accommodation-item');
            
            accommodationItems.forEach(item => {
                if (!destinationId || item.dataset.destination === destinationId) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Activity selection
        document.querySelectorAll('.activity-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const activityId = this.value;
                const activityName = this.dataset.name;
                const duration = this.dataset.duration;
                const price = this.dataset.price;
                
                if (this.checked) {
                    selectedActivities.push({
                        id: activityId,
                        name: activityName,
                        duration: duration,
                        price: price
                    });
                } else {
                    const index = selectedActivities.findIndex(a => a.id === activityId);
                    if (index !== -1) {
                        selectedActivities.splice(index, 1);
                    }
                }
                updateSelectedActivities();
                updateSubmitButton();
            });
        });
        
        // Accommodation selection
        document.querySelectorAll('.accommodation-checkbox').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    selectedAccommodation = {
                        id: this.value,
                        name: this.dataset.name,
                        type: this.dataset.type,
                        price: this.dataset.price
                    };
                    updateSelectedAccommodation();
                    updateSubmitButton();
                }
            });
        });
        
        // Update selected destinations display
        function updateSelectedDestinations() {
            const container = document.getElementById('selected-destinations');
            const summaryContainer = document.getElementById('summary-destinations');
            
            if (selectedDestinations.length === 0) {
                container.innerHTML = '<div class="text-center py-4"><i class="bi bi-pin-map fs-1 text-muted"></i><p class="text-muted mt-2">No destinations selected yet</p></div>';
                summaryContainer.innerHTML = '<li class="text-muted">None selected</li>';
                return;
            }
            
            container.innerHTML = '<h6 class="mb-3">Selected Destinations</h6>';
            summaryContainer.innerHTML = '';
            
            selectedDestinations.forEach(destination => {
                // Selected items panel
                const itemDiv = document.createElement('div');
                itemDiv.className = 'selected-item d-flex align-items-center';
                itemDiv.innerHTML = `
                    <i class="bi bi-geo-alt me-2"></i>
                    <span class="flex-grow-1">${destination.name}</span>
                    <button class="btn btn-sm btn-outline-danger remove-item" data-id="${destination.id}" data-type="destination">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                container.appendChild(itemDiv);
                
                // Summary panel
                const summaryItem = document.createElement('li');
                summaryItem.className = 'd-flex align-items-center mb-2';
                summaryItem.innerHTML = `
                    <i class="bi bi-geo-alt text-primary me-2"></i>
                    <span>${destination.name}</span>
                `;
                summaryContainer.appendChild(summaryItem);
            });
            
            // Add remove event listeners
            document.querySelectorAll('.remove-item[data-type="destination"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const destinationId = this.dataset.id;
                    const checkbox = document.querySelector(`.destination-checkbox[value="${destinationId}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });
            });
        }
        
        // Update selected activities display
        function updateSelectedActivities() {
            const container = document.getElementById('selected-activities');
            const summaryContainer = document.getElementById('summary-activities');
            
            if (selectedActivities.length === 0) {
                container.innerHTML = '<div class="text-center py-4"><i class="bi bi-activity fs-1 text-muted"></i><p class="text-muted mt-2">No activities selected yet</p></div>';
                summaryContainer.innerHTML = '<li class="text-muted">None selected</li>';
                return;
            }
            
            container.innerHTML = '<h6 class="mb-3">Selected Activities</h6>';
            summaryContainer.innerHTML = '';
            
            selectedActivities.forEach(activity => {
                // Selected items panel
                const itemDiv = document.createElement('div');
                itemDiv.className = 'selected-item d-flex align-items-center';
                itemDiv.innerHTML = `
                    <i class="bi bi-activity me-2"></i>
                    <span class="flex-grow-1">
                        <strong>${activity.name}</strong>
                        <small class="d-block text-muted">${activity.duration} • ${activity.price}</small>
                    </span>
                    <button class="btn btn-sm btn-outline-danger remove-item" data-id="${activity.id}" data-type="activity">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                container.appendChild(itemDiv);
                
                // Summary panel
                const summaryItem = document.createElement('li');
                summaryItem.className = 'd-flex mb-3';
                summaryItem.innerHTML = `
                    <i class="bi bi-activity text-primary me-2 mt-1"></i>
                    <div>
                        <strong>${activity.name}</strong>
                        <div class="text-muted small">${activity.duration}</div>
                    </div>
                `;
                summaryContainer.appendChild(summaryItem);
            });
            
            // Add remove event listeners
            document.querySelectorAll('.remove-item[data-type="activity"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const activityId = this.dataset.id;
                    const checkbox = document.querySelector(`.activity-checkbox[value="${activityId}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });
            });
        }
        
        // Update selected accommodation display
        function updateSelectedAccommodation() {
            const container = document.getElementById('selected-accommodations');
            const summaryContainer = document.getElementById('summary-accommodation');
            
            if (!selectedAccommodation) {
                container.innerHTML = '<div class="text-center py-4"><i class="bi bi-house fs-1 text-muted"></i><p class="text-muted mt-2">No accommodation selected yet</p></div>';
                summaryContainer.innerHTML = '<li class="text-muted">None selected</li>';
                return;
            }
            
            container.innerHTML = `
                <h6 class="mb-3">Selected Accommodation</h6>
                <div class="selected-item d-flex align-items-center">
                    <i class="bi bi-house me-2"></i>
                    <span class="flex-grow-1">
                        <strong>${selectedAccommodation.name}</strong>
                        <small class="d-block text-muted">${selectedAccommodation.type} • ${selectedAccommodation.price}</small>
                    </span>
                    <button class="btn btn-sm btn-outline-danger remove-item" data-type="accommodation">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
            
            summaryContainer.innerHTML = `
                <li class="d-flex mb-2">
                    <i class="bi bi-house text-primary me-2 mt-1"></i>
                    <div>
                        <strong>${selectedAccommodation.name}</strong>
                    </div>
                </li>
                <li class="text-muted small">Type: ${selectedAccommodation.type}</li>
                <li class="text-muted small">Price: ${selectedAccommodation.price}</li>
            `;
            
            // Add remove event listener
            document.querySelector('.remove-item[data-type="accommodation"]').addEventListener('click', function() {
                const radio = document.querySelector(`.accommodation-checkbox[value="${selectedAccommodation.id}"]`);
                if (radio) {
                    radio.checked = false;
                    selectedAccommodation = null;
                    updateSelectedAccommodation();
                    updateSubmitButton();
                }
            });
        }
        
        // Enable/disable submit button based on selections
        function updateSubmitButton() {
            const submitBtn = document.getElementById('submit-trip-btn');
            submitBtn.disabled = selectedDestinations.length === 0;
        }
        
        // Submit trip inquiry
        document.getElementById('submit-trip-btn').addEventListener('click', function() {
            // Set hidden form values
            document.getElementById('form-destinations').value = JSON.stringify(selectedDestinations);
            document.getElementById('form-activities').value = JSON.stringify(selectedActivities);
            document.getElementById('form-accommodation').value = JSON.stringify(selectedAccommodation);
            
            // Show modal
            const inquiryModal = new bootstrap.Modal(document.getElementById('inquiryModal'));
            inquiryModal.show();
        });
    });
    

    const countrySelect = document.getElementById('country');
const countryCodePrefix = document.getElementById('country-code-prefix');
const customCountryContainer = document.getElementById('customCountryContainer');
const customCountryCode = document.getElementById('customCountryCode');
const countryCodeInput = document.getElementById('countryCode');
const phoneNumberInput = document.getElementById('phoneNumber');
const fullPhoneNumberInput = document.getElementById('fullPhoneNumber');

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
    
    updateFullPhoneNumber();
});

customCountryCode.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '');
    countryCodePrefix.textContent = '+' + this.value;
    countryCodeInput.value = this.value;
    updateFullPhoneNumber();
});

phoneNumberInput.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '');
    updateFullPhoneNumber();
});

function updateFullPhoneNumber() {
    const countryCode = countryCodeInput.value;
    const phoneNumber = phoneNumberInput.value;
    
    if (countryCode && phoneNumber) {
        fullPhoneNumberInput.value = '+' + countryCode + phoneNumber;
    } else {
        fullPhoneNumberInput.value = '';
    }
}

// Initialize with default country if needed
const sriLankaOption = countrySelect.querySelector('option[value="Sri Lanka"]');
if (sriLankaOption) {
    const code = sriLankaOption.getAttribute('data-code');
    countryCodePrefix.textContent = '+' + code;
    countryCodeInput.value = code;
}

// Form validation
document.getElementById('tripInquiryForm').addEventListener('submit', function(e) {
    
    const startDate = new Date(this.elements['travel_start_date'].value);
    const endDate = new Date(this.elements['travel_end_date'].value);
    
    if (startDate > endDate) {
        e.preventDefault();
        alert('End date must be after start date');
        return;
    }

    if (!countryCodeInput.value) {
        e.preventDefault();
        alert('Please select or enter a valid country code');
        return;
    }
    
    // Validate phone number
    if (!phoneNumberInput.value || !phoneNumberInput.validity.valid) {
        e.preventDefault();
        alert('Please enter a valid phone number (8-15 digits)');
        phoneNumberInput.focus();
        return;
    }
    
    // If "Other" is selected but no custom country code provided
    if (countrySelect.value === 'Other' && !customCountryCode.value) {
        e.preventDefault();
        alert('Please enter a custom country code');
        customCountryCode.focus();
        return;
    }
});
    </script>
</body>
</html>