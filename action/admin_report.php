<?php
include("../config/database.php");

// get all filter data
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$lab_name = $_GET['lab_name'] ?? 'All';
$status = $_GET['sit_in_status'] ?? 'All';
$result = false;


// generate report filter 
if (isset($_GET['generate_report'])) {
    $sql = "SELECT sr.student_id_str,
                   sr.fullname,
                   sr.lab,
                   sr.login_time,
                   sr.logout_time,
                   sr.status
            FROM sit_in_records AS sr 
            WHERE 1=1"; // always true

    $filters = [];
    $types = '';

    // date filter 
    if (!empty($start_date) && !empty($end_date)) {
        $sql .= " AND DATE(sr.login_time) BETWEEN ? AND ?";
        $filters[] = $start_date;
        $filters[] = $end_date;
        $types .= "ss";
    }

    // Lab Filter
    if ($lab_name !== 'All') {
        $sql .= " AND sr.lab = ?";
        $filters[] = $lab_name;
        $types .= "s";
    }

    // Status Filter
    if ($status !== 'All') {
        $sql .= " AND sr.status = ?";
        $filters[] = $status;
        $types .= "s";
    }

    $sql .= " ORDER BY sr.login_time DESC";

    // Prepare and Execute
    $stmt = mysqli_prepare($conn,$sql);
    if ($stmt) {
        if (!empty($filters)) {
            $stmt = mysqli_stmt_bind_param($stmt,$types,...$filters);
        }
    
        $stmt = mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        mysqli_stmt_close($stmt);
    }
    
}


