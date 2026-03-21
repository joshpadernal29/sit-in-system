<?php
// session start if there is no session active
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

require_once __DIR__ . '/../action/crud_functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$student = getStudentById($conn, $id);

// 4. Safety check: If the student doesn't exist, go back to the list
if (!$student) {
    header("Location: studentList.php?error=not_found");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Delete | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Maintain Header Superiority for Dropdowns */
        header, .navbar {
            z-index: 1050 !important;
            position: relative;
        }

        .delete-card {
            max-width: 500px;
            margin: 60px auto;
        }

        .warning-icon {
            font-size: 3.5rem;
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
        }

        .student-preview-box {
            background-color: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 12px;
        }
    </style>
</head>
<body class="bg-light">

    <?php include("../includes/adminHeader.php"); ?>

    <main class="container">
        <div class="card border-0 shadow-lg rounded-4 delete-card overflow-hidden">
            <div class="card-body p-5 text-center">
                
                <div class="warning-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>

                <h3 class="fw-bold text-dark">Delete Student?</h3>
                <p class="text-muted">This action is permanent and cannot be undone. All sit-in records for this student will be removed.</p>

                <div class="student-preview-box p-3 my-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['firstname'].'+'.$student['lastname']); ?>&background=random&size=48" 
                             class="rounded-circle me-3" alt="avatar">
                        <div class="text-start">
                            <div class="fw-bold text-dark mb-0">
                                <?php echo htmlspecialchars($student['firstname'] . " " . $student['lastname']); ?>
                            </div>
                            <small class="text-muted d-block">ID: <?php echo htmlspecialchars($student['student_id']); ?></small>
                        </div>
                    </div>
                </div>

                <form action="../action/crud_functions.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="confirm_delete" class="btn btn-danger btn-lg py-3 fw-bold shadow-sm">
                            <i class="bi bi-trash3 me-2"></i> Permanently Delete
                        </button>
                        <a href="studentList.php" class="btn btn-light btn-lg py-3 border">
                            No, Keep Student
                        </a>
                    </div>
                </form>

            </div>
            
            <div class="card-footer bg-light border-0 py-3 text-center">
                <small class="text-muted"><i class="bi bi-shield-lock me-1"></i> Admin Authorization Required</small>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>