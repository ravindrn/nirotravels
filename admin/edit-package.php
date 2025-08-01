<?php
require_once 'includes/auth_check.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage-packages.php");
    exit();
}

$package_id = $_GET['id'];
$package = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tour_packages WHERE id = $package_id"));

if (!$package) {
    header("Location: manage-packages.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle package update
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $subtitle = mysqli_real_escape_string($conn, $_POST['subtitle']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $ideal_for = mysqli_real_escape_string($conn, $_POST['ideal_for']);
    $highlights = mysqli_real_escape_string($conn, $_POST['highlights']);
    $inclusions = mysqli_real_escape_string($conn, $_POST['inclusions']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle image update
    $featured_image = $package['featured_image'];
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/packages/';
        $file_name = uniqid() . '_' . basename($_FILES['featured_image']['name']);
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target_path)) {
            // Delete old image if exists
            if ($featured_image && file_exists($upload_dir . $featured_image)) {
                unlink($upload_dir . $featured_image);
            }
            $featured_image = $file_name;
        }
    }
    
    $query = "UPDATE tour_packages SET 
              title = '$title',
              subtitle = '$subtitle',
              duration = '$duration',
              ideal_for = '$ideal_for',
              highlights = '$highlights',
              inclusions = '$inclusions',
              price = '$price',
              featured_image = '$featured_image',
              is_active = $is_active
              WHERE id = $package_id";
    
    mysqli_query($conn, $query);
    
    $_SESSION['message'] = "Package updated successfully!";
    header("Location: manage-packages.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tour Package</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Edit Tour Package</h1>
            <a href="manage-packages.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Packages
            </a>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-12">
                    <label for="title" class="form-label">Package Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($package['title']); ?>" required>
                </div>
                <div class="col-md-12">
                    <label for="subtitle" class="form-label">Subtitle (Ideal for...)</label>
                    <input type="text" class="form-control" id="subtitle" name="subtitle" value="<?= htmlspecialchars($package['subtitle']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="duration" class="form-label">Duration</label>
                    <input type="text" class="form-control" id="duration" name="duration" value="<?= htmlspecialchars($package['duration']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="ideal_for" class="form-label">Ideal For</label>
                    <input type="text" class="form-control" id="ideal_for" name="ideal_for" value="<?= htmlspecialchars($package['ideal_for']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" class="form-control" id="price" name="price" value="<?= htmlspecialchars($package['price']); ?>" required>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch pt-4">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= $package['is_active'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">Active Package</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="featured_image" class="form-label">Featured Image</label>
                    <?php if ($package['featured_image']): ?>
                        <div class="mb-3">
                            <img src="../uploads/packages/<?= $package['featured_image']; ?>" alt="Current image" class="img-thumbnail" style="max-height: 150px;">
                            <div class="form-text">Current image</div>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="featured_image" name="featured_image">
                </div>
                <div class="col-md-12">
                    <label for="highlights" class="form-label">Highlights (One per line)</label>
                    <textarea class="form-control" id="highlights" name="highlights" rows="4" required><?= htmlspecialchars($package['highlights']); ?></textarea>
                </div>
                <div class="col-md-12">
                    <label for="inclusions" class="form-label">Inclusions (One per line)</label>
                    <textarea class="form-control" id="inclusions" name="inclusions" rows="4" required><?= htmlspecialchars($package['inclusions']); ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Package</button>
                </div>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>