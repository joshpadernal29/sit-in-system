<?php 
require __DIR__. '/../config/database.php';

// posting annoucemnt 
if (isset($_POST['post_now'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $target_students = $_POST['target'];
    $message = $_POST['message'];

    $sql = "INSERT INTO announcements (title,message,priority,target_audience)
            VALUES (?,?,?,?)";

    $getData = mysqli_prepare($conn, $sql);

    if ($getData) {
        mysqli_stmt_bind_param($getData, 'ssss', $title,$message,$category,$target_students);
        mysqli_stmt_execute($getData);
        mysqli_stmt_close($getData);
        header("Location: ../admin_module/adminDashboard.php?announcement=success"); // redirect t dashboard after posting 
    }
}