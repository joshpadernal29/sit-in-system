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
        /* ================= BASE & DARK MODE ADAPTATION ================= */
        body {
            margin: 0;
            background: var(--bg-body); 
            color: var(--text-main);    
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background 0.3s, color 0.3s;
        }

        /* LIGHT MODE THEME MAP VARIABLES */
        body:not([data-theme="dark"]) {
            --bg-body: #f5f7fb;
            --text-main: #212529;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --bg-sidebar: #ffffff;
            --bg-card: #f8f9fa;
        }

        /* DARK MODE THEME MAP VARIABLES */
        body[data-theme="dark"] {
            --bg-body: #121212;
            --text-main: #e0e0e0;
            --text-muted: #a0a0a0;
            --border-color: #333333;
            --bg-sidebar: #1e1e1e;
            --bg-card: #252525;
        }

        /* ================= MAIN CONTENT LAYOUT ================= */
        .main-content { 
            margin-left: 260px; 
            padding: 2rem; 
            transition: margin-left .3s ease; 
        }
        
        .sidebar.collapsed ~ .main-content { margin-left: 80px; }

        /* ================= UI COMPONENTS ================= */
        .feedback-card { 
            border-radius: 16px; 
            border: 1px solid var(--border-color); 
            background: var(--bg-sidebar); 
            overflow: hidden; 
        }
        
        .table {
            color: var(--text-main);
        }

        /* Force table cells to be transparent so they don't stay white */
        .table > :not(caption) > * > * {
            background-color: transparent !important;
            color: inherit;
            border-bottom-color: var(--border-color);
        }

        .table thead th { 
            background: var(--bg-card) !important; 
            color: var(--text-muted); 
            font-size: 0.75rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            padding: 1.2rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .status-badge { 
            font-size: 0.7rem; 
            padding: 5px 12px; 
            border-radius: 50px; 
            font-weight: 700; 
        }

        .btn-sm {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
        }

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
                <h3 class="fw-bold mb-1">Portal Feedbacks</h3>
                <p class="text-muted small mb-0">Review and moderate student portal testimonials.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="p-2 d-inline-block rounded-3 border shadow-sm target-filter-container" style="background: var(--bg-card); border-color: var(--border-color) !important;">
                    <form action="" method="GET">
                        <span class="text-muted small px-2">Show:</span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary px-3 target-filter-btn" name="all_requests">All</button>
                            <button class="btn btn-outline-secondary px-3 target-filter-btn" name="featured_requests">Featured</button>
                        </div>
                    </form>
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
                                <th>Testimonial</th>
                                <th class="text-center">Rating</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php while($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-semibold text-main"><?= htmlspecialchars($row['fullname']) ?></span> <br>
                                        <small class="text-muted" style="font-size: 0.8rem;">
                                            <?= htmlspecialchars($row['student_id']) ?>
                                        </small>
                                    </td>
                                    <td>"<?= htmlspecialchars($row['content']) ?>"</td>
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
                                    <td class="text-center align-middle">
                                        <?php if ((int)$row['is_featured'] === 1): ?>
                                            <span class="status-badge bg-primary-subtle text-primary border border-primary fw-semibold px-2 py-1 rounded">
                                                <i class="bi bi-star-fill me-1"></i> Featured
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge bg-secondary-subtle text-secondary border border-secondary fw-semibold px-2 py-1 rounded">
                                                <i class="bi bi-eye-slash me-1"></i> Unfeatured
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="" class="d-inline-flex justify-content-center gap-2">
                                            <input type="hidden" name="feedback_id" value="<?= $row['student_pk']; ?>">
                                            <input type="hidden" name="testimonial_id" value="<?= $row['id']; ?>">

                                            <button type="submit" name="action" value="feature" class="btn btn-sm btn-success shadow-sm" title="Mark as Featured">
                                                <i class="bi bi-megaphone-fill"></i> 
                                                <span class="d-none d-xl-inline">Feature</span>
                                            </button>

                                            <button type="submit" name="action" value="unfeature" class="btn btn-sm btn-outline-secondary target-unfeature-btn" title="Remove Feature Status">
                                                <i class="bi bi-archive-fill"></i> 
                                                <span class="d-none d-xl-inline">Unfeature</span>
                                            </button>

                                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-light border text-danger target-delete-btn" title="Delete Permanently" onclick="return confirm('Delete permanently?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile ?>
                        <?php else : ?>      
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted"> Currently no Testimonials...</td>
                            </tr>
                        <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer border-0 py-3" style="background: var(--bg-card);">
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

<!-- LIVE MONITOR SCRIPT: Listens to live theme toggles from your sidebar configuration -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const body = document.body;
        const unfeatureBtns = document.querySelectorAll(".target-unfeature-btn");
        const deleteBtns = document.querySelectorAll(".target-delete-btn");
        const filterBtns = document.querySelectorAll(".target-filter-btn");

        const syncBootstrapTheme = () => {
            if (body.getAttribute("data-theme") === "dark") {
                body.setAttribute("data-bs-theme", "dark");
                
                // Adjust contextual buttons for dark background readability
                unfeatureBtns.forEach(btn => {
                    btn.classList.remove("btn-outline-secondary");
                    btn.classList.add("btn-outline-light");
                });
                deleteBtns.forEach(btn => {
                    btn.classList.remove("btn-light", "border");
                    btn.classList.add("btn-outline-danger");
                });
                filterBtns.forEach(btn => {
                    btn.classList.remove("btn-outline-secondary");
                    btn.classList.add("btn-outline-light");
                });
            } else {
                body.setAttribute("data-bs-theme", "light");
                
                // Revert to light mode button themes
                unfeatureBtns.forEach(btn => {
                    btn.classList.remove("btn-outline-light");
                    btn.classList.add("btn-outline-secondary");
                });
                deleteBtns.forEach(btn => {
                    btn.classList.remove("btn-outline-danger");
                    btn.classList.add("btn-light", "border");
                });
                filterBtns.forEach(btn => {
                    btn.classList.remove("btn-outline-light");
                    btn.classList.add("btn-outline-secondary");
                });
            }
        };

        // Initial execution on layout render
        syncBootstrapTheme();

        // Observe attribute mutations coming from sidebar action triggers
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === "data-theme") {
                    syncBootstrapTheme();
                }
            });
        });

        observer.observe(body, { attributes: true });
    });
</script>
</body>
</html>