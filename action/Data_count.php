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

function getStudentHistory($conn,$student_pk,$date_filter,$start_from,$limit){
    $history=[];
    $sql="SELECT 
            sr.id,
            sr.student_pk_id,
            sr.lab,
            sr.login_time,
            sr.logout_time,
            sr.language,
            sr.task_status,
            sr.behavior_score,
            sr.points_earned_this_session,
            COALESCE(f.id,0) AS feedback_submitted,
            TIMESTAMPDIFF(MINUTE,sr.login_time,sr.logout_time)/60 AS duration_hours
        FROM sit_in_records sr
        LEFT JOIN feedbacks f ON sr.id=f.record_id
        WHERE sr.student_pk_id=?";

    if(!empty($date_filter)){
        $sql .= " AND DATE(sr.login_time)=?";
    }

    $sql .= " ORDER BY sr.login_time DESC LIMIT ?,?";
    $stmt = mysqli_prepare($conn,$sql);

    if($stmt){
        if(!empty($date_filter)){
            mysqli_stmt_bind_param($stmt,'isii',$student_pk,$date_filter,$start_from,$limit);
        }else{
            mysqli_stmt_bind_param($stmt,'iii',$student_pk,$start_from,$limit);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while($row = mysqli_fetch_assoc($result)){
            $history[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
    return $history;
}

function getTotalRecords($conn,$student_pk,$date_filter){

    $sql="SELECT COUNT(*) as total 
        FROM sit_in_records 
        WHERE student_pk_id=?";

    if(!empty($date_filter)){
        $sql.=" AND DATE(login_time)=?";
    }

    $stmt=mysqli_prepare($conn,$sql);

    if(!empty($date_filter)){

        mysqli_stmt_bind_param($stmt,'is',$student_pk,$date_filter);

    }else{

        mysqli_stmt_bind_param($stmt,'i',$student_pk);

    }

    mysqli_stmt_execute($stmt);

    $result=mysqli_stmt_get_result($stmt);
    $row=mysqli_fetch_assoc($result);

    return $row['total'];
}

/* ==========================================
   NEW ANALYTICS FUNCTIONS FOR ADMIN PANEL
   ========================================== */

/**
 * Fetches sit-in volume grouped by day of the week for the past 7 days.
 * Returns an array with structure: ['Mon' => int, 'Tue' => int, ...]
 */
function getWeeklyTrafficData($conn) {
    // Establish structural day values initialized to 0
    $weekly_data = [
        'Mon' => 0, 
        'Tue' => 0, 
        'Wed' => 0, 
        'Thu' => 0, 
        'Fri' => 0, 
        'Sat' => 0
    ];

    // Query extracting day abbreviations ('Mon', 'Tue') within a 7-day window
    $sql = "SELECT DATE_FORMAT(login_time, '%a') AS day_name, COUNT(*) AS session_count 
            FROM sit_in_records 
            WHERE login_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DAYOFWEEK(login_time), day_name";
            
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Map query details dynamically back into tracking matrix
        while ($row = mysqli_fetch_assoc($result)) {
            $day = $row['day_name'];
            if (array_key_exists($day, $weekly_data)) {
                $weekly_data[$day] = (int)$row['session_count'];
            }
        }
        mysqli_stmt_close($stmt);
    }

    return $weekly_data;
}