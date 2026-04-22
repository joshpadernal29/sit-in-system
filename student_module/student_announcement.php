<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include("../action/studentData.php");
include("../config/database.php");
include("../action/Data_count.php");

$course = isset($student['course']) ? strtolower($student['course']) : 'all';
$date = $_GET['date'] ?? '';
$priority = $_GET['priority'] ?? '';

// Build Query
$query = "SELECT * FROM announcements WHERE is_active = 1 AND (target_audience = 'all' OR target_audience = ?)";
$filterParams = [$course];
$filterTypes = "s";

if (!empty($date)) {
    $query .= " AND DATE(date_posted) = ?";
    $filterParams[] = $date;
    $filterTypes .= "s";
}
if (!empty($priority)) {
    $query .= " AND priority = ?";
    $filterParams[] = $priority;
    $filterTypes .= "s";
}

$query .= " ORDER BY date_posted DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param($filterTypes, ...$filterParams);
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements | SIT-IN System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* CORE LAYOUT - Fixed height for the inbox feel */
        body { 
            background-color: #f8fafc; 
            height: 100vh; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
            font-family: 'Inter', sans-serif;
            margin: 0;
        }

        .inbox-wrapper {
            display: flex;
            flex: 1;
            overflow: hidden;
            border-top: 1px solid #e2e8f0;
        }

        /* SIDEBAR - Slate blue/gray background for contrast */
        .inbox-list {
            width: 380px;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            background: #f1f5f9; /* Soft Slate */
        }

        .filter-header {
            background: #f1f5f9;
            padding: 1.2rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* CARD STYLE ITEMS */
        .list-item {
            padding: 18px;
            margin: 0 12px 10px 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: transparent;
            border: 1px solid transparent;
            position: relative;
        }

        .list-item:hover {
            background: rgba(13, 110, 253, 0.04);
        }

        .list-item.active {
            background: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transform: translateX(4px);
        }

        .list-item.active::before {
            content: "";
            position: absolute;
            left: -12px; top: 15px; bottom: 15px; width: 4px;
            background: #0d6efd;
            border-radius: 0 4px 4px 0;
        }

        /* DETAIL PANE - Clean white workspace */
        .inbox-detail {
            flex: 1;
            overflow-y: auto;
            padding: 50px 80px;
            background: #ffffff;
        }

        .announcement-body {
            background: #f8fafc;
            border-radius: 1.5rem;
            padding: 40px;
            line-height: 1.8;
            color: #334155;
            font-size: 1.1rem;
            border: 1px solid #edf2f7;
            margin-top: 2rem;
        }

        .priority-pill {
            font-size: 0.65rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.8px;
            padding: 5px 12px; border-radius: 6px;
        }

        /* NOTIFICATION DOT */
        .unread-dot {
            height: 10px; width: 10px;
            background-color: #ef4444;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #ef4444;
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body>

    <?php include("../includes/studentHeader.php"); ?>

    <div class="inbox-wrapper">
        <aside class="inbox-list">
            <div class="filter-header">
                <form action="" method="GET" class="row g-2">
                    <div class="col-12 mb-1">
                        <label class="form-label small fw-bold text-primary text-uppercase" style="font-size: 0.6rem;">Date Posted</label>
                        <input type="date" name="date" class="form-control form-control-sm border shadow-sm rounded-3" value="<?php echo htmlspecialchars($date); ?>">
                    </div>
                    <div class="col-8">
                        <select name="priority" class="form-select form-select-sm border shadow-sm rounded-3">
                            <option value="">All Types</option>
                            <option value="urgent" <?php echo ($priority=='urgent')?'selected':'';?>>Urgent</option>
                            <option value="academic" <?php echo ($priority=='academic')?'selected':'';?>>Academic</option>
                            <option value="general" <?php echo ($priority=='general')?'selected':'';?>>General</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3 shadow-sm">
                            <i class="bi bi-funnel"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-2 pb-4">
                <?php if(!empty($posts)): ?>
                    <?php foreach($posts as $post): ?>
                    <div class="list-item" id="item-<?php echo $post['id']; ?>" onclick="loadAnnouncement(this, <?php echo htmlspecialchars(json_encode($post)); ?>)">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <span id="dot-<?php echo $post['id']; ?>" class="unread-dot d-none"></span>
                                <small class="text-muted fw-semibold"><?php echo date('M d, Y', strtotime($post['date_posted'])); ?></small>
                            </div>
                            <span class="priority-pill bg-white text-dark border shadow-sm"><?php echo $post['priority']; ?></span>
                        </div>
                        <h6 class="mb-1 fw-bold text-dark text-truncate"><?php echo htmlspecialchars($post['title']); ?></h6>
                        <p class="text-muted small mb-0 text-truncate"><?php echo htmlspecialchars($post['message']); ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-5 text-center">
                        <i class="bi bi- megaphone display-4 text-light"></i>
                        <p class="text-muted mt-2">No announcements</p>
                    </div>
                <?php endif; ?>
            </div>
        </aside>

        <article class="inbox-detail" id="detailPane">
            <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted text-center">
                <div class="bg-light rounded-pill p-4 mb-3">
                    <i class="bi bi-megaphone text-primary opacity-25" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold text-dark">CCS News Feed</h4>
                <p>Select an item from the sidebar to read the full announcement.</p>
            </div>
        </article>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Check local storage for unread status
        document.addEventListener("DOMContentLoaded", function() {
            const lastRead = parseInt(localStorage.getItem('lastReadAnnounce')) || 0;
            document.querySelectorAll('.unread-dot').forEach(dot => {
                const id = parseInt(dot.id.replace('dot-', ''));
                if (id > lastRead) dot.classList.remove('d-none');
            });
        });

        function loadAnnouncement(element, data) {
            // UI Update
            document.querySelectorAll('.list-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');

            // Mark as read
            const dot = document.getElementById('dot-' + data.id);
            if(dot) dot.classList.add('d-none');
            const lastRead = parseInt(localStorage.getItem('lastReadAnnounce')) || 0;
            if (data.id > lastRead) localStorage.setItem('lastReadAnnounce', data.id);

            // Format date
            const date = new Date(data.date_posted);
            const formattedDate = date.toLocaleDateString('en-US', { 
                month: 'long', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true 
            });

            // Color logic
            let pColor = data.priority === 'urgent' ? 'bg-danger' : (data.priority === 'academic' ? 'bg-primary' : 'bg-success');

            const pane = document.getElementById('detailPane');
            pane.innerHTML = `
                <div class="animate-fade-in">
                    <span class="badge ${pColor} rounded-pill px-3 py-2 mb-3 text-uppercase shadow-sm">${data.priority}</span>
                    <h1 class="fw-bold text-dark display-5 mb-2">${data.title}</h1>
                    <div class="d-flex align-items-center text-muted">
                        <i class="bi bi-clock-history me-2"></i>
                        <span>Posted on ${formattedDate}</span>
                    </div>

                    <div class="announcement-body shadow-sm">
                        ${data.message.replace(/\n/g, '<br>')}
                    </div>

                    <div class="mt-5 d-flex align-items-center p-4 rounded-4 border bg-light shadow-sm">
                        <div class="bg-white rounded-circle p-2 shadow-sm me-3 border">
                            <img src='../assets/ccsmainlogo2.png' alt="Logo" style="width: 40px; height:40px; object-fit:contain;">
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">CCS ADMIN</h6>
                            <small class="text-muted">University of Cebu Main Campus</small>
                        </div>
                    </div>
                </div>
            `;
        }
    </script>
</body>
</html>