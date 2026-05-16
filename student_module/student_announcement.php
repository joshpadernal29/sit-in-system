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
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Announcements | SIT-IN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body{
            min-height:100vh;
            overflow-x:hidden;
            font-family:'Inter',sans-serif;
            margin:0;
        }
        
        .inbox-wrapper{
            height:calc(100vh - 3rem);
            overflow:hidden;
        }

        #detailPane{
            display:flex;
        }
        
        /* Sidebar - Slate background for contrast against body cards */
        .inbox-list { 
            width: 380px; 
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
        .list-item:hover { background-color: var(--bs-secondary-bg-subtle); }
        .list-item.active { 
            background-color: var(--bs-body-bg) !important; 
            border-color: var(--bs-border-color-translucent) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            transform: translateX(4px);
        }
        
        /* Highlight styling for the absolute newest post */
        .latest-announcement-highlight {
            background-color: rgba(var(--bs-primary-rgb), 0.05);
            border: 1px solid rgba(var(--bs-primary-rgb), 0.15);
        }
        
        .avatar-box { width: 48px; height: 48px; flex-shrink: 0; }

        /* Message Content Depth */
        .msg-bubble {
            padding: 35px;
            border-radius: 20px;
            line-height: 1.8;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .inbox-detail{
            flex:1;
            overflow-y:auto;
            position:relative;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: var(--bs-border-color); border-radius: 10px; }

        .main-content{
            margin-left:260px;
            height:100vh;
            transition:.3s ease;
        }

        .sidebar.collapsed ~ .main-content{
            margin-left:80px;
        }

        @media (max-width: 991px) {
            .inbox-list { width: 100%; }
            #detailPane.active { display: flex !important; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 1050; }
        }
    </style>
</head>
<body class="bg-body text-body">

    <?php include("../includes/student_sidebar.php"); ?>

    <div class="main-content">
        <div class="inbox-wrapper d-flex">
            <aside class="inbox-list d-flex flex-column bg-body-tertiary border-end border-light-subtle">
                <div class="p-3 sticky-top bg-body border-bottom border-light-subtle shadow-sm">
                    <form action="" method="GET" class="row g-2">
                        <div class="col-12">
                            <div class="input-group input-group-sm border border-secondary-subtle rounded-pill overflow-hidden bg-body-tertiary">
                                <span class="input-group-text border-0 bg-transparent ps-3"><i class="bi bi-calendar-event text-secondary"></i></span>
                                <input type="date" name="date" class="form-control border-0 bg-transparent text-body shadow-none" value="<?= htmlspecialchars($date); ?>">
                            </div>
                        </div>
                        <div class="col-8">
                            <select name="priority" class="form-select form-select-sm rounded-pill border border-secondary-subtle bg-body-tertiary px-3 shadow-none text-body">
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
                    <?php 
                    foreach($posts as $index => $post): 
                        $isLatest = ($index === 0 && empty($date) && empty($priority));
                    ?>
                    <div class="list-item p-3 d-flex align-items-center <?= $isLatest ? 'latest-announcement-highlight' : ''; ?>" 
                        id="item-<?= $post['id']; ?>"
                        onclick="loadAnnouncement(this, <?= htmlspecialchars(json_encode($post)); ?>)">
                        
                        <div class="avatar-box rounded-circle bg-body d-flex align-items-center justify-content-center border border-light-subtle shadow-sm">
                            <img src="../assets/ccsmainlogo2.png" style="width: 25px;">
                        </div>

                        <div class="ms-3 overflow-hidden w-100">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 text-truncate fw-bold text-body" style="font-size: 0.9rem;">
                                    <?= htmlspecialchars($post['title']); ?>
                                </h6>
                                <?php if ($isLatest): ?>
                                    <!-- Added dynamic ID here so JS can target it -->
                                    <span id="badge-<?= $post['id']; ?>" class="badge rounded-pill bg-primary px-2 py-1 shadow-sm" style="font-size: 0.65rem; letter-spacing: 0.5px;">NEW</span>
                                <?php else: ?>
                                    <span id="dot-<?= $post['id']; ?>" class="badge rounded-circle p-1 bg-primary d-none" style="height: 8px; width: 8px;"> </span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="mb-0 text-secondary small text-truncate" style="max-width: 150px;"><?= htmlspecialchars($post['message']); ?></p>
                                <small class="text-secondary fw-bold" style="font-size: 0.7rem;"><?= date('M d', strtotime($post['date_posted'])); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="mx-4 border-bottom border-light-subtle opacity-50"></div>
                    <?php endforeach; ?>
                </div>
            </aside>

            <article class="inbox-detail d-flex flex-column bg-body" id="detailPane">
                <div class="m-auto text-center opacity-75">
                    <i class="bi bi-chat-square-quote display-1 text-secondary mb-3"></i>
                    <h4 class="fw-bold text-body">No Announcement Selected</h4>
                    <p class="text-secondary">Click on a post from the sidebar to view details.</p>
                </div>
            </article>
        </div>
    </div>

    <script>
        // Track announcements read during this active page session
        const readSessionAnnouncements = new Set();

        function loadAnnouncement(element, data) {
            document.querySelectorAll('.list-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');

            // 1. Hide the regular notification dot if it exists
            const dot = document.getElementById('dot-' + data.id);
            if(dot) dot.classList.add('d-none');

            // 2. Hide the "NEW" badge immediately upon clicking
            const badge = document.getElementById('badge-' + data.id);
            if(badge) {
                badge.classList.add('d-none');
                
                // Only trigger a badge decrement if this specific new post hasn't been read yet
                if (!readSessionAnnouncements.has(data.id)) {
                    readSessionAnnouncements.add(data.id);
                    
                    // Sync with localStorage
                    const currentLastRead = parseInt(localStorage.getItem('lastReadId')) || 0;
                    if (data.id > currentLastRead) {
                        localStorage.setItem('lastReadId', data.id);
                        // Dispatch event to instantly notify the sidebar to recalculate
                        window.dispatchEvent(new Event('storage'));
                    }
                }
            }
            
            // 3. Remove the background highlight styling
            element.classList.remove('latest-announcement-highlight');
            
            const pane = document.getElementById('detailPane');
            const colors = { urgent: 'danger', academic: 'primary', general: 'success' };
            const badgeColor = colors[data.priority] || 'secondary';

            pane.innerHTML = `
                <div class="p-4 border-bottom border-light-subtle bg-body sticky-top d-flex align-items-center shadow-sm">
                    <div class="rounded-circle bg-body-tertiary border border-light-subtle p-2 me-3">
                        <img src="../assets/ccsmainlogo2.png" style="width:30px;">
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-body">CCS Admin Official</h6>
                        <small class="text-secondary"><i class="bi bi-clock me-1"></i> Posted on ${new Date(data.date_posted).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary d-md-none ms-auto" onclick="closeDetails()">Back</button>
                </div>
                <div class="p-5 mx-auto w-100" style="max-width: 850px;">
                    <span class="badge bg-${badgeColor} rounded-pill px-3 py-2 mb-3 text-uppercase shadow-sm" style="font-size:0.7rem;">${data.priority}</span>
                    <h1 class="fw-bold text-body display-6 mb-4">${data.title}</h1>
                    
                    <div class="msg-bubble bg-body-tertiary border border-light-subtle text-body animate-fade-in">
                        ${data.message.replace(/\n/g, '<br>')}
                    </div>

                    <div class="mt-4 p-3 border-start border-4 border-primary bg-body-tertiary rounded-end border-light-subtle">
                        <p class="mb-0 small text-secondary"><strong>Note:</strong> Please refer to the official CCS Bulletin board for supplemental documents if mentioned above.</p>
                    </div>
                </div>
            `;
            pane.classList.add('active');
        }

        function closeDetails() {
            document.getElementById('detailPane').classList.remove('active');
            document.querySelectorAll('.list-item').forEach(el => el.classList.remove('active'));
        }

        // Automatically clear out numbers when checking the master list feed if a high anchor ID exists
        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.querySelector('.flex-grow-1.py-2');
            if (wrapper) {
                const firstItem = wrapper.querySelector('.list-item');
                if (firstItem) {
                    // Extract the first item's true ID string structure (e.g., "item-42" -> 42)
                    const absoluteLatestId = parseInt(firstItem.id.replace('item-', ''));
                    if (!isNaN(absoluteLatestId)) {
                        // If the user loads this page, assume they have caught up with everything up to this item
                        localStorage.setItem('lastReadId', absoluteLatestId);
                        window.dispatchEvent(new Event('storage'));
                    }
                }
            }
        });
    </script>
</body>
</html>