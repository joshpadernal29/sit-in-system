<?php
require_once __DIR__ . '/../config/database.php';

// --- FUNCTIONS FOR THE LIST PAGE ---
function current_sit_in($conn) {
    // This query looks specifically for 'Active' status
    $sql = "SELECT * FROM sit_in_records WHERE status = 'Active' ORDER BY login_time DESC";
    return mysqli_query($conn, $sql);
}

// --- UPDATED HISTORY FUNCTION ---
function past_sit_in($conn, $filter_date = null) {
    $sql = "SELECT * FROM sit_in_records WHERE status = 'Completed'";
    
    // If a date is provided (YYYY-MM-DD), filter by the login_time date
    if ($filter_date) {
        $sql .= " AND DATE(login_time) = '" . mysqli_real_escape_numeric($conn, $filter_date) . "'";
    }
    
    $sql .= " ORDER BY logout_time DESC LIMIT 100";
    return mysqli_query($conn, $sql);
}

// Helper to prevent SQL injection for the date string
function mysqli_real_escape_numeric($conn, $value) {
    return mysqli_real_escape_string($conn, $value);
}

function searchStudentById($conn, $student_id) {
    $sql = "SELECT * FROM students WHERE student_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// --- CONFIRM SIT-IN (Deduct & Log) ---
if (isset($_POST['update_sitin_session'])) {
    $pk_id = $_POST['id'];
    $student_id_str = $_POST['student_id_str'];
    $fullname = $_POST['firstname'] . " " . $_POST['lastname'];
    $lab = $_POST['lab'];
    $language = $_POST['language'];
    $current_sessions = (int)$_POST['sit_ins'];

    //  Deduct 1 session from the students table
    $new_count = $current_sessions - 1;
    $upd = mysqli_prepare($conn, "UPDATE students SET sit_ins = ? WHERE id = ?");
    mysqli_stmt_bind_param($upd, "ii", $new_count, $pk_id);
    mysqli_stmt_execute($upd);

    $ins = mysqli_prepare($conn, "INSERT INTO sit_in_records (student_pk_id, student_id_str, fullname, lab, language, status) VALUES (?, ?, ?, ?, ?, 'Active')");
    mysqli_stmt_bind_param($ins, "issss", $pk_id, $student_id_str, $fullname, $lab, $language);
    
    if (mysqli_stmt_execute($ins)) {
        header("Location: ../admin_module/sit_in_list.php?session=started");
        exit();
    } else {
        die("Query Failed: " . mysqli_error($conn));
    }
}

// --- LOGOUT from sit-in session ---
if (isset($_POST['logout_student'])) {
    $record_id = $_POST['record_id'];
    $sql = "UPDATE sit_in_records SET status = 'Completed', logout_time = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $record_id);
    mysqli_stmt_execute($stmt);
    
    header("Location: ../admin_module/sit_in_list.php?session=stopped");
    exit();
}