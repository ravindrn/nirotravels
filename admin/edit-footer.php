<?php
session_start();
require_once 'includes/db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log database operations
function log_db_operation($message) {
    file_put_contents('footer_edits.log', date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    log_db_operation("Form submission started");
    
    try {
        if (!isset($_POST['footer']) || !is_array($_POST['footer'])) {
            throw new Exception("Invalid form data structure");
        }

        mysqli_begin_transaction($conn);
        
        foreach ($_POST['footer'] as $section => $fields) {
            if (!is_array($fields)) continue;
            
            foreach ($fields as $field_name => $field_value) {
                $field_value = mysqli_real_escape_string($conn, $field_value);
                $section = mysqli_real_escape_string($conn, $section);
                $field_name = mysqli_real_escape_string($conn, $field_name);
                
                // Check if entry exists
                $check_query = "SELECT id FROM footer_content WHERE section = '$section' AND field_name = '$field_name'";
                log_db_operation("Checking existence: " . $check_query);
                
                $check = mysqli_query($conn, $check_query);
                if (!$check) {
                    throw new Exception("Check query failed: " . mysqli_error($conn));
                }
                
                if (mysqli_num_rows($check) > 0) {
                    // Update existing
                    $update_query = "UPDATE footer_content SET field_value = '$field_value' 
                                   WHERE section = '$section' AND field_name = '$field_name'";
                    log_db_operation("Updating: " . $update_query);
                    
                    $result = mysqli_query($conn, $update_query);
                    if (!$result) {
                        throw new Exception("Update failed: " . mysqli_error($conn));
                    }
                } else {
                    // Insert new
                    $insert_query = "INSERT INTO footer_content (section, field_name, field_value) 
                                   VALUES ('$section', '$field_name', '$field_value')";
                    log_db_operation("Inserting: " . $insert_query);
                    
                    $result = mysqli_query($conn, $insert_query);
                    if (!$result) {
                        throw new Exception("Insert failed: " . mysqli_error($conn));
                    }
                }
            }
        }
        
        mysqli_commit($conn);
        $_SESSION['success_message'] = "Footer content updated successfully";
        log_db_operation("Update completed successfully");
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = "Error saving changes: " . $e->getMessage();
        log_db_operation("Error: " . $e->getMessage());
    }
    
    header("Location: edit-footer.php");
    exit;
}

// Get all footer content with error handling
$footer_data = [];
$result = mysqli_query($conn, "SELECT * FROM footer_content");
if (!$result) {
    log_db_operation("Footer content query failed: " . mysqli_error($conn));
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        $footer_data[$row['section']][$row['field_name']] = $row['field_value'];
    }
}

// Default values if not in database
$defaults = [
    'feedback' => [
        'heading' => "We'd love your feedback",
        'placeholder' => "Write your feedback here..."
    ],
    'about' => [
        'heading' => "About Niro SL Travels",
        'content' => "Explore the beauty of Sri Lanka with Niro SL Travels. We bring you unforgettable experiences with passion and professionalism.",
        'social_heading' => "Stay Connected"
    ],
    'contact' => [
        'heading' => "Contact Us",
        'hotline' => "+94 71 123 4567",
        'email' => "support@nirosltravels.lk",
        'address' => "123 Travel St, Colombo, Sri Lanka",
        'hours' => "Mon–Fri: 9am–6pm"
    ],
    'copyright' => [
        'text' => "&copy; 2025 Niro SL Travels. All rights reserved."
    ],
    'social' => [
        'facebook' => "#",
        'instagram' => "#",
        'twitter' => "#",
        'youtube' => "#",
        'linkedin' => "#",
        'tiktok' => "#"
    ],
    'links' => [
        'explore' => [
            'Home' => "index.html",
            'Destinations' => "destinations.html",
            'Packages' => "packages.html",
            'Gallery' => "gallery.html",
            'Contact' => "contact.html"
        ],
        'quick' => [
            'About Us' => "about.html",
            'Terms & Conditions' => "terms.html",
            'Privacy Policy' => "privacy.html",
            'FAQs' => "faq.html"
        ]
    ]
];

// Merge defaults with database values
foreach ($defaults as $section => $fields) {
    if (!isset($footer_data[$section])) {
        $footer_data[$section] = [];
    }
    foreach ($fields as $field_name => $value) {
        if (!isset($footer_data[$section][$field_name])) {
            $footer_data[$section][$field_name] = $value;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Footer Content</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .nav-tabs .nav-link.active {
            font-weight: bold;
            border-bottom: 3px solid #0d6efd;
        }
        .tab-content {
            padding: 20px;
            border-left: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
        }

        .debug-info {
            position: fixed;
            bottom: 0;
            left: 0;
            background: #f8f9fa;
            padding: 10px;
            border-top: 1px solid #ddd;
            font-family: monospace;
            font-size: 12px;
            z-index: 1000;
        }
    </style>
</head>
<body>
   <div class="container py-4">
        <h1 class="mb-4">Edit Footer Content</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success_message'] ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error_message'] ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <form method="POST">
            <ul class="nav nav-tabs" id="footerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback" type="button" role="tab">Feedback</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">About & Social</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="links-tab" data-bs-toggle="tab" data-bs-target="#links" type="button" role="tab">Navigation Links</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">Contact Info</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="copyright-tab" data-bs-toggle="tab" data-bs-target="#copyright" type="button" role="tab">Copyright</button>
                </li>
            </ul>
            
            <div class="tab-content" id="footerTabsContent">
                <!-- Feedback Tab -->
                <div class="tab-pane fade show active" id="feedback" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label">Feedback Heading</label>
                        <input type="text" class="form-control" name="footer[feedback][heading]" value="<?= htmlspecialchars($footer_data['feedback']['heading']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Placeholder Text</label>
                        <input type="text" class="form-control" name="footer[feedback][placeholder]" value="<?= htmlspecialchars($footer_data['feedback']['placeholder']) ?>">
                    </div>
                </div>
                
                <!-- About & Social Tab -->
                <div class="tab-pane fade" id="about" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label">About Heading</label>
                        <input type="text" class="form-control" name="footer[about][heading]" value="<?= htmlspecialchars($footer_data['about']['heading']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">About Content</label>
                        <textarea class="form-control" name="footer[about][content]" rows="3"><?= htmlspecialchars($footer_data['about']['content']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Social Heading</label>
                        <input type="text" class="form-control" name="footer[about][social_heading]" value="<?= htmlspecialchars($footer_data['about']['social_heading']) ?>">
                    </div>
                    <hr>
                    <h5>Social Media Links</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-facebook"></i> Facebook URL</label>
                                <input type="url" class="form-control" name="footer[social][facebook]" value="<?= htmlspecialchars($footer_data['social']['facebook']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-instagram"></i> Instagram URL</label>
                                <input type="url" class="form-control" name="footer[social][instagram]" value="<?= htmlspecialchars($footer_data['social']['instagram']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-twitter"></i> Twitter URL</label>
                                <input type="url" class="form-control" name="footer[social][twitter]" value="<?= htmlspecialchars($footer_data['social']['twitter']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-youtube"></i> YouTube URL</label>
                                <input type="url" class="form-control" name="footer[social][youtube]" value="<?= htmlspecialchars($footer_data['social']['youtube']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-linkedin"></i> LinkedIn URL</label>
                                <input type="url" class="form-control" name="footer[social][linkedin]" value="<?= htmlspecialchars($footer_data['social']['linkedin']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-tiktok"></i> TikTok URL</label>
                                <input type="url" class="form-control" name="footer[social][tiktok]" value="<?= htmlspecialchars($footer_data['social']['tiktok']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Links Tab -->
                <div class="tab-pane fade" id="links" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Explore Links</h5>
                            <?php foreach ($footer_data['links']['explore'] as $text => $url): ?>
                                <div class="mb-3">
                                    <label class="form-label"><?= htmlspecialchars($text) ?> URL</label>
                                    <input type="url" class="form-control" name="footer[links][explore][<?= htmlspecialchars($text) ?>]" value="<?= htmlspecialchars($url) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-md-6">
                            <h5>Quick Links</h5>
                            <?php foreach ($footer_data['links']['quick'] as $text => $url): ?>
                                <div class="mb-3">
                                    <label class="form-label"><?= htmlspecialchars($text) ?> URL</label>
                                    <input type="url" class="form-control" name="footer[links][quick][<?= htmlspecialchars($text) ?>]" value="<?= htmlspecialchars($url) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Info Tab -->
                <div class="tab-pane fade" id="contact" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label">Contact Heading</label>
                        <input type="text" class="form-control" name="footer[contact][heading]" value="<?= htmlspecialchars($footer_data['contact']['heading']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hotline</label>
                        <input type="text" class="form-control" name="footer[contact][hotline]" value="<?= htmlspecialchars($footer_data['contact']['hotline']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="footer[contact][email]" value="<?= htmlspecialchars($footer_data['contact']['email']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="footer[contact][address]" rows="2"><?= htmlspecialchars($footer_data['contact']['address']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Business Hours</label>
                        <input type="text" class="form-control" name="footer[contact][hours]" value="<?= htmlspecialchars($footer_data['contact']['hours']) ?>">
                    </div>
                </div>
                
                <!-- Copyright Tab -->
                <div class="tab-pane fade" id="copyright" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label">Copyright Text</label>
                        <input type="text" class="form-control" name="footer[copyright][text]" value="<?= htmlspecialchars($footer_data['copyright']['text']) ?>">
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

    <div class="debug-info">
        <strong>Debug Info:</strong><br>
        Last Action: <?= date('Y-m-d H:i:s') ?><br>
        <?php if (isset($footer_data)): ?>
            Data Count: <?= count($footer_data) ?> sections loaded<br>
        <?php endif; ?>
        <a href="view_logs.php" target="_blank">View Operation Logs</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>