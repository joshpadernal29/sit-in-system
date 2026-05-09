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
</head>
<body class="bg-light">

    <?php include("../includes/admin_sidebar.php"); ?>
    
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="p-4 mb-4 bg-white rounded shadow-sm border">
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-person-badge me-2 text-primary"></i>Sit-in Entry</h4>
                    <form action="" method="GET" class="input-group input-group-lg">
                        <input type="text" name="search_id" class="form-control" placeholder="Enter Student ID" value="<?php echo htmlspecialchars($search_query); ?>" required>
                        <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search me-2"></i>Search</button>
                    </form>
                </div>

                <?php if ($student): ?>
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="text-uppercase fw-bold">Verification Successful</span>
                        <span class="badge bg-primary">Active</span>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if ($student['res_id']): ?>
                        <div class="alert alert-warning border-dark rounded-0 d-flex align-items-center mb-4">
                            <i class="bi bi-bookmark-check-fill fs-3 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">RESERVATION DETECTED</h6>
                                <p class="mb-0 small">Assigned to <strong>PC-<?php echo $student['pc_number']; ?></strong> in <strong><?php echo $student['res_lab']; ?></strong>.</p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <form action="../action/sit_in.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                            <input type="hidden" name="student_id_str" value="<?php echo $student['student_id']; ?>">
                            <input type="hidden" name="firstname" value="<?php echo $student['firstname']; ?>">
                            <input type="hidden" name="lastname" value="<?php echo $student['lastname']; ?>">
                            <input type="hidden" name="res_id" value="<?php echo $student['res_id'] ?? ''; ?>">

                            <div class="row align-items-center mb-4">
                                <div class="col-auto">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['firstname'].'+'.$student['lastname']); ?>&size=70&rounded=true" alt="Student">
                                </div>
                                <div class="col">
                                    <h3 class="mb-0 fw-bold"><?php echo htmlspecialchars($student['firstname'] . " " . $student['lastname']); ?></h3>
                                    <p class="text-muted mb-0">ID: <?php echo htmlspecialchars($student['student_id']); ?> | <?php echo htmlspecialchars($student['course']); ?></p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Programming Language</label>
                                    <select name="language" class="form-select" required>
                                        <option value="Java">Java</option>
                                        <option value="Python">Python</option>
                                        <option value="PHP" selected>PHP</option>
                                        <option value="C#">C#</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Laboratory</label>
                                    <select name="lab" class="form-select" required>
                                        <option value="Lab 542" <?php echo (strpos($student['res_lab'], '542') !== false) ? 'selected' : ''; ?>>Lab 542</option>
                                        <option value="Lab 544" <?php echo (strpos($student['res_lab'], '544') !== false) ? 'selected' : ''; ?>>Lab 544</option>
                                        <option value="Lab 526" <?php echo (strpos($student['res_lab'], '526') !== false) ? 'selected' : ''; ?>>Lab 526</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="form-label fw-bold small text-uppercase">Remaining Sessions</label>
                                    <input type="number" name="sit_ins" class="form-control fw-bold text-primary" value="<?php echo $student['sit_ins']; ?>" min="0">
                                </div>
                            </div>

                            <button type="submit" name="update_sitin_session" class="btn btn-success btn-lg w-100 mt-4 py-3 shadow-sm fw-bold">
                                <i class="bi bi-box-arrow-in-right me-2"></i> START SESSION
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>