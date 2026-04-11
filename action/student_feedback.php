<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

session_start();
include("studentData.php"); // This gives us access to $conn and the $student array

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get the Student's Numeric Primary Key (the fix we did earlier)
    $student_pk = isset($student['id']) ? $student['id'] : null;
    
    // 2. Get Form Data
    $record_id = $_POST['session_id'];      // From the hidden input in the modal
    $category  = $_POST['category'];        // From the radio buttons
    $message   = $_POST['feedback_text'];   // From the textarea

    // 3. Validation
    if (!$student_pk || empty($record_id) || empty($message)) {
        header("Location: history.php?status=error&msg=missing_data");
        exit();
    }

    // 4. Insert into the 'feedbacks' table
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