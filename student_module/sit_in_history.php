<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
}

include("../action/studentData.php");
include("../action/Data_count.php");

$student_pk=isset($student['id']) ? $student['id'] : 0;
$limit=5;
$page=isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page<1){
    $page=1;
}

$date_filter=$_GET['date'] ?? '';
$total_records=getTotalRecords($conn,$student_pk,$date_filter);
$total_pages=ceil($total_records/$limit);

if($page>$total_pages && $total_pages>0){
    $page=$total_pages;
}

$start_from=($page-1)*$limit;

// Fetch paginated history records
$history=getStudentHistory($conn,$student_pk,$date_filter,$start_from,$limit);

$sql="SELECT sit_ins FROM students WHERE student_id=?";
$getData=mysqli_prepare($conn,$sql);

// Fixed potential undefined variable notice by falling back safely
$bind_student_id = $student_id ?? ($student['student_id'] ?? '');
mysqli_stmt_bind_param($getData,'s',$bind_student_id);
mysqli_stmt_execute($getData);

$result=mysqli_stmt_get_result($getData);
$row=mysqli_fetch_assoc($result);

$currentSession=$row['sit_ins'] ?? 0;

$max_sessions=30;
$used_sessions=$total_records;
$remaining=max(0,$max_sessions-$used_sessions);
$percentage=($used_sessions/$max_sessions)*100;

// Dynamic global metric aggregation query for accurate calculation regardless of pagination limits
$hours_sql = "SELECT SUM(TIMESTAMPDIFF(SECOND, login_time, logout_time) / 3600) as total_accumulated_hours 
              FROM sit_in_records 
              WHERE student_pk_id = ? AND logout_time IS NOT NULL";
$hours_stmt = mysqli_prepare($conn, $hours_sql);
mysqli_stmt_bind_param($hours_stmt, "i", $student_pk);
mysqli_stmt_execute($hours_stmt);
$hours_res = mysqli_stmt_get_result($hours_stmt);
$hours_row = mysqli_fetch_assoc($hours_res);

$total_hours = $hours_row['total_accumulated_hours'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Sit-in History | Student Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
    :root{
        --primary:#0d6efd;
        --dark:#0046af;
    }

    .main-content{
        margin-left:260px;
        padding:1.5rem;
        transition:.3s ease;
    }

    .sidebar.collapsed ~ .main-content{
        margin-left:80px;
    }

    .hero-card{
        background:linear-gradient(135deg,var(--primary),var(--dark));
        border-radius:24px;
        padding:2rem;
        color:#fff;
    }

    .stats-card{
        border:none;
        border-radius:22px;
        padding:1.5rem;
        box-shadow:0 4px 20px rgba(0,0,0,.03);
        height:100%;
    }

    .history-card{
        border:none;
        border-radius:24px;
        overflow:hidden;
        box-shadow:0 4px 20px rgba(0,0,0,.03);
    }

    .table thead th{
        border:none;
        padding:1rem;
        font-size:.8rem;
        text-transform:uppercase;
    }

    .table tbody td{
        padding:1rem;
        vertical-align:middle;
    }

    .lab-badge{
        background: rgba(13, 110, 253, 0.12);
        color:#0d6efd;
        padding:.45rem .9rem;
        border-radius:30px;
        font-size:.8rem;
        font-weight:600;
    }

    .focus-box{
        padding:.6rem .9rem;
        border-radius:12px;
        display:inline-block;
        font-size:.85rem;
    }

    .badge-soft{
        background: rgba(13, 110, 253, 0.12);
        color:#0d6efd;
        padding:.45rem .9rem;
        border-radius:30px;
        font-size:.75rem;
        font-weight:600;
    }

    .pagination .page-link{
        border:none;
        border-radius:10px;
        color:#0d6efd;
        margin:0 3px;
        background: var(--bs-secondary-bg);
    }

    .pagination .active .page-link{
        background:#0d6efd !important;
        color:#fff !important;
    }
    
    .pagination .disabled .page-link {
        background: var(--bs-tertiary-bg);
        opacity: 0.6;
    }

    .modal-content{
        border:none;
        border-radius:24px;
    }

    .btn-check:checked + .btn-outline-secondary{
        background:#0d6efd!important;
        border-color:#0d6efd!important;
        color:#fff!important;
    }

    @media(max-width:991px){
        .main-content{
            margin-left:0!important;
            padding:1rem;
        }
        .table{
            min-width:1000px;
        }
    }
    </style>
</head>

<body class="bg-body-tertiary text-body">

<?php include("../includes/student_sidebar.php"); ?>

<div class="main-content">
    <div class="container-fluid">

        <div class="hero-card mb-4 shadow-sm">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-2">My Sit-in History</h2>
                    <p class="opacity-75 mb-0">Monitor your laboratory sessions, point allocations, and activities.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <div class="fs-1 fw-bold"><?= $currentSession ?></div>
                    <div class="opacity-75">Remaining Sessions</div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stats-card bg-body border border-light-subtle">
                    <small class="text-secondary fw-bold text-uppercase">Sessions Used</small>
                    <h2 class="fw-bold text-body mt-2"><?= $used_sessions ?></h2>
                    <div class="progress mt-4" style="height:8px;border-radius:20px; background-color: var(--bs-secondary-bg);">
                        <div class="progress-bar" style="width:<?= $percentage ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stats-card bg-body border border-light-subtle">
                    <small class="text-secondary fw-bold text-uppercase">Total Hours</small>
                    <h2 class="fw-bold text-body mt-2"><?= number_format($total_hours,1) ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stats-card bg-body border border-light-subtle">
                    <small class="text-secondary fw-bold text-uppercase">Remaining</small>
                    <h2 class="fw-bold mt-2 text-success"><?= $remaining ?></h2>
                </div>
            </div>
        </div>

        <div class="history-card bg-body border border-light-subtle">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-4 border-bottom border-light-subtle">
                <div>
                    <h5 class="fw-bold text-body mb-1">Recent Activity</h5>
                    <div class="text-secondary small"><?= $total_records ?> total records</div>
                </div>

                <form method="GET" class="d-flex gap-2">
                    <input type="date" name="date" class="form-control bg-body text-body border-secondary-subtle" value="<?= htmlspecialchars($date_filter); ?>">
                    <button class="btn btn-primary"><i class="bi bi-funnel"></i></button>
                    <a href="sit_in_history.php" class="btn btn-outline-secondary">Reset</a>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark-subtle">
                        <tr class="bg-body-tertiary">
                            <th class="text-secondary bg-body-tertiary">Date</th>
                            <th class="text-secondary bg-body-tertiary">Laboratory</th>
                            <th class="text-secondary bg-body-tertiary">Focus</th>
                            <th class="text-secondary bg-body-tertiary">Duration</th>
                            <th class="text-center text-secondary bg-body-tertiary">Task Status</th>
                            <th class="text-center text-secondary bg-body-tertiary">Behavior</th>
                            <th class="text-end text-secondary bg-body-tertiary">Points</th>
                            <th class="text-center text-secondary bg-body-tertiary">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($history)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-clock-history display-5 text-secondary opacity-25"></i>
                                    <div class="mt-3 text-secondary">No records found.</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($history as $record): ?>
                            <tr>
                                <td class="text-body border-light-subtle">
                                    <div class="fw-semibold"><?= date('M d, Y',strtotime($record['login_time'])); ?></div>
                                    <div class="small text-secondary"><?= date('h:i A',strtotime($record['login_time'])); ?></div>
                                </td>

                                <td class="border-light-subtle">
                                    <span class="lab-badge"><?= htmlspecialchars($record['lab'] ?? 'N/A'); ?></span>
                                </td>

                                <td class="border-light-subtle">
                                    <div class="focus-box bg-body-tertiary text-body border border-light-subtle">
                                        <?= htmlspecialchars($record['language'] ?? 'General Lab'); ?>
                                    </div>
                                </td>

                                <td class="border-light-subtle">
                                    <?php if($record['logout_time']): ?>
                                        <span class="badge-soft"><?= number_format($record['duration_hours'],1); ?> Hours</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Ongoing</span>
                                    <?php endif; ?>
                                </td>

                                <!-- FIXED: Case-insensitive handling + auto-resolve on logout -->
                                <td class="text-center border-light-subtle">
                                    <?php if(!$record['logout_time']): ?>
                                        <span class="badge bg-light text-secondary rounded-pill border border-light-subtle px-3 py-2">Active Session</span>
                                    <?php elseif(isset($record['task_status']) && strcasecmp(trim($record['task_status']), 'Completed') === 0): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle-fill me-1"></i>Completed
                                        </span>
                                    <?php else: ?>
                                        <!-- If logged out but status isn't explicitly 'Completed', display custom status safely or fall back to settled layout -->
                                        <span class="badge bg-danger-subtle text-danger border border-success-subtle rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle-fill me-1"></i>Unfinished
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center border-light-subtle fw-bold font-monospace text-muted">
                                    <?php if($record['logout_time']): ?>
                                        <?= isset($record['behavior_score']) ? $record['behavior_score'] . ' / 10' : '10 / 10'; ?>
                                    <?php else: ?>
                                        <span class="text-secondary opacity-50">—</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end border-light-subtle fw-bold font-monospace text-primary">
                                    <?php if($record['logout_time']): ?>
                                        +<?= isset($record['points_earned_this_session']) ? number_format($record['points_earned_this_session'], 2) : '0.00'; ?>
                                    <?php else: ?>
                                        <span class="text-secondary opacity-50 small font-sans-serif">Pending</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center border-light-subtle">
                                    <?php if($record['feedback_submitted']): ?>
                                        <button class="btn btn-secondary btn-sm rounded-pill px-3" disabled>
                                            <i class="bi bi-check-all me-1"></i>Sent
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3"
                                        data-bs-toggle="modal"
                                        data-bs-target="#feedbackModal"
                                        data-session-id="<?= $record['id']; ?>">
                                            <i class="bi bi-chat-left-text me-1"></i>Feedback
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($total_pages>1): ?>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-4 border-top border-light-subtle">
                <div class="small text-secondary">Showing page <?= $page ?> of <?= $total_pages ?></div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($page<=1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?= $page-1 ?>&date=<?= urlencode($date_filter); ?>">Previous</a>
                        </li>
                        <?php for($i=1;$i<=$total_pages;$i++): ?>
                            <li class="page-item <?= ($i==$page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?= $i ?>&date=<?= urlencode($date_filter); ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page>=$total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?= $page+1 ?>&date=<?= urlencode($date_filter); ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- FEEDBACK MODAL -->
<div class="modal fade" id="feedbackModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-body text-body shadow-lg border border-light-subtle">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Submit Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form action="../action/student_feedback.php" method="POST">
                    <input type="hidden" name="session_id" id="modal_session_id">
                    <label class="form-label small fw-bold text-uppercase text-secondary">Category</label>
                    <div class="d-flex gap-2 mb-4 flex-wrap">
                        <input type="radio" class="btn-check" name="category" id="cat1" value="Hardware" checked>
                        <label class="btn btn-outline-secondary btn-sm rounded-pill px-3" for="cat1">Hardware</label>

                        <input type="radio" class="btn-check" name="category" id="cat2" value="Software">
                        <label class="btn btn-outline-secondary btn-sm rounded-pill px-3" for="cat2">Software</label>

                        <input type="radio" class="btn-check" name="category" id="cat3" value="Environment">
                        <label class="btn btn-outline-secondary btn-sm rounded-pill px-3" for="cat3">Environment</label>
                    </div>
                    <textarea class="form-control bg-body-tertiary text-body border-secondary-subtle rounded-4 mb-3" name="feedback_text" rows="4" placeholder="Report issues or suggestions..."></textarea>
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Submit Feedback</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const feedbackModal=document.getElementById('feedbackModal');
if(feedbackModal){
    feedbackModal.addEventListener('show.bs.modal',event=>{
        const button=event.relatedTarget;
        const sessionId=button.getAttribute('data-session-id');
        const modalInput=feedbackModal.querySelector('#modal_session_id');
        modalInput.value=sessionId;
    });
}
</script>
</body>
</html>