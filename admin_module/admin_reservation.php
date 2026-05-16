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

    <!-- UI Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed: 80px;
        }

        /* ================= SIDEBAR SAFE ================= */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            padding-right: 12px;
        }

        body.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
            }
        }

        /* ================= GRID IMPROVEMENT ================= */
        #adminGridContainer {
            transform: scale(0.98);
            transform-origin: top center;
        }

        /* ================= PC BUTTON (FIXED CENTER + BIG TEXT) ================= */
        .pc-unit {
            width: 60px !important;
            height: 60px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.78rem !important;
            font-weight: 800 !important;
            text-align: center !important;
            line-height: 1 !important;
            padding: 0 !important;
            border-radius: 6px !important;
            transition: 0.2s ease;
        }

        .pc-unit:hover {
            transform: scale(1.08);
            z-index: 2;
        }

        /* Custom badge sizes for software list */
        .software-tag {
            font-size: 0.72rem;
            letter-spacing: 0.3px;
        }

        body {
            overflow-x: hidden;
        }
    </style>
</head>

<body class="bg-light">

    <?php include("../includes/admin_sidebar.php"); ?>

    <div class="main-content">
        <div class="container-fluid px-3 px-md-4 py-3 py-md-4">

            <!-- HEADER -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Admin Management Dashboard</h4>
                    <p class="text-muted small mb-0">Monitor PCs, reservations, and system logs.</p>
                </div>

                <div class="d-flex gap-2">
                    <select id="labSwitcher" class="form-select border-dark fw-bold w-auto" onchange="syncAdminDashboard()">
                        <option value="544">Lab 544</option>
                        <option value="542">Lab 542</option>
                        <option value="526">Lab 526</option>
                    </select>
                    <button class="btn btn-dark fw-bold" onclick="syncAdminDashboard()">
                        <i class="bi bi-arrow-repeat"></i> SYNC
                    </button>
                </div>
            </div>

            <!-- TABS NAVIGATION -->
            <ul class="nav nav-pills gap-2 mb-4 flex-column flex-sm-row">
                <li class="nav-item flex-fill text-center">
                    <button class="nav-link active border border-dark w-100 fw-bold" data-bs-toggle="tab" data-bs-target="#pc-control">
                        PC Control
                    </button>
                </li>
                <li class="nav-item flex-fill text-center">
                    <button class="nav-link border border-dark w-100 fw-bold" data-bs-toggle="tab" data-bs-target="#reserved-list">
                        Approved
                    </button>
                </li>
                <li class="nav-item flex-fill text-center">
                    <button class="nav-link border border-dark w-100 fw-bold" data-bs-toggle="tab" data-bs-target="#logs">
                        Logs
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- ================= PC CONTROL TAB ================= -->
                <div class="tab-pane fade show active" id="pc-control">
                    <div class="row g-3">
                        
                        <!-- LEFT COLUMN: GRID DISPLAY + SOFTWARE INVENTORY CARD -->
                        <div class="col-xl-8 d-flex flex-column gap-3">
                            
                            <!-- GRID MONITORING CONTAINER -->
                            <div class="card border border-dark shadow-sm">
                                <div class="card-header bg-dark text-white d-flex justify-content-between">
                                    <span id="labHeading" class="fw-bold">LAB-544 MONITORING</span>
                                    <span class="badge bg-light text-dark" id="pcCountLabel">0 Units</span>
                                </div>
                                <div class="card-body">
                                    <div id="adminGridContainer" class="d-flex flex-wrap justify-content-center gap-3"></div>
                                </div>
                            </div>

                            <!-- NEW ADDITION: LAB ENVIRONMENT SOFTWARE APPLICATIONS PROFILES -->
                            <div class="card border border-dark shadow-sm">
                                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                    <span class="fw-bold"><i class="bi bi-cpu me-2"></i>Available Lab Software Suite</span>
                                    <span class="badge bg-light text-dark shadow-sm" id="softwareCountLabel">0 Apps Configured</span>
                                </div>
                                <div class="card-body bg-white" style="max-height: 280px; overflow-y: auto;">
                                    <div class="row g-2" id="softwareAppGrid">
                                        <!-- Real-time elements are appended here asynchronously -->
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- RIGHT COLUMN: QUEUE LIST -->
                        <div class="col-xl-4">
                            <div class="card border border-dark shadow-sm">
                                <div class="card-header bg-secondary text-white">
                                    Queue <span class="badge bg-danger float-end"><?= $total_pending_requests ?></span>
                                </div>
                                <div class="card-body p-2" style="max-height:500px; overflow:auto;">
                                    <?php while($pendingRequest = mysqli_fetch_assoc($row)): ?>
                                        <div class="p-3 border rounded mb-3 bg-light shadow-sm">
                                            <!-- Header: Name & Lab -->
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-primary">
                                                    <i class="bi bi-user me-1"></i> <?= htmlspecialchars($pendingRequest['fullname']) ?>
                                                </span>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-flask me-1"></i> LAB-<?= htmlspecialchars($pendingRequest['lab_name']) ?>
                                                </span>
                                            </div>

                                            <!-- Details: PC Name and Schedule -->
                                            <div class="row g-0 mt-3 pt-2 border-top align-items-center">
                                                <!-- Left Side: PC Info -->
                                                <div class="col-5 border-end">
                                                    <div class="small text-muted mb-1 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.8px; font-weight: 700;">
                                                        PC Reserved
                                                    </div>
                                                    <div class="d-inline-flex align-items-center px-2 py-1 rounded bg-white border">
                                                        <i class="bi bi-display me-2 text-primary" style="font-size: 0.85rem;"></i> 
                                                        <span class="fw-bold text-dark small"><?= htmlspecialchars($pendingRequest['pc_number']) ?></span>
                                                    </div>
                                                </div>

                                                <!-- Right Side: Schedule Info -->
                                                <div class="col-7 ps-3">
                                                    <div class="small text-muted mb-1 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.8px; font-weight: 700;">
                                                        Scheduled Date & Time
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <div class="fw-semibold text-dark small mb-0">
                                                            <i class="bi bi-calendar-check me-1 text-success"></i>
                                                            <?= date('M d, Y', strtotime($pendingRequest['schedule_date'])) ?>
                                                        </div>
                                                        <div class="text-muted" style="font-size: 0.8rem;">
                                                            <i class="bi bi-clock me-1"></i>
                                                            <?= date('h:i A', strtotime($pendingRequest['schedule_time'])) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action Buttons Form -->
                                            <form action="../action/admin_pc_control.php" method="post" class="mt-3">
                                                <input type="hidden" name="request_id" value="<?= $pendingRequest['id'] ?>">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <button type="submit" name="approve" value="approve" class="btn btn-sm btn-success w-100 fw-bold">
                                                            <i class="bi bi-check-circle me-1"></i> Approve
                                                        </button>
                                                    </div>
                                                    <div class="col-6">
                                                        <button type="submit" name="reject" value="reject" class="btn btn-sm btn-danger w-100 fw-bold">
                                                            <i class="bi bi-times-circle me-1"></i> Reject
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= APPROVED TAB ================= -->
                <div class="tab-pane fade" id="reserved-list">
                    <div class="card border border-dark shadow-sm">
                        <div class="card-header bg-dark text-white">Approved Reservations</div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light text-uppercase small">
                                    <tr>
                                        <th>Name</th>
                                        <th>Lab</th>
                                        <th>PC</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($approved = mysqli_fetch_assoc($approvedResult)): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($approved['fullname']) ?></td>
                                            <td><?= htmlspecialchars($approved['lab_name']) ?></td>
                                            <td><span class="badge bg-secondary">PC-<?= $approved['pc_number'] ?></span></td>
                                            <td><?= $approved['schedule_date'] ?></td>
                                            <td><span class="badge bg-success"><?= ucfirst($approved['action']) ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ================= LOGS TAB ================= -->
                <div class="tab-pane fade" id="logs">
                    <div class="card border border-dark shadow-sm">
                        <div class="card-header bg-dark text-white">System Logs</div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-striped mb-0">
                                <thead class="table-light text-uppercase small">
                                    <tr>
                                        <th>Name</th>
                                        <th>Lab</th>
                                        <th>PC</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($logs = mysqli_fetch_assoc($logsResult)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($logs['fullname']) ?></td>
                                            <td><?= htmlspecialchars($logs['lab_name']) ?></td>
                                            <td><span class="badge bg-secondary">PC-<?= $logs['pc_number'] ?></span></td>
                                            <td><?= $logs['schedule_date'] ?></td>
                                            <td>
                                                <?php
                                                    $badge = 'bg-secondary';
                                                    if($logs['action']=='approved') $badge='bg-success';
                                                    if($logs['action']=='rejected') $badge='bg-danger';
                                                    if($logs['action']=='pending') $badge='bg-warning text-dark';
                                                ?>
                                                <span class="badge <?= $badge ?>"><?= ucfirst($logs['action']) ?></span>
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

    <!-- MODAL FOR PC CONTROL -->
    <div class="modal fade" id="pcSettingModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">PC Control</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <h4 id="modalPcTitle">PC-00</h4>
                    <input type="hidden" id="selectedPcNumber">
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button class="btn btn-success" onclick="processToggle('available')">Available</button>
                        <button class="btn btn-warning" onclick="processToggle('unavailable')">Unavailable</button>
                        <button class="btn btn-danger" onclick="processToggle('maintenance')">Maintenance</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function syncAdminDashboard() {
            const lab = document.getElementById('labSwitcher').value;
            document.getElementById('labHeading').innerText = lab + " MONITORING";

            // Fetch Software Inventory concurrently with PC configurations
            fetchSoftwareInventory(lab);

            try {
                const res = await fetch(`../action/get_lab_status.php?lab=${lab}`);
                const data = await res.json();

                document.getElementById('pcCountLabel').innerText = data.total + " Units";
                const grid = document.getElementById('adminGridContainer');
                grid.innerHTML = '';

                let pc = 1;
                const islands = Math.ceil(data.total / 8);

                for (let i = 0; i < islands; i++) {
                    let wrap = document.createElement('div');
                    wrap.className = "d-flex gap-2";
                    wrap.innerHTML = `
                        <div id="l-${i}" class="d-flex flex-column gap-2"></div>
                        <div class="bg-dark" style="width:4px;"></div>
                        <div id="r-${i}" class="d-flex flex-column gap-2"></div>
                    `;
                    grid.appendChild(wrap);

                    // Left side of island (4 PCs)
                    for (let j = 0; j < 4; j++) {
                        if (pc <= data.total) {
                            document.getElementById(`l-${i}`).appendChild(createPC(pc, data));
                            pc++;
                        }
                    }
                    // Right side of island (4 PCs)
                    for (let j = 0; j < 4; j++) {
                        if (pc <= data.total) {
                            document.getElementById(`r-${i}`).appendChild(createPC(pc, data));
                            pc++;
                        }
                    }
                }
            } catch (error) {
                console.error("Sync failed:", error);
            }
        }

        // Asynchronously populate the newly imported software metrics
        async function fetchSoftwareInventory(labId) {
            try {
                const response = await fetch(`../action/get_lab_software.php?lab=${labId}`);
                const softwareList = await response.json();
                
                const appGrid = document.getElementById('softwareAppGrid');
                document.getElementById('softwareCountLabel').innerText = `${softwareList.length} Apps Configured`;
                appGrid.innerHTML = '';

                if(softwareList.length === 0) {
                    appGrid.innerHTML = `
                        <div class="col-12 text-center py-4 text-muted">
                            <i class="bi bi-app-indicator display-6 opacity-25"></i>
                            <p class="small mb-0 mt-1">No applications linked to this lab instance repository.</p>
                        </div>`;
                    return;
                }

                softwareList.forEach(app => {
                    const card = document.createElement('div');
                    card.className = "col-md-6 col-lg-4";
                    card.innerHTML = `
                        <div class="p-2 border rounded bg-light shadow-sm d-flex align-items-center h-100">
                            <div class="p-2 rounded bg-dark-subtle me-2">
                                <i class="bi bi-box-seam-fill text-dark small"></i>
                            </div>
                            <div class="overflow-hidden flex-grow-1">
                                <div class="fw-bold text-dark text-truncate small mb-0">${app.software_name}</div>
                                <div class="text-secondary text-truncate" style="font-size:0.7rem;">${app.developer}</div>
                                <div class="mt-1 d-flex gap-1 flex-wrap">
                                    <span class="badge bg-white text-dark border software-tag">v${app.version}</span>
                                    <span class="badge bg-secondary-subtle text-dark border software-tag">${app.license_type}</span>
                                </div>
                            </div>
                        </div>`;
                    appGrid.appendChild(card);
                });
            } catch (err) {
                console.error("Failed loading software manifest profile logs:", err);
            }
        }

        function createPC(id, data) {
            const d = document.createElement('div');
            d.className = "btn btn-success pc-unit";
            d.innerText = "PC-" + id;

            const pcId = id.toString();
            const isMaint = data.maintenance?.map(String).includes(pcId);
            const isOccupied = (data.active?.map(String).includes(pcId) || data.reserved?.map(String).includes(pcId));
            const isPending = data.pending?.map(String).includes(pcId);
            const isUnavail = data.unavailable?.map(String).includes(pcId);

            if (isMaint || isOccupied) {
                d.classList.replace("btn-success", "btn-danger");
            } else if (isPending) {
                d.classList.replace("btn-success", "btn-primary");
            } else if (isUnavail) {
                d.classList.replace("btn-success", "btn-warning");
                d.classList.add("text-dark");
            }

            d.onclick = () => openPcModal(id);
            return d;
        }

        function openPcModal(id) {
            document.getElementById('modalPcTitle').innerText = "PC-" + id;
            document.getElementById('selectedPcNumber').value = id;
            new bootstrap.Modal(document.getElementById('pcSettingModal')).show();
        }

        async function processToggle(status) {
            const f = new FormData();
            f.append("lab_name", document.getElementById('labSwitcher').value);
            f.append("pc_number", document.getElementById('selectedPcNumber').value);
            f.append("status", status);

            await fetch("../action/admin_pc_control.php", { method: "POST", body: f });
            bootstrap.Modal.getInstance(document.getElementById('pcSettingModal')).hide();
            syncAdminDashboard();
        }

        window.onload = () => {
            syncAdminDashboard();
            setInterval(syncAdminDashboard, 5000);
        };
    </script>
</body>
</html>