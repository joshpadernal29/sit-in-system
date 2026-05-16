<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/database.php");
include("../action/student_profile_logic.php");

$current_page = basename($_SERVER['PHP_SELF']);

// 1. Fetch all active announcement IDs applicable to this student from the backend database
$course = isset($student['course']) ? strtolower($student['course']) : 'all';
$count_query = "SELECT id FROM announcements WHERE is_active = 1 AND (target_audience = 'all' OR target_audience = ?)";
$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param("s", $course);
$count_stmt->execute();
$active_ids = $count_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Map rows to a simple flat JSON array of integers: [12, 11, 10...]
$announcement_ids_json = json_encode(array_column($active_ids, 'id'));
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
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
}

/* ================= SIDEBAR ================= */
.sidebar{
    position:fixed;
    top:0;
    left:0;

    width:260px;
    height:100vh;

    border-right:1px solid var(--bs-border-color);

    display:flex;
    flex-direction:column;

    transition:.3s ease;
    z-index: 1040;
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
    border-bottom:1px solid var(--bs-border-color-translucent);
}

/* LOGO WRAPPER ENGINE (Symmetrical to Login/Register Circles) */
.sidebar-brand-wrapper {
    cursor:pointer;
    width: 50px;
    height: 50px;
    background-color: transparent; /* Seamless with sidebar bg in light mode */
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: background-color 0.3s ease, box-shadow 0.3s ease, width 0.3s, height 0.3s;
}

.sidebar-brand-wrapper img {
    position: relative;
    z-index: 10 !important;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    display: block;
}

/* --- Dark Mode Configuration Override for Sidebar Disk --- */
[data-bs-theme="dark"] .sidebar-brand-wrapper {
    background-color: #ffffff !important; /* Pure white backing disk */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Adjust top padding distribution when collapsed to maintain perfect balance */
.sidebar.collapsed .sidebar-top {
    justify-content: center;
    padding: 1rem 0;
}

/* ================= TOGGLE BUTTON (FIXED) ================= */
.toggle-btn{
    border:none;
    background: var(--bs-secondary-bg);
    color: var(--bs-body-color);
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
    overflow-y: auto;
}

.nav-link{
    display:flex;
    align-items:center;
    gap:12px;

    padding:.85rem 1rem;
    border-radius:12px;

    font-weight:600;
    color: var(--bs-secondary-color);

    transition:.2s;
    margin-bottom:6px;
    position: relative;
}

/* hide text when collapsed */
.sidebar.collapsed .nav-link span,
.sidebar.collapsed .theme-label {
    display:none;
}

/* center icons when collapsed */
.sidebar.collapsed .nav-link{
    justify-content:center;
}

/* hover */
.nav-link:hover{
    background: var(--bs-secondary-bg);
    color: var(--bs-primary);
}

.nav-link.active{
    background: var(--bs-primary) !important;
    color:#fff !important;
}

/* ================= FOOTER ================= */
.sidebar-footer{
    padding:1rem;
    border-top:1px solid var(--bs-border-color-translucent);
}

.user-card{
    display:flex;
    align-items:center;
    gap:10px;

    background: var(--bs-secondary-bg);
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
    background: var(--bs-primary);

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

<body class="bg-body-tertiary text-body">

<!-- ================= SIDEBAR ================= -->
<aside class="sidebar bg-body" id="sidebar">

    <!-- TOP -->
    <div class="sidebar-top">

        <!-- UNIFIED CIRCULAR LOGO WRAPPER CONTAINER -->
        <div class="sidebar-brand-wrapper rounded-circle" id="logoToggle">
            <img src="../assets/ccsmainlogo2.png" alt="Logo">
        </div>

        <!-- TOGGLE BUTTON (ONLY IN EXPANDED) -->
        <button class="toggle-btn" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>

    </div>

    <!-- NAV -->
    <div class="sidebar-nav">

        <a href="studentDashboard.php" class="nav-link <?= ($current_page == 'studentDashboard.php') ? 'active' : ''; ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <a href="student_profile.php" class="nav-link <?= ($current_page == 'student_profile.php') ? 'active' : ''; ?>">
            <i class="bi bi-person-circle"></i>
            <span>My Profile</span>
        </a>

        <a href="sit_in_history.php" class="nav-link <?= ($current_page == 'sit_in_history.php') ? 'active' : ''; ?>">
            <i class="bi bi-clock-history"></i>
            <span>History</span>
        </a>

        <a href="student_reservation.php" class="nav-link <?= ($current_page == 'student_reservation.php') ? 'active' : ''; ?>">
            <i class="bi bi-calendar-check"></i>
            <span>Reservation</span>
        </a>

        <!-- ================= ANNOUNCEMENTS LINK WITH BADGES ================= -->
        <a href="student_announcement.php" class="nav-link <?= ($current_page == 'student_announcement.php') ? 'active' : ''; ?>">
            <i class="bi bi-megaphone"></i>
            <span>Announcements</span>
            
            <!-- Number Counter (Shows when Expanded) -->
            <span id="unreadCountBadge" class="badge rounded-pill bg-danger ms-auto d-none animate-fade-in" style="font-size: 0.75rem;">0</span>
            
            <!-- Mini Indicator Dot (Shows on top corner of icon when Collapsed) -->
            <span id="unreadDotBadge" class="position-absolute top-0 start-50 translate-middle p-1 bg-danger border border-light rounded-circle d-none" style="margin-left: 10px; margin-top: 10px;"></span>
        </a>

        <a href="student_testimonial.php" class="nav-link <?= ($current_page == 'student_testimonial.php') ? 'active' : ''; ?>">
            <i class="bi bi-chat-right-quote"></i>
            <span>Testimonial</span>
        </a>

        <!-- ================= DARK MODE UTILITY BUTTON ================= -->
        <button class="nav-link border-0 bg-transparent w-100 text-start" id="themeToggleBtn">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
            <span class="theme-label">Dark Mode</span>
        </button>

    </div>

    <!-- FOOTER -->
    <div class="sidebar-footer">

        <div class="user-card">

            <div class="avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="user-info">
                <div class="fw-bold" style="font-size:.8rem;">
                    <?= htmlspecialchars($student['firstname']." ".$student['lastname']); ?>
                </div>

                <div class="text-secondary" style="font-size:.7rem;">
                    <?= htmlspecialchars($student['course']." ".$student['year_level']); ?>
                </div>
            </div>

        </div>
        <!--LOGOUT-->
        <form action="../action/logout_logic.php" method="post">
            <button class="btn btn-primary w-100 mt-2" name="log_out">
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

// Helper tracking for Responsive UI visibility changes
function updateBadgeVisibility() {
    const textBadge = document.getElementById('unreadCountBadge');
    const dotBadge = document.getElementById('unreadDotBadge');
    
    if (!textBadge || !dotBadge) return;
    
    const count = parseInt(textBadge.innerText) || 0;
    
    if (count > 0) {
        if (sidebar.classList.contains('collapsed')) {
            textBadge.classList.add('d-none');
            dotBadge.classList.remove('d-none');
        } else {
            textBadge.classList.remove('d-none');
            dotBadge.classList.add('d-none');
        }
    } else {
        textBadge.classList.add('d-none');
        dotBadge.classList.add('d-none');
    }
}

// Hamburger (only in expanded state)
if (toggleBtn) {
    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        updateBadgeVisibility();
    });
}

// Logo toggle functionality
if (logo) {
    logo.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        updateBadgeVisibility();
    });
}

// ================= UNREAD ANNOUNCEMENT ENGINE =================
document.addEventListener('DOMContentLoaded', () => {
    // Array of database announcement IDs from PHP array engine
    const backendIds = <?= $announcement_ids_json; ?>;
    const lastReadId = parseInt(localStorage.getItem('lastReadId')) || 0;

    // Filter out items that are strictly larger/newer than the client's last read anchor ID
    const unreadItems = backendIds.filter(id => parseInt(id) > lastReadId);
    const unreadCount = unreadItems.length;

    const countBadge = document.getElementById('unreadCountBadge');
    if (countBadge && unreadCount > 0) {
        countBadge.innerText = unreadCount;
    }
    
    updateBadgeVisibility();
});

// ================= THEME ARCHITECTURE ENGINE =================
const htmlElement = document.documentElement;
const themeToggleBtn = document.getElementById('themeToggleBtn');
const themeIcon = document.getElementById('themeIcon');
const themeLabel = document.querySelector('.theme-label');

const savedTheme = localStorage.getItem('theme');
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
const initialTheme = savedTheme || (systemPrefersDark ? 'dark' : 'light');

setTheme(initialTheme);

if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });
}

function setTheme(theme) {
    htmlElement.setAttribute('data-bs-theme', theme);
    localStorage.setItem('theme', theme);

    if (!themeIcon || !themeLabel) return;

    if (theme === 'dark') {
        themeIcon.className = 'bi bi-sun-fill text-warning';
        themeLabel.innerText = 'Light Mode';
    } else {
        themeIcon.className = 'bi bi-moon-stars-fill text-primary';
        themeLabel.innerText = 'Dark Mode';
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>