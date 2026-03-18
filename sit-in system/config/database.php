<?php
// database connection

$servername = "localhost";
$username = "root";
$password = "";
$db_name = "sit_in_monitoring";

// create connection 
$conn = mysqli_connect($servername,$username,$password,$db_name);

if (!$conn) {
    die("connection failed: " . mysqli_connect_error());
} else {
    echo "db connected!";
}