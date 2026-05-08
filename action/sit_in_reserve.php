<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/database.php');

if (isset($_POST['reserve_pc'])) {
    $student_pk = $_POST['student_pk_id'];
    
    // check student request before procceding to put another request for reservation
    if (checkRequest($conn, $student_pk)) {
        // throw message 'you curenlty have a pending / approved request!'
        header("Location: ../student_module/student_reservation.php?id=$student_pk,request=blocked");
        exit();
    } else {
        // get reservation data
        $pc_no = $_POST['pc_number'];
        $labName = $_POST['lab_name'];
        $reserveDate = $_POST['res_date'];
        $reserveTime = $_POST['res_time'];
        $purpose = $_POST['sit_in_purpose'];
        $language = $_POST['language']; // added language from modal

        // logic to check if student has sessions left before inserting
        $checkSession = mysqli_prepare($conn, "SELECT sit_ins FROM students WHERE id = ?");
        mysqli_stmt_bind_param($checkSession, "i", $student_pk);
        mysqli_stmt_execute($checkSession);
        $resObj = mysqli_fetch_assoc(mysqli_stmt_get_result($checkSession));

        if ($resObj['sit_ins'] <= 0) {
            header("Location: ../student_module/student_reservation.php?id=$student_pk,request=no_balance");
            exit();
        }

        // insert the reservation data with language and default pending status
        $sql = "INSERT INTO reservations (student_pk_id, pc_number, lab_name, schedule_date, schedule_time, purpose, language, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $getData = mysqli_prepare($conn, $sql);
        if ($getData) {
            mysqli_stmt_bind_param($getData, 'iisssss', $student_pk, $pc_no, $labName, $reserveDate, $reserveTime, $purpose, $language);
            mysqli_stmt_execute($getData); // insert the reservation data
            mysqli_stmt_close($getData);
        }
        
        header("Location: ../student_module/student_reservation.php?id=$student_pk,request=sent");
        exit();
    }
}

// function to check if student has currently have pending or approved request (block reservation if have pending or approved)
// can only request reservation one at a time to prevent spamming
function checkRequest($conn, $student_pk) {
    // stop request if the schedule date and time does not match the request's reseved date and time
    // modified to only target 'approved' and 'pending' - this prevents 'active' sessions from being rejected
    $cleanReservation = "UPDATE reservations 
                        SET status = 'rejected', 
                            action = 'rejected' 
                        WHERE status IN ('approved', 'pending') 
                        AND action IN ('approved', 'pending') 
                        AND (schedule_date < CURDATE() OR (schedule_date = CURDATE() AND schedule_time < CURTIME()))";
    mysqli_query($conn, $cleanReservation);

    $sql = "SELECT id FROM reservations
            WHERE student_pk_id = ?
            AND status IN ('approved', 'pending', 'active')
            LIMIT 1";
    
    $getData = mysqli_prepare($conn, $sql);
    if ($getData) {
        mysqli_stmt_bind_param($getData, 'i', $student_pk);
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        mysqli_stmt_close($getData);

        if (mysqli_num_rows($result) > 0) {
            return true;
        }

        return false;
    }    
}