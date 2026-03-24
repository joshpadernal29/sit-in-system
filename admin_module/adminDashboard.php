<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/Data_count.php");

// get language used data
$Php = languageUsed($conn, 'PHP');
$Java = languageUsed($conn, 'JAVA');
$C = languageUsed($conn, 'C');
$Csharp = languageUsed($conn, 'C#');
$CPlusPlus = languageUsed($conn, 'C++');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | UC Sit-in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Language', 'Students'],
                ['C', <?php echo $C ?>], 
                ['C#', <?php echo $Csharp ?>], 
                ['C++', <?php echo $CPlusPlus ?>], 
                ['Java', <?php echo $Java ?>], 
                ['PHP', <?php echo $Php ?>]
            ]);

            var options = {
                pieHole: 0.4, // Modern Donut Style
                colors: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#fd7e14'],
                legend: { position: 'bottom' },
                chartArea: { width: '100%', height: '80%' },
                backgroundColor: 'transparent'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>
</head>

<body class="bg-light">
    <?php include("../includes/adminHeader.php"); ?>

    <main class="container py-4">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3"> <!--MAKE THIS DYNAMIC-->
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-people-fill text-primary fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase">Registered</small>
                            <h4 class="mb-0 fw-bold"><?php echo  $total_students = countStudents($conn); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4"> <!--MAKE THIS DYNAMIC-->
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-person-video3 text-success fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase">Current Sit-in</small>
                            <h4 class="mb-0 fw-bold"><?php echo $current_sitting_in = currentSitIns($conn); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4"><!--MAKE THIS DYNAMIC-->
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase">Total Sessions</small>
                            <h4 class="mb-0 fw-bold"><?php echo $total_sessions = getTotalSessions($conn); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--MAKE THIS DYNAMIC-->
        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Programming Preference
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        <div id="piechart" style="width: 100%; height: 350px;"></div>
                    </div>
                </div>
            </div>
            <!--MAKE THIS DYNAMIC-->
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-megaphone-fill text-danger me-2"></i>Broadcasts
                        </h5>
                        <button class="btn btn-sm btn-outline-primary rounded-pill">Post New</button>
                    </div>
                    <div class="card-body overflow-auto" style="max-height: 400px;">
                        
                        <div class="d-flex mb-4">
                            <div class="me-3 text-center">
                                <div class="bg-danger rounded-circle mb-1" style="width:12px; height:12px;"></div>
                                <div class="vr h-100"></div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">System Maintenance</h6>
                                <small class="text-muted d-block mb-2">March 20, 2026 • 10:00 PM</small>
                                <p class="small text-muted mb-0">The system will undergo scheduled maintenance to optimize database queries.</p>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <div class="me-3 text-center">
                                <div class="bg-info rounded-circle mb-1" style="width:12px; height:12px;"></div>
                                <div class="vr h-100"></div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">New Feature: CSV Export</h6>
                                <small class="text-muted d-block mb-2">March 15, 2026</small>
                                <p class="small text-muted mb-0">Admins can now export sit-in logs directly to Excel/CSV format.</p>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="me-3 text-center">
                                <div class="bg-success rounded-circle mb-1" style="width:12px; height:12px;"></div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Welcome Admin</h6>
                                <small class="text-muted d-block mb-2">March 14, 2026</small>
                                <p class="small text-muted mb-0">Portal is officially live for the second semester.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        .card { transition: transform 0.2s ease; }
        .card:hover { transform: translateY(-3px); }
        .vr { width: 2px; background-color: #dee2e6; opacity: 1; }
    </style>
    <!--main end-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>