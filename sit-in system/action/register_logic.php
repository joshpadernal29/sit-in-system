<?php

require __DIR__ . "/config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $student_id = $_POST['student_id'];
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $course = $_POST['regCourse'];
    $year_level = $_POST['regCourseLvl'];
    $email = $_POST['regEmail'];
    $address = $_POST['regAddress'];
    $password = $_POST['regPass'];
    $confirm_password = $_POST['confPass'];

    // Validate password
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare query
    $sql = "INSERT INTO students 
            (student_id, first_name, middle_name, last_name, course, year_level, email, address, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssss",
        $student_id,
        $fname,
        $mname,
        $lname,
        $course,
        $year_level,
        $email,
        $address,
        $hashed_password
    );

    // Execute
    if (mysqli_stmt_execute($stmt)) {
        echo "Registration successful!";
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>