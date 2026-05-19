<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/studentData.php");
include("../action/Data_count.php");

// get language used data
$language_used = progLanguage($conn,$student_id);

// prepare pie chart data (ignoring case-insensitive N/A and blank values)
$rows = [];
foreach ($language_used as $row) {
    $trimmed_lang = strtoupper(trim($row['language']));
    if ($trimmed_lang !== 'N/A' && $trimmed_lang !== '') {
        $rows[] = "['" . htmlspecialchars($row['language'], ENT_QUOTES) . "', " . $row['language_count'] . "]";
    }
}
$chartDataString = implode(',', $rows);

// get sit in data for line chart
$sit_in_rate = sit_in_rate($conn,$student_id);

$data = [['Date', 'Sessions']];

// Check if we actually got records back from the function
if (!empty($sit_in_rate)) {
    foreach ($sit_in_rate as $row) {
        $data[] = [(string)$row['sit_in_date'], (int)$row['sit_in_rate']];
    }
} else {
    // SAFEGUARD: If it's a new student with 0 records, inject a baseline zero row.
    $data[] = [date('Y-m-d'), 0];
}

$jsonTable = json_encode($data);

// total hours 
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
$total_hours=0;

foreach($history as $h){
    $total_hours+=$h['duration_hours'];
}

// ================= GAMIFIED REWARD CALCULATIONS =================
// Fetch dynamic point variables safely from your updated student row reference array
$accumulated_points = isset($student['accumulated_points']) ? floatval($student['accumulated_points']) : 0.0;
$sessions_earned    = isset($student['sessions_earned']) ? intval($student['sessions_earned']) : 0;

// Milestone targets: compute remaining points needed to hit the next 50-point credit tier
$points_per_milestone = 50;
$current_tier_progress = fmod($accumulated_points, $points_per_milestone);
$progress_percentage   = ($current_tier_progress / $points_per_milestone) * 100;
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<title>Dashboard | Student Portal</title>

<!-- Google Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
    // Detect theme status dynamically
    const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const textColor = isDarkMode ? '#f8f9fa' : '#212529';
    const gridColor = isDarkMode ? '#444' : '#dee2e6';

    // LINE CHART
    var lineData = google.visualization.arrayToDataTable(
        <?= $jsonTable ?>
    );

    var lineOptions = {
        curveType: 'function',
        legend: { position: 'none' },
        colors: ['#0d6efd'],
        chartArea: { width: '85%', height: '80%' },
        vAxis: { 
            minValue: 0,
            textStyle: { color: textColor },
            gridlines: { color: gridColor }
        },
        hAxis: {
            textStyle: { color: textColor },
            gridlines: { color: gridColor }
        },
        backgroundColor: 'transparent'
    };

    var lineChart = new google.visualization.LineChart(document.getElementById('line_chart_div'));
    lineChart.draw(lineData, lineOptions);

    // PIE CHART
    var pieData = google.visualization.arrayToDataTable([
        ['Language', 'Usage'],
        <?= $chartDataString ?>
    ]);

    var pieOptions = {
        pieHole: 0.55,
        colors: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#fd7e14'],
        chartArea: { width: '95%', height: '85%' },
        legend: { 
            position: 'bottom',
            textStyle: { color: textColor }
        },
        pieSliceText: 'none',
        backgroundColor: 'transparent'
    };

    var pieChart = new google.visualization.PieChart(document.getElementById('pie_chart_div'));
    pieChart.draw(pieData, pieOptions);
}

// Watch theme engine shifts to redraw chart parameters instantly
const observer = new MutationObserver(() => {
    if (typeof drawCharts === 'function') drawCharts();
});
observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
</script>

<style>

/* ================= MAIN LAYOUT (SIDEBAR COMPATIBLE) ================= */
.main-content{
    margin-left:260px;
    padding:1.5rem;
    transition: margin-left .3s ease;
}

/* sidebar collapsed support */
.sidebar.collapsed ~ .main-content{
    margin-left:80px;
}

/* ================= KPI CARDS ================= */
.kpi-card{
    border-radius:14px;
    padding:18px;
    box-shadow:0 6px 20px rgba(0,0,0,.04);
    min-height:105px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.kpi-label{
    font-size:.75rem;
    text-transform:uppercase;
    font-weight:600;
}

.kpi-value{
    font-size:1.7rem;
    font-weight:700;
}

/* ================= CHART CARDS ================= */
.chart-card{
    border-radius:14px;
    box-shadow:0 6px 20px rgba(0,0,0,.04);
    padding:1rem;
}

.chart-title{
    font-weight:600;
    margin-bottom:.5rem;
}

/* chart size */
.chart-box{
    height:340px;
}

/* ================= RULES ================= */
.rules-column{
    border-radius:14px;
    box-shadow:0 6px 20px rgba(0,0,0,.04);
    padding:1rem;
}

.rule-card{
    border-left:4px solid #0d6efd;
    padding:10px;
    border-radius:8px;
    font-size:.85rem;
    margin-bottom:10px;
}

[data-bs-theme="dark"] .policy-logo {
    filter: brightness(1.2) drop-shadow(0px 0px 8px rgba(255, 255, 255, 0.25));
}

/* ================= RESPONSIVE ================= */
@media(max-width:991px){
    .main-content{
        margin-left:0 !important;
    }

    .rules-column{
        margin-top:1rem;
    }
}

</style>

</head>

<body class="bg-body-tertiary text-body">

<?php include("../includes/student_sidebar.php"); ?>

<!-- ================= MAIN CONTENT ================= -->
<main class="main-content">

    <!-- PAGE TITLE -->
    <div class="mb-4">
        <h4 class="fw-bold text-body mb-0">Student Dashboard</h4>
        <small class="text-secondary">Overview of your laboratory activity and rewards balance</small>
    </div>

    <!-- REVISIONS INCLUDED: DYNAMIC ACCUMULATED REWARD POOLS AND MILESTONES -->
    <div class="row g-4 mb-4">
        
        <!-- 1. Accumulated Performance Balance Points Card -->
        <div class="col-md-6 col-xl-3">
            <div class="kpi-card bg-body border border-light-subtle">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="kpi-label text-secondary">Performance Points</div>
                    <i class="bi bi-award text-primary fs-5"></i>
                </div>
                <div class="kpi-value text-body mt-1"><?= number_format($accumulated_points, 2); ?></div>
                
                <!-- Dynamic progress metric to visually track milestones -->
                <div class="mt-2">
                    <div class="d-flex justify-content-between text-muted mb-1" style="font-size: 0.65rem;">
                        <span>Next Bonus Tier</span>
                        <span class="font-monospace fw-bold"><?= round($current_tier_progress, 1); ?> / 50</span>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $progress_percentage; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Earned Bonus Sessions Card -->
        <div class="col-md-6 col-xl-3">
            <div class="kpi-card bg-body border border-light-subtle">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="kpi-label text-secondary">Bonus Unlocked</div>
                    <i class="bi bi-gift text-success fs-5"></i>
                </div>
                <div class="kpi-value text-success mt-1">+<?= $sessions_earned; ?></div>
                <small class="text-muted text-truncate" style="font-size:0.65rem; margin-top: 4px;">Sessions earned from actions</small>
            </div>
        </div>

        <!-- 3. Dynamic Remaining Sessions Allotment Card -->
        <div class="col-md-6 col-xl-3">
            <div class="kpi-card bg-body border border-light-subtle">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="kpi-label text-secondary">Remaining Sessions</div>
                    <i class="bi bi-cpu text-info fs-5"></i>
                </div>
                <!-- Displays the live active sessions column value from your database -->
                <div class="kpi-value text-primary mt-1"><?= htmlspecialchars($student['sit_ins']); ?></div>
                <small class="text-muted text-truncate" style="font-size:0.65rem; margin-top: 4px;">Includes unlocked reward offsets</small>
            </div>
        </div>

        <!-- 4. Total Accumulated Lab Hours Card -->
        <div class="col-md-6 col-xl-3">
            <div class="kpi-card bg-body border border-light-subtle">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="kpi-label text-secondary">Total Lab Hours</div>
                    <i class="bi bi-hourglass-split text-secondary fs-5"></i>
                </div>
                <div class="kpi-value text-body mt-1"><?= number_format($total_hours, 1) ?></div>
                <small class="text-muted text-truncate" style="font-size:0.65rem; margin-top: 4px;">Active track duration summaries</small>
            </div>
        </div>

    </div>

    <div class="row g-4">

        <!-- LEFT CONTENT (CHARTS) -->
        <div class="col-lg-9">
            <div class="row g-4">

                <div class="col-md-7">
                    <div class="chart-card bg-body border border-light-subtle">
                        <div class="chart-title text-body">
                            <i class="bi bi-graph-up me-1 text-primary"></i> Sit-in Activity
                        </div>
                        <div id="line_chart_div" class="chart-box"></div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="chart-card bg-body border border-light-subtle">
                        <div class="chart-title text-body">
                            <i class="bi bi-pie-chart-fill me-1 text-primary"></i> Programming Focus
                        </div>
                        <div id="pie_chart_div" class="chart-box"></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- RIGHT RULES -->
        <div class="col-lg-3">
            <div class="rules-column bg-body border border-light-subtle">

                <div class="text-center mb-3">
                    <img src="../assets/ccsmainlogo2.png" width="45" class="policy-logo" alt="Logo">
                    <h6 class="fw-bold text-primary mt-2 mb-0">Laboratory Policies</h6>
                    <small class="text-secondary">University Guidelines</small>
                </div>

                <div class="rule-card bg-body-tertiary border-top-0 border-end-0 border-bottom-0 text-body">Maintain proper conduct inside the laboratory.</div>
                <div class="rule-card bg-body-tertiary border-top-0 border-end-0 border-bottom-0 text-body">Use assigned units only.</div>
                <div class="rule-card bg-body-tertiary border-top-0 border-end-0 border-bottom-0 text-body">Academic-only internet usage.</div>
                <div class="rule-card bg-body-tertiary border-top-0 border-end-0 border-bottom-0 text-body">Handle equipment responsibly.</div>

                <div class="alert alert-danger mt-3 py-2 small border-0">
                    Violations may result in suspension of privileges.
                </div>

            </div>
        </div>

    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>