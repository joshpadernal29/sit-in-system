<?php
include("../config/database.php");

// 1. STOP THE HANG: Release the session lock immediately
session_start();
session_write_close(); 

header('Content-Type: application/json');

// 2. Get the lab (e.g., "544") directly from the request
$lab = $_GET['lab'] ?? '544';

// 3. Match keys exactly to your "544" style input
$capacities = [
    '544' => 40,
    '542' => 30,
    '526' => 35
];

$total = $capacities[$lab] ?? 40;

/**
 * Helper function to fetch PC numbers for a specific status
 */
function fetchPcs($conn, $table, $lab, $status) {
    $sql = "SELECT pc_number FROM $table WHERE lab_name = ? AND status = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $lab, $status);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $pcs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pcs[] = (int)$row['pc_number'];
    }
    mysqli_stmt_close($stmt);
    return $pcs;
}

// 4. Gather data using the exact "544" string
$reserved    = fetchPcs($conn, 'reservations', $lab, 'approved');
$pending     = fetchPcs($conn, 'reservations', $lab, 'pending');
$maintenance = fetchPcs($conn, 'pc_status',    $lab, 'unavailable');

// 5. Return clean JSON
echo json_encode([
    "total"       => $total,
    "reserved"    => $reserved,
    "pending"     => $pending,
    "maintenance" => $maintenance
]);
