<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registry | UC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

    <?php include("../includes/adminHeader.php"); ?>

    <main class="container-fluid py-4 px-lg-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">Student Registry</h4>
                <p class="text-muted small mb-0">Manage and view all registered students in the system.</p>
            </div>

            <div class="d-flex gap-2">
                <div class="input-group shadow-sm" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Search ID or Name...">
                </div>
                <button class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus-fill"></i> <span class="d-none d-sm-inline">Add Student</span>
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-bold small text-uppercase">Student ID</th>
                            <th class="py-3 text-muted fw-bold small text-uppercase">Full Name</th>
                            <th class="py-3 text-muted fw-bold small text-uppercase">Course & Year</th>
                            <th class="py-3 text-muted fw-bold small text-uppercase text-center">Sessions</th>
                            <th class="py-3 text-muted fw-bold small text-uppercase">Status</th>
                            <th class="pe-4 py-3 text-muted fw-bold small text-uppercase text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 fw-bold text-primary">2021-0001-CEBU</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=Juan+Dela+Cruz&background=random"
                                        class="rounded-circle me-2" width="32">
                                    <span>Juan Dela Cruz</span>
                                </div>
                            </td>
                            <td>BSIT - 3</td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-light text-dark border">24 / 30</span>
                            </td>
                            <td>
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle px-3">Active</span>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light border" title="Edit Student"><i
                                        class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-light border text-danger" title="Delete"><i
                                        class="bi bi-trash"></i></button>
                            </td>
                        </tr>

                        <tr>
                            <td class="ps-4 fw-bold text-primary">2021-0452-CEBU</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=Maria+Santos&background=random"
                                        class="rounded-circle me-2" width="32">
                                    <span>Maria Santos</span>
                                </div>
                            </td>
                            <td>BSCS - 4</td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-light text-dark border">10 / 30</span>
                            </td>
                            <td>
                                <span
                                    class="badge bg-warning-subtle text-warning border border-warning-subtle px-3">Warning</span>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-light border text-danger"><i
                                        class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top-0 py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Showing 1 to 10 of 1,240 students</small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>

    <style>
        .table thead th {
            letter-spacing: 0.5px;
            font-size: 0.75rem;
            background-color: #f8f9fa;
        }

        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.1);
        }

        tr {
            transition: background-color 0.2s ease;
        }

        tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }
    </style>
</body>

</html>