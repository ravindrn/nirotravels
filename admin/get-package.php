<?php
require_once 'includes/db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "SELECT * FROM tour_packages WHERE id = $id";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $package = mysqli_fetch_assoc($result);
        echo json_encode([
            'success' => true,
            'id' => $package['id'],
            'title' => $package['title'],
            'description' => $package['description'],
            'itinerary' => $package['itinerary'],
            'duration' => $package['duration'],
            'price_range' => $package['price_range'],
            'difficulty' => $package['difficulty'],
            'image' => $package['image'], // Make sure this is included
            'hero_image' => $package['hero_image'], // And this too
            'is_active' => $package['is_active'],
            'sort_order' => $package['sort_order'],
            'inclusions' => $package['inclusions'],
            'exclusions' => $package['exclusions']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Package not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
}