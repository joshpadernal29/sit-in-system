<?php
require __DIR__. '/../config/database.php';
// this file contains functions for number of data's for the admin dashboard

// get number of students in the database
function countStudents($conn) {
    $sql = "SELECT COUNT(id) AS registered FROM students";
    $getNo = mysqli_prepare($conn,$sql);

    if ($getNo) {
        mysqli_stmt_execute($getNo);
        $result = mysqli_stmt_get_result($getNo);
        $student_no = mysqli_fetch_assoc($result);
        return $student_no['registered'];
    }
    return 0;
}

// get current sit-ins
function currentSitIns($conn) {
    $status = 'Active';
    $sql = "SELECT COUNT(*) AS current_sit_in FROM sit_in_records WHERE status = ?";
    $getData = mysqli_prepare($conn,$sql);
    
    if ($getData) {
        mysqli_stmt_bind_param($getData, 's', $status);
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        $row = mysqli_fetch_assoc($result);
        $current_sit_ins = $row['current_sit_in']; // asign the single value to the current_sit_in variable 
        mysqli_stmt_close($getData);

        return $current_sit_ins;
    }

    return 0;
}

// get total sessions (sit in)
function getTotalSessions($conn) {
    $status = 'Completed';
    $sql = "SELECT COUNT(*) AS total_sessions FROM sit_in_records WHERE status = ?";
    $getData = mysqli_prepare($conn,$sql);

    if ($getData) {
        mysqli_stmt_bind_param($getData, 's', $status);
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        $row = mysqli_fetch_assoc($result);
        $total_sessions = $row['total_sessions'];// asign the single value to the total_sessions variable 
        mysqli_close($conn);

        return $total_sessions;
    }

    return 0;
}

// get programming language preferences/used
function languageUsed($conn) {
    $sql = "SELECT";
}

