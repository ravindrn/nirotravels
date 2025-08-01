<?php
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_image'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $date_taken = mysqli_real_escape_string($conn, $_POST['date_taken']);
        $tour_id = isset($_POST['tour_id']) ? (int)$_POST['tour_id'] : null;
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/gallery/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $target_path = $upload_dir . $file_name;
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $query = "INSERT INTO gallery 
                              (title, description, image_path, location, date_taken, tour_id, featured) 
                              VALUES ('$title', '$description', '$file_name', '$location', '$date_taken', $tour_id, $featured)";
                    
                    if (mysqli_query($conn, $query)) {
                        $message = "Image added successfully!";
                    } else {
                        $error = "Error adding image: " . mysqli_error($conn);
                    }
                } else {
                    $error = "Error uploading file.";
                }
            } else {
                $error = "Only JPG, PNG, GIF, and WEBP files are allowed.";
            }
        } else {
            $error = "Please select an image file.";
        }
    }
    
    // Handle delete action
    if (isset($_POST['delete_image'])) {
        $id = (int)$_POST['id'];
        
        // First get the image path to delete it
        $query = "SELECT image_path FROM gallery WHERE id = $id";
        $result = mysqli_query($conn, $query);
        $image = mysqli_fetch_assoc($result);
        
        if ($image && !empty($image['image_path'])) {
            $image_path = '../uploads/gallery/' . $image['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Now delete the gallery entry
        $query = "DELETE FROM gallery WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            $message = "Image deleted successfully!";
        } else {
            $error = "Error deleting image: " . mysqli_error($conn);
        }
    }
    
    // Handle featured status toggle
    if (isset($_POST['toggle_featured'])) {
        $id = (int)$_POST['id'];
        $featured = (int)$_POST['featured'];
        
        $query = "UPDATE gallery SET featured = $featured WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            $message = "Featured status updated!";
        } else {
            $error = "Error updating featured status: " . mysqli_error($conn);
        }
    }
}

// Fetch all gallery images
$gallery_query = "SELECT g.*, t.title AS tour_title 
                  FROM gallery g 
                  LEFT JOIN tour_packages t ON g.tour_id = t.id 
                  ORDER BY date_taken DESC";
$gallery_result = mysqli_query($conn, $gallery_query);

// Fetch all tours for dropdown
$tours_query = "SELECT id, title FROM tour_packages ORDER BY title";
$tours_result = mysqli_query($conn, $tours_query);
$tours = [];
while ($tour = mysqli_fetch_assoc($tours_result)) {
    $tours[] = $tour;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .gallery-image {
            max-width: 150px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
        .featured-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #ffc107;
            color: #000;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .image-container {
            position: relative;
        }
    </style>
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Manage Gallery</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addImageModal">
                <i class="bi bi-plus-circle"></i> Add New Image
            </button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Date Taken</th>
                        <th>Tour</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($image = mysqli_fetch_assoc($gallery_result)): ?>
                    <tr>
                        <td>
                            <div class="image-container">
                                <img src="../uploads/gallery/<?= htmlspecialchars($image['image_path']); ?>" 
                                     alt="<?= htmlspecialchars($image['title']); ?>" 
                                     class="gallery-image">
                                <?php if ($image['featured']): ?>
                                    <span class="featured-badge">Featured</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($image['title']); ?></td>
                        <td><?= htmlspecialchars($image['location']); ?></td>
                        <td><?= date('M j, Y', strtotime($image['date_taken'])); ?></td>
                        <td>
                            <?php if ($image['tour_id']): ?>
                                <span class="badge bg-info"><?= htmlspecialchars($image['tour_title']); ?></span>
                            <?php else: ?>
                                <span class="text-muted">None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= $image['id']; ?>">
                                <input type="hidden" name="featured" value="<?= $image['featured'] ? 0 : 1; ?>">
                                <button type="submit" name="toggle_featured" class="btn btn-sm <?= $image['featured'] ? 'btn-warning' : 'btn-outline-secondary'; ?>">
                                    <i class="bi <?= $image['featured'] ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                <input type="hidden" name="id" value="<?= $image['id']; ?>">
                                <button type="submit" name="delete_image" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Add Image Modal -->
    <div class="modal fade" id="addImageModal" tabindex="-1" aria-labelledby="addImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addImageModalLabel">Add New Image to Gallery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">Image Title*</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location">
                            </div>
                            <div class="col-md-6">
                                <label for="date_taken" class="form-label">Date Taken*</label>
                                <input type="date" class="form-control" id="date_taken" name="date_taken" required>
                            </div>
                            <div class="col-md-6">
                                <label for="tour_id" class="form-label">Associated Tour</label>
                                <select class="form-select" id="tour_id" name="tour_id">
                                    <option value="">-- Select Tour --</option>
                                    <?php foreach ($tours as $tour): ?>
                                        <option value="<?= $tour['id']; ?>"><?= htmlspecialchars($tour['title']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4 pt-3">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured">
                                    <label class="form-check-label" for="featured">
                                        Featured Image
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="image" class="form-label">Image File*</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                <div class="form-text">Allowed formats: JPG, PNG, GIF, WEBP. Max size: 5MB</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_image" class="btn btn-primary">Add Image</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set today's date as default for date taken
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date_taken').value = today;
        });
    </script>
</body>
</html>