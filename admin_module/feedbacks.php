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
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
body{
    margin:0;
    background:#f8fafc;
    font-family:'Inter', sans-serif;
}

/* ================= SIDEBAR COMPATIBILITY ================= */
:root{
    --sidebar-width: 260px;
    --sidebar-collapsed: 80px;
}

/* SHIFT CONTENT TO FIT SIDEBAR */
.inbox-wrapper{
    margin-left: var(--sidebar-width);
    display:flex;
    height: 100vh;
    overflow:hidden;
    border-top:1px solid #edf2f7;
    transition: margin-left .3s ease;
}

/* when sidebar collapses */
.sidebar.collapsed ~ .inbox-wrapper{
    margin-left: var(--sidebar-collapsed);
}

/* ================= LEFT LIST ================= */
.inbox-list{
    width:380px;
    border-right:1px solid #edf2f7;
    overflow-y:auto;
    background:#ffffff;
}

/* sticky filter */
.inbox-list .p-3{
    position: sticky;
    top: 0;
    z-index: 2;
    background: #fff;
    border-bottom:1px solid #edf2f7;
}

/* ================= DETAIL ================= */
.inbox-detail{
    flex:1;
    overflow-y:auto;
    padding:50px;
    background:#f8fafc;
}

/* ================= ITEM CARD ================= */
.list-item{
    padding:18px 20px;
    border-bottom:1px solid #f1f4f8;
    cursor:pointer;
    transition:.2s;
    position:relative;
}

.list-item:hover{
    background:#f1f5f9;
}

.list-item.active{
    background:#fff;
    box-shadow:0 4px 14px rgba(0,0,0,0.06);
}

.list-item.active::before{
    content:"";
    position:absolute;
    left:0;
    top:0;
    bottom:0;
    width:4px;
    background:#0d6efd;
}

/* ================= AVATAR ================= */
.avatar-lg{
    width:58px;
    height:58px;
    border-radius:14px;
    background:#111827;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    font-size:1.2rem;
}

/* ================= MESSAGE ================= */
.msg-container{
    background:#ffffff;
    border-radius:1.2rem;
    padding:30px;
    border:1px solid #e5e7eb;
    line-height:1.7;
    font-size:1.05rem;
}

/* ================= CATEGORY ================= */
.cat-pill{
    font-size:.65rem;
    font-weight:800;
    text-transform:uppercase;
    padding:4px 10px;
    border-radius:6px;
}

/* ================= EMPTY STATE ================= */
.empty-state{
    height:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    color:#94a3b8;
}
</style>
</head>

<body>

<?php include("../includes/admin_sidebar.php"); ?>

<div class="inbox-wrapper">

    <!-- LEFT LIST -->
    <aside class="inbox-list">

        <div class="p-3">
            <form method="GET" class="row g-2">
                <div class="col-8">
                    <select name="filter_cat" class="form-select form-select-sm bg-light border-0 rounded-3">
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
                <div class="list-item"
                     onclick="showDetail(this, <?php echo htmlspecialchars(json_encode($row)); ?>)">

                    <div class="d-flex justify-content-between mb-1">
                        <span class="cat-pill bg-primary-subtle text-primary">
                            <?php echo $row['category']; ?>
                        </span>
                        <small class="text-muted">
                            <?php echo date('M d, Y | h:i A', strtotime($row['submitted_at'])); ?>
                        </small>
                    </div>

                    <h6 class="fw-bold mb-1">
                        <?php echo htmlspecialchars($row['fullname']); ?>
                    </h6>

                    <p class="text-muted small mb-0 text-truncate">
                        <?php echo htmlspecialchars($row['message']); ?>
                    </p>

                </div>
            <?php endwhile; ?>
        <?php endif; ?>

    </aside>

    <!-- RIGHT DETAIL -->
    <article class="inbox-detail" id="detailPane">
        <div class="empty-state">
            <i class="bi bi-layout-sidebar-inset" style="font-size:5rem;"></i>
            <h5 class="mt-3">Select a Feedback to view</h5>
        </div>
    </article>

</div>

<script>
function showDetail(element, data) {
    document.querySelectorAll('.list-item').forEach(el => el.classList.remove('active'));
    element.classList.add('active');

    const date = new Date(data.submitted_at);
    const formattedDate = date.toLocaleDateString('en-US', {
        month:'long', day:'numeric', year:'numeric',
        hour:'2-digit', minute:'2-digit', hour12:true
    });

    document.getElementById('detailPane').innerHTML = `
        <div class="d-flex align-items-center mb-4">
            <div class="avatar-lg me-3">${data.fullname.charAt(0).toUpperCase()}</div>
            <div>
                <h3 class="fw-bold mb-0">${data.fullname}</h3>
                <small class="text-muted">ID: ${data.student_id} • ${formattedDate}</small>
            </div>
        </div>

        <div class="mb-3">
            <span class="badge bg-dark">${data.category}</span>
        </div>

        <div class="msg-container">
            ${data.message.replace(/\n/g,'<br>')}
        </div>
    `;
}
</script>

</body>
</html>