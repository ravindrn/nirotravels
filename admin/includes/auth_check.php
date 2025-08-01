<?php
session_start();

// Debugging: Uncomment to see session data
// echo "<pre>Session Data: "; print_r($_SESSION); echo "</pre>";

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}

// Check for inactivity (30 minutes timeout)
$inactive = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Set last login time if not set
if (!isset($_SESSION['last_login'])) {
    $_SESSION['last_login'] = date('Y-m-d H:i:s');
}

// Verify admin role if you have role-based access
if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] !== 'super_admin') {
    // Optional: Add role-based restrictions here
    // header("Location: unauthorized.php");
    // exit;
}
?>