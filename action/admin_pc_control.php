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
            CONCAT(st.firstname, ' ' ,st.lastname) AS fullname
            FROM reservations rs
            LEFT JOIN students st ON rs.student_pk_id = st.id
            WHERE rs.status = 'approved'
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
            CONCAT(st.firstname, ' ' ,st.lastname) AS fullname
            FROM reservations rs
            LEFT JOIN students st ON rs.student_pk_id = st.id
            WHERE rs.status IN ('approved','rejected')
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
