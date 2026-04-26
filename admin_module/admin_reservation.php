<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control | SIT-IN Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { 
            --sidebar-w: 380px; 
            --admin-accent: #0f172a; 
            --glass-bg: rgba(255, 255, 255, 0.95);
        }
        
        body { 
            background-color: #f1f5f9; 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            overflow: hidden; 
        }

        /* Layout Structure */
        .admin-wrapper { 
            display: flex; 
            height: calc(100vh - 65px); 
        }
        
        /* Left Panel: Approvals & Logs */
        .side-panel {
            width: var(--sidebar-w);
            background: white;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        /* Right Panel: Interactive Lab */
        .main-panel {
            flex-grow: 1;
            padding: 2rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Floor Plan Card */
        .floor-plan-card {
            background: white;
            border: 3px solid var(--admin-accent);
            padding: 3rem;
            border-radius: 4px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            position: relative;
            min-width: 900px;
        }

        /* PC Unit Styling */
        .pc-unit {
            width: 44px; 
            height: 44px;
            border: 2px solid var(--admin-accent);
            display: flex; 
            align-items: center; 
            justify-content: center;
            font-size: 0.65rem; 
            font-weight: 800; 
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .pc-unit:hover { transform: scale(1.15); z-index: 5; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

        /* Status Colors */
        .bg-open { background: #008000; color: white; }
        .bg-reserved { background: #ef4444; color: white; border-color: #b91c1c; }
        .bg-warning { background: #fbbf24; color: white; border-color: #00000; }

        /* Cluster Layout */
        .island { display: flex; gap: 6px; margin-bottom: 20px; }
        .spine { width: 5px; background: var(--admin-accent); border-radius: 2px; }

        /* Request Items (Sidebar) */
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
                    <span class="badge bg-primary rounded-pill">2 Pending</span>
                </div>

                <div class="request-item bg-white border p-3 rounded mb-3 shadow-sm">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="status-pill bg-primary text-white">Pending Request</span>
                        <small class="text-muted">ID: 20260427</small>
                    </div>
                    <h6 class="mb-1 fw-bold">Juan Dela Cruz <span class="text-primary">• PC-12</span></h6>
                    <p class="text-muted small mb-3">Today @ 1:30 PM <br>Purpose: Web Dev Project</p>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success btn-action flex-grow-1 rounded-0" onclick="confirmAction('approve', 'Juan Dela Cruz', 12)">
                            APPROVE
                        </button>
                        <button class="btn btn-danger btn-action flex-grow-1 rounded-0" onclick="confirmAction('reject', 'Juan Dela Cruz', 12)">
                            REJECT
                        </button>
                    </div>
                </div>

                <div class="request-item bg-white border p-3 rounded mb-3 shadow-sm">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="status-pill bg-primary text-white">Pending Request</span>
                        <small class="text-muted">ID: 20260428</small>
                    </div>
                    <h6 class="mb-1 fw-bold">Maria Clara <span class="text-primary">• PC-24</span></h6>
                    <p class="text-muted small mb-3">Today @ 3:00 PM <br>Purpose: Cisco Packet Tracer</p>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success btn-action flex-grow-1 rounded-0" onclick="confirmAction('approve', 'Maria Clara', 24)">APPROVE</button>
                        <button class="btn btn-danger btn-action flex-grow-1 rounded-0" onclick="confirmAction('reject', 'Maria Clara', 24)">REJECT</button>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-logs">
                <div class="p-2">
                    <div class="border-start border-3 ps-3 mb-4">
                        <small class="text-muted">11:45 AM</small>
                        <p class="small mb-0"><strong>Admin</strong> manually set <strong>PC-13</strong> to Yellow.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-panel">
        <div class="w-100 d-flex justify-content-between align-items-center mb-4 px-5">
            <div>
                <h4 class="fw-black mb-0">LAB 544 MONITORING</h4>
                <p class="text-muted small mb-0">Click workstations to manually override status.</p>
            </div>
            <div class="d-flex gap-3">
                <select class="form-select border-dark fw-bold shadow-none">
                    <option>Lab 544</option>
                    <option>Lab 542</option>
                </select>
                <button class="btn btn-dark px-4 fw-bold shadow-sm">REFRESH</button>
            </div>
        </div>

        <div class="floor-plan-card">
            <div class="d-flex justify-content-around flex-wrap">
                <?php for($isl=0; $isl<5; $isl++): ?>
                    <div class="island">
                        <div class="d-flex flex-column gap-2">
                            <?php for($i=1; $i<=4; $i++): 
                                $id = ($isl * 8) + $i;
                                $status = ($id == 12 || $id == 24) ? 'bg-reserved' : 'bg-open';
                            ?>
                                <div class="pc-unit <?=$status?>" onclick="showControl(<?=$id?>, '<?=$status?>')">pc-<?=$id?></div>
                            <?php endfor; ?>
                        </div>
                        <div class="spine"></div>
                        <div class="d-flex flex-column gap-2">
                            <?php for($i=5; $i<=8; $i++): 
                                $id = ($isl * 8) + $i;
                                $status = ($id == 13) ? 'bg-warning' : 'bg-open';
                            ?>
                                <div class="pc-unit <?=$status?>" onclick="showControl(<?=$id?>, '<?=$status?>')">pc-<?=$id?></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="mt-5 d-flex gap-4 justify-content-center border-top pt-4">
                <span class="small fw-bold"><i class="bi bi-square-fill text-success me-1"></i> Available</span>
                <span class="small fw-bold"><i class="bi bi-square-fill text-danger me-1"></i> Occupied</span>
                <span class="small fw-bold"><i class="bi bi-square-fill text-warning me-1"></i> Maintenance</span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pcControlModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-0 border-dark">
            <div class="modal-header bg-dark text-white rounded-0 py-2">
                <h6 class="modal-title fw-bold">Control: PC-<span id="displayPC"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-success btn-sm rounded-0 fw-bold py-2">SET OPEN (GREEN)</button>
                    <button class="btn btn-outline-danger btn-sm rounded-0 fw-bold py-2">SET TAKEN (RED)</button>
                    <button class="btn btn-outline-warning btn-sm rounded-0 fw-bold py-2 text-dark">SET BROKEN (YELLOW)</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="actionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0 border-dark shadow-lg">
            <div class="modal-body p-5 text-center">
                <div id="actionIcon" class="display-3 mb-3"></div>
                <h4 class="fw-bold" id="actionTitle">Confirm Action</h4>
                <p id="actionDesc" class="text-muted mb-4"></p>
                <div class="d-flex gap-3 mt-2">
                    <button id="confirmBtn" class="btn btn-lg flex-grow-1 rounded-0 fw-bold">PROCEED</button>
                    <button class="btn btn-lg btn-outline-dark flex-grow-1 rounded-0" data-bs-dismiss="modal">CANCEL</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showControl(id, status) {
        document.getElementById('displayPC').innerText = id;
        new bootstrap.Modal(document.getElementById('pcControlModal')).show();
    }

    function confirmAction(type, name, pc) {
        const title = document.getElementById('actionTitle');
        const desc = document.getElementById('actionDesc');
        const icon = document.getElementById('actionIcon');
        const btn = document.getElementById('confirmBtn');

        if(type === 'approve') {
            title.innerText = "Approve Reservation";
            desc.innerHTML = `Confirm <strong>${name}</strong> for <strong>PC-${pc}</strong>? <br>A notification will be sent to the student.`;
            icon.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
            btn.className = "btn btn-lg btn-success flex-grow-1 rounded-0 fw-bold";
        } else {
            title.innerText = "Reject Reservation";
            desc.innerHTML = `Reject <strong>${name}</strong>'s request for <strong>PC-${pc}</strong>? <br>Please ensure this is valid.`;
            icon.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i>';
            btn.className = "btn btn-lg btn-danger flex-grow-1 rounded-0 fw-bold";
        }
        new bootstrap.Modal(document.getElementById('actionModal')).show();
    }
</script>

</body>
</html>