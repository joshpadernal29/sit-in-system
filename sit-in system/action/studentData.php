<?php
include("../config/database.php");

// get student id from the query string
$studentId = htmlspecialchars($_GET['student_id']);   

$sql = "SELECT * FROM students WHERE student_id = ? LIMIT 1";
// prepare query
$getData = mysqli_prepare($conn,$sql);
// bind param
mysqli_stmt_bind_param($getData, 's', $studentId);
// execute sql
mysqli_stmt_execute($getData);
// get result
$result = mysqli_stmt_get_result($getData);
// fetch student data to assoc array
$student = mysqli_fetch_assoc($result);



// close db connection
mysqli_close($conn);