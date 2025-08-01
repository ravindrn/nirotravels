<?php
include 'admin/includes/db.php';

$response = ['success' => false, 'message' => ''];
error_log("Received travel_dates: " . ($_POST['travel_dates'] ?? 'NULL'));
try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    // Required fields validation
    $requiredFields = ['name', 'email', 'phone', 'country', 'country_code', 'full_phone', 'destinations'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Please fill in all required fields.");
        }
    }

    // Process and validate dates
  $travel_start_date = !empty($_POST['travel_start_date']) 
    ? date('Y-m-d', strtotime($_POST['travel_start_date']))
    : null;

$travel_end_date = !empty($_POST['travel_end_date']) 
    ? date('Y-m-d', strtotime($_POST['travel_end_date']))
    : null;

// Validate date range
if ($travel_start_date && $travel_end_date && $travel_end_date < $travel_start_date) {
    throw new Exception("End date cannot be before start date");
}
    
    if (!empty($_POST['travel_dates'])) {
        $dates = explode(' to ', $_POST['travel_dates']);
        if (count($dates) >= 1) {
            $travel_start_date = date('Y-m-d', strtotime(trim($dates[0])));
            if (count($dates) >= 2) {
                $travel_end_date = date('Y-m-d', strtotime(trim($dates[1])));
                if ($travel_end_date < $travel_start_date) {
                    throw new Exception("End date cannot be before start date");
                }
            }
        }
    }

    // Prepare all data
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $country = mysqli_real_escape_string($conn, trim($_POST['country']));
    $country_code = mysqli_real_escape_string($conn, trim($_POST['country_code']));
    $full_phone = mysqli_real_escape_string($conn, trim($_POST['full_phone']));
    $travelers = !empty($_POST['travelers']) ? (int)$_POST['travelers'] : null;
    $budget_range = !empty($_POST['budget_range']) ? mysqli_real_escape_string($conn, trim($_POST['budget_range'])) : null;
    $special_requests = !empty($_POST['special_requests']) ? mysqli_real_escape_string($conn, trim($_POST['special_requests'])) : null;
    
    // Process JSON data
    $destinations = json_decode($_POST['destinations'], true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($destinations)) {
        throw new Exception("Invalid destinations data");
    }
    $destinations_json = json_encode($destinations);
    
    $activities_json = null;
    if (!empty($_POST['activities'])) {
        $activities = json_decode($_POST['activities'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($activities)) {
            $activities_json = json_encode($activities);
        }
    }
    
    $accommodation_json = null;
    if (!empty($_POST['accommodation'])) {
        $accommodation = json_decode($_POST['accommodation'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($accommodation)) {
            $accommodation_json = json_encode($accommodation);
        }
    }

    // Prepare and execute query
    $query = "INSERT INTO custom_trip_inquiries (
        user_name, email, phone, country, country_code, full_phone,
        travel_start_date, travel_end_date, travelers, budget_range,
        special_requests, destinations, activities, accommodation
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    // Bind parameters
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssssissss",
        $name, $email, $phone, $country, $country_code, $full_phone,
        $travel_start_date, $travel_end_date, $travelers, $budget_range,
        $special_requests, $destinations_json, $activities_json, $accommodation_json
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    // Success response
    $response = [
        'success' => true,
        'message' => "Thank you for your inquiry! We'll contact you shortly.",
        'inquiry_id' => mysqli_insert_id($conn)
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);

if (isset($conn)) {
    mysqli_close($conn);
}