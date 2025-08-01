<?php
require_once 'includes/db.php';
require_once 'includes/auth_check.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_activity'])) {
        // Add new activity
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $duration = mysqli_real_escape_string($conn, $_POST['duration']);
        $price_range = mysqli_real_escape_string($conn, $_POST['price_range']);
        $destination_id = intval($_POST['destination_id']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Handle image upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/activities/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('act_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_path = $destination;
            }
        }

        $query = "INSERT INTO activities 
                  (name, description, duration, price_range, destination_id, is_active, image)
                  VALUES ('$name', '$description', '$duration', '$price_range', $destination_id, $is_active, '$image_path')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Activity added successfully!";
            header("Location: manage-activities.php");
            exit();
        } else {
            $error = "Error adding activity: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['update_activity'])) {
        // Update existing activity
        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $duration = mysqli_real_escape_string($conn, $_POST['duration']);
        $price_range = mysqli_real_escape_string($conn, $_POST['price_range']);
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
            
            $uploadDir = '../uploads/activities/';
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('act_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_path = $destination;
            }
        }

        $query = "UPDATE activities SET
                  name = '$name',
                  description = '$description',
                  duration = '$duration',
                  price_range = '$price_range',
                  destination_id = $destination_id,
                  is_active = $is_active,
                  image = '$image_path'
                  WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Activity updated successfully!";
            header("Location: manage-activities.php");
            exit();
        } else {
            $error = "Error updating activity: " . mysqli_error($conn);
        }
    }
} elseif (isset($_GET['delete'])) {
    // Delete activity
    $id = intval($_GET['delete']);
    
    // Get image path to delete the file
    $result = mysqli_query($conn, "SELECT image FROM activities WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
    if ($row && !empty($row['image']) && file_exists($row['image'])) {
        unlink($row['image']);
    }
    
    $query = "DELETE FROM activities WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Activity deleted successfully!";
        header("Location: manage-activities.php");
        exit();
    } else {
        $error = "Error deleting activity: " . mysqli_error($conn);
    }
}

// Fetch all activities
$activities = [];
$query = "SELECT a.*, d.popular_title AS destination_name 
          FROM activities a
          LEFT JOIN destinations d ON a.destination_id = d.id
          ORDER BY d.popular_title, a.name";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $activities[] = $row;
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
    <title>Manage Activities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .activity-image {
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
        .description-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
  

    <div class="container py-4">
        <h1 class="mb-4">Manage Activities</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="form-container mb-4">
            <h2><?php echo isset($_GET['edit']) ? 'Edit Activity' : 'Add New Activity'; ?></h2>
            
            <form method="POST" enctype="multipart/form-data">
                <?php
                $editing = false;
                $current_activity = null;
                if (isset($_GET['edit'])) {
                    $edit_id = intval($_GET['edit']);
                    foreach ($activities as $act) {
                        if ($act['id'] == $edit_id) {
                            $current_activity = $act;
                            $editing = true;
                            break;
                        }
                    }
                }
                ?>
                
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo $current_activity['id']; ?>">
                    <input type="hidden" name="current_image" value="<?php echo $current_activity['image']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Activity Name</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?php echo $editing ? htmlspecialchars($current_activity['name']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php 
                                echo $editing ? htmlspecialchars($current_activity['description']) : ''; 
                            ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="duration" name="duration"
                                   value="<?php echo $editing ? htmlspecialchars($current_activity['duration']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="price_range" class="form-label">Price Range</label>
                            <input type="text" class="form-control" id="price_range" name="price_range"
                                   value="<?php echo $editing ? htmlspecialchars($current_activity['price_range']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="destination_id" class="form-label">Destination</label>
                            <select class="form-select" id="destination_id" name="destination_id">
                                <option value="">Select Destination</option>
                                <?php foreach ($destinations as $dest): ?>
                                    <option value="<?php echo $dest['id']; ?>"
                                        <?php if ($editing && $current_activity['destination_id'] == $dest['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($dest['popular_title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                <?php if ($editing && $current_activity['is_active']) echo 'checked'; ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Activity Image</label>
                    <input type="file" class="form-control" id="image" name="image">
                    
                    <?php if ($editing && !empty($current_activity['image'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo htmlspecialchars($current_activity['image']); ?>" 
                                 class="activity-image mb-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remove_image" name="remove_image">
                                <label class="form-check-label" for="remove_image">Remove current image</label>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-between">
                    <?php if ($editing): ?>
                        <button type="submit" name="update_activity" class="btn btn-primary">Update Activity</button>
                        <a href="manage-activities.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_activity" class="btn btn-success">Add Activity</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Activities List -->
        <h2>All Activities</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th>Destination</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activities)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No activities found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activities as $act): ?>
                            <tr>
                                <td><?php echo $act['id']; ?></td>
                                <td>
                                    <?php if (!empty($act['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($act['image']); ?>" 
                                             class="activity-image">
                                    <?php else: ?>
                                        <i class="bi bi-image"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($act['name']); ?></td>
                                <td class="description-cell" title="<?php echo htmlspecialchars($act['description']); ?>">
                                    <?php echo htmlspecialchars($act['description']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($act['duration']); ?></td>
                                <td><?php echo htmlspecialchars($act['price_range']); ?></td>
                                <td><?php echo htmlspecialchars($act['destination_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $act['is_active'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $act['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="manage-activities.php?edit=<?php echo $act['id']; ?>" 
                                       class="btn btn-sm btn-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="manage-activities.php?delete=<?php echo $act['id']; ?>" 
                                       class="btn btn-sm btn-danger" title="Delete"
                                       onclick="return confirm('Are you sure you want to delete this activity?')">
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