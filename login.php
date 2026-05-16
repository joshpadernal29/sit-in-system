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
    <title>Login - CCS Sit-in Monitoring</title>
    <style>
        /* Smooth Global Theme Transitions */
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Forces Equal Structural Columns inside the Large Unified Card Layout */
        .login-card .row.g-0 {
            min-height: 620px; /* Forces both left and right sides to stretch to this minimum height */
        }

        /* Enhanced Dark Mode Card Properties */
        [data-bs-theme="dark"] .login-card {
            background-color: var(--bs-body-bg) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 1.5rem 4rem rgba(0, 0, 0, 0.6) !important;
        }

        /* Form Control Improvements under Dark Mode */
        [data-bs-theme="dark"] .form-control {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border-color: rgba(255, 255, 255, 0.18) !important;
        }

        [data-bs-theme="dark"] .form-control:focus {
            border-color: #0d6efd !important;
            background-color: rgba(255, 255, 255, 0.04) !important;
        }

        /* Focus Ring Glow Animation */
        .form-floating .form-control {
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .form-floating .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.2);
        }

        /* --- Circular Logo Wrapper Properties --- */
        .logo-wrapper {
            width: 190px;
            height: 190px;
            background-color: transparent; /* Invisible by default in light mode */
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative; /* Establishes stacking layer */
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Force the logo image to layer cleanly and retain scale constraints */
        .login-page-logo {
            position: relative;
            z-index: 10 !important; /* Forces image completely above the background layer container */
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        /* --- Dark Mode Configuration Override --- */
        [data-bs-theme="dark"] .logo-wrapper {
            background-color: #ffffff !important; /* Pure white background disk */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        [data-bs-theme="dark"] .login-page-logo {
            filter: contrast(1.05) brightness(1.00); /* Keeps color clarity over white circle */
        }
    </style>
</head>

<body class="bg-body-tertiary text-body">
    
    <!--navbar-->
    <?php include("includes/header.html") ?>
    <!--end of navbar-->
    
    <!--main content-->
    <main>
        <div class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
            
            <!-- Large Unified Card Container -->
            <div class="card login-card border border-secondary-subtle bg-body shadow-lg rounded-4 overflow-hidden w-100" style="max-width: 1100px;">
                <div class="row g-0">
                    
                    <!-- Left Column: Center-aligned Branding Pane (Equal Vertical Stretch) -->
                    <div class="col-12 col-md-6 bg-body-secondary d-flex flex-column justify-content-center align-items-center p-4 p-md-5 border-end border-secondary-subtle text-center">
                        <!-- Dynamic Circular Logo Wrapper -->
                        <div class="logo-wrapper mb-4 rounded-circle">
                            <img src="assets/ccsmainlogo2.png" alt="CCS Logo" class="img-fluid login-page-logo">
                        </div>
                        <h1 class="fw-bold text-body display-6 mb-3">CCS Sit-in Monitoring</h1>
                        <p class="text-body-secondary lead fs-6 mb-0 px-xl-4">
                            Ensuring an organized, structured laboratory space environment for every computing student.
                        </p>
                    </div>

                    <!-- Right Column: Form Block (Equal Vertical Stretch) -->
                    <div class="col-12 col-md-6 d-flex flex-column justify-content-center p-4 p-md-5">
                        
                        <div class="mb-4 text-center text-md-start">
                            <h2 class="fw-bold text-body h4 mb-1">User Login</h2>
                            <p class="text-body-secondary small mb-0">Please enter your credentials to access the system.</p>
                        </div>
                        
                        <!--login form-->
                        <form action="action/login_logic.php" method="POST">
                            
                            <!-- User Identity Field -->
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control border border-secondary-subtle" id="student-id" name="user_id" placeholder="ID Number" required>
                                <label for="student-id" class="text-body-secondary">ID Number</label>
                            </div>

                            <!-- Security Code / Password Field -->
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control border border-secondary-subtle" id="pwd" name="user_password" placeholder="Password" required>
                                <label for="pwd" class="text-body-secondary">Password</label>
                            </div>

                            <!-- Accessibility & Session Persistence Controls -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input shadow-none" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label small text-body-secondary" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                <a href="#" class="small text-primary text-decoration-none fw-bold">Forgot Password?</a>
                            </div>

                            <!-- Action Intent Submission Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold py-3 rounded-3 fs-6" name="user_login">LOGIN</button>
                            </div>

                            <!-- Secondary Routing Onboarding Link -->
                            <div class="text-center mt-3">
                                <p class="small text-body-secondary mb-0">Don't have an account?
                                    <a href="register.php" class="text-primary fw-bold text-decoration-none ms-1">Register here</a>
                                </p>
                            </div>
                        </form>
                        <!--login form end-->
                        
                    </div>

                </div>
            </div>
            <!-- End of Unified Card Container -->

        </div>
    </main>
    <!--end of main content-->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Native Theme Context Orchestration Engine -->
    <script>
        const htmlElement = document.documentElement;
        const themeToggleBtn = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');

        // Parse default environmental profiles
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

            if (!themeIcon) return;
            
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill';
                if (themeToggleBtn) themeToggleBtn.title = 'Switch to Light Mode';
            } else {
                themeIcon.className = 'bi bi-moon-stars-fill';
                if (themeToggleBtn) themeToggleBtn.title = 'Switch to Dark Mode';
            }
        }
    </script>
</body>
</html>