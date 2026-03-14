<?php
include("../login_logic.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>login page</title>
</head>

<body>
    <!--navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm py-2 sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.html">
                <div class="bg-white rounded-circle shadow d-flex align-items-center justify-content-center p-1 me-3"
                    style="width: 65px; height: 65px; border: 2px solid rgba(255,255,255,0.3);">
                    <img src="assets/uclogo2.png" class="rounded-circle"
                        style="width: 100%; height: 100%; object-fit: contain;" alt="UC Logo">
                </div>

                <div class="lh-1">
                    <span class="fw-bold d-block mb-0 h6 text-white" style="letter-spacing: 0.5px;">
                        SIT-IN MONITORING SYSTEM
                    </span>
                    <small class="text-white-50 text-uppercase fw-semibold"
                        style="font-size: 0.65rem; letter-spacing: 1px;">
                        University of Cebu Main-Campus
                    </small>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto nav-underline mb-3 mb-lg-0">
                    <li class="nav-item"><a class="nav-link fw-medium px-3 text-white" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium px-3 text-white" href="#">About</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link fw-medium px-3 text-white dropdown-toggle" href="#"
                            data-bs-toggle="dropdown">Community</a>
                        <ul class="dropdown-menu border-0 shadow-sm mt-2">
                            <li><a class="dropdown-item py-2" href="#">Announcements</a></li>
                            <li><a class="dropdown-item py-2" href="#">Lab Guidelines</a></li>
                            <li><a class="dropdown-item py-2" href="#">Student Hub</a></li>
                        </ul>
                    </li>
                </ul>

                <div class="d-grid d-lg-flex align-items-center gap-2 pb-3 pb-lg-0">
                    <a href="login.html"
                        class="btn btn-link text-white text-decoration-none fw-medium px-3 text-center">Login</a>
                    <a href="register.html"
                        class="btn btn-light text-primary fw-bold px-4 rounded-pill shadow-sm">Register</a>
                </div>
            </div>
        </div>
    </nav>
    <!--end of navbar-->
    <!--main content-->
    <main class="bg-light">
        <div class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
            <div class="row w-100 justify-content-center align-items-center g-4">

                <div class="col-12 col-lg-6 text-center mb-3 mb-lg-0">
                    <img src="assets/ccsmainlogo2.png" alt="CCS Logo" class="img-fluid mb-3"
                        style="max-width: 180px; width: 40%;">
                    <h1 class="fw-bold">CCS Sit-in Monitoring</h1>
                    <p class="text-secondary d-none d-md-block">Ensuring an organized laboratory experience for every
                        student.</p>
                </div>

                <div class="col-12 col-md-8 col-lg-5">
                    <div class="card border-0 shadow-lg p-2 p-md-3 rounded-4">
                        <div class="card-body">
                            <div class="mb-4 text-center text-lg-start">
                                <h2 class="fw-bold text-dark h4">User Login</h2>
                                <p class="text-muted small">Please enter your credentials to access the system.</p>
                            </div>
                            <!--login form-->
                            <form action="login_logic.php" method="POST">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control bg-light border-0" id="student-id"
                                        placeholder="STUDENT ID" required>
                                    <label for="student-id" class="text-secondary">Student ID Number</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control bg-light border-0" id="pwd"
                                        placeholder="Password" required>
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
                                        class="btn btn-primary btn-lg shadow-sm fw-bold py-3 rounded-3">LOGIN</button>
                                </div>

                                <div class="text-center mt-4">
                                    <p class="small text-muted">Don't have an account?
                                        <a href="register.html"
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
</body>

</html>