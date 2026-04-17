<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../action/sit_in.php'; 

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$active_list = current_sit_in($conn, $limit, $offset);
$total_rows = mysqli_num_rows(current_sit_in($conn)); 
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Active Records | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; border: none; }
        .table thead th { background-color: #212529; color: white; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
        .status-pulse { width: 8px; height: 8px; background: #198754; border-radius: 50%; display: inline-block; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); } 100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); } }
        /* Ensure dropdowns aren't hidden by table responsive containers */
        .table-responsive { overflow: visible !important; }
    </style>
</head>
<body>
    <?php include("../includes/adminHeader.php"); ?>

    <main class="container py-4">
        <div class="mb-4">
            <h2 class="fw-bold mb-0">Active Sit-in Records</h2>
            <div class="d-flex align-items-center mt-1">
                <span class="status-pulse me-2"></span>
                <span class="text-success small fw-bold"><?php echo $total_rows; ?> Students In Lab</span>
            </div>
        </div>

        <div class="card shadow-sm table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Student ID</th>
                        <th>Lab</th>
                        <th>Language</th>
                        <th>Time In</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_rows > 0): 
                        while($row = mysqli_fetch_assoc($active_list)): ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?php echo htmlspecialchars($row['student_id_str']); ?></td>
                        <td><span class="badge bg-primary-subtle text-primary px-3 rounded-pill">Lab <?php echo $row['lab']; ?></span></td>
                        <td><code><?php echo $row['language']; ?></code></td>
                        <td><?php echo date('h:i A', strtotime($row['login_time'])); ?></td>
                        <td class="text-center">
                            <form action="../action/sit_in.php" method="POST" onsubmit="return confirm('End Session?');">
                                <input type="hidden" name="record_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="logout_student" class="btn btn-danger btn-sm px-3 rounded-pill">
                                    Logout Student
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No active sessions.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>