<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$log_file = 'footer_edits.log';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Footer Edit Logs</title>
    <style>
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Footer Edit Logs</h2>
        <pre><?= file_exists($log_file) ? htmlspecialchars(file_get_contents($log_file)) : 'No logs found' ?></pre>
        <a href="edit-footer.php" class="btn btn-primary">Back to Footer Editor</a>
    </div>
</body>
</html>