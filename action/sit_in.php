<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Search for a student using their University ID string
 */
function searchStudentById($conn, $student_id) {
    $sql = "SELECT * FROM students WHERE student_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
    return null;
}


/**
 * Update the remaining sessions
 */
function updateSessionCount($conn, $pk_id, $new_count) {
    $sql = "UPDATE students SET sit_ins = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $new_count, $pk_id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
    return false;
}



// Handle the Update Button click
if (isset($_POST['update_sitin_session'])) {
    $pk_id = $_POST['id'];
    $new_count = $_POST['sit_ins'];
    
    // Optional: You could also capture 'language' and 'lab' here
    // WORK ON THIS LATER------
    // if you decide to create a 'sit_in_history' table later.

    if (updateSessionCount($conn, $pk_id, $new_count)) {
        // Redirect back to the management page with a success message
        header("Location: ../admin_module/studentList.php?status=success&search_id=" . $_POST['student_id_str']);
        exit();
    } else {
        header("Location: ../admin_module/sitin_management.php?status=error");
        exit();
    }
}