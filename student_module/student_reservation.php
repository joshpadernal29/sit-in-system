<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/database.php");
include("../action/studentData.php"); // student session
include("../action/sit_in_reserve.php");
$student_pk = $student['id']; 

// =========================================================================
// ACTION HANDLER: STUDENT CANCEL / DISABLE ACTIONS
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reservation_state'])) {
    $target_res_id = intval($_POST['reservation_id']);
    $intended_action = $_POST['update_reservation_state']; // 'cancel' or 'disable'
    
    if ($intended_action === 'cancel') {
        // Only allow cancellation if the request is still pending
        $stmt = $conn->prepare("UPDATE reservations SET status = 'rejected', action = 'rejected' WHERE id = ? AND student_pk_id = ? AND status = 'pending'");
        $stmt->bind_param("ii", $target_res_id, $student_pk);
        $stmt->execute();
        $stmt->close();
    } elseif ($intended_action === 'disable') {
        // Change approved request to un-active / discarded state context
        $stmt = $conn->prepare("UPDATE reservations SET status = 'rejected' WHERE id = ? AND student_pk_id = ? AND status = 'approved'");
        $stmt->bind_param("ii", $target_res_id, $student_pk);
        $stmt->execute();
        $stmt->close();
    }
    // Refresh to update UI states
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch current active / pending session logs for this student
$log_query = "SELECT * FROM reservations WHERE student_pk_id = ? ORDER BY id DESC LIMIT 5";
$stmt_log = $conn->prepare($log_query);
$stmt_log->bind_param("i", $student_pk);
$stmt_log->execute();
$reservation_logs = $stmt_log->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_log->close();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIT-IN | Floor Plan Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* ================= SIDEBAR COMPATIBILITY ONLY ================= */
        .main-content-wrapper{
            margin-left:260px;
            transition: margin-left .3s ease;
        }

        /* when sidebar collapsed */
        .sidebar.collapsed ~ .main-content-wrapper{
            margin-left:80px;
        }

        /* mobile (sidebar becomes overlay) */
        @media (max-width:991px){
            .main-content-wrapper{
                margin-left:0 !important;
            }
        }

        /* Software Profile badge sizing */
        .software-tag {
            font-size: 0.68rem;
            letter-spacing: 0.2px;
        }
    </style>
</head>
<body class="bg-body-tertertiary text-body">

<?php include("../includes/student_sidebar.php"); ?>

<div class="main-content-wrapper">
    <div class="container-fluid px-4 py-4">
        
        <!-- HEADER PANEL -->
        <div class="row mb-4">
            <!-- FIXED: Added border-light-subtle to replace harsh black line markers -->
            <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-center bg-body p-4 border border-light-subtle shadow-sm">
                <div>
                    <h3 class="fw-bold text-body mb-1 text-uppercase">Laboratory Floor Plan</h3>
                    <!-- FIXED: Modified text-secondary to text-body-secondary for safe adaptation -->
                    <p class="text-body-secondary small mb-0">Select an <span class="text-success fw-bold">Available</span> PC unit to submit a reservation request.</p>
                </div>
                <div class="d-flex gap-3 mt-3 mt-md-0">
                    <select id="labSwitcher" class="form-select bg-body text-body border-secondary-subtle fw-bold rounded-0 shadow-none" onchange="syncStudentDashboard()">
                        <option value="544">LAB 544</option>
                        <option value="542">LAB 542</option>
                        <option value="526">LAB 526</option>
                    </select>
                    <button class="btn btn-outline-secondary border-secondary-subtle rounded-0 px-4 fw-bold shadow-sm" onclick="syncStudentDashboard()">REFRESH</button>
                </div>
            </div>
        </div>

        <!-- MAIN MONITORING & ENVIRONMENT DATA LAYOUT -->
        <div class="row g-4 justify-content-center">
            
            <!-- LEFT COLUMN: TARGET LAB GRID & STATUS LEGEND -->
            <div class="col-xl-8">
                <div class="card border border-light-subtle rounded-0 shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5 bg-body">
                        <div id="studentGridContainer" class="d-flex flex-wrap justify-content-center gap-4"></div>
                        
                        <hr class="my-4 opacity-10">
                        
                        <div class="d-flex flex-wrap gap-4 justify-content-center">
                            <span class="small fw-bold text-success"><i class="bi bi-square-fill me-1"></i> Available</span>
                            <span class="small fw-bold text-danger"><i class="bi bi-square-fill me-1"></i> Occupied / Active</span>
                            <span class="small fw-bold text-primary"><i class="bi bi-square-fill me-1"></i> Pending</span>
                            <span class="small fw-bold text-warning"><i class="bi bi-square-fill me-1"></i> Maintenance</span>
                        </div>
                    </div>
                </div>

                <!-- STUDENT ACTIVITY MONITORING LOGS -->
                <div class="card border border-light-subtle rounded-0 shadow-sm mt-4">
                    <div class="card-header bg-body border-bottom border-light-subtle py-3">
                        <span class="fw-bold text-body small text-uppercase"><i class="bi bi-clock-history me-2 text-primary"></i>My Reservation History</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-body" style="font-size: 0.85rem;">
                            <thead>
                                <tr>
                                    <!-- FIXED: Swapped light-explicit headers to adaptive bg-body-tertiary metrics -->
                                    <th class="text-body-secondary small text-uppercase py-3 ps-3 bg-body-tertiary border-bottom border-light-subtle">PC Node</th>
                                    <th class="text-body-secondary small text-uppercase py-3 bg-body-tertiary border-bottom border-light-subtle">Lab System</th>
                                    <th class="text-body-secondary small text-uppercase py-3 bg-body-tertiary border-bottom border-light-subtle">Date & Time</th>
                                    <th class="text-body-secondary small text-uppercase py-3 bg-body-tertiary border-bottom border-light-subtle">Status</th>
                                    <th class="text-body-secondary small text-uppercase py-3 pe-3 text-end bg-body-tertiary border-bottom border-light-subtle">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reservation_logs)): ?>
                                    <tr>
                                        <!-- FIXED: Replaced explicit text-muted utilities -->
                                        <td colspan="5" class="text-center py-4 text-body-secondary small bg-body">
                                            <i class="bi bi-calendar-x display-6 opacity-25 mb-2 d-block"></i>
                                            No recent registration logs compiled for this workspace.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reservation_logs as $log): ?>
                                        <tr>
                                            <td class="ps-3 fw-bold border-light-subtle bg-body">PC-<?= htmlspecialchars($log['pc_number']) ?></td>
                                            <td class="border-light-subtle text-uppercase fw-semibold bg-body">LAB <?= htmlspecialchars($log['lab_name']) ?></td>
                                            <td class="border-light-subtle text-body-secondary bg-body">
                                                <?= htmlspecialchars($log['schedule_date']) ?> @ <?= htmlspecialchars($log['schedule_time']) ?>
                                            </td>
                                            <td class="border-light-subtle bg-body">
                                                <?php if ($log['status'] === 'pending'): ?>
                                                    <span class="badge bg-primary text-white rounded-0 text-uppercase" style="font-size:0.65rem;">Pending Approval</span>
                                                <?php elseif ($log['status'] === 'approved' || $log['status'] === 'active'): ?>
                                                    <span class="badge bg-success text-white rounded-0 text-uppercase" style="font-size:0.65rem;">Approved / Live</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary text-white rounded-0 text-uppercase" style="font-size:0.65rem;"><?= htmlspecialchars($log['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="pe-3 text-end border-light-subtle bg-body">
                                                <form method="POST" action="" onsubmit="return confirm('Are you sure you want to execute this state amendment?');" class="m-0 p-0">
                                                    <input type="hidden" name="reservation_id" value="<?= $log['id'] ?>">
                                                    
                                                    <?php if ($log['status'] === 'pending'): ?>
                                                        <button type="submit" name="update_reservation_state" value="cancel" class="btn btn-sm btn-outline-danger rounded-0 py-0 px-2 fw-bold" style="font-size:0.75rem;">
                                                            <i class="bi bi-x-square me-1"></i>CANCEL
                                                        </button>
                                                    <?php elseif ($log['status'] === 'approved'): ?>
                                                        <button type="submit" name="update_reservation_state" value="disable" class="btn btn-sm btn-outline-secondary rounded-0 py-0 px-2 fw-bold" style="font-size:0.75rem;">
                                                            <i class="bi bi-slash-circle me-1"></i>DISABLE
                                                        </button>
                                                    <?php else: ?>
                                                        <!-- FIXED: Locked button template now renders correctly against unified dark/light themes -->
                                                        <button type="button" class="btn btn-sm btn-secondary opacity-50 border-0 text-white rounded-0 py-0 px-2 disabled small" style="font-size:0.75rem;">
                                                            LOCKED
                                                        </button>
                                                    <?php endif; ?>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: LAB ENVIRONMENT SOFTWARE APPLICATIONS PROFILES -->
            <div class="col-xl-4">
                <div class="card border border-light-subtle rounded-0 shadow-sm h-100">
                    <!-- FIXED: Modified bg-dark to native theme headers for high visibility -->
                    <div class="card-header bg-body border-bottom border-light-subtle py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold small text-uppercase text-body"><i class="bi bi-cpu-fill me-2 text-info"></i>Lab Software Available</span>
                        <span class="badge bg-secondary text-white rounded-0 shadow-sm small" id="softwareCountLabel">0 Apps</span>
                    </div>
                    <div class="card-body bg-body p-3" style="max-height: 480px; overflow-y: auto;">
                        <div class="row g-2" id="softwareAppGrid">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- APPLICATION RESERVATION MODAL -->
    <div class="modal fade" id="resModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-body text-body border border-light-subtle rounded-0 shadow-lg">
                <!-- FIXED: Changed background to match current body criteria safely -->
                <div class="modal-header bg-body border-bottom border-light-subtle py-3">
                    <h5 class="modal-title fw-bold text-uppercase text-body"><i class="bi bi-pencil-square me-2 text-primary"></i>Apply for Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../action/sit_in_reserve.php" method="POST">
                    <input type="hidden" name="student_pk_id" value="<?php echo $student_pk; ?>">
                    <input type="hidden" name="pc_number" id="modal_pc_number">
                    <input type="hidden" name="lab_name" id="modal_lab_name">

                    <div class="modal-body p-4">
                        <div class="text-center mb-4 bg-body-tertiary p-3 border border-light-subtle">
                            <h2 class="fw-bold mb-0 text-body" id="display_pc">PC-00</h2>
                            <p class="text-body-secondary small mb-0 fw-bold text-uppercase" id="display_lab">LAB 544</p>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary text-uppercase">Reservation Date</label>
                                <input type="date" class="form-control bg-body text-body border-secondary-subtle rounded-0 shadow-none" name="res_date" required min="<?= date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary text-uppercase">Start Time</label>
                                <input type="time" class="form-control bg-body text-body border-secondary-subtle rounded-0 shadow-none" name="res_time" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-body-secondary text-uppercase">Programming Language</label>
                            <select class="form-select bg-body text-body border-secondary-subtle rounded-0 shadow-none" name="language" required>
                                <option value="" selected disabled>Select language...</option>
                                <option value="PHP">PHP</option>
                                <option value="Java">Java</option>
                                <option value="Python">Python</option>
                                <option value="C#">C#</option>
                                <option value="C++">C++</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-body-secondary text-uppercase">Purpose of Use</label>
                            <select class="form-select bg-body text-body border-secondary-subtle rounded-0 shadow-none" name="sit_in_purpose" required>
                                <option value="Research">Research</option>
                                <option value="Programming Task">Programming Task</option>
                                <option value="Exam / Quiz">Exam / Quiz</option>
                                <option value="Self Study">Self Study</option>
                            </select>
                        </div>
                        
                        <div class="alert alert-info border-0 rounded-0 small mb-0 mt-3">
                            <i class="bi bi-info-circle-fill me-2"></i> Requests are sent to admin for approval.
                        </div>
                    </div>
                    <div class="modal-footer border-top border-light-subtle p-4">
                        <button type="button" class="btn btn-outline-secondary rounded-0 fw-bold px-4" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" name="reserve_pc" class="btn btn-primary rounded-0 fw-bold px-4">CONFIRM APPLICATION</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let studentHasPending = false;

    async function syncStudentDashboard() {
        const lab = document.getElementById('labSwitcher').value;
        const studentId = <?php echo $_SESSION['student_id'] ?? '0'; ?>;
        
        fetchSoftwareInventory(lab);
        
        try {
            const response = await fetch(`../action/get_lab_status.php?lab=${lab}`);
            const data = await response.json();
            
            studentHasPending = (data.student_pending && data.student_pending.includes(studentId));

            const gridContainer = document.getElementById('studentGridContainer');
            gridContainer.innerHTML = ''; 
            
            let pcCounter = 1;
            const numIslands = Math.ceil(data.total / 8);

            for (let isl = 0; isl < numIslands; isl++) {
                const island = document.createElement('div');
                island.className = "d-flex gap-2 mb-3 border border-light-subtle p-2 bg-body-tertiary";

                const leftBank = document.createElement('div');
                leftBank.className = "d-flex flex-column gap-2";
                for (let i = 0; i < 4; i++) {
                    if (pcCounter <= data.total) {
                        leftBank.appendChild(createPCUnit(pcCounter, data));
                        pcCounter++;
                    }
                }

                const spine = document.createElement('div');
                spine.className = "bg-secondary rounded-pill opacity-50";
                spine.style.width = "5px";

                const rightBank = document.createElement('div');
                rightBank.className = "d-flex flex-column gap-2";
                for (let i = 0; i < 4; i++) {
                    if (pcCounter <= data.total) {
                        rightBank.appendChild(createPCUnit(pcCounter, data));
                        pcCounter++;
                    }
                }

                island.appendChild(leftBank);
                island.appendChild(spine);
                island.appendChild(rightBank);
                gridContainer.appendChild(island);
            }
        } catch (error) {
            console.error("Failed syncing floor plan grid context:", error);
        }
    }

    // FIXED: Formatted JavaScript string generator to adapt correctly to dark/light environments
    async function fetchSoftwareInventory(labId) {
        try {
            const response = await fetch(`../action/get_lab_software.php?lab=${labId}`);
            const softwareList = await response.json();
            
            const appGrid = document.getElementById('softwareAppGrid');
            document.getElementById('softwareCountLabel').innerText = `${softwareList.length} Apps`;
            appGrid.innerHTML = '';

            if (softwareList.length === 0) {
                appGrid.innerHTML = `
                    <div class="col-12 text-center py-5 text-body-secondary">
                        <i class="bi bi-app-indicator display-6 opacity-25"></i>
                        <p class="small mb-0 mt-2">No applications deployed in this lab setup.</p>
                    </div>`;
                return;
            }

            softwareList.forEach(app => {
                const item = document.createElement('div');
                item.className = "col-12";
                item.innerHTML = `
                    <div class="p-2 border border-light-subtle bg-body-tertiary d-flex align-items-center">
                        <div class="p-2 bg-secondary-subtle text-body me-2 d-flex align-items-center justify-content-center border border-light-subtle" style="width:32px; height:32px;">
                            <i class="bi bi-box-seam small"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="fw-bold text-truncate text-body small mb-0" style="font-size: 0.78rem;">${app.software_name}</div>
                            <div class="d-flex gap-1 mt-1">
                                <span class="badge bg-body text-body border border-secondary-subtle software-tag">v${app.version}</span>
                                <span class="badge bg-body-secondary text-body-secondary border border-light-subtle software-tag text-uppercase">${app.license_type}</span>
                            </div>
                        </div>
                    </div>`;
                appGrid.appendChild(item);
            });
        } catch (err) {
            console.error("Error updating software inventory component:", err);
        }
    }

    function createPCUnit(id, data) {
        const pcId = id.toString(); 
        const btn = document.createElement('div');
        
        btn.className = "btn btn-success d-flex align-items-center justify-content-center border-secondary rounded-0 shadow-sm fw-bold p-0 text-white";
        btn.style.width = "46px";
        btn.style.height = "46px";
        btn.style.fontSize = "0.65rem";
        btn.innerText = `PC-${id}`;

        const isMaint    = data.maintenance && data.maintenance.map(String).includes(pcId);
        const isUnavail  = data.unavailable && data.unavailable.map(String).includes(pcId);
        const isOccupied = (data.active && data.active.map(String).includes(pcId)) || 
                        (data.reserved && data.reserved.map(String).includes(pcId));
        const isPending  = data.pending && data.pending.map(String).includes(pcId);

        if (isMaint || isOccupied) {
            btn.className = btn.className.replace('btn-success', 'btn-danger') + " pe-none";
        } 
        else if (isPending) {
            btn.className = btn.className.replace('btn-success', 'btn-primary') + " pe-none";
        } 
        else if (isUnavail) {
            btn.className = btn.className.replace('btn-success', 'btn-warning') + " text-dark pe-none";
        } 
        else {
            btn.onclick = () => openModal(id);
            btn.style.cursor = "pointer";
        }

        return btn;
    }

    function openModal(pcNumber) {
        if (studentHasPending) {
            alert("You cannot reserve another PC while you have a pending request.");
            return;
        }

        const labName = document.getElementById('labSwitcher').value;
        document.getElementById('modal_pc_number').value = pcNumber;
        document.getElementById('modal_lab_name').value = labName;
        document.getElementById('display_pc').innerText = "PC-" + pcNumber;
        document.getElementById('display_lab').innerText = "LAB " + labName;

        var myModal = new bootstrap.Modal(document.getElementById('resModal'));
        myModal.show();
    }

    window.onload = () => {
        syncStudentDashboard();
        setInterval(syncStudentDashboard, 15000); 
    };
</script>
</body>
</html>