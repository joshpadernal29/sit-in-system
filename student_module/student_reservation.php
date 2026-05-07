<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../config/database.php");
include("../action/studentData.php"); // student session
include("../action/sit_in_reserve.php");
//echo($student_id);
$student_pk = $student['id']; // get students pk id 
//echo($student_pk);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIT-IN | Floor Plan Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

<?php include("../includes/studentHeader.php"); ?>

<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-center bg-white p-4 border border-dark shadow-sm">
            <div>
                <h3 class="fw-bold text-dark mb-1 text-uppercase">Laboratory Floor Plan</h3>
                <p class="text-muted small mb-0">Select an <span class="text-success fw-bold">Available</span> PC unit to submit a reservation request.</p>
            </div>
            <div class="d-flex gap-3 mt-3 mt-md-0">
                <select id="labSwitcher" class="form-select border-dark fw-bold rounded-0 shadow-none" onchange="syncStudentDashboard()">
                    <option value="544">LAB 544</option>
                    <option value="542">LAB 542</option>
                    <option value="526">LAB 526</option>
                </select>
                <button class="btn btn-dark rounded-0 px-4 fw-bold shadow-sm" onclick="syncStudentDashboard()">REFRESH</button>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border border-dark rounded-0 shadow-sm">
                <div class="card-body p-5 bg-white">
                    <div id="studentGridContainer" class="d-flex flex-wrap justify-content-center gap-4">
                        </div>

                    <div class="mt-5 d-flex flex-wrap gap-4 justify-content-center border-top pt-4">
                        <span class="small fw-bold text-success"><i class="bi bi-square-fill me-1"></i> Available</span>
                        <span class="small fw-bold text-danger"><i class="bi bi-square-fill me-1"></i> Reserved</span>
                        <span class="small fw-bold text-warning"><i class="bi bi-square-fill me-1"></i> Pending</span>
                        <span class="small fw-bold" style="color: #ea580c;"><i class="bi bi-square-fill me-1"></i> Under Maintenance</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--RESERVE MODAL-->
<div class="modal fade" id="resModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-dark rounded-0 shadow-lg">
            <div class="modal-header bg-dark text-white rounded-0 py-3">
                <h5 class="modal-title fw-bold text-uppercase"><i class="bi bi-pencil-square me-2"></i>Apply for Reservation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../action/sit_in_reserve.php" method="POST">
                <input type="hidden" name="student_pk_id" value="<?php echo $student_pk ?? '1'; ?>">
                <input type="hidden" name="pc_number" id="modal_pc_number">
                <input type="hidden" name="lab_name" id="modal_lab_name">

                <div class="modal-body p-4">
                    <div class="text-center mb-4 bg-light p-3 border border-secondary">
                        <h2 class="fw-bold mb-0 text-dark" id="display_pc">PC-00</h2>
                        <p class="text-muted small mb-0 fw-bold text-uppercase" id="display_lab">LAB 544</p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Reservation Date</label>
                            <input type="date" class="form-control border-dark rounded-0 shadow-none" name="res_date" required min="<?= date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Start Time</label>
                            <input type="time" class="form-control border-dark rounded-0 shadow-none" name="res_time" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Purpose of Use</label>
                        <select class="form-select border-dark rounded-0 shadow-none" name="sit_in_purpose" required>
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
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-outline-dark rounded-0 fw-bold px-4" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" name="reserve_pc" class="btn btn-dark rounded-0 fw-bold px-4">CONFIRM APPLICATION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let studentHasPending = false;

    async function syncStudentDashboard() {
        const lab = document.getElementById('labSwitcher').value;
        const studentId = <?php echo $_SESSION['student_id'] ?? '1'; ?>;
        
        const response = await fetch(`../action/get_lab_status.php?lab=${lab}`);
        const data = await response.json();
        
        // Check if this student already has a pending reservation
        studentHasPending = (data.student_pending && data.student_pending.includes(studentId));

        const gridContainer = document.getElementById('studentGridContainer');
        gridContainer.innerHTML = ''; // Clear
        
        let pcCounter = 1;
        const numIslands = Math.ceil(data.total / 8);

        for (let isl = 0; isl < numIslands; isl++) {
            // Island Container (Using Bootstrap Flex)
            const island = document.createElement('div');
            island.className = "d-flex gap-2 mb-3 border p-2 bg-light";

            // Left Bank
            const leftBank = document.createElement('div');
            leftBank.className = "d-flex flex-column gap-2";
            for (let i = 0; i < 4; i++) {
                if (pcCounter <= data.total) {
                    leftBank.appendChild(createPCUnit(pcCounter, data));
                    pcCounter++;
                }
            }

            // Spine
            const spine = document.createElement('div');
            spine.className = "bg-dark rounded-pill";
            spine.style.width = "5px";

            // Right Bank
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
    }

    function createPCUnit(id, data) {
        const btn = document.createElement('div');
        // Initial Available Styling (Pure Bootstrap)
        btn.className = "btn btn-success d-flex align-items-center justify-content-center border-secondary rounded-0 shadow-sm fw-bold p-0";
        btn.style.width = "46px";
        btn.style.height = "46px";
        btn.style.fontSize = "0.65rem";
        btn.innerText = `PC-${id}`;
        btn.onclick = () => openModal(id);

        // Apply Status based on data
        if (data.reserved && data.reserved.includes(id)) {
            btn.className = btn.className.replace('btn-success', 'btn-danger') + " pe-none";
        } else if (data.pending && data.pending.includes(id)) {
            btn.className = btn.className.replace('btn-success', 'btn-warning') + " pe-none text-dark";
        } else if (data.maintenance && data.maintenance.includes(id)) {
            // Orange for maintenance
            btn.className = btn.className.replace('btn-success', '') + " pe-none text-white";
            btn.style.backgroundColor = "#ea580c";
        }

        return btn;
    }

    function openModal(pcNumber) {
        if (studentHasPending) {
            alert("You cannot reserve another PC while you have a pending request.");
            return;
        }

        const labName = document.getElementById('labSwitcher').value;
        
        // Set values to hidden inputs
        document.getElementById('modal_pc_number').value = pcNumber;
        document.getElementById('modal_lab_name').value = labName;
        
        // Set values for display
        document.getElementById('display_pc').innerText = "PC-" + pcNumber;
        document.getElementById('display_lab').innerText = labName;

        // Open Modal
        var myModal = new bootstrap.Modal(document.getElementById('resModal'));
        myModal.show();
    }

    window.onload = () => {
        syncStudentDashboard();
        setInterval(syncStudentDashboard, 15000); // Auto refresh
    };
</script>

</body>
</html>