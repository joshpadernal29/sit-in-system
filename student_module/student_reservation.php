<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/studentData.php"); // for session 

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

        /* Compact Floor Plan */
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
            display: grid;
            grid-template-columns: 45px 6px 45px;
            grid-template-rows: repeat(4, 45px);
            gap: 8px;
            padding: 10px;
            border: 1px solid #e2e8f0;
        }

        .spine { background: var(--blueprint-line); grid-column: 2; grid-row: 1 / span 4; border-radius: 2px; }

        .pc-box {
            border: 2px solid var(--blueprint-line);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .green { background: #dcfce7; color: #166534; border-color: var(--pc-open); }
        .green:hover { transform: scale(1.1); z-index: 10; }
        .red { background: #fee2e2; color: #991b1b; border-color: var(--pc-taken); cursor: not-allowed; }
        .yellow { background: #fef3c7; color: #92400e; border-color: var(--pc-broken); cursor: not-allowed; }

        /* Header Filters */
        .filter-section {
            background: white;
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
<!--HEADER-->
<?php include("../includes/studentHeader.php"); ?>

<div class="filter-section shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">Laboratory Reservation</h5>
            <small class="text-muted">Select a laboratory and click a PC to reserve</small>
        </div>
        <div style="width: 250px;">
            <select class="form-select fw-bold shadow-none border-dark">
                <option>Lab 542</option>
                <option>Lab 544</option>
                <option>Lab 526</option>
                <option>Lab 524</option>
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
        <div class="pc-grid">
            <?php for($isl=1; $isl<=5; $isl++): ?>
            <div class="island">
                <?php for($i=1; $i<=4; $i++): 
                    $id = (($isl-1)*8)+$i; 
                    $status = ($id % 9 == 0) ? 'red' : 'green';
                ?>
                    <div class="pc-box <?=$status?>" onclick="openReserve(<?=$id?>, '<?=$status?>')">pc<?=$id?></div>
                <?php endfor; ?>

                <div class="spine"></div>

                <?php for($i=5; $i<=8; $i++): 
                    $id = (($isl-1)*8)+$i; 
                    $status = ($id == 13 || $id == 27) ? 'yellow' : 'green';
                ?>
                    <div class="pc-box <?=$status?>" onclick="openReserve(<?=$id?>, '<?=$status?>')"><?=$id?></div>
                <?php endfor; ?>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="reservationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0 border-dark shadow-lg">
            <div class="modal-header bg-dark text-white rounded-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pc-display me-2"></i>Reserve Workstation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_reservation.php" method="POST">
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-black mb-0" id="pcTitle">PC-00</h2>
                        <span class="text-muted">Selected Workstation</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">RESERVATION DATE</label>
                        <input type="date" class="form-control rounded-0" name="res_date" required min="<?= date('Y-m-d'); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">START TIME</label>
                        <input type="time" class="form-control rounded-0" name="res_time" required>
                        <div class="form-text mt-2 text-primary">
                            <i class="bi bi-info-circle"></i> Logout will be determined by manual logout or class schedule.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">SIT-IN PURPOSE</label>
                        <select class="form-select rounded-0" name="purpose">
                            <option>Research</option>
                            <option>Programming Task</option>
                            <option>Exam / Quiz</option>
                            <option>Self Study</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-dark rounded-0 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-0 px-4 fw-bold">Confirm Reservation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openReserve(id, status) {
        if(status === 'red') {
            alert("This PC is already occupied.");
            return;
        }
        if(status === 'yellow') {
            alert("This PC is currently under maintenance.");
            return;
        }

        document.getElementById('pcTitle').innerText = "PC-" + id;
        var myModal = new bootstrap.Modal(document.getElementById('reservationModal'));
        myModal.show();
    }
</script>

</body>
</html>