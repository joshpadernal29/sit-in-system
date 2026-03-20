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

