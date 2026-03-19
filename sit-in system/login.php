<?php
// session start
session_start();
include("action/login_logic.php"); 
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
    <?php include("includes/header.html") ?>
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
                            <form action="action/login_logic.php" method="POST">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control bg-light border-0" id="student-id" name="user_id" required>
                                    <label for="student-id" class="text-secondary">ID Number</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control bg-light border-0" id="pwd" name="user_password" required>
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