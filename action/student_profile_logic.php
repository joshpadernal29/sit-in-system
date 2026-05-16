<?php
// Database Connection
require_once __DIR__ . '/../config/database.php';

// 2. Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// FETCH FUNCTION
// Used by the UI to pull student info using the session ID
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

// UPDATE PROCESSOR
// Runs when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $id_to_update = $_POST['id_to_update']; 
    $fname  = trim($_POST['firstname']);
    $lname  = trim($_POST['lastname']);
    $email  = trim($_POST['email']);
    $course = trim($_POST['course']);
    $year   = trim($_POST['year_level']);
    
    // Grab password variables from post stream
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $password_changed = false;
    $hashed_password  = "";

    // Check if the user filled out the password fields
    if (!empty($password)) {
        // Backend Length Validation Guard
        if (strlen($password) < 6) {
            header("Location: ../student_module/student_profile.php?error=password_too_short");
            exit();
        }
        
        // Backend Matching Check Guard
        if ($password !== $confirm_password) {
            header("Location: ../student_module/student_profile.php?error=password_mismatch");
            exit();
        }
        
        // Securely encrypt the password using standard modern hashing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $password_changed = true;
    }

    // Prepare SQL dynamically depending on whether a password update is needed
    if ($password_changed) {
        $sql = "UPDATE students SET firstname=?, lastname=?, email=?, course=?, year_level=?, password=? WHERE student_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssss", $fname, $lname, $email, $course, $year, $hashed_password, $id_to_update);
        }
    } else {
        $sql = "UPDATE students SET firstname=?, lastname=?, email=?, course=?, year_level=? WHERE student_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $fname, $lname, $email, $course, $year, $id_to_update);
        }
    }
    
    // Execute the configured statement context
    if ($stmt) {
        if (mysqli_stmt_execute($stmt)) {
            // Login uses 'user_id', keep session intact,
            // update the names so the Header refreshes immediately.
            $_SESSION['firstname'] = $fname;
            $_SESSION['lastname']  = $lname;

            mysqli_stmt_close($stmt);
            header("Location: ../student_module/student_profile.php?update=success");
            exit();
        } else {
            mysqli_stmt_close($stmt);
            header("Location: ../student_module/student_profile.php?update=error");
            exit();
        }
    } else {
        header("Location: ../student_module/student_profile.php?update=error");
        exit();
    }
}