<?php
require_once 'includes/db.php';
require_once 'includes/auth_check.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Debug: Verify POST data is being received
error_log("POST data: " . print_r($_POST, true));
error_log("FILES data: " . print_r($_FILES, true));
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mysqli_begin_transaction($conn);

    try {
        // Sanitize inputs
        $hero_title = mysqli_real_escape_string($conn, $_POST['hero_title']);
        $hero_description = mysqli_real_escape_string($conn, $_POST['hero_description']);
        $hero_image = processImageUpload('hero_image', $_POST['current_hero_image'] ?? null, '../uploads/about/', 'hero_');

        $story_title = mysqli_real_escape_string($conn, $_POST['story_title']);
        $story_content = mysqli_real_escape_string($conn, $_POST['story_content']);
        $story_image = processImageUpload('story_image', $_POST['current_story_image'] ?? null, '../uploads/about/', 'story_');

        $stats = [
            'stat1_number' => mysqli_real_escape_string($conn, $_POST['stat1_number']),
            'stat1_text' => mysqli_real_escape_string($conn, $_POST['stat1_text']),
            'stat2_number' => mysqli_real_escape_string($conn, $_POST['stat2_number']),
            'stat2_text' => mysqli_real_escape_string($conn, $_POST['stat2_text']),
            'stat3_number' => mysqli_real_escape_string($conn, $_POST['stat3_number']),
            'stat3_text' => mysqli_real_escape_string($conn, $_POST['stat3_text']),
            'stat4_number' => mysqli_real_escape_string($conn, $_POST['stat4_number']),
            'stat4_text' => mysqli_real_escape_string($conn, $_POST['stat4_text'])
        ];

        $mission_heading = mysqli_real_escape_string($conn, $_POST['mission_heading']);
    $mission_lead = mysqli_real_escape_string($conn, $_POST['mission_lead']);
    $mission_additional = mysqli_real_escape_string($conn, $_POST['mission_additional']);

    // Update mission
    $mission_query = "UPDATE about_mission SET 
                     heading = '$mission_heading',
                     lead_text = '$mission_lead',
                     additional_text = '$mission_additional'
                     WHERE id = 1";
    if (!mysqli_query($conn, $mission_query)) {
        throw new Exception("Failed to update mission section: " . mysqli_error($conn));
    }
        // Update hero
        $hero_query = "UPDATE about_hero SET 
                          title = '$hero_title',
                          description = '$hero_description',
                          background_image = " . ($hero_image ? "'$hero_image'" : "NULL") . "
                          WHERE id = 1";
        if (!mysqli_query($conn, $hero_query)) {
            throw new Exception("Failed to update hero section.");
        }

        // Update story
        $story_query = "UPDATE about_story SET 
                           title = '$story_title',
                           content = '$story_content',
                           image = " . ($story_image ? "'$story_image'" : "NULL") . "
                           WHERE id = 1";
        if (!mysqli_query($conn, $story_query)) {
            throw new Exception("Failed to update story section.");
        }

        // Update stats
        $stats_query = "UPDATE about_stats SET 
                           stat1_number = '{$stats['stat1_number']}',
                           stat1_text = '{$stats['stat1_text']}',
                           stat2_number = '{$stats['stat2_number']}',
                           stat2_text = '{$stats['stat2_text']}',
                           stat3_number = '{$stats['stat3_number']}',
                           stat3_text = '{$stats['stat3_text']}',
                           stat4_number = '{$stats['stat4_number']}',
                           stat4_text = '{$stats['stat4_text']}'
                           WHERE id = 1";
        if (!mysqli_query($conn, $stats_query)) {
            throw new Exception("Failed to update stats section.");
        }

        mysqli_commit($conn);
        $_SESSION['message'] = "About Us page updated successfully!";
        header("Location: edit-aboutus.php");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = htmlspecialchars("Error updating content: " . $e->getMessage());
    }
}

function processImageUpload($fieldName, $currentImage, $uploadDir, $prefix) {
    if (!isset($_FILES[$fieldName])) return $currentImage;

    if ($_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $mimeType = mime_content_type($_FILES[$fieldName]['tmp_name']);
        if (strpos($mimeType, 'image/') !== 0) {
            throw new Exception("Uploaded file for $fieldName is not a valid image.");
        }

        $extension = pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION);
        $filename = $prefix . time() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination)) {
            if ($currentImage && file_exists($uploadDir . $currentImage)) {
                if (!unlink($uploadDir . $currentImage)) {
                    throw new Exception("Failed to delete old image: $currentImage");
                }
            }
            return $filename;
        }
    } elseif (!empty($_POST['remove_' . $fieldName]) && $_POST['remove_' . $fieldName] === 'on') {
        if ($currentImage && file_exists($uploadDir . $currentImage)) {
            if (!unlink($uploadDir . $currentImage)) {
                throw new Exception("Failed to remove old image: $currentImage");
            }
        }
        return null;
    }

    return $currentImage;
}

// Fetch current data
$hero = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_hero LIMIT 1")) ?: [
    'title' => 'Discover Ceylon Wonders',
    'description' => 'Your trusted local experts...',
    'background_image' => null
];

$story = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_story LIMIT 1")) ?: [
    'title' => 'Our Sri Lankan Story',
    'content' => "Founded by three childhood friends...",
    'image' => null
];

$stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_stats LIMIT 1")) ?: [
    'stat1_number' => '13+', 'stat1_text' => 'Years Showing Sri Lanka',
    'stat2_number' => '25', 'stat2_text' => 'Local Guides',
    'stat3_number' => '100+', 'stat3_text' => 'Happy Travelers',
    'stat4_number' => '100%', 'stat4_text' => 'Sri Lankan Team'
];

$mission = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_mission LIMIT 1")) ?: [
    'heading' => 'Our Mission',
    'lead_text' => 'To provide authentic, responsible, and unforgettable Sri Lankan travel experiences...',
    'additional_text' => 'We measure our success by the smiles of our guests...'
];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About Us Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .section-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }
        .section-title {
            color: #2a5a3c;
            border-bottom: 2px solid #f9a825;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .preview-image {
            max-width: 100%;
            height: auto;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .stat-card {
            padding: 15px;
            margin-bottom: 15px;
            background-color: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .stat-card h4 {
            color: #457b9d;
            font-size: 1.1rem;
        }
        .tab-content {
            padding: 20px 0;
        }
    </style>
</head>
<body>
   

    <div class="container py-4">
        <h1 class="mb-4">Edit About Us Page</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <!-- Hero Section -->
            <div class="section-container">
                <h2 class="section-title">Hero Section</h2>
                <input type="hidden" name="current_hero_image" value="<?= htmlspecialchars($hero['background_image']) ?>">
                
                <div class="mb-3">
                    <label for="hero_title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="hero_title" name="hero_title" required
                           value="<?= htmlspecialchars($hero['title']) ?>">
                </div>
                
                <div class="mb-3">
                    <label for="hero_description" class="form-label">Description</label>
                    <textarea class="form-control" id="hero_description" name="hero_description" rows="3" required><?= 
                        htmlspecialchars($hero['description']) 
                    ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="hero_image" class="form-label">Background Image</label>
                    <input type="file" class="form-control" id="hero_image" name="hero_image" accept="image/*">
                    
                    <?php if ($hero['background_image']): ?>
                        <div class="mt-3">
                            <p>Current Image:</p>
                            <img src="../uploads/about/<?= htmlspecialchars($hero['background_image']) ?>" 
                                 class="preview-image" id="heroImagePreview">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="remove_hero_image" name="remove_hero_image">
                                <label class="form-check-label" for="remove_hero_image">Remove current image</label>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Story Section -->
            <div class="section-container">
                <h2 class="section-title">Our Story Section</h2>
                <input type="hidden" name="current_story_image" value="<?= htmlspecialchars($story['image']) ?>">
                
                <div class="mb-3">
                    <label for="story_title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="story_title" name="story_title" required
                           value="<?= htmlspecialchars($story['title']) ?>">
                </div>
                
                <div class="mb-3">
                    <label for="story_content" class="form-label">Content</label>
                    <textarea class="form-control" id="story_content" name="story_content" rows="6" required><?= 
                        htmlspecialchars($story['content']) 
                    ?></textarea>
                    <small class="text-muted">Separate paragraphs with blank lines</small>
                </div>
                
                <div class="mb-3">
                    <label for="story_image" class="form-label">Story Image</label>
                    <input type="file" class="form-control" id="story_image" name="story_image" accept="image/*">
                    
                    <?php if ($story['image']): ?>
                        <div class="mt-3">
                            <p>Current Image:</p>
                            <img src="../uploads/about/<?= htmlspecialchars($story['image']) ?>" 
                                 class="preview-image" id="storyImagePreview">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="remove_story_image" name="remove_story_image">
                                <label class="form-check-label" for="remove_story_image">Remove current image</label>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="section-container">
                <h2 class="section-title">Statistics Section</h2>
                
                <div class="row">
                    <!-- Stat 1 -->
                    <div class="col-md-6">
                        <div class="stat-card">
                            <h4>Statistic 1</h4>
                            <div class="mb-3">
                                <label for="stat1_number" class="form-label">Number</label>
                                <input type="text" class="form-control" id="stat1_number" name="stat1_number" required
                                       value="<?= htmlspecialchars($stats['stat1_number']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="stat1_text" class="form-label">Description</label>
                                <input type="text" class="form-control" id="stat1_text" name="stat1_text" required
                                       value="<?= htmlspecialchars($stats['stat1_text']) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Stat 2 -->
                    <div class="col-md-6">
                        <div class="stat-card">
                            <h4>Statistic 2</h4>
                            <div class="mb-3">
                                <label for="stat2_number" class="form-label">Number</label>
                                <input type="text" class="form-control" id="stat2_number" name="stat2_number" required
                                       value="<?= htmlspecialchars($stats['stat2_number']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="stat2_text" class="form-label">Description</label>
                                <input type="text" class="form-control" id="stat2_text" name="stat2_text" required
                                       value="<?= htmlspecialchars($stats['stat2_text']) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Stat 3 -->
                    <div class="col-md-6">
                        <div class="stat-card">
                            <h4>Statistic 3</h4>
                            <div class="mb-3">
                                <label for="stat3_number" class="form-label">Number</label>
                                <input type="text" class="form-control" id="stat3_number" name="stat3_number" required
                                       value="<?= htmlspecialchars($stats['stat3_number']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="stat3_text" class="form-label">Description</label>
                                <input type="text" class="form-control" id="stat3_text" name="stat3_text" required
                                       value="<?= htmlspecialchars($stats['stat3_text']) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Stat 4 -->
                    <div class="col-md-6">
                        <div class="stat-card">
                            <h4>Statistic 4</h4>
                            <div class="mb-3">
                                <label for="stat4_number" class="form-label">Number</label>
                                <input type="text" class="form-control" id="stat4_number" name="stat4_number" required
                                       value="<?= htmlspecialchars($stats['stat4_number']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="stat4_text" class="form-label">Description</label>
                                <input type="text" class="form-control" id="stat4_text" name="stat4_text" required
                                       value="<?= htmlspecialchars($stats['stat4_text']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-container">
    <h2 class="section-title">Mission Section</h2>
    
    <div class="mb-3">
        <label for="mission_heading" class="form-label">Heading</label>
        <input type="text" class="form-control" id="mission_heading" name="mission_heading" required
               value="<?= htmlspecialchars($mission['heading']) ?>">
    </div>
    
    <div class="mb-3">
        <label for="mission_lead" class="form-label">Lead Text</label>
        <textarea class="form-control" id="mission_lead" name="mission_lead" rows="3" required><?= 
            htmlspecialchars($mission['lead_text']) 
        ?></textarea>
    </div>
    
    <div class="mb-3">
        <label for="mission_additional" class="form-label">Additional Text</label>
        <textarea class="form-control" id="mission_additional" name="mission_additional" rows="2"><?= 
            htmlspecialchars($mission['additional_text']) 
        ?></textarea>
    </div>
</div>

            <div class="d-flex justify-content-between mt-4">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                <button type="submit" class="btn btn-primary">Save All Changes</button>
            </div>
        </form>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        function setupImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.getElementById(previewId);
                    if (!preview) {
                        const container = input.closest('.mb-3');
                        preview = document.createElement('img');
                        preview.id = previewId;
                        preview.className = 'preview-image mt-3';
                        container.appendChild(document.createElement('p')).textContent = 'New Image Preview:';
                        container.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        setupImagePreview('hero_image', 'heroImagePreview');
        setupImagePreview('story_image', 'storyImagePreview');
    </script>
</body>
</html>