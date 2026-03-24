<?php
require __DIR__. '/../config/database.php';

// get student info by id
function getStudentById($conn, $id) {
    $sql = "SELECT * FROM students WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id); 
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $student = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $student; // Returns the array of student data or null
    }
    return null;
}


// read students data from the db
function getStudents($conn) {
    $sql = "SELECT * FROM students";
    $getData = mysqli_prepare($conn,$sql);
    
    // initialized empty array
    $students = [];

    if ($getData) {
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        // loop trough the table
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row; // add each student to the array
        }
        mysqli_stmt_close($getData);
    }
    return $students;
}

// edit students
function editStudent($conn) {
    $id = $_POST['id'];
    $student_id = $_POST['student_id']; 
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $course = $_POST['course'];
    $year_level = $_POST['year_level'];
    $sit_ins = $_POST['sit_ins'];

    $sql = "UPDATE students SET 
            student_id = ?, 
            firstname = ?, 
            lastname = ?, 
            course = ?, 
            year_level = ?, 
            sit_ins = ? 
            WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssii", 
            $student_id, $firstname, $lastname, $course, $year_level, $sit_ins, $id
        );
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
    return false;
}

//the button was clicked and CALL the function
if (isset($_POST['update_student'])) {
    if (editStudent($conn)) {
        editStudent($conn);
        // Redirect back to list on success
        header("Location: ../admin_module/studentList.php?update=success");
        exit();
    } else {
        echo "Error updating record.";
    }
}


// Function to Delete Student
function deleteStudent($conn) {
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        $sql = "DELETE FROM students WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $success;
        }
    }
    return false;
}

// delete button trigger
if (isset($_POST['confirm_delete'])) {
    if (deleteStudent($conn)) {
        header("Location: ../admin_module/studentList.php?status=deleted");
        exit();
    } else {
        die("Error deleting record.");
    }
}

// add student function
function addStudent($conn) {
    $student_id = $_POST['student_id'];
    $firstname  = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname   = $_POST['lastname'];
    $course     = $_POST['course'];
    $year_level = $_POST['year_level'];
    $email      = $_POST['email'];     
    $address    = $_POST['home_address']; 
    $sit_ins    = 30; // Default sessions
    
    $raw_password = $_POST['password']; 
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO students (student_id, firstname, middlename, lastname, course, year_level, email, home_address, password, sit_ins) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssssssi", 
            $student_id, $firstname, $middlename, $lastname, $course, $year_level, $email, $address, $hashed_password, $sit_ins
        );
        
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
    return false;
}

// add student trigger
if (isset($_POST['add_student'])) {
    if (addStudent($conn)) {
        header("Location: ../admin_module/studentList.php?status=added");
        exit();
    } else {
        die("Error adding student. Make sure the Student ID is unique.");
    }
}