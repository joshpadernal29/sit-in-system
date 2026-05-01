<?php
include("../config/database.php");
header('Content-Type: application/json');

$lab = $_GET['lab'] ?? 'LAB-544';

// Map capacity for laboratories dynamically or read from a table 
$capacities = [
    'LAB-544' => 40,
    'LAB-542' => 30,
    'LAB-526' => 35
];

$total = $capacities[$lab] ?? 40;

$sql = "SELECT pc_number FROM reservations WHERE lab_name = ? AND status IN ('pending', 'approved')";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $lab);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$reserved = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reserved[] = (int)$row['pc_number'];
}

echo json_encode([
    "total" => $total,
    "reserved" => $reserved,
    "warning" => []
]);
?>