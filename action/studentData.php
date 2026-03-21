<?php
include("../config/database.php");

// // get student id from the query string
// $studentId = htmlspecialchars($_GET['student_id']);   

// $sql = "SELECT * FROM students WHERE student_id = ? LIMIT 1";
// // prepare query
// $getData = mysqli_prepare($conn,$sql);
// // bind param
// mysqli_stmt_bind_param($getData, 's', $studentId);
// // execute sql
// mysqli_stmt_execute($getData);
// // get result
// $result = mysqli_stmt_get_result($getData);
// // fetch student data to assoc array
// $student = mysqli_fetch_assoc($result);

// 1. Check for 'user_id' since that's what your login saves
$student_id = $_SESSION['user_id'] ?? null; 

// 2. If it's missing, don't let the script continue
if (!$student_id) {
    // Optional: redirect to login if session is lost
    header("Location: ../login.php");
    exit();
}

// 3. Your query remains the same
$sql = "SELECT * FROM students WHERE student_id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);



// close db connection
mysqli_close($conn);