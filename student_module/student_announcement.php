<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/studentData.php");
include("../config/database.php");
// notification controller
$course = isset($student['course']) ? strtolower($student['course']) : 'all';

$query = "SELECT id FROM announcements 
          WHERE is_active = 1 
          AND (target_audience = 'all' OR target_audience = ?) 
          ORDER BY id DESC LIMIT 1";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $course);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$latest = mysqli_fetch_assoc($res);
$latest_id = $latest['id'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | SIT-IN System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* Subtle Backgrounds for Badges */
        .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
        .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
        .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1); }

        /* Card Hover Animations */
        .card { 
            transition: transform 0.2s ease, box-shadow 0.2s ease; 
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
        }

        /* NEW Tag Pulse Animation */
        @keyframes pulse-new {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pulse {
            animation: pulse-new 2s infinite;
        }

        .btn-white {
            background: white;
            border: 1px solid #dee2e6;
        }
        
        .btn-white:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>

    <?php include("../includes/studentHeader.php"); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-megaphone-fill text-primary me-2"></i>Campus Announcements
                        </h5>
                        <small class="text-muted">Stay updated with the latest news from CCS Admin</small>
                    </div>
                    <button class="btn btn-white btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                </div>

                <div class="vstack gap-3">

                    <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-primary overflow-hidden position-relative">
                        <span class="position-absolute top-0 end-0 m-3 badge rounded-pill bg-primary animate-pulse" 
                              style="font-size: 0.65rem; padding: 5px 10px;">
                            NEW UPDATE
                        </span>

                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 small fw-bold">
                                        <i class="bi bi-pin-angle-fill me-1"></i> IMPORTANT
                                    </span>
                                    <div class="bg-primary rounded-circle ms-2 shadow-sm" style="width: 8px; height: 8px;"></div>
                                </div>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i> 2 hours ago</small>
                            </div>
                            
                            <h6 class="fw-bold text-dark fs-5">Midterm Examination Schedule</h6>
                            <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.6;">
                                Please be guided on the upcoming midterm examination schedule for the 2nd Semester.
                                Ensure that your sit-in requirements are settled at the CCS Laboratory before the
                                exam starts.
                            </p>

                            <div class="d-flex align-items-center pt-2">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2"
                                    style="width: 30px; height: 30px;">
                                    <i class="bi bi-person-fill text-primary small"></i>
                                </div>
                                <small class="fw-bold text-dark">CCS Dean's Office</small>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-success overflow-hidden">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 small fw-bold">
                                    EVENT
                                </span>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i> Yesterday</small>
                            </div>
                            
                            <h6 class="fw-bold text-dark fs-5">CCS IT Week 2026: Call for Participants</h6>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Registration for the "Code-A-Thon" and "Network Troubleshooting" competitions is now
                                open. Visit the lab admin to sign up.
                            </p>
                            
                            <a href="#" class="btn btn-link text-success p-0 mt-3 small fw-bold text-decoration-none">
                                View Mechanics <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-info overflow-hidden">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-info-subtle text-info rounded-pill px-3 py-2 small fw-bold">
                                    SYSTEM
                                </span>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i> 3 days ago</small>
                            </div>
                            
                            <h6 class="fw-bold text-dark fs-5">Lab B Maintenance</h6>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Lab B will be closed for software updates on Saturday. Students are advised to use
                                Lab A for their sit-in sessions.
                            </p>
                        </div>
                    </div>

                </div> <div class="text-center mt-4 pt-2">
                    <button class="btn btn-link text-muted small fw-bold border-0 text-decoration-none">
                        Show older announcements <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!--notification controller-->
    <script>
        // Ensure this variable matches the $latest_id you fetched at the top of this page
        const currentId = "<?php echo $latest_id; ?>";
        
        if (currentId !== "0") {
            // Save the ID to permanent memory
            localStorage.setItem('lastReadId', currentId);
            console.log("Marked announcement " + currentId + " as read.");
        }
    </script>
</body>
</html>