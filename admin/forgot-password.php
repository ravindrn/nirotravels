<?php
require_once 'includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $query = "SELECT * FROM admin_users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // In a real implementation, you would:
        // 1. Generate a reset token
        // 2. Store it in the database with expiration
        // 3. Send an email with reset link
        
        $message = "If an account exists with this email, a password reset link has been sent.";
    } else {
        $message = "If an account exists with this email, a password reset link has been sent.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Niro SL Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Reset Your Password</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-info"><?= $message ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Request Reset Link</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="login.php">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>