<?php
include(__DIR__. "/../config/database.php");

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
     $sql = "SELECT t.id,
                    t.student_pk,
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

// feature/unfeature/delete testimonials
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture testimonal pk and student pk
    $testimonial_id = isset($_POST['testimonial_id']) ? (int)$_POST['testimonial_id'] : 0;
    $student_pk = isset($_POST['feedback_id']) ? (int)$_POST['feedback_id'] : 0;
    
    if ($testimonial_id > 0 && $student_pk > 0) {
        
        // FEATURE 
        if (isset($_POST['action']) && $_POST['action'] === 'feature') {
            $sql = "UPDATE testimonials 
                    SET status = 'featured', is_featured = 1 
                    WHERE id = ? AND student_pk = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ii', $testimonial_id, $student_pk);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                $_SESSION['msg'] = "Testimonial successfully featured!";
                $_SESSION['msg_type'] = "success";
            }
        }
        
        //  UNFEATURE 
        elseif (isset($_POST['action']) && $_POST['action'] === 'unfeature') {
            $sql = "UPDATE testimonials 
                    SET status = 'unfeatured', is_featured = 0 
                    WHERE id = ? AND student_pk = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ii', $testimonial_id, $student_pk);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                $_SESSION['msg'] = "Testimonial removed from featured list.";
                $_SESSION['msg_type'] = "secondary";
            }
        }
        
        // DELETE ACTION
        elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $sql = "DELETE FROM testimonials WHERE id = ? AND student_pk = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ii', $testimonial_id, $student_pk);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                $_SESSION['msg'] = "Testimonial permanently deleted.";
                $_SESSION['msg_type'] = "danger";
            }
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        
    } else {
        $_SESSION['msg'] = "Error: Invalid or mismatched record identifiers.";
        $_SESSION['msg_type'] = "danger";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch only the testimonials that have been explicitly featured by the admin setup
function getFeaturedTestimonials($conn,$limit) {
    $sql = "SELECT  t.id,
                    t.student_pk,
                    t.content,
                    t.rating,
                    CONCAT(st.firstname, ' ' ,st.lastname) AS fullname,
                    CONCAT(st.course, '-' ,st.year_level) AS course_year
                    FROM testimonials t
                    LEFT JOIN students st ON t.student_pk = st.id
                    WHERE status = 'featured' AND is_featured = 1
                    ORDER BY t.id DESC
                    LIMIT ?";
    $getData = mysqli_prepare($conn,$sql);
    if ($getData) {
        mysqli_stmt_bind_param($getData,'i',$limit);
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        mysqli_stmt_close($getData);
        return $result;
    } 
    return false;
}