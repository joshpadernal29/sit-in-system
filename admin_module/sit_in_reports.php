<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);

include("../action/admin_report.php"); 
include("../config/database.php");
include("../action/Data_count.php"); 

$active_sessions = currentSitIns($conn);
$total_Sessions = getTotalSessions($conn);
$user_registered = countStudents($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generate Reports | CCS ADMIN PANEL</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
    /* ================= THEME VARIABLES ================= */
    :root {
        --bg-body: #f5f7fb;
        --bg-card: #ffffff;
        --text-main: #495057;
        --text-muted: #6c757d;
        --border-color: #e9ecef;
        --input-bg: #ffffff;
        --table-header: #f8f9fc;
    }

    [data-theme="dark"] {
        --bg-body: #121212 !important;
        --bg-card: #1e1e1e !important;
        --text-main: #e0e0e0 !important;
        --text-muted: #a0a0a0 !important;
        --border-color: #333333 !important;
        --input-bg: #2d2d2d !important;
        --table-header: #252525 !important;
    }

    body { 
        margin: 0; 
        background: var(--bg-body); 
        color: var(--text-main);
        font-family: 'Inter', sans-serif; 
        transition: background 0.3s, color 0.3s;
    }

    .main-content { margin-left: 260px; padding: 2rem; transition: margin-left .3s ease; }
    .sidebar.collapsed ~ .main-content { margin-left: 80px; }

    /* ================= CARD & UI ================= */
    .custom-card { 
        background: var(--bg-card) !important; 
        border: 1px solid var(--border-color) !important;
        border-radius: 18px; padding: 1.5rem; 
        box-shadow: 0 4px 12px rgba(0,0,0,.03); 
        color: var(--text-main) !important;
    }

    .date-badge { 
        background: var(--bg-card); 
        border: 1px solid var(--border-color); 
        color: var(--text-main);
        padding: .8rem 1rem; border-radius: 12px; font-weight: 600; 
    }

    .stats-card {
        background: var(--bg-card) !important; 
        border: 1px solid var(--border-color) !important;
        border-radius: 18px; padding: 1.3rem; display: flex; align-items: center; gap: 1rem;
    }

    /* ================= INPUTS & SELECTS ================= */
    .form-control, .form-select, .custom-input {
        background-color: var(--input-bg) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-main) !important;
        border-radius: 12px;
    }

    /* Fix for Date Input Icons in Dark Mode */
    [data-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    /* ================= TABLE FIXES ================= */
    .table {
        color: var(--text-main) !important;
        background-color: transparent !important;
    }

    .table thead th {
        background-color: var(--table-header) !important;
        color: var(--text-muted) !important;
        border-bottom: 2px solid var(--border-color) !important;
    }

    .table tbody td {
        background-color: var(--bg-card) !important;
        color: var(--text-main) !important;
        border-color: var(--border-color) !important;
    }

    /* Striped row fix for Dark Mode */
    [data-theme="dark"] .table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-accent-bg: rgba(255, 255, 255, 0.02) !important;
        color: var(--text-main) !important;
    }

    .section-title { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main); }
    .stats-icon { width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }

    @media(max-width:991px){ .main-content { margin-left: 0 !important; padding: 1rem; } }
</style>
</head>
<body>

    <?php include('../includes/admin_sidebar.php'); ?>

    <main class="main-content">
        <!-- Header -->
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">Generate Reports</h2>
                <p class="text-muted mb-0">Filter and export sit-in activity logs</p>
            </div>
            <div class="date-badge shadow-sm">
                <i class="bi bi-calendar-event me-2 text-primary"></i>
                <?php echo date("F d, Y"); ?>
            </div>
        </div>

        <!-- Filters -->
        <div class="custom-card mb-4">
            <div class="section-title"><i class="bi bi-funnel-fill me-2 text-primary"></i>Report Filters</div>
            <form action="" method="GET">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-muted small fw-bold">Start Date</label>
                        <input type="date" class="form-control custom-input" name="start_date">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-muted small fw-bold">End Date</label>
                        <input type="date" class="form-control custom-input" name="end_date">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-muted small fw-bold">Laboratory</label>
                        <select class="form-select custom-input" name="lab_name">
                            <option value="All">All Laboratories</option>
                            <option value="Lab 544">Lab 544</option>
                            <option value="Lab 542">Lab 542</option>
                            <option value="Lab 526">Lab 526</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-muted small fw-bold">Status</label>
                        <select class="form-select custom-input" name="sit_in_status">
                            <option value="All">All Status</option>
                            <option value="Completed">Completed</option>
                            <option value="Active">Active</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 fw-bold" name="generate_report">Generate Report</button>
                    <button type="reset" class="btn btn-outline-secondary px-4">Reset</button>
                </div>
            </form>
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="stats-card">
                    <div class="stats-icon bg-primary-subtle text-primary"><i class="bi bi-clipboard-data"></i></div>
                    <div><h3 class="fw-bold mb-0 text-main"><?= $total_Sessions ?></h3><small class="text-muted">Total Sit-ins</small></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stats-card">
                    <div class="stats-icon bg-success-subtle text-success"><i class="bi bi-person-check-fill"></i></div>
                    <div><h3 class="fw-bold mb-0 text-main"><?= $active_sessions ?></h3><small class="text-muted">Active Sessions</small></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stats-card">
                    <div class="stats-icon bg-danger-subtle text-danger"><i class="bi bi-people"></i></div>
                    <div><h3 class="fw-bold mb-0 text-main"><?= $user_registered ?></h3><small class="text-muted">Registered Users</small></div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="custom-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div class="section-title mb-0"><i class="bi bi-table me-2 text-primary"></i>Sit-in Logs</div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-danger px-3" onclick="exportToPDF()"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</button>
                    <button class="btn btn-sm btn-outline-success px-3" onclick="exportToExcel()"><i class="bi bi-file-earmark-excel me-1"></i> Excel</button>
                </div>
            </div>
            
            <div class="table-responsive mt-4">
                <table id="Generated_report" class="table custom-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Laboratory</th>
                            <th>Login</th>
                            <th>Logout</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result instanceof mysqli_result && mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['student_id_str']); ?></td>
                                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($row['lab']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($row['login_time'])); ?></td>
                                <td>
                                    <?php 
                                        echo $row['logout_time'] 
                                            ? date('M d, Y h:i A', strtotime($row['logout_time'])) 
                                            : '<span class="badge bg-warning text-dark">Still In</span>'; 
                                    ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $row['status'] == 'Completed' ? 'bg-success' : 'bg-primary'; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                            <tr> 
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    <?php echo isset($_GET['generate_report']) ? "No records found for the selected filters." : "Select filters and click Generate Report."; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

    <script>
        // Apply theme immediately on load to prevent flash
        (function() {
            const savedTheme = localStorage.getItem("theme");
            if (savedTheme === "dark") {
                document.body.setAttribute("data-theme", "dark");
            }
        })();

        function exportToExcel() {
            const table = document.getElementById("Generated_report");
            const wb = XLSX.utils.table_to_book(table, { sheet: "Sit-in Report" });
            XLSX.writeFile(wb, "sit_in_Report.xlsx");
        }

        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const pageWidth = doc.internal.pageSize.getWidth();
            const img = new Image();
            img.src = '../assets/ccsmainlogo2.png'; 

            img.onload = function() {
                doc.addImage(img, 'PNG', (pageWidth - 20) / 2, 10, 20, 20); 
                doc.setFont("helvetica", "bold");
                doc.setFontSize(16);
                doc.text("UC MAIN CCS LAB", pageWidth / 2, 38, { align: "center" });
                doc.setFontSize(10);
                doc.setFont("helvetica", "normal");
                doc.text("Sanciangko St., Cebu City, Philippines", pageWidth / 2, 44, { align: "center" });
                doc.setFontSize(12);
                doc.setFont("helvetica", "bold");
                doc.text("SIT-IN SYSTEM GENERATED REPORT", pageWidth / 2, 52, { align: "center" });
                doc.line(15, 55, pageWidth - 15, 55);

                doc.autoTable({ 
                    html: '#Generated_report',
                    startY: 60,
                    theme: 'grid',
                    headStyles: { fillColor: [1, 33, 105] },
                    styles: { fontSize: 9, cellPadding: 3 },
                    margin: { top: 30 } 
                });
                doc.save("UC_Main_CCS_LAB_Report.pdf");
            };
        }
    </script>
</body>
</html>