<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/Data_count.php");

// Fetch language data
$Php = languageUsed($conn, 'PHP');
$Java = languageUsed($conn, 'JAVA');
$C = languageUsed($conn, 'C');
$Csharp = languageUsed($conn, 'C#');
$CPlusPlus = languageUsed($conn, 'C++');

// Get announcements
$posts = getPost($conn, 1);

// Get Weekly Traffic Data (Instantiated dynamically via Data_count)
// Expected format from Data_count.php: ['Mon' => 12, 'Tue' => 19, ...]
$weekly_traffic = getWeeklyTrafficData($conn); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | UC Sit-in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* --- DASHBOARD THEME ADAPTATION --- */
        body {
            background-color: var(--bg-body) !important;
            color: var(--text-main);
            transition: background 0.3s, color 0.3s;
        }

        .main-text { color: var(--text-main); }
        .text-muted-custom { color: var(--text-muted) !important; }

        /* Card Styling */
        .card-custom {
            background-color: var(--bg-sidebar) !important;
            border: 1px solid var(--border-color) !important;
            transition: transform 0.2s ease, background 0.3s;
        }
        .card-custom:hover { transform: translateY(-3px); }

        /* Modal & Form Styling for Dark Mode */
        .modal-content {
            background-color: var(--bg-sidebar);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }
        .modal-header, .modal-footer {
            border-color: var(--border-color);
            background-color: var(--bg-body) !important;
        }
        .form-control, .form-select {
            background-color: var(--bg-body) !important;
            color: var(--text-main) !important;
            border: 1px solid var(--border-color) !important;
        }
        .input-group-text {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--text-muted) !important;
        }

        /* Sidebar Layout Fix */
        main.container-fluid {
            margin-left: 260px;
            width: calc(100% - 260px);
            transition: 0.3s ease;
            padding: 2rem;
        }
        .sidebar.collapsed ~ main.container-fluid {
            margin-left: 80px;
            width: calc(100% - 80px);
        }
        @media (max-width: 991px) {
            main.container-fluid { margin-left: 0 !important; width: 100% !important; }
        }

        .vr { background-color: var(--border-color); opacity: 0.5; }

        /* --- DARK MODE MODAL & INPUT CONTRAST FIX --- */
        [data-theme="dark"] .modal-content {
            background-color: var(--bg-sidebar) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }

        [data-theme="dark"] .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        /* Force dark-mode inputs to have clean background, bright text, and distinct borders */
        [data-theme="dark"] .form-control, 
        [data-theme="dark"] .form-select {
            background-color: #1a1a1a !important; /* Forces a solid, readable dark gray instead of pure black */
            color: #ffffff !important;
            border: 1px solid #444444 !important;
        }

        [data-theme="dark"] .form-control:focus, 
        [data-theme="dark"] .form-select:focus {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        /* Style the placeholder text so it's readable against dark backgrounds */
        [data-theme="dark"] .form-control::placeholder {
            color: #aaaaaa !important;
            opacity: 1;
        }

        /* Invert close button to white */
        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(1) brightness(2) !important;
        }
    </style>

    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            // Detect theme for chart color synchronization
            const isDark = document.body.getAttribute('data-theme') === 'dark';
            const textColor = isDark ? '#e0e0e0' : '#495057';
            const gridColor = isDark ? '#333333' : '#e9ecef';

            // 1. Programming Preferences Pie Chart
            var programmingData = google.visualization.arrayToDataTable([
                ['Language', 'Students'],
                ['C', <?php echo (int)$C; ?>],
                ['C#', <?php echo (int)$Csharp; ?>],
                ['C++', <?php echo (int)$CPlusPlus; ?>],
                ['Java', <?php echo (int)$Java; ?>],
                ['PHP', <?php echo (int)$Php; ?>]
            ]);

            var pieOptions = {
                pieHole: 0.4,
                colors: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#fd7e14'],
                legend: { position: 'bottom', textStyle: { color: textColor } },
                chartArea: { width: '90%', height: '75%' },
                backgroundColor: 'transparent',
                pieSliceBorderColor: isDark ? '#1e1e1e' : '#ffffff'
            };

            var pieChart = new google.visualization.PieChart(document.getElementById('piechart'));
            pieChart.draw(programmingData, pieOptions);

            // 2. Weekly Lab Traffic Line Chart (NEW ANALYTICS)
            var trafficData = google.visualization.arrayToDataTable([
                ['Day', 'Sessions'],
                ['Mon', <?php echo isset($weekly_traffic['Mon']) ? (int)$weekly_traffic['Mon'] : 0; ?>],
                ['Tue', <?php echo isset($weekly_traffic['Tue']) ? (int)$weekly_traffic['Tue'] : 0; ?>],
                ['Wed', <?php echo isset($weekly_traffic['Wed']) ? (int)$weekly_traffic['Wed'] : 0; ?>],
                ['Thu', <?php echo isset($weekly_traffic['Thu']) ? (int)$weekly_traffic['Thu'] : 0; ?>],
                ['Fri', <?php echo isset($weekly_traffic['Fri']) ? (int)$weekly_traffic['Fri'] : 0; ?>],
                ['Sat', <?php echo isset($weekly_traffic['Sat']) ? (int)$weekly_traffic['Sat'] : 0; ?>]
            ]);

            var lineOptions = {
                curveType: 'function',
                colors: ['#198754'],
                backgroundColor: 'transparent',
                legend: { position: 'none' },
                chartArea: { width: '85%', height: '75%' },
                hAxis: { textStyle: { color: textColor } },
                vAxis: { 
                    textStyle: { color: textColor },
                    gridlines: { color: gridColor },
                    baselineColor: gridColor
                }
            };

            var lineChart = new google.visualization.LineChart(document.getElementById('trafficChart'));
            lineChart.draw(trafficData, lineOptions);
        }

        // Re-render graphs on Theme Toggle click events
        document.addEventListener('click', function(e) {
            if (e.target.closest('#themeToggle')) {
                setTimeout(drawCharts, 350); 
            }
        });
    </script>
</head>

<body>
    <?php include("../includes/admin_sidebar.php"); ?>

    <main class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="fw-bold main-text">Admin Dashboard</h4>
                <p class="text-muted-custom">Overview of lab activities and system insights.</p>
            </div>
        </div>

        <!-- Stat Cards Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-custom border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-people-fill text-primary fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted-custom fw-bold text-uppercase">Registered Students</small>
                            <h4 class="mb-0 fw-bold main-text"><?php echo countStudents($conn); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-person-video3 text-success fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted-custom fw-bold text-uppercase">Current Sit-in</small>
                            <h4 class="mb-0 fw-bold main-text"><?php echo currentSitIns($conn); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted-custom fw-bold text-uppercase">Total Sessions</small>
                            <h4 class="mb-0 fw-bold main-text"><?php echo getTotalSessions($conn); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Dashboard Panels -->
        <div class="row g-4 mb-4">
            <!-- Pie Chart Section -->
            <div class="col-12 col-lg-6">
                <div class="card card-custom border-0 shadow-sm h-100 rounded-4">
                    <div class="card-header bg-transparent py-3 border-0">
                        <h5 class="mb-0 fw-bold main-text">
                            <i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Programming Preference
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        <div id="piechart" style="width: 100%; height: 320px;"></div>
                    </div>
                </div>
            </div>

            <!-- Weekly Activity Line Chart Section -->
            <div class="col-12 col-lg-6">
                <div class="card card-custom border-0 shadow-sm h-100 rounded-4">
                    <div class="card-header bg-transparent py-3 border-0">
                        <h5 class="mb-0 fw-bold main-text">
                            <i class="bi bi-graph-up text-success me-2"></i>Weekly Sit-In Traffic Trend
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        <div id="trafficChart" style="width: 100%; height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Announcements / Broadcasts Footer Row -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card card-custom border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold main-text">
                            <i class="bi bi-megaphone-fill text-danger me-2"></i>Broadcasts & Notices
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                            <i class="bi bi-plus-lg"></i> Post
                        </button>
                    </div>

                    <div class="card-body overflow-auto" style="max-height: 300px;">
                        <?php if(!empty($posts)): ?>
                            <?php foreach ($posts as $post): ?>
                                <?php 
                                    $p_color = ($post['priority'] === 'urgent') ? 'bg-danger' : (($post['priority'] === 'academic') ? 'bg-info' : 'bg-success');
                                ?>
                                <div class="d-flex mb-4">
                                    <div class="me-3 text-center">
                                        <div class="<?php echo $p_color ?> rounded-circle mb-1" style="width:12px; height:12px;"></div>
                                        <div class="vr h-100"></div>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 main-text"><?php echo htmlspecialchars($post['title']) ?></h6>
                                        <small class="text-muted-custom d-block mb-2">
                                            <?php echo date("F j, Y • g:i a", strtotime($post['date_posted'])); ?>
                                        </small>
                                        <p class="small text-muted-custom mb-0"><?php echo htmlspecialchars($post['message']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php else: ?>
                            <p class="text-center text-muted-custom py-4">No recent Announcements...</p>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Post Announcement Modal -->
    <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-4 main-text">
                <div class="modal-header border-0 py-3 px-4">
                    <h5 class="modal-title fw-bold">New Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="../action/announcement.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted-custom text-uppercase">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. System Maintenance" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted-custom text-uppercase">Category</label>
                                <select name="category" class="form-select">
                                    <option value="General">General</option>
                                    <option value="Urgent">Urgent</option>
                                    <option value="Academic">Academic</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted-custom text-uppercase">Target</label>
                                <select name="target" class="form-select">
                                    <option value="all">All Students</option>
                                    <option value="BSIT">BSIT Only</option>
                                    <option value="BSCS">BSCS Only</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted-custom text-uppercase">Message</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Write content here..." required></textarea>
                        </div>
                        <button type="submit" name="post_now" class="btn btn-primary w-100 py-2 fw-bold">Post Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Success Message -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="margin-top: 60px;">
        <div id="successToast" class="toast border-0 shadow-lg rounded-4" role="alert">
            <div class="d-flex align-items-center p-3 card-custom">
                <i class="bi bi-check-circle-fill text-success fs-5 me-3"></i>
                <div class="toast-body p-0">
                    <strong class="main-text d-block">Success!</strong>
                    <span class="text-muted-custom small">Announcement posted successfully.</span>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('status') === 'success') {
                const toast = new bootstrap.Toast(document.getElementById('successToast'));
                toast.show();
            }
        });
    </script>
</body>
</html>