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
                            <h4 class="mb-0 fw-bold">
                                <?php echo  $total_students = countStudents($conn); ?>
                            </h4>
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
                            <h4 class="mb-0 fw-bold">
                                <?php echo $current_sitting_in = currentSitIns($conn); ?>
                            </h4>
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
                            <h4 class="mb-0 fw-bold">
                                <?php echo $total_sessions = getTotalSessions($conn); ?>
                            </h4>
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
                        <button type="button" class="btn btn-primary shadow-sm d-flex align-items-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                            <i class="bi bi-megaphone-fill"></i> Post Announcement
                        </button>
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
                                <p class="small text-muted mb-0">The system will undergo scheduled maintenance to
                                    optimize database queries.</p>
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
                                <p class="small text-muted mb-0">Admins can now export sit-in logs directly to Excel/CSV
                                    format.</p>
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
        .card {
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .vr {
            width: 2px;
            background-color: #dee2e6;
            opacity: 1;
        }
    </style>

    <!--modal-->
    <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="announcementModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">

                <div class="modal-header bg-light border-bottom-0 py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-megaphone text-primary fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark" id="announcementModalLabel">New
                                Announcement</h5>
                            <p class="text-muted small mb-0">Broadcast a message to all students.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <form action="../action/announcement.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Announcement
                                Title</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="bi bi-type-h1 text-muted"></i></span>
                                <input type="text" name="title" class="form-control border-start-0 ps-0"
                                    placeholder="e.g. System Maintenance" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Category</label>
                                <select name="category" class="form-select bg-light border-0">
                                    <option value="General">General</option>
                                    <option value="Urgent">Urgent</option>
                                    <option value="Academic">Academic</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Target
                                    Audience</label>
                                <select name="target" class="form-select bg-light border-0">
                                    <option value="all">All Students</option>
                                    <option value="BSIT">BSIT Only</option>
                                    <option value="BSCS">BSCS Only</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Message
                                Content</label>
                            <textarea name="message" class="form-control bg-light border-0" rows="4"
                                placeholder="Write your announcement here..." required></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="post_now" class="btn btn-primary py-2 fw-bold shadow-sm">
                                <i class="bi bi-send-fill me-2"></i> Post Announcement
                            </button>
        
                            <button type="button" class="btn btn-outline-secondary text-decoration-none small"
                                data-bs-dismiss="modal">
                                Discard Draft
                            </button>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light border-top-0 py-2 justify-content-center">
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i> This will appear on
                        the Student Dashboard.</small>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-content {
            overflow: hidden;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #fff !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
        }

        .input-group-text {
            color: #6c757d;
            transition: all 0.2s;
        }

        .input-group:focus-within .input-group-text {
            border-color: #0d6efd;
            color: #0d6efd;
        }

        /* FIX: Prevents the background table from shifting when modal opens */
        body.modal-open {
            padding-right: 0 !important;
        }
    </style>
    <!--end of modal-->

    <!--toast message succesful posting of announcement-->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="margin-top: 60px;">
        <div id="successToast" class="toast border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center p-2">
                <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3 ms-2">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                </div>
                <div class="toast-body ps-0">
                    <strong class="d-block text-dark">Success!</strong>
                    <span class="text-muted small">Announcement posted successfully.</span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <style>
        #successToast {
            background-color: #ffffff;
            min-width: 300px;
        }
        .toast-container {
            z-index: 2000; 
        }
    </style>
    <!--end of toast message-->

    <!--main end-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!--for toast message trigger-->
    <script> 
        document.addEventListener('DOMContentLoaded', function () {
            // Check if the URL has ?status=success
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.get('status') === 'success') {
                // Initialize the Bootstrap Toast
                const toastEl = document.getElementById('successToast');
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 5000 // Visible for 5 seconds
                });
                
                // Show toast message
                toast.show();

                // Clean the URL (Optional: removes ?status=success from address bar)
                //window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>

</html>