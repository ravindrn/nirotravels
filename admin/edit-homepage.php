<?php
include 'includes/db.php';

// ADD NEW DESTINATION (for hero section - preserved as is)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_destination'])) {
    $title1 = mysqli_real_escape_string($conn, $_POST['title_line1']);
    $title2 = mysqli_real_escape_string($conn, $_POST['title_line2']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $sort = (int)$_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target = '../assets/img/destinations/' . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $sql = "INSERT INTO destinations (image, title_line1, title_line2, description, sort_order, is_active)
            VALUES ('$image', '$title1', '$title2', '$desc', $sort, $is_active)";
    mysqli_query($conn, $sql);
    echo "<div class='alert alert-success'>Destination added successfully.</div>";
}

// DELETE DESTINATION (preserved as is)
if (isset($_GET['delete_destination'])) {
    $id = (int)$_GET['delete_destination'];
    mysqli_query($conn, "DELETE FROM destinations WHERE id = $id");
    echo "<div class='alert alert-danger'>Destination deleted.</div>";
}

// UPDATE DESTINATION (preserved as is)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_destination'])) {
    $id = (int)$_POST['destination_id'];
    $title1 = mysqli_real_escape_string($conn, $_POST['title_line1']);
    $title2 = mysqli_real_escape_string($conn, $_POST['title_line2']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $sort = (int)$_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image = $_POST['existing_image'];
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target = '../assets/img/destinations/' . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $sql = "UPDATE destinations SET 
            image='$image', 
            title_line1='$title1', 
            title_line2='$title2', 
            description='$desc', 
            sort_order=$sort, 
            is_active=$is_active 
            WHERE id = $id";

    mysqli_query($conn, $sql);
    echo "<div class='alert alert-info'>Destination updated.</div>";
}

// Handle toggle popular status (updated with error handling)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_popular'])) {
    $id = (int)$_POST['id'];
    $is_popular = (int)$_POST['is_popular'];
    
    $query = "UPDATE destinations SET is_popular = $is_popular WHERE id = $id";
    if (!mysqli_query($conn, $query)) {
        die("Update failed: " . mysqli_error($conn));
    }
    $_SESSION['success_message'] = "Popular status updated successfully";
    header("Location: edit-homepage.php#popular-section");
    exit;
}

// Handle update popular title (updated with error handling)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_title'])) {
    $id = (int)$_POST['id'];
    $popular_title = mysqli_real_escape_string($conn, $_POST['popular_title']);
    
    $query = "UPDATE destinations SET popular_title = '$popular_title' WHERE id = $id";
    if (!mysqli_query($conn, $query)) {
        die("Update failed: " . mysqli_error($conn));
    }
    $_SESSION['success_message'] = "Popular title updated successfully";
    header("Location: edit-homepage.php#popular-section");
    exit;
}

// Get all destinations for popular section (updated with error handling)
$destinations = [];
$query = "SELECT id, title_line1 as name, popular_title, is_popular FROM destinations ORDER BY name ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($result)) {
    $destinations[] = $row;
}

// Get destinations for hero section listing (preserved as is)
$hero_destinations = mysqli_query($conn, "SELECT * FROM destinations ORDER BY sort_order ASC");

// ==================== SPECIAL OFFERS MANAGEMENT ====================

// ADD NEW SPECIAL OFFER
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_offer'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];

    $image = '';
    if (!empty($_FILES['offer_image']['name'])) {
        $image = basename($_FILES['offer_image']['name']);
        $target = '../assets/img/destinations/' . $image;
        move_uploaded_file($_FILES['offer_image']['tmp_name'], $target);
    }

    $sql = "INSERT INTO special_offers (title, description, price, image, is_available, sort_order)
            VALUES ('$title', '$description', '$price', '$image', $is_available, $sort_order)";
    mysqli_query($conn, $sql);
    $_SESSION['success_message'] = "Special offer added successfully";
    header("Location: edit-homepage.php#offers-section");
    exit;
}

// DELETE SPECIAL OFFER
if (isset($_GET['delete_offer'])) {
    $id = (int)$_GET['delete_offer'];
    mysqli_query($conn, "DELETE FROM special_offers WHERE id = $id");
    $_SESSION['success_message'] = "Special offer deleted successfully";
    header("Location: edit-homepage.php#offers-section");
    exit;
}

// UPDATE SPECIAL OFFER
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_offer'])) {
    $id = (int)$_POST['offer_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];

    $image = $_POST['existing_image'];
    if (!empty($_FILES['offer_image']['name'])) {
        $image = basename($_FILES['offer_image']['name']);
        $target = '../assets/img/destinations/' . $image;
        move_uploaded_file($_FILES['offer_image']['tmp_name'], $target);
    }

    $sql = "UPDATE special_offers SET 
            title='$title', 
            description='$description', 
            price='$price', 
            image='$image', 
            is_available=$is_available, 
            sort_order=$sort_order 
            WHERE id = $id";

    mysqli_query($conn, $sql);
    $_SESSION['success_message'] = "Special offer updated successfully";
    header("Location: edit-homepage.php#offers-section");
    exit;
}

// Get all special offers
$special_offers = mysqli_query($conn, "SELECT * FROM special_offers ORDER BY sort_order ASC");

// ==================== TESTIMONIALS MANAGEMENT ====================

// ADD NEW TESTIMONIAL
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_testimonial'])) {
    $name = mysqli_real_escape_string($conn, $_POST['reviewer_name']);
    $title = mysqli_real_escape_string($conn, $_POST['reviewer_title']);
    $text = mysqli_real_escape_string($conn, $_POST['review_text']);
    $sort_order = (int)$_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image = '';
    if (!empty($_FILES['testimonial_image']['name'])) {
        $image = basename($_FILES['testimonial_image']['name']);
        $target = '../assets/img/destinations/' . $image;
        move_uploaded_file($_FILES['testimonial_image']['tmp_name'], $target);
    }

    $sql = "INSERT INTO testimonials (reviewer_name, reviewer_title, review_text, image, sort_order, is_active)
            VALUES ('$name', '$title', '$text', '$image', $sort_order, $is_active)";
    mysqli_query($conn, $sql);
    $_SESSION['success_message'] = "Testimonial added successfully";
    header("Location: edit-homepage.php#testimonials-section");
    exit;
}

// DELETE TESTIMONIAL
if (isset($_GET['delete_testimonial'])) {
    $id = (int)$_GET['delete_testimonial'];
    mysqli_query($conn, "DELETE FROM testimonials WHERE id = $id");
    $_SESSION['success_message'] = "Testimonial deleted successfully";
    header("Location: edit-homepage.php#testimonials-section");
    exit;
}

// UPDATE TESTIMONIAL
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_testimonial'])) {
    $id = (int)$_POST['testimonial_id'];
    $name = mysqli_real_escape_string($conn, $_POST['reviewer_name']);
    $title = mysqli_real_escape_string($conn, $_POST['reviewer_title']);
    $text = mysqli_real_escape_string($conn, $_POST['review_text']);
    $sort_order = (int)$_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image = $_POST['existing_image'];
    if (!empty($_FILES['testimonial_image']['name'])) {
        $image = basename($_FILES['testimonial_image']['name']);
        $target = '../assets/img/destinations/' . $image;
        move_uploaded_file($_FILES['testimonial_image']['tmp_name'], $target);
    }

    $sql = "UPDATE testimonials SET 
            reviewer_name='$name', 
            reviewer_title='$title', 
            review_text='$text', 
            image='$image', 
            sort_order=$sort_order, 
            is_active=$is_active 
            WHERE id = $id";

    mysqli_query($conn, $sql);
    $_SESSION['success_message'] = "Testimonial updated successfully";
    header("Location: edit-homepage.php#testimonials-section");
    exit;
}

// Get all testimonials
$testimonials = mysqli_query($conn, "SELECT * FROM testimonials ORDER BY sort_order ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .popular-badge { background-color: #28a745; }
        .actions { white-space: nowrap; }
        textarea { width: 100%; min-height: 100px; }
        #popular-section { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container mt-4">

        <!-- Success message display -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success_message'] ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- ========================== ADD NEW DESTINATION (hero section - preserved) ========================== -->
        <h2>Add New Destination</h2>
        <form method="POST" enctype="multipart/form-data" class="mb-5">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title Line 1:</label>
                    <input type="text" name="title_line1" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Title Line 2:</label>
                    <input type="text" name="title_line2" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Description:</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Image (1920x1080 recommended):</label>
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sort Order:</label>
                    <input type="number" name="sort_order" class="form-control" value="0">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                        <label class="form-check-label" for="is_active">Show on site</label>
                    </div>
                </div>
                <div class="col-12">
                    <input type="hidden" name="add_destination" value="1">
                    <input type="submit" value="Add Destination" class="btn btn-primary">
                </div>
            </div>
        </form>

        <!-- ========================== DESTINATION LIST (hero section - preserved) ========================== -->
        <!-- ========================== DESTINATION LIST (hero section) ========================== -->
<h2>Current Destinations</h2>
<div class="table-responsive mb-5">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Image</th>
                <th>Title Line 1</th>
                <th>Title Line 2</th>
                <th>Description</th>
                <th>Sort</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($hero_destinations)): ?>
                <tr>
                    <td><img src="../assets/img/destinations/<?= htmlspecialchars($row['image']) ?>" width="100"></td>
                    <td><?= htmlspecialchars($row['title_line1']) ?></td>
                    <td><?= htmlspecialchars($row['title_line2']) ?></td>
                    <td><?= htmlspecialchars(substr($row['description'], 0, 50)) ?>...</td>
                    <td><?= $row['sort_order'] ?></td>
                    <td><?= $row['is_active'] ? 'Active' : 'Hidden' ?></td>
                    <td class="actions">
                        <div class="d-flex gap-2">  <!-- Added flex container with gap -->
                            <!-- Update Form -->
                            <form method="POST" enctype="multipart/form-data" class="flex-grow-1">
                                <input type="hidden" name="update_destination" value="1">
                                <input type="hidden" name="destination_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($row['image']) ?>">

                                <div class="row g-2">
                                    <div class="col-12">
                                        <input type="text" name="title_line1" value="<?= htmlspecialchars($row['title_line1']) ?>" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-12">
                                        <input type="text" name="title_line2" value="<?= htmlspecialchars($row['title_line2']) ?>" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-12">
                                        <textarea name="description" class="form-control form-control-sm" required><?= htmlspecialchars($row['description']) ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <input type="file" name="image" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="sort_order" value="<?= $row['sort_order'] ?>" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="active_<?= $row['id'] ?>" <?= $row['is_active'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="active_<?= $row['id'] ?>">Show</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <input type="submit" value="Update" class="btn btn-sm btn-warning w-100">
                                    </div>
                                </div>
                            </form>

                            <!-- Delete Form -->
                            <form method="GET" class="align-self-end">  <!-- Aligns to bottom of flex container -->
                                <input type="hidden" name="delete_destination" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger h-100" onclick="return confirm('Are you sure?');">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
        <!-- ========================== POPULAR DESTINATIONS SECTION ========================== -->
        <div id="popular-section">
            <h2>Manage Popular Destinations</h2>
            <p>Toggle which destinations appear in the "Popular Destinations" section and set their display titles.</p>
            
            <?php if (empty($destinations)): ?>
                <div class="alert alert-warning">No destinations found in the database.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Destination</th>
                                <th>Popular Title</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($destinations as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['name']) ?></td>
                                <td>
                                    <form method="POST" class="d-flex">
                                        <input type="text" name="popular_title" value="<?= htmlspecialchars($d['popular_title'] ?? $d['name']) ?>" 
                                            class="form-control form-control-sm me-2" required>
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <button type="submit" name="update_title" class="btn btn-sm btn-primary">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <?php if ($d['is_popular']): ?>
                                        <span class="badge popular-badge">Popular</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <input type="hidden" name="is_popular" value="<?= $d['is_popular'] ? 0 : 1 ?>">
                                        <button type="submit" name="toggle_popular" class="btn btn-sm <?= $d['is_popular'] ? 'btn-warning' : 'btn-success' ?>">
                                            <?= $d['is_popular'] ? 'Remove from Popular' : 'Add to Popular' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    <h4>Current Popular Destinations Order</h4>
                    <?php
                    $popularResult = mysqli_query($conn, "SELECT id, popular_title FROM destinations WHERE is_popular = 1 ORDER BY sort_order ASC");
                    if (mysqli_num_rows($popularResult) > 0): ?>
                        <ol class="list-group">
                            <?php while ($row = mysqli_fetch_assoc($popularResult)): ?>
                                <li class="list-group-item"><?= htmlspecialchars($row['popular_title']) ?></li>
                            <?php endwhile; ?>
                        </ol>
                    <?php else: ?>
                        <p class="text-muted">No destinations marked as popular yet.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!-- ==================== SPECIAL OFFERS SECTION ==================== -->
<div id="offers-section" class="mt-5 pt-4 border-top">
    <h2>Manage Special Offers</h2>
    
    <!-- Add New Offer Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Add New Special Offer</h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title:</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Price:</label>
                        <input type="text" name="price" class="form-control" placeholder="$99">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description:</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Image:</label>
                        <input type="file" name="offer_image" class="form-control" required>
                        <small class="text-muted">Recommended size: 800x600px</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort Order:</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_available" id="is_available" checked>
                            <label class="form-check-label" for="is_available">Available</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <input type="hidden" name="add_offer" value="1">
                        <button type="submit" class="btn btn-primary">Add Offer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Current Offers -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Current Special Offers</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Sort</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($offer = mysqli_fetch_assoc($special_offers)): ?>
                        <tr>
                            <td><img src="../assets/img/destinations/<?= htmlspecialchars($offer['image']) ?>" width="80"></td>
                            <td><?= htmlspecialchars($offer['title']) ?></td>
                            <td><?= htmlspecialchars(substr($offer['description'], 0, 50)) ?>...</td>
                            <td><?= htmlspecialchars($offer['price']) ?></td>
                            <td>
                                <span class="badge <?= $offer['is_available'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $offer['is_available'] ? 'Available' : 'Not Available' ?>
                                </span>
                            </td>
                            <td><?= $offer['sort_order'] ?></td>
                            <td class="actions">
                                <div class="d-flex gap-2">
                                    <!-- Update Form -->
                                    <form method="POST" enctype="multipart/form-data" class="flex-grow-1">
                                        <input type="hidden" name="update_offer" value="1">
                                        <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($offer['image']) ?>">
                                        
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <input type="text" name="title" value="<?= htmlspecialchars($offer['title']) ?>" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-12">
                                                <textarea name="description" class="form-control form-control-sm" rows="2" required><?= htmlspecialchars($offer['description']) ?></textarea>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="price" value="<?= htmlspecialchars($offer['price']) ?>" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-6">
                                                <input type="file" name="offer_image" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-4">
                                                <input type="number" name="sort_order" value="<?= $offer['sort_order'] ?>" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_available" id="available_<?= $offer['id'] ?>" <?= $offer['is_available'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="available_<?= $offer['id'] ?>">Available</label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <button type="submit" class="btn btn-sm btn-warning w-100">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <!-- Delete Button -->
                                    <form method="GET" class="align-self-end">
                                        <input type="hidden" name="delete_offer" value="<?= $offer['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger h-100" onclick="return confirm('Are you sure?');">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ==================== TESTIMONIALS SECTION ==================== -->
<div id="testimonials-section" class="mt-5 pt-4 border-top">
    <h2>Manage Testimonials</h2>
    
    <!-- Add New Testimonial Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Add New Testimonial</h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Reviewer Name:</label>
                        <input type="text" name="reviewer_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Reviewer Title:</label>
                        <input type="text" name="reviewer_title" class="form-control" value="Traveler">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Review Text:</label>
                        <textarea name="review_text" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Image:</label>
                        <input type="file" name="testimonial_image" class="form-control" required>
                        <small class="text-muted">Recommended size: 400x400px</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort Order:</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <input type="hidden" name="add_testimonial" value="1">
                        <button type="submit" class="btn btn-primary">Add Testimonial</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Current Testimonials -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Current Testimonials</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Title</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Sort</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($testimonial = mysqli_fetch_assoc($testimonials)): ?>
                        <tr>
                            <td><img src="../assets/img/destinations/<?= htmlspecialchars($testimonial['image']) ?>" width="80"></td>
                            <td><?= htmlspecialchars($testimonial['reviewer_name']) ?></td>
                            <td><?= htmlspecialchars($testimonial['reviewer_title']) ?></td>
                            <td><?= htmlspecialchars(substr($testimonial['review_text'], 0, 50)) ?>...</td>
                            <td>
                                <span class="badge <?= $testimonial['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $testimonial['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td><?= $testimonial['sort_order'] ?></td>
                            <td class="actions">
                                <div class="d-flex gap-2">
                                    <!-- Update Form -->
                                    <form method="POST" enctype="multipart/form-data" class="flex-grow-1">
                                        <input type="hidden" name="update_testimonial" value="1">
                                        <input type="hidden" name="testimonial_id" value="<?= $testimonial['id'] ?>">
                                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($testimonial['image']) ?>">
                                        
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <input type="text" name="reviewer_name" value="<?= htmlspecialchars($testimonial['reviewer_name']) ?>" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-12">
                                                <input type="text" name="reviewer_title" value="<?= htmlspecialchars($testimonial['reviewer_title']) ?>" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-12">
                                                <textarea name="review_text" class="form-control form-control-sm" rows="2" required><?= htmlspecialchars($testimonial['review_text']) ?></textarea>
                                            </div>
                                            <div class="col-6">
                                                <input type="file" name="testimonial_image" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-3">
                                                <input type="number" name="sort_order" value="<?= $testimonial['sort_order'] ?>" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="active_<?= $testimonial['id'] ?>" <?= $testimonial['is_active'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="active_<?= $testimonial['id'] ?>">Active</label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <button type="submit" class="btn btn-sm btn-warning w-100">Update</button>
                                            </div>
                                            <div class="col-6">
                                                <a href="?delete_testimonial=<?= $testimonial['id'] ?>" class="btn btn-sm btn-danger w-100" onclick="return confirm('Are you sure?');">
                                                    Delete
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>