<?php
require_once __DIR__ . '/../config/database.php';

// --- FUNCTIONS FOR THE LIST PAGE ---
function current_sit_in($conn) {
    // This query look for 'Active' status
    $sql = "SELECT * FROM sit_in_records WHERE status = 'Active' ORDER BY login_time DESC";
    return mysqli_query($conn, $sql);
}

// past_sit_in records and and feedbacks
// JOIN students,sit_in_records, and feedbacks table
function past_sit_in($conn, $date = null, $limit = null, $offset = null) {
    $sql = "SELECT sr.*, CONCAT(s.firstname,' ',s.lastname) AS fullname, fb.message AS feedback_message 
            FROM sit_in_records sr
            JOIN students s ON sr.student_pk_id = s.id
            LEFT JOIN feedbacks fb ON sr.id = fb.record_id";
    
    // Filter (Optional)
    if ($date) {
        $safe_date = mysqli_real_escape_string($conn, $date);
        $sql .= " WHERE DATE(sr.login_time) = '$safe_date'";
    }
    
    // Sort and Paginate
    $sql .= " ORDER BY sr.login_time DESC";
    
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT $limit OFFSET $offset";
    }
    
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

// get all feedbacks (resturn result)
function getFeedbacks($conn) {
    $sql = "SELECT fb.message,fb.category,s.student_id, CONCAT(s.firstname, ' ', s.lastname) AS fullname, sr.lab,fb.submitted_at 
            FROM feedbacks fb
            JOIN students s ON fb.student_id = s.id
            JOIN sit_in_records sr ON fb.record_id = sr.id
            ORDER BY fb.submitted_at DESC";
    $getData = mysqli_prepare($conn,$sql);

    if ($getData) {
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
    }
    return $result;
}