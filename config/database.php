<?php
// database connection

$servername = "localhost";
$username = "root";
$password = "";
$db_name = "sit_in_monitoring";
$portNo = 3307;

// create connection 
$conn = mysqli_connect($servername,$username,$password,$db_name,$portNo);

if (!$conn) {
    die("connection failed: " . mysqli_connect_error());
} else {
    // echo "db connected!"; test connection
}

?>