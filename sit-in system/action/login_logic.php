<?php
// databse connection
require __DIR__ . "/../config/database.php";

// admin credentials (hard coded)
$adminID = "a-01";
$adminPass = "admin123";

if (isset($_POST['user_login'])) {
    // check admin credentials
    if ($_POST['user_id'] && $_POST['user_password']) {
        header("Location:  ../dashboard.php");
    }
}