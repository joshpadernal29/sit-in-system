<?php
include("../config/database.php");

// Fetch Pending Reservations
function getPendingReservations($conn) {
    $sql = "SELECT rs.pc_number,
            rs.id,
            rs.lab_name,
            rs.schedule_date,
            rs.schedule_time,
            rs.purpose,
            rs.status,
            CONCAT(st.firstname, ' ' ,st.lastname) AS fullname
            FROM reservations rs
            LEFT JOIN students st ON rs.student_pk_id = st.id
            WHERE rs.status = 'pending'
            ORDER BY rs.created_at DESC";

    $getData = mysqli_prepare($conn,$sql);
    if($getData) {
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        mysqli_stmt_close($getData);
        return $result;
    }
    return false;
}

// Fetch Approved Reservations
function getApprovedReservations($conn) {
    $sql = "SELECT rs.pc_number,
            rs.id,
            rs.lab_name,
            rs.schedule_date,
            rs.schedule_time,
            rs.purpose,
            rs.status,
            rs.action,
            CONCAT(st.firstname, ' ' ,st.lastname) AS fullname
            FROM reservations rs
            LEFT JOIN students st ON rs.student_pk_id = st.id
            WHERE rs.action = 'approved'
            ORDER BY rs.created_at DESC";
    
    $getData = mysqli_prepare($conn,$sql);
    if($getData) {
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        mysqli_stmt_close($getData);
        return $result;
    }
    return false;
}

// Fetch System Logs
function getSystemLogs($conn) {
    $sql = "SELECT rs.pc_number,
            rs.id,
            rs.lab_name,
            rs.schedule_date,
            rs.schedule_time,
            rs.purpose,
            rs.status,
            rs.action,
            CONCAT(st.firstname, ' ' ,st.lastname) AS fullname
            FROM reservations rs
            LEFT JOIN students st ON rs.student_pk_id = st.id
            WHERE rs.action IN ('approved','rejected')
            ORDER BY rs.created_at DESC";
    
    $getData = mysqli_prepare($conn,$sql);
    if($getData) {
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        mysqli_stmt_close($getData);
        return $result;
    }
    return false;
}

// Handle Reservation Action (Approve/Reject)
// capture the action from the url querystring

// for approve request
if (isset($_GET['action']) && $_GET['action'] == 'approve') {
    // get id from the url
    $id = (int)$_GET['id']; // parse to int to match db type
    $sql = "UPDATE reservations SET status = 'approved', action = 'approved' WHERE id = $id";
    mysqli_query($conn,$sql);
    header('Location: ../admin_module/admin_reservation.php?response=Approved');
    exit();
}
// for reject request
if (isset($_GET['action']) && $_GET['action'] == 'reject') {
    $id = (int)$_GET['id'];
    $sql = "UPDATE reservations SET status = 'rejected', action = 'rejected' WHERE id = $id";
    mysqli_query($conn,$sql);
    header('Location: ../admin_module/admin_reservation.php?response=Rejected');
    exit();
}


// admin pc control (admin can: set pc to available,unavailable,maintenance)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lab    = $_POST['lab_name'] ?? '';
    $pc     = $_POST['pc_number'] ?? '';
    $status = $_POST['status'] ?? ''; 

    if (empty($lab) || empty($pc) || empty($status)) {
        echo json_encode(["success" => false, "error" => "Missing data"]);
        exit;
    }

    if ($status === 'available') {
        // Remove the record to make the PC green
        $sql = "DELETE FROM pc_status WHERE lab_name = ? AND pc_number = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $lab, $pc);
    } else {
        // Handles 'unavailable' (Yellow) and 'maintenance' (Red)
        $sql = "INSERT INTO pc_status (lab_name, pc_number, status) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE status = VALUES(status)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $lab, $pc, $status);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
}