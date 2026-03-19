<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// databse connection
require __DIR__ . "/../config/database.php";

// admin credentials (hard coded)
$adminID = "a-01";
$adminPass = "admin123";

if (isset($_POST['user_login'])) {
    // if admin login check credentials
    if ($_POST['user_id'] === $adminID && $_POST['user_password'] === $adminPass) {
        $_SESSION['user_id'] = $_POST['user_id'];
        header("Location:  ../admin_module/adminDashboard.php");
        die(); // exit
    }

    // user login check credentials
    $userId = trim($_POST['user_id']);
    $userPassword = trim($_POST['user_password']);

    $sql = "SELECT student_id,password FROM students WHERE student_id = ? LIMIT 1";
    $getData = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($getData, 's', $userId);
    // execute query
    mysqli_stmt_execute($getData);
    // get result from the query
    $result = mysqli_stmt_get_result($getData);
    // fetch student data from assoc array
    $row = mysqli_fetch_assoc($result);
    // if student data in db and compare password inputted to the hashed password from the db
    if ($row && password_verify($userPassword,$row['password'])) {
        $_SESSION['user_id'] = $row['student_id'];
        header("Location: ../student_module/studentDashboard.php");
        exit();
    } else {
        header("Location: ../login.php?error=1"); // temp message
    }
    
}