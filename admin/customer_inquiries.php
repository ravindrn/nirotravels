<?php
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

// Handle status updates
if (isset($_POST['update_status'])) {
    $inquiry_id = $_POST['inquiry_id'];
    $new_status = $_POST['status'];
    
    $update_query = "UPDATE custom_trip_inquiries SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $inquiry_id);
    mysqli_stmt_execute($stmt);
    
    $_SESSION['message'] = "Status updated successfully!";
    $_SESSION['msg_type'] = "success";
    header("Location: customer_inquiries.php");
    exit();
}

// Handle inquiry deletion
if (isset($_GET['delete'])) {
    $inquiry_id = $_GET['delete'];
    
    $delete_query = "DELETE FROM custom_trip_inquiries WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $inquiry_id);
    mysqli_stmt_execute($stmt);
    
    $_SESSION['message'] = "Inquiry deleted successfully!";
    $_SESSION['msg_type'] = "success";
    header("Location: customer_inquiries.php");
    exit();
}

// Get all inquiries
$query = "SELECT * FROM custom_trip_inquiries ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Get admin info for sidebar
$admin_id = $_SESSION['admin_id'];
$admin_query = "SELECT * FROM admin_users WHERE id = $admin_id LIMIT 1";
$admin_result = mysqli_query($conn, $admin_query);
$admin = mysqli_fetch_assoc($admin_result);

// Function to format JSON data into readable format
function formatJsonData($json) {
    if (empty($json)) return 'Not specified';
    
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) return $json;
    
    $output = '';
    
    if (isset($data['id'])) {
        // Single item
        $output .= '<div class="json-item">';
        foreach ($data as $key => $value) {
            if ($key === 'id') continue;
            $output .= '<div><strong>' . ucfirst($key) . ':</strong> ' . htmlspecialchars($value) . '</div>';
        }
        $output .= '</div>';
    } else {
        // Array of items
        foreach ($data as $item) {
            $output .= '<div class="json-item mb-2 p-2 border rounded">';
            foreach ($item as $key => $value) {
                if ($key === 'id') continue;
                $output .= '<div><strong>' . ucfirst($key) . ':</strong> ' . htmlspecialchars($value) . '</div>';
            }
            $output .= '</div>';
        }
    }
    
    return $output;
}

// Function to format travel dates
function formatTravelDates($start, $end) {
    if (!$start && !$end) return 'Not specified';
    if ($start && !$end) return date('M j, Y', strtotime($start));
    return date('M j, Y', strtotime($start)) . ' to ' . date('M j, Y', strtotime($end));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Trip Inquiries - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        .inquiry-card.processing { border-left-color: #0dcaf0; }
        .inquiry-card.completed { border-left-color: #198754; }
        .inquiry-card.rejected { border-left-color: #dc3545; }
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
                            <a class="nav-link active" href="customer_inquiries.php">
                                <i class="bi bi-envelope me-2"></i> Trip Inquiries
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
                    <h1 class="h2">Customer Trip Inquiries</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <select class="form-select form-select-sm" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
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
                                        <?= $inquiry['status'] == 'processing' ? 'bg-info' : '' ?>
                                        <?= $inquiry['status'] == 'completed' ? 'bg-success' : '' ?>
                                        <?= $inquiry['status'] == 'rejected' ? 'bg-danger' : '' ?>">
                                        <?= ucfirst($inquiry['status']) ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <!-- Customer Info -->
                                    <div class="customer-info p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="mb-2"><i class="bi bi-person me-2"></i>Customer Details</h6>
                                                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($inquiry['user_name']) ?></p>
                                                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($inquiry['email']) ?></p>
                                                <p class="mb-1"><strong>Phone:</strong> 
                                                    <span class="phone-number">
                                                        <?php if (!empty($inquiry['country_code'])): ?>
                                                            +<?= htmlspecialchars($inquiry['country_code']) ?> 
                                                        <?php endif; ?>
                                                        <?= htmlspecialchars($inquiry['phone']) ?>
                                                    </span>
                                                </p>
                                                <?php if (!empty($inquiry['country'])): ?>
                                                    <p class="mb-0"><strong>Country:</strong> <?= htmlspecialchars($inquiry['country']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-2"><i class="bi bi-calendar me-2"></i>Trip Details</h6>
                                                <p class="mb-1"><strong>Travel Dates:</strong> 
                                                    <?= formatTravelDates($inquiry['travel_start_date'], $inquiry['travel_end_date']) ?>
                                                </p>
                                                <p class="mb-1"><strong>Travelers:</strong> <?= htmlspecialchars($inquiry['travelers']) ?></p>
                                                <?php if (!empty($inquiry['budget_range'])): ?>
                                                    <p class="mb-1"><strong>Budget:</strong> <?= htmlspecialchars($inquiry['budget_range']) ?></p>
                                                <?php endif; ?>
                                                <p class="mb-0"><strong>Submitted:</strong> <?= date('M j, Y g:i a', strtotime($inquiry['created_at'])) ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Trip Details Accordion -->
                                    <div class="accordion mb-3" id="accordion<?= $inquiry['id'] ?>">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $inquiry['id'] ?>">
                                                    <i class="bi bi-info-circle me-2"></i> View Trip Details
                                                </button>
                                            </h2>
                                            <div id="collapse<?= $inquiry['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordion<?= $inquiry['id'] ?>">
                                                <div class="accordion-body details-section p-3">
                                                    <div class="mb-3">
                                                        <h6><i class="bi bi-geo-alt me-2"></i>Destinations</h6>
                                                        <?= formatJsonData($inquiry['destinations']) ?>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6><i class="bi bi-activity me-2"></i>Activities</h6>
                                                        <?= formatJsonData($inquiry['activities']) ?>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6><i class="bi bi-house-door me-2"></i>Accommodation</h6>
                                                        <?= formatJsonData($inquiry['accommodation']) ?>
                                                    </div>
                                                    <?php if (!empty($inquiry['special_requests'])): ?>
                                                        <div>
                                                            <h6><i class="bi bi-chat-square-text me-2"></i>Special Requests</h6>
                                                            <div class="json-item"><?= htmlspecialchars($inquiry['special_requests']) ?></div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-flex flex-wrap justify-content-between action-buttons">
                                        <form method="POST" action="customer_inquiries.php" class="mb-2">
                                            <input type="hidden" name="inquiry_id" value="<?= $inquiry['id'] ?>">
                                            <div class="input-group">
                                                <select class="form-select form-select-sm" name="status">
                                                    <option value="pending" <?= $inquiry['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="processing" <?= $inquiry['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                                    <option value="completed" <?= $inquiry['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                    <option value="rejected" <?= $inquiry['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-check-circle"></i> Update
                                                </button>
                                            </div>
                                        </form>
                                        <div>
                                            <a href="mailto:<?= htmlspecialchars($inquiry['email']) ?>" class="btn btn-sm btn-success mb-2">
                                                <i class="bi bi-envelope"></i> Email
                                            </a>
                                            <a href="customer_inquiries.php?delete=<?= $inquiry['id'] ?>" class="btn btn-sm btn-danger mb-2" onclick="return confirm('Are you sure you want to delete this inquiry?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                            <?php if (!empty($inquiry['full_phone'])): ?>
                                                <a href="https://wa.me/<?= htmlspecialchars($inquiry['full_phone']) ?>" class="btn btn-sm btn-success mb-2" target="_blank">
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