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
        $_SESSION['user_password'] = $_POST['user_password'];
        header("Location:  ../admin_module/adminDashboard.php");
        die(); // exit
    }

    // db get data logic here...
    // user login check credentials
    // if ($_POST['user_id'] === )
}