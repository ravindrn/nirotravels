<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the start of the script
error_log("Feedback submission started");

// Database connection
$db_path = __DIR__ . '/admin/includes/db.php';
if (!file_exists($db_path)) {
    error_log("Database connection file not found at: $db_path");
    die("Database configuration error");
}

require_once $db_path;

// Verify connection
if (!$conn) {
    $error = "Database connection failed: " . mysqli_connect_error();
    error_log($error);
    die($error);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST data received: " . print_r($_POST, true));
    
    // Validate required fields
    if (empty($_POST['customer_name']) || empty($_POST['feedback_text'])) {
        error_log("Validation failed - empty fields");
        die("empty");
    }

    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, trim($_POST['customer_name']));
    $feedback = mysqli_real_escape_string($conn, trim($_POST['feedback_text']));
    error_log("Sanitized data - Name: $name, Feedback: $feedback");

    // Insert into database
    $sql = "INSERT INTO customer_feedback (customer_name, feedback_text, submitted_at, is_read) 
            VALUES (?, ?, NOW(), 0)";
    
    error_log("Preparing SQL: $sql");
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        $error = "Prepare failed: " . mysqli_error($conn);
        error_log($error);
        die("error");
    }

    if (!mysqli_stmt_bind_param($stmt, "ss", $name, $feedback)) {
        $error = "Bind failed: " . mysqli_stmt_error($stmt);
        error_log($error);
        die("error");
    }

    if (!mysqli_stmt_execute($stmt)) {
        $error = "Execute failed: " . mysqli_stmt_error($stmt);
        error_log($error);
        
        // Additional table-specific error checking
        $table_exists = mysqli_query($conn, "SELECT 1 FROM customer_feedback LIMIT 1");
        if (!$table_exists) {
            error_log("Critical: customer_feedback table doesn't exist!");
        }
        
        die("error");
    }

    // Success
    $insert_id = mysqli_stmt_insert_id($stmt);
    error_log("Feedback submitted successfully! ID: $insert_id");
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    die("success");
}

error_log("Invalid request method");
die("invalid");
?>