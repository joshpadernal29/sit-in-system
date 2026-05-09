<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/Data_count.php");

// data (UNCHANGED LOGIC)
$Php = languageUsed($conn, 'PHP');
$Java = languageUsed($conn, 'JAVA');
$C = languageUsed($conn, 'C');
$Csharp = languageUsed($conn, 'C#');
$CPlusPlus = languageUsed($conn, 'C++');

$posts = getPost($conn, 1);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script src="https://www.gstatic.com/charts/loader.js"></script>

<script>
google.charts.load('current', {packages:['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart(){
    var data = google.visualization.arrayToDataTable([
        ['Language','Students'],
        ['C', <?php echo $C ?>],
        ['C#', <?php echo $Csharp ?>],
        ['C++', <?php echo $CPlusPlus ?>],
        ['Java', <?php echo $Java ?>],
        ['PHP', <?php echo $Php ?>]
    ]);

    var options = {
        pieHole: 0.45,
        colors: ['#0d6efd','#6610f2','#6f42c1','#d63384','#fd7e14'],
        legend:{position:'bottom'},
        chartArea:{width:'95%',height:'80%'}
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
}
</script>

<style>

/* ================= BASE ================= */
body{
    margin:0;
    background:#f5f7fb;
    font-family:Segoe UI;
}

/* ================= IMPORTANT: SIDEBAR COMPATIBILITY ================= */
.main-content{
    margin-left:260px;
    padding:1.5rem;
    transition:.3s ease;
}

/* THIS is what makes collapse work */
.sidebar.collapsed ~ .main-content{
    margin-left:80px;
}

/* ================= CARDS ================= */
.card{
    border:0;
    border-radius:14px;
}

/* ================= STATS ================= */
.stat-icon{
    width:48px;
    height:48px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:12px;
}

/* ================= CHART ================= */
.chart-box{
    height:360px;
}

/* ================= ANNOUNCEMENTS ================= */
.announcement-box{
    max-height:420px;
    overflow:auto;
}

/* timeline dot */
.timeline-dot{
    width:10px;
    height:10px;
    border-radius:50%;
}

</style>

</head>

<body>

<?php include("../includes/admin_sidebar.php"); ?>

<!-- ================= MAIN CONTENT ================= -->
<div class="main-content">

    <!-- HEADER -->
    <div class="mb-4">
        <h5 class="fw-bold mb-1">Admin Dashboard</h5>
        <small class="text-muted">Overview of system activity</small>
    </div>

    <!-- STATS -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-people-fill text-primary"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold">Registered</small>
                    <h4 class="mb-0"><?php echo countStudents($conn); ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-person-video3 text-success"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold">Current Sit-in</small>
                    <h4 class="mb-0"><?php echo currentSitIns($conn); ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10">
                    <i class="bi bi-clock-history text-warning"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold">Total Sessions</small>
                    <h4 class="mb-0"><?php echo getTotalSessions($conn); ?></h4>
                </div>
            </div>
        </div>

    </div>

    <!-- CHART + ANNOUNCEMENTS -->
    <div class="row g-4">

        <div class="col-lg-7">
            <div class="card shadow-sm p-3">
                <h6 class="fw-bold mb-3">Programming Preference</h6>
                <div id="piechart" class="chart-box"></div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm p-3">

                <h6 class="fw-bold mb-3">Broadcasts</h6>

                <div class="announcement-box">

                    <?php if(!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>

                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <div class="timeline-dot bg-primary"></div>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo $post['title'] ?></h6>
                                    <small class="text-muted">
                                        <?php echo date("F j, Y . g:i a", strtotime($post['date_posted'])); ?>
                                    </small>
                                    <p class="small text-muted mb-0"><?php echo $post['message'] ?></p>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No announcements</p>
                    <?php endif; ?>

                </div>

            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>