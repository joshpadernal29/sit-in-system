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

        .bg-open { background: #008000; color: white; }
        .bg-reserved { background: #ef4444; color: white; border-color: #b91c1c; }
        .bg-warning { background: #fbbf24; color: white; border-color: #000000; }

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
                <div class="p-2 text-muted small">Viewing logs for current session...</div>
            </div>
        </div>
    </div>

    <div class="main-panel">
        <div class="w-100 d-flex justify-content-between align-items-center mb-4 px-5">
            <div>
                <h4 class="fw-black mb-0"><span id="labHeading">LAB 544</span> MONITORING</h4>
                <p class="text-muted small mb-0">Lab Capacity: <span id="pcCountLabel">40</span> PCs</p>
            </div>
            <div class="d-flex gap-3">
                <select id="labSwitcher" class="form-select border-dark fw-bold shadow-none" onchange="switchLab(this.value)">
                    <option value="544">Lab 544</option>
                    <option value="542">Lab 542</option>
                    <option value="526">Lab 526</option>
                </select>
                <button class="btn btn-dark px-4 fw-bold shadow-sm" onclick="switchLab(document.getElementById('labSwitcher').value)">REFRESH</button>
            </div>
        </div>

        <div class="floor-plan-card">
            <div id="pcGrid" class="d-flex justify-content-around flex-wrap">
                </div>

            <div class="mt-5 d-flex gap-4 justify-content-center border-top pt-4">
                <span class="small fw-bold"><i class="bi bi-square-fill text-success me-1"></i> Available</span>
                <span class="small fw-bold"><i class="bi bi-square-fill text-danger me-1"></i> Occupied</span>
                <span class="small fw-bold"><i class="bi bi-square-fill text-warning me-1"></i> Maintenance</span>
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
    const labData = {
        '544': { total: 40, reserved: [5, 12, 24, 38], warning: [13, 20] },
        '542': { total: 30, reserved: [2, 15, 29], warning: [7] },
        '526': { total: 35, reserved: [10, 11, 33], warning: [1, 22] }
    };

    function switchLab(labNum) {
        document.getElementById('labHeading').innerText = `LAB ${labNum}`;
        document.getElementById('pcCountLabel').innerText = labData[labNum].total;
        renderLab(labNum);
    }

    function renderLab(labNum) {
        const config = labData[labNum];
        const pcGrid = document.getElementById('pcGrid');
        let html = '';
        let pcCounter = 1;
        const numIslands = Math.ceil(config.total / 8);

        for(let isl = 0; isl < numIslands; isl++) {
            html += `<div class="island"><div class="d-flex flex-column gap-2">`;
            for(let i = 0; i < 4; i++) {
                if (pcCounter <= config.total) {
                    html += generatePC(pcCounter, config);
                    pcCounter++;
                }
            }
            html += `</div><div class="spine"></div><div class="d-flex flex-column gap-2">`;
            for(let i = 0; i < 4; i++) {
                if (pcCounter <= config.total) {
                    html += generatePC(pcCounter, config);
                    pcCounter++;
                }
            }
            html += `</div></div>`;
        }
        pcGrid.innerHTML = html;
    }

    function generatePC(id, config) {
        let statusClass = 'bg-open';
        if (config.reserved.includes(id)) statusClass = 'bg-reserved';
        if (config.warning.includes(id)) statusClass = 'bg-warning';
        return `<div class="pc-unit ${statusClass}" onclick="alert('Manage pc-${id}')">pc-${id}</div>`;
    }

    // Modal logic for sidebar buttons
    function confirmAction(type, name, pc) {
        const title = document.getElementById('actionTitle');
        const desc = document.getElementById('actionDesc');
        const icon = document.getElementById('actionIcon');
        const btn = document.getElementById('confirmBtn');

        if(type === 'approve') {
            title.innerText = "Approve Reservation";
            desc.innerHTML = `Confirm <strong>${name}</strong> for <strong>PC-${pc}</strong>?`;
            icon.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
            btn.className = "btn btn-lg btn-success flex-grow-1 rounded-0 fw-bold";
        } else {
            title.innerText = "Reject Reservation";
            desc.innerHTML = `Reject request for <strong>PC-${pc}</strong>?`;
            icon.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i>';
            btn.className = "btn btn-lg btn-danger flex-grow-1 rounded-0 fw-bold";
        }
        new bootstrap.Modal(document.getElementById('actionModal')).show();
    }

    window.onload = () => switchLab('544');
</script>

</body>
</html>