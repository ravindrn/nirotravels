<?php
require_once __DIR__ . '/admin/includes/db.php';
$test = mysqli_query($conn, "INSERT INTO customer_feedback 
                           (customer_name, feedback_text) 
                           VALUES ('Test', 'This is a test')");
echo $test ? "Success - ID: " . mysqli_insert_id($conn) : 
            "Failed: " . mysqli_error($conn);
?>