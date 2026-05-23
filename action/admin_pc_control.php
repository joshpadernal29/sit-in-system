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
// for approve request
if (isset($_POST['approve'])) {
    $student_pk = (int)$_POST['request_id']; // student pk
    $sql = "UPDATE reservations SET status = 'approved', action = 'approved' WHERE id = $student_pk";
    mysqli_query($conn,$sql);
    header('Location: ../admin_module/admin_reservation.php?response=Approved');
    exit();
}
// for reject request
if (isset($_POST['reject'])) {
    $student_pk = (int)$_POST['request_id']; // student pk
    $sql = "UPDATE reservations SET status = 'rejected', action = 'rejected' WHERE id = $student_pk";
    mysqli_query($conn,$sql);
    header('Location: ../admin_module/admin_reservation.php?response=Rejected');
    exit();
}


// Asynchronous Admin Management Routing Block
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. ROUTE INTERCEPT: Handle Application Entry Deletions
    if (isset($_POST['action']) && $_POST['action'] === 'delete_software') {
        $software_id = (int)($_POST['software_id'] ?? 0);

        if ($software_id <= 0) {
            echo json_encode(["success" => false, "error" => "Invalid software ID configuration."]);
            exit;
        }

        // REPLACE 'softwares' with your exact table name
        // REPLACE 'id' with your primary key column name if it's different (e.g. software_id)
        $sql = "DELETE FROM software_applications  WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $software_id);
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to prepare query statement."]);
        }
        exit;
    }

    // 2. DEFAULT ROUTE: Admin physical PC status state tracking
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
?>