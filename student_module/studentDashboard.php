<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/studentData.php");
include("../action/Data_count.php");

// get language used data
$language_used = progLanguage($conn,$student_id);

// prepare pie chart data
$rows = [];
foreach ($language_used as $row) {
    $rows[] = "['" . $row['language'] . "', " . $row['language_count'] . "]";
}
$chartDataString = implode(',', $rows);

// get sit in data for line chart
$sit_in_rate = sit_in_rate($conn,$student_id);

$data = [['Date', 'Sessions']];
foreach ($sit_in_rate as $row) {
    $data[] = [(string)$row['sit_in_date'], (int)$row['sit_in_rate']];
}

$jsonTable = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
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

    // LINE CHART
    var lineData = google.visualization.arrayToDataTable(
        <?= $jsonTable ?>
    );

    var lineOptions = {
        curveType: 'function',
        legend: { position: 'none' },
        colors: ['#0d6efd'],
        chartArea: { width: '92%', height: '80%' },
        vAxis: { minValue: 0 },
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
        legend: { position: 'bottom' },
        pieSliceText: 'none'
    };

    var pieChart = new google.visualization.PieChart(document.getElementById('pie_chart_div'));
    pieChart.draw(pieData, pieOptions);
}
</script>

<style>

/* ================= BASE ================= */
body{
    background-color: #f4f7f6;
}

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

/* ================= PAGE HEADER ================= */
.page-title{
    font-weight:700;
    color:#1f2937;
}

/* ================= KPI CARDS ================= */
.kpi-card{
    border:0;
    border-radius:14px;
    padding:18px;
    background:#fff;
    box-shadow:0 6px 20px rgba(0,0,0,.05);
    height:100px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.kpi-label{
    font-size:.75rem;
    text-transform:uppercase;
    color:#6c757d;
    font-weight:600;
}

.kpi-value{
    font-size:1.7rem;
    font-weight:700;
}

/* ================= CHART CARDS ================= */
.chart-card{
    background:#fff;
    border:0;
    border-radius:14px;
    box-shadow:0 6px 20px rgba(0,0,0,.05);
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
    background:#fff;
    border-left:1px solid #dee2e6;
    border-radius:14px;
    box-shadow:0 6px 20px rgba(0,0,0,.05);
    padding:1rem;
}

.rule-card{
    background:#f8f9fa;
    border-left:4px solid #0d6efd;
    padding:10px;
    border-radius:8px;
    font-size:.85rem;
    margin-bottom:10px;
}

/* ================= RESPONSIVE ================= */
@media(max-width:991px){
    .main-content{
        margin-left:0 !important;
    }

    .rules-column{
        border-left:none;
        margin-top:1rem;
    }
}

</style>

</head>

<body>

<?php include("../includes/student_sidebar.php"); ?>

<!-- ================= MAIN CONTENT ================= -->
<main class="main-content">

    <!-- PAGE TITLE -->
    <div class="mb-4">
        <h4 class="page-title">Student Dashboard</h4>
        <small class="text-muted">Overview of your laboratory activity</small>
    </div>

    <div class="row g-4">

        <!-- LEFT CONTENT -->
        <div class="col-lg-9">

            <!-- KPI ROW -->
            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="kpi-card">
                        <div class="kpi-label">Remaining Sessions</div>
                        <div class="kpi-value text-primary"><?= $student['sit_ins'] ?></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card">
                        <div class="kpi-label">Total Lab Hours</div>
                        <div class="kpi-value">12.5</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card">
                        <div class="kpi-label">Status</div>
                        <div class="kpi-value text-success">Verified</div>
                    </div>
                </div>

            </div>

            <!-- CHARTS -->
            <div class="row g-4">

                <div class="col-md-7">
                    <div class="chart-card">
                        <div class="chart-title">
                            <i class="bi bi-graph-up me-1"></i> Sit-in Activity
                        </div>
                        <div id="line_chart_div" class="chart-box"></div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="chart-card">
                        <div class="chart-title">
                            <i class="bi bi-pie-chart-fill me-1"></i> Programming Focus
                        </div>
                        <div id="pie_chart_div" class="chart-box"></div>
                    </div>
                </div>

            </div>

        </div>

        <!-- RIGHT RULES -->
        <div class="col-lg-3">

            <div class="rules-column">

                <div class="text-center mb-3">
                    <img src="../assets/ccsmainlogo2.png" width="45">
                    <h6 class="fw-bold text-primary mt-2">Laboratory Policies</h6>
                    <small class="text-muted">University Guidelines</small>
                </div>

                <div class="rule-card">Maintain proper conduct inside the laboratory.</div>
                <div class="rule-card">Use assigned units only.</div>
                <div class="rule-card">Academic-only internet usage.</div>
                <div class="rule-card">Handle equipment responsibly.</div>

                <div class="alert alert-danger mt-3 py-2 small">
                    Violations may result in suspension of privileges.
                </div>

            </div>

        </div>

    </div>

</main>

</body>
</html>