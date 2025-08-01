<?php
// process_quick_inquiry.php
require_once 'admin/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $packageId = mysqli_real_escape_string($conn, $_POST['package_id']);
    $travelDates = mysqli_real_escape_string($conn, $_POST['travel_dates']);
    
    // Determine package type
    $packageType = ($packageId === 'custom') ? 'custom' : 'predefined';
    
    // Save to database
    $query = "INSERT INTO inquiries (
        user_name, 
        email, 
        phone, 
        package_type, 
        package_id, 
        travel_dates,
        status,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, 'new', NOW())";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssss", 
        $name, $email, $phone, $packageType, $packageId, $travelDates);
    
    if (mysqli_stmt_execute($stmt)) {
        // Send email to user
        $to = $email;
        $subject = "Your Quick Booking Inquiry - Niro SL Travels";
        $message = "Dear $name,\n\nThank you for your quick booking inquiry with Niro SL Travels.\n\n";
        $message .= "Package Type: " . ucfirst($packageType) . "\n";
        $message .= "Travel Dates: $travelDates\n\n";
        $message .= "Our travel specialist will contact you within 24 hours via email or WhatsApp ($phone) to discuss your trip.\n\n";
        $message .= "Best regards,\nNiro SL Travels Team";
        $headers = "From: inquiries@nirosltravels.com";
        
        mail($to, $subject, $message, $headers);
        
        // Redirect to thank you page
        header("Location: thank-you.html");
        exit();
    } else {
        // Error handling
        header("Location: error.html?reason=db_error");
        exit();
    }
} else {
    header("Location: index.html");
    exit();
}
?>