<?php
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage-packages.php");
    exit();
}

$package_id = $_GET['id'];

// Optional: Delete associated image first
$package = mysqli_fetch_assoc(mysqli_query($conn, "SELECT featured_image FROM tour_packages WHERE id = $package_id"));
if ($package && $package['featured_image']) {
    $image_path = 'uploads/packages/' . $package['featured_image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// Delete package
mysqli_query($conn, "DELETE FROM tour_packages WHERE id = $package_id");

$_SESSION['message'] = "Package deleted successfully!";
header("Location: manage-packages.php");
exit();
?>