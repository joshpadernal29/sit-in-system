<?php
include("action/register_logic.php")
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Register Account</title>
</head>

<body>
    <!--navbar-->
    <?php include("includes/header.html") ?>
    <!--end of navbar-->
    <!--main content-->
    <main>
        <div class="container mt-3">
            <div class="container-fluid min-vh-100 d-flex align-items-center">
                <div class="container">
                    <div class="row align-items-center justify-content-center g-4 g-lg-5">

                        <div class="col-12 col-lg-5 text-center">
                            <img src="assets/ccsmainlogo2.png" alt="Logo" class="img-fluid mb-4"
                                style="max-width: 220px;">
                            <h2 class="fw-bold">CCS Lab Registration</h2>
                            <p class="text-muted d-none d-sm-block">Provide your university credentials to set up your
                                monitoring profile
                                and access laboratory resources.</p>
                        </div>

                        <div class="col-12 col-lg-7">
                            <div class="card border-0 shadow-lg p-4">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <h3 class="fw-bold mb-1">Register Account</h3>
                                        <p class="text-secondary small" id="step-indicator">Step 1 of 4: Personal Info
                                        </p>
                                        <div class="progress" style="height: 6px;">
                                            <div id="form-progress" class="progress-bar bg-success" style="width: 25%;">
                                            </div>
                                        </div>
                                    </div>
                                    <!--register form-->
                                    <form id="multiStepForm" action="action/register_logic.php" method="post">
                                        <div class="form-step" id="step-1">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="student-id" placeholder="ID"
                                                    required name="reg_student_id">
                                                <label>Student ID Number</label>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-12 col-md-4">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" id="fname"
                                                            placeholder="First" required name="reg_fname">
                                                        <label>Firstname</label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" id="mname"
                                                            placeholder="M" name="reg_mname">
                                                        <label class="text-nowrap">M.I. <span
                                                                class="small opacity-50">(Optional)</span></label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-5">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" id="lname"
                                                            placeholder="Last" required name="reg_lname">
                                                        <label>Lastname</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-grid">
                                                <button type="button" class="btn btn-primary btn-lg"
                                                    onclick="goToStep(2)">Continue</button>
                                            </div>
                                        </div>

                                        <div class="form-step d-none" id="step-2">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="regCourse"
                                                    placeholder="Course" required name="reg_course">
                                                <label>Course / Program</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <select class="form-select" id="regCourseLvl" name="reg_lvl">
                                                    <option value="1">First Year</option>
                                                    <option value="2">Second Year</option>
                                                    <option value="3">Third Year</option>
                                                    <option value="4">Fourth Year</option>
                                                </select>
                                                <label>Year Level</label>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-secondary w-50"
                                                    onclick="goToStep(1)">Back</button>
                                                <button type="button" class="btn btn-primary w-50"
                                                    onclick="goToStep(3)">Next</button>
                                            </div>
                                        </div>

                                        <div class="form-step d-none" id="step-3">
                                            <div class="form-floating mb-3">
                                                <input type="email" class="form-control" id="regEmail"
                                                    placeholder="Email" required name="reg_email">
                                                <label>School Email Address</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <textarea class="form-control" id="regAddress" style="height: 100px"
                                                    placeholder="Address" name="reg_address"></textarea>
                                                <label>Home Address</label>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-secondary w-50"
                                                    onclick="goToStep(2)">Back</button>
                                                <button type="button" class="btn btn-primary w-50"
                                                    onclick="goToStep(4)">Next</button>
                                            </div>
                                        </div>

                                        <div class="form-step d-none" id="step-4">
                                            <div class="form-floating mb-3">
                                                <input type="password" class="form-control" id="regPass"
                                                    placeholder="Password" required name="reg_password">
                                                <label>Create Password</label>
                                            </div>
                                            <div class="form-floating mb-4">
                                                <input type="password" class="form-control" id="confPass"
                                                    placeholder="Confirm" required name="confirm_password">
                                                <label>Confirm Password</label>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-secondary w-50"
                                                    onclick="goToStep(3)">Back</button>
                                                <button type="submit" class="btn btn-success w-50" name="reg_btn">Finish
                                                    Registration</button>
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

            // 1. Get all required inputs in the CURRENT step
            const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');

            // 2. Check validity (only if moving forward)
            let allValid = true;
            if (stepNumber > currentStepNum) {
                inputs.forEach(input => {
                    if (!input.checkValidity()) {
                        input.reportValidity(); // Shows the "Please fill out this field" bubble
                        allValid = false;
                    }
                });
            }

            // 3. If valid (or moving backward), proceed
            if (allValid || stepNumber < currentStepNum) {
                // Hide all steps
                document.querySelectorAll('.form-step').forEach(step => step.classList.add('d-none'));

                // Show target step
                document.getElementById('step-' + stepNumber).classList.remove('d-none');

                // Update Progress Bar
                const progress = (stepNumber / 4) * 100;
                const progressBar = document.getElementById('form-progress');
                progressBar.style.width = progress + '%';

                // Change color based on completion
                if (stepNumber === 4) {
                    progressBar.classList.replace('bg-success', 'bg-info');
                } else {
                    progressBar.classList.replace('bg-info', 'bg-success');
                }

                // Update Text
                const titles = ["Personal Identity", "Academic Details", "Contact Information", "Security"];
                document.getElementById('step-indicator').innerText = `Step ${stepNumber} of 4: ${titles[stepNumber - 1]}`;
            }
        }
    </script>
</body>

</html>