<?php
include("action/register_logic.php")
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Register Account</title>
    <style>
        /* Automatically makes your left main logo pop cleanly in Dark Mode */
        [data-bs-theme="dark"] .login-page-logo {
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
        <div class="container mt">
            <div class="container-fluid min-vh-100 d-flex align-items-center">
                <div class="container">
                    <div class="row align-items-center justify-content-center g-4 g-lg-5">

                        <div class="col-12 col-lg-5 text-center">
                            <img src="assets/ccsmainlogo2.png" alt="Logo" class="img-fluid mb-4 login-page-logo"
                                style="max-width: 220px;">
                            <h2 class="fw-bold text-body">CCS Lab Registration</h2>
                            <p class="text-secondary d-none d-sm-block">Provide your university credentials to set up your
                                monitoring profile and access laboratory resources.</p>
                        </div>

                        <div class="col-12 col-lg-7">
                            <!-- Enhanced layout definition with dynamic border-light-subtle for visibility in dark mode -->
                            <div class="card border border-light-subtle bg-body shadow-lg p-4 rounded-4">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <h3 class="fw-bold mb-1 text-body">Register Account</h3>
                                        <p class="text-secondary small" id="step-indicator">Step 1 of 4: Personal Info</p>
                                        <div class="progress" style="height: 6px;">
                                            <div id="form-progress" class="progress-bar bg-success" style="width: 25%;">
                                            </div>
                                        </div>
                                    </div>
                                    <!--register form-->
                                    <form id="multiStepForm" action="action/register_logic.php" method="post">
                                        
                                        <!-- Step 1 Layout Panel -->
                                        <div class="form-step" id="step-1">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control bg-body-tertiary border-0" id="student-id" placeholder="ID"
                                                    required name="reg_student_id">
                                                <label class="text-secondary">Student ID Number</label>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-12 col-md-4">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control bg-body-tertiary border-0" id="fname"
                                                            placeholder="First" required name="reg_fname">
                                                        <label class="text-secondary">Firstname</label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control bg-body-tertiary border-0" id="mname"
                                                            placeholder="M" name="reg_mname">
                                                        <label class="text-nowrap text-secondary">M.I. <span
                                                                class="small opacity-50">(Optional)</span></label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-5">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control bg-body-tertiary border-0" id="lname"
                                                            placeholder="Last" required name="reg_lname">
                                                        <label class="text-secondary">Lastname</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-grid">
                                                <button type="button" class="btn btn-primary btn-lg"
                                                    onclick="goToStep(2)">Continue</button>
                                            </div>
                                        </div>

                                        <!-- Step 2 Layout Panel -->
                                        <div class="form-step d-none" id="step-2">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control bg-body-tertiary border-0" id="regCourse"
                                                    placeholder="Course" required name="reg_course">
                                                <label class="text-secondary">Course / Program</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <select class="form-select bg-body-tertiary border-0 text-body" id="regCourseLvl" name="reg_lvl">
                                                    <option value="1">First Year</option>
                                                    <option value="2">Second Year</option>
                                                    <option value="3">Third Year</option>
                                                    <option value="4">Fourth Year</option>
                                                </select>
                                                <label class="text-secondary">Year Level</label>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-secondary w-50"
                                                    onclick="goToStep(1)">Back</button>
                                                <button type="button" class="btn btn-primary w-50"
                                                    onclick="goToStep(3)">Next</button>
                                            </div>
                                        </div>

                                        <!-- Step 3 Layout Panel -->
                                        <div class="form-step d-none" id="step-3">
                                            <div class="form-floating mb-3">
                                                <input type="email" class="form-control bg-body-tertiary border-0" id="regEmail"
                                                    placeholder="Email" required name="reg_email">
                                                <label class="text-secondary">School Email Address</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <textarea class="form-control bg-body-tertiary border-0" id="regAddress" style="height: 100px"
                                                    placeholder="Address" name="reg_address"></textarea>
                                                <label class="text-secondary">Home Address</label>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-secondary w-50"
                                                    onclick="goToStep(2)">Back</button>
                                                <button type="button" class="btn btn-primary w-50"
                                                    onclick="goToStep(4)">Next</button>
                                            </div>
                                        </div>

                                        <!-- Step 4 Layout Panel -->
                                        <div class="form-step d-none" id="step-4">
                                            <div class="form-floating mb-3">
                                                <input type="password" class="form-control bg-body-tertiary border-0" id="regPass"
                                                    placeholder="Password" required name="reg_password">
                                                <label class="text-secondary">Create Password</label>
                                            </div>
                                            <div class="form-floating mb-4">
                                                <input type="password" class="form-control bg-body-tertiary border-0" id="confPass"
                                                    placeholder="Confirm" required name="confirm_password">
                                                <label class="text-secondary">Confirm Password</label>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-secondary w-50"
                                                    onclick="goToStep(3)">Back</button>
                                                <button type="submit" class="btn btn-success w-50" name="reg_btn">Finish Registration</button>
                                            </div>
                                        </div>

                                        <div class="text-center mt-4">
                                            <p class="small text-muted">Already have an account? <a href="login.php"
                                                    class="text-primary fw-bold text-decoration-none">Login</a></p>
                                        </div>
                                    </form>
                                    <!--register form end-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!--end of main content-->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

                const titles = ["Personal Identity", "Academic Details", "Contact Information", "Security"];
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