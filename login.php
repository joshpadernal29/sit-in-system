<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("action/login_logic.php"); 
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>login page</title>
    <style>
        /* Automatically applies only when dark mode is turned on */
        [data-bs-theme="dark"] .login-page-logo {
            /* This adds a crisp white glow around your PNG shapes and boosts brightness */
            filter: brightness(1.2) drop-shadow(0px 0px 12px rgba(255, 255, 255, 0.25));
        }
    </style>
</head>

<body class="bg-body text-body">
    <!--navbar-->
    <?php include("includes/header.html") ?>
    <!--end of navbar-->
    
    <!--main content-->
    <main class="bg-body-tertiary">
        <div class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
            <div class="row w-100 justify-content-center align-items-center g-4">

                <div class="col-12 col-lg-6 text-center mb-3 mb-lg-0">
                    <!-- Added 'login-page-logo' class here to connect with our style fix -->
                    <img src="assets/ccsmainlogo2.png" alt="CCS Logo" class="img-fluid mb-3 login-page-logo"
                        style="max-width: 180px; width: 40%;">
                    <h1 class="fw-bold text-body">CCS Sit-in Monitoring</h1>
                    <p class="text-secondary d-none d-md-block">Ensuring an organized laboratory experience for every
                        student.</p>
                </div>

                <div class="col-12 col-md-8 col-lg-5">
                    <div class="card border border-light-subtle bg-body shadow-lg p-2 p-md-3 rounded-4">
                        <div class="card-body">
                            <div class="mb-4 text-center text-lg-start">
                                <h2 class="fw-bold text-body h4">User Login</h2>
                                <p class="text-muted small">Please enter your credentials to access the system.</p>
                            </div>
                            <!--login form-->
                            <form action="action/login_logic.php" method="POST">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control bg-body-tertiary border-0" id="student-id" name="user_id" required>
                                    <label for="student-id" class="text-secondary">ID Number</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control bg-body-tertiary border-0" id="pwd" name="user_password" required>
                                    <label for="pwd" class="text-secondary">Password</label>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input shadow-none" type="checkbox" id="remember"
                                            name="remember">
                                        <label class="form-check-label small text-secondary" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                    <a href="#" class="small text-primary text-decoration-none fw-bold">Forgot
                                        Password?</a>
                                </div>

                                <div class="d-grid">
                                    <button type="submit"
                                        class="btn btn-primary btn-lg shadow-sm fw-bold py-3 rounded-3" name="user_login">LOGIN</button>
                                </div>

                                <div class="text-center mt-4">
                                    <p class="small text-muted">Don't have an account?
                                        <a href="register.php"
                                            class="text-primary fw-bold text-decoration-none d-block d-sm-inline mt-2 mt-sm-0">Register
                                            here</a>
                                    </p>
                                </div>
                            </form>
                            <!--login form end-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!--end of main content-->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Synchronized Hybrid Theme Management Engine -->
    <script>
        const htmlElement = document.documentElement;
        const themeToggleBtn = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        
        const mainNavbar = document.getElementById('mainNavbar');
        const navbarLinks = document.getElementById('navbarLinks');
        const regBtn = document.getElementById('regBtn');

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

            if (!mainNavbar) return;

            if (theme === 'dark') {
                if (themeIcon) themeIcon.className = 'bi bi-sun-fill';
                if (themeToggleBtn) {
                    themeToggleBtn.title = 'Switch to Light Mode';
                    themeToggleBtn.classList.remove('text-white');
                    themeToggleBtn.classList.add('text-body');
                }
                
                mainNavbar.classList.remove('bg-primary', 'navbar-dark');
                mainNavbar.classList.add('bg-body-tertiary', 'navbar-light');
                
                if (navbarLinks) {
                    navbarLinks.style.setProperty('--bs-navbar-active-color', 'var(--bs-primary)');
                    navbarLinks.style.setProperty('--bs-nav-underline-border-width', '3px');
                }
                
                // SAFE CLASS ADJUSTMENT: Modifies text colors without discarding routing/events
                document.querySelectorAll('#navbarLinks .nav-link').forEach(link => {
                    link.classList.add('text-body-secondary');
                    link.classList.remove('text-white', 'text-white-50');
                });
                
                if (regBtn) {
                    regBtn.classList.remove('btn-light', 'text-primary');
                    regBtn.classList.add('btn-primary');
                }
                
                const brandText = document.querySelector('.navbar-brand .lh-1 span');
                const brandSub = document.querySelector('.navbar-brand .lh-1 small');
                if (brandText) {
                    brandText.classList.remove('text-white');
                    brandText.classList.add('text-body');
                }
                if (brandSub) {
                    brandSub.classList.remove('text-white-50');
                    brandSub.classList.add('text-body-secondary');
                }
            } else {
                if (themeIcon) themeIcon.className = 'bi bi-moon-stars-fill';
                if (themeToggleBtn) {
                    themeToggleBtn.title = 'Switch to Dark Mode';
                    themeToggleBtn.classList.remove('text-body');
                    themeToggleBtn.classList.add('text-white');
                }
                
                mainNavbar.classList.remove('bg-body-tertiary', 'navbar-light');
                mainNavbar.classList.add('bg-primary', 'navbar-dark');
                
                if (navbarLinks) {
                    navbarLinks.style.setProperty('--bs-navbar-active-color', '#fff');
                    navbarLinks.style.setProperty('--bs-nav-underline-border-width', '3px');
                }
                
                // SAFE CLASS ADJUSTMENT: Modifies text colors without discarding routing/events
                document.querySelectorAll('#navbarLinks .nav-link').forEach(link => {
                    link.classList.remove('text-body-secondary');
                    link.classList.add('text-white-50');
                });
                
                if (regBtn) {
                    regBtn.classList.remove('btn-primary');
                    regBtn.classList.add('btn-light', 'text-primary');
                }
                
                const brandText = document.querySelector('.navbar-brand .lh-1 span');
                const brandSub = document.querySelector('.navbar-brand .lh-1 small');
                if (brandText) {
                    brandText.classList.remove('text-body');
                    brandText.classList.add('text-white');
                }
                if (brandSub) {
                    brandSub.classList.remove('text-body-secondary');
                    brandSub.classList.add('text-white-50');
                }
            }
        }
    </script>
</body>

</html>