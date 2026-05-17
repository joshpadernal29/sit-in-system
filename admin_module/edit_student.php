<?php
require __DIR__ . '/../action/crud_functions.php';
require __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$student = getStudentById($conn, $id);

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
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
body{
    background:#f5f7fb;
    margin:0;
}

/* sidebar spacing */
.main-content{
    margin-left:260px;
    padding:2rem;
}

.page-wrap{
    max-width:1100px;
    margin:0 auto;
}

/* title spacing */
.top-title{
    margin-bottom:1.2rem;
}

/* PROFILE CARD FIX */
.profile-card{
    text-align:center;
    padding:2rem 1.5rem;
    display:flex;
    flex-direction:column;
    align-items:center; /* centers everything */
}

/* avatar */
.avatar-preview{
    width:90px;
    height:90px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #fff;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

/* form */
.form-label{
    font-size:.85rem;
    font-weight:600;
    color:#495057;
}

.action-buttons{
    display:flex;
    gap:.75rem;
    margin-top:1.8rem;
}

.card{
    border:0;
    border-radius:16px;
}
</style>
</head>

<body>

<?php include("../includes/admin_sidebar.php"); ?>

<main class="main-content">

<div class="page-wrap">

    <!-- HEADER -->
    <div class="top-title">
        <h4 class="fw-bold mb-1">Edit Student Profile</h4>
        <p class="text-muted small mb-0">
            Student ID:
            <span class="text-primary fw-bold">
                <?php echo $student['student_id']; ?>
            </span>
        </p>
    </div>

    <div class="row g-4 align-items-start">

        <!-- LEFT PROFILE CARD -->
        <div class="col-lg-4">
            <div class="card shadow-sm profile-card">

                <!-- CENTERED IMAGE FIX -->
                <div class="d-flex justify-content-center w-100 mb-3">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['firstname'].'+'.$student['lastname']); ?>&background=0d6efd&color=fff&size=128"
                         class="avatar-preview">
                </div>

                <h5 class="fw-bold mb-1">
                    <?php echo htmlspecialchars($student['firstname']." ".$student['lastname']); ?>
                </h5>

                <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill mb-3">
                    <?php echo htmlspecialchars($student['course']); ?>
                </span>

                <hr class="w-100">

                <div class="row w-100 text-center">
                    <div class="col-6">
                        <small class="text-muted">Sit-ins</small>
                        <div class="fw-bold"><?php echo $student['sit_ins']; ?></div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Year</small>
                        <div class="fw-bold"><?php echo $student['year_level']; ?></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- RIGHT FORM -->
        <div class="col-lg-8">

            <div class="card shadow-sm p-4">
                <form action="../action/crud_functions.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                    <!-- MESSAGE ALERT BANNER -->
                    <?php if (isset($_GET['msg'])): ?>
                        <div class="col-md-12 mb-3">
                            <?php if ($_GET['msg'] == 'confirm_required'): ?>
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Please confirm your new password.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php elseif ($_GET['msg'] == 'password_mismatch'): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-x-circle-fill me-2"></i> Passwords do not match! Please try again.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php elseif ($_GET['msg'] == 'success'): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i> Student profile updated successfully!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Student ID</label>
                            <input type="text" name="student_id" class="form-control bg-light"
                                   value="<?php echo htmlspecialchars($student['student_id']); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="firstname" class="form-control"
                                   value="<?php echo htmlspecialchars($student['firstname']); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lastname" class="form-control"
                                   value="<?php echo htmlspecialchars($student['lastname']); ?>">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Course</label>
                            <input type="text" name="course" class="form-control"
                                   value="<?php echo htmlspecialchars($student['course']); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Year Level</label>
                            <select name="year_level" class="form-select">
                                <?php for($i=1;$i<=4;$i++): ?>
                                    <option value="<?php echo $i; ?>"
                                        <?php echo ($student['year_level']==$i)?'selected':''; ?>>
                                        Year <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Sit-ins</label>
                            <input type="number" name="sit_ins" class="form-control"
                                   value="<?php echo $student['sit_ins']; ?>">
                        </div>

                        <!-- NEW PASSWORD FIELDS -->
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Leave blank to keep current password">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" 
                                   placeholder="Repeat new password">
                        </div>

                    </div>

                    <!-- BUTTONS -->
                    <div class="action-buttons">

                        <button type="submit" name="update_student" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i> Save Changes
                        </button>

                        <a href="studentList.php" class="btn btn-light border">
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

</main>

</body>
</html>