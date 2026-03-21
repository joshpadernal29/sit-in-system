<?php
// session start if there is no session active
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }
require __DIR__ . '/../action/crud_functions.php';
require __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Fetch the student data
$student = getStudentById($conn, $id);

// 3. Safety Check: If student doesn't exist, redirect back to list
if (!$student) {
    header("Location: student_list.php?error=StudentNotFound");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Maintain Header Superiority */
        header, .navbar {
            z-index: 1050 !important;
            position: relative;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
        }

        .card-header-custom {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .avatar-preview {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">

    <?php include("../includes/adminHeader.php"); ?>

    <main class="container py-5">
        <div class="mb-4">
            <a href="student_list.php" class="btn btn-link text-decoration-none text-muted p-0 mb-2">
                <i class="bi bi-arrow-left"></i> Back to Registry
            </a>
            <h4 class="fw-bold text-dark">Edit Student Profile</h4>
            <p class="text-muted small">Update the information for Student ID: <span class="text-primary fw-bold"><?php echo $student['student_id'] ?></span></p>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                    <div class="mb-3">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['firstname'].'+'.$student['lastname']); ?>&background=0d6efd&color=fff&size=128" 
                             class="rounded-circle avatar-preview" alt="Student Photo">
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($student['firstname'] . " " . $student['lastname']); ?></h5>
                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill mb-3">
                        <?php echo htmlspecialchars($student['course']); ?>
                    </span>
                    <hr class="my-3 opacity-50">
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted d-block">Total Sit-ins</small>
                            <span class="fw-bold"><?php echo $student['sit_ins']; ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Year Level</small>
                            <span class="fw-bold"><?php echo $student['year_level']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header card-header-custom py-3 px-4">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>Personal & Academic Details</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="../action/crud_functions.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Student ID Number</label>
                                    <input type="text" class="form-control bg-light" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($student['firstname']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($student['lastname']); ?>">
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">Course</label>
                                    <input type="text" name="course" class="form-control" 
                                        value="<?php echo htmlspecialchars($student['course']); ?>" 
                                        required placeholder="e.g. BSIT">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Year Level</label>
                                    <select name="year_level" class="form-select" required>
                                        <?php for($i=1; $i<=4; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo ($student['year_level'] == $i) ? 'selected' : ''; ?>>Year <?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Total Sit-in Sessions</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-clock-history"></i></span>
                                        <input type="number" name="sit_ins" class="form-control" value="<?php echo $student['sit_ins']; ?>" min="0">
                                    </div>
                                    <div class="form-text text-info small">Current remaining sessions: <?php echo (30 - $student['sit_ins']); ?></div>
                                </div>
                            </div>

                            <div class="mt-5 d-flex gap-2">
                                <button type="submit" name="update_student" class="btn btn-primary px-4 shadow-sm">
                                    <i class="bi bi-save me-2"></i> Save Changes
                                </button>
                                <a href="studentList.php" class="btn btn-light border px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>