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
                                <img src="../assets/default_profile.jpg"
                                    class="rounded-circle border border-4 border-white shadow-sm" alt="Student Avatar"
                                    width="90"> <span
                                    class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-2"
                                    title="Online"></span>
                            </div>

                            <h6 class="fw-bold mb-1 text-dark">
                                <?php echo $student['firstname']. " " .$student['lastname'] ?>
                            </h6>
                            <p class="text-muted mb-3" style="font-size: 0.75rem;">
                                <?php echo $student['student_id'] ?>
                            </p>
                            <!---EDIT PROFILE-->
                            <div class="d-grid">
                                <a href="student_profile.php" class="btn btn-primary btn-sm rounded-pill px-4"
                                    style="font-size: 0.8rem;">Edit Profile</a>
                            </div>
                        </div>

                        <div class="list-group list-group-flush border-top border-light">
                            <div class="list-group-item bg-transparent px-4 py-2"> <small
                                    class="text-muted d-block text-uppercase fw-bold"
                                    style="font-size: 0.6rem;">Course</small>
                                <span class="fw-semibold small text-truncate d-block">
                                    <?php echo $student['course']. "-" .$student['year_level'] ?>
                                </span>
                            </div>
                            <div class="list-group-item bg-transparent px-4 py-2">
                                <small class="text-muted d-block text-uppercase fw-bold"
                                    style="font-size: 0.6rem;">Email</small>
                                <span class="fw-semibold small text-truncate d-block">
                                    <?php echo $student['email'] ?>
                                </span>
                            </div>
                            <div class="list-group-item bg-transparent px-4 py-2">
                                <small class="text-muted d-block text-uppercase fw-bold"
                                    style="font-size: 0.6rem;">Remaining Sessions</small>
                                <span class="badge bg-soft-info text-info rounded-pill px-3">
                                    <?php echo $student['sit_ins'] ?>
                                </span>
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
                <!--MAKE THIS DYNAMIC ANOUNCEMENT BOARD-->
                <div class="col-md-9">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-megaphone-fill text-primary me-2"></i>Campus Announcements
                            </h5>
                            <small class="text-muted">Stay updated with the latest news from CCS Admin</small>
                        </div>
                        <button class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                    </div>

                    <div class="vstack gap-3">

                        <div
                            class="card border-0 shadow-sm rounded-4 border-start border-4 border-primary overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span
                                        class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 small fw-bold">
                                        <i class="bi bi-pin-angle-fill me-1"></i> IMPORTANT
                                    </span>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i> 2 hours ago</small>
                                </div>
                                <h6 class="fw-bold text-dark fs-5">Midterm Examination Schedule</h6>
                                <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.6;">
                                    Please be guided on the upcoming midterm examination schedule for the 2nd Semester.
                                    Ensure that your sit-in requirements are settled at the CCS Laboratory before the
                                    exam starts.
                                </p>
                                <div class="d-flex align-items-center pt-2">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 30px; height: 30px;">
                                        <i class="bi bi-person-fill text-primary small"></i>
                                    </div>
                                    <small class="fw-bold text-dark">CCS Dean's Office</small>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-success">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span
                                        class="badge bg-success-subtle text-success rounded-pill px-3 py-2 small fw-bold">
                                        EVENT
                                    </span>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i> Yesterday</small>
                                </div>
                                <h6 class="fw-bold text-dark fs-5">CCS IT Week 2026: Call for Participants</h6>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                    Registration for the "Code-A-Thon" and "Network Troubleshooting" competitions is now
                                    open. Visit the lab admin to sign up.
                                </p>
                                <a href="#"
                                    class="btn btn-link text-success p-0 mt-3 small fw-bold text-decoration-none">
                                    View Mechanics <i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-info">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-info-subtle text-info rounded-pill px-3 py-2 small fw-bold">
                                        SYSTEM
                                    </span>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i> 3 days ago</small>
                                </div>
                                <h6 class="fw-bold text-dark fs-5">Lab B Maintenance</h6>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                    Lab B will be closed for software updates on Saturday. Students are advised to use
                                    Lab A for their sit-in sessions.
                                </p>
                            </div>
                        </div>

                    </div>

                    <div class="text-center mt-4 pt-2">
                        <button class="btn btn-white text-muted small fw-bold border-0">
                            Show older announcements <i class="bi bi-chevron-down ms-1"></i>
                        </button>
                    </div>
                </div>

                <style>
                    /* Add these colors to your stylesheet if not using Bootstrap 5.3+ */
                    .bg-primary-subtle {
                        background-color: rgba(13, 110, 253, 0.1);
                    }

                    .bg-success-subtle {
                        background-color: rgba(25, 135, 84, 0.1);
                    }

                    .bg-info-subtle {
                        background-color: rgba(13, 202, 240, 0.1);
                    }

                    .card {
                        transition: transform 0.2s ease, box-shadow 0.2s ease;
                    }

                    .card:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
                    }
                </style>

            </div>
        </div>
        <!--main contend end -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>