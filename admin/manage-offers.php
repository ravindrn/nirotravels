<?php
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_offer'])) {
        $package_id = (int)$_POST['package_id'];
        $discount_type = mysqli_real_escape_string($conn, $_POST['discount_type']);
        $discount_value = (float)$_POST['discount_value'];
        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
        
        // Get package price
        $package_query = "SELECT price_range FROM tour_packages WHERE id = $package_id";
        $package_result = mysqli_query($conn, $package_query);
        $package = mysqli_fetch_assoc($package_result);
        $original_price = (float)str_replace(['$', ','], '', $package['price_range']);
        
        // Calculate offer price
        if ($discount_type === 'percentage') {
            $offer_price = $original_price * (1 - ($discount_value / 100));
        } else {
            $offer_price = $original_price - $discount_value;
        }
        
        $query = "INSERT INTO special_offers 
                 (package_id, discount_type, discount_value, original_price, offer_price, start_date, end_date) 
                 VALUES ($package_id, '$discount_type', $discount_value, $original_price, $offer_price, '$start_date', '$end_date')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Offer added successfully!";
        } else {
            $_SESSION['error'] = "Error adding offer: " . mysqli_error($conn);
        }
        
        header("Location: manage-offers.php");
        exit();
    }
    
    // Handle delete action
    if (isset($_POST['delete_offer'])) {
        $id = (int)$_POST['id'];
        $query = "DELETE FROM special_offers WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Offer deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting offer: " . mysqli_error($conn);
        }
        
        header("Location: manage-offers.php");
        exit();
    }
}

// Fetch all active offers
$offers_query = "SELECT o.*, p.title AS package_title, p.image AS package_image 
                FROM special_offers o
                JOIN tour_packages p ON o.package_id = p.id
                WHERE o.is_active = 1
                ORDER BY o.start_date DESC";
$offers_result = mysqli_query($conn, $offers_query);

// Fetch all packages for dropdown
$packages_query = "SELECT id, title, price_range FROM tour_packages WHERE is_active = 1 ORDER BY title";
$packages_result = mysqli_query($conn, $packages_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Special Offers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .offer-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .offer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        .offer-image {
            height: 180px;
            object-fit: cover;
        }
        .original-price {
            text-decoration: line-through;
            color: #6c757d;
        }
        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Manage Special Offers</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfferModal">
                <i class="bi bi-plus-circle"></i> Add New Offer
            </button>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php while ($offer = mysqli_fetch_assoc($offers_result)): 
                $discount_text = $offer['discount_type'] === 'percentage' 
                    ? $offer['discount_value'] . '% OFF' 
                    : '$' . $offer['discount_value'] . ' OFF';
                $is_active = strtotime($offer['start_date']) <= time() && strtotime($offer['end_date']) >= time();
            ?>
            <div class="col">
                <div class="card offer-card h-100">
                    <div class="position-relative">
                        <img src="../uploads/packages/<?= htmlspecialchars($offer['package_image']); ?>" 
                             class="card-img-top offer-image" 
                             alt="<?= htmlspecialchars($offer['package_title']); ?>">
                        <div class="discount-badge"><?= $discount_text; ?></div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($offer['package_title']); ?></h5>
                        <p class="card-text">
                            <span class="original-price">$<?= number_format($offer['original_price'], 2); ?></span>
                            <span class="fw-bold text-danger"> $<?= number_format($offer['offer_price'], 2); ?></span>
                        </p>
                        <p class="card-text">
                            <small class="text-muted">
                                Valid: <?= date('M j, Y', strtotime($offer['start_date'])); ?> - 
                                <?= date('M j, Y', strtotime($offer['end_date'])); ?>
                            </small>
                        </p>
                        <p class="card-text">
                            <span class="badge bg-<?= $is_active ? 'success' : 'secondary'; ?>">
                                <?= $is_active ? 'Active' : 'Inactive'; ?>
                            </span>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this offer?');">
                            <input type="hidden" name="id" value="<?= $offer['id']; ?>">
                            <button type="submit" name="delete_offer" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <!-- Add Offer Modal -->
    <div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOfferModalLabel">Add New Special Offer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="package_id" class="form-label">Package</label>
                            <select class="form-select" id="package_id" name="package_id" required>
                                <option value="">-- Select Package --</option>
                                <?php while ($package = mysqli_fetch_assoc($packages_result)): ?>
                                    <option value="<?= $package['id']; ?>">
                                        <?= htmlspecialchars($package['title']); ?> ($<?= htmlspecialchars($package['price_range']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discount Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="discount_type" id="percentage" value="percentage" checked>
                                <label class="form-check-label" for="percentage">Percentage</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="discount_type" id="fixed" value="fixed">
                                <label class="form-check-label" for="fixed">Fixed Amount</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="discount_value" class="form-label">Discount Value</label>
                            <input type="number" class="form-control" id="discount_value" name="discount_value" min="0" step="0.01" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_offer" class="btn btn-primary">Add Offer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set default dates
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').value = today;
            
            // Set end date to 7 days from today by default
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            const nextWeekFormatted = nextWeek.toISOString().split('T')[0];
            document.getElementById('end_date').value = nextWeekFormatted;
        });
    </script>
</body>
</html>