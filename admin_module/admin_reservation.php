<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/database.php");
include("../action/admin_pc_control.php");

// Handle Reservation Action (Approve/Reject)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    $status = ($action == 'approve') ? 'approved' : 'rejected';

    $update = mysqli_prepare($conn, "UPDATE reservations SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($update, "si", $status, $id);
    mysqli_stmt_execute($update);
    header("Location: dashboard.php");
    exit();
}

// Fetch Pending Reservations
$pendingQuery = "SELECT r.*, s.firstname, s.lastname 
                 FROM reservations r 
                 JOIN students s ON r.student_pk_id = s.id 
                 WHERE r.status = 'pending' 
                 ORDER BY r.created_at ASC";
$pendingResult = mysqli_query($conn, $pendingQuery);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SIT-IN Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { 
            --sidebar-w: 380px; 
            --admin-accent: #0f172a; 
        }
        
        body { 
            background-color: #f1f5f9; 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            overflow: hidden; 
        }

        .admin-wrapper { display: flex; height: calc(100vh - 65px); }
        
        .side-panel {
            width: var(--sidebar-w);
            background: white;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .main-panel {
            flex-grow: 1;
            padding: 2rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .floor-plan-card {
            background: white;
            border: 3px solid var(--admin-accent);
            padding: 3rem;
            border-radius: 4px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            position: relative;
            min-width: 900px;
        }

        .pc-unit {
            width: 44px; height: 44px;
            border: 2px solid var(--admin-accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem; font-weight: 800; cursor: pointer;
            transition: 0.2s;
        }
        .pc-unit:hover { transform: scale(1.15); z-index: 5; }

        .bg-open { background: #22c55e; color: white; border-color: #166534; }
        .bg-reserved { background: #ef4444; color: white; border-color: #b91c1c; }
        .bg-warning { background: #fbbf24; color: white; border-color: #92400e; }

        .island { display: flex; gap: 6px; margin-bottom: 20px; }
        .spine { width: 5px; background: var(--admin-accent); border-radius: 2px; }
        .bank { display: flex; flex-direction: column; gap: 8px; }

        .request-item {
            border-left: 5px solid #3b82f6;
            transition: 0.2s;
        }
        .request-item:hover { background-color: #f8fafc !important; }
        .btn-action { font-size: 0.75rem; font-weight: 700; padding: 6px; }

        .status-pill {
            font-size: 0.6rem;
            padding: 2px 8px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

<?php include("../includes/adminHeader.php"); ?>

<div class="admin-wrapper">
    <div class="side-panel">
        <ul class="nav nav-pills nav-justified p-2 bg-light m-2 rounded" id="adminTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold small py-2" data-bs-toggle="tab" data-bs-target="#tab-res">
                    <i class="bi bi-person-check me-2"></i>APPROVALS
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small py-2" data-bs-toggle="tab" data-bs-target="#tab-logs">
                    <i class="bi bi-clock-history me-2"></i>LOGS
                </button>
            </li>
        </ul>

        <div class="tab-content p-3 overflow-auto flex-grow-1">
            <div class="tab-pane fade show active" id="tab-res">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-secondary">Queue</h6>
                    <span class="badge bg-primary rounded-pill" id="pendingCount"><?= mysqli_num_rows($pendingResult); ?></span>
                </div>

                <div id="queueContainer">
                    <?php if(mysqli_num_rows($pendingResult) == 0): ?>
                        <p class="text-muted small text-center my-4">No pending reservations.</p>
                    <?php else: ?>
                        <?php while($req = mysqli_fetch_assoc($pendingResult)): ?>
                            <div class="request-item bg-white border p-3 rounded mb-3 shadow-sm">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="status-pill bg-primary text-white">Pending Request</span>
                                </div>
                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($req['firstname']." ".$req['lastname']) ?> <span class="text-primary">• PC-<?= $req['pc_number'] ?></span></h6>
                                <p class="text-muted small mb-3"><?= $req['schedule_date'] ?> @ <?= $req['schedule_time'] ?><br>Purpose: <?= htmlspecialchars($req['lab_name']) ?></p>
                                <div class="d-flex gap-2">
                                    <a href="dashboard.php?action=approve&id=<?= $req['id'] ?>" class="btn btn-success btn-action flex-grow-1 rounded-0">APPROVE</a>
                                    <a href="dashboard.php?action=reject&id=<?= $req['id'] ?>" class="btn btn-danger btn-action flex-grow-1 rounded-0">REJECT</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-logs">
                <div class="p-2 text-muted small">Viewing logs for the current session...</div>
            </div>
        </div>
    </div>

    <div class="main-panel">
        <div class="w-100 d-flex justify-content-between align-items-center mb-4 px-5">
            <div>
                <h4 class="fw-bold mb-0"><span id="labHeading">LAB-544</span> MONITORING</h4>
                <p class="text-muted small mb-0">Lab Capacity: <span id="pcCountLabel">40</span> PCs</p>
            </div>
            <div class="d-flex gap-3">
                <select id="labSwitcher" class="form-select border-dark fw-bold shadow-none" onchange="syncAdminDashboard()">
                    <option value="544">Lab 544</option>
                    <option value="542">Lab 542</option>
                    <option value="526">Lab 526</option>
                </select>
                <button class="btn btn-dark px-4 fw-bold shadow-sm" onclick="syncAdminDashboard()">REFRESH</button>
            </div>
        </div>

        <div class="floor-plan-card">
            <div class="pc-grid d-flex justify-content-around flex-wrap" id="adminGridContainer">
                </div>

            <div class="mt-5 d-flex gap-4 justify-content-center border-top pt-4">
                <span class="small fw-bold"><i class="bi bi-square-fill text-success me-1"></i> Available</span>
                <span class="small fw-bold"><i class="bi bi-square-fill text-danger me-1"></i> Occupied</span>
                <span class="small fw-bold"><i class="bi bi-square-fill text-info me-1"></i>Pending</span>
                <span class="small fw-bold"><i class="bi bi-square-fill text-warning me-1"></i> Unavailable</span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pcSettingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0 border-dark">
            <div class="modal-header bg-dark text-white rounded-0">
                <h5 class="modal-title fw-bold">Workstation Configuration</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h3 class="fw-bold mb-1" id="modalPcTitle">PC-00</h3>
                <p class="text-muted mb-4" id="modalLabTitle">Lab 544</p>
                <p class="text-secondary small mb-4">Set the status of this workstation below.</p>

                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-success rounded-0 px-4 py-2 fw-bold" onclick="processToggle('available')">Available</button>
                    <button class="btn btn-warning rounded-0 px-4 py-2 fw-bold" onclick="processToggle('unavailable')">Unavailable</button>
                </div>
                <input type="hidden" id="selectedPcNumber">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    async function syncAdminDashboard() {
        const lab = document.getElementById('labSwitcher').value;
        document.getElementById('labHeading').innerText = `${lab} MONITORING`;
        
        const response = await fetch(`../action/get_lab_status.php?lab=${lab}`);
        const data = await response.json();
        
        document.getElementById('pcCountLabel').innerText = data.total;

        const gridContainer = document.getElementById('adminGridContainer');
        let html = '';
        let pcCounter = 1;
        const numIslands = Math.ceil(data.total / 8);

        for (let isl = 0; isl < numIslands; isl++) {
            html += `<div class="island"><div class="bank">`;
            
            for (let i = 0; i < 4; i++) {
                if (pcCounter <= data.total) {
                    html += `<div class="pc-unit bg-open" id="admin-pc-${pcCounter}" onclick="openPcModal(${pcCounter})">PC-${pcCounter}</div>`;
                    pcCounter++;
                }
            }
            
            html += `</div><div class="spine"></div><div class="bank">`;
            
            for (let i = 0; i < 4; i++) {
                if (pcCounter <= data.total) {
                    html += `<div class="pc-unit bg-open" id="admin-pc-${pcCounter}" onclick="openPcModal(${pcCounter})">PC-${pcCounter}</div>`;
                    pcCounter++;
                }
            }
            
            html += `</div></div>`;
        }

        gridContainer.innerHTML = html;

        // Apply reservations and warnings directly to the map
        data.reserved.forEach(id => {
            const el = document.getElementById(`admin-pc-${id}`);
            if (el) el.className = 'pc-unit bg-reserved';
        });

        data.warning.forEach(id => {
            const el = document.getElementById(`admin-pc-${id}`);
            if (el) el.className = 'pc-unit bg-warning';
        });
    }

    function openPcModal(pcNumber) {
        document.getElementById('modalPcTitle').innerText = "PC-" + pcNumber;
        document.getElementById('modalLabTitle').innerText = "Lab: " + document.getElementById('labSwitcher').value;
        document.getElementById('selectedPcNumber').value = pcNumber;
        
        var pcModal = new bootstrap.Modal(document.getElementById('pcSettingModal'));
        pcModal.show();
    }

        async function processToggle(status) {
        const pcNumber = document.getElementById('selectedPcNumber').value;
        const lab = document.getElementById('labSwitcher').value;
        
        const formData = new FormData();
        formData.append('lab_name', lab);
        formData.append('pc_number', pcNumber);
        formData.append('status', status);

        try {
            // Updated path traversing one level up into the action folder
            const response = await fetch('../action/admin_pc_control.php', {
                method: 'POST',
                body: formData
            });

            // Parse response text or JSON
            const result = await response.text(); 
            console.log("Response:", result);

            // Close modal and refresh UI
            bootstrap.Modal.getInstance(document.getElementById('pcSettingModal')).hide();
            syncAdminDashboard();
        } catch (error) {
            console.error("Error toggling PC:", error);
        }
    }

    window.onload = () => {
        syncAdminDashboard();
        setInterval(syncAdminDashboard, 10000);
    };
</script>
</body>
</html>