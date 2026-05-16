<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("action/register_logic.php");
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Register Account - CCS Sit-in Monitoring</title>
    <style>
        /* Smooth Global Theme Transitions */
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Forces Equal Structural Columns inside the Large Unified Card Layout */
        .register-card .row.g-0 {
            min-height: 620px; /* Forces both left and right sides to stretch to this minimum height */
        }

        /* Enhanced Dark Mode Card Properties */
        [data-bs-theme="dark"] .register-card {
            background-color: var(--bs-body-bg) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 1.5rem 4rem rgba(0, 0, 0, 0.6) !important;
        }

        /* Form Control Improvements under Dark Mode */
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .form-control:disabled {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border-color: rgba(255, 255, 255, 0.18) !important;
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            border-color: #0d6efd !important;
            background-color: rgba(255, 255, 255, 0.04) !important;
        }

        /* Focus Ring Glow Animation */
        .form-floating .form-control,
        .form-floating .form-select {
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .form-floating .form-control:focus,
        .form-floating .form-select:focus {
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
            <div class="card register-card border border-secondary-subtle bg-body shadow-lg rounded-4 overflow-hidden w-100" style="max-width: 1100px;">
                <div class="row g-0">
                    
                    <!-- Left Column: Center-aligned Branding Pane (Equal Vertical Stretch) -->
                    <div class="col-12 col-md-6 bg-body-secondary d-flex flex-column justify-content-center align-items-center p-4 p-md-5 border-end border-secondary-subtle text-center">
                        <!-- Dynamic Circular Logo Wrapper -->
                        <div class="logo-wrapper mb-4 rounded-circle">
                            <img src="assets/ccsmainlogo2.png" alt="CCS Logo" class="img-fluid login-page-logo">
                        </div>
                        <h1 class="fw-bold text-body display-6 mb-3">CCS Lab Registration</h1>
                        <p class="text-body-secondary lead fs-6 mb-0 px-xl-4">
                            Provide your university credentials to set up your monitoring profile and access laboratory resources.
                        </p>
                    </div>

                    <!-- Right Column: Multi-step Form Block (Equal Vertical Stretch) -->
                    <div class="col-12 col-md-6 d-flex flex-column justify-content-center p-4 p-md-5">
                        
                        <!-- Progress Header Block -->
                        <div class="mb-4">
                            <h2 class="fw-bold text-body h4 mb-1">Register Account</h2>
                            <p class="text-body-secondary small mb-2" id="step-indicator">Step 1 of 4: Personal Info</p>
                            <div class="progress" style="height: 6px;">
                                <div id="form-progress" class="progress-bar bg-success" style="width: 25%;"></div>
                            </div>
                        </div>
                        
                        <!--register form-->
                        <form id="multiStepForm" action="action/register_logic.php" method="post">
                            
                            <!-- Step 1 Layout Panel -->
                            <div class="form-step" id="step-1">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control border border-secondary-subtle" id="student-id" placeholder="ID" required name="reg_student_id">
                                    <label class="text-body-secondary">Student ID Number</label>
                                </div>
                                <div class="row g-2 mb-4">
                                    <div class="col-12 col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border border-secondary-subtle" id="fname" placeholder="First" required name="reg_fname">
                                            <label class="text-body-secondary">Firstname</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border border-secondary-subtle" id="mname" placeholder="M" name="reg_mname">
                                            <label class="text-nowrap text-body-secondary">M.I. <span class="small opacity-50">(Optional)</span></label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-5">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border border-secondary-subtle" id="lname" placeholder="Last" required name="reg_lname">
                                            <label class="text-body-secondary">Lastname</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="button" class="btn btn-primary btn-lg shadow-sm fw-bold py-3 rounded-3 fs-6" onclick="goToStep(2)">Continue</button>
                                </div>
                            </div>

                            <!-- Step 2 Layout Panel -->
                            <div class="form-step d-none" id="step-2">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control border border-secondary-subtle" id="regCourse" placeholder="Course" required name="reg_course">
                                    <label class="text-body-secondary">Course / Program</label>
                                </div>
                                <div class="form-floating mb-4">
                                    <select class="form-select border border-secondary-subtle text-body" id="regCourseLvl" name="reg_lvl">
                                        <option value="1">First Year</option>
                                        <option value="2">Second Year</option>
                                        <option value="3">Third Year</option>
                                        <option value="4">Fourth Year</option>
                                    </select>
                                    <label class="text-body-secondary">Year Level</label>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary w-50 py-3 rounded-3 fw-bold" onclick="goToStep(1)">Back</button>
                                    <button type="button" class="btn btn-primary w-50 py-3 rounded-3 fw-bold shadow-sm" onclick="goToStep(3)">Next</button>
                                </div>
                            </div>

                            <!-- Step 3 Layout Panel -->
                            <div class="form-step d-none" id="step-3">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control border border-secondary-subtle" id="regEmail" placeholder="Email" required name="reg_email">
                                    <label class="text-body-secondary">School Email Address</label>
                                </div>
                                <div class="form-floating mb-4">
                                    <textarea class="form-control border border-secondary-subtle" id="regAddress" style="height: 100px" placeholder="Address" name="reg_address"></textarea>
                                    <label class="text-body-secondary">Home Address</label>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary w-50 py-3 rounded-3 fw-bold" onclick="goToStep(2)">Back</button>
                                    <button type="button" class="btn btn-primary w-50 py-3 rounded-3 fw-bold shadow-sm" onclick="goToStep(4)">Next</button>
                                </div>
                            </div>

                            <!-- Step 4 Layout Panel -->
                            <div class="form-step d-none" id="step-4">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control border border-secondary-subtle" id="regPass" placeholder="Password" required name="reg_password">
                                    <label class="text-body-secondary">Create Password</label>
                                </div>
                                <div class="form-floating mb-4">
                                    <input type="password" class="form-control border border-secondary-subtle" id="confPass" placeholder="Confirm" required name="confirm_password">
                                    <label class="text-body-secondary">Confirm Password</label>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary w-50 py-3 rounded-3 fw-bold" onclick="goToStep(3)">Back</button>
                                    <button type="submit" class="btn btn-success w-50 py-3 rounded-3 fw-bold shadow-sm" name="reg_btn">Finish</button>
                                </div>
                            </div>

                            <!-- Secondary Navigation Route Link -->
                            <div class="text-center mt-4">
                                <p class="small text-body-secondary mb-0">Already have an account? 
                                    <a href="login.php" class="text-primary fw-bold text-decoration-none ms-1">Login</a>
                                </p>
                            </div>
                        </form>
                        <!--register form end-->
                        
                    </div>

                </div>
            </div>
            <!-- End of Unified Card Container -->

        </div>
    </main>
    <!--end of main content-->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Multi-Step Navigation Validation Router -->
    <script>
        function goToStep(stepNumber) {
            const currentStepNum = stepNumber > 1 ? stepNumber - 1 : 1;
            const currentStepEl = document.getElementById('step-' + currentStepNum);
            const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');

            let allValid = true;
            if (stepNumber > currentStepNum) {
                inputs.forEach(input => {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        allValid = false;
                    }
                });
            }

            if (allValid || stepNumber < currentStepNum) {
                document.querySelectorAll('.form-step').forEach(step => step.classList.add('d-none'));
                document.getElementById('step-' + stepNumber).classList.remove('d-none');

                const progress = (stepNumber / 4) * 100;
                const progressBar = document.getElementById('form-progress');
                progressBar.style.width = progress + '%';

                if (stepNumber === 4) {
                    progressBar.classList.replace('bg-success', 'bg-info');
                } else {
                    progressBar.classList.replace('bg-info', 'bg-success');
                }

                const titles = ["Personal Info", "Academic Details", "Contact Information", "Security"];
                document.getElementById('step-indicator').innerText = `Step ${stepNumber} of 4: ${titles[stepNumber - 1]}`;
            }
        }
    </script>

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
                if (brandText) brandText.className = 'fw-bold d-block mb-0 h6 text-body';
                if (brandSub) brandSub.className = 'text-body-secondary text-uppercase fw-semibold';
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
                if (brandText) brandText.className = 'fw-bold d-block mb-0 h6 text-white';
                if (brandSub) brandSub.className = 'text-white-50 text-uppercase fw-semibold';
            }
        }
    </script>
</body>

</html>