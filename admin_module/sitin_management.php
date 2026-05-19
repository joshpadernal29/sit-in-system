<?php 
require __DIR__ . '/../action/sit_in.php';
$student = null;
$search_query = "";

if (isset($_GET['search_id'])) {
    $search_query = $_GET['search_id'];
    $student = searchStudentById($conn, $search_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Entry | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* --- DARK MODE ADAPTATION --- */
        body {
            background-color: var(--bg-body) !important;
            color: var(--text-main);
            transition: background 0.3s, color 0.3s;
        }

        .main-text { color: var(--text-main); }
        .text-muted-custom { color: var(--text-muted) !important; }

        /* Card & Container Styling */
        .card-custom {
            background-color: var(--bg-sidebar) !important;
            border: 1px solid var(--border-color) !important;
            transition: 0.3s;
        }

        /* Form Controls */
        .form-control, .form-select {
            background-color: var(--bg-body) !important;
            color: var(--text-main) !important;
            border: 1px solid var(--border-color) !important;
        }
        .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.7;
        }

        /* Sidebar Layout Adjustments */
        main.container-fluid {
            margin-left: 260px;
            width: calc(100% - 260px);
            transition: 0.3s ease;
            padding: 2rem;
        }
        .sidebar.collapsed ~ main.container-fluid {
            margin-left: 80px;
            width: calc(100% - 80px);
        }

        /* Verification Alert Box */
        .alert-reservation {
            background-color: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.5);
            border-left: 5px solid #ffc107;
            color: var(--text-main);
            transition: 0.3s ease;
        }

        /* Keep icon yellow */
        .alert-reservation i {
            color: #ffc107;
        }

        /* Make all text inside follow theme color */
        .alert-reservation h6,
        .alert-reservation p,
        .alert-reservation strong {
            color: var(--text-main);
        }

        /* LIGHT MODE */
        body.light-mode .alert-reservation {
            background-color: #fff8e1;
        }

        /* DARK MODE */
        body.dark-mode .alert-reservation {
            background-color: rgba(255, 193, 7, 0.15);
        }



        @media (max-width: 991px) {
            main.container-fluid { margin-left: 0 !important; width: 100% !important; }
        }
    </style>
</head>
<body>

    <?php include("../includes/admin_sidebar.php"); ?>
    
    <main class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-xl-6">
                
                <!-- Search Section -->
                <div class="p-4 mb-4 rounded-4 shadow-sm card-custom">
                    <h4 class="fw-bold main-text mb-3">
                        <i class="bi bi-person-badge me-2 text-primary"></i>Sit-in Entry
                    </h4>
                    <form action="" method="GET" class="input-group input-group-lg">
                        <input type="text" name="search_id" class="form-control" 
                               placeholder="Enter Student ID (e.g. 21-1234-567)" 
                               value="<?php echo htmlspecialchars($search_query); ?>" required>
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>

                <?php if ($student): ?>
                <!-- Student Details Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden card-custom">
                    <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="text-uppercase fw-bold small tracking-wider">Verification Successful</span>
                        <span class="badge bg-white text-primary">Found</span>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if ($student['res_id']): ?>
                        <div class="alert alert-reservation rounded-3 d-flex align-items-center mb-4">
                            <i class="bi bi-bookmark-check-fill fs-3 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">RESERVATION DETECTED</h6>
                                <p class="mb-0 small opacity-75">Assigned to <strong>PC-<?php echo $student['pc_number']; ?></strong> in <strong><?php echo $student['res_lab']; ?></strong>.</p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <form action="../action/sit_in.php" method="POST">
                            <!-- Hidden Fields -->
                            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                            <input type="hidden" name="student_id_str" value="<?php echo $student['student_id']; ?>">
                            <input type="hidden" name="firstname" value="<?php echo $student['firstname']; ?>">
                            <input type="hidden" name="lastname" value="<?php echo $student['lastname']; ?>">
                            <input type="hidden" name="res_id" value="<?php echo $student['res_id'] ?? ''; ?>">

                            <!-- Student Identity Header -->
                            <div class="d-flex align-items-center mb-4">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['firstname'].'+'.$student['lastname']); ?>&size=100&rounded=true&background=0d6efd&color=fff" 
                                     alt="Student" class="me-3 border border-2 border-primary p-1 rounded-circle">
                                <div>
                                    <h3 class="mb-0 fw-bold main-text"><?php echo htmlspecialchars($student['firstname'] . " " . $student['lastname']); ?></h3>
                                    <p class="text-muted-custom mb-0"><?php echo htmlspecialchars($student['student_id']); ?> • <?php echo htmlspecialchars($student['course']); ?></p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted-custom text-uppercase">Programming Language</label>
                                    <select name="language" class="form-select" required>
                                        <option value="Java">Java</option>
                                        <option value="Python">Python</option>
                                        <option value="PHP" selected>PHP</option>
                                        <option value="C#">C#</option>
                                        <option value="N/A">N/A</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted-custom text-uppercase">Laboratory</label>
                                    <select name="lab" class="form-select" required>
                                        <option value="Lab 542" <?php echo (strpos($student['res_lab'], '542') !== false) ? 'selected' : ''; ?>>Lab 542</option>
                                        <option value="Lab 544" <?php echo (strpos($student['res_lab'], '544') !== false) ? 'selected' : ''; ?>>Lab 544</option>
                                        <option value="Lab 526" <?php echo (strpos($student['res_lab'], '526') !== false) ? 'selected' : ''; ?>>Lab 526</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="form-label fw-bold small text-muted-custom text-uppercase">Session Credits Remaining</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
                                        <input type="number" name="sit_ins" class="form-control fw-bold" 
                                               value="<?php echo $student['sit_ins']; ?>" min="0">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" name="update_sitin_session" class="btn btn-success btn-lg w-100 mt-4 py-3 shadow-sm fw-bold">
                                <i class="bi bi-box-arrow-in-right me-2"></i> START SESSION
                            </button>
                        </form>
                    </div>
                </div>

                <?php elseif (!empty($search_query)): ?>
                <!-- Not Found State -->
                <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 rounded-4 p-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-danger"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Student Not Found</h6>
                        <p class="mb-0 small opacity-75">No record matches <strong><?php echo htmlspecialchars($search_query); ?></strong>. Ensure the ID is correct and registered.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>