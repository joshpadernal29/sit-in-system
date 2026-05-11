<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);

include("../action/admin_report.php"); // logic file for generate reports
include("../config/database.php");
include("../action/Data_count.php"); // get data for total sit_ins active sessions ....

// for data cards
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
        /* Base Body Style */
        body { margin: 0; background: #f5f7fb; font-family: 'Inter', -apple-system, sans-serif; }

        /* IMPORTANT: Keeping the layout CSS here ensures the main content shifts correctly 
           when the sidebar in the included file is toggled. */
        .main-content {
            margin-left: 260px;
            padding: 2rem;
            transition: margin-left .3s ease;
        }

        /* Shift logic for collapsed state */
        .sidebar.collapsed ~ .main-content { margin-left: 80px; }

        /* UI Component Styles */
        .page-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
        .date-badge { background: #fff; border: 1px solid #e9ecef; padding: .8rem 1rem; border-radius: 12px; font-weight: 600; }
        .custom-card { background: #fff; border-radius: 18px; padding: 1.5rem; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0,0,0,.03); }
        .section-title { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; }
        .custom-input { border-radius: 12px; padding: .75rem 1rem; border: 1px solid #dee2e6; }
        
        .stats-card {
            background: #fff; border-radius: 18px; padding: 1.3rem;
            border: 1px solid #e9ecef; display: flex; align-items: center; gap: 1rem; transition: .2s ease;
        }
        .stats-icon { width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        
        .custom-table thead th { background: #f8f9fc; border: none; padding: 1rem; font-size: 0.85rem; color: #6c757d; }
        .custom-table tbody td { padding: 1rem; border-top: 1px solid #f1f3f5; }

        @media(max-width:991px){ .main-content { margin-left: 0 !important; padding: 1rem; } }
    </style>
</head>
<body>

    <!-- Include the separate sidebar file -->
    <?php include('../includes/admin_sidebar.php'); ?>

    <main class="main-content">
        <!-- Header -->
        <div class="page-header">
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
                    <button type="reset" class="btn btn-light border px-4">Reset</button>
                </div>
            </form>
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="stats-card">
                    <div class="stats-icon bg-primary-subtle text-primary"><i class="bi bi-clipboard-data"></i></div>
                    <div><h3 class="fw-bold mb-0"><?= $total_Sessions ?></h3><small class="text-muted">Total Sit-ins</small></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stats-card">
                    <div class="stats-icon bg-success-subtle text-success"><i class="bi bi-person-check-fill"></i></div>
                    <div><h3 class="fw-bold mb-0"><?= $active_sessions ?></h3><small class="text-muted">Active Sessions</small></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stats-card">
                    <div class="stats-icon bg-danger-subtle text-danger"><i class="bi bi-people"></i></div>
                    <div><h3 class="fw-bold mb-0"><?= $user_registered ?></h3><small class="text-muted">Registered Users</small></div>
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
            <!--GENERATED REPORT-->
            <div class="table-responsive mt-4">
                <table id="Generated_report" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Labaratory</th>
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
                                <td><?php echo htmlspecialchars($row['student_id_str']); ?></td>
                                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($row['lab']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($row['login_time'])); ?></td>
                                <td>
                                    <?php 
                                        echo $row['logout_time'] 
                                            ? date('M d, Y h:i A', strtotime($row['logout_time'])) 
                                            : '<span class="badge bg-warning">Still In</span>'; 
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
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <?php if (isset($_GET['generate_report'])): ?>
                                        No records found for the selected filters.
                                    <?php else: ?>
                                        Select a date range and click "Generate Report" to view data.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!--FOR exporting to PDF/excel file (reports)-->
    <!-- For Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- For PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

    <script>
        // export to excel
        function exportToExcel() {
            // Select your table by ID
            const table = document.getElementById("Generated_report");
            
            // Convert table to a workbook
            const wb = XLSX.utils.table_to_book(table, { sheet: "Sit-in Report" });
            
            // Trigger the download
            XLSX.writeFile(wb, "sit_in_Report.xlsx");
        }

        // export to pdf
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // 1. Setup Dimensions
            const pageWidth = doc.internal.pageSize.getWidth();
            const img = new Image();
            img.src = '../assets/ccsmainlogo2.png'; // Make sure this path is correct

            img.onload = function() {
                // --- HEADER SECTION ---
                
                // 2. Center the Logo (assuming logo is 20mm wide)
                const logoWidth = 20;
                const logoX = (pageWidth - logoWidth) / 2;
                doc.addImage(img, 'PNG', logoX, 10, logoWidth, 20); 

                // 3. Center the Main Title
                doc.setFont("helvetica", "bold");
                doc.setFontSize(16);
                doc.text("UC MAIN CCS LAB", pageWidth / 2, 38, { align: "center" });

                // 4. Center the Report Type & Date
                doc.setFont("helvetica", "normal");
                doc.setFontSize(10);
                doc.text("Sanciangko St., Cebu City, Philippines", pageWidth / 2, 44, { align: "center" });
                
                doc.setFontSize(12);
                doc.setFont("helvetica", "bold");
                doc.text("SIT-IN SYSTEM GENERATED REPORT", pageWidth / 2, 52, { align: "center" });

                // 5. Add a separator line
                doc.setLineWidth(0.5);
                doc.line(15, 55, pageWidth - 15, 55);

                // --- TABLE SECTION ---
                
                doc.autoTable({ 
                    html: '#Generated_report',
                    startY: 60, // Start below the header info
                    theme: 'grid',
                    headStyles: { fillColor: [1, 33, 105] }, // UC Blue color (approx)
                    styles: { fontSize: 9, cellPadding: 3 },
                    // This ensures the table headers don't repeat over our custom logo on page 2
                    margin: { top: 30 } 
                });

                // 6. Save File
                doc.save("UC_Main_CCS_LAB_Report.pdf");
            };
        }


    </script>
</body>
</html>