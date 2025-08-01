<?php
require_once 'admin/includes/db.php';

// Initialize response array
$response = ['success' => false, 'message' => ''];

try {
    // Validate required fields
    $requiredFields = ['name', 'email', 'country', 'phone', 'travelers', 'package_id', 'country_code'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Please fill in all required fields.");
        }
    }

    // Sanitize input data
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $country = mysqli_real_escape_string($conn, trim($_POST['country']));
    $country_code = mysqli_real_escape_string($conn, trim($_POST['country_code']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $full_phone = mysqli_real_escape_string($conn, trim($_POST['full_phone']));
    $travel_dates = !empty($_POST['travel_dates']) ? mysqli_real_escape_string($conn, trim($_POST['travel_dates'])) : NULL;
    $travelers = intval($_POST['travelers']);
    $package_id = intval($_POST['package_id']);
    $special_requests = !empty($_POST['special_requests']) ? mysqli_real_escape_string($conn, trim($_POST['special_requests'])) : NULL;
    $package_type = mysqli_real_escape_string($conn, trim($_POST['package_type']));

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Please enter a valid email address.");
    }

    // Validate phone number
    if (!preg_match('/^[0-9]{8,15}$/', $phone)) {
        throw new Exception("Please enter a valid phone number (8-15 digits).");
    }

    // Validate country code
    if (!preg_match('/^[0-9]{1,4}$/', $country_code)) {
        throw new Exception("Please enter a valid country code (1-4 digits).");
    }

    // Check if package exists
    $packageCheckQuery = "SELECT id FROM tour_packages WHERE id = $package_id AND is_active = 1";
    $packageCheck = mysqli_query($conn, $packageCheckQuery);
    
    if (!$packageCheck) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($packageCheck) === 0) {
        throw new Exception("Invalid package selected.");
    }

    // Check for active special offers
    $currentDate = date('Y-m-d');
    $offerQuery = "SELECT offer_price FROM special_offers 
                  WHERE package_id = $package_id 
                  AND is_active = 1 
                  AND start_date <= '$currentDate' 
                  AND end_date >= '$currentDate' 
                  LIMIT 1";
    
    $offerResult = mysqli_query($conn, $offerQuery);
    $offer_price = NULL;
    
    if ($offerResult && mysqli_num_rows($offerResult) > 0) {
        $offerData = mysqli_fetch_assoc($offerResult);
        $offer_price = floatval($offerData['offer_price']);
    }

    // Build the INSERT query
    $query = "INSERT INTO package_inquiries (
                package_id,
                package_type,
                name,
                email,
                country,
                country_code,
                phone,
                full_phone,
                travelers,
                travel_dates,
                offer_price,
                special_requests,
                inquiry_date,
                status
              ) VALUES (
                $package_id,
                '$package_type',
                '$name',
                '$email',
                '$country',
                '$country_code',
                '$phone',
                '$full_phone',
                $travelers,
                " . ($travel_dates ? "'$travel_dates'" : "NULL") . ",
                " . ($offer_price ? $offer_price : "NULL") . ",
                " . ($special_requests ? "'$special_requests'" : "NULL") . ",
                NOW(),
                'pending'
              )";

    // Execute the query
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    // Success response
    $inquiry_id = mysqli_insert_id($conn);
    $response['success'] = true;
    $response['message'] = "Thank you for your inquiry! We'll contact you shortly.";
    $response['inquiry_id'] = $inquiry_id;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Close database connection
mysqli_close($conn);