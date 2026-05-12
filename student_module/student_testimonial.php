<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/database.php");
include("../action/studentData.php"); // session id
include("../action/testimonials.php");

$student_pk = $student['id'];

// Active page for sidebar highlighting
$current_page = basename($_SERVER['PHP_SELF']);

$message_alert = $message;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Testimonials | CCS LAB PORTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Use your existing CSS here -->
    <style>
        /* [INSERT YOUR SIDEBAR CSS FROM THE PROMPT HERE] */
        
        /* TESTIMONIAL SPECIFIC STYLES */
        .testimonial-card {
            background: #fff;
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .rating-select {
            color: #ffc107;
            font-size: 1.2rem;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .star-rating {
            color: #ffc107;
        }
    </style>
</head>
<body>

<!-- Reuse your Sidebar HTML -->
<?php include('../includes/student_sidebar.php'); ?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold mb-4">Feedback & Testimonials</h3>
                <?= $message_alert ?? ""; ?>
            </div>
        </div>

        <div class="row g-4">
            <!-- FORM SECTION -->
            <div class="col-lg-5">
                <div class="card testimonial-card p-4">
                    <h5 class="fw-bold mb-3">Share your experience</h5>
                    <form action="" method="POST">
                        <input type="hidden" name="student_pk" value="<?= $student_pk ?>"> <!--Student_pk-->
                        <div class="mb-3">
                            <label class="form-label">How would you rate us?</label>
                            <select name="rating" class="form-select" required>
                                <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                                <option value="4">⭐⭐⭐⭐ (Good)</option>
                                <option value="3">⭐⭐⭐ (Average)</option>
                                <option value="2">⭐⭐ (Poor)</option>
                                <option value="1">⭐ (Very Bad)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Share Us your Thoughts!</label>
                            <textarea name="content" class="form-control" rows="5" placeholder="Tell us what you think about the CCS Sit in Monitoring..." required></textarea>
                        </div>
                        <button type="submit" name="submit_testimonial" class="btn btn-primary w-100 py-2 fw-bold">
                            Submit Feedback
                        </button>
                    </form>
                </div>
            </div>

            <!-- HISTORY SECTION -->
            <div class="col-lg-7">
                <h5 class="fw-bold mb-3">Your Submissions</h5>
                <div class="row g-3">
                    <?php
                    $fetch_query = "SELECT * FROM testimonials WHERE student_pk = $student_pk ORDER BY created_at DESC";
                    $result = mysqli_query($conn, $fetch_query);

                    if(mysqli_num_rows($result) > 0):
                        while($row = mysqli_fetch_assoc($result)):
                    ?>
                        <div class="col-12">
                            <div class="card testimonial-card p-3 shadow-sm">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="star-rating mb-2">
                                        <?php for($i=1; $i<=5; $i++) {
                                            echo $i <= $row['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                        } ?>
                                    </div>
                                    <span class="badge status-badge 
                                        <?= $row['status'] == 'approved' ? 'bg-success-subtle text-success' : ($row['status'] == 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </div>
                                <p class="text-muted mb-2 small italic">"<?= htmlspecialchars($row['content']) ?>"</p>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    Submitted on: <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-chat-square-dots" style="font-size: 3rem;"></i>
                            <p>No testimonials yet. Be the first to share!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Copy the sidebar script you provided here
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggleSidebar");
    const logo = document.getElementById("logoToggle");

    toggleBtn.addEventListener("click", () => { sidebar.classList.toggle("collapsed"); });
    logo.addEventListener("click", () => { sidebar.classList.toggle("collapsed"); });
</script>

</body>
</html>