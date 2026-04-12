<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/studentData.php");
include("../config/database.php");
include("../action/Data_count.php");

// notification controller
$course = isset($student['course']) ? strtolower($student['course']) : 'all';

$query = "SELECT id FROM announcements 
          WHERE is_active = 1 
          AND (target_audience = 'all' OR target_audience = ?) 
          ORDER BY id DESC LIMIT 1";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $course);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$latest = mysqli_fetch_assoc($res);
$latest_id = $latest['id'] ?? 0;

// get posts/announcements
$posts = getPost($conn, 1);

// 1. Pagination Settings
$limit = 6; // Number of posts per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 2. Capture Filters
$date = $_GET['date'] ?? '';
$priority = $_GET['priority'] ?? '';

// --- COUNT TOTAL RESULTS (For pagination math) ---
$countQuery = "SELECT COUNT(*) as total FROM announcements WHERE 1=1";
$filterParams = [];
$filterTypes = "";

if (!empty($date)) {
    $countQuery .= " AND DATE(date_posted) = ?";
    $filterParams[] = $date;
    $filterTypes .= "s";
}
if (!empty($priority)) {
    $countQuery .= " AND priority = ?";
    $filterParams[] = $priority;
    $filterTypes .= "s";
}

$stmtCount = $conn->prepare($countQuery);
if (!empty($filterParams)) {
    $stmtCount->bind_param($filterTypes, ...$filterParams);
}
$stmtCount->execute();
$totalRecords = $stmtCount->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// --- FETCH DATA WITH LIMIT & OFFSET ---
$query = "SELECT * FROM announcements WHERE 1=1";
if (!empty($date)) $query .= " AND DATE(date_posted) = ?";
if (!empty($priority)) $query .= " AND priority = ?";

$query .= " ORDER BY date_posted DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);

// Combine filter params with pagination params
$allParams = $filterParams;
$allParams[] = $limit;
$allParams[] = $offset;
$allTypes = $filterTypes . "ii";

$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | SIT-IN System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* Subtle Backgrounds for Badges */
        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1);
        }

        /* Card Hover Animations */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
        }

        /* NEW Tag Pulse Animation */
        @keyframes pulse-new {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-pulse {
            animation: pulse-new 2s infinite;
        }

        .btn-white {
            background: white;
            border: 1px solid #dee2e6;
        }

        .btn-white:hover {
            background: #f8f9fa;
        }
    </style>
</head>

<body>
    <!--HEADER-->
    <?php include("../includes/studentHeader.php"); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-10">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-megaphone-fill text-primary me-2"></i>Campus Announcements
                        </h5>
                        <small class="text-muted">Stay updated with the latest news from CCS Admin</small>
                    </div>
                </div>
                <!--FILTER POSTS-->
                <form method="GET" action=""
                    class="row g-3 mb-5 align-items-end bg-white p-3 rounded-4 shadow-sm mx-0 border">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted text-uppercase">Filter by Date</label>
                        <input type="date" name="date" class="form-control border rounded-3 bg-light"
                            value="<?php echo htmlspecialchars($date); ?>">
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted text-uppercase">Priority</label>
                        <select name="priority" class="form-select border rounded-3 bg-light">
                            <option value="">All Types</option>
                            <option value="urgent" <?php echo ($priority=='urgent' ) ? 'selected' : '' ; ?>>Urgent
                            </option>
                            <option value="academic" <?php echo ($priority=='academic' ) ? 'selected' : '' ; ?>>Academic
                            </option>
                            <option value="general" <?php echo ($priority=='general' ) ? 'selected' : '' ; ?>>General
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-3 shadow-sm">
                            Apply
                        </button>

                        <?php if (!empty($date) || !empty($priority)): ?>
                        <a href="?" class="btn btn-outline-secondary rounded-3" title="Clear Filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): 
                        switch ($post['priority']) {
                            case 'urgent': 
                                $color = 'danger'; $label = 'URGENT'; $icon = 'bi-exclamation-circle'; 
                                break;
                            case 'academic': 
                                $color = 'primary'; $label = 'ACADEMIC'; $icon = 'bi-book'; 
                                break;
                            default: 
                                $color = 'success'; $label = 'GENERAL'; $icon = 'bi-info-circle'; 
                                break;
                        }
                    ?>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-4 position-relative overflow-hidden transition-hover">
                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                <span id="blinker-<?php echo $post['id']; ?>" class="notification-blinker d-none"></span>
                                <span
                                    class="badge bg-<?php echo $color; ?>-subtle text-<?php echo $color; ?> rounded-pill px-3 py-2 small fw-bold">
                                    <i class="bi <?php echo $icon; ?> me-1"></i>
                                    <?php echo $label; ?>
                                </span>
                            </div>

                            <div class="card-body px-4">
                                <h6 class="fw-bold text-dark mb-2 mt-2"
                                    style="height: 2.5rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </h6>
                                <p class="text-muted mb-0 small"
                                    style="height: 4.5rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical;">
                                    <?php echo htmlspecialchars($post['message']); ?>
                                </p>
                            </div>

                            <div class="card-footer bg-white border-0 px-4 pb-4">
                                <hr class="opacity-25 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?php echo date("M j, Y", strtotime($post['date_posted'])); ?>
                                    </small>
                                    <button class="btn btn-link btn-sm text-<?php echo $color; ?> p-0 fw-bold text-decoration-none" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modal<?php echo $post['id']; ?>"
                                            onclick="markAsRead(<?php echo $post['id']; ?>)">
                                            Read More <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--READ MORE MODAL-->
                    <div class="modal fade" id="modal<?php echo $post['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

                                <div class="bg-<?php echo $color; ?>" style="height: 6px;"></div>

                                <div class="modal-header border-0 pt-4 px-4 pb-2">
                                    <span
                                        class="badge bg-<?php echo $color; ?>-subtle text-<?php echo $color; ?> rounded-pill px-3 py-2 small fw-bold">
                                        <i class="bi <?php echo $icon; ?> me-1"></i>
                                        <?php echo $label; ?>
                                    </span>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body px-4 pt-0 pb-4">
                                    <h3 class="fw-bold text-dark mb-1">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </h3>
                                    <p class="text-muted small mb-4">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Published on
                                        <?php echo date("F j, Y \a\\t g:i a", strtotime($post['date_posted'])); ?>
                                    </p>
                                    <!--POST MESSAGE-->
                                    <div class="p-4 rounded-4 bg-light text-dark shadow-sm mb-4"
                                        style="line-height: 1.8; white-space: pre-line; font-size: 1.05rem; letter-spacing: 0.01rem; text-align: left;">
                                        <?php echo nl2br(htmlspecialchars(trim($post['message']))); ?>
                                    </div>

                                    <div class="d-flex align-items-center p-3 rounded-3 border border-dashed shadow-sm">
                                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                            style="width: 45px; height: 45px;">
                                            <img src='../assets/ccsmainlogo2.png' alt="CCS Logo"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark">CCS ADMIN</h6>
                                            <small class="text-muted">University of Cebu Main Campus</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-0 bg-light-subtle px-4">
                                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                                        data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="col-12 w-100">
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                            <i class="bi bi-megaphone text-light display-1"></i>
                            <p class="text-muted mt-3">No announcements found matching your filters.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!--POSTS PAGINATION-->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation" class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link border-0 shadow-sm rounded-pill px-3 me-2"
                    href="?page=<?php echo $page - 1; ?>&date=<?php echo $date; ?>&priority=<?php echo $priority; ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item mx-1">
                <a class="page-link border-0 shadow-sm rounded-circle <?php echo ($page == $i) ? 'bg-primary text-white' : 'text-dark'; ?>"
                    style="width: 40px; height: 40px; text-align: center;"
                    href="?page=<?php echo $i; ?>&date=<?php echo $date; ?>&priority=<?php echo $priority; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>

            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link border-0 shadow-sm rounded-pill px-3 ms-2"
                    href="?page=<?php echo $page + 1; ?>&date=<?php echo $date; ?>&priority=<?php echo $priority; ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

    <style>
        .transition-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .transition-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }

        /* for notification blinker for new post(card) */
        .notification-blinker {
            width: 12px;
            height: 12px;
            background-color: #dc3545; /* Bootstrap Red */
            border-radius: 50%;
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 10;
            border: 2px solid white; /* Makes it pop against the card */
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.5);
            animation: blinker-pulse 1.5s infinite;
        }

        @keyframes blinker-pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!--notification controller-->
    <script>
        // 1. Logic to show/hide blinkers based on what's in storage
        document.addEventListener("DOMContentLoaded", function() {
            // Get the highest ID previously marked as read
            const lastReadId = parseInt(localStorage.getItem('lastReadId')) || 0;

            // Select all blinkers and show them ONLY if the post ID is newer than lastReadId
            document.querySelectorAll('.notification-blinker').forEach(blinker => {
                const postId = parseInt(blinker.id.replace('blinker-', ''));
                
                if (postId > lastReadId) {
                    blinker.classList.remove('d-none');
                }
            });
        });

        // 2. Function called when "Read More" is clicked
        function markAsRead(postId) {
            // Hide the blinker immediately for better UX
            const blinker = document.getElementById('blinker-' + postId);
            if (blinker) {
                blinker.classList.add('d-none');
            }

            // Update localStorage so this (and older posts) don't blink on refresh
            const lastReadId = parseInt(localStorage.getItem('lastReadId')) || 0;
            if (postId > lastReadId) {
                localStorage.setItem('lastReadId', postId);
                console.log("Announcement " + postId + " marked as latest read.");
            }
        }
    </script>
</body>

</html>