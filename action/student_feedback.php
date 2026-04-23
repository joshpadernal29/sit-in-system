<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

session_start();
include("studentData.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_pk = isset($student['id']) ? $student['id'] : null;
    
    $record_id = $_POST['session_id'];      // From the hidden input in the modal
    $category  = $_POST['category'];        // From the radio buttons
    $message   = $_POST['feedback_text'];   // From the textarea

    if (!$student_pk || empty($record_id) || empty($message)) {
        header("Location: history.php?status=error&msg=missing_data");
        exit();
    }

    $sql = "INSERT INTO feedbacks (record_id, student_id, category, message) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iiss", $record_id, $student_pk, $category, $message);
        
        if (mysqli_stmt_execute($stmt)) {
            // SUCCESS: Redirect back with a success flag
            header("Location: ../student_module/sit_in_history.php?status=success");
        } else {
            // DATABASE ERROR
            header("Location: history.php?status=error&msg=db_fail");
        }
        mysqli_stmt_close($stmt);
    } else {
        header("Location: ../student_module/sit_in_history.php?status=error&msg=stmt_fail");
    }
} else {
    // If someone tries to access this file directly without POSTing
    header("Location: ../student_module/sit_in_history.php");
}
?>