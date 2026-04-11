<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/studentData.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Dashboard | Student Portal</title>
    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            // Line Chart: Sit-in Sessions Over Time
            var lineData = google.visualization.arrayToDataTable([
                ['Day', 'Sessions'],
                ['Mon',  2],
                ['Tue',  4],
                ['Wed',  3],
                ['Thu',  5],
                ['Fri',  1]
            ]);

            var lineOptions = {
                curveType: 'function',
                legend: { position: 'bottom' },
                colors: ['#0d6efd'],
                chartArea: {width: '85%', height: '70%'}
            };

            var lineChart = new google.visualization.LineChart(document.getElementById('line_chart_div'));
            lineChart.draw(lineData, lineOptions);

            // Pie Chart: Preferred Programming Languages
            var pieData = google.visualization.arrayToDataTable([
                ['Language', 'Usage'],
                ['PHP',     11],
                ['Python',      7],
                ['C++',  4],
                ['Java', 2]
            ]);

            var pieOptions = {
                pieHole: 0.4,
                colors: ['#0d6efd', '#198754', '#0dcaf0', '#ffc107'],
                chartArea: {width: '90%', height: '80%'}
            };

            var pieChart = new google.visualization.PieChart(document.getElementById('pie_chart_div'));
            pieChart.draw(pieData, pieOptions);
        }
    </script>

    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
        .card { transition: transform 0.2s ease; border: none; }
        .card:hover { transform: translateY(-3px); }
        .chart-container { min-height: 300px; width: 100%; }
        .stat-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>

    <?php include("../includes/studentHeader.php"); ?>

    <main class="container-fluid py-4 px-lg-5">
        <div class="row g-4">
            
            <div class="col-lg-3">
                <div class="card shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="../assets/default_profile.jpg" class="rounded-circle border border-4 border-white shadow-sm" alt="Profile" width="90">
                            <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-2" title="Online"></span>
                        </div>
                        <h6 class="fw-bold mb-1"><?php echo $student['firstname']. " " .$student['lastname'] ?></h6>
                        <p class="text-muted small mb-3"><?php echo $student['student_id'] ?></p>
                        <div class="d-grid">
                            <a href="student_profile.php" class="btn btn-primary btn-sm rounded-pill">Edit Profile</a>
                        </div>
                    </div>
                    <div class="list-group list-group-flush border-top border-light px-2 pb-3">
                        <div class="list-group-item bg-transparent border-0">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.6rem;">Course</small>
                            <span class="fw-semibold small"><?php echo $student['course']. "-" .$student['year_level'] ?></span>
                        </div>
                        <div class="list-group-item bg-transparent border-0">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.6rem;">Verification</small>
                            <span class="badge bg-soft-success text-success rounded-pill px-3 mt-1">Active for 2nd Sem</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm rounded-4 p-3 border-start border-4 border-info">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-soft-info text-info me-3">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Remaining Sessions</small>
                                    <h4 class="fw-bold mb-0"><?php echo $student['sit_ins'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm rounded-4 p-3 border-start border-4 border-primary">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary-subtle text-primary me-3">
                                    <i class="bi bi-pc-display fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Current Status</small>
                                    <h4 class="fw-bold mb-0 text-primary">Logged Out</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm rounded-4 p-3 border-start border-4 border-success">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-soft-success text-success me-3">
                                    <i class="bi bi-calendar-check fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Total Hours</small>
                                    <h4 class="fw-bold mb-0">12.5 hrs</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="card shadow-sm rounded-4 p-4">
                            <h6 class="fw-bold mb-4">Sit-in Activity (Weekly)</h6>
                            <div id="line_chart_div" class="chart-container"></div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card shadow-sm rounded-4 p-4">
                            <h6 class="fw-bold mb-4">Preferred Languages</h6>
                            <div id="pie_chart_div" class="chart-container"></div>
                        </div>
                    </div>
                </div>

            </div> </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>