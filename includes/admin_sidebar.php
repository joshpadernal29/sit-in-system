<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>CCS ADMIN PANEL</title>

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

/* COLLAPSED */
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

/* LOGO */
.sidebar-brand{
    cursor:pointer;
}

.sidebar-brand img{
    width:38px;
    height:38px;
}

/* toggle button only expanded */
.toggle-btn{
    border:none;
    background:#f1f4ff;
    border-radius:10px;
    padding:6px 10px;
    display:none;
}

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
    text-decoration:none;
}

/* hover */
.nav-link:hover{
    background:#f1f4ff;
    color:#0d6efd;
}

/* active */
.nav-link.active{
    background:#0d6efd;
    color:#fff;
}

/* collapse behavior */
.sidebar.collapsed .nav-link span{
    display:none;
}

.sidebar.collapsed .nav-link{
    justify-content:center;
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

/* avatar */
.avatar{
    width:40px;
    height:40px;
    min-width:40px;

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

.sidebar.collapsed .user-card{
    justify-content:center;
}

/* ================= MAIN ================= */
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

        <!-- LOGO TOGGLE -->
        <div class="sidebar-brand" id="logoToggle">
            <img src="../assets/uclogo2.png">
        </div>

        <!-- BUTTON -->
        <button class="toggle-btn" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>

    </div>

    <!-- NAV (ADMIN VERSION) -->
    <div class="sidebar-nav">
        <a href="adminDashboard.php" class="nav-link">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <a href="sitin_management.php" class="nav-link">
            <i class="bi bi-search"></i>
            <span>Search Student</span>
        </a>

        <a href="studentList.php" class="nav-link">
            <i class="bi bi-people"></i>
            <span>Students</span>
        </a>

        <a href="sit_in_list.php" class="nav-link">
            <i class="bi bi-person-video3"></i>
            <span>Sit-in</span>
        </a>

        <a href="sit_in_reports.php" class="nav-link">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span>Generate  Report</span>
        </a>

        <a class="nav-link" href="sit_in_records.php">
            <i class="bi bi-card-list"></i>
            <span>Sit-in Records</span>
        </a>

        <a class="nav-link" href="feedbacks.php">
            <i class="bi bi-chat-left-text"></i>
            <span>Student Feedbacks</span>
        </a>

        <a class="nav-link" href="admin_reservation.php">
            <i class="bi bi-ticket-detailed"></i>
            <span>Admin Reservation Management</span>
        </a>   

        <a class="nav-link" href="admin_testimonial.php">
            <i class="bi bi-chat-right-quote"></i>
            <span>Manage Testimonials</span>
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
                    Administrator
                </div>
                <div class="text-muted" style="font-size:.7rem;">
                    Main Campus
                </div>
            </div>

        </div>
        <!--LOGOUT LOGIC-->
        <form action="../action/logout_logic.php" method="post"> 
            <button class="btn btn-primary w-100 mt-2" name='log_out'>
                <i class="bi bi-box-arrow-right me-1"></i>
                <span class="logout-text">Logout</span>
            </button>
        </form>

    </div>

</aside>

<!-- ================= SCRIPT ================= -->
<script>
    const sidebar = document.getElementById("sidebar");
    const toggle = document.getElementById("toggleSidebar");
    const logo = document.getElementById("logoToggle");

    // toggle button (only expanded visible)
    toggle.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
    });

    // logo always toggles
    logo.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
    });
</script>

</body>
</html>