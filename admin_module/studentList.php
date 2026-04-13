<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/crud_functions.php");
// call function to read students from db
$students = getStudents($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registry | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* 1. FIX: Ensures the Header Dropdown is always above the table content */
        header, .navbar {
            z-index: 1050 !important;
            position: relative;
        }

        /* 2. FIX: Allows dropdowns to "pop out" of the card and table */
        .card { overflow: visible !important; }
        .table-responsive { 
            overflow-x: auto; 
            overflow-y: visible !important; 
        }

        /* Redesign Styles */
        .student-id {
            font-family: 'Monaco', 'Consolas', monospace;
            color: #0d6efd;
            font-size: 0.85rem;
            background: #f0f7ff;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .table thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }

        .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; color: #198754 !important; }
        .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; color: #0dcaf0 !important; }
        .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; color: #ffc107 !important; }

        .btn-white { background: #fff; border: 1px solid #dee2e6; }
        .btn-white:hover { background: #f8f9fa; }
        
        tr { transition: background 0.2s; }
        tr:hover { background-color: rgba(13, 110, 253, 0.02) !important; }
    </style>
</head>
<body class="bg-light">
    <!--HEADER-->
    <?php include("../includes/adminHeader.php"); ?>

    <main class="container-fluid py-4 px-lg-5">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="fw-bold mb-1">Student Registry</h4>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end gap-2 mt-3 mt-md-0">
                <div class="input-group shadow-sm" style="max-width: 280px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Search students...">
                </div>
                <a class="btn btn-primary shadow-sm" href="add_student.php"><i class="bi bi-plus-lg me-1"></i> Add</a>
                <form action="../action/crud_functions.php" method="post"> 
                    <button class="btn btn-outline-danger shadow-sm" name="reset_session" type="submit" onclick="return confirm('Reset all student Sessions?');">Reset Session</buton>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-bold">Student ID</th>
                            <th class="py-3 text-muted fw-bold">Student Profile</th>
                            <th class="py-3 text-muted fw-bold">Course-Year</th>
                            <th class="py-3 text-muted fw-bold text-center">Sessions</th>
                            <th class="pe-4 py-3 text-muted fw-bold text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach($students as $student): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="student-id fw-bold"><?php echo htmlspecialchars($student['student_id']); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['firstname'].'+'.$student['lastname']); ?>&background=0d6efd&color=fff&size=32" 
                                             class="rounded-circle me-3 border" alt="avatar">
                                        <div class="lh-1">
                                            <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($student['firstname'] . " " . $student['lastname']); ?></div>
                                            <small class="text-muted">Verified Registry</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark"><?php echo htmlspecialchars($student['course']); ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Year: <?php echo htmlspecialchars($student['year_level']); ?></div>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $sit_ins = (int)$student['sit_ins'];
                                        $status = ($sit_ins > 10) ? 'success' : (($sit_ins > 5) ? 'info' : 'warning');
                                    ?>
                                    <span class="badge rounded-pill bg-<?php echo $status; ?>-subtle px-3 py-2">
                                        <?php echo $sit_ins; ?> Sit-ins
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group">
                                        <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-white text-primary px-3" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_student.php?id=<?php echo $student['id']; ?>" 
                                           class="btn btn-sm btn-white text-danger px-3" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-people text-muted opacity-25" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">No students found in the database.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top py-3 px-4 rounded-bottom-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="text-muted small mb-0">Total: <strong><?php echo count($students); ?></strong> Students</p>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>