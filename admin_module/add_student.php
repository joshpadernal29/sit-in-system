<?php require_once __DIR__ . '/../action/crud_functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include("../includes/adminHeader.php"); ?>

    <main class="container py-5">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mx-auto" style="max-width: 800px;">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">Register New Student</h5>
            </div>
            <div class="card-body p-4">
                <form action="../action/crud_functions.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ID Number</label>
                            <input type="text" name="student_id" class="form-control" placeholder="22-1234-567" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="student@uc.edu.ph" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">First Name</label>
                            <input type="text" name="firstname" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Middle Name</label>
                            <input type="text" name="middlename" class="form-control" placeholder="(Optional)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Last Name</label>
                            <input type="text" name="lastname" class="form-control" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-bold">Course</label>
                            <input type="text" name="course" class="form-control" placeholder="BSIT" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Year Level</label>
                            <select name="year_level" class="form-select" required>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Home Address</label>
                            <textarea name="home_address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                            <div class="form-text">Set a secure password for the student portal.</div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" name="add_student" class="btn btn-primary px-5 shadow-sm">
                            <i class="bi bi-person-plus-fill me-2"></i> Register Student
                        </button>
                        <a href="studentList.php" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>