<?php
include("../config/database.php");

// submit testimonial
$message = "";
if (isset($_POST['submit_testimonial'])) {
    $getStudentPk = $_POST['student_pk'];
    $getRating = $_POST['rating'];
    $getContent = $_POST['content'];

    $sql = "INSERT INTO testimonials (student_pk,content,rating)
            VALUES (?,?,?)";
    $getData = mysqli_prepare($conn,$sql);
    
    if ($getData) {
        mysqli_stmt_bind_param($getData,'isi',$getStudentPk,$getContent,$getRating);
        if (mysqli_stmt_execute($getData)) {
            $message = "<div class='alert alert-success'>Testimonial submitted successfully! It is pending for approval.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
        mysqli_stmt_close($getData);
    }
}