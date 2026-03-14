<?php

<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "sit_in_db";

/* Create connection */
$conn = mysqli_connect($servername, $username, $password, $database);

/* Check connection */
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: set charset
mysqli_set_charset($conn, "utf8");

?>