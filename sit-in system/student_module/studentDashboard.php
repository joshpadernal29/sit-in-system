<?php
// session start if there is no session active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../action/studentData.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net">
    <title>dashboard</title>
</head>

<body>
    <!--header-->
    <?php include("../includes/studentHeader.php"); ?> 
    <!--header end-->

    <!--main contend-->
    <main>
        <div class="container-fluid py-4 px-lg-5">
            <div class="row g-4">

                <div class="col-md-3">
                    <div class="card border-0 shadow rounded-4 mb-4">
                        <div class="card-body p-4 text-center">
                            <div class="position-relative d-inline-block mb-3">
                                <img src="https://ui-avatars.com/api/?name=Juan+Dela+Cruz&background=0D6EFD&color=fff&size=128"
                                    class="rounded-circle border border-4 border-white shadow-sm" alt="Student Avatar"
                                    width="90"> <span
                                    class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-2"
                                    title="Online"></span>
                            </div>

                            <h6 class="fw-bold mb-1 text-dark"><?php echo $student['firstname']. " " .$student['lastname'] ?></h6>
                            <p class="text-muted mb-3" style="font-size: 0.75rem;"><?php echo $student['student_id'] ?></p>
                            <!---EDIT PROFILE-->
                            <div class="d-grid">
                                <a href="profile.php" class="btn btn-primary btn-sm rounded-pill px-4"
                                    style="font-size: 0.8rem;">Edit Profile</a>
                            </div>
                        </div>

                        <div class="list-group list-group-flush border-top border-light">
                            <div class="list-group-item bg-transparent px-4 py-2"> <small
                                    class="text-muted d-block text-uppercase fw-bold"
                                    style="font-size: 0.6rem;">Course</small>
                                <span class="fw-semibold small text-truncate d-block"><?php echo $student['course']. "-" .$student['year_level'] ?></span>
                            </div>
                            <div class="list-group-item bg-transparent px-4 py-2">
                                <small class="text-muted d-block text-uppercase fw-bold"
                                    style="font-size: 0.6rem;">Email</small>
                                <span class="fw-semibold small text-truncate d-block"><?php echo $student['email'] ?></span>
                            </div>
                            <div class="list-group-item bg-transparent px-4 py-2">
                                <small class="text-muted d-block text-uppercase fw-bold"
                                    style="font-size: 0.6rem;">Remaining Sessions</small>
                                <span class="badge bg-soft-info text-info rounded-pill px-3"><?php echo $student['sit_ins'] ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 bg-primary text-white shadow rounded-4 p-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check fs-4 me-2"></i>
                            <div>
                                <p class="mb-0 fw-bold" style="font-size: 0.7rem;">Verified</p>
                                <p class="mb-0 opacity-75" style="font-size: 0.65rem;">Active for 2nd Sem</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div
                            class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Recent Activity</h5>
                            <span class="badge bg-light text-dark fw-normal border">Last 30 Days</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="text-center py-5">
                                <i class="bi bi-calendar2-x text-muted opacity-25" style="font-size: 4rem;"></i>
                                <h6 class="text-dark mt-3">No activity yet</h6>
                                <p class="text-muted small">Your recent laboratory sit-in records will appear here.</p>
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-4 mt-2">Check In
                                    Now</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--main contend end -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>