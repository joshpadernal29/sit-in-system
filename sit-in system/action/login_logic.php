<?php
// databse connection
require __DIR__ . "/../config/database.php";

// admin credentials (hard coded)
$adminID = "a-01";
$adminPass = "admin123";

if (isset($_POST['user_login'])) {
    // if admin login check credentials
    if ($_POST['user_id'] === $adminID && $_POST['user_password'] === $adminPass) {
        header("Location:  ../admin_module/adminDashboard.php");
    }

    // db get data logic here...
    // user login check credentials
    // if ($_POST['user_id'] === )
}