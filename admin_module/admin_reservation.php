<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/database.php");
include("../action/admin_pc_control.php");

// Fetch pending requests
$row = getPendingReservations($conn);
$total_pending_requests = mysqli_num_rows($row);

// Fetch approved requests
$approvedResult = getApprovedReservations($conn); 

// Fetch system logs
$logsResult = getSystemLogs($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SIT-IN Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

<?php include("../includes/adminHeader.php"); ?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Admin Management Dashboard</h3>
            <p class="text-muted small mb-0">Monitor computers, process reservations, and maintain audit logs.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <select id="labSwitcher" class="form-select border-dark fw-bold rounded-0 shadow-none w-auto" onchange="syncAdminDashboard()">
                <option value="544">Lab 544</option>
                <option value="542">Lab 542</option>
                <option value="526">Lab 526</option>
            </select>
            <button class="btn btn-dark rounded-0 px-4 fw-bold shadow-sm d-flex align-items-center gap-1" onclick="syncAdminDashboard()">
                <i class="bi bi-arrow-repeat"></i> SYNC
            </button>
        </div>
    </div>

    <ul class="nav nav-pills gap-2 mb-4 flex-column flex-sm-row" id="adminControlTabs" role="tablist">
        <li class="nav-item flex-fill text-center text-sm-start" role="presentation">
            <button class="nav-link active border border-dark rounded-0 px-4 w-100 fw-bold text-uppercase small" data-bs-toggle="tab" data-bs-target="#pc-control" type="button" role="tab">
                <i class="bi bi-pc-display me-2"></i> PC Control Panel
            </button>
        </li>
        <li class="nav-item flex-fill text-center text-sm-start" role="presentation">
            <button class="nav-link border border-dark rounded-0 px-4 w-100 fw-bold text-uppercase small" data-bs-toggle="tab" data-bs-target="#reserved-list" type="button" role="tab">
                <i class="bi bi-bookmark-check me-2"></i> Approved Reservations
            </button>
        </li>
        <li class="nav-item flex-fill text-center text-sm-start" role="presentation">
            <button class="nav-link border border-dark rounded-0 px-4 w-100 fw-bold text-uppercase small" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab">
                <i class="bi bi-clock-history me-2"></i> System Logs
            </button>
        </li>
    </ul>

    <div class="tab-content" id="adminControlTabContent">
        <div class="tab-pane fade show active" id="pc-control" role="tabpanel">
            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="card border border-dark rounded-0 shadow-sm h-100">
                        <div class="card-header bg-dark text-white rounded-0 d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0 fw-bold text-uppercase" id="labHeading">LAB-544 MONITORING</h5>
                            <span class="badge bg-light text-dark rounded-pill" id="pcCountLabel">0 Units</span>
                        </div>
                        <div class="card-body bg-white p-4">
                            <div class="d-flex flex-wrap justify-content-center gap-4" id="adminGridContainer"></div>
                            
                            <hr class="my-4">
                            <div class="d-flex flex-wrap gap-4 justify-content-center">
                                <span class="small fw-bold text-success"><i class="bi bi-square-fill me-1"></i> Available</span>
                                <span class="small fw-bold text-danger"><i class="bi bi-square-fill me-1"></i> Occupied / Active</span>
                                <span class="small fw-bold text-primary"><i class="bi bi-square-fill me-1"></i> Pending</span>
                                <span class="small fw-bold text-warning"><i class="bi bi-square-fill me-1"></i> Maintenance</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card border border-dark rounded-0 shadow-sm">
                        <div class="card-header bg-secondary text-white border-bottom border-dark rounded-0 d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0 fs-6 fw-bold text-uppercase"><i class="bi bi-person-exclamation me-2"></i>Queue for Approvals</h5>
                            <span class="badge bg-danger rounded-pill"><?= $total_pending_requests ?></span>
                        </div>
                        <div class="card-body p-3" style="max-height: 520px; overflow-y: auto;">
                            <?php if($total_pending_requests == 0): ?>
                                <p class="text-muted small text-center my-5">No pending reservation requests.</p>
                            <?php else: ?>
                                <?php while($pendingRequest = mysqli_fetch_assoc($row)): ?>
                                    <div class="p-3 border border-secondary rounded-0 mb-3 bg-light shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary text-white text-uppercase" style="font-size: 0.6rem;">Pending Application</span>
                                            <small class="text-muted fw-bold">PC-<?= $pendingRequest['pc_number'] ?></small>
                                        </div>
                                        <h6 class="mb-1 fw-bold text-dark"><?= htmlspecialchars($pendingRequest['fullname']) ?></h6>
                                        <p class="text-secondary small mb-3 lh-sm">
                                            <span class="text-primary">Lab:</span> <?= htmlspecialchars($pendingRequest['lab_name']) ?><br>
                                            <span class="text-primary">Time:</span> <?= htmlspecialchars($pendingRequest['schedule_time']) ?>
                                        </p>
                                        <div class="d-flex gap-2">
                                            <a href="../action/admin_pc_control.php?action=approve&id=<?= $pendingRequest['id'] ?>" class="btn btn-success btn-sm w-50 rounded-0 fw-bold">APPROVE</a>
                                            <a href="../action/admin_pc_control.php?action=reject&id=<?= $pendingRequest['id'] ?>" class="btn btn-danger btn-sm w-50 rounded-0 fw-bold">REJECT</a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="reserved-list" role="tabpanel">
            <div class="card border border-dark rounded-0 shadow-sm">
                <div class="card-header bg-dark text-white rounded-0 py-3">
                    <i class="bi bi-check-circle me-2"></i>Approved Reservations Application Logs
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-bottom text-uppercase small">
                                <tr>
                                    <th class="ps-4">Name</th>
                                    <th>Lab Name</th>
                                    <th>PC Unit</th>
                                    <th>Schedule Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($approvedResults = mysqli_fetch_assoc($approvedResult)): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?= htmlspecialchars($approvedResults['fullname']) ?></td>
                                    <td><?= htmlspecialchars($approvedResults['lab_name']) ?></td>
                                    <td><span class="badge bg-secondary">PC-<?= $approvedResults['pc_number'] ?></span></td>
                                    <td><?= $approvedResults['schedule_date'] ?></td>
                                    <td>
                                        <!-- Use the new action column here -->
                                        <span class="badge bg-success"><?= ucfirst($approvedResults['action']) ?></span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="logs" role="tabpanel">
             <div class="card border border-dark rounded-0 shadow-sm">
                <div class="card-header bg-dark text-white rounded-0 py-3">
                    <i class="bi bi-file-text me-2"></i>System-wide Audit Trail
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light border-bottom small text-uppercase">
                                <tr>
                                    <th class="ps-4">Name</th>
                                    <th>Lab Name</th>
                                    <th>PC Unit</th>
                                    <th>Schedule Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($logs = mysqli_fetch_assoc($logsResult)): ?>
                                <tr>
                                    <td class="ps-4"><?= $logs['fullname'] ?></td>
                                    <td><?= htmlspecialchars($logs['lab_name']) ?></td>
                                    <td><span class="badge bg-secondary">PC-<?= $logs['pc_number'] ?></span></td>
                                    <td><?= $logs['schedule_date'] ?></td>
                                    <td>
                                        <?php 
                                            // Set color based on the action value
                                            $badgeClass = 'bg-secondary';
                                            if ($logs['action'] == 'approved') $badgeClass = 'bg-success';
                                            if ($logs['action'] == 'rejected') $badgeClass = 'bg-danger';
                                            if ($logs['action'] == 'pending')  $badgeClass = 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= ucfirst($logs['action']) ?></span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pcSettingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0 border-dark">
            <div class="modal-header bg-dark text-white rounded-0">
                <h5 class="modal-title fw-bold">Workstation Maintenance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h3 class="fw-bold mb-1" id="modalPcTitle">PC-00</h3>
                <input type="hidden" id="selectedPcNumber">
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button class="btn btn-success rounded-0 px-3 py-2 fw-bold" onclick="processToggle('available')">Available</button>
                    <button class="btn btn-warning rounded-0 px-3 py-2 fw-bold" onclick="processToggle('unavailable')">Unavailable</button>
                    <button class="btn btn-danger rounded-0 px-3 py-2 fw-bold" onclick="processToggle('maintenance')">Maintenance</button>
                </div>
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
        
        document.getElementById('pcCountLabel').innerText = `${data.total} Units`;
        const gridContainer = document.getElementById('adminGridContainer');
        
        // VITAL: Clear the container every time we sync to remove old red icons
        gridContainer.innerHTML = ''; 

        let pcCounter = 1;
        const numIslands = Math.ceil(data.total / 8);

        for (let isl = 0; isl < numIslands; isl++) {
            let island = document.createElement('div');
            island.className = "d-flex gap-2 mb-3";
            island.innerHTML = `<div class="d-flex flex-column gap-2" id="isl-left-${isl}"></div>
                                <div class="bg-dark rounded" style="width: 5px;"></div>
                                <div class="d-flex flex-column gap-2" id="isl-right-${isl}"></div>`;
            gridContainer.appendChild(island);

            // Left side of the island (4 units)
            for (let i = 0; i < 4; i++) {
                if (pcCounter <= data.total) {
                    document.getElementById(`isl-left-${isl}`).appendChild(createPCUnit(pcCounter, data));
                    pcCounter++;
                }
            }
            // Right side of the island (4 units)
            for (let i = 0; i < 4; i++) {
                if (pcCounter <= data.total) {
                    document.getElementById(`isl-right-${isl}`).appendChild(createPCUnit(pcCounter, data));
                    pcCounter++;
                }
            }
        }
    }

    function createPCUnit(id, data) {
        const btn = document.createElement('div');
        // Standard Green Setup
        btn.className = "btn btn-success d-flex align-items-center justify-content-center border-secondary rounded-0 shadow-sm fw-bold p-0";
        btn.style.width = "46px";
        btn.style.height = "46px";
        btn.style.fontSize = "0.65rem";
        btn.innerText = `PC-${id}`;
        btn.onclick = () => openPcModal(id);

        // Define status logic
        const isOccupied = (data.reserved && data.reserved.map(String).includes(id.toString())) || 
                    (data.active && data.active.map(String).includes(id.toString()));

        const isPending = data.pending && data.pending.map(String).includes(id.toString());

        const isMaint = (data.maintenance && data.maintenance.map(String).includes(id.toString())) || 
                        (data.warning && data.warning.map(String).includes(id.toString()));

        // Apply visual classes (ONLY one status at a time)
        if (isOccupied) {
            btn.className = btn.className.replace('btn-success', 'btn-danger') + " pe-none";
        } else if (isPending) {
            btn.className = btn.className.replace('btn-success', 'btn-primary');
        } else if (isMaint) {
            btn.className = btn.className.replace('btn-success', 'btn-warning') + " text-dark";
        }

        return btn;
    }

    function openPcModal(pcNumber) {
        document.getElementById('modalPcTitle').innerText = "PC-" + pcNumber;
        document.getElementById('selectedPcNumber').value = pcNumber;
        new bootstrap.Modal(document.getElementById('pcSettingModal')).show();
    }

    async function processToggle(status) {
        const formData = new FormData();
        formData.append('lab_name', document.getElementById('labSwitcher').value);
        formData.append('pc_number', document.getElementById('selectedPcNumber').value);
        formData.append('status', status);

        await fetch('../action/admin_pc_control.php', { method: 'POST', body: formData });
        bootstrap.Modal.getInstance(document.getElementById('pcSettingModal')).hide();
        syncAdminDashboard();
    }

    window.onload = () => {
        syncAdminDashboard();
        setInterval(syncAdminDashboard, 5000); // 5-second interval for real-time updates
    };
</script>
</body>
</html>