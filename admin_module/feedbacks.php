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
        /* FIX: We do not set height on body to allow the header 
           to exist in the natural document flow.
        */
        body { 
            background-color: #fff; 
            margin: 0; 
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        
        /* FIX: The dropdowns in the include need to be on top.
        */
        header, .navbar {
            z-index: 1060 !important;
            position: relative;
        }

        /* FIX: The Inbox Wrapper now calculates height based on 
           the viewport MINUS the header height (approx 60px).
        */
        .inbox-wrapper {
            display: flex;
            height: calc(100vh - 62px); 
            overflow: hidden;
            border-top: 1px solid #edf2f7;
        }

        /* --- UI DESIGN PRESERVED --- */
        .inbox-list {
            width: 400px;
            border-right: 1px solid #edf2f7;
            overflow-y: auto;
            background: #fbfcfd;
        }

        .list-item {
            padding: 20px;
            border-bottom: 1px solid #f1f4f8;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        .list-item:hover { background: #f1f5f9; }
        .list-item.active {
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            z-index: 2;
        }
        .list-item.active::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0; width: 4px;
            background: #0d6efd;
        }

        .inbox-detail {
            flex: 1;
            overflow-y: auto;
            padding: 50px;
            background: #fff;
        }

        .avatar-lg {
            width: 55px; height: 55px;
            background: #212529; color: #fff;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.4rem;
        }

        .cat-pill {
            font-size: 0.65rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.5px;
            padding: 4px 10px; border-radius: 6px;
        }

        .msg-container {
            background: #f8fafc;
            border-radius: 1.5rem;
            padding: 30px;
            line-height: 1.8;
            color: #334155;
            font-size: 1.05rem;
            border: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <?php include("../includes/adminHeader.php"); ?>

    <div class="inbox-wrapper">
        <aside class="inbox-list">
            <div class="p-3 sticky-top bg-white border-bottom shadow-sm">
                <form action="" method="GET" class="row g-2">
                    <div class="col-8">
                        <select name="filter_cat" class="form-select form-select-sm border-0 bg-light rounded-3">
                            <option value="">All Categories</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Software">Software</option>
                            <option value="Environment">Environment</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-sm btn-primary w-100 rounded-3">Filter</button>
                    </div>
                </form>
            </div>

            <?php if($total_rows > 0): ?>
                <?php while($row = mysqli_fetch_assoc($feedback_list)): ?>
                <div class="list-item" onclick="showDetail(this, <?php echo htmlspecialchars(json_encode($row)); ?>)">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="cat-pill bg-primary-subtle text-primary"><?php echo $row['category']; ?></span>
                        <small class="text-muted" style="font-size: 0.7rem;">
                            <?php echo date('M d, Y | h:i A', strtotime($row['submitted_at'])); ?>
                        </small>
                    </div>
                    <h6 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($row['fullname']); ?></h6>
                    <p class="text-muted small mb-0 text-truncate"><?php echo htmlspecialchars($row['message']); ?></p>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </aside>

        <article class="inbox-detail" id="detailPane">
            <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                <i class="bi bi-layout-sidebar-inset text-light mb-3" style="font-size: 5rem;"></i>
                <h5>Select a Feedback to view</h5>
            </div>
        </article>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function showDetail(element, data) {
            document.querySelectorAll('.list-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');

            const date = new Date(data.submitted_at);
            const formattedDate = date.toLocaleDateString('en-US', { 
                month: 'long', day: 'numeric', year: 'numeric', 
                hour: '2-digit', minute: '2-digit', hour12: true 
            });

            const pane = document.getElementById('detailPane');
            pane.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-5">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg me-3 shadow-sm">${data.fullname.charAt(0).toUpperCase()}</div>
                        <div>
                            <h2 class="fw-bold mb-0 text-dark">${data.fullname}</h2>
                            <p class="text-muted mb-0">ID: ${data.student_id} • ${formattedDate}</p>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <span class="badge bg-dark rounded-pill px-4 py-2">${data.category}</span>
                </div>
                <div class="msg-container shadow-sm">
                    ${data.message.replace(/\n/g, '<br>')}
                </div>
            `;
        }
    </script>
</body>
</html>