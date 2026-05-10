<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../action/student_profile_logic.php';

$session_id = $_SESSION['user_id'] ?? null;

if (!$session_id) {
    die("Error: Session 'user_id' is empty. Please log in again.");
}

$student = getStudentDetails($conn, $session_id);

if ($student === null) {
    echo "<h3>Database Error</h3>";
    echo "The system found your Session ID (<b>$session_id</b>), but that ID does not exist in your 'students' table.<br>";
    echo "Check your database to see if the student_id actually exists.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile | CCS Sit-in System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background: #f5f7fb; }
        .main-content { margin-left: 260px; padding: 1.5rem; transition: .3s ease; }
        .sidebar.collapsed ~ .main-content { margin-left: 80px; }
        .profile-header-bg { height: 120px; background: #0d6efd; border-radius: 15px 15px 0 0; }
        .profile-avatar-container { margin-top: -60px; }
        .card { border: none; border-radius: 15px; }
        @media (max-width: 991px) { .main-content { margin-left: 0 !important; padding: 1rem; } }
    </style>
</head>

<body>
    <?php include("../includes/student_sidebar.php"); ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm overflow-hidden">
                        <div class="profile-header-bg"></div>
                        <div class="card-body text-center profile-avatar-container">
                            <img src="../assets/default_profile.jpg" class="rounded-circle border border-4 border-white shadow-sm mb-3" width="120" height="120">
                            <h4 class="fw-bold mb-1"><?= $student['firstname'] . " " . $student['lastname']; ?></h4>
                            <p class="text-muted small mb-3"><?= $student['student_id']; ?></p>
                            <hr>
                            <div class="text-start px-3">
                                <label class="small text-uppercase fw-bold text-muted">Current Course</label>
                                <p class="fw-semibold"><?= $student['course'] . " - Year " . $student['year_level']; ?></p>
                            </div>
                            <hr>
                            <div class="text-start px-3">
                                <label class="small text-uppercase fw-bold text-muted">Sessions Available</label>
                                <p class="fw-semibold"><?= $student['sit_ins']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm p-4 p-md-5">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                            <h3 class="fw-bold m-0">Edit Profile</h3>
                            <a href="studentDashboard.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
                        </div>

                        <?php if (isset($_GET['update']) && $_GET['update'] == 'success'): ?>
                            <div class="alert alert-success d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i>Profile updated successfully!</div>
                        <?php endif; ?>

                        <form action="../action/student_profile_logic.php" method="POST">
                            <input type="hidden" name="id_to_update" value="<?= $student['student_id']; ?>">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted">First Name</label>
                                    <input type="text" name="firstname" class="form-control form-control-lg" value="<?= htmlspecialchars($student['firstname']); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted">Middle Name</label>
                                    <input type="text" name="middlename" class="form-control form-control-lg" value="<?= htmlspecialchars($student['middlename'] ?? ''); ?>" placeholder="Optional">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted">Last Name</label>
                                    <input type="text" name="lastname" class="form-control form-control-lg" value="<?= htmlspecialchars($student['lastname']); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted">Email Address</label>
                                    <input type="email" name="email" class="form-control form-control-lg" value="<?= htmlspecialchars($student['email']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted">Course</label>
                                    <input type="text" name="course" class="form-control form-control-lg" value="<?= htmlspecialchars($student['course']); ?>" placeholder="e.g. BSIT">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted">Year Level</label>
                                    <select name="year_level" class="form-select form-select-lg">
                                        <option value="1" <?= ($student['year_level'] == '1') ? 'selected' : ''; ?>>1st Year</option>
                                        <option value="2" <?= ($student['year_level'] == '2') ? 'selected' : ''; ?>>2nd Year</option>
                                        <option value="3" <?= ($student['year_level'] == '3') ? 'selected' : ''; ?>>3rd Year</option>
                                        <option value="4" <?= ($student['year_level'] == '4') ? 'selected' : ''; ?>>4th Year</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-4 pt-3 border-top">
                                    <button type="submit" name="update_profile" class="btn btn-primary btn-lg px-5">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>