<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/database.php");
include("../action/studentData.php"); // student session
include("../action/sit_in_reserve.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIT-IN | PC Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --blueprint-line: #334155;
            --pc-open: #22c55e;
            --pc-taken: #ef4444;
            --pc-broken: #f59e0b;
        }
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }

        .lab-container {
            background: white;
            border: 3px solid var(--blueprint-line);
            border-radius: 4px;
            padding: 30px;
            position: relative;
            max-width: 1000px;
            margin: 20px auto;
        }

        .pc-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 25px;
        }

        .island {
            display: flex;
            gap: 6px;
            margin-bottom: 20px;
        }
        
        .bank {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .spine { width: 5px; background: var(--blueprint-line); border-radius: 2px; }

        .pc-box {
            width: 44px;
            height: 44px;
            border: 2px solid var(--blueprint-line);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s;
        }
        
        .pc-box:hover { transform: scale(1.15); z-index: 5; }

        .green { background: #dcfce7; color: #166534; border-color: var(--pc-open); }
        .red { background: #fee2e2; color: #991b1b; border-color: #b91c1c; cursor: not-allowed; }
        .yellow { background: #fef3c7; color: #92400e; border-color: #f59e0b; cursor: not-allowed; }

        .filter-section {
            background: white;
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

<?php include("../includes/studentHeader.php"); ?>

<div class="filter-section shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">Laboratory Reservation</h5>
            <small class="text-muted">Select a laboratory and click a PC to reserve</small>
        </div>
        <div style="width: 250px;">
            <select class="form-select fw-bold shadow-none border-dark" id="labNameSelect" onchange="syncStudentLayout()">
                <option value="LAB-544">Lab 544</option>
                <option value="LAB-542">Lab 542</option>
                <option value="LAB-526">Lab 526</option>
            </select>
        </div>
    </div>
</div>

<div class="container">
    <div class="d-flex gap-4 mb-4 justify-content-center">
        <div class="small fw-bold"><i class="bi bi-square-fill text-success"></i> Open</div>
        <div class="small fw-bold"><i class="bi bi-square-fill text-danger"></i> Occupied</div>
        <div class="small fw-bold"><i class="bi bi-square-fill text-warning"></i> Maintenance</div>
    </div>

    <div class="lab-container shadow-sm">
        <div class="pc-grid" id="pcGridContainer">
            </div>
    </div>
</div>
<!--SIT-IN RESERVE MODAL-->
<div class="modal fade" id="reservationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0 border-dark shadow-lg">
            <div class="modal-header bg-dark text-white rounded-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pc-display me-2"></i>Reserve Workstation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../action/sit_in_reserve.php" method="POST">
                <input type="hidden" name="student_pk_id" value="<?php echo $_SESSION['student_id'] ?? '1'; ?>">
                <input type="hidden" name="pc_number" id="pcNumberInput">
                <input type="hidden" name="lab_name" id="labNameInput">

                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-0" id="pcTitle">PC-00</h2>
                        <span class="text-muted" id="labTitle">Lab 544</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">RESERVATION DATE</label>
                        <input type="date" class="form-control rounded-0" name="res_date" required min="<?= date('Y-m-d'); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">START TIME</label>
                        <input type="time" class="form-control rounded-0" name="res_time" required>
                        <div class="form-text mt-2 text-primary">
                            <i class="bi bi-info-circle"></i> Logout determined by manual logout or class schedule.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">SIT-IN PURPOSE</label>
                        <select class="form-select rounded-0" name="sit_in_purpose">
                            <option>Research</option>
                            <option>Programming Task</option>
                            <option>Exam / Quiz</option>
                            <option>Self Study</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-dark rounded-0 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-0 px-4 fw-bold" name="reserve_pc">Confirm Reservation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentLab = 'LAB-544';

    async function syncStudentLayout() {
        currentLab = document.getElementById('labNameSelect').value;
        const response = await fetch(`../action/get_lab_status.php?lab=${currentLab}`);
        const data = await response.json();
        
        document.getElementById('pcCountLabel') && (document.getElementById('pcCountLabel').innerText = data.total);
        
        // Build islands based on the total number of PCs
        const gridContainer = document.getElementById('pcGridContainer');
        let html = '';
        let pcCounter = 1;
        const numIslands = Math.ceil(data.total / 8);

        for (let isl = 0; isl < numIslands; isl++) {
            html += `<div class="island"><div class="bank d-flex flex-column gap-2">`;
            
            for (let i = 0; i < 4; i++) {
                if (pcCounter <= data.total) {
                    html += `<div class="pc-box green" id="pc-${pcCounter}" onclick="openReserve(${pcCounter}, 'green')">pc-${pcCounter}</div>`;
                    pcCounter++;
                }
            }
            
            html += `</div><div class="spine"></div><div class="bank d-flex flex-column gap-2">`;
            
            for (let i = 0; i < 4; i++) {
                if (pcCounter <= data.total) {
                    html += `<div class="pc-box green" id="pc-${pcCounter}" onclick="openReserve(${pcCounter}, 'green')">pc-${pcCounter}</div>`;
                    pcCounter++;
                }
            }
            
            html += `</div></div>`;
        }
        
        gridContainer.innerHTML = html;

        // Apply any live reservation indicators from the database
        data.reserved.forEach(id => {
            const pcElement = document.getElementById(`pc-${id}`);
            if (pcElement) {
                pcElement.className = 'pc-box red';
                pcElement.onclick = () => alert("This PC is already occupied.");
            }
        });
    }

    function openReserve(id, status) {
        if (status === 'red') {
            alert("This PC is already occupied.");
            return;
        }

        const labSelect = document.getElementById('labNameSelect');
        document.getElementById('pcNumberInput').value = id;
        document.getElementById('labNameInput').value = labSelect.value;
        
        document.getElementById('pcTitle').innerText = "PC-" + id;
        document.getElementById('labTitle').innerText = labSelect.options[labSelect.selectedIndex].text;

        var myModal = new bootstrap.Modal(document.getElementById('reservationModal'));
        myModal.show();
    }

    window.onload = () => {
        syncStudentLayout();
        setInterval(syncStudentLayout, 10000);
    };
</script>

</body>
</html>