<?php
require_once 'includes/db.php';
require_once 'includes/auth_check.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_accommodation'])) {
        // Add new accommodation
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $price_range = mysqli_real_escape_string($conn, $_POST['price_range']);
        $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $website = mysqli_real_escape_string($conn, $_POST['website']);
        $destination_id = intval($_POST['destination_id']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Handle image upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/accommodations/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('acc_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_path = $destination;
            }
        }

        $query = "INSERT INTO accommodations 
                  (name, type, price_range, image_path, contact_number, email, website, destination_id, is_active)
                  VALUES ('$name', '$type', '$price_range', '$image_path', '$contact_number', '$email', '$website', $destination_id, $is_active)";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Accommodation added successfully!";
            header("Location: manage-accommodations.php");
            exit();
        } else {
            $error = "Error adding accommodation: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['update_accommodation'])) {
        // Update existing accommodation
        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $price_range = mysqli_real_escape_string($conn, $_POST['price_range']);
        $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $website = mysqli_real_escape_string($conn, $_POST['website']);
        $destination_id = intval($_POST['destination_id']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Get current image path
        $current_image = mysqli_real_escape_string($conn, $_POST['current_image']);

        // Handle image upload/removal
        $image_path = $current_image;
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            if (!empty($current_image) && file_exists($current_image)) {
                unlink($current_image);
            }
            $image_path = null;
        } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image if exists
            if (!empty($current_image) && file_exists($current_image)) {
                unlink($current_image);
            }
            
            $uploadDir = '../uploads/accommodations/';
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('acc_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_path = $destination;
            }
        }

        $query = "UPDATE accommodations SET
                  name = '$name',
                  type = '$type',
                  price_range = '$price_range',
                  image_path = '$image_path',
                  contact_number = '$contact_number',
                  email = '$email',
                  website = '$website',
                  destination_id = $destination_id,
                  is_active = $is_active
                  WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Accommodation updated successfully!";
            header("Location: manage-accommodations.php");
            exit();
        } else {
            $error = "Error updating accommodation: " . mysqli_error($conn);
        }
    }
} elseif (isset($_GET['delete'])) {
    // Delete accommodation
    $id = intval($_GET['delete']);
    
    // Get image path to delete the file
    $result = mysqli_query($conn, "SELECT image_path FROM accommodations WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
    if ($row && !empty($row['image_path']) && file_exists($row['image_path'])) {
        unlink($row['image_path']);
    }
    
    $query = "DELETE FROM accommodations WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Accommodation deleted successfully!";
        header("Location: manage-accommodations.php");
        exit();
    } else {
        $error = "Error deleting accommodation: " . mysqli_error($conn);
    }
}

// Fetch all accommodations
$accommodations = [];
$query = "SELECT a.*, d.popular_title AS destination_name 
          FROM accommodations a
          LEFT JOIN destinations d ON a.destination_id = d.id
          ORDER BY d.popular_title, a.name";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $accommodations[] = $row;
    }
}

// Fetch destinations for dropdown
$destinations = [];
$query = "SELECT id, popular_title FROM destinations ORDER BY popular_title";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $destinations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accommodations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .accommodation-image {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
        }
        .form-container {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
  

    <div class="container py-4">
        <h1 class="mb-4">Manage Accommodations</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="form-container mb-4">
            <h2><?php echo isset($_GET['edit']) ? 'Edit Accommodation' : 'Add New Accommodation'; ?></h2>
            
            <form method="POST" enctype="multipart/form-data">
                <?php
                $editing = false;
                $current_accommodation = null;
                if (isset($_GET['edit'])) {
                    $edit_id = intval($_GET['edit']);
                    foreach ($accommodations as $acc) {
                        if ($acc['id'] == $edit_id) {
                            $current_accommodation = $acc;
                            $editing = true;
                            break;
                        }
                    }
                }
                ?>
                
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo $current_accommodation['id']; ?>">
                    <input type="hidden" name="current_image" value="<?php echo $current_accommodation['image_path']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?php echo $editing ? htmlspecialchars($current_accommodation['name']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" class="form-control" id="type" name="type" required
                                   value="<?php echo $editing ? htmlspecialchars($current_accommodation['type']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="price_range" class="form-label">Price Range</label>
                            <input type="text" class="form-control" id="price_range" name="price_range" required
                                   value="<?php echo $editing ? htmlspecialchars($current_accommodation['price_range']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="destination_id" class="form-label">Destination</label>
                            <select class="form-select" id="destination_id" name="destination_id" required>
                                <option value="">Select Destination</option>
                                <?php foreach ($destinations as $dest): ?>
                                    <option value="<?php echo $dest['id']; ?>"
                                        <?php if ($editing && $current_accommodation['destination_id'] == $dest['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($dest['popular_title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number"
                                   value="<?php echo $editing ? htmlspecialchars($current_accommodation['contact_number']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo $editing ? htmlspecialchars($current_accommodation['email']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website"
                                   value="<?php echo $editing ? htmlspecialchars($current_accommodation['website']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                <?php if ($editing && $current_accommodation['is_active']) echo 'checked'; ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Accommodation Image</label>
                    <input type="file" class="form-control" id="image" name="image">
                    
                    <?php if ($editing && !empty($current_accommodation['image_path'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo htmlspecialchars($current_accommodation['image_path']); ?>" 
                                 class="accommodation-image mb-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remove_image" name="remove_image">
                                <label class="form-check-label" for="remove_image">Remove current image</label>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-between">
                    <?php if ($editing): ?>
                        <button type="submit" name="update_accommodation" class="btn btn-primary">Update Accommodation</button>
                        <a href="manage-accommodations.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_accommodation" class="btn btn-success">Add Accommodation</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Accommodations List -->
        <h2>All Accommodations</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Price Range</th>
                        <th>Destination</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($accommodations)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No accommodations found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($accommodations as $acc): ?>
                            <tr>
                                <td><?php echo $acc['id']; ?></td>
                                <td>
                                    <?php if (!empty($acc['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($acc['image_path']); ?>" 
                                             class="accommodation-image">
                                    <?php else: ?>
                                        <i class="bi bi-image"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($acc['name']); ?></td>
                                <td><?php echo htmlspecialchars($acc['type']); ?></td>
                                <td><?php echo htmlspecialchars($acc['price_range']); ?></td>
                                <td><?php echo htmlspecialchars($acc['destination_name']); ?></td>
                                <td>
                                    <?php if (!empty($acc['contact_number'])): ?>
                                        <div><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($acc['contact_number']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($acc['email'])): ?>
                                        <div><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($acc['email']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $acc['is_active'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $acc['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="manage-accommodations.php?edit=<?php echo $acc['id']; ?>" 
                                       class="btn btn-sm btn-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="manage-accommodations.php?delete=<?php echo $acc['id']; ?>" 
                                       class="btn btn-sm btn-danger" title="Delete"
                                       onclick="return confirm('Are you sure you want to delete this accommodation?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>