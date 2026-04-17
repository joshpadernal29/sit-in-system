<?php 
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../action/sit_in.php'; 

$selected_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : null;
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$history_list = past_sit_in($conn, $selected_date, $limit, $offset);
$total_rows = mysqli_num_rows(past_sit_in($conn, $selected_date)); 
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sit-in History | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; border: none; }
        .table thead th { background-color: #212529; color: white; text-transform: uppercase; font-size: 0.8rem; }
        .duration-text { font-weight: 700; color: #0d6efd; }
    </style>
</head>
<body>
    <?php include("../includes/adminHeader.php"); ?>
    <main class="container py-4">
        <h2 class="fw-bold mb-4">Past Records & Feedback</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-auto"><input type="date" name="filter_date" class="form-control form-control-sm" value="<?php echo $selected_date; ?>"></div>
                    <div class="col-auto"><button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">Filter</button></div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Details</th>
                        <th>Lab</th>
                        <th>Duration</th>
                        <th class="text-center">Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_rows > 0): 
                        while($row = mysqli_fetch_assoc($history_list)): 
                            $hasFeedback = !empty($row['feedback']);
                            $diff = (new DateTime($row['login_time']))->diff(new DateTime($row['logout_time']));
                    ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold"><?php echo htmlspecialchars($row['fullname']); ?></div>
                            <small class="text-muted"><?php echo $row['student_id_str']; ?></small>
                        </td>
                        <td><span class="badge bg-primary-subtle text-primary px-3 rounded-pill">Lab <?php echo $row['lab']; ?></span></td>
                        <td>
                            <div class="duration-text"><?php echo $diff->format('%h hr %i min'); ?></div>
                            <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('M d, Y', strtotime($row['login_time'])); ?></small>
                        </td>
                        <td class="text-center">
                            <?php if($hasFeedback): ?>
                                <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold" 
                                    data-bs-toggle="modal" data-bs-target="#fbModal" 
                                    data-student="<?php echo $row['fullname']; ?>" 
                                    data-text="<?php echo htmlspecialchars($row['feedback']); ?>">
                                    <i class="bi bi-chat-left-text-fill"></i> Read
                                </button>
                            <?php else: ?>
                                <button class="btn btn-light btn-sm rounded-pill px-3 text-muted border" disabled>None</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="4" class="text-center py-5 text-muted">No history found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div class="modal fade" id="fbModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold">Session Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-1">Feedback from:</p>
                    <h5 id="fbName" class="fw-bold mb-3"></h5>
                    <div class="p-3 bg-light rounded-3 border">
                        <p id="fbContent" class="mb-0 text-dark italic"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const fbModal = document.getElementById('fbModal');
        fbModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            document.getElementById('fbName').textContent = btn.getAttribute('data-student');
            document.getElementById('fbContent').textContent = '"' + btn.getAttribute('data-text') + '"';
        });
    </script>
</body>
</html>