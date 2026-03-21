<?php 
// Include the new logic file instead of the general crud one
require __DIR__ . '/../action/sit_in.php';

$student = null;
$search_query = "";

// 2. Handle the Search Trigger
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
        .search-section { background: #fff; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .student-card { border: none; border-radius: 20px; transition: all 0.3s ease; }
        .session-badge { font-size: 1.2rem; padding: 10px 20px; border-radius: 10px; }
    </style>
</head>
<body class="bg-light">

    <?php include("../includes/adminHeader.php"); ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="search-section p-4 mb-4">
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-person-badge me-2 text-primary"></i>Sit-in Entry</h4>
                    <form action="" method="GET" class="input-group input-group-lg">
                        <input type="text" name="search_id" class="form-control border-end-0" 
                               placeholder="Enter Student ID (e.g. 22-1234-567)" 
                               value="<?php echo htmlspecialchars($search_query); ?>" required>
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="bi bi-search me-2"></i>Search
                        </button>
                    </form>
                </div>

                <?php if ($student): ?>
                <div class="card shadow-lg student-card overflow-hidden">
                    <div class="card-header bg-dark text-white py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-uppercase fw-bold tracking-wider">Verification Successful</span>
                            <span class="badge bg-primary">Active Student</span>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <form action="../action/sit_in.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                            <input type="hidden" name="student_id_str" value="<?php echo $student['student_id']; ?>">

                            <div class="row align-items-center mb-4">
                                <div class="col-auto">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['firstname'].'+'.$student['lastname']); ?>&size=80&background=random&rounded=true" alt="Student">
                                </div>
                                <div class="col">
                                    <h3 class="mb-0 fw-bold"><?php echo htmlspecialchars($student['firstname'] . " " . $student['lastname']); ?></h3>
                                    <p class="text-muted mb-0">ID: <?php echo htmlspecialchars($student['student_id']); ?> | <?php echo htmlspecialchars($student['course']); ?></p>
                                </div>
                            </div>

                            <hr>

                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Language Used</label>
                                    <select name="language" class="form-select form-select-lg shadow-sm" required>
                                        <option value="Java">Java</option>
                                        <option value="Python">Python</option>
                                        <option value="C#">C#</option>
                                        <option value="PHP">PHP</option>
                                        <option value="C++">C++</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Laboratory</label>
                                    <select name="lab" class="form-select form-select-lg shadow-sm" required>
                                        <option value="Lab 542">Lab 542</option>
                                        <option value="Lab 544">Lab 544</option>
                                        <option value="Lab 524">Lab 524</option>
                                        <option value="Lab 526">Lab 526</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <div class="p-3 border rounded-3 bg-light">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <label class="form-label fw-bold mb-0">Remaining Sessions</label>
                                                <p class="small text-muted mb-0">Admin can manually adjust this count.</p>
                                            </div>
                                            <div class="col-auto">
                                                <input type="number" name="sit_ins" class="form-control form-control-lg text-center fw-bold text-primary" 
                                                       style="width: 100px;" value="<?php echo $student['sit_ins']; ?>" min="0" max="30">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <button type="submit" name="update_sitin_session" class="btn btn-success btn-lg w-100 py-3 shadow">
                                    <i class="bi bi-check2-circle me-2"></i> Confirm & Log Sit-in Session
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php elseif (isset($_GET['search_id'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm text-center p-4">
                        <i class="bi bi-person-x-fill display-4 d-block mb-3"></i>
                        <h5 class="fw-bold">Student Not Found</h5>
                        <p class="mb-0">The ID "<?php echo htmlspecialchars($search_query); ?>" does not exist in the UC Sit-in Registry.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>