<?php
include("../config/database.php");
header('Content-Type: application/json');

// Get and sanitize lab name. If numeric (e.g. 544), append 'LAB-' to match your array and DB naming convention.
$labInput = $_GET['lab'] ?? '544';
$lab = strpos($labInput, 'LAB-') === 0 ? $labInput : 'LAB-' . ltrim($labInput, 'LAB-');

// Map capacity for laboratories
$capacities = [
    'LAB-544' => 40,
    'LAB-542' => 30,
    'LAB-526' => 35
];

$total = $capacities[$lab] ?? 40;

// 1. Get ONLY the Approved/Occupied PCs (Turns them Red)
$sqlReserved = "SELECT pc_number FROM reservations WHERE lab_name = ? AND status = 'approved'";
$stmtReserved = mysqli_prepare($conn, $sqlReserved);
mysqli_stmt_bind_param($stmtReserved, "s", $lab);
mysqli_stmt_execute($stmtReserved);
$resResult = mysqli_stmt_get_result($stmtReserved);

$reserved = [];
while ($row = mysqli_fetch_assoc($resResult)) {
    $reserved[] = (int)$row['pc_number'];
}
mysqli_stmt_close($stmtReserved);

// 2. Get Pending PCs (Turns them Yellow)
$sqlPending = "SELECT pc_number FROM reservations WHERE lab_name = ? AND status = 'pending'";
$stmtPending = mysqli_prepare($conn, $sqlPending);
mysqli_stmt_bind_param($stmtPending, "s", $lab);
mysqli_stmt_execute($stmtPending);
$pendResult = mysqli_stmt_get_result($stmtPending);

$pending = [];
while ($row = mysqli_fetch_assoc($pendResult)) {
    $pending[] = (int)$row['pc_number'];
}
mysqli_stmt_close($stmtPending);

// 3. Get Maintenance/Broken PCs from your status tracking (Turns them Orange)
$sqlMaint = "SELECT pc_number FROM pc_status WHERE lab_name = ? AND status = 'unavailable'";
$stmtMaint = mysqli_prepare($conn, $sqlMaint);
mysqli_stmt_bind_param($stmtMaint, "s", $lab);
mysqli_stmt_execute($stmtMaint);
$maintResult = mysqli_stmt_get_result($stmtMaint);

$maintenance = [];
while ($row = mysqli_fetch_assoc($maintResult)) {
    $maintenance[] = (int)$row['pc_number'];
}
mysqli_stmt_close($stmtMaint);

// Return JSON output
echo json_encode([
    "total" => $total,
    "reserved" => $reserved,
    "pending" => $pending,
    "maintenance" => $maintenance
]);
?>