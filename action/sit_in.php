<?php
require_once __DIR__ . '/../config/database.php';

// --- FUNCTIONS FOR THE LIST PAGE ---
function current_sit_in($conn) {
    // Fetches currently active sessions for the admin list
    $sql = "SELECT * FROM sit_in_records WHERE status = 'Active' ORDER BY login_time DESC";
    return mysqli_query($conn, $sql);
}

function past_sit_in($conn, $date = null, $limit = null, $offset = null) {
    $sql = "SELECT sr.*, CONCAT(s.firstname,' ',s.lastname) AS fullname, fb.message AS feedback_message 
            FROM sit_in_records sr
            JOIN students s ON sr.student_pk_id = s.id
            LEFT JOIN feedbacks fb ON sr.id = fb.record_id";
    
    if ($date) {
        $safe_date = mysqli_real_escape_string($conn, $date);
        $sql .= " WHERE DATE(sr.login_time) = '$safe_date'";
    }
    
    $sql .= " ORDER BY sr.login_time DESC";
    
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT $limit OFFSET $offset";
    }
    return mysqli_query($conn, $sql);
}

// INTEGRATED SEARCH: Detects if the student has an approved OR already active reservation for today
function searchStudentById($conn, $student_id) {
    // MODIFIED: Added 'active' to status check so search results show info if they are already sitting in
    $sql = "SELECT s.*, r.id AS res_id, r.pc_number, r.lab_name AS res_lab, r.status AS res_status
            FROM students s 
            LEFT JOIN reservations r ON s.id = r.student_pk_id 
                AND r.status IN ('approved', 'active') 
                AND r.schedule_date = CURDATE()
            WHERE s.student_id = ? LIMIT 1";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// --- CONFIRM SIT-IN (Deduct, Log, and Activate Reservation) ---
if (isset($_POST['update_sitin_session'])) {
    $pk_id = $_POST['id'];
    $res_id = $_POST['res_id']; 
    $student_id_str = $_POST['student_id_str'];
    $firstname = $_POST['firstname']; 
    $lastname = $_POST['lastname'];
    $fullname = $firstname . " " . $lastname;
    $lab = $_POST['lab'];
    //$pc_number = $_POST['pc_number']; // Ensure this is passed from the search result hidden input
    $language = $_POST['language'];
    $current_sessions = (int)$_POST['sit_ins'];

    // 1. Deduct 1 session from the students table
    $new_count = $current_sessions - 1;
    $upd = mysqli_prepare($conn, "UPDATE students SET sit_ins = ? WHERE id = ?");
    mysqli_stmt_bind_param($upd, "ii", $new_count, $pk_id);
    mysqli_stmt_execute($upd);

    // 2. INTEGRATION: If a reservation exists, move it to 'active'
    // This keeps the PC RED on both admin and student dashboards
    if (!empty($res_id)) {
        $updRes = mysqli_prepare($conn, "UPDATE reservations SET status = 'active' WHERE id = ?");
        mysqli_stmt_bind_param($updRes, "i", $res_id);
        mysqli_stmt_execute($updRes);
    }

    // 3. Insert into sit_in_records (Including pc_number for tracking)
    $ins = mysqli_prepare($conn, "INSERT INTO sit_in_records (student_pk_id, student_id_str, fullname, lab, language, status) VALUES (?, ?, ?, ?, ?, 'Active')");
    mysqli_stmt_bind_param($ins, "issss", $pk_id, $student_id_str, $fullname, $lab,$language);
    
    if (mysqli_stmt_execute($ins)) {
        header("Location: ../admin_module/sit_in_list.php?session=started");
        exit();
    } else {
        die("Query Failed: " . mysqli_error($conn));
    }
}

// --- LOGOUT: Close Session and Release PC ---
if (isset($_POST['logout_student'])) {
    $record_id = $_POST['record_id'];
    $student_pk = $_POST['student_pk_id']; 

    // 1. Close Sit-in Record
    $sql = "UPDATE sit_in_records SET status = 'Completed', logout_time = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $record_id);
    mysqli_stmt_execute($stmt);
    
    // 2. RELEASE RESERVATION: Change status to 'completed' so PC turns Green again
    $sqlRes = "UPDATE reservations SET status = 'completed' WHERE student_pk_id = ? AND status = 'active'";
    $stmtRes = mysqli_prepare($conn, $sqlRes);
    mysqli_stmt_bind_param($stmtRes, "i", $student_pk);
    mysqli_stmt_execute($stmtRes);
    
    if (mysqli_stmt_execute($stmtRes)) {
        header("Location: ../admin_module/sit_in_list.php?session=stopped");
        exit();
    } else {
        die("Critical Error releasing PC: " . mysqli_error($conn));
    }
}

function getFeedbacks($conn) {
    $sql = "SELECT fb.message, fb.category, s.student_id, CONCAT(s.firstname, ' ', s.lastname) AS fullname, sr.lab, fb.submitted_at 
            FROM feedbacks fb
            JOIN students s ON fb.student_id = s.id
            JOIN sit_in_records sr ON fb.record_id = sr.id
            ORDER BY fb.submitted_at DESC";
    return mysqli_query($conn, $sql);
}