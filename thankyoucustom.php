<?php
require_once 'admin/includes/db.php';
require('fpdf/fpdf.php');
session_start();

// Function to generate PDF for custom trip
function generateCustomTripPDF($inquiryData) {
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Logo handling
    $logoPath = 'assets/img/logo/1.png';
    if (file_exists($logoPath) && getimagesize($logoPath) && getimagesize($logoPath)['mime'] === 'image/png') {
        try {
            $pdf->Image($logoPath, 10, 10, 30);
        } catch (Exception $e) {
            $pdf->SetFont('Arial','B',16);
            $pdf->Cell(0,10,'NIRO SL TRAVELS',0,1,'L');
        }
    } else {
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'NIRO SL TRAVELS',0,1,'L');
    }
    $pdf->Ln(20);
    
    // Title
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'CUSTOM TRIP INQUIRY CONFIRMATION',0,1,'C');
    $pdf->Ln(10);
    
    // Inquiry details
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(40,10,'Date:',0,0);
    $pdf->Cell(0,10,date('Y-m-d H:i:s'),0,1);
    
    $pdf->Cell(40,10,'Name:',0,0);
    $pdf->Cell(0,10,$inquiryData['name'],0,1);
    
    $pdf->Cell(40,10,'Email:',0,0);
    $pdf->Cell(0,10,$inquiryData['email'],0,1);
    
    $pdf->Cell(40,10,'Phone:',0,0);
    $pdf->Cell(0,10,$inquiryData['full_phone'],0,1);
    
    $pdf->Cell(40,10,'Country:',0,0);
    $pdf->Cell(0,10,$inquiryData['country'],0,1);
    
    $pdf->Cell(40,10,'Travelers:',0,0);
    $pdf->Cell(0,10,$inquiryData['travelers'],0,1);
    
    $pdf->Cell(40,10,'Travel Dates:',0,0);
    $pdf->Cell(0,10,$inquiryData['travel_start_date'].' to '.$inquiryData['travel_end_date'],0,1);
    
    // Destinations
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,'Selected Destinations:',0,1);
    $pdf->SetFont('Arial','',10);
    
    $destinations = json_decode($inquiryData['destinations'], true);
    foreach ($destinations as $destination) {
        $pdf->Cell(5);
        $pdf->Cell(0,7,'- '.$destination['name'],0,1);
    }
    
    // Activities
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,'Selected Activities:',0,1);
    $pdf->SetFont('Arial','',10);
    
    $activities = json_decode($inquiryData['activities'], true);
    foreach ($activities as $activity) {
        $pdf->Cell(5);
        $pdf->Cell(0,7,'- '.$activity['name'].' ('.$activity['duration'].')',0,1);
    }
    
    // Accommodation
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,'Selected Accommodation:',0,1);
    $pdf->SetFont('Arial','',10);
    
    $accommodation = json_decode($inquiryData['accommodation'], true);
    if ($accommodation) {
        $pdf->Cell(5);
        $pdf->Cell(0,7,'- '.$accommodation['name'].' ('.$accommodation['type'].')',0,1);
    } else {
        $pdf->Cell(5);
        $pdf->Cell(0,7,'- Not specified',0,1);
    }
    
    // Special Requests
    if (!empty($inquiryData['special_requests'])) {
        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,10,'Special Requests:',0,1);
        $pdf->SetFont('Arial','',10);
        $pdf->MultiCell(0,7,$inquiryData['special_requests']);
    }
    
    $pdf->Ln(10);
    $pdf->SetFont('Arial','I',10);
    $pdf->MultiCell(0,7,'Thank you for your custom trip inquiry. Our travel experts will contact you within 24 hours.');
    
    $filename = 'custom_trip_'.date('Ymd_His').'.pdf';
    $filepath = 'inquiries/'.$filename;
    
    // Create inquiries directory if it doesn't exist
    if (!file_exists('inquiries')) {
        mkdir('inquiries', 0755, true);
    }
    
    $pdf->Output('F', $filepath);
    return $filepath;
}

// Try to get inquiry from database first
$inquiry = null;
$inquiry_id = isset($_GET['inquiry_id']) ? intval($_GET['inquiry_id']) : 0;

if ($inquiry_id > 0) {
    $query = "SELECT * FROM custom_trips WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $inquiry_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $inquiry = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

// If no database record found, check session
if (!$inquiry && isset($_SESSION['custom_trip_data'])) {
    $inquiry = $_SESSION['custom_trip_data'];
    $inquiry_id = 'temp_'.uniqid(); // Generate temporary ID for session-based inquiries
}

// If still no data, show error
if (!$inquiry) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Invalid inquiry reference. Please submit your trip inquiry first.</div>
         <a href='trip-builder.php' class='btn btn-primary'>Back to Trip Builder</a></div>");
}

// Generate PDF
$pdf_url = generateCustomTripPDF($inquiry);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Custom Trip Inquiry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <style>
        .thank-you-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .detail-card {
            border-left: 4px solid #0c2261;
            margin-bottom: 1.5rem;
        }
        .selected-item {
            background: #f8f9fa;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 5px;
        }
        .btn-download {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'components/header.php' ?>
    
    <div class="container my-5">
        <div class="thank-you-container">
            <div class="text-center mb-4">
                <div class="icon-circle bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-check2-all fs-3"></i>
                </div>
                <h1>Thank You!</h1>
                <p class="lead">Your custom trip inquiry has been received.</p>
                <?php if (strpos($inquiry_id, 'temp_') === 0): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Note: This is a temporary confirmation. Our team will contact you with a permanent reference number.
                    </div>
                <?php else: ?>
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle me-2"></i>
                        Reference Number: <strong><?= htmlspecialchars($inquiry_id) ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Inquiry Summary -->
            <div class="card detail-card mb-4">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-person text-primary me-2"></i> Your Details</h3>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?= htmlspecialchars($inquiry['name']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($inquiry['email']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Phone:</strong> <?= htmlspecialchars($inquiry['full_phone']) ?></p>
                            <p><strong>Country:</strong> <?= htmlspecialchars($inquiry['country']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Travel Dates:</strong> <?= date('M j, Y', strtotime($inquiry['travel_start_date'])) ?> to <?= date('M j, Y', strtotime($inquiry['travel_end_date'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Travelers:</strong> <?= htmlspecialchars($inquiry['travelers']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trip Details -->
            <div class="card detail-card mb-4">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-map text-primary me-2"></i> Your Trip Details</h3>
                    
                    <h4 class="mt-4"><i class="bi bi-geo-alt text-primary me-2"></i> Destinations</h4>
                    <?php $destinations = json_decode($inquiry['destinations'], true); ?>
                    <?php if ($destinations && count($destinations) > 0): ?>
                        <?php foreach ($destinations as $destination): ?>
                            <div class="selected-item">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <?= htmlspecialchars($destination['name']) ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No destinations selected</p>
                    <?php endif; ?>
                    
                    <h4 class="mt-4"><i class="bi bi-activity text-primary me-2"></i> Activities</h4>
                    <?php $activities = json_decode($inquiry['activities'], true); ?>
                    <?php if ($activities && count($activities) > 0): ?>
                        <?php foreach ($activities as $activity): ?>
                            <div class="selected-item">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong><?= htmlspecialchars($activity['name']) ?></strong>
                                <div class="text-muted small">
                                    Duration: <?= htmlspecialchars($activity['duration']) ?> | 
                                    Price: <?= htmlspecialchars($activity['price']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No activities selected</p>
                    <?php endif; ?>
                    
                    <h4 class="mt-4"><i class="bi bi-house text-primary me-2"></i> Accommodation</h4>
                    <?php $accommodation = json_decode($inquiry['accommodation'], true); ?>
                    <?php if ($accommodation): ?>
                        <div class="selected-item">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong><?= htmlspecialchars($accommodation['name']) ?></strong>
                            <div class="text-muted small">
                                Type: <?= htmlspecialchars($accommodation['type']) ?> | 
                                Price: <?= htmlspecialchars($accommodation['price']) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No accommodation selected</p>
                    <?php endif; ?>
                    
                    <?php if (!empty($inquiry['special_requests'])): ?>
                        <h4 class="mt-4"><i class="bi bi-chat-left-text text-primary me-2"></i> Special Requests</h4>
                        <p><?= nl2br(htmlspecialchars($inquiry['special_requests'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card border-primary mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Next Steps</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0 text-primary me-3">
                            <i class="bi bi-1-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h5>Initial Review</h5>
                            <p>We'll review your request within 24 hours.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0 text-primary me-3">
                            <i class="bi bi-2-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h5>Itinerary Creation</h5>
                            <p>Our experts will create a personalized itinerary.</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="flex-shrink-0 text-primary me-3">
                            <i class="bi bi-3-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h5>Final Confirmation</h5>
                            <p>We'll send you the final details for approval.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-grid gap-3">
                <a href="<?= $pdf_url ?>" class="btn btn-download" download>
                    <i class="bi bi-download me-2"></i> Download Confirmation
                </a>
                <a href="index.php" class="btn btn-primary">
                    <i class="bi bi-house me-2"></i> Back to Home
                </a>
            </div>

            <!-- Contact Info -->
            <div class="alert alert-info mt-4">
                <h5><i class="bi bi-info-circle-fill me-2"></i> Need Help?</h5>
                <p class="mb-1">Call: <a href="tel:+94782024424" class="alert-link">+94 782 024 424</a></p>
                <p class="mb-1">WhatsApp: <a href="https://wa.me/94782024424" class="alert-link">+94 782 024 424</a></p>
                <p class="mb-0">Email: <a href="mailto:info@nirosltravels.com" class="alert-link">info@nirosltravels.com</a></p>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php' ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>