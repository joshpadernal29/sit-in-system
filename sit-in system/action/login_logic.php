<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_POST['student_id'];
    $password = $_POST['password'];

    // Hardcoded Admin
    $admin_id = "admin";
    $admin_pass = "admin123";

    // Hardcoded Student
    $student_user = "1010";
    $student_pass = "student123";

    // ADMIN LOGIN
    if ($student_id === $admin_id && $password === $admin_pass) {

        $_SESSION['role'] = "admin";
        $_SESSION['user'] = "Administrator";

        header("Location: admin/dashboard.php");
        exit();
    }

    // STUDENT LOGIN
    elseif ($student_id === $student_user && $password === $student_pass) {

        $_SESSION['role'] = "student";
        $_SESSION['user'] = $student_user;

        header("Location: dashboard.php");
        exit();
    }

    else {
        echo "<script>
                alert('Invalid Login Credentials');
                window.location.href='login.php';
              </script>";
    }
}
?>