<?php 
require_once __DIR__ . '/../action/sit_in.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sit-in Records | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <?php include("../includes/adminHeader.php"); ?>

    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Sit-in Records</h2>
            <a href="sitin_management.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Entry
            </a>
        </div>

        <ul class="nav nav-pills mb-4 bg-white p-2 rounded shadow-sm" id="sitinTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="active-tab" data-bs-toggle="pill" data-bs-target="#active" type="button">
                    <i class="bi bi-broadcast me-2"></i>Currently Sitting In
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="history-tab" data-bs-toggle="pill" data-bs-target="#history" type="button">
                    <i class="bi bi-clock-history me-2"></i>Past Records
                </button>
            </li>
        </ul>

        <div class="tab-content" id="sitinTabsContent">
            
            <div class="tab-pane fade show active" id="active">
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Student ID</th>
                                    <th>Full Name</th>
                                    <th>Lab</th>
                                    <th>Language</th>
                                    <th>Time In</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $active_list = current_sit_in($conn);
                                if(mysqli_num_rows($active_list) > 0):
                                    while($row = mysqli_fetch_assoc($active_list)): 
                                ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($row['student_id_str']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                    <td><span class="badge bg-info text-dark"><?php echo $row['lab']; ?></span></td>
                                    <td><?php echo $row['language']; ?></td>
                                    <td><?php echo date('h:i A', strtotime($row['login_time'])); ?></td>
                                    <td>
                                        <form action="../action/sit_in.php" method="POST">
                                            <input type="hidden" name="record_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="logout_student" class="btn btn-sm btn-outline-danger">Logout</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No students currently in the lab.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="history">
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>Date</th>
                                    <th>Student ID</th>
                                    <th>Full Name</th>
                                    <th>Lab / Language</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $history_list = past_sit_in($conn);
                                while($row = mysqli_fetch_assoc($history_list)): 
                                    // Calculate Duration for a professional touch
                                    $start = new DateTime($row['login_time']);
                                    $end = new DateTime($row['logout_time']);
                                    $diff = $start->diff($end);
                                ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($row['login_time'])); ?></td>
                                    <td><?php echo $row['student_id_str']; ?></td>
                                    <td><?php echo $row['fullname']; ?></td>
                                    <td>
                                        <?php echo $row['lab']; ?> <br>
                                        <small class="badge bg-light text-dark border"><?php echo $row['language']; ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo $diff->format('%h hr %i min'); ?></div>
                                        <small class="text-muted">
                                            <?php echo date('h:i A', strtotime($row['login_time'])); ?> - 
                                            <?php echo date('h:i A', strtotime($row['logout_time'])); ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>