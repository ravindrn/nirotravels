<?php
require_once 'admin/includes/db.php';
require('fpdf/fpdf.php'); // Make sure to include FPDF library

// Function to generate PDF
function generateInquiryPDF($inquiry) {
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // 1. Verify the logo file exists and is valid
    $logoPath = 'assets/img/logo/1.png';
    
    // Check if file exists and is PNG
    if (!file_exists($logoPath)) {
        // Use text header instead
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'NIRO SL TRAVELS',0,1,'L');
    } else {
        // Get image dimensions
        $imageInfo = getimagesize($logoPath);
        
        // Only add if it's a valid PNG
        if ($imageInfo && $imageInfo['mime'] === 'image/png') {
            try {
                $pdf->Image($logoPath, 10, 10, 30);
            } catch (Exception $e) {
                // Fallback to text if image fails
                $pdf->SetFont('Arial','B',16);
                $pdf->Cell(0,10,'NIRO SL TRAVELS',0,1,'L');
            }
        }
    }
    $pdf->Ln(20);
    
    // Title
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'INQUIRY CONFIRMATION',0,1,'C');
    $pdf->Ln(10);
    
    // Inquiry details
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(40,10,'Reference ID:',0,0);
    $pdf->Cell(0,10,$inquiry['id'],0,1);
    
    $pdf->Cell(40,10,'Package:',0,0);
    $pdf->Cell(0,10,$inquiry['title'],0,1);
    
    $pdf->Cell(40,10,'Name:',0,0);
    $pdf->Cell(0,10,$inquiry['name'],0,1);
    
    $pdf->Cell(40,10,'Email:',0,0);
    $pdf->Cell(0,10,$inquiry['email'],0,1);
    
    $pdf->Cell(40,10,'Phone:',0,0);
    $pdf->Cell(0,10,$inquiry['phone'],0,1);
    
    $pdf->Cell(40,10,'Travelers:',0,0);
    $pdf->Cell(0,10,$inquiry['travelers'],0,1);
    
    if (!empty($inquiry['travel_dates'])) {
        $pdf->Cell(40,10,'Travel Dates:',0,0);
        $pdf->Cell(0,10,$inquiry['travel_dates'],0,1);
    }
    
    if (!empty($inquiry['offer_price'])) {
        $pdf->Cell(40,10,'Offer Price:',0,0);
        $pdf->Cell(0,10,'$'.number_format($inquiry['offer_price'], 2),0,1);
    }
    
    $pdf->Ln(10);
    $pdf->SetFont('Arial','I',10);
    $pdf->MultiCell(0,10,'Thank you for your inquiry. We will contact you within 24 hours to confirm your booking. For any urgent matters, please contact us at +94 782 024 424 or info@nirosltravels.com');
    
    $filename = 'inquiry_'.$inquiry['id'].'.pdf';
    $pdf->Output('F', 'inquiries/'.$filename); // Save to server
    // $pdf->Output('D', $filename); // Force download
    
    return $filename;
}

// Get and validate inquiry_id
$inquiry_id = isset($_GET['inquiry_id']) ? intval($_GET['inquiry_id']) : 0;

if ($inquiry_id <= 0) {
    die("Invalid inquiry reference.");
}

// Fetch inquiry details
$query = "SELECT pi.*, tp.title 
          FROM package_inquiries pi
          JOIN tour_packages tp ON pi.package_id = tp.id
          WHERE pi.id = $inquiry_id";
$result = mysqli_query($conn, $query);

if ($result === false) {
    die("Database error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) === 0) {
    die("Inquiry not found.");
}

$inquiry = mysqli_fetch_assoc($result);

// Generate PDF
$pdf_file = generateInquiryPDF($inquiry);
$pdf_url = 'inquiries/'.$pdf_file;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Inquiry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .thank-you-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 50px;
        }
        .btn-download {
            background-color: #dc3545;
            color: white;
        }
        .btn-home {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'components/header.php' ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 thank-you-container text-center">
                <h1 class="mb-4">Thank You for Your Inquiry!</h1>
                <p class="lead">We've received your travel inquiry and our team will contact you shortly.</p>
                
                <div class="inquiry-details text-start mb-4 p-4 bg-light rounded">
                    <h3>Your Inquiry Details</h3>
                    <p><strong>Reference ID:</strong> <?= htmlspecialchars($inquiry['id']) ?></p>
                    <p><strong>Package:</strong> <?= htmlspecialchars($inquiry['title']) ?></p>
                    <p><strong>Name:</strong> <?= htmlspecialchars($inquiry['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($inquiry['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($inquiry['phone']) ?></p>
                    <p><strong>Travelers:</strong> <?= htmlspecialchars($inquiry['travelers']) ?></p>
                    <?php if (!empty($inquiry['travel_dates'])): ?>
                        <p><strong>Travel Dates:</strong> <?= htmlspecialchars($inquiry['travel_dates']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($inquiry['offer_price'])): ?>
                        <p><strong>Special Offer Price:</strong> $<?= number_format($inquiry['offer_price'], 2) ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-center gap-3">
                    <a href="<?= $pdf_url ?>" class="btn btn-download" download>
                        <i class="bi bi-download"></i> Download Confirmation
                    </a>
                    <a href="index.php" class="btn btn-home">
                        <i class="bi bi-house"></i> Back to Home
                    </a>
                </div>
                
                <div class="mt-4 text-muted">
                    <p>Need to make changes? Contact us immediately at <strong>+94 782 024 424</strong> or <strong>info@nirosltravels.com</strong></p>
                </div>
            </div>
        </div>
    </div>

     <?php include 'components/footer.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>



