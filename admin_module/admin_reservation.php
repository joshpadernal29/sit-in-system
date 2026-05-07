<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/database.php");
include("../action/admin_pc_control.php");

// fetch pending requests
$row = getPendingReservations($conn);
$total_pending_requests = mysqli_num_rows($row);

// fetch approved requests
$approvedResult = getApprovedReservations($conn); 

// fetch systelogs
$logsResult =  getSystemLogs($conn);

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
                            <span class="badge bg-light text-dark rounded-pill" id="pcCountLabel">40 Units</span>
                        </div>
                        <div class="card-body bg-white p-4">
                            <div class="d-flex flex-wrap justify-content-center gap-4" id="adminGridContainer"></div>
                            <hr class="my-4">
                            <div class="d-flex flex-wrap gap-4 justify-content-center">
                                <span class="small fw-bold text-success"><i class="bi bi-square-fill me-1"></i> Available</span>
                                <span class="small fw-bold text-danger"><i class="bi bi-square-fill me-1"></i> Occupied</span>
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
                            <span class="badge bg-danger rounded-pill"><?= $total_pending_requests?></span>
                        </div>
                        <div class="card-body p-3" style="max-height: 520px; overflow-y: auto;">
                            <?php if($total_pending_requests == 0): ?>
                                <p class="text-muted small text-center my-5">No pending reservation requests at the moment.</p>
                            <?php else: ?>
                                <?php while($pendingRequest = mysqli_fetch_assoc($row)): ?>
                                    <div class="p-3 border border-secondary rounded-0 mb-3 bg-light shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary text-white text-uppercase" style="font-size: 0.6rem;">Pending Application</span>
                                            <small class="text-muted fw-bold">PC-<?= $pendingRequest['pc_number'] ?></small>
                                        </div>
                                        <h6 class="mb-1 fw-bold text-dark"><?= htmlspecialchars($pendingRequest['fullname']) ?></h6>
                                        <p class="text-secondary small mb-3 lh-sm">
                                            <span class="text-primary">Scheduled Date:</span> <?= htmlspecialchars($pendingRequest['schedule_date']) ?><br>
                                            <span class="text-primary">Time:</span> <?= htmlspecialchars($pendingRequest['schedule_time']) ?><br>
                                            <span class="text-primary">LAB:</span> <?= htmlspecialchars($pendingRequest['lab_name']) ?><br/>
                                            <span class="text-primary">Purpose:</span> <?= htmlspecialchars($pendingRequest['purpose']) ?>                         
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
                                    <th>PC Unit Number</th>
                                    <th>Schedule Date</th>
                                    <th>Schedule Time</th>
                                    <th>Purpose</th>
                                    <th class="pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($approvedResult) == 0): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">No approved reservations.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php while($approvedResults = mysqli_fetch_assoc($approvedResult)): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($approvedResults['fullname']) ?></td>
                                            <td><?= htmlspecialchars($approvedResults['lab_name']) ?></td>
                                            <td><span class="badge bg-secondary">PC-<?= $approvedResults['pc_number'] ?></span></td>
                                            <td><?= $approvedResults['schedule_date'] ?></td>
                                            <td><?= $approvedResults['schedule_time'] ?></td>
                                            <td><?= htmlspecialchars($approvedResults['sit_in_purpose'] ?? 'Research') ?></td>
                                            <td class="pe-4">
                                                <span class="badge bg-success text-white">Active</span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="logs" role="tabpanel">
            <div class="card border border-dark rounded-0 shadow-sm">
                <div class="card-header bg-dark text-white rounded-0 py-3">
                    <i class="bi bi-file-text me-2"></i>System-wide Audit Trail & Logs
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light border-bottom small text-uppercase">
                                <tr>
                                    <th class="ps-4">Log ID</th>
                                    <th>User Name</th>
                                    <th>Lab</th>
                                    <th>PC Target</th>
                                    <th>Activity / Status</th>
                                    <th class="pe-4">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($logsResult) == 0): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">No transaction logs recorded yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php while($logs = mysqli_fetch_assoc($logsResult)): ?>
                                        <tr>
                                            <td class="ps-4">#<?= $logs['id'] ?></td>
                                            <td><?= htmlspecialchars($logs['fullname']) ?></td>
                                            <td><?= htmlspecialchars($logs['lab_name']) ?></td>
                                            <td>PC-<?= $logs['pc_number'] ?></td>
                                            <td>
                                                <?php if ($logs['status'] == 'approved'): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php elseif ($logs['status'] == 'rejected'): ?>
                                                    <span class="badge bg-danger">Rejected</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="pe-4"><?= $logs['created_at'] ?? 'Just now' ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
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
        <div class="modal-content rounded-0 border-dark shadow-lg">
            <div class="modal-header bg-dark text-white rounded-0">
                <h5 class="modal-title fw-bold">Workstation Maintenance Configuration</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h3 class="fw-bold mb-1 text-dark" id="modalPcTitle">PC-00</h3>
                <p class="text-muted mb-4" id="modalLabTitle">Lab 544</p>
                <p class="text-secondary small mb-4">Select the specific status for this workstation below:</p>

                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-success rounded-0 px-3 py-2 fw-bold" onclick="processToggle('available')">Available</button>
                    <button class="btn btn-warning rounded-0 px-3 py-2 fw-bold" onclick="processToggle('unavailable')">Unavailable</button>
                    <button class="btn btn-danger rounded-0 px-3 py-2 fw-bold" onclick="processToggle('maintenance')">Maintenance</button>
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
        
        document.getElementById('pcCountLabel').innerText = `${data.total} Units`;

        const gridContainer = document.getElementById('adminGridContainer');
        let html = '';
        let pcCounter = 1;
        const numIslands = Math.ceil(data.total / 8);

        for (let isl = 0; isl < numIslands; isl++) {
            html += `<div class="d-flex gap-2 mb-3"><div class="d-flex flex-column gap-2">`;
            
            for (let i = 0; i < 4; i++) {
                if (pcCounter <= data.total) {
                    html += `<div class="btn btn-success p-0 border border-secondary d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; font-size: 0.65rem; font-weight: 800;" id="admin-pc-${pcCounter}" onclick="openPcModal(${pcCounter})">PC-${pcCounter}</div>`;
                    pcCounter++;
                }
            }
            
            html += `</div><div class="bg-dark rounded" style="width: 5px;"></div><div class="d-flex flex-column gap-2">`;
            
            for (let i = 0; i < 4; i++) {
                if (pcCounter <= data.total) {
                    html += `<div class="btn btn-success p-0 border border-secondary d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; font-size: 0.65rem; font-weight: 800;" id="admin-pc-${pcCounter}" onclick="openPcModal(${pcCounter})">PC-${pcCounter}</div>`;
                    pcCounter++;
                }
            }
            
            html += `</div></div>`;
        }

        gridContainer.innerHTML = html;

        // Apply dynamic classes without using CSS declarations in an HTML style tag
        if (data.reserved && data.reserved.length) {
            data.reserved.forEach(id => {
                const el = document.getElementById(`admin-pc-${id}`);
                if (el) {
                    el.className = "btn btn-danger p-0 border border-secondary d-flex align-items-center justify-content-center";
                    el.style.width = "46px";
                    el.style.height = "46px";
                    el.style.fontSize = "0.65rem";
                    el.style.fontWeight = "800";
                }
            });
        }
        
        if (data.warning && data.warning.length) {
            data.warning.forEach(id => {
                const el = document.getElementById(`admin-pc-${id}`);
                if (el) {
                    el.className = "btn btn-warning p-0 border border-secondary d-flex align-items-center justify-content-center";
                    el.style.width = "46px";
                    el.style.height = "46px";
                    el.style.fontSize = "0.65rem";
                    el.style.fontWeight = "800";
                }
            });
        }
        
        if (data.pending && data.pending.length) {
            data.pending.forEach(id => {
                const el = document.getElementById(`admin-pc-${id}`);
                if (el) {
                    el.className = "btn btn-primary p-0 border border-secondary d-flex align-items-center justify-content-center";
                    el.style.width = "46px";
                    el.style.height = "46px";
                    el.style.fontSize = "0.65rem";
                    el.style.fontWeight = "800";
                }
            });
        }
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
            const response = await fetch('../action/admin_pc_control.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.text();
            console.log("Response:", result);

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