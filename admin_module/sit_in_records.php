<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/database.php');
include('../action/sit_in.php');

$selected_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : null;

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch data using procedural function
$history_list = past_sit_in($conn, $selected_date, $limit, $offset);
$total_rows = mysqli_num_rows(past_sit_in($conn, $selected_date)); 
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sit-in History | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .card { border-radius: 15px; border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .table thead th { 
            background-color: #f8f9fa; 
            color: #6c757d; 
            text-transform: uppercase; 
            font-size: 0.75rem; 
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }
        .duration-badge { 
            background-color: #e7f1ff; 
            color: #0d6efd; 
            font-weight: 600; 
            padding: 5px 12px; 
            border-radius: 8px;
            font-size: 0.85rem;
        }
        .date-badge {
            background-color: #f8f9fa;
            color: #212529;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <?php include("../includes/adminHeader.php"); ?>

    <main class="container py-3">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark">Sit-in History</h2>
                <p class="text-muted small mb-0">Reviewing past lab sessions and student attendance.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="bg-white d-inline-flex p-2 rounded-3 shadow-sm border px-3">
                    <span class="text-muted small me-2">Total Logs:</span>
                    <span class="fw-bold text-primary"><?php echo $total_rows; ?></span>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Filter by Date</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" name="filter_date" class="form-control border-start-0" value="<?php echo htmlspecialchars($selected_date); ?>">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Apply Filter</button>
                        <?php if($selected_date): ?>
                            <a href="sit_in_records.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 ms-2">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3">Student Details</th>
                            <th class="py-3 text-center">Lab Location</th>
                            <th class="py-3 text-center">Session Date</th>
                            <th class="py-3 text-center">Time Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($total_rows > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($history_list)): 
                                $login_ts = strtotime($row['login_time']);
                                $logout_ts = strtotime($row['logout_time']);
                                $seconds = $logout_ts - $login_ts;
                                $hours = floor($seconds / 3600);
                                $minutes = floor(($seconds / 60) % 60);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold" style="width: 38px; height: 38px; font-size: 0.8rem;">
                                            <?php echo strtoupper(substr($row['fullname'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['fullname']); ?></div>
                                            <div class="text-muted" style="font-size: 0.8rem;"><?php echo htmlspecialchars($row['student_id_str']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-dark-subtle text-dark px-3 rounded-pill fw-medium">Lab <?php echo htmlspecialchars($row['lab']); ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="fw-medium"><?php echo date('M d, Y', $login_ts); ?></div>
                                    <small class="text-muted" style="font-size: 0.75rem;"><?php echo date('h:i A', $login_ts) . ' - ' . date('h:i A', $logout_ts); ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="duration-badge">
                                        <i class="bi bi-clock-history me-1"></i> <?php echo "$hours"."h "."$minutes"."m"; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-folder-x" style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0">No attendance logs found for this criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination pagination-sm justify-content-center">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-sm" href="?page=<?php echo $page - 1; ?>&filter_date=<?php echo $selected_date; ?>"><i class="bi bi-chevron-left"></i></a>
                </li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link shadow-sm" href="?page=<?php echo $i; ?>&filter_date=<?php echo $selected_date; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-sm" href="?page=<?php echo $page + 1; ?>&filter_date=<?php echo $selected_date; ?>"><i class="bi bi-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>