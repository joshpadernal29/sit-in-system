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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Active Records | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* --- CORE THEME ADAPTATION --- */
        body { 
            background-color: var(--bg-body) !important; 
            color: var(--text-main);
            transition: background 0.3s, color 0.3s;
        }

        .main-text { color: var(--text-main) !important; }
        .text-muted-custom { color: var(--text-muted) !important; }

        /* Card Styling */
        .card-custom { 
            background-color: var(--bg-sidebar) !important; 
            border: 1px solid var(--border-color) !important;
            border-radius: 12px; 
            overflow: hidden;
        }

        /* --- TABLE DARK MODE FIXES --- */
        .table-custom {
            --bs-table-bg: transparent !important;
            --bs-table-color: var(--text-main) !important;
            border-color: var(--border-color) !important;
        }

        .table-custom thead th { 
            background-color: rgba(0, 0, 0, 0.2) !important; 
            color: var(--text-muted) !important; 
            text-transform: uppercase; 
            font-size: 0.75rem; 
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-color) !important;
        }

        [data-theme="dark"] .table-custom thead th {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        .table-custom td {
            color: var(--text-main) !important;
            border-bottom: 1px solid var(--border-color) !important;
        }

        /* Status & UI Elements */
        .status-pulse { 
            width: 8px; 
            height: 8px; 
            background: #198754; 
            border-radius: 50%; 
            display: inline-block; 
            animation: pulse 2s infinite; 
        }

        @keyframes pulse { 
            0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); } 
            70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); } 
            100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); } 
        }

        .badge-lab {
            background-color: rgba(13, 110, 253, 0.15) !important;
            color: #0d6efd !important;
            border: 1px solid rgba(13, 110, 253, 0.3);
        }

        code {
            background-color: rgba(255, 255, 255, 0.05);
            padding: 2px 6px;
            border-radius: 4px;
            color: #e83e8c;
        }

        /* Modal Theme Overrides for custom background variables */
        .modal-content-custom {
            background-color: var(--bg-sidebar) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }
        .modal-header-custom { border-bottom: 1px solid var(--border-color) !important; }
        .modal-footer-custom { border-top: 1px solid var(--border-color) !important; }

        /* Sidebar Spacing */
        .main-content {
            margin-left: 260px; 
            transition: 0.3s ease;
        }
        @media (max-width: 991px) {
            .main-content { margin-left: 0 !important; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR COMPONENT -->
    <?php include("../includes/admin_sidebar.php"); ?>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="main-content">
        <main class="container-fluid py-4 px-lg-5">
            
            <!-- HEADER -->
            <div class="mb-4">
                <h2 class="fw-bold mb-0 main-text">Active Sit-in Records</h2>
                <div class="d-flex align-items-center mt-1">
                    <span class="status-pulse me-2"></span>
                    <span class="text-success small fw-bold"><?php echo $total_rows; ?> Students Currently in Lab</span>
                </div>
            </div>

            <!-- TABLE CARD -->
            <div class="card card-custom shadow-sm">
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Student ID</th>
                                <th>Lab</th>
                                <th>Language</th>
                                <th>Time In</th>
                                <th class="text-center">Task Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($total_rows > 0): 
                                while($row = mysqli_fetch_assoc($active_list)): ?>
                            <tr>
                                <td class="ps-4 fw-bold main-text">
                                    <?php echo htmlspecialchars($row['student_id_str']); ?>
                                </td>
                                <td>
                                    <span class="badge badge-lab px-3 rounded-pill">
                                        Lab <?php echo $row['lab']; ?>
                                    </span>
                                </td>
                                <td>
                                    <code><?php echo htmlspecialchars($row['language']); ?></code>
                                </td>
                                <td class="main-text">
                                    <?php echo date('h:i A', strtotime($row['login_time'])); ?>
                                </td>
                                
                                <!-- NEW: Live Inline Task Status View Badge -->
                                <td class="text-center">
                                    <?php if (isset($row['task_status']) && $row['task_status'] === 'Completed'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill fw-bold" style="font-size: 0.75rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Completed
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 rounded-pill fw-bold" style="font-size: 0.75rem;">
                                            <i class="bi bi-hourglass-split me-1"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <!-- Modal Trigger Button replaces direct form submit -->
                                    <button type="button" class="btn btn-danger btn-sm px-3 rounded-pill shadow-sm" 
                                            data-bs-toggle="modal" data-bs-target="#logoutGradingModal<?php echo $row['id']; ?>">
                                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                                    </button>
                                </td>
                            </tr>

                            <!-- DYNAMIC ACTION GRADING MODAL PER STUDENT ROW -->
                            <div class="modal fade" id="logoutGradingModal<?php echo $row['id']; ?>" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content modal-content-custom shadow-lg">
                                        <div class="modal-header modal-header-custom p-4">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-award text-primary me-2"></i>Session Evaluation & Logout</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="../action/sit_in.php" method="POST">
                                            <div class="modal-body p-4">
                                                <p class="text-muted-custom small mb-4">Grade operational tasks and station management parameters before finalizing the logout for student <strong><?php echo htmlspecialchars($row['student_id_str']); ?></strong>.</p>
                                                
                                                <!-- Hidden Form Passports -->
                                                <input type="hidden" name="record_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="student_pk_id" value="<?php echo $row['student_pk_id']; ?>">

                                                <!-- 1. Task Completed Dropdown Selection (20% Weight Formula Component) -->
                                                <div class="mb-4 p-3 rounded-3" style="background-color: rgba(255,255,255,0.03); border: 1px solid var(--border-color);">
                                                    <label class="form-label mb-1 fw-bold small main-text d-block">Task Completion Flag</label>
                                                    <small class="text-muted-custom d-block mb-2" style="font-size: 0.75rem;">Did the student complete their assigned laboratory tasks?</small>
                                                    <select name="task_status" class="form-select bg-transparent main-text border-secondary-subtle" style="font-size: 0.9rem;">
                                                        <option value="Pending" class="bg-dark text-white" <?php echo (!isset($row['task_status']) || $row['task_status'] === 'Pending') ? 'selected' : ''; ?>>❌ Pending / Unfinished (0 pts)</option>
                                                        <option value="Completed" class="bg-dark text-white" <?php echo (isset($row['task_status']) && $row['task_status'] === 'Completed') ? 'selected' : ''; ?>>✅ Verified Completed (10 pts)</option>
                                                    </select>
                                                </div>

                                                <!-- 2. Behavioral Condition Slider Module (60% Weight Formula Component) -->
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label mb-0 small fw-bold text-muted-custom text-uppercase">Workspace / Behavior Grade</label>
                                                        <span class="badge bg-primary rounded-pill font-monospace fw-bold" id="sliderValLabel<?php echo $row['id']; ?>">10 / 10</span>
                                                    </div>
                                                    <input type="range" name="behavior_score" class="form-range" min="1" max="10" value="10" 
                                                           id="behaviorSlider<?php echo $row['id']; ?>" 
                                                           oninput="document.getElementById('sliderValLabel<?php echo $row['id']; ?>').innerText = this.value + ' / 10'">
                                                    <div class="d-flex justify-content-between text-muted-custom" style="font-size: 0.65rem;">
                                                        <span>Left Station Messy</span>
                                                        <span>Clean & Arranged</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer modal-footer-custom p-3" style="background-color: rgba(0,0,0,0.15);">
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-3 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="logout_student" class="btn btn-sm btn-danger px-4 rounded-pill fw-bold">
                                                    Process Score & Logout
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted-custom">
                                    <i class="bi bi-clock-history opacity-25 d-block mb-2" style="font-size: 3rem;"></i>
                                    No active sessions found.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Optional: Pagination Render Block to map with your config calculations -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination pagination-sm justify-content-end mb-0">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link border-secondary-subtle bg-transparent main-text" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
            
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>