<?php
require_once 'includes/db.php';

// Mark feedback as read
if (isset($_GET['mark_read'])) {
    $id = (int)$_GET['mark_read'];
    mysqli_query($conn, "UPDATE customer_feedback SET is_read = 1 WHERE id = $id");
    header("Location: view-feedbacks.php");
    exit;
}

// Delete feedback
if (isset($_GET['delete_feedback'])) {
    $id = (int)$_GET['delete_feedback'];
    mysqli_query($conn, "DELETE FROM customer_feedback WHERE id = $id");
    header("Location: view-feedbacks.php");
    exit;
}

// Get all feedback
$feedback_query = "SELECT * FROM customer_feedback ORDER BY submitted_at DESC";
$feedback_result = mysqli_query($conn, $feedback_query);
$unread_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM customer_feedback WHERE is_read = 0"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customer Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .unread {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
        }
        .feedback-card {
            transition: all 0.3s ease;
        }
        .feedback-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Customer Feedback</h1>
            <?php if ($unread_count > 0): ?>
                <span class="badge bg-primary"><?= $unread_count ?> unread</span>
            <?php endif; ?>
        </div>
        
        <?php if (mysqli_num_rows($feedback_result) > 0): ?>
            <div class="row">
                <?php while ($feedback = mysqli_fetch_assoc($feedback_result)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card feedback-card <?= $feedback['is_read'] ? '' : 'unread' ?>">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><?= htmlspecialchars($feedback['customer_name']) ?></h5>
                                <small class="text-muted"><?= date('M j, Y g:i a', strtotime($feedback['submitted_at'])) ?></small>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?= nl2br(htmlspecialchars($feedback['feedback_text'])) ?></p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-end gap-2">
                                    <?php if (!$feedback['is_read']): ?>
                                        <a href="?mark_read=<?= $feedback['id'] ?>" class="btn btn-sm btn-outline-success">Mark as Read</a>
                                    <?php endif; ?>
                                    <a href="?delete_feedback=<?= $feedback['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this feedback?')">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No feedback submissions yet.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>