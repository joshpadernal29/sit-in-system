<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm py-1 sticky-top">
        <div class="container-fluid px-lg-4">

            <a class="navbar-brand d-flex align-items-center" href="student_dashboard.php" style="gap: 10px;">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; padding: 2px;">
                    <img src="../assets/uclogo2.png" class="rounded-circle"
                        style="width: 100%; height: 100%; object-fit: contain;" alt="UC Logo">
                </div>
                <div class="lh-1">
                    <span class="fw-bold d-block mb-0" style="font-size: 0.85rem; letter-spacing: 0.5px;">STUDENT
                        PORTAL</span>
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
                        <a class="nav-link px-2 text-white d-flex align-items-center" href="#"
                            style="font-size: 0.9rem;">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 text-white d-flex align-items-center" href="#"
                            style="font-size: 0.9rem;">
                            <i class="bi bi-person-circle me-2"></i>My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 text-white d-flex align-items-center" href="#"
                            style="font-size: 0.9rem;">
                            <i class="bi bi-clock-history me-2"></i>Sit-in History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 text-white d-flex align-items-center" href="#"
                            style="font-size: 0.9rem;">
                            <i class="bi bi-megaphone me-2"></i>Announcements
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-xl-block lh-1">
                        <!--MAKE THIS DYNAMIC-->
                        <span class="text-white fw-bold" style="font-size: 0.75rem;">User</span>
                        <br><small class="text-white-50" style="font-size: 0.65rem;">BSIT - 3rd Year</small>
                    </div>

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