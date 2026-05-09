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
                        <!-- GRID DISPLAY -->
                        <div class="col-xl-8">
                            <div class="card border border-dark shadow-sm">
                                <div class="card-header bg-dark text-white d-flex justify-content-between">
                                    <span id="labHeading" class="fw-bold">LAB-544 MONITORING</span>
                                    <span class="badge bg-light text-dark" id="pcCountLabel">0 Units</span>
                                </div>
                                <div class="card-body">
                                    <div id="adminGridContainer" class="d-flex flex-wrap justify-content-center gap-3"></div>
                                </div>
                            </div>
                        </div>

                        <!-- QUEUE LIST -->
                        <div class="col-xl-4">
                            <div class="card border border-dark shadow-sm">
                                <div class="card-header bg-secondary text-white">
                                    Queue <span class="badge bg-danger float-end"><?= $total_pending_requests ?></span>
                                </div>
                                <div class="card-body p-2" style="max-height:500px; overflow:auto;">
                                    <?php while($pendingRequest = mysqli_fetch_assoc($row)): ?>
                                        <div class="p-2 border mb-2 bg-light">
                                            <div class="fw-bold small"><?= htmlspecialchars($pendingRequest['fullname']) ?></div>
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