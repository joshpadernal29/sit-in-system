<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../config/database.php');
include('../action/sit_in.php');

$filter_cat = $_GET['filter_cat'] ?? '';
$feedback_list = getFeedbacks($conn, $filter_cat); 
$total_rows = mysqli_num_rows($feedback_list);

$all_ids = [];
if($total_rows > 0) {
    while($row = mysqli_fetch_assoc($feedback_list)) {
        $all_ids[] = $row['id'];
    }
    mysqli_data_seek($feedback_list, 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback Inbox | UC Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
    /* ================= THEME VARIABLES (Uniform) ================= */
    :root {
        --bg-body: #f8fafc;
        --bg-card: #ffffff;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #edf2f7;
        --input-bg: #f1f5f9;
        --sidebar-width: 260px;
        --sidebar-collapsed: 80px;
    }

    [data-theme="dark"] {
        --bg-body: #121212 !important;
        --bg-card: #1e1e1e !important;
        --text-main: #e0e0e0 !important;
        --text-muted: #a0a0a0 !important;
        --border-color: #333333 !important;
        --input-bg: #2d2d2d !important;
    }

    body {
        margin: 0;
        background: var(--bg-body);
        font-family: 'Inter', sans-serif;
        color: var(--text-main);
        transition: background 0.3s;
    }

    /* ================= INBOX LAYOUT ================= */
    .inbox-wrapper {
        margin-left: var(--sidebar-width);
        display: flex;
        height: 100vh;
        overflow: hidden;
        transition: margin-left .3s ease;
    }

    .sidebar.collapsed ~ .inbox-wrapper {
        margin-left: var(--sidebar-collapsed);
    }

    /* ================= LEFT LIST (Inbox) ================= */
    .inbox-list {
        width: 380px;
        border-right: 1px solid var(--border-color);
        overflow-y: auto;
        background: var(--bg-card);
    }

    /* Specific fix for the top filter area */
    .inbox-list .sticky-header {
        position: sticky;
        top: 0;
        z-index: 2;
        background: var(--bg-card) !important;
        border-bottom: 1px solid var(--border-color);
        padding: 1rem;
    }

    /* ================= INPUT & SELECT FIXES ================= */
    /* This forces the dropdown and inputs to stay dark */
    .form-select, .form-control {
        background-color: var(--input-bg) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-main) !important;
    }

    /* Fix for when you click inside the input */
    .form-select:focus, .form-control:focus {
        background-color: var(--input-bg) !important;
        color: var(--text-main) !important;
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Fix for the dropdown arrow and text color in dark mode */
    [data-theme="dark"] .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23a0a0a0' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
    }

    /* ================= DETAIL PANE ================= */
    .inbox-detail {
        flex: 1;
        overflow-y: auto;
        padding: 50px;
        background: var(--bg-body);
    }

    /* ================= LIST ITEMS ================= */
    .list-item {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        transition: .2s;
        position: relative;
        background: var(--bg-card);
    }

    .list-item:hover {
        background: var(--input-bg);
    }

    .list-item.active {
        background: var(--bg-body) !important;
    }

    .list-item.active::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #0d6efd;
    }

    .unread-feedback-highlight {
        background-color: rgba(13, 110, 253, 0.05) !important;
    }

    /* ================= UI ELEMENTS ================= */
    .avatar-lg {
        width: 58px; height: 58px; border-radius: 14px;
        background: #2563eb; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1.2rem;
    }

    .msg-container {
        background: var(--bg-card) !important;
        border-radius: 1.2rem;
        padding: 30px;
        border: 1px solid var(--border-color) !important;
        line-height: 1.7;
        font-size: 1.05rem;
        color: var(--text-main) !important;
    }

    .cat-pill {
        font-size: .65rem; font-weight: 800; text-transform: uppercase;
        padding: 4px 10px; border-radius: 6px;
    }

    .empty-state {
        height: 100%;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        color: var(--text-muted);
    }

    /* Scrollbar Styling for Dark Mode */
    [data-theme="dark"] ::-webkit-scrollbar { width: 8px; }
    [data-theme="dark"] ::-webkit-scrollbar-track { background: #121212; }
    [data-theme="dark"] ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }

    @media(max-width: 991px) {
        .inbox-wrapper { margin-left: 0 !important; flex-direction: column; }
        .inbox-list { width: 100%; height: 40vh; }
    }
</style>
</head>

<body>

<?php include("../includes/admin_sidebar.php"); ?>

<!-- Theme Persistence Script -->
<script>
    (function() {
        const savedTheme = localStorage.getItem("theme");
        if (savedTheme === "dark") {
            document.body.setAttribute("data-theme", "dark");
        }
    })();
</script>

<div class="inbox-wrapper">

    <!-- LEFT LIST -->
    <aside class="inbox-list">
        <div class="sticky-header">
            <form method="GET" class="row g-2">
                <div class="col-8">
                    <select name="filter_cat" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <option value="Hardware" <?= ($filter_cat == 'Hardware') ? 'selected' : ''; ?>>Hardware</option>
                        <option value="Software" <?= ($filter_cat == 'Software') ? 'selected' : ''; ?>>Software</option>
                        <option value="Environment" <?= ($filter_cat == 'Environment') ? 'selected' : ''; ?>>Environment</option>
                    </select>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>

        <div id="feedbackItemsContainer">
            <?php if($total_rows > 0): ?>
                <?php while($row = mysqli_fetch_assoc($feedback_list)): ?>
                    <div class="list-item" 
                         id="feedback-item-<?= $row['id']; ?>"
                         onclick="showDetail(this, <?php echo htmlspecialchars(json_encode($row)); ?>)">

                        <div class="d-flex justify-content-between mb-1 align-items-center">
                            <span class="cat-pill bg-primary-subtle text-primary">
                                <?php echo $row['category']; ?>
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <span id="badge-admin-<?= $row['id']; ?>" class="badge rounded-pill bg-danger d-none" style="font-size: 0.6rem;">NEW</span>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    <?php echo date('M d', strtotime($row['submitted_at'])); ?>
                                </small>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-1" style="color: var(--text-main);">
                            <?php echo htmlspecialchars($row['fullname']); ?>
                        </h6>

                        <p class="text-muted small mb-0 text-truncate">
                            <?php echo htmlspecialchars($row['message']); ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="p-4 text-center text-muted small">No feedback found.</div>
            <?php endif; ?>
        </div>
    </aside>

    <!-- RIGHT DETAIL -->
    <article class="inbox-detail" id="detailPane">
        <div class="empty-state">
            <i class="bi bi-envelope-open" style="font-size:5rem; opacity: 0.2;"></i>
            <h5 class="mt-3">Select a message to read</h5>
        </div>
    </article>

</div>

<script>
let readFeedbacks = JSON.parse(localStorage.getItem('readFeedbackIds')) || [];

function initBadges() {
    const systemIds = <?php echo json_encode($all_ids); ?> || [];
    
    systemIds.forEach(id => {
        if (!readFeedbacks.includes(id)) {
            const badge = document.getElementById('badge-admin-' + id);
            const cardElement = document.getElementById('feedback-item-' + id);
            
            if (badge) badge.classList.remove('d-none');
            if (cardElement) cardElement.classList.add('unread-feedback-highlight');
        }
    });
}

function showDetail(element, data) {
    document.querySelectorAll('.list-item').forEach(el => el.classList.remove('active'));
    element.classList.add('active');

    const badge = document.getElementById('badge-admin-' + data.id);
    if(badge) badge.classList.add('d-none');
    
    element.classList.remove('unread-feedback-highlight');

    if (!readFeedbacks.includes(data.id)) {
        readFeedbacks.push(data.id);
        localStorage.setItem('readFeedbackIds', JSON.stringify(readFeedbacks));
    }

    const date = new Date(data.submitted_at);
    const formattedDate = date.toLocaleDateString('en-US', {
        month:'long', day:'numeric', year:'numeric',
        hour:'2-digit', minute:'2-digit', hour12:true
    });
    
    window.dispatchEvent(new Event('storage'));

    document.getElementById('detailPane').innerHTML = `
        <div class="d-flex align-items-center mb-4">
            <div class="avatar-lg me-3">${data.fullname.charAt(0).toUpperCase()}</div>
            <div>
                <h3 class="fw-bold mb-0" style="color: var(--text-main);">${data.fullname}</h3>
                <small class="text-muted">ID: ${data.student_id} • ${formattedDate}</small>
            </div>
        </div>

        <div class="mb-4">
            <span class="badge bg-primary px-3 py-2">${data.category}</span>
        </div>

        <div class="msg-container shadow-sm">
            ${data.message.replace(/\n/g,'<br>')}
        </div>
    `;
}

document.addEventListener('DOMContentLoaded', initBadges);
</script>

</body>
</html>