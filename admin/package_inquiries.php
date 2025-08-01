<?php
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

// Country mapping with flags
$country_code_map = [
    'Sri Lanka' => 'lk',
    'India' => 'in',
    'United States' => 'us',
    'United Kingdom' => 'gb',
    'Australia' => 'au',
    'Germany' => 'de',
    'France' => 'fr',
    'Japan' => 'jp',
    'China' => 'cn',
    'Canada' => 'ca',
    'Brazil' => 'br',
    'Russia' => 'ru',
    'South Africa' => 'za',
    'United Arab Emirates' => 'ae',
    'Singapore' => 'sg',
    'Malaysia' => 'my',
    'Maldives' => 'mv',
    'Qatar' => 'qa',
    'Saudi Arabia' => 'sa',
    'Other' => 'globe'
];

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $inquiry_id = intval($_POST['inquiry_id']);
    $new_status = $_POST['status'];
    $admin_notes = mysqli_real_escape_string($conn, $_POST['admin_notes']);
    
    $query = "UPDATE package_inquiries SET 
              status = '$new_status',
              admin_notes = '$admin_notes'
              WHERE id = $inquiry_id";
    mysqli_query($conn, $query);
    
    $_SESSION['message'] = "Status updated successfully!";
    $_SESSION['msg_type'] = "success";
    header("Location: package_inquiries.php");
    exit();
}

// Get admin info for sidebar
$admin_id = $_SESSION['admin_id'];
$admin_query = "SELECT * FROM admin_users WHERE id = $admin_id LIMIT 1";
$admin_result = mysqli_query($conn, $admin_query);
$admin = mysqli_fetch_assoc($admin_result);

// Get all inquiries
$query = "SELECT pi.*, tp.title AS package_name
          FROM package_inquiries pi
          LEFT JOIN tour_packages tp ON pi.package_id = tp.id
          ORDER BY pi.inquiry_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Inquiries - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }
        .inquiry-card {
            border-left: 4px solid;
            transition: all 0.2s ease;
        }
        .inquiry-card.pending { border-left-color: #ffc107; }
        .inquiry-card.contacted { border-left-color: #0dcaf0; }
        .inquiry-card.confirmed { border-left-color: #198754; }
        .inquiry-card.cancelled { border-left-color: #dc3545; }
        .json-item {
            background-color: #f8f9fa;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 0.25rem;
        }
        .customer-info {
            background-color: #f1f8ff;
            border-radius: 0.5rem;
        }
        .details-section {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
        }
        .action-buttons .btn {
            min-width: 120px;
        }
        .accordion-button:not(.collapsed) {
            background-color: rgba(13, 110, 253, 0.1);
        }
        .phone-number {
            direction: ltr;
            display: inline-block;
        }
        .flag-icon { width: 1.2em; height: 0.9em; border-radius: 2px; }
        .globe-icon { color: #6c757d; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="bi bi-person-circle text-white" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-white mb-1"><?= htmlspecialchars($admin['full_name']) ?></h5>
                        <small class="text-muted"><?= htmlspecialchars($admin['email']) ?></small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin-dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage-packages.php">
                                <i class="bi bi-briefcase me-2"></i> Tour Packages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="package_inquiries.php">
                                <i class="bi bi-envelope me-2"></i> Package Inquiries
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Package Inquiries</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <select class="form-select form-select-sm" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="contacted">Contacted</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Flash Message -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <!-- Inquiries List -->
                <div class="row">
                    <?php while ($inquiry = mysqli_fetch_assoc($result)): ?>
                        <div class="col-lg-6 mb-4 inquiry-card <?= $inquiry['status'] ?>">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Inquiry #<?= $inquiry['id'] ?></h5>
                                    <span class="badge status-badge 
                                        <?= $inquiry['status'] == 'pending' ? 'bg-warning text-dark' : '' ?>
                                        <?= $inquiry['status'] == 'contacted' ? 'bg-info' : '' ?>
                                        <?= $inquiry['status'] == 'confirmed' ? 'bg-success' : '' ?>
                                        <?= $inquiry['status'] == 'cancelled' ? 'bg-danger' : '' ?>">
                                        <?= ucfirst($inquiry['status']) ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <!-- Customer Info -->
                                    <div class="customer-info p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-2"><i class="bi bi-person me-2"></i>Customer Details</h6>
                                                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($inquiry['name']) ?></p>
                                                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($inquiry['email']) ?></p>
                                                <p class="mb-1"><strong>Phone:</strong> 
                                                    <span class="phone-number">
                                                        <?php if ($inquiry['country'] === 'Other' && !empty($inquiry['full_phone'])): ?>
                                                            <?= htmlspecialchars($inquiry['full_phone']) ?>
                                                        <?php else: ?>
                                                            <?php if (!empty($inquiry['phone']) && !empty($inquiry['country_code'])): ?>
                                                                +<?= htmlspecialchars($inquiry['country_code']) ?> <?= htmlspecialchars($inquiry['phone']) ?>
                                                            <?php elseif (!empty($inquiry['phone'])): ?>
                                                                <?= htmlspecialchars($inquiry['phone']) ?>
                                                            <?php else: ?>
                                                                Not provided
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </span>
                                                </p>
                                                <p class="mb-0">
                                                    <strong>Country:</strong>
                                                    <?php if ($inquiry['country'] === 'Other'): ?>
                                                        <i class="fas fa-globe globe-icon me-1"></i>
                                                    <?php else: ?>
                                                        <span class="flag-icon flag-icon-<?= $country_code_map[$inquiry['country']] ?? 'globe' ?> me-1"></span>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($inquiry['country']) ?>
                                                    <?php if (!empty($inquiry['country_code'])): ?>
                                                        (+<?= htmlspecialchars($inquiry['country_code']) ?>)
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-2"><i class="bi bi-briefcase me-2"></i>Package Details</h6>
                                                <p class="mb-1"><strong>Package:</strong> <?= $inquiry['package_name'] ? htmlspecialchars($inquiry['package_name']) : 'Custom' ?></p>
                                                <p class="mb-1"><strong>Travelers:</strong> <?= $inquiry['travelers'] ?></p>
                                                <p class="mb-1"><strong>Travel Dates:</strong> <?= $inquiry['travel_dates'] ? htmlspecialchars($inquiry['travel_dates']) : 'Flexible' ?></p>
                                                <?php if ($inquiry['offer_price']): ?>
                                                    <p class="mb-1"><strong>Offer Price:</strong> $<?= number_format($inquiry['offer_price'], 2) ?></p>
                                                <?php endif; ?>
                                                <p class="mb-0"><strong>Submitted:</strong> <?= date('M j, Y g:i a', strtotime($inquiry['inquiry_date'])) ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Special Requests Accordion -->
                                    <?php if ($inquiry['special_requests']): ?>
                                    <div class="accordion mb-3" id="accordion<?= $inquiry['id'] ?>">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $inquiry['id'] ?>">
                                                    <i class="bi bi-chat-square-text me-2"></i> Special Requests
                                                </button>
                                            </h2>
                                            <div id="collapse<?= $inquiry['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordion<?= $inquiry['id'] ?>">
                                                <div class="accordion-body details-section p-3">
                                                    <?= nl2br(htmlspecialchars($inquiry['special_requests'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Actions -->
                                    <div class="d-flex flex-wrap justify-content-between action-buttons">
                                        <form method="POST" action="package_inquiries.php" class="mb-2">
                                            <input type="hidden" name="inquiry_id" value="<?= $inquiry['id'] ?>">
                                            <div class="input-group">
                                                <select class="form-select form-select-sm" name="status">
                                                    <option value="pending" <?= $inquiry['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="contacted" <?= $inquiry['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                                    <option value="confirmed" <?= $inquiry['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                    <option value="cancelled" <?= $inquiry['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-check-circle"></i> Update
                                                </button>
                                            </div>
                                            <div class="mt-2">
                                                <textarea class="form-control form-control-sm" name="admin_notes" placeholder="Admin notes" rows="2"><?= 
                                                    htmlspecialchars($inquiry['admin_notes'] ?? '') 
                                                ?></textarea>
                                            </div>
                                        </form>
                                        <div>
                                            <a href="mailto:<?= htmlspecialchars($inquiry['email']) ?>" class="btn btn-sm btn-success mb-2">
                                                <i class="bi bi-envelope"></i> Email
                                            </a>
                                            <?php if (!empty($inquiry['full_phone']) || (!empty($inquiry['phone']) && !empty($inquiry['country_code']))): ?>
                                                <a href="https://wa.me/<?= !empty($inquiry['full_phone']) ? htmlspecialchars($inquiry['full_phone']) : htmlspecialchars($inquiry['country_code'].$inquiry['phone']) ?>" 
                                                   class="btn btn-sm btn-success mb-2" target="_blank">
                                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter by status
        document.getElementById('statusFilter').addEventListener('change', function() {
            const status = this.value.toLowerCase();
            const cards = document.querySelectorAll('.inquiry-card');
            
            cards.forEach(card => {
                if (!status || card.classList.contains(status)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>