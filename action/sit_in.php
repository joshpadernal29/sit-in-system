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
    
    $sql .= " ORDER BY sr.logout_time DESC";
    
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

// --- LOGOUT: Close Session, Run Weighted Grading Matrix, and Release PC ---
if (isset($_POST['logout_student'])) {
    $record_id = intval($_POST['record_id']);
    $student_pk = intval($_POST['student_pk_id']); 
    
    // Catch new grading inputs sent from the admin modal interface
    $task_status = (isset($_POST['task_status']) && $_POST['task_status'] === 'Completed') ? 'Completed' : 'Pending';
    $behavior_score = isset($_POST['behavior_score']) ? intval($_POST['behavior_score']) : 10; // Default max score baseline

    // 1. Fetch check-in time to calculate exact duration dynamically
    $time_sql = "SELECT login_time FROM sit_in_records WHERE id = ?";
    $time_stmt = mysqli_prepare($conn, $time_sql);
    mysqli_stmt_bind_param($time_stmt, "i", $record_id);
    mysqli_stmt_execute($time_stmt);
    $time_result = mysqli_stmt_get_result($time_stmt);
    $record = mysqli_fetch_assoc($time_result);
    
    // Establish time objects to compute numeric hour intervals
    $login_time = new DateTime($record['login_time']);
    $logout_time = new DateTime(); // Represents NOW() equivalent timestamp
    
    $interval = $login_time->diff($logout_time);
    $duration_hours = $interval->h + ($interval->i / 60) + ($interval->s / 3600);

    // ================= INSTRUCTOR PERFORMANCE FORMULA MATRIX =================
    // Component A: Behavior Score (60% weight) - Input scaled between 1 and 10
    $behavior_points = $behavior_score * 0.60;

    // Component B: Task Status Score (20% weight) - Binary evaluation check
    $task_points = ($task_status === 'Completed') ? (10 * 0.20) : 0.0;

    // Component C: Sit-In Hours Duration (20% weight)
    // Formula Normalization: Assumes a 3-hour lab block is standard for a full 10-point yield.
    $normalized_duration_score = ($duration_hours > 0) ? min(10, ($duration_hours / 3.0) * 10) : 0;
    $duration_points = $normalized_duration_score * 0.20;

    // Aggregate point values into a clean decimal total
    $session_points_earned = round(($behavior_points + $task_points + $duration_points), 2);

    // 2. CLOSE SIT-IN RECORD (Save metrics, timestamps, and active flags)
    $sql = "UPDATE sit_in_records SET 
            status = 'Completed', 
            logout_time = NOW(),
            task_status = ?,
            behavior_score = ?,
            points_earned_this_session = ?
            WHERE id = ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sidi", $task_status, $behavior_score, $session_points_earned, $record_id);
    mysqli_stmt_execute($stmt);
    
    // 3. CREDIT POINTS TO STUDENT PROFILE & COMPILE EXTRA SESSION REWARDS
    // REVISED: Fetching sit_ins and old sessions_earned to check for milestone crossovers
    $student_sql = "SELECT accumulated_points, sessions_earned, sit_ins FROM students WHERE id = ?";
    $student_stmt = mysqli_prepare($conn, $student_sql);
    mysqli_stmt_bind_param($student_stmt, "i", $student_pk);
    mysqli_stmt_execute($student_stmt);
    $student_result = mysqli_stmt_get_result($student_stmt);
    $student_data = mysqli_fetch_assoc($student_result);

    $old_sessions_earned = isset($student_data['sessions_earned']) ? intval($student_data['sessions_earned']) : 0;
    $current_sit_ins     = isset($student_data['sit_ins']) ? intval($student_data['sit_ins']) : 0;
    
    $new_accumulated_points = $student_data['accumulated_points'] + $session_points_earned;
    
    // Milestone Rule check: Every 50 accumulated points earns an addition to their session cap
    $new_sessions_earned = floor($new_accumulated_points / 50);

    // Calculate if they just unlocked a new milestone right now
    $unlocked_bonus_this_session = $new_sessions_earned - $old_sessions_earned;
    
    // If they hit a milestone, top-up their available sit_ins balance automatically!
    $new_sit_ins_balance = $current_sit_ins + $unlocked_bonus_this_session;

    // Save recalculated values back down to the target student profile row
    $update_student_sql = "UPDATE students SET 
                           accumulated_points = ?, 
                           sessions_earned = ?,
                           sit_ins = ?
                           WHERE id = ?";
    $update_student_stmt = mysqli_prepare($conn, $update_student_sql);
    mysqli_stmt_bind_param($update_student_stmt, "diii", $new_accumulated_points, $new_sessions_earned, $new_sit_ins_balance, $student_pk);
    mysqli_stmt_execute($update_student_stmt);

    // 4. RELEASE RESERVATION: Change status to 'completed' so PC turns Green again
    $sqlRes = "UPDATE reservations SET status = 'completed' WHERE student_pk_id = ? AND status = 'active'";
    $stmtRes = mysqli_prepare($conn, $sqlRes);
    mysqli_stmt_bind_param($stmtRes, "i", $student_pk);
    
    if (mysqli_stmt_execute($stmtRes)) {
        header("Location: ../admin_module/sit_in_list.php?session=stopped&points=" . $session_points_earned);
        exit();
    } else {
        die("Critical Error releasing PC: " . mysqli_error($conn));
    }
}

function getFeedbacks($conn, $category = '') {
    $sql = "SELECT fb.id, fb.message, fb.category, s.student_id, CONCAT(s.firstname, ' ', s.lastname) AS fullname, sr.lab, fb.submitted_at 
            FROM feedbacks fb
            JOIN students s ON fb.student_id = s.id
            JOIN sit_in_records sr ON fb.record_id = sr.id";
    
    // If a specific category filter is requested, append the WHERE clause
    if (!empty($category)) {
        $sql .= " WHERE fb.category = ?";
    }
    
    $sql .= " ORDER BY fb.submitted_at DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!empty($category)) {
        mysqli_stmt_bind_param($stmt, "s", $category);
    }
    
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}