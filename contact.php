<?php
// Start session and connect to database
require_once 'admin/includes/db.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Insert into database
    $query = "INSERT INTO contact_submissions (name, email, phone, subject, message, submitted_at) 
              VALUES ('$name', '$email', '$phone', '$subject', '$message', NOW())";
    
    if (mysqli_query($conn, $query)) {
        $success = "Thank you! Your message has been sent successfully.";
    } else {
        $error = "Sorry, there was an error sending your message. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us | Niro SL Travels</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  
  <style>
    .contact-hero {
      background: linear-gradient(rgba(12, 34, 97, 0.8), rgba(12, 34, 97, 0.8)), 
                  url('assets/img/contact-bg.jpg') center/cover no-repeat;
      padding: 100px 0;
      color: white;
    }
    
    .contact-card {
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      border: none;
    }
    
    .contact-card:hover {
      transform: translateY(-5px);
    }
    
    .contact-icon {
      font-size: 2rem;
      color: #0c2261;
      margin-bottom: 15px;
    }
    
    .form-control, .form-select {
      padding: 12px 15px;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: #0c2261;
      box-shadow: 0 0 0 0.25rem rgba(12, 34, 97, 0.25);
    }
    
    .btn-contact {
      background: #0c2261;
      color: white;
      padding: 12px 30px;
      border-radius: 50px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .btn-contact:hover {
      background: #1a3a8f;
      transform: translateY(-2px);
    }
    
    .map-container {
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<!-- Navigation (Include your existing navbar here) -->
<?php include 'components/header.php'; ?>

<!-- Hero Section -->
<section class="contact-hero text-center">
  <div class="container">
    <h1 class="display-4 fw-bold mb-3">Get In Touch</h1>
    <p class="lead">We'd love to hear from you! Reach out for inquiries, bookings, or travel advice.</p>
  </div>
</section>

<!-- Contact Section -->
<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <!-- Contact Info Cards -->
      <div class="col-lg-4">
        <div class="contact-card card h-100 p-4 text-center">
          <i class="bi bi-telephone contact-icon"></i>
          <h4>Call Us</h4>
          <p class="text-muted">Available 9AM - 6PM daily</p>
          <a href="tel:+94112345678" class="btn btn-outline-primary rounded-pill">+94 11 234 5678</a>
        </div>
      </div>
      
      <div class="col-lg-4">
        <div class="contact-card card h-100 p-4 text-center">
          <i class="bi bi-envelope contact-icon"></i>
          <h4>Email Us</h4>
          <p class="text-muted">We'll respond within 24 hours</p>
          <a href="mailto:info@nirosltravels.com" class="btn btn-outline-primary rounded-pill">info@nirosltravels.com</a>
        </div>
      </div>
      
      <div class="col-lg-4">
        <div class="contact-card card h-100 p-4 text-center">
          <i class="bi bi-geo-alt contact-icon"></i>
          <h4>Visit Us</h4>
          <p class="text-muted">123 Galle Road, Colombo 03, Sri Lanka</p>
          <a href="#map" class="btn btn-outline-primary rounded-pill">View Map</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Contact Form Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <h2 class="mb-4">Send Us a Message</h2>
        
        <?php if (isset($success)): ?>
          <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="contact.php">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="col-12">
              <label for="phone" class="form-label">Phone/WhatsApp</label>
              <input type="tel" class="form-control" id="phone" name="phone">
            </div>
            <div class="col-12">
              <label for="subject" class="form-label">Subject</label>
              <select class="form-select" id="subject" name="subject" required>
                <option value="" selected disabled>Select a subject</option>
                <option value="General Inquiry">General Inquiry</option>
                <option value="Tour Booking">Tour Booking</option>
                <option value="Custom Trip">Custom Trip Request</option>
                <option value="Feedback">Feedback</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="col-12">
              <label for="message" class="form-label">Message</label>
              <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-contact">Send Message</button>
            </div>
          </div>
        </form>
      </div>
      
      <div class="col-lg-6">
        <div class="map-container h-100">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.798511757686!2d79.8587744153286!3d6.914657220411325!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2596b8d5f07b1%3A0x6b83e26352a28a98!2sGalle%20Rd%2C%20Colombo!5e0!3m2!1sen!2slk!4v1620000000000!5m2!1sen!2slk" 
                  width="100%" height="100%" style="min-height: 400px; border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
  <div class="container">
    <h2 class="text-center mb-5">Frequently Asked Questions</h2>
    <div class="accordion" id="faqAccordion">
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
            What are your business hours?
          </button>
        </h2>
        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Our office is open from 9:00 AM to 6:00 PM, Monday through Saturday. We're closed on Sundays and public holidays.
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
            How soon can I expect a response?
          </button>
        </h2>
        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            We typically respond to all inquiries within 24 hours during business days. For urgent matters, please call us directly.
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
            Do you offer 24/7 emergency support?
          </button>
        </h2>
        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes! For emergencies during tours or outside business hours, please call our 24/7 hotline at +94 77 234 5678.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<?php include 'components/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>