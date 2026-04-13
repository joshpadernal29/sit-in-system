<?php
require __DIR__. '/../config/database.php';
// this file contains functions for getting data's for the admin/student dashboard

// get number of students in the database
function countStudents($conn) {
    $sql = "SELECT COUNT(id) AS registered FROM students";
    $getNo = mysqli_prepare($conn,$sql);

    if ($getNo) {
        mysqli_stmt_execute($getNo);
        $result = mysqli_stmt_get_result($getNo);
        $student_no = mysqli_fetch_assoc($result);
        return $student_no['registered'];
    }
    return 0;
}

// get current sit-ins
function currentSitIns($conn) {
    $status = 'Active';
    $sql = "SELECT COUNT(*) AS current_sit_in FROM sit_in_records WHERE status = ?";
    $getData = mysqli_prepare($conn,$sql);
    
    if ($getData) {
        mysqli_stmt_bind_param($getData, 's', $status);
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        $row = mysqli_fetch_assoc($result);
        $current_sit_ins = $row['current_sit_in']; // asign the single value to the current_sit_in variable 
        mysqli_stmt_close($getData);

        return $current_sit_ins;
    }

    return 0;
}

// get total sessions (sit in)
function getTotalSessions($conn) {
    $status = 'Completed';
    $sql = "SELECT COUNT(*) AS total_sessions FROM sit_in_records WHERE status = ?";
    $getData = mysqli_prepare($conn,$sql);

    if ($getData) {
        mysqli_stmt_bind_param($getData, 's', $status);
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        $row = mysqli_fetch_assoc($result);
        $total_sessions = $row['total_sessions'];// asign the single value to the total_sessions variable 
        mysqli_close($conn);

        return $total_sessions;
    }

    return 0;
}

// get programming language preferences/used
function languageUsed($conn, $language) {
    $sql = "SELECT COUNT(*) AS language_used FROM sit_in_records WHERE language = ?";
    $getData = mysqli_prepare($conn, $sql);

    if ($getData) {
        mysqli_stmt_bind_param($getData, 's' ,$language);
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        $row = mysqli_fetch_assoc($result);
        $language_used = $row['language_used'];
        mysqli_stmt_close($getData);

        return $language_used;
    }

    return 0;
}

// getting annoucements from the db
function getPost($conn, $active) {
    $announcements = []; // initialize array
    $sql = "SELECT * FROM announcements WHERE is_active = ?
            ORDER BY date_posted DESC";
    $getData = mysqli_prepare($conn,$sql);

    if ($getData) {
        mysqli_stmt_bind_param($getData, 'i', $active);
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        
        // loop through the array
        while ($row = mysqli_fetch_assoc($result)) {
            $announcements[] = $row;
        }
    }
    return $announcements;
}


// get students language used
function progLanguage($conn,$student_id) {
    $language = []; // initialize array to contain sql data
    $sql = "SELECT language,COUNT(*) AS language_count FROM sit_in_records 
            WHERE student_id_str = ?
            GROUP BY language";
    $getData = mysqli_prepare($conn,$sql);

    if ($getData) {
        mysqli_stmt_bind_param($getData,'s',$student_id);
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        
        // loop through array
        while($row = mysqli_fetch_assoc($result)) {
            $language[] = $row;
        }
        mysqli_stmt_close($getData);
    }
    return $language;
}

// function to get students sit in (for line graph data)
function sit_in_rate($conn, $student_id) {
    $sit_in = [];
    $sql = "SELECT DATE(logout_time) AS sit_in_date, COUNT(*) AS sit_in_rate FROM sit_in_records
            WHERE student_id_str = ?
            GROUP BY DATE(logout_time)
            ORDER BY sit_in_date ASC";
    $getData = mysqli_prepare($conn,$sql);
    if ($getData) {
        mysqli_stmt_bind_param($getData,'s',$student_id);
        mysqli_stmt_execute($getData);
        $result = mysqli_stmt_get_result($getData);
        while($row = mysqli_fetch_assoc($result)) {
            $sit_in[] = $row;
        }
        mysqli_stmt_close($getData);
    }
    return $sit_in;
}
