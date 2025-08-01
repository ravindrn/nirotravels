<?php
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_package'])) {
        // Add new package
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);
        $duration = mysqli_real_escape_string($conn, $_POST['duration']);
        $price_range = mysqli_real_escape_string($conn, $_POST['price_range']);
        $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);
        $inclusions = mysqli_real_escape_string($conn, $_POST['inclusions']);
        $exclusions = mysqli_real_escape_string($conn, $_POST['exclusions']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $sort_order = (int)$_POST['sort_order'];
        
        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/packages/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image = $file_name;
            }
        }
        
        $query = "INSERT INTO tour_packages 
                  (title, description, itinerary, duration, price_range, difficulty, image, is_active, sort_order, inclusions, exclusions, created_at) 
                  VALUES ('$title', '$description', '$itinerary', '$duration', '$price_range', '$difficulty', '$image', $is_active, $sort_order, '$inclusions', '$exclusions', NOW())";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Package added successfully!";
        } else {
            $_SESSION['error'] = "Error adding package: " . mysqli_error($conn);
        }
        
        header("Location: manage-packages.php");
        exit();
    }
    
    if (isset($_POST['edit_package'])) {
        // Edit existing package
        $id = (int)$_POST['id'];
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $itinerary = mysqli_real_escape_string($conn, $_POST['itinerary']);
        $duration = mysqli_real_escape_string($conn, $_POST['duration']);
        $price_range = mysqli_real_escape_string($conn, $_POST['price_range']);
        $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);
        $inclusions = mysqli_real_escape_string($conn, $_POST['inclusions']);
        $exclusions = mysqli_real_escape_string($conn, $_POST['exclusions']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $sort_order = (int)$_POST['sort_order'];
        
        // Handle image upload if new image is provided
        $image_update = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/packages/';
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_update = ", image = '$file_name'";
                
                // Delete old image if exists
                $old_image_query = mysqli_query($conn, "SELECT image FROM tour_packages WHERE id = $id");
                if ($old_image = mysqli_fetch_assoc($old_image_query)) {
                    if (!empty($old_image['image']) && file_exists($upload_dir . $old_image['image'])) {
                        unlink($upload_dir . $old_image['image']);
                    }
                }
            }
        }
        
        $query = "UPDATE tour_packages SET 
                  title = '$title',
                  description = '$description',
                  itinerary = '$itinerary',
                  duration = '$duration',
                  price_range = '$price_range',
                  difficulty = '$difficulty',
                  is_active = $is_active,
                  sort_order = $sort_order,
                  inclusions = '$inclusions',
                  exclusions = '$exclusions'
                  $image_update
                  WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Package updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating package: " . mysqli_error($conn);
        }
        
        header("Location: manage-packages.php");
        exit();
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // First get the image path to delete it
    $query = "SELECT image FROM tour_packages WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $package = mysqli_fetch_assoc($result);
    
    if ($package && !empty($package['image'])) {
        $image_path = 'uploads/packages/' . $package['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Now delete the package
    $query = "DELETE FROM tour_packages WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Package deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting package: " . mysqli_error($conn);
    }
    
    header("Location: manage-packages.php");
    exit();
}

// Fetch all packages
$packages = mysqli_query($conn, "SELECT * FROM tour_packages ORDER BY sort_order ASC, created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tour Packages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .package-image {
            max-width: 100px;
            max-height: 60px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Manage Tour Packages</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                <i class="bi bi-plus-circle"></i> Add New Package
            </button>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th>Difficulty</th>
                        <th>Status</th>
                        <th>Sort</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($package = mysqli_fetch_assoc($packages)): ?>
                    <tr>
                        <td><?= $package['id']; ?></td>
                        <td>
                            <?php if (!empty($package['image'])): ?>
                                <img src="uploads/packages/<?= htmlspecialchars($package['image']); ?>" alt="<?= htmlspecialchars($package['title']); ?>" class="package-image">
                            <?php else: ?>
                                <span class="text-muted">No image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($package['title']); ?></strong><br>
                            <small class="text-muted"><?= substr(htmlspecialchars($package['description']), 0, 50) . '...'; ?></small>
                        </td>
                        <td><?= htmlspecialchars($package['duration']); ?></td>
                        <td><?= htmlspecialchars($package['price_range']); ?></td>
                        <td><?= htmlspecialchars($package['difficulty']); ?></td>
                        <td>
                            <span class="badge bg-<?= $package['is_active'] ? 'success' : 'secondary'; ?>">
                                <?= $package['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td><?= $package['sort_order']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-package" data-id="<?= $package['id']; ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="manage-packages.php?delete=<?= $package['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this package?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Add Package Modal -->
    <div class="modal fade" id="addPackageModal" tabindex="-1" aria-labelledby="addPackageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPackageModalLabel">Add New Tour Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">Package Title*</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description*</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="itinerary" class="form-label">Itinerary*</label>
                                <textarea class="form-control" id="itinerary" name="itinerary" rows="5" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="duration" class="form-label">Duration*</label>
                                <input type="text" class="form-control" id="duration" name="duration" required>
                            </div>
                            <div class="col-md-6">
                                <label for="price_range" class="form-label">Price Range*</label>
                                <input type="text" class="form-control" id="price_range" name="price_range" required>
                            </div>
                            <div class="col-md-6">
                                <label for="difficulty" class="form-label">Difficulty Level*</label>
                                <select class="form-select" id="difficulty" name="difficulty" required>
                                    <option value="Easy">Easy</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Challenging">Challenging</option>
                                    <option value="Difficult">Difficult</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label">Featured Image</label>
                                <input type="file" class="form-control" id="image" name="image">
                            </div>
                            <div class="col-md-6">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4 pt-3">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        Active Package
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="inclusions" class="form-label">Inclusions (One per line)*</label>
                                <textarea class="form-control" id="inclusions" name="inclusions" rows="4" required></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="exclusions" class="form-label">Exclusions (One per line)</label>
                                <textarea class="form-control" id="exclusions" name="exclusions" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_package" class="btn btn-primary">Save Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Package Modal -->
    <div class="modal fade" id="editPackageModal" tabindex="-1" aria-labelledby="editPackageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPackageModalLabel">Edit Tour Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="edit_package" value="1">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="edit_title" class="form-label">Package Title*</label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                            <div class="col-md-12">
                                <label for="edit_description" class="form-label">Description*</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="edit_itinerary" class="form-label">Itinerary*</label>
                                <textarea class="form-control" id="edit_itinerary" name="itinerary" rows="5" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_duration" class="form-label">Duration*</label>
                                <input type="text" class="form-control" id="edit_duration" name="duration" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_price_range" class="form-label">Price Range*</label>
                                <input type="text" class="form-control" id="edit_price_range" name="price_range" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_difficulty" class="form-label">Difficulty Level*</label>
                                <select class="form-select" id="edit_difficulty" name="difficulty" required>
                                    <option value="Easy">Easy</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Challenging">Challenging</option>
                                    <option value="Difficult">Difficult</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_image" class="form-label">Featured Image</label>
                                <input type="file" class="form-control" id="edit_image" name="image">
                                <div class="mt-2" id="current_image_container">
                                    <small>Current Image:</small>
                                    <img id="current_image" src="" class="package-image d-block mt-1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="edit_sort_order" name="sort_order" value="0">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4 pt-3">
                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                    <label class="form-check-label" for="edit_is_active">
                                        Active Package
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="edit_inclusions" class="form-label">Inclusions (One per line)*</label>
                                <textarea class="form-control" id="edit_inclusions" name="inclusions" rows="4" required></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="edit_exclusions" class="form-label">Exclusions (One per line)</label>
                                <textarea class="form-control" id="edit_exclusions" name="exclusions" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle edit button clicks
            $('.edit-package').click(function() {
                var packageId = $(this).data('id');
                
                // Fetch package data via AJAX
                $.ajax({
                    url: 'includes/get-package.php',
                    type: 'GET',
                    data: { id: packageId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Populate the edit form with package data
                            $('#edit_id').val(response.id);
                            $('#edit_title').val(response.title);
                            $('#edit_description').val(response.description);
                            $('#edit_itinerary').val(response.itinerary);
                            $('#edit_duration').val(response.duration);
                            $('#edit_price_range').val(response.price_range);
                            $('#edit_difficulty').val(response.difficulty);
                            $('#edit_sort_order').val(response.sort_order);
                            $('#edit_inclusions').val(response.inclusions);
                            $('#edit_exclusions').val(response.exclusions);
                            $('#edit_is_active').prop('checked', response.is_active == 1);
                            
                            // Handle image display
                            if (response.image) {
                                $('#current_image').attr('src', 'uploads/packages/' + response.image);
                                $('#current_image_container').show();
                            } else {
                                $('#current_image_container').hide();
                            }
                            
                            // Show the modal
                            var editModal = new bootstrap.Modal(document.getElementById('editPackageModal'));
                            editModal.show();
                        } else {
                            alert('Error loading package data');
                        }
                    },
                    error: function() {
                        alert('Error loading package data');
                    }
                });
            });
        });
    </script>
</body>
</html>