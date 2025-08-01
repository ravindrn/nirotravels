<?php
require_once 'admin/includes/db.php';
// Get all footer content
$footer_data = [];
$result = mysqli_query($conn, "SELECT * FROM footer_content");
while ($row = mysqli_fetch_assoc($result)) {
    $footer_data[$row['section']][$row['field_name']] = $row['field_value'];
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


// Get all footer content
$footer_data = [];
$result = mysqli_query($conn, "SELECT * FROM footer_content");
while ($row = mysqli_fetch_assoc($result)) {
    $footer_data[$row['section']][$row['field_name']] = $row['field_value'];
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

// Handle form submission directly in footer.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_name'], $_POST['feedback_text'])) {
    require_once __DIR__ . '/admin/includes/db.php';
    
    $submission_status = '';
    $name = trim($_POST['customer_name']);
    $feedback = trim($_POST['feedback_text']);
    
    if (empty($name) || empty($feedback)) {
        $submission_status = 'Please fill in all fields.';
    } else {
        // Sanitize and submit
        $name = mysqli_real_escape_string($conn, $name);
        $feedback = mysqli_real_escape_string($conn, $feedback);
        
        $sql = "INSERT INTO customer_feedback (customer_name, feedback_text) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt && mysqli_stmt_bind_param($stmt, "ss", $name, $feedback) && mysqli_stmt_execute($stmt)) {
            $submission_status = 'success';
            // Clear form fields on success
            $_POST['customer_name'] = '';
            $_POST['feedback_text'] = '';
        } else {
            $submission_status = 'Error submitting feedback. Please try again.';
            error_log("Feedback error: " . mysqli_error($conn));
        }
        
        if ($stmt) mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
?>

<head>
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous" />
  <link href="https://fonts.googleapis.com/css2?family=Lato&family=Playfair+Display:wght@700&family=Roboto&family=Poppins&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<!-- Footer -->
<footer class="footer">
  <div class="container">

   <div class="feedback-box text-center" data-aos="fade-down">
  <h1 class="mb-3">We'd love your feedback</h1>
  
  <div id="feedbackMessage" class="mb-3" style="display:none;"></div>
  
  <form id="feedbackForm" method="POST" action="submit-feedback.php">
    <div class="mb-3">
      <input type="text" 
             name="customer_name" 
             class="form-control" 
             placeholder="Your Name" 
             required
             minlength="2">
    </div>
    <div class="mb-3">
      <textarea name="feedback_text" 
                class="form-control" 
                placeholder="Your feedback..."
                required
                minlength="10"
                rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">
      <span id="submitText">Submit Feedback</span>
      <span id="loadingSpinner" style="display:none;">
        <i class="fas fa-spinner fa-spin"></i> Processing...
      </span>
    </button>
  </form>
</div>

<style>
.submission-message {
    padding: 10px;
    border-radius: 4px;
    font-weight: bold;
}
.text-success {
    color: #28a745;
    background-color: #e8f9f0;
}
.text-danger {
    color: #dc3545;
    background-color: #fce8e8;
}
</style>

    <div class="row text-start">

      <!-- About & Social -->
      <div class="col-md-4 mb-4" data-aos="fade-right">
        <h5><?= htmlspecialchars($footer_data['about']['heading']) ?></h5>
        <p><?= htmlspecialchars($footer_data['about']['content']) ?></p>
        <h6><?= htmlspecialchars($footer_data['about']['social_heading']) ?></h6>
        <div class="social-icons mt-3">
          <a href="<?= htmlspecialchars($footer_data['social']['facebook']) ?>" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="<?= htmlspecialchars($footer_data['social']['instagram']) ?>" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="<?= htmlspecialchars($footer_data['social']['twitter']) ?>" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
          <a href="<?= htmlspecialchars($footer_data['social']['youtube']) ?>" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          <a href="<?= htmlspecialchars($footer_data['social']['linkedin']) ?>" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
          <a href="<?= htmlspecialchars($footer_data['social']['tiktok']) ?>" aria-label="Tiktok"><i class="bi bi-tiktok"></i></a>
        </div>
      </div>

      <!-- Navigation Links -->
      <div class="col-md-4 mb-4" data-aos="zoom-in">
        <div class="row">
          <div class="col-6">
            <h6>Explore</h6>
            <ul class="list-unstyled">
              <?php foreach ($footer_data['links']['explore'] as $text => $url): ?>
                <li><a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($text) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="col-6">
            <h6>Quick Links</h6>
            <ul class="list-unstyled">
              <?php foreach ($footer_data['links']['quick'] as $text => $url): ?>
                <li><a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($text) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>

      <!-- Contact Info -->
      <div class="col-md-4 mb-4" data-aos="fade-left">
        <h5><?= htmlspecialchars($footer_data['contact']['heading']) ?></h5>
        <p><strong>Hotline:</strong> <?= htmlspecialchars($footer_data['contact']['hotline']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($footer_data['contact']['email']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($footer_data['contact']['address']) ?></p>
        <p><strong>Hours:</strong> <?= htmlspecialchars($footer_data['contact']['hours']) ?></p>
      </div>

    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom mt-4 text-center" data-aos="fade-up">
      <?= $footer_data['copyright']['text'] ?>
    </div>

  </div>
</footer>

<script>
document.getElementById('feedbackForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const messageDiv = document.getElementById('feedbackMessage');
    const submitText = document.getElementById('submitText');
    const spinner = document.getElementById('loadingSpinner');
    
    // Show loading state
    submitText.style.display = 'none';
    spinner.style.display = 'inline-block';
    messageDiv.style.display = 'none';
    
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
    })
    .then(response => response.text())
    .then(result => {
        messageDiv.style.display = 'block';
        
        if (result === 'success') {
            messageDiv.className = 'alert alert-success';
            messageDiv.textContent = 'Thank you for your feedback!';
            form.reset();
        } else if (result === 'empty') {
            messageDiv.className = 'alert alert-warning';
            messageDiv.textContent = 'Please fill in all fields.';
        } else {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Error submitting feedback. Please try again.';
            console.error('Submission error:', result);
        }
    })
    .catch(error => {
        messageDiv.style.display = 'block';
        messageDiv.className = 'alert alert-danger';
        messageDiv.textContent = 'Network error. Please try again.';
        console.error('Fetch error:', error);
    })
    .finally(() => {
        submitText.style.display = 'inline-block';
        spinner.style.display = 'none';
    });
});


</script>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    offset: 100,
    once: true
  });
</script>