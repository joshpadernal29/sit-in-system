<?php
include("../config/database.php");

// submit testimonial (student)
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

// admin 
// get testimonials from the dba_close
// all (testimonoials)
function getAllTestimonials($conn) {
     $sql = "SELECT t.student_pk,
                   t.content,
                   t.rating,
                   t.status,
                   t.is_featured,
                   t.created_at,
                   CONCAT(st.firstname, ' ' ,st.lastname) AS fullname,
                   st.student_id
                   FROM testimonials t
                   LEFT JOIN students st ON t.student_pk = st.id
                   ORDER BY t.created_at DESC";
    $getData = mysqli_prepare($conn,$sql);
    if ($getData) {
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        mysqli_stmt_close($getData);

        return $result;
    }
    return false;
}


// pending (testimonials)
if(isset($_GET['pending_requests'])) {

}



// approved
if(isset($_GET['approved_requests'])) {

}