<?php 
require_once __DIR__ . '/../action/sit_in.php'; 

//Capture the filter date if provided
$selected_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sit-in Records | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .nav-pills .nav-link.active { background-color: #0d6efd; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
        .table-hover tbody tr:hover { background-color: rgba(0,0,0,.02); }
    </style>
</head>
<body class="bg-light">
    <?php include("../includes/adminHeader.php"); ?>

    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0 text-dark">Sit-in Records</h2>
                <p class="text-muted mb-0">Monitor and manage laboratory sessions.</p>
            </div>
            <a href="sitin_management.php" class="btn btn-primary px-4 py-2 shadow-sm">
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
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4">Student ID</th>
                                    <th>Full Name</th>
                                    <th>Lab</th>
                                    <th>Language</th>
                                    <th>Time In</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $active_list = current_sit_in($conn);
                                if(mysqli_num_rows($active_list) > 0):
                                    while($row = mysqli_fetch_assoc($active_list)): 
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($row['student_id_str']); ?></td>
                                    <td><?php echo htmlspecialchars($student['firstname']. "" .$student['lastname']); ?></td>
                                    <td><span class="badge bg-info text-dark px-3"><?php echo $row['lab']; ?></span></td>
                                    <td><?php echo $row['language']; ?></td>
                                    <td><?php echo date('h:i A', strtotime($row['login_time'])); ?></td>
                                    <td class="text-center">
                                        <form action="../action/sit_in.php" method="POST" onsubmit="return confirm('Log this student out?');">
                                            <input type="hidden" name="record_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="logout_student" class="btn btn-sm btn-outline-danger px-3">
                                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-people display-4 d-block mb-2"></i>
                                        No students currently in the laboratory.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="history">
                
                <div class="card border-0 shadow-sm mb-3 bg-white">
                    <div class="card-body">
                        <form action="" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-uppercase">Filter by Date</label>
                                <input type="date" name="filter_date" class="form-control" 
                                       value="<?php echo htmlspecialchars($selected_date); ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-dark px-4">
                                    <i class="bi bi-filter me-2"></i>Filter Records
                                </button>
                                <?php if ($selected_date): ?>
                                    <a href="sit_in_list.php" class="btn btn-link text-decoration-none">Clear Filter</a>
                                <?php endif; ?>
                            </div>
                            <?php if ($selected_date): ?>
                            <div class="col-md-4 text-md-end">
                                <span class="text-muted small">Viewing records for: <strong><?php echo date('F j, Y', strtotime($selected_date)); ?></strong></span>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Student Details</th>
                                    <th>Lab / Language</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $history_list = past_sit_in($conn, $selected_date);
                                if(mysqli_num_rows($history_list) > 0):
                                    while($row = mysqli_fetch_assoc($history_list)): 
                                        $start = new DateTime($row['login_time']);
                                        $end = new DateTime($row['logout_time']);
                                        $diff = $start->diff($end);
                                ?>
                                <tr>
                                    <td class="ps-4"><?php echo date('M d, Y', strtotime($row['login_time'])); ?></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($row['fullname']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['student_id_str']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo $row['lab']; ?> <br>
                                        <span class="badge bg-light text-dark border fw-normal mt-1"><?php echo $row['language']; ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary"><?php echo $diff->format('%h hr %i min'); ?></div>
                                        <small class="text-muted">
                                            <?php echo date('h:i A', strtotime($row['login_time'])); ?> - <?php echo date('h:i A', strtotime($row['logout_time'])); ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-calendar-x display-4 d-block mb-2"></i>
                                        No history found for the selected criteria.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // SMART TAB PERSISTENCE
        // If the URL has 'filter_date', automatically switch to the History tab on load
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('filter_date')) {
                var someTabTriggerEl = document.querySelector('#history-tab')
                var tab = new bootstrap.Tab(someTabTriggerEl)
                tab.show()
            }
        });
    </script>
</body>
</html>