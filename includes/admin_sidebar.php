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
        /* ================= THEME VARIABLES ================= */
        :root {
            --bg-body: #f5f7fb;
            --bg-sidebar: #ffffff;
            --bg-card: #f8f9fa;
            --text-main: #495057;
            --text-muted: #6c757d;
            --border-color: #e9ecef;
            --nav-hover: #f1f4ff;
        }

        [data-theme="dark"] {
            --bg-body: #121212;
            --bg-sidebar: #1e1e1e;
            --bg-card: #2d2d2d;
            --text-main: #e0e0e0;
            --text-muted: #a0a0a0;
            --border-color: #333333;
            --nav-hover: #2c2c2c;
        }

        /* ================= BASE ================= */
        body {
            margin: 0;
            background: var(--bg-body);
            color: var(--text-main);
            transition: background 0.3s, color 0.3s;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: .3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        /* ================= LOGO CONTAINER ================= */
        .sidebar-brand {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            transition: background 0.3s ease;
        }

        /* White circle only in dark mode */
        [data-theme="dark"] .logo-wrapper {
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }

        .sidebar-brand img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .toggle-btn {
            border: none;
            background: var(--nav-hover);
            color: var(--text-main);
            border-radius: 10px;
            padding: 6px 10px;
            display: none;
        }

        .sidebar:not(.collapsed) .toggle-btn {
            display: flex;
        }

        /* ================= NAV ================= */
        .sidebar-nav {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: .85rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            color: var(--text-main);
            transition: .2s;
            margin-bottom: 6px;
            text-decoration: none;
        }

        .nav-link:hover {
            background: var(--nav-hover);
            color: #0d6efd;
        }

        .nav-link.active {
            background: #0d6efd;
            color: #fff;
        }

        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .theme-text {
            display: none;
        }

        .sidebar.collapsed .nav-link,
        .sidebar.collapsed .theme-toggle {
            justify-content: center;
        }

        /* ================= DARK MODE TOGGLE ================= */
        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: .85rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .theme-toggle:hover {
            background: var(--nav-hover);
        }

        /* ================= FOOTER ================= */
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            padding: .8rem;
            border-radius: 12px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 50%;
            background: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .sidebar.collapsed .user-info,
        .sidebar.collapsed .logout-text {
            display: none;
        }

        .sidebar.collapsed .user-card {
            justify-content: center;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        /* ================= MAIN ================= */
        .main-content {
            margin-left: 260px;
            padding: 1.5rem;
            transition: margin-left .3s ease;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: 80px;
        }

        /* ================= MOBILE ================= */
        @media(max-width: 991px) {
            .sidebar {
                left: -100%;
            }
            .sidebar.expanded {
                left: 0;
            }
            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body>

<!-- ================= SIDEBAR ================= -->
<aside class="sidebar" id="sidebar">

    <!-- TOP -->
    <div class="sidebar-top">
        <div class="sidebar-brand" id="logoToggle">
            <!-- Added wrapper for white circle effect -->
            <div class="logo-wrapper">
                <img src="../assets/ccsmainlogo2.png" alt="Logo">
            </div>
        </div>
        <button class="toggle-btn" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <!-- NAV -->
    <div class="sidebar-nav">
        <a href="adminDashboard.php" class="nav-link <?= ($current_page == 'adminDashboard.php') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <a href="sitin_management.php" class="nav-link <?= ($current_page == 'sitin_management.php') ? 'active' : '' ?>">
            <i class="bi bi-search"></i>
            <span>Search Student</span>
        </a>

        <a href="studentList.php" class="nav-link <?= ($current_page == 'studentList.php') ? 'active' : '' ?>">
            <i class="bi bi-people"></i>
            <span>Students</span>
        </a>

        <a href="sit_in_list.php" class="nav-link <?= ($current_page == 'sit_in_list.php') ? 'active' : '' ?>">
            <i class="bi bi-person-video3"></i>
            <span>Current Sit-in</span>
        </a>

        <a href="sit_in_reports.php" class="nav-link <?= ($current_page == 'sit_in_reports.php') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span>Generate Report</span>
        </a>

        <a class="nav-link <?= ($current_page == 'sit_in_records.php') ? 'active' : '' ?>" href="sit_in_records.php">
            <i class="bi bi-card-list"></i>
            <span>Sit-in Records</span>
        </a>

        <a class="nav-link <?= ($current_page == 'feedbacks.php') ? 'active' : '' ?>" href="feedbacks.php">
            <i class="bi bi-chat-left-text"></i>
            <span>Student Feedbacks</span>
        </a>

        <a class="nav-link <?= ($current_page == 'admin_reservation.php') ? 'active' : '' ?>" href="admin_reservation.php">
            <i class="bi bi-ticket-detailed"></i>
            <span>Reservation</span>
        </a>   

        <a class="nav-link <?= ($current_page == 'admin_testimonial.php') ? 'active' : '' ?>" href="admin_testimonial.php">
            <i class="bi bi-chat-right-quote"></i>
            <span>Testimonials</span>
        </a>

        <a class="nav-link <?= ($current_page == 'software_import.php') ? 'active' : '' ?>" href="software_import.php">
            <i class="bi bi-plugin"></i>
            <span>Software Import</span>
        </a>
    </div>

    <!-- FOOTER -->
    <div class="sidebar-footer">
        
        <!-- Dark Mode Toggle Button -->
        <button class="theme-toggle" id="themeToggle">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
            <span class="theme-text">Dark Mode</span>
        </button>

        <div class="user-card">
            <div class="avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="user-info">
                <div class="fw-bold" style="font-size:.8rem;">Administrator</div>
                <div class="text-muted" style="font-size:.7rem;">Main Campus</div>
            </div>
        </div>

        <form action="../action/logout_logic.php" method="post"> 
            <button class="btn btn-primary w-100 mt-2" name='log_out'>
                <i class="bi bi-box-arrow-right me-1"></i>
                <span class="logout-text">Logout</span>
            </button>
        </form>
    </div>

</aside>

<!-- ================= MAIN CONTENT AREA =================
<main class="main-content">
    <h2>Admin Panel</h2>
    <p>The logo now has a white background circle for visibility when Dark Mode is enabled.</p>
</main> -->

<!-- ================= SCRIPT ================= -->
<script>
    const sidebar = document.getElementById("sidebar");
    const toggle = document.getElementById("toggleSidebar");
    const logo = document.getElementById("logoToggle");
    const themeToggle = document.getElementById("themeToggle");
    const themeIcon = document.getElementById("themeIcon");
    const themeText = document.querySelector(".theme-text");
    const body = document.body;

    // --- Sidebar Toggle Logic ---
    const toggleSidebar = () => {
        sidebar.classList.toggle("collapsed");
    };

    toggle.addEventListener("click", toggleSidebar);
    logo.addEventListener("click", toggleSidebar);

    // --- Dark Mode Logic ---
    const currentTheme = localStorage.getItem("theme");
    if (currentTheme === "dark") {
        body.setAttribute("data-theme", "dark");
        themeIcon.classList.replace("bi-moon-stars-fill", "bi-sun-fill");
        themeText.innerText = "Light Mode";
    }

    themeToggle.addEventListener("click", () => {
        if (body.getAttribute("data-theme") === "dark") {
            body.removeAttribute("data-theme");
            themeIcon.classList.replace("bi-sun-fill", "bi-moon-stars-fill");
            themeText.innerText = "Dark Mode";
            localStorage.setItem("theme", "light");
        } else {
            body.setAttribute("data-theme", "dark");
            themeIcon.classList.replace("bi-moon-stars-fill", "bi-sun-fill");
            themeText.innerText = "Light Mode";
            localStorage.setItem("theme", "dark");
        }
    });
</script>

</body>
</html>