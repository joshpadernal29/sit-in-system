<?php
require __DIR__. '/../config/database.php';

// read students data from the db
function getStudents($conn) {
    $sql = "SELECT * FROM students";
    $getData = mysqli_prepare($conn,$sql);
    
    // initialized empty array
    $students = [];

    if ($getData) {
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        // loop trough the table
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row; // add each student to the array
        }
        mysqli_stmt_close($getData);
    }
    return $students;
}