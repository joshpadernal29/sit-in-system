<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/database.php");
include("../action/student_profile_logic.php");

// toast message
$course = isset($student['course']) ? strtolower($student['course']) : 'all';

// CRITICAL FIX: Added 'id' to the SELECT
$query = "SELECT id, title FROM announcements 
          WHERE is_active = 1 
          AND (target_audience = 'all' OR target_audience = ?) 
          ORDER BY id DESC LIMIT 1";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $course);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$latest = mysqli_fetch_assoc($res);

$latest_id = $latest['id'] ?? 0;
$announcement_title = $latest['title'] ?? "No new updates";
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!--NEW ANNOUNCEMENT NOTIF-->
<style>
    .notification-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .pulse-indicator {
        position: absolute;
        top: -2px;
        right: -10px;
        width: 8px;
        height: 8px;
        background-color: #ff4d4d; /* Noticeable Red */
        border-radius: 50%;
        border: 1px solid white;
    }

    .pulse-indicator::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ff4d4d;
        border-radius: 50%;
        animation: navbar-pulse 1.5s infinite;
    }

    @keyframes navbar-pulse {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(3.5); opacity: 0; }
    }
</style>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm py-1 sticky-top">
        <div class="container-fluid px-lg-4">

            <a class="navbar-brand d-flex align-items-center" href="student_dashboard.php" style="gap: 10px;">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; padding: 2px;">
                    <img src="../assets/ccsmainlogo2.png" class="rounded-circle"
                        style="width: 100%; height: 100%; object-fit: contain;" alt="UC Logo">
                </div>
                <div class="lh-1">
                    <span class="fw-bold d-block mb-0" style="font-size: 0.85rem; letter-spacing: 0.5px;">CCS LAB PORTAL</span>
                    <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.55rem;">University of
                        Cebu</small>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#studentNav">
                <span class="navbar-toggler-icon" style="transform: scale(0.75);"></span>
            </button>

            <div class="collapse navbar-collapse" id="studentNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 nav-underline">
                    <li class="nav-item">
                        <a class="nav-link px-2 text-white d-flex align-items-center" href="studentDashboard.php"
                            style="font-size: 0.9rem;">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 text-white d-flex align-items-center" href="student_profile.php"
                            style="font-size: 0.9rem;">
                            <i class="bi bi-person-circle me-2"></i>My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 text-white d-flex align-items-center" href="sit_in_history.php"
                            style="font-size: 0.9rem;">
                            <i class="bi bi-clock-history me-2"></i>Sit-in History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 text-white d-flex align-items-center" href="student_reservation.php"
                            style="font-size: 0.9rem;">
                            <i class="bi bi-calendar-check me-2"></i>sit-in reserve
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 text-white d-flex align-items-center" href="student_announcement.php"
                            style="font-size: 0.9rem;">
                            <div class="notification-wrapper">
                                <i class="bi bi-megaphone me-2"></i>Announcements
                                
                                <?php if ($announcement_title !== "No new updates"): ?>
                                    <span id="nav-pulse-dot" class="pulse-indicator"></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <div class="text-center d-none d-xl-block lh-1">
                        <!--MAKE THIS DYNAMIC-->
                        <span class="text-white fw-bold" style="font-size: 0.75rem;"><?php echo $student['firstname']. " " .$student['lastname'] ?></span>
                        <br><small class="text-white-50" style="font-size: 0.70rem;"><?php echo $student['course']. " " .$student['year_level'] ?></small>
                    </div>
                    
                    <!--LOGOUT-->
                    <form action="../action/logout_logic.php" method="post">
                        <button type="submit" name="log_out"
                            class="btn btn-sm btn-light text-primary fw-bold px-3 py-1 rounded-2 shadow-sm d-flex align-items-center transition-hover">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</header>

<!--NOTIFICATION TOAST-->
<div class="toast-container position-fixed top-0 end-0 p-3 mt-5" style="z-index: 1100;">
    <div id="globalAnnouncementToast" class="toast border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-primary text-white border-0 rounded-top-4 py-3">
            <i class="bi bi-megaphone-fill me-2"></i>
            <strong class="me-auto">System Notification</strong>
            <small class="text-white-50">Just now</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body bg-white rounded-bottom-4 p-3">
            <p class="mb-2 fw-bold text-dark"><?php echo htmlspecialchars($announcement_title); ?></p>
            <p class="small text-muted mb-3">A new update has been posted to the portal.</p>
            <a href="student_announcement.php" class="btn btn-primary btn-sm rounded-pill w-100 shadow-sm">
                <i class="bi bi-eye me-1"></i> View Details
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // These come from your PHP variables at the top of the header
        const latestId = "<?php echo $latest_id; ?>";
        const pulseDot = document.getElementById('nav-pulse-dot');
        const toastEl = document.getElementById('globalAnnouncementToast');

        // --- 1. BLINKER LOGIC ---
        // Hide the dot ONLY if the stored ID matches the current ID from DB
        if (localStorage.getItem('lastReadId') === latestId) {
            if (pulseDot) pulseDot.style.display = 'none';
        }

        // --- 2. TOAST LOGIC ---
        // Show if: 1. There is an announcement AND 2. This specific ID hasn't been shown in this session
        if (latestId !== "0" && sessionStorage.getItem('lastToastId') !== latestId) {
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 8000 });
                toast.show();
                // Mark this ID as "Toast Shown" for this session
                sessionStorage.setItem('lastToastId', latestId);
            }
        }
    });
</script>