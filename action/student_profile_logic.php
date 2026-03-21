<?php
// 1. Database Connection
require_once __DIR__ . '/../config/database.php';

// 2. Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * FETCH FUNCTION
 * Used by the UI to pull student info using the session ID
 */
if (!function_exists('getStudentDetails')) {
    function getStudentDetails($conn, $student_id) {
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
}

/**
 * UPDATE PROCESSOR
 * Runs when the form is submitted
 */
if (isset($_POST['update_profile'])) {
    $id_to_update = $_POST['id_to_update']; 
    $fname        = $_POST['firstname'];
    $lname        = $_POST['lastname'];
    $email        = $_POST['email'];
    $course       = $_POST['course'];
    $year         = $_POST['year_level'];

    $sql = "UPDATE students SET firstname=?, lastname=?, email=?, course=?, year_level=? WHERE student_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $fname, $lname, $email, $course, $year, $id_to_update);
        
        if (mysqli_stmt_execute($stmt)) {
            // Since your login uses 'user_id', we keep that session intact,
            // but we update the names so the Header refreshes immediately.
            $_SESSION['firstname'] = $fname;
            $_SESSION['lastname']  = $lname;

            header("Location: ../student_module/student_profile.php?update=success");
            exit();
        } else {
            header("Location: ../student_module/student_profile.php?update=error");
            exit();
        }
    }
}