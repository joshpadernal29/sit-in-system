<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../config/database.php');
include('../action/sit_in.php');

$feedback_list = getFeedbacks($conn); 
$total_rows = mysqli_num_rows($feedback_list);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Inbox | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #fff; height: 100vh; overflow: hidden; }
        
        /* Layout Container */
        .inbox-wrapper {
            display: flex;
            height: calc(100vh - 70px); /* Adjust based on your header height */
            border-top: 1px solid #dee2e6;
        }

        /* Left Side: List of Feedbacks */
        .inbox-list {
            width: 380px;
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
            background: #f8f9fa;
        }

        /* Right Side: Detail View */
        .inbox-detail {
            flex: 1;
            overflow-y: auto;
            background: #fff;
            padding: 40px;
        }

        /* List Items */
        .list-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8f9fa;
        }
        .list-item:hover { background: #f1f3f5; }
        .list-item.active {
            background: #fff;
            border-left: 4px solid #0d6efd;
            box-shadow: -5px 0 15px rgba(0,0,0,0.05);
        }

        /* Typography */
        .snippet {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: 0.85rem;
            color: #6c757d;
        }

        .avatar-circle {
            width: 45px;
            height: 45px;
            background: #212529;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .cat-tag {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include("../includes/adminHeader.php"); ?>

    <div class="inbox-wrapper">
        <div class="inbox-list">
            <div class="p-3 bg-white sticky-top border-bottom">
                <form action="" method="GET" class="row g-2">
                    <div class="col-8">
                        <select name="filter_cat" class="form-select form-select-sm border-light bg-light">
                            <option value="">All Categories</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Software">Software</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-sm btn-dark w-100">Filter</button>
                    </div>
                </form>
            </div>

            <?php if($total_rows > 0): ?>
                <?php while($row = mysqli_fetch_assoc($feedback_list)): ?>
                <div class="list-item" onclick="viewFeedback(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="cat-tag bg-primary text-white"><?php echo $row['category']; ?></span>
                        <small class="text-muted" style="font-size: 0.7rem;">
                            <?php echo date('M d, Y | h:i A', strtotime($row['submitted_at'])); ?>
                        </small>
                    </div>
                    <h6 class="mb-1 fw-bold text-dark"><?php echo $row['fullname']; ?></h6>
                    <p class="snippet mb-0"><?php echo $row['message']; ?></p>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div class="inbox-detail" id="detailPane">
            <div class="text-center py-5 mt-5">
                <i class="bi bi-envelope-open text-light" style="font-size: 5rem;"></i>
                <h4 class="text-muted">Select a feedback to read</h4>
                <p class="text-muted">Click on any item in the left list to view full details.</p>
            </div>
        </div>
    </div>

    <script>
        function viewFeedback(data) {
            // Update active state in list
            document.querySelectorAll('.list-item').forEach(item => item.classList.remove('active'));
            event.currentTarget.classList.add('active');

            // Convert the string from the database into a JavaScript Date object
            const dateObj = new Date(data.submitted_at);

            const formattedDate = dateObj.toLocaleDateString('en-US', { 
                month: 'long', 
                day: 'numeric', 
                year: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: true // 12-hour format with AM/PM
            });

            // Render Detail Pane
            const detailPane = document.getElementById('detailPane');
            detailPane.innerHTML = `
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar-circle me-3">${data.fullname.charAt(0).toUpperCase()}</div>
                    <div>
                        <h3 class="fw-bold mb-0">${data.fullname}</h3>
                        <p class="text-muted mb-0">Student ID: ${data.student_id} • ${formattedDate}</p>
                    </div>
                </div>
                <hr>
                <div class="py-3">
                    <span class="badge bg-primary mb-3 px-3 py-2 text-uppercase" style="letter-spacing:1px">${data.category}</span>
                    <div class="p-4 bg-light rounded-4 border-0" style="min-height: 200px; font-size: 1.1rem; line-height: 1.8;">
                        ${data.message.replace(/\n/g, '<br>')}
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-4" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Print This Feedback
                    </button>
                </div>
            `;
        }
    </script>
</body>
</html>