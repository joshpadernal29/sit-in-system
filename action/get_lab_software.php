<?php
header('Content-Type: application/json');
include("../config/database.php");

$lab = isset($_GET['lab']) ? mysqli_real_escape_string($conn, $_GET['lab']) : '';

if (empty($lab)) {
    echo json_encode([]);
    exit;
}

// Select apps matching either this specific lab context or globally flagged environments
$query = "SELECT id,software_name, developer, version, license_type 
          FROM software_applications 
          WHERE target_lab = '$lab' OR target_lab = 'all' 
          ORDER BY software_name ASC";

$result = mysqli_query($conn, $query);
$software = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $software[] = $row;
    }
}

echo json_encode($software);
exit;