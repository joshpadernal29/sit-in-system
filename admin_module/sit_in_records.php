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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in History | UC Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

    /* ================= PAGE ================= */
    body{
        background:#f1f5f9;
        font-family:Inter, sans-serif;
        color:#1e293b;
    }

    /* ================= MAIN LAYOUT ================= */
    main{
        margin-left:260px;
        padding:2rem;
        transition:.3s ease;
    }

    .sidebar.collapsed ~ main{
        margin-left:85px;
    }

    @media(max-width:991px){
        main{
            margin-left:0 !important;
            padding:1rem;
        }
    }

    /* ================= PAGE HEADER ================= */
    .page-title{
        font-size:1.8rem;
        font-weight:700;
        color:#0f172a;
    }

    .page-subtitle{
        color:#64748b;
        font-size:.95rem;
    }

    /* ================= STATS BOX ================= */
    .stats-box{
        background:#fff;
        border-radius:16px;
        padding:.9rem 1.2rem;

        border:1px solid #e2e8f0;

        display:inline-flex;
        align-items:center;

        box-shadow:
            0 4px 15px rgba(0,0,0,.03);
    }

    /* ================= CARDS ================= */
    .card{
        border:none;
        border-radius:22px;

        background:#fff;

        box-shadow:
            0 8px 24px rgba(15,23,42,.04);

        overflow:hidden;
    }

    /* ================= FILTER ================= */
    .form-label{
        font-size:.75rem;
        letter-spacing:.5px;
    }

    .input-group-text{
        background:#f8fafc;
        border:1px solid #e2e8f0;
    }

    .form-control{
        border:1px solid #e2e8f0;
        box-shadow:none !important;
    }

    .form-control:focus{
        border-color:#2563eb;
    }

    /* ================= BUTTONS ================= */
    .btn-primary{
        background:#2563eb;
        border:none;
        font-weight:600;
    }

    .btn-primary:hover{
        background:#1d4ed8;
    }

    .btn-outline-secondary{
        border-color:#cbd5e1;
    }

    /* ================= TABLE ================= */
    .table{
        margin-bottom:0;
    }

    .table thead th{
        background:#f8fafc;

        color:#64748b;

        text-transform:uppercase;
        font-size:.72rem;
        letter-spacing:.7px;

        border-bottom:1px solid #e2e8f0;

        padding-top:1rem;
        padding-bottom:1rem;
    }

    .table tbody tr{
        transition:.2s ease;
    }

    .table tbody tr:hover{
        background:#f8fafc;
    }

    .table td{
        padding-top:1rem;
        padding-bottom:1rem;
        vertical-align:middle;
        border-color:#f1f5f9;
    }

    /* ================= AVATAR ================= */
    .student-avatar{
        width:42px;
        height:42px;

        border-radius:50%;

        background:linear-gradient(
            135deg,
            #3b82f6,
            #2563eb
        );

        color:white;

        display:flex;
        align-items:center;
        justify-content:center;

        font-size:.85rem;
        font-weight:700;
    }

    /* ================= BADGES ================= */
    .duration-badge{
        background:#eff6ff;
        color:#2563eb;

        font-weight:600;

        padding:7px 14px;

        border-radius:999px;

        font-size:.82rem;
    }

    .lab-badge{
        background:#f1f5f9;
        color:#334155;

        padding:8px 14px;

        border-radius:999px;

        font-weight:600;
        font-size:.82rem;
    }

    /* ================= EMPTY ================= */
    .empty-state i{
        font-size:3rem;
        color:#94a3b8;
    }

    /* ================= PAGINATION ================= */
    .pagination .page-link{
        border:none;

        margin:0 3px;

        border-radius:10px;

        color:#334155;

        box-shadow:
            0 2px 6px rgba(0,0,0,.04);
    }

    .pagination .page-item.active .page-link{
        background:#2563eb;
    }

    .pagination .page-link:hover{
        background:#dbeafe;
        color:#2563eb;
    }

    </style>
</head>

<body>

<?php include("../includes/admin_sidebar.php"); ?>

<main>

    <!-- HEADER -->
    <div class="row align-items-center mb-4">

        <div class="col-md-6">
            <h2 class="page-title">
                Sit-in History
            </h2>

            <p class="page-subtitle mb-0">
                Reviewing past lab sessions and student attendance.
            </p>
        </div>

        <div class="col-md-6 text-md-end mt-3 mt-md-0">

            <div class="stats-box">

                <span class="text-muted small me-2">
                    Total Logs:
                </span>

                <span class="fw-bold text-primary">
                    <?php echo $total_rows; ?>
                </span>

            </div>

        </div>

    </div>

    <!-- FILTER -->
    <div class="card mb-4">

        <div class="card-body">

            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-3">

                    <label class="form-label fw-bold text-muted text-uppercase">
                        Filter by Date
                    </label>

                    <div class="input-group input-group-sm">

                        <span class="input-group-text border-end-0">
                            <i class="bi bi-calendar-event"></i>
                        </span>

                        <input
                            type="date"
                            name="filter_date"
                            class="form-control border-start-0"
                            value="<?php echo htmlspecialchars($selected_date); ?>"
                        >

                    </div>

                </div>

                <div class="col-auto">

                    <button
                        type="submit"
                        class="btn btn-primary btn-sm rounded-pill px-4"
                    >
                        Apply Filter
                    </button>

                    <?php if($selected_date): ?>

                        <a
                            href="sit_in_records.php"
                            class="btn btn-outline-secondary btn-sm rounded-pill px-3 ms-2"
                        >
                            Clear
                        </a>

                    <?php endif; ?>

                </div>

            </form>

        </div>

    </div>

    <!-- TABLE -->
    <div class="card">

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>
                        <th class="ps-4">Student Details</th>
                        <th class="text-center">Lab Location</th>
                        <th class="text-center">Session Date</th>
                        <th class="text-center">Time Duration</th>
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

                        <!-- STUDENT -->
                        <td class="ps-4">

                            <div class="d-flex align-items-center">

                                <div class="student-avatar me-3">
                                    <?php echo strtoupper(substr($row['fullname'], 0, 1)); ?>
                                </div>

                                <div>

                                    <div class="fw-bold text-dark">
                                        <?php echo htmlspecialchars($row['fullname']); ?>
                                    </div>

                                    <div class="text-muted small">
                                        <?php echo htmlspecialchars($row['student_id_str']); ?>
                                    </div>

                                </div>

                            </div>

                        </td>

                        <!-- LAB -->
                        <td class="text-center">

                            <span class="lab-badge">
                                Lab <?php echo htmlspecialchars($row['lab']); ?>
                            </span>

                        </td>

                        <!-- DATE -->
                        <td class="text-center">

                            <div class="fw-semibold">
                                <?php echo date('M d, Y', $login_ts); ?>
                            </div>

                            <small class="text-muted">

                                <?php
                                    echo date('h:i A', $login_ts)
                                    . ' - ' .
                                    date('h:i A', $logout_ts);
                                ?>

                            </small>

                        </td>

                        <!-- DURATION -->
                        <td class="text-center">

                            <span class="duration-badge">

                                <i class="bi bi-clock-history me-1"></i>

                                <?php
                                    echo "$hours"."h "."$minutes"."m";
                                ?>

                            </span>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="4" class="text-center py-5">

                            <div class="empty-state">

                                <i class="bi bi-folder-x"></i>

                                <p class="mt-3 mb-0 text-muted">
                                    No attendance logs found for this criteria.
                                </p>

                            </div>

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- PAGINATION -->
    <?php if($total_pages > 1): ?>

    <nav class="mt-4">

        <ul class="pagination pagination-sm justify-content-center">

            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">

                <a
                    class="page-link"
                    href="?page=<?php echo $page - 1; ?>&filter_date=<?php echo $selected_date; ?>"
                >
                    <i class="bi bi-chevron-left"></i>
                </a>

            </li>

            <?php for($i = 1; $i <= $total_pages; $i++): ?>

                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">

                    <a
                        class="page-link"
                        href="?page=<?php echo $i; ?>&filter_date=<?php echo $selected_date; ?>"
                    >
                        <?php echo $i; ?>
                    </a>

                </li>

            <?php endfor; ?>

            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&filter_date=<?php echo $selected_date; ?>" >
                    <i class="bi bi-chevron-right"></i>
                </a>

            </li>

        </ul>

    </nav>

    <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>