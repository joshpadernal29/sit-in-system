<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Sit-In Monitoring System</title>
    <style>
        html {
            scroll-behavior: smooth;
        }
        /* Custom timeline line for the 'How it works' workflow */
        .workflow-steps .col-lg-4:not(:last-child) {
            position: relative;
        }
        @media (min-width: 992px) {
            .workflow-steps .col-lg-4:not(:last-child)::after {
                content: '';
                position: absolute;
                top: 2.5rem;
                right: -15%;
                width: 30%;
                height: 2px;
                background-color: var(--bs-border-color-translucent);
                z-index: 1;
            }
        }

        html {
        scroll-behavior: smooth;
        /* Tells the browser to stop scrolling slightly before the element to clear your sticky navbar space */
        scroll-padding-top: 90px; 
        }
    </style>
</head>

<!-- Add data-bs-spy and data-bs-target to activate the scrolling underline tracker -->
<body class="bg-body text-body" data-bs-spy="scroll" data-bs-target="#mainNav" data-bs-offset="100">

    <!-- Navbar -->
    <?php include("includes/header.html") ?>
    <!-- End Navbar -->

    <!-- 1. HOME SECTION -->
    <header id="home" class="py-5 my-5">
        <div class="container px-5 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">
                    <span class="badge bg-primary-subtle text-primary mb-4 px-3 py-2 rounded-pill fw-semibold border border-primary-subtle">
                        <i class="bi bi-cpu me-1"></i> Smart Lab Ecosystem
                    </span>
                    <h1 class="display-3 text-body tracking-tight mb-3" style="letter-spacing: -1px; font-weight: 800;">
                        The modern way to track <span class="text-primary">laboratory hours.</span>
                    </h1>
                    <p class="lead text-body-secondary fs-4 mx-auto mb-5 mt-4" style="max-width: 700px;">
                        An elegant, automated check-in pipeline designed for students and administrators to view live terminal queues, track sit-in balances, and manage log schedules seamlessly.
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 mb-5">
                        <a class="btn btn-primary btn-lg px-5 py-3 fw-semibold shadow-sm fs-6" href="login.php">
                            Get Started Now <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a class="btn btn-link link-secondary text-decoration-none fw-medium" href="#about">
                            Learn how it works <i class="bi bi-chevron-down ms-1"></i>
                        </a>
                    </div>
                    <div class="row g-3 justify-content-center mt-5 pt-3">
                        <div class="col-6 col-md-3">
                            <div class="py-3 px-2 bg-body-tertiary border border-light-subtle rounded-4">
                                <span class="d-block fs-3 fw-bold text-primary"><i class="bi bi-lightning-charge"></i></span>
                                <small class="text-body-secondary fw-medium">Instant Logs</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="py-3 px-2 bg-body-tertiary border border-light-subtle rounded-4">
                                <span class="d-block fs-3 fw-bold text-primary"><i class="bi bi-pc-display"></i></span>
                                <small class="text-body-secondary fw-medium">Live Matrix</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="py-3 px-2 bg-body-tertiary border border-light-subtle rounded-4">
                                <span class="d-block fs-3 fw-bold text-primary"><i class="bi bi-shield-check"></i></span>
                                <small class="text-body-secondary fw-medium">Secure Portal</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. ABOUT / HOW IT WORKS SECTION -->
    <section id="about" class="py-5 bg-body-tertiary border-top border-bottom border-light-subtle">
        <div class="container px-5 my-5">
            <div class="text-center mb-5 pb-3">
                <span class="text-primary fw-bold text-uppercase small tracking-wider">System Pipeline</span>
                <h2 class="display-5 fw-bold mt-1" style="letter-spacing: -1px;">How the System Works</h2>
                <p class="text-body-secondary mx-auto" style="max-width: 550px;">A transparent overview of your laboratory journey from registration to final session feedback.</p>
            </div>

            <!-- Stage 1: Registration, Tracking, & Reservations -->
            <div class="row g-5 workflow-steps justify-content-center text-center text-lg-start mb-5">
                <div class="col-md-6 col-lg-4">
                    <div class="z-3 position-relative">
                        <div class="bg-primary text-white rounded-4 shadow-sm mb-4 d-inline-flex align-items-center justify-content-center" style="width: 4.5rem; height: 4.5rem;">
                            <i class="bi bi-person-plus-fill fs-3"></i>
                        </div>
                        <h4 class="fw-bold h5">1. Quick Registration</h4>
                        <p class="text-body-secondary small pe-lg-3">Create your account with your student ID credentials to open up access to all available laboratory networks instantly.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="z-3 position-relative">
                        <div class="bg-primary text-white rounded-4 shadow-sm mb-4 d-inline-flex align-items-center justify-content-center" style="width: 4.5rem; height: 4.5rem;">
                            <i class="bi bi-pc-display-horizontal fs-3"></i>
                        </div>
                        <h4 class="fw-bold h5">2. Reserve a Terminal</h4>
                        <p class="text-body-secondary small pe-lg-3">Browse open workstations remotely via our real-time grid mapping, and secure a specific PC before arriving.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="z-3 position-relative">
                        <div class="bg-primary text-white rounded-4 shadow-sm mb-4 d-inline-flex align-items-center justify-content-center" style="width: 4.5rem; height: 4.5rem;">
                            <i class="bi bi-hourglass-split fs-3"></i>
                        </div>
                        <h4 class="fw-bold h5">3. Live Sit-In Tracking</h4>
                        <p class="text-body-secondary small">Authenticate at your node to track your hours automatically. Your dashboard updates your balance dynamically.</p>
                    </div>
                </div>
            </div>

            <!-- Stage 2: Feedback Loop Callouts -->
            <div class="row g-4 mt-4 justify-content-center">
                <div class="col-md-6">
                    <div class="p-4 bg-body border border-light-subtle rounded-4 d-flex gap-3 align-items-start shadow-sm">
                        <div class="text-primary bg-primary-subtle p-2 px-3 rounded-3"><i class="bi bi-chat-left-heart-fill fs-4"></i></div>
                        <div>
                            <h5 class="fw-bold h6 mb-1">Add Sit-In Feedback</h5>
                            <p class="text-body-secondary small m-0">Encountered an error or system defect? Log structural feedback instantly on your session logout form for tech review.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 bg-body border border-light-subtle rounded-4 d-flex gap-3 align-items-start shadow-sm">
                        <div class="text-primary bg-primary-subtle p-2 px-3 rounded-3"><i class="bi bi-chat-square-quote-fill fs-4"></i></div>
                        <div>
                            <h5 class="fw-bold h6 mb-1">Post Public Testimonials</h5>
                            <p class="text-body-secondary small m-0">Share your overall platform user experiences to help our engineering team continuously upgrade the laboratory ecosystem.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 3. REDESIGNED CLEAN TESTIMONIALS SECTION -->
    <section id="community" class="py-5 bg-body">
        <div class="container px-5 my-5">
            <div class="row justify-content-between align-items-center mb-5">
                <div class="col-md-6 text-center text-md-start">
                    <span class="text-primary fw-bold text-uppercase small tracking-wider">Community Voiced</span>
                    <h2 class="display-5 fw-bold mt-1 mb-0" style="letter-spacing: -1px;">Student Perspectives</h2>
                </div>
                <div class="col-md-5 text-center text-md-start mt-2 mt-md-0">
                    <p class="text-body-secondary m-0">Real opinions shared by developers and engineering students utilizing the sit-in tracking framework daily.</p>
                </div>
            </div>

            <!-- Elegant Typography Centered Grid -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-4 bg-body-tertiary border border-light-subtle rounded-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-warning small mb-3"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                            <p class="text-body small lh-base mb-4">"The absolute best feature is reserving a PC ahead of time. I can lock down my preferred development computer terminal straight from my mobile browser."</p>
                        </div>
                        <div class="d-flex align-items-center pt-3 border-top border-light-subtle">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2.5 small" style="width: 32px; height: 32px; font-size: 0.75rem;">JD</div>
                            <div>
                                <h6 class="mb-0 fw-bold small">John Doe</h6>
                                <span class="text-body-secondary d-block" style="font-size: 0.7rem;">IT Major</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-body-tertiary border border-light-subtle rounded-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-warning small mb-3"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                            <p class="text-body small lh-base mb-4">"Registering was practically instant. Being able to track my remaining balance directly inside my portal completely beats manual logbooks."</p>
                        </div>
                        <div class="d-flex align-items-center pt-3 border-top border-light-subtle">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2.5 small" style="width: 32px; height: 32px; font-size: 0.75rem;">MS</div>
                            <div>
                                <h6 class="mb-0 fw-bold small">Maria Santos</h6>
                                <span class="text-body-secondary d-block" style="font-size: 0.7rem;">CS Student</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-body-tertiary border border-light-subtle rounded-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-warning small mb-3"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i></div>
                            <p class="text-body small lh-base mb-4">"Adding specific room-level feedback during check-out lets the administrators know right away if a keyboard or mouse breaks down on a certain node position."</p>
                        </div>
                        <div class="d-flex align-items-center pt-3 border-top border-light-subtle">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2.5 small" style="width: 32px; height: 32px; font-size: 0.75rem;">AS</div>
                            <div>
                                <h6 class="mb-0 fw-bold small">Alex Smith</h6>
                                <span class="text-body-secondary d-block" style="font-size: 0.7rem;">CpE Senior</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-body-tertiary py-4 border-top border-light-subtle">
        <div class="container px-5 text-center">
            <div class="small text-body-secondary">Copyright &copy; Josh Efraim C. Padernal 2026</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Theme Logic Management Engine -->
    <script>
        const htmlElement = document.documentElement;
        const themeToggleBtn = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');

        // Check local storage or system preference on load
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        const initialTheme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
        setTheme(initialTheme);

        // Click Event Listener
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });

        // Function to handle layout changes dynamically
        function setTheme(theme) {
            htmlElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);

            // Dynamically change the icon graphic interface
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill';
                themeToggleBtn.title = 'Switch to Light Mode';
            } else {
                themeIcon.className = 'bi bi-moon-stars-fill';
                themeToggleBtn.title = 'Switch to Dark Mode';
            }
        }
    </script>
</body>

</html>