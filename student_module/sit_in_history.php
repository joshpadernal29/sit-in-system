<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
}

include("../action/studentData.php");

function getStudentHistory($conn,$student_pk,$date_filter,$start_from,$limit){

    $history=[];

    $sql="SELECT 
            sr.id,
            sr.student_pk_id,
            sr.lab,
            sr.login_time,
            sr.logout_time,
            sr.language,
            COALESCE(f.id,0) AS feedback_submitted,
            TIMESTAMPDIFF(MINUTE,sr.login_time,sr.logout_time)/60 AS duration_hours
        FROM sit_in_records sr
        LEFT JOIN feedbacks f ON sr.id=f.record_id
        WHERE sr.student_pk_id=?";

    if(!empty($date_filter)){
        $sql.=" AND DATE(sr.login_time)=?";
    }

    $sql.=" ORDER BY sr.login_time DESC LIMIT ?,?";

    $stmt=mysqli_prepare($conn,$sql);

    if($stmt){

        if(!empty($date_filter)){

            mysqli_stmt_bind_param($stmt,'isii',$student_pk,$date_filter,$start_from,$limit);

        }else{

            mysqli_stmt_bind_param($stmt,'iii',$student_pk,$start_from,$limit);

        }

        mysqli_stmt_execute($stmt);

        $result=mysqli_stmt_get_result($stmt);

        while($row=mysqli_fetch_assoc($result)){
            $history[]=$row;
        }

        mysqli_stmt_close($stmt);
    }

    return $history;
}

function getTotalRecords($conn,$student_pk,$date_filter){

    $sql="SELECT COUNT(*) as total 
        FROM sit_in_records 
        WHERE student_pk_id=?";

    if(!empty($date_filter)){
        $sql.=" AND DATE(login_time)=?";
    }

    $stmt=mysqli_prepare($conn,$sql);

    if(!empty($date_filter)){

        mysqli_stmt_bind_param($stmt,'is',$student_pk,$date_filter);

    }else{

        mysqli_stmt_bind_param($stmt,'i',$student_pk);

    }

    mysqli_stmt_execute($stmt);

    $result=mysqli_stmt_get_result($stmt);
    $row=mysqli_fetch_assoc($result);

    return $row['total'];
}

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

$history=getStudentHistory($conn,$student_pk,$date_filter,$start_from,$limit);

$sql="SELECT sit_ins FROM students WHERE student_id=?";
$getData=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($getData,'s',$student_id);
mysqli_stmt_execute($getData);

$result=mysqli_stmt_get_result($getData);
$row=mysqli_fetch_assoc($result);

$currentSession=$row['sit_ins'] ?? 0;

$max_sessions=30;
$used_sessions=$total_records;
$remaining=max(0,$max_sessions-$used_sessions);
$percentage=($used_sessions/$max_sessions)*100;

$total_hours=0;

foreach($history as $h){
    $total_hours+=$h['duration_hours'];
}
?>

<!DOCTYPE html>
<html lang="en">
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
        --bg:#f5f7fb;
    }

    body{
        background:var(--bg);
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
        background:#fff;
        border:none;
        border-radius:22px;
        padding:1.5rem;
        box-shadow:0 4px 20px rgba(0,0,0,.04);
        height:100%;
    }

    .history-card{
        background:#fff;
        border:none;
        border-radius:24px;
        overflow:hidden;
        box-shadow:0 4px 20px rgba(0,0,0,.04);
    }

    .table thead th{
        background:#f8f9fa;
        border:none;
        padding:1rem;
        font-size:.8rem;
        text-transform:uppercase;
        color:#6c757d;
    }

    .table tbody td{
        padding:1rem;
        vertical-align:middle;
        border-color:#f1f3f5;
    }

    .table tbody tr:hover{
        background:#f8fbff;
    }

    .lab-badge{
        background:#eef4ff;
        color:#0d6efd;
        padding:.45rem .9rem;
        border-radius:30px;
        font-size:.8rem;
        font-weight:600;
    }

    .focus-box{
        background:#f8f9fa;
        padding:.6rem .9rem;
        border-radius:12px;
        display:inline-block;
        font-size:.85rem;
    }

    .badge-soft{
        background:#eef4ff;
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
    }

    .pagination .active .page-link{
        background:#0d6efd;
        color:#fff;
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
            min-width:850px;
        }

    }

    </style>

</head>

<body>

<?php include("../includes/student_sidebar.php"); ?>

<div class="main-content">

    <div class="container-fluid">

        <div class="hero-card mb-4">

            <div class="row align-items-center">

                <div class="col-lg-8">

                    <h2 class="fw-bold mb-2">
                        My Sit-in History
                    </h2>

                    <p class="opacity-75 mb-0">
                        Monitor your laboratory sessions and activities.
                    </p>

                </div>

                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

                    <div class="fs-1 fw-bold">
                        <?= $currentSession ?>
                    </div>

                    <div class="opacity-75">
                        Remaining Sessions
                    </div>

                </div>

            </div>

        </div>

        <div class="row g-4 mb-4">

            <div class="col-md-4">

                <div class="stats-card">

                    <small class="text-muted fw-bold text-uppercase">
                        Sessions Used
                    </small>

                    <h2 class="fw-bold mt-2">
                        <?= $used_sessions ?>
                    </h2>

                    <div class="progress mt-4" style="height:8px;border-radius:20px;">
                        <div class="progress-bar" style="width:<?= $percentage ?>%"></div>
                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="stats-card">

                    <small class="text-muted fw-bold text-uppercase">
                        Total Hours
                    </small>

                    <h2 class="fw-bold mt-2">
                        <?= number_format($total_hours,1) ?>
                    </h2>

                </div>

            </div>

            <div class="col-md-4">

                <div class="stats-card">

                    <small class="text-muted fw-bold text-uppercase">
                        Remaining
                    </small>

                    <h2 class="fw-bold mt-2 text-success">
                        <?= $remaining ?>
                    </h2>

                </div>

            </div>

        </div>

        <div class="history-card">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-4 border-bottom">

                <div>

                    <h5 class="fw-bold mb-1">
                        Recent Activity
                    </h5>

                    <div class="text-muted small">
                        <?= $total_records ?> total records
                    </div>

                </div>

                <form method="GET" class="d-flex gap-2">

                    <input type="date"
                    name="date"
                    class="form-control"
                    value="<?= htmlspecialchars($date_filter); ?>">

                    <button class="btn btn-primary">
                        <i class="bi bi-funnel"></i>
                    </button>

                    <a href="sit_in_history.php" class="btn btn-outline-secondary">
                        Reset
                    </a>

                </form>

            </div>

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead>

                        <tr>
                            <th>Date</th>
                            <th>Laboratory</th>
                            <th>Focus</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if(empty($history)): ?>

                            <tr>

                                <td colspan="6" class="text-center py-5">

                                    <i class="bi bi-clock-history display-5 text-muted opacity-25"></i>

                                    <div class="mt-3 text-muted">
                                        No records found.
                                    </div>

                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach($history as $record): ?>

                            <tr>

                                <td>

                                    <div class="fw-semibold">
                                        <?= date('M d, Y',strtotime($record['login_time'])); ?>
                                    </div>

                                    <div class="small text-muted">
                                        <?= date('h:i A',strtotime($record['login_time'])); ?>
                                    </div>

                                </td>

                                <td>

                                    <span class="lab-badge">
                                        <?= htmlspecialchars($record['lab'] ?? 'N/A'); ?>
                                    </span>

                                </td>

                                <td>

                                    <div class="focus-box">
                                        <?= htmlspecialchars($record['language'] ?? 'General Lab'); ?>
                                    </div>

                                </td>

                                <td>

                                    <?php if($record['logout_time']): ?>

                                        <span class="badge-soft">
                                            <?= number_format($record['duration_hours'],1); ?> Hours
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-warning rounded-pill px-3 py-2">
                                            Ongoing
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php if($record['logout_time']): ?>

                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                                            Completed
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2">
                                            Active
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td class="text-center">

                                    <?php if($record['feedback_submitted']): ?>

                                        <button class="btn btn-secondary btn-sm rounded-pill px-3" disabled>
                                            <i class="bi bi-check-all me-1"></i>
                                            Sent
                                        </button>

                                    <?php else: ?>

                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3"
                                        data-bs-toggle="modal"
                                        data-bs-target="#feedbackModal"
                                        data-session-id="<?= $record['id']; ?>">

                                            <i class="bi bi-chat-left-text me-1"></i>
                                            Feedback

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

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-4 border-top">

                <div class="small text-muted">
                    Showing page <?= $page ?> of <?= $total_pages ?>
                </div>

                <nav>

                    <ul class="pagination mb-0">

                        <li class="page-item <?= ($page<=1) ? 'disabled' : ''; ?>">

                            <a class="page-link"
                            href="?page=<?= $page-1 ?>&date=<?= urlencode($date_filter); ?>">
                                Previous
                            </a>

                        </li>

                        <?php for($i=1;$i<=$total_pages;$i++): ?>

                            <li class="page-item <?= ($i==$page) ? 'active' : ''; ?>">

                                <a class="page-link"
                                href="?page=<?= $i ?>&date=<?= urlencode($date_filter); ?>">
                                    <?= $i ?>
                                </a>

                            </li>

                        <?php endfor; ?>

                        <li class="page-item <?= ($page>=$total_pages) ? 'disabled' : ''; ?>">

                            <a class="page-link"
                            href="?page=<?= $page+1 ?>&date=<?= urlencode($date_filter); ?>">
                                Next
                            </a>

                        </li>

                    </ul>

                </nav>

            </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<div class="modal fade" id="feedbackModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content shadow-lg">

            <div class="modal-header border-0 pt-4 px-4">

                <h5 class="fw-bold mb-0">
                    Submit Feedback
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body px-4 pb-4">

                <form action="../action/student_feedback.php" method="POST">

                    <input type="hidden" name="session_id" id="modal_session_id">

                    <label class="form-label small fw-bold text-uppercase text-muted">
                        Category
                    </label>

                    <div class="d-flex gap-2 mb-4 flex-wrap">

                        <input type="radio" class="btn-check" name="category" id="cat1" value="Hardware" checked>
                        <label class="btn btn-outline-secondary btn-sm rounded-pill px-3" for="cat1">
                            Hardware
                        </label>

                        <input type="radio" class="btn-check" name="category" id="cat2" value="Software">
                        <label class="btn btn-outline-secondary btn-sm rounded-pill px-3" for="cat2">
                            Software
                        </label>

                        <input type="radio" class="btn-check" name="category" id="cat3" value="Environment">
                        <label class="btn btn-outline-secondary btn-sm rounded-pill px-3" for="cat3">
                            Environment
                        </label>

                    </div>

                    <textarea class="form-control bg-light border-0 rounded-4 mb-3"
                    name="feedback_text"
                    rows="4"
                    placeholder="Report issues or suggestions..."></textarea>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">
                        Submit Feedback
                    </button>

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