<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/database.php");
include("../action/testimonials.php");

$current_page = basename($_SERVER['PHP_SELF']);

// get all testimonials
$result = getAllTestimonials($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Management | CCS ADMIN</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ================= BASE ================= */
        body {
            margin: 0;
            background: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ================= MAIN CONTENT LAYOUT ================= */
        .main-content { 
            margin-left: 260px; 
            padding: 2rem; 
            transition: margin-left .3s ease; 
        }
        
        /* Ensure layout works with your sidebar.php state */
        .sidebar.collapsed ~ .main-content { margin-left: 80px; }

        /* ================= UI COMPONENTS ================= */
        .feedback-card { border-radius: 16px; border: none; background: #fff; overflow: hidden; }
        
        .table thead th { 
            background: #fcfcfd; 
            color: #7a7a7a; 
            font-size: 0.75rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            padding: 1.2rem 1rem;
            border-bottom: 1px solid #f1f1f1;
        }

        .status-badge { 
            font-size: 0.7rem; 
            padding: 5px 12px; 
            border-radius: 50px; 
            font-weight: 700; 
        }

        /* Button Customization for consistent alignment */
        .btn-sm {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
        }

        /* Mobile Adjustments */
        @media(max-width:991px){
            .main-content { margin-left: 0 !important; }
        }
    </style>
</head>
<body>

<?php include("../includes/admin_sidebar.php"); ?>

<!-- MAIN CONTENT -->
<main class="main-content">
    <div class="container-fluid">
        
        <!-- Header Section -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark mb-1">Portal Feedbacks</h3>
                <p class="text-muted small mb-0">Review and moderate student portal testimonials.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="bg-white p-2 d-inline-block rounded-3 border shadow-sm">
                    <form action="" method="GET">
                        <span class="text-muted small px-2">Show:</span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary px-3" name="all_requests">All</button>
                            <button class="btn btn-outline-secondary px-3" name="pending_requests">Pending</button>
                            <button class="btn btn-outline-secondary px-3 shadow-sm" name="approved_requests">Approved</button>
                        </div>
                    </form
                </div>
            </div>
        </div>

        <!-- Feedback Table Card -->
        <div class="card feedback-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Student</th>
                                <th>Testimonial</th> <!-- Removed text-center to align with standard text flow -->
                                <th class="text-center">Rating</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th> <!-- Unified to center -->
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php while($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td class="ps-4"> <!-- Added padding to match header -->
                                        <?= htmlspecialchars($row['fullname']) ?> <br>
                                        <small class="text-muted" style="font-size: 0.8rem;">
                                            <?= htmlspecialchars($row['student_id']) ?>
                                        </small>
                                    </td>
                                    <td>"<?= htmlspecialchars($row['content']) ?>"</td>
                                    <!--STARS RATING-->
                                    <td class="text-center" style="color: #ffc107; white-space: nowrap;">
                                        <?php 
                                            $rating = (int)$row['rating'];
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $rating) {
                                                    echo '<i class="bi bi-star-fill"></i>';
                                                } else {
                                                    echo '<i class="bi bi-star"></i>';
                                                }
                                            }
                                        ?>
                                    </td>   
                                    <!--TESTIOMIAL STATUS-->
                                    <td class="text-center">
                                        <?php 
                                        $status = strtolower($row['status']);
                                        if ($status == 'pending'): ?>
                                            <span class="status-badge bg-warning-subtle text-dark border border-warning">
                                                <i class="bi bi-clock-history me-1"></i> Pending
                                            </span>
                                        <?php elseif ($status == 'approved' || $status == 'published'): ?>
                                            <span class="status-badge bg-success-subtle text-success border border-success">
                                                <i class="bi bi-check-circle me-1"></i> Published
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge bg-danger-subtle text-danger border border-danger">
                                                <i class="bi bi-x-octagon me-1"></i> Rejected
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <!--ACTION BUTTONS-->
                                    <td class="text-center"> <!-- Changed from text-end to text-center -->
                                        <form method="POST" action="" class="d-inline-flex justify-content-center gap-2">
                                            <input type="hidden" name="feedback_id" value="<?= $row['student_pk']; ?>">

                                            <?php if ($row['is_featured'] == 0): ?>
                                                <button type="submit" name="action" value="feature" class="btn btn-sm btn-primary shadow-sm">
                                                    <i class="bi bi-megaphone-fill"></i> 
                                                    <span class="d-none d-xl-inline">Post to Landing</span>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" name="action" value="unfeature" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-archive-fill"></i> 
                                                    <span class="d-none d-xl-inline">Remove</span>
                                                </button>
                                            <?php endif; ?>

                                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Delete permanently?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile ?>
                        <?php else : ?>      
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted"> Currently no Testimonials...</td> <!-- Fixed colspan to 5 -->
                            </tr>
                        <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination Section -->
            <div class="card-footer bg-light border-0 py-3">
                <nav>
                    <ul class="pagination pagination-sm justify-content-center m-0 shadow-sm">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>