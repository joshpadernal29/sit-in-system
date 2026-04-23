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
    <title>Announcements | SIT-IN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --slate-blue: #f8fafc; --msg-active: #ffffff; --border-color: #e2e8f0; }
        body { height: 100vh; overflow: hidden; font-family: 'Inter', sans-serif; background: #fff; margin: 0; }
        
        .inbox-wrapper { height: calc(100vh - 65px); } 
        
        /* Sidebar - Slate background for contrast against white cards */
        .inbox-list { 
            width: 380px; 
            background: #f1f5f9; 
            border-right: 1px solid var(--border-color); 
            overflow-y: auto; 
        }

        /* List Item Depth */
        .list-item { 
            cursor: pointer; 
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); 
            margin: 8px 12px;
            border-radius: 12px;
            background: transparent;
            border: 1px solid transparent;
        }
        .list-item:hover { background-color: rgba(255,255,255,0.5); }
        .list-item.active { 
            background-color: var(--msg-active); 
            border-color: var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateX(4px);
        }
        
        .avatar-box { width: 48px; height: 48px; flex-shrink: 0; }

        /* Message Content Depth */
        .msg-bubble {
            background: #ffffff;
            padding: 35px;
            border-radius: 20px;
            line-height: 1.8;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .inbox-detail { flex: 1; overflow-y: auto; background: #ffffff; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body>

    <?php include("../includes/studentHeader.php"); ?>

    <div class="inbox-wrapper d-flex">
        
        <aside class="inbox-list d-flex flex-column">
            <div class="p-3 sticky-top bg-white border-bottom shadow-sm">
                <form action="" method="GET" class="row g-2">
                    <div class="col-12">
                        <div class="input-group input-group-sm border rounded-pill overflow-hidden bg-light">
                            <span class="input-group-text border-0 bg-transparent ps-3"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" name="date" class="form-control border-0 bg-transparent shadow-none" value="<?= htmlspecialchars($date); ?>">
                        </div>
                    </div>
                    <div class="col-8">
                        <select name="priority" class="form-select form-select-sm rounded-pill border bg-light px-3 shadow-none">
                            <option value="">All Priorities</option>
                            <option value="urgent" <?= ($priority=='urgent')?'selected':'';?>>Urgent</option>
                            <option value="academic" <?= ($priority=='academic')?'selected':'';?>>Academic</option>
                            <option value="general" <?= ($priority=='general')?'selected':'';?>>General</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill fw-bold shadow-sm">Apply</button>
                    </div>
                </form>
            </div>

            <div class="flex-grow-1 py-2">
                <?php foreach($posts as $post): ?>
                <div class="list-item p-3 d-flex align-items-center" 
                     id="item-<?= $post['id']; ?>"
                     onclick="loadAnnouncement(this, <?= htmlspecialchars(json_encode($post)); ?>)">
                    
                    <div class="avatar-box rounded-circle bg-white d-flex align-items-center justify-content-center border shadow-sm">
                        <img src="../assets/ccsmainlogo2.png" style="width: 25px;">
                    </div>

                    <div class="ms-3 overflow-hidden w-100">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 text-truncate fw-bold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($post['title']); ?></h6>
                            <span id="dot-<?= $post['id']; ?>" class="badge rounded-circle p-1 bg-primary d-none" style="height: 8px; width: 8px;"> </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-0 text-muted small text-truncate" style="max-width: 150px;"><?= htmlspecialchars($post['message']); ?></p>
                            <small class="text-muted fw-bold" style="font-size: 0.7rem;"><?= date('M d', strtotime($post['date_posted'])); ?></small>
                        </div>
                    </div>
                </div>
                <div class="mx-4 border-bottom opacity-50"></div>
                <?php endforeach; ?>
            </div>
        </aside>

        <article class="inbox-detail d-flex flex-column" id="detailPane">
            <div class="m-auto text-center opacity-75">
                <i class="bi bi-chat-square-quote display-1 text-light-emphasis mb-3"></i>
                <h4 class="fw-bold">No Announcement Selected</h4>
                <p class="text-muted">Click on a post from the sidebar to view details.</p>
            </div>
        </article>

    </div>

    <script>
        function loadAnnouncement(element, data) {
            document.querySelectorAll('.list-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');

            const dot = document.getElementById('dot-' + data.id);
            if(dot) dot.classList.add('d-none');
            
            const pane = document.getElementById('detailPane');
            const colors = { urgent: 'danger', academic: 'primary', general: 'success' };
            const badgeColor = colors[data.priority] || 'secondary';

            pane.innerHTML = `
                <div class="p-4 border-bottom bg-white sticky-top d-flex align-items-center shadow-sm">
                    <div class="rounded-circle bg-light border p-2 me-3">
                        <img src="../assets/ccsmainlogo2.png" style="width:30px;">
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">CCS Admin Official</h6>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i> Posted on ${new Date(data.date_posted).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</small>
                    </div>
                </div>
                <div class="p-5 mx-auto w-100" style="max-width: 850px;">
                    <span class="badge bg-${badgeColor} rounded-pill px-3 py-2 mb-3 text-uppercase shadow-sm" style="font-size:0.7rem;">${data.priority}</span>
                    <h1 class="fw-bold text-dark display-6 mb-4">${data.title}</h1>
                    
                    <div class="msg-bubble animate-fade-in">
                        ${data.message.replace(/\n/g, '<br>')}
                    </div>

                    <div class="mt-4 p-3 border-start border-4 border-primary bg-light rounded-end">
                        <p class="mb-0 small text-muted"><strong>Note:</strong> Please refer to the official CCS Bulletin board for supplemental documents if mentioned above.</p>
                    </div>
                </div>
            `;
        }
    </script>
</body>
</html>