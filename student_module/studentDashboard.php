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
            // 1. Line Chart: Direct Data Input
            var lineData = google.visualization.arrayToDataTable([
                ['Day', 'Sessions'],
                ['Mon', 2],
                ['Tue', 4],
                ['Wed', 3],
                ['Thu', 5],
                ['Fri', 1]
            ]);

            var lineOptions = {
                curveType: 'function',
                legend: { position: 'none' },
                colors: ['#0d6efd'],
                chartArea: { width: '90%', height: '75%' },
                vAxis: { gridlines: { color: '#f0f0f0' }, minValue: 0 },
                hAxis: { textStyle: { color: '#6c757d', fontSize: 11 } }
            };

            var lineChart = new google.visualization.LineChart(document.getElementById('line_chart_div'));
            lineChart.draw(lineData, lineOptions);

            // 2. Pie Chart: Direct Data Input
            var pieData = google.visualization.arrayToDataTable([
                ['Language', 'Usage'],
                ['PHP', 15],
                ['Python', 8],
                ['C++', 5],
                ['Java', 3]
            ]);

            var pieOptions = {
                pieHole: 0.5,
                colors: ['#0d6efd', '#198754', '#0dcaf0', '#ffc107'],
                chartArea: { width: '95%', height: '80%' },
                legend: { position: 'bottom', textStyle: { fontSize: 11 } },
                pieSliceText: 'none'
            };

            var pieChart = new google.visualization.PieChart(document.getElementById('pie_chart_div'));
            pieChart.draw(pieData, pieOptions);
        }
    </script>

    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 12px; transition: all 0.3s ease; }
        .stat-card { border-bottom: 4px solid; }
        .chart-container { min-height: 320px; width: 100%; }
        
        /* Rules Sidebar Styling */
        .rules-column { background: #ffffff; border-left: 1px solid #e0e0e0; min-height: 100vh; }
        .rule-card { 
            background: #f8f9fa; 
            border-radius: 10px; 
            padding: 12px; 
            margin-bottom: 12px; 
            border-left: 4px solid #0d6efd;
            display: flex;
            align-items: flex-start;
        }
        .rule-card i { font-size: 1.2rem; margin-right: 12px; margin-top: 2px; }
        .rule-text { font-size: 0.85rem; line-height: 1.4; color: #444; }
    </style>
</head>
<body>

    <?php include("../includes/studentHeader.php"); ?>

    <main class="container-fluid">
        <div class="row">
            
            <div class="col-lg-9 p-4 px-lg-5">
                <h5 class="fw-bold mb-4 text-dark">Student Overview</h5>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm stat-card border-info p-3">
                            <small class="text-muted fw-bold">REMAINING SESSIONS</small>
                            <h3 class="fw-bold mb-0 mt-1"><?php echo $student['sit_ins'] ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm stat-card border-primary p-3">
                            <small class="text-muted fw-bold">TOTAL LAB HOURS</small>
                            <h3 class="fw-bold mb-0 mt-1">12.5</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm stat-card border-success p-3">
                            <small class="text-muted fw-bold">STATUS</small>
                            <h3 class="fw-bold mb-0 mt-1 text-success">Verified</h3>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="card shadow-sm p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-bold m-0"><i class="bi bi-graph-up me-2"></i>Sit-in</h6>
                            </div>
                            <div id="line_chart_div" class="chart-container"></div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card shadow-sm p-4">
                            <h6 class="fw-bold mb-4"><i class="bi bi-pie-chart-fill me-2"></i>Programming Focus</h6>
                            <div id="pie_chart_div" class="chart-container"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 rules-column p-4 shadow-sm">
                <div class="text-center mb-4">
                    <img src="../assets/ccsmainlogo2.png" alt="UC Logo" width="50" class="mb-2"> <h6 class="fw-bold text-primary mb-0">LABORATORY POLICIES</h6>
                    <small class="text-muted">University of Cebu</small>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i>
                        <span class="fw-bold small text-uppercase">General Rules</span>
                    </div>
                    <div class="rule-card shadow-sm border-primary">
                        <div class="rule-text">Proper conduct must be maintained. Only authorized users are allowed. No eating, drinking, or loud conversations.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-pc-display text-primary me-2"></i>
                        <span class="fw-bold small text-uppercase">Laboratory Use</span>
                    </div>
                    <div class="rule-card shadow-sm border-primary">
                        <div class="rule-text">Log in properly before use. Use your <b>assigned unit</b> only. Do not modify software or system settings.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-globe2 text-primary me-2"></i>
                        <span class="fw-bold small text-uppercase">Internet & Files</span>
                    </div>
                    <div class="rule-card shadow-sm border-primary">
                        <div class="rule-text">Academic use only. No illegal downloads. You are responsible for backing up your own files.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-tools text-primary me-2"></i>
                        <span class="fw-bold small text-uppercase">Equipment</span>
                    </div>
                    <div class="rule-card shadow-sm border-primary">
                        <div class="rule-text">Handle equipment with care. Keep your area clean and organized before leaving.</div>
                    </div>
                </div>

                <div class="alert alert-danger border-0 mt-3 py-2 shadow-sm" style="font-size: 0.75rem;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>Penalty:</strong> Violations may result in suspension of laboratory privileges.
                </div>
            </div>

            <style>
                .rules-column { 
                    background: #ffffff; 
                    border-left: 1px solid #dee2e6; 
                    max-height: 100vh; 
                    overflow-y: auto; 
                }

                .rule-card { 
                    background: #fdfdfd; 
                    border-radius: 8px; 
                    padding: 10px; 
                    border-left: 3px solid #0d6efd;
                    margin-bottom: 5px;
                }

                .rule-text { 
                    font-size: 0.8rem; 
                    color: #495057; 
                    line-height: 1.5;
                }
            </style>

        </div> 
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>