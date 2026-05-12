<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/database.php");
include("../action/student_profile_logic.php");

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>CCS LAB PORTAL</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>

/* ================= BASE ================= */
body{
    margin:0;
    background:#f5f7fb;
}

/* ================= SIDEBAR ================= */
.sidebar{
    position:fixed;
    top:0;
    left:0;

    width:260px;
    height:100vh;

    background:#fff;
    border-right:1px solid #e9ecef;

    display:flex;
    flex-direction:column;

    transition:.3s ease;
}

/* COLLAPSED STATE */
.sidebar.collapsed{
    width:80px;
}

/* ================= TOP ================= */
.sidebar-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:1rem;
    border-bottom:1px solid #eee;
}

/* LOGO (clickable) */
.sidebar-brand{
    cursor:pointer;
}

.sidebar-brand img{
    width:38px;
    height:38px;
}

/* ================= TOGGLE BUTTON (FIXED) ================= */
.toggle-btn{
    border:none;
    background:#f1f4ff;
    border-radius:10px;
    padding:6px 10px;

    display:none; /* hidden by default */
}

/* SHOW ONLY WHEN EXPANDED */
.sidebar:not(.collapsed) .toggle-btn{
    display:flex;
}

/* ================= NAV ================= */
.sidebar-nav{
    flex:1;
    padding:1rem;
}

.nav-link{
    display:flex;
    align-items:center;
    gap:12px;

    padding:.85rem 1rem;
    border-radius:12px;

    font-weight:600;
    color:#495057;

    transition:.2s;
    margin-bottom:6px;
}

/* hide text when collapsed */
.sidebar.collapsed .nav-link span{
    display:none;
}

/* center icons when collapsed */
.sidebar.collapsed .nav-link{
    justify-content:center;
}

/* hover */
.nav-link:hover{
    background:#f1f4ff;
    color:#0d6efd;
}

.nav-link.active{
    background:#0d6efd;
    color:#fff;
}

/* ================= FOOTER ================= */
.sidebar-footer{
    padding:1rem;
    border-top:1px solid #eee;
}

.user-card{
    display:flex;
    align-items:center;
    gap:10px;

    background:#f8f9fa;
    padding:.8rem;
    border-radius:12px;
}

/* avatar FIX */
.avatar{
    width:40px;
    height:40px;
    min-width:40px;
    min-height:40px;

    border-radius:50%;
    background:#0d6efd;

    display:flex;
    align-items:center;
    justify-content:center;

    color:#fff;
}

/* hide text collapsed */
.sidebar.collapsed .user-info,
.sidebar.collapsed .logout-text{
    display:none;
}

/* center footer collapsed */
.sidebar.collapsed .user-card{
    justify-content:center;
}

/* ================= MAIN CONTENT ================= */
.main-content{
    margin-left:260px;
    padding:1.5rem;

    transition:margin-left .3s ease;
}

/* shift when collapsed */
.sidebar.collapsed ~ .main-content{
    margin-left:80px;
}

/* ================= MOBILE ================= */
@media(max-width:991px){

    .sidebar{
        left:-100%;
        width:260px;
    }

    .sidebar.expanded{
        left:0;
    }

    .main-content{
        margin-left:0 !important;
    }
}

</style>
</head>

<body>

<!-- ================= SIDEBAR ================= -->
<aside class="sidebar" id="sidebar">

    <!-- TOP -->
    <div class="sidebar-top">

        <!-- LOGO (TOGGLES SIDEBAR) -->
        <div class="sidebar-brand" id="logoToggle">
            <img src="../assets/ccsmainlogo2.png">
        </div>

        <!-- TOGGLE BUTTON (ONLY IN EXPANDED) -->
        <button class="toggle-btn" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>

    </div>

    <!-- NAV -->
    <div class="sidebar-nav">

        <a href="studentDashboard.php" class="nav-link">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <a href="student_profile.php" class="nav-link">
            <i class="bi bi-person-circle"></i>
            <span>My Profile</span>
        </a>

        <a href="sit_in_history.php" class="nav-link">
            <i class="bi bi-clock-history"></i>
            <span>History</span>
        </a>

        <a href="student_reservation.php" class="nav-link">
            <i class="bi bi-calendar-check"></i>
            <span>Reservation</span>
        </a>

        <a href="student_announcement.php" class="nav-link">
            <i class="bi bi-megaphone"></i>
            <span>Announcements</span>
        </a>

        <a href="student_testimonial.php" class="nav-link">
            <i class="bi bi-chat-right-quote"></i>
            <span>Testimonial</span>
        </a>

    </div>

    <!-- FOOTER -->
    <div class="sidebar-footer">

        <div class="user-card">

            <div class="avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="user-info">
                <div class="fw-bold" style="font-size:.8rem;">
                    <?= $student['firstname']." ".$student['lastname']; ?>
                </div>

                <div class="text-muted" style="font-size:.7rem;">
                    <?= $student['course']." ".$student['year_level']; ?>
                </div>
            </div>

        </div>
        <!--LOGOUT-->
        <form action="../action/logout_logic.php" method="post">
            <button class="btn btn-primary w-100 mt-2" name='log_out'">
                <i class="bi bi-box-arrow-right me-1"></i>
                <span class="logout-text">Logout</span>
            </button>
        </form>

    </div>

</aside>

<!-- ================= SCRIPT ================= -->
<script>

const sidebar = document.getElementById("sidebar");
const toggleBtn = document.getElementById("toggleSidebar");
const logo = document.getElementById("logoToggle");

// hamburger (only in expanded)
toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("collapsed");
});

// logo toggle (always works)
logo.addEventListener("click", () => {
    sidebar.classList.toggle("collapsed");
});

</script>

</body>
</html>