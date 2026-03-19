<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net">
    <!--google charts cdn and loader-->
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['Language', 'Hours per Day'],
                ['C', 1],
                ['C#', 2],
                ['C++', 3],
                ['Java', 4],
                ['Php', 5]
            ]);

            var options = {
                legend: {
                    position: 'bottom',   // moves legend below
                    alignment: 'center'   // centers it horizontally
                },
                chartArea: {
                    width: '90%',
                    height: '70%'
                }
            };

            var chart = new google.visualization.PieChart(
                document.getElementById('piechart')
            );

            chart.draw(data, options);
        }
    </script>
    <title>admin Home</title>
</head>

<body>
    <!--header-->
    <?php include("../includes/adminHeader.php"); ?>
    <!--header end-->

    <!--main content-->
    <main>
        <div class="container my-5">
            <div class="row g-4">

                <!-- Pie Chart Section -->
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow h-100">

                        <!-- Header -->
                        <div
                            class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-pie-chart-fill text-primary me-2"></i>
                                Statistics Overview
                            </h5>
                        </div>

                        <!-- Body -->
                        <span class="p-2"> <!--insert php data here-->
                            <p>Students Registered: </p>
                            <p>Current Sit-in: </p>
                            <p>Total Sit-in: </p>
                        </span>
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div class="w-100 d-flex justify-content-center">
                                <div id="piechart" style="width:100%; max-width:400px; height:400px;"></div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Announcements Section -->
                <!-- php foreach loop -->
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow h-100">

                        <!-- Header -->
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-megaphone-fill text-warning me-2"></i>
                                Announcements
                            </h5>
                        </div>

                        <!-- Body -->
                        <div class="card-body">

                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="fw-semibold mb-1">System Maintenance</h6>
                                <small class="text-muted">March 20, 2026</small>
                                <p class="mb-0 text-muted">
                                    The system will undergo scheduled maintenance at 10:00 PM.
                                </p>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="fw-semibold mb-1">New Feature Released</h6>
                                <small class="text-muted">March 15, 2026</small>
                                <p class="mb-0 text-muted">
                                    We added new dashboard analytics for administrators.
                                </p>
                            </div>

                            <div>
                                <h6 class="fw-semibold mb-1">Welcome Admin</h6>
                                <small class="text-muted">March 14, 2026</small>
                                <p class="mb-0 text-muted">
                                    Stay updated with the latest system announcements here.
                                </p>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>
    <!--main end-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>