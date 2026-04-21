<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include('../config/database.php');
include('../action/sit_in.php');

// get feedbacks from students
$feedback_list = getFeedbacks($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Feedback | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f0f2f5; }
        .feedback-card { transition: transform 0.2s; border: none; border-radius: 15px; }
        .feedback-card:hover { transform: translateY(-5px); }
        .quote-icon { font-size: 2rem; color: #dee2e6; position: absolute; top: 10px; right: 20px; }
    </style>
</head>
<body>
    <?php include("../includes/adminHeader.php"); ?>

    <main class="container py-5">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold text-dark">Student Feedbacks</h2>
                <p class="text-muted">Monitor lab experiences and student reports</p>
            </div>
            <div class="bg-white p-3 rounded-pill shadow-sm border px-4">
                <span class="fw-bold text-primary"><?php echo mysqli_num_rows($feedback_list); ?></span> 
                <span class="text-muted small">Total Submissions</span>
            </div>
        </header>

        <div class="row">
            <?php if(mysqli_num_rows($feedback_list) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($feedback_list)): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 feedback-card shadow-sm">
                            <div class="card-body p-4 position-relative">
                                <i class="bi bi-quote quote-icon"></i>
                                
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 mb-3">
                                    Lab <?php echo htmlspecialchars($row['category']); ?>
                                </span>

                                <p class="card-text text-dark mt-2" style="font-size: 1.05rem; min-height: 80px;">
                                    <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                                </p>

                                <div class="d-flex align-items-center mt-4 pt-3 border-top">
                                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-weight: bold;">
                                        <?php echo substr($row['fullname'], 0, 1); ?>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($row['fullname']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['student_id']); ?></small>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-calendar3"></i> Submitted: <?php echo date('M d, Y | h:i A', strtotime($row['submitted_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="bg-white d-inline-block p-5 rounded-circle shadow-sm mb-4">
                        <i class="bi bi-chat-left-dots text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-muted">No Feedback Yet</h3>
                    <p class="text-muted">Student reviews will appear here once they are submitted.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>