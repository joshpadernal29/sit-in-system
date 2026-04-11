<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Include your data logic. 
// This file sets up $conn and fetches the $student array based on the session string ID.
include("../action/studentData.php");

/**
 * Fetches student history using the numeric Primary Key
 */
function getStudentHistory($conn, $student_pk) {
    $history = [];
    // We use TIMESTAMPDIFF to handle the duration calculation directly in SQL
    $sql = "SELECT *, 
            TIMESTAMPDIFF(MINUTE, login_time, logout_time) / 60 AS duration_hours 
            FROM sit_in_records 
            WHERE student_pk_id = ? 
            ORDER BY login_time DESC";
            
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $student_pk);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $history[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
    return $history;
}

/** * IMPLEMENTATION OF THE FIX:
 * Your studentData.php fetches a $student array. 
 * We must use the 'id' (Primary Key) from that array to query sit_in_records.
 */
$student_pk = isset($student['id']) ? $student['id'] : 0;
$history = getStudentHistory($conn, $student_pk);

// Stats Calculation
$max_sessions = 30;
$used_sessions = count($history);
$remaining = max(0, $max_sessions - $used_sessions);
$percentage = ($used_sessions / $max_sessions) * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sit-in History | Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --uc-blue: #0d6efd;
            --uc-dark-blue: #0046af;
            --uc-light-bg: #f4f7fe;
        }

        body { 
            background-color: var(--uc-light-bg); 
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #2d3748;
        }

        .welcome-banner {
            background: linear-gradient(135deg, var(--uc-blue) 0%, var(--uc-dark-blue) 100%);
            border-radius: 24px;
            color: white;
            padding: 40px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            border: none;
        }

        .stat-card {
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover { transform: translateY(-5px); }

        .history-timeline {
            position: relative;
            padding-left: 32px;
        }

        .history-timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 10px;
            bottom: 0;
            width: 3px;
            background: #dee2e6;
            border-radius: 3px;
        }

        .timeline-node {
            position: relative;
            padding-bottom: 40px;
        }

        .timeline-node::after {
            content: '';
            position: absolute;
            left: -32px;
            top: 22px;
            width: 18px;
            height: 18px;
            background: white;
            border: 4px solid var(--uc-blue);
            border-radius: 50%;
            z-index: 2;
        }

        .session-card {
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            background: white;
            transition: all 0.3s ease;
        }

        .date-badge {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 12px;
            min-width: 80px;
        }

        .purpose-box {
            background-color: #f1f5f9;
            border-left: 4px solid #cbd5e1;
            border-radius: 8px;
        }

        .btn-check:checked + .btn-outline-secondary {
            background-color: var(--uc-blue) !important;
            border-color: var(--uc-blue) !important;
            color: white !important;
        }
    </style>
</head>
<body>
    <?php include("../includes/studentHeader.php"); ?>

    <div class="container py-5">
        <div class="welcome-banner shadow">
            <h2 class="fw-bold mb-1">My Sit-in History</h2>
            <p class="opacity-75 mb-0">Track your laboratory hours and remaining sessions.</p>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card stat-card p-4 h-100">
                    <small class="text-muted fw-bold text-uppercase">Sessions Remaining</small>
                    <div class="d-flex align-items-end mt-2">
                        <h1 class="fw-bold mb-0 text-primary"><?= $remaining ?></h1>
                        <span class="ms-2 text-muted">/ <?= $max_sessions ?></span>
                    </div>
                    <div class="progress mt-4" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar" style="width: <?= $percentage ?>%"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card p-4 h-100">
                    <small class="text-muted fw-bold text-uppercase">Total Hours Accumulated</small>
                    <?php 
                        $total_hours = array_sum(array_column($history, 'duration_hours')); 
                    ?>
                    <h1 class="fw-bold mt-2 mb-0"><?= number_format($total_hours, 1) ?></h1>
                    <small class="text-success fw-bold d-block mt-2">Active Student Account</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card p-4 h-100">
                    <small class="text-muted fw-bold text-uppercase">Current Status</small>
                    <?php 
                        $is_sitting_in = false;
                        foreach($history as $r) { if(!$r['logout_time']) $is_sitting_in = true; }
                    ?>
                    <h4 class="mt-2 mb-0 fw-bold <?= $is_sitting_in ? 'text-warning' : 'text-success' ?>">
                        <?= $is_sitting_in ? 'In Session' : 'Available' ?>
                    </h4>
                    <span class="badge <?= $is_sitting_in ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' ?> rounded-pill px-3 py-2 mt-2">
                        <?= $is_sitting_in ? 'Currently Logged In' : 'Ready to Sit-in' ?>
                    </span>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-4 text-dark">Recent Activity</h5>

        <div class="history-timeline">
            <?php if (empty($history)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-clock-history display-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3">No sit-in records found yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($history as $record): ?>
                    <div class="timeline-node">
                        <div class="card session-card shadow-sm">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center border-end d-none d-md-block">
                                        <div class="date-badge">
                                            <h3 class="fw-bold mb-0 text-primary">
                                                <?= date('d', strtotime($record['login_time'])) ?>
                                            </h3>
                                            <small class="text-muted fw-bold text-uppercase">
                                                <?= date('M', strtotime($record['login_time'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-10 ps-md-4">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-dark">
                                                    Lab: <?= htmlspecialchars($record['lab'] ?? 'Not Assigned') ?>
                                                </h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="bi bi-calendar3 me-1"></i> 
                                                    <?= date('F j, Y', strtotime($record['login_time'])) ?> 
                                                    at <?= date('h:i A', strtotime($record['login_time'])) ?>
                                                </p>
                                            </div>
                                            <span class="badge bg-light text-dark border rounded-pill">
                                                <?php if($record['logout_time']): ?>
                                                    <?= number_format($record['duration_hours'], 1) ?> Hours
                                                <?php else: ?>
                                                    <span class="text-primary fw-bold">Active</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                            <div class="purpose-box p-3 small text-secondary flex-grow-1">
                                                <strong>Focus:</strong> <?= htmlspecialchars($record['language'] ?? 'General Lab') ?>
                                            </div>
                                            <button class="btn btn-outline-primary btn-sm rounded-pill px-4" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#feedbackModal"
                                                    data-session-id="<?= $record['id'] ?>">
                                                <i class="bi bi-chat-left-text me-1"></i> Feedback
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="feedbackModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Submit Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <form action="../action/student_feedback.php" method="POST">
                        <input type="hidden" name="session_id" id="modal_session_id">
                        
                        <label class="form-label small fw-bold text-uppercase text-muted">Category</label>
                        <div class="d-flex gap-2 mb-4 flex-wrap">
                            <input type="radio" class="btn-check" name="category" id="cat1" value="Hardware" checked>
                            <label class="btn btn-outline-secondary btn-sm rounded-pill px-3" for="cat1">Hardware</label>
                            
                            <input type="radio" class="btn-check" name="category" id="cat2" value="Software">
                            <label class="btn btn-outline-secondary btn-sm rounded-pill px-3" for="cat2">Software</label>
                            
                            <input type="radio" class="btn-check" name="category" id="cat3" value="Environment">
                            <label class="btn btn-outline-secondary btn-sm rounded-pill px-3" for="cat3">Environment</label>
                        </div>

                        <div class="mb-3">
                            <textarea class="form-control bg-light border-0 rounded-3" name="feedback_text" rows="4" placeholder="Report issues or provide suggestions..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Submit Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const feedbackModal = document.getElementById('feedbackModal');
        if (feedbackModal) {
            feedbackModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const sessionId = button.getAttribute('data-session-id');
                const modalInput = feedbackModal.querySelector('#modal_session_id');
                modalInput.value = sessionId;
            });
        }
    </script>
</body>
</html>