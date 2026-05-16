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
        chartArea: { width: '92%', height: '80%' },
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
    height:100px;
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
        <small class="text-secondary">Overview of your laboratory activity</small>
    </div>

    <div class="row g-4">

        <!-- LEFT CONTENT -->
        <div class="col-lg-9">

            <!-- KPI ROW -->
            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="kpi-card bg-body border border-light-subtle">
                        <div class="kpi-label text-secondary">Remaining Sessions</div>
                        <div class="kpi-value text-primary"><?= htmlspecialchars($student['sit_ins']); ?></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card bg-body border border-light-subtle">
                        <div class="kpi-label text-secondary">Total Lab Hours</div>
                        <div class="kpi-value text-body">12.5</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card bg-body border border-light-subtle">
                        <div class="kpi-label text-secondary">Status</div>
                        <div class="kpi-value text-success">Verified</div>
                    </div>
                </div>

            </div>

            <!-- CHARTS -->
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