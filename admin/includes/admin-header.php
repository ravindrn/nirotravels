<?php
require_once 'includes/auth_check.php'; // Ensure admin is logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Admin Panel'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom Admin CSS -->
    <style>
        body {
            padding-top: 0;
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #212529;
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            font-weight: 500;
            color: #adb5bd;
            padding: 0.75rem 1rem;
            border-left: 4px solid transparent;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #0d6efd;
        }
        .sidebar .nav-link i {
            margin-right: 4px;
            color: #6c757d;
        }
        .sidebar .nav-link.active i,
        .sidebar .nav-link:hover i {
            color: inherit;
        }
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }
        .navbar .navbar-toggler {
            top: .25rem;
            right: 1rem;
        }
        .main-content {
            margin-left: 250px;
        }
        @media (max-width: 767.98px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .sidebar-sticky {
                height: auto;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3 sidebar-sticky">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="bi bi-person-circle text-white" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-white mb-1"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></h5>
                        <small class="text-muted"><?= htmlspecialchars($_SESSION['admin_email'] ?? 'admin@example.com'); ?></small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'active' : ''; ?>" href="admin-dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage-packages.php' ? 'active' : ''; ?>" href="manage-packages.php">
                                <i class="bi bi-briefcase"></i> Tour Packages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'edit-homepage.php' ? 'active' : ''; ?>" href="edit-homepage.php">
                                <i class="bi bi-house-door"></i> Homepage Content
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'view-feedbacks.php' ? 'active' : ''; ?>" href="view-feedbacks.php">
                                <i class="bi bi-chat-left-text"></i> Customer Feedback
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage-testimonials.php' ? 'active' : ''; ?>" href="manage-testimonials.php">
                                <i class="bi bi-quote"></i> Testimonials
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage-bookings.php' ? 'active' : ''; ?>" href="manage-bookings.php">
                                <i class="bi bi-calendar-check"></i> Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'admin-settings.php' ? 'active' : ''; ?>" href="admin-settings.php">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= $page_title ?? 'Dashboard'; ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-calendar"></i> <?= date('F j, Y'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-clock"></i> <?= date('h:i A'); ?>
                            </button>
                        </div>
                        <a href="../index.php" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View Website
                        </a>
                    </div>
                </div>

                <!-- Display success/error messages -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type'] ?? 'success'; ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>