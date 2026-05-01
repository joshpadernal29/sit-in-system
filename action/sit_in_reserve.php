<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/database.php');

if (isset($_POST['reserve_pc'])) {
    // get reservation data
    $student_pk = $_POST['student_pk_id'];
    $pc_no = $_POST['pc_number'];
    $labName = $_POST['lab_name'];
    $reserveDate = $_POST['res_date'];
    $reserveTime = $_POST['res_time'];
    $purpose = $_POST['sit_in_purpose'];

    $sql = "INSERT INTO reservations (student_pk_id,pc_number,lab_name,schedule_date,schedule_time,purpose)
            VALUES (?,?,?,?,?,?)";
    $getData = mysqli_prepare($conn,$sql);
    if ($getData) {
        mysqli_stmt_bind_param($getData,'iissss',$student_pk,$pc_no,$labName,$reserveDate,$reserveTime,$purpose);
        mysqli_stmt_execute($getData); // insert the reservation data
        mysqli_stmt_close($getData);
    }
    header("Location: ../admin_module/student_reservation.php?reservation=sent");
}