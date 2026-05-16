<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include("../config/database.php");

// Fetch real count from the database table for the statistics badge
$total_apps = 0;
$count_query = "SELECT COUNT(*) as total FROM software_applications";
if ($result = $conn->query($count_query)) {
    $row = $result->fetch_assoc();
    $total_apps = $row['total'];
}

// Fetch the 5 most recently imported items to show in the live table preview
$recent_apps = [];
$preview_query = "SELECT * FROM software_applications ORDER BY id DESC LIMIT 5";
if ($result = $conn->query($preview_query)) {
    $recent_apps = $result->fetch_all(MYSQLI_ASSOC);
}

$target_labs = ['544', '542', '526'];
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Import Software Assets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .main-content-wrapper { margin-left: 260px; transition: margin-left .3s ease; }
        .sidebar.collapsed ~ .main-content-wrapper { margin-left: 80px; }
        
        /* Drag and Drop Zone styling adjustments */
        .dropzone-area {
            border: 2px dashed var(--bs-border-color);
            border-radius: 16px;
            transition: all 0.2s ease-in-out;
            background: var(--bs-body-tertiary);
            cursor: pointer;
        }
        .dropzone-area.dragover, .dropzone-area:hover {
            border-color: var(--bs-primary) !important;
            background: var(--bs-secondary-bg-subtle);
        }
        @media (max-width: 991px) { .main-content-wrapper { margin-left: 0 !important; } }
    </style>
</head>
<body class="bg-body-tertiary text-body">

<?php include("../includes/admin_sidebar.php"); ?>

<div class="main-content-wrapper">
    <div class="container-fluid px-4 py-4">
        
        <!-- Status Notification Toasts/Alerts -->
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <strong>Mass Import Successful!</strong> Data rows parsed and saved into laboratory nodes.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['status'] == 'empty'): ?>
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Import Warning:</strong> The uploaded file was empty or contained unparseable line data profiles.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['status'] == 'error'): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i> <strong>System Failure:</strong> Could not complete transaction loop operations. Please verify template column formatting.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-body p-4 border border-light-subtle shadow-sm rounded-3">
                <div>
                    <h3 class="fw-bold text-body mb-1 text-uppercase"><i class="bi bi-box-arrow-in-up-right me-2 text-primary"></i>Import Software Registry</h3>
                    <p class="text-secondary small mb-0">Batch upload software applications into global laboratory systems via standardized CSV templates.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="data:text/csv;charset=utf-8,Software Name,Developer,Version,Category,License Type%0AVisual Studio Code,Microsoft,1.87.2,Development,Open Source%0AAnalytics Suite,Google,4.5.0,Analytics,Proprietary Free" download="software_template.csv" class="btn btn-sm btn-outline-secondary rounded-2 px-3 fw-semibold">
                        <i class="bi bi-download me-1"></i> Download CSV Template
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Interaction Panel: Dropzone File Uploader -->
            <div class="col-xl-4">
                <div class="card bg-body border-light-subtle shadow-sm rounded-3 h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <h5 class="fw-bold text-body mb-3">Upload Channel</h5>
                        
                        <!-- Fixed Action Path targets process_software_import.php -->
                        <form action="../action/software_import_logic.php" method="POST" enctype="multipart/form-data" class="flex-grow-1 d-flex flex-column">
                            
                            <!-- Interactive Box Zone Frame -->
                            <div class="dropzone-area p-5 text-center my-auto d-flex flex-column align-items-center justify-content-center" id="dropZoneContainer">
                                <i class="bi bi-filetype-csv text-primary display-3 mb-3"></i>
                                <h6 class="fw-bold text-body" id="uploadStatusText">Drag & Drop CSV File</h6>
                                <p class="text-secondary small px-3 mb-3" id="uploadSubText">Or click to browse files on your computer.</p>
                                
                                <!-- Hidden form control payload tag -->
                                <input type="file" name="csv_file" id="csvFileInput" class="d-none" accept=".csv" required>
                                
                                <button type="button" id="browseBtn" class="btn btn-sm btn-primary px-4 rounded-pill fw-semibold shadow-sm">
                                    Browse Files
                                </button>
                            </div>

                            <div class="mt-4">
                                <label class="form-label small fw-bold text-secondary text-uppercase">Target Lab Deployment</label>
                                <select name="target_lab" class="form-select bg-body text-body border-secondary-subtle rounded-2 shadow-none mb-3">
                                    <option value="all">Deploy Globally (All Labs)</option>
                                    <?php foreach($target_labs as $lab): ?>
                                        <option value="<?= $lab; ?>">LAB <?= $lab; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" name="execute_import" class="btn btn-dark w-100 py-2.5 rounded-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                <i class="bi bi-cloud-arrow-up-fill"></i>Import
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Database Repository View Container -->
            <div class="col-xl-8">
                <div class="card bg-body border-light-subtle shadow-sm rounded-3 h-100">
                    <div class="card-header bg-body border-bottom border-light-subtle p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-body mb-0">System Deployment Sync Logs</h5>
                            <small class="text-secondary">Displaying the most recent application profile imports.</small>
                        </div>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">
                            <?= $total_apps ?> App Profiles Registered
                        </span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-body-tertiary">
                                <tr>
                                    <th class="text-secondary small text-uppercase py-3 ps-4 bg-body-tertiary">Software Handle</th>
                                    <th class="text-secondary small text-uppercase py-3 bg-body-tertiary">Developer</th>
                                    <th class="text-secondary small text-uppercase py-3 bg-body-tertiary">Version</th>
                                    <th class="text-secondary small text-uppercase py-3 bg-body-tertiary">Lab Assigned</th>
                                    <th class="text-secondary small text-uppercase py-3 pe-4 text-end bg-body-tertiary">License</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_apps)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-secondary">
                                            <i class="bi bi-folder-x display-4 text-muted opacity-50 mb-2"></i>
                                            <div>No software applications imported yet. Use the upload card channel.</div>
                                        </td>
                                    </tr>
                                <?php align: else: ?>
                                    <?php foreach($recent_apps as $app): ?>
                                        <tr>
                                            <td class="ps-4 border-light-subtle">
                                                <div class="fw-bold text-body"><?= htmlspecialchars($app['software_name']); ?></div>
                                                <small class="text-secondary text-uppercase" style="font-size:0.7rem;"><?= htmlspecialchars($app['category']); ?></small>
                                            </td>
                                            <td class="text-secondary border-light-subtle"><?= htmlspecialchars($app['developer']); ?></td>
                                            <td class="border-light-subtle"><span class="badge bg-body-tertiary text-body border border-light-subtle px-2 py-1">v<?= htmlspecialchars($app['version']); ?></span></td>
                                            <td class="border-light-subtle">
                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-1 px-2">
                                                    <?= $app['target_lab'] === 'all' ? 'GLOBAL' : 'LAB '.$app['target_lab']; ?>
                                                </span>
                                            </td>
                                            <td class="pe-4 text-end border-light-subtle">
                                                <span class="small fw-semibold"><?= htmlspecialchars($app['license_type']); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const dropZone = document.getElementById('dropZoneContainer');
    const fileInput = document.getElementById('csvFileInput');
    const browseBtn = document.getElementById('browseBtn');
    const statusText = document.getElementById('uploadStatusText');
    const subText = document.getElementById('uploadSubText');

    // Clicking anywhere on the box (or button) opens file picker
    dropZone.addEventListener('click', (e) => {
        fileInput.click();
    });

    // Handle normal file browse input change
    fileInput.addEventListener('change', (e) => {
        updateVisualState(e.target.files);
    });

    // Prevent default behaviors for drag events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
        }, false);
    });

    // Toggle highlight class states
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
    });

    // Capture dropped files and assign them to the form field array directly
    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;

        if(files.length > 0) {
            // Confirm it's actually a CSV file before setting input value
            if (files[0].name.toLowerCase().endsWith('.csv')) {
                fileInput.files = files; 
                updateVisualState(files);
            } else {
                alert("Invalid format! Please upload a structured .csv template file.");
            }
        }
    });

    // Sync visual UI elements
    function updateVisualState(files) {
        if(files.length > 0) {
            statusText.innerText = "File Attached Successfully";
            subText.innerText = files[0].name + " (" + Math.round(files[0].size/1024) + " KB)";
            subText.classList.remove('text-secondary');
            subText.classList.add('text-success', 'fw-bold');
        }
    }
</script>
</body>
</html>