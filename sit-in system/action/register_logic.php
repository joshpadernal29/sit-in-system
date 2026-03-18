<?php
// databse connection
require __DIR__ . "/../config/database.php";

if (isset($_POST['reg_btn'])) {
    // get all user data and store to variables
    $studentID = $_POST['reg_student_id'];
    $firstName = $_POST['reg_fname'];
    $middleName = $_POST['reg_mname'];
    $lastName = $_POST['reg_lname'];
    $course = $_POST['reg_course'];
    $level = $_POST['reg_lvl'];
    $email = $_POST['reg_email'];
    $address = $_POST['reg_address'];
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['confirm_password'];

    // check password
    $confirmPass = confirmPass($password,$confirm_password);
    if ($confirmPass) {
        // hash password
        $hashedPass = hashPassword($confirmPass);

        // query to insert data
        $sql = "INSERT INTO students (student_id,firstname,middlename,lastname,course,year_level,email,home_address,password)
                VALUES(?,?,?,?,?,?,?,?,?)";

        // prepare stmt
        $insertData = mysqli_prepare($conn,$sql);
        // bind parameters
        mysqli_stmt_bind_param($insertData,'sssssssss', $studentID, $firstName,$middleName,$lastName,$course,$level,$email,$address,$hashedPass);
        // execute stmt
        mysqli_stmt_execute($insertData);
        // close stmt
        mysqli_stmt_close($insertData);
        // redirect to login page
        header("Location: ../login.php");
    } else {
        echo "password does not match!"; // display in ui later....
    }
}

// function confirm password
function confirmPass($password,$confirmPassword) {
    if ($confirmPassword !== $password) {
        return false;
    }
    return true;
} 

// hash password
function hashPassword($password) {
    // hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    return $hashedPassword;
}

// close db connection
mysqli_close($conn);
