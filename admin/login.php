<?php
session_start();
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM admin_users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password_hash'])) {
            // Update last login time
            mysqli_query($conn, "UPDATE admin_users SET last_login = NOW() WHERE id = {$user['id']}");
            
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_full_name'] = $user['full_name'];
            $_SESSION['last_login'] = date('Y-m-d H:i:s');
            $_SESSION['last_activity'] = time();
            
            header("Location: admin-dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Niro SL Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            max-width: 400px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: none;
        }
        .login-header {
            background-color: #0d6efd;
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn-login {
            background-color: #0d6efd;
            border: none;
            padding: 10px;
        }
        .btn-login:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card login-card">
                    <div class="card-header login-header text-center py-3">
                        <h4><i class="bi bi-shield-lock"></i> Admin Portal</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <img src="../assets/img/logo.png" alt="Niro SL Travels" width="120" class="mb-3">
                            <h5>Niro SL Travels Admin</h5>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-login btn-primary text-white">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </button>
                            </div>
                            <div class="text-center">
                                <a href="forgot-password.php" class="text-decoration-none">Forgot Password?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>