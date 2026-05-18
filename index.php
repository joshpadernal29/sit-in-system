<?php
include("config/database.php");
include("action/testimonials.php");

// get featured testimonials
$testimonials = getFeaturedTestimonials($conn,3); // limit to 3 testimnials
$featured_testimonials = [];
while ($row = mysqli_fetch_assoc($testimonials)) {
    $featured_testimonials[] = $row;
}

// 2. FETCH LIVE LEADERBOARD DATA (Crucial fix to eliminate the error)
$lb_sql = "SELECT CONCAT(firstname, ' ', lastname) AS name, student_id, accumulated_points 
           FROM students 
           WHERE accumulated_points > 0 
           ORDER BY accumulated_points DESC 
           LIMIT 10";

$top_players = []; // Keeps it safe even if database is empty

if (isset($conn)) {
    $lb_result = mysqli_query($conn, $lb_sql);
    if ($lb_result) {
        while ($row = mysqli_fetch_assoc($lb_result)) {
            $top_players[] = $row;
        }
    }
}

/**
 * Assigns a hilarious tech-meme rank title, styling, and proper UI icons
 * based on the student's lifetime accumulated performance points.
 */
function getGamingRank($points) {
    if ($points >= 150) {
        return [
            'title' => 'Ascended Caffeine Entity', 
            'icon'  => 'bi-cup-hot-fill',
            'class' => 'bg-dark text-info border-info fw-bold'
        ];
    } elseif ($points >= 100) {
        return [
            'title' => 'Ultimate Vibe Coder', 
            'icon'  => 'bi-headset',
            'class' => 'bg-danger text-white border-danger-subtle fw-semibold'
        ];
    } elseif ($points >= 50) {
        return [
            'title' => 'Dual-Monitor Overlord', 
            'icon'  => 'bi-display-fill',
            'class' => 'bg-success text-white border-success-subtle'
        ];
    } elseif ($points >= 25) {
        return [
            'title' => 'Git Commit Spammer', 
            'icon'  => 'bi-git',
            'class' => 'bg-info-subtle text-info border-info-subtle'
        ];
    } elseif ($points >= 10) {
        return [
            'title' => 'Localhost Specialist', 
            'icon'  => 'bi-house-gear-fill',
            'class' => 'bg-warning text-dark border-warning'
        ];
    } else {
        return [
            'title' => 'Semicolon Searching Cadet', 
            'icon'  => 'bi-search',
            'class' => 'bg-danger-subtle text-danger border-danger'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Sit-In Monitoring System</title>
    <style>
        html {
            scroll-behavior: smooth;
        }
        /* Custom timeline line for the 'How it works' workflow */
        .workflow-steps .col-lg-4:not(:last-child) {
            position: relative;
        }
        @media (min-width: 992px) {
            .workflow-steps .col-lg-4:not(:last-child)::after {
                content: '';
                position: absolute;
                top: 2.5rem;
                right: -15%;
                width: 30%;
                height: 2px;
                background-color: var(--bs-border-color-translucent);
                z-index: 1;
            }
        }

        html {
        scroll-behavior: smooth;
        /* Tells the browser to stop scrolling slightly before the element to clear your sticky navbar space */
        scroll-padding-top: 90px; 
        }
    </style>
</head>

<!-- Add data-bs-spy and data-bs-target to activate the scrolling underline tracker -->
<body class="bg-body text-body" data-bs-spy="scroll" data-bs-target="#mainNav" data-bs-offset="100">

    <!-- Navbar -->
    <?php include("includes/header.html") ?>
    <!-- End Navbar -->

    <!-- 1. HOME SECTION -->
    <header id="home" class="py-5 my-5">
        <div class="container px-5 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">
                    <span class="badge bg-primary-subtle text-primary mb-4 px-3 py-2 rounded-pill fw-semibold border border-primary-subtle">
                        <i class="bi bi-cpu me-1"></i> Sit in Monitoring system
                    </span>
                   <h1 class="display-3 text-body tracking-tight mb-3" style="letter-spacing: -1px; font-weight: 800;">
                        Smarter monitoring for <span class="text-primary">laboratory sit-ins</span>
                    </h1>

                    <p class="lead text-body-secondary fs-4 mx-auto mb-5 mt-4" style="max-width: 700px;">
                        A modern sit-in monitoring system built to simplify laboratory management through real-time session tracking, automated reservations, terminal monitoring, and seamless student check-ins — all within one centralized platform.
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 mb-5">
                        <a class="btn btn-primary btn-lg px-5 py-3 fw-semibold shadow-sm fs-6" href="login.php">
                            Get Started Now <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a class="btn btn-link link-secondary text-decoration-none fw-medium" href="#about">
                            Learn how it works <i class="bi bi-chevron-down ms-1"></i>
                        </a>
                    </div>
                    <div class="row g-3 justify-content-center mt-5 pt-3">
                        <div class="col-6 col-md-3">
                            <div class="py-3 px-2 bg-body-tertiary border border-light-subtle rounded-4">
                                <span class="d-block fs-3 fw-bold text-primary"><i class="bi bi-lightning-charge"></i></span>
                                <small class="text-body-secondary fw-medium">Instant Logs</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="py-3 px-2 bg-body-tertiary border border-light-subtle rounded-4">
                                <span class="d-block fs-3 fw-bold text-primary"><i class="bi bi-pc-display"></i></span>
                                <small class="text-body-secondary fw-medium">Live Matrix</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="py-3 px-2 bg-body-tertiary border border-light-subtle rounded-4">
                                <span class="d-block fs-3 fw-bold text-primary"><i class="bi bi-shield-check"></i></span>
                                <small class="text-body-secondary fw-medium">Secure Portal</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. ABOUT / HOW IT WORKS SECTION -->
    <section id="about" class="py-5 bg-body-tertiary border-top border-bottom border-light-subtle">
        <div class="container px-5 my-5">
            <div class="text-center mb-5 pb-3">
                <span class="text-primary fw-bold text-uppercase small tracking-wider">System Pipeline</span>
                <h2 class="display-5 fw-bold mt-1" style="letter-spacing: -1px;">How the System Works</h2>
                <p class="text-body-secondary mx-auto" style="max-width: 550px;">A transparent overview of your laboratory journey from registration to final session feedback.</p>
            </div>

            <!-- Stage 1: Registration, Tracking, & Reservations -->
            <div class="row g-5 workflow-steps justify-content-center text-center text-lg-start mb-5">
                <div class="col-md-6 col-lg-4">
                    <div class="z-3 position-relative">
                        <div class="bg-primary text-white rounded-4 shadow-sm mb-4 d-inline-flex align-items-center justify-content-center" style="width: 4.5rem; height: 4.5rem;">
                            <i class="bi bi-person-plus-fill fs-3"></i>
                        </div>
                        <h4 class="fw-bold h5">1. Quick Registration</h4>
                        <p class="text-body-secondary small pe-lg-3">Create your account with your student ID credentials to open up access to all available laboratory networks instantly.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="z-3 position-relative">
                        <div class="bg-primary text-white rounded-4 shadow-sm mb-4 d-inline-flex align-items-center justify-content-center" style="width: 4.5rem; height: 4.5rem;">
                            <i class="bi bi-pc-display-horizontal fs-3"></i>
                        </div>
                        <h4 class="fw-bold h5">2. Reserve a Terminal</h4>
                        <p class="text-body-secondary small pe-lg-3">Browse open workstations remotely via our real-time grid mapping, and secure a specific PC before arriving.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="z-3 position-relative">
                        <div class="bg-primary text-white rounded-4 shadow-sm mb-4 d-inline-flex align-items-center justify-content-center" style="width: 4.5rem; height: 4.5rem;">
                            <i class="bi bi-hourglass-split fs-3"></i>
                        </div>
                        <h4 class="fw-bold h5">3. Live Sit-In Tracking</h4>
                        <p class="text-body-secondary small">Authenticate at your node to track your hours automatically. Your dashboard updates your balance dynamically.</p>
                    </div>
                </div>
            </div>

            <!-- Stage 2: Feedback Loop Callouts -->
            <div class="row g-4 mt-4 justify-content-center">
                <div class="col-md-6">
                    <div class="p-4 bg-body border border-light-subtle rounded-4 d-flex gap-3 align-items-start shadow-sm">
                        <div class="text-primary bg-primary-subtle p-2 px-3 rounded-3"><i class="bi bi-chat-left-heart-fill fs-4"></i></div>
                        <div>
                            <h5 class="fw-bold h6 mb-1">Add Sit-In Feedback</h5>
                            <p class="text-body-secondary small m-0">Encountered an error or system defect? Log structural feedback instantly on your session logout form for tech review.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 bg-body border border-light-subtle rounded-4 d-flex gap-3 align-items-start shadow-sm">
                        <div class="text-primary bg-primary-subtle p-2 px-3 rounded-3"><i class="bi bi-chat-square-quote-fill fs-4"></i></div>
                        <div>
                            <h5 class="fw-bold h6 mb-1">Post Public Testimonials</h5>
                            <p class="text-body-secondary small m-0">Share your overall platform user experiences to help our engineering team continuously upgrade the laboratory ecosystem.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="leaderboard" class="py-5 bg-body">
        <div class="container px-5 my-5">
            
            <!-- Section Header Typography -->
            <div class="text-center mb-5 pb-3">
                <span class="text-primary fw-bold text-uppercase small tracking-wider" style="letter-spacing: 1px;">Top Performers</span>
                <h2 class="display-5 fw-bold mt-1" style="letter-spacing: -1px;">Lab Hours Leaderboard</h2>
                <p class="text-body-secondary mx-auto" style="max-width: 550px;">Celebrating the students dedicated to maximizing their hands-on system terminal experience and climbing the meme stack.</p>
            </div>

            <div class="row g-4 align-items-stretch">
                
                <!-- Left Side Column: Top 3 Podium Highlights Cards -->
                <div class="col-xl-6 d-flex flex-column justify-content-center gap-3">
                    <?php 
                    $podium_count = min(3, count($top_players));
                    if ($podium_count > 0):
                        for ($i = 0; $i < $podium_count; $i++):
                            $player = $top_players[$i];
                            $rank = $i + 1;
                            $rankInfo = getGamingRank($player['accumulated_points']);
                            
                            // Premium Gradient Badges for Podium Medals
                            $gradients = [
                                1 => "linear-gradient(135deg, #ffd700, #ffa500)", // Gold
                                2 => "linear-gradient(135deg, #c0c0c0, #808080)", // Silver
                                3 => "linear-gradient(135deg, #cd7f32, #8b4513)"  // Bronze
                            ];
                    ?>
                            <!-- Dynamic Podium Card Component -->
                            <div class="p-4 bg-body-tertiary border border-light-subtle rounded-4 d-flex align-items-center shadow-sm" style="transition: transform 0.2s ease-in-out;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4 me-4 shadow-sm" 
                                    style="width: 3.5rem; height: 3.5rem; flex-shrink: 0; background: <?= $gradients[$rank]; ?>; color: #fff;">
                                    <?= $rank ?>
                                </div>
                                <div class="me-auto overflow-hidden">
                                    <h5 class="fw-bold mb-1 text-truncate h6 text-body"><?= htmlspecialchars($player['name']); ?></h5>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="text-body-secondary small text-truncate font-monospace"><?= htmlspecialchars($player['student_id']); ?></span>
                                        <span class="badge border <?= $rankInfo['class']; ?> rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.65rem; padding: 0.25rem 0.5rem;">
                                            <i class="bi <?= $rankInfo['icon']; ?>"></i>
                                            <?= $rankInfo['title']; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end ps-3">
                                    <span class="d-block fw-extrabold text-primary fs-4 mb-0" style="font-weight: 800;"><?= number_format($player['accumulated_points'], 1); ?></span>
                                    <small class="text-body-secondary fw-semibold text-uppercase" style="font-size: 0.65rem;">Total Points</small>
                                </div>
                            </div>
                    <?php 
                        endfor;
                    else: 
                    ?>
                        <!-- Fallback Empty State if Data is Null -->
                        <div class="p-5 text-center text-muted bg-body-tertiary border border-light-subtle rounded-4 shadow-sm">
                            <i class="bi bi-shield-slash fs-2 text-secondary opacity-50 mb-2"></i>
                            <p class="mb-0">No active students on the podium yet. Log into a terminal to get started!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Side Column: Runner-Up Honor Roll Queue Matrix Table (Ranks 4-5) -->
                <div class="col-xl-6">
                    <div class="card bg-body-tertiary border-light-subtle shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="card-header border-bottom border-light-subtle p-4 bg-transparent">
                            <h5 class="fw-bold mb-0 h6 text-body"><i class="bi bi-list-ol me-2 text-primary"></i>Honor Roll Standings</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-start">
                                <thead>
                                    <tr>
                                        <th class="text-secondary small text-uppercase py-3 bg-transparent border-bottom border-light-subtle ps-4" style="width: 15%;">Rank</th>
                                        <th class="text-secondary small text-uppercase py-3 bg-transparent border-bottom border-light-subtle">Student Details</th>
                                        <th class="text-secondary small text-uppercase py-3 bg-transparent border-bottom border-light-subtle pe-4 text-end" style="width: 25%;">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (count($top_players) > 3):
                                        for ($i = 3; $i < count($top_players); $i++):
                                            $player = $top_players[$i];
                                            $rank = $i + 1;
                                            $rankInfo = getGamingRank($player['accumulated_points']);
                                    ?>
                                            <!-- Dynamic Table Standings Row -->
                                            <tr>
                                                <td class="ps-4 border-light-subtle fw-bold text-body-secondary">#<?= $rank ?></td>
                                                <td class="border-light-subtle">
                                                    <div class="fw-bold text-body small"><?= htmlspecialchars($player['name']); ?></div>
                                                    <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                                        <small class="text-secondary font-monospace" style="font-size:0.72rem;"><?= htmlspecialchars($player['student_id']); ?></small>
                                                        <span class="badge border <?= $rankInfo['class']; ?> rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">
                                                            <i class="bi <?= $rankInfo['icon']; ?>"></i>
                                                            <?= $rankInfo['title']; ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="pe-4 text-end border-light-subtle">
                                                    <span class="badge bg-body border border-light-subtle text-primary rounded-pill px-3 py-1.5 font-monospace fw-bold">
                                                        <?= number_format($player['accumulated_points'], 1); ?> pts
                                                    </span>
                                                </td>
                                            </tr>
                                    <?php 
                                        endfor;
                                    else:
                                    ?>
                                        <!-- Fallback row if there are fewer than 4 students ranked -->
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-5 border-0">
                                                <small class="d-block text-secondary opacity-75">No secondary runners-up data available yet.</small>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 3. DYNAMIC CLEAN TESTIMONIALS SECTION -->
    <section id="community" class="py-5 bg-body-tertiary border-top border-light-subtle">
        <div class="container px-5 my-5">
            <div class="row justify-content-between align-items-center mb-5">
                <div class="col-md-6 text-center text-md-start">
                    <span class="text-primary fw-bold text-uppercase small tracking-wider">Community Voiced</span>
                    <h2 class="display-5 fw-bold mt-1 mb-0" style="letter-spacing: -1px;">Student Perspectives</h2>
                </div>
                <div class="col-md-5 text-center text-md-start mt-2 mt-md-0">
                    <p class="text-body-secondary m-0">Real opinions shared by  students utilizing the sit-in monitoring system daily.</p>
                </div>
            </div>

            <!-- Elegant Typography Centered Grid -->
            <div class="row g-4">
                <?php if (empty($featured_testimonials)): ?>
                    <!-- Fallback view if no testimonials are currently flagged as featured -->
                    <div class="col-12 text-center py-5">
                        <p class="text-body-secondary italic mb-0">No featured testimonials found at this moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($featured_testimonials as $item): ?>
                        <div class="col-md-4">
                            <div class="p-4 bg-body border border-light-subtle rounded-4 h-100 d-flex flex-column justify-content-between shadow-sm">
                                <div>
                                    <!-- Dynamic Star Rating Render engine -->
                                    <div class="text-warning small mb-3">
                                        <?php 
                                        $rating = floatval($item['rating']);
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating) {
                                                echo '<i class="bi bi-star-fill"></i>';
                                            } elseif ($i - 0.5 <= $rating) {
                                                echo '<i class="bi bi-star-half"></i>';
                                            } else {
                                                echo '<i class="bi bi-star"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <p class="text-body small lh-base mb-4">
                                        "<?= htmlspecialchars($item['content']) ?>"
                                    </p>
                                </div>
                                <div class="d-flex align-items-center pt-3 border-top border-light-subtle">
                                    <?php
                                    $fullname = !empty($item['fullname']) ? trim($item['fullname']) : 'Anonymous';                    
                                    $defaultIconPath = "assets/default_profile.jpg";
                                    ?>
                                    <div class="me-3 flex-shrink-0" style="width: 32px; height: 32px;">
                                        <img src="<?= $defaultIconPath ?>" 
                                            alt="<?= htmlspecialchars($fullname) ?>'s profile picture" 
                                            class="rounded-circle border border-light-subtle w-100 h-100 object-fit-cover">
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mb-0 fw-bold small text-truncate"><?= htmlspecialchars($fullname) ?></h6>
                                        <span class="text-body-secondary d-block text-truncate" style="font-size: 0.7rem;"><?= htmlspecialchars($item['course_year']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-body-tertiary py-4 border-top border-light-subtle">
        <div class="container px-5 text-center">
            <div class="small text-body-secondary">Copyright &copy; Josh Efraim C. Padernal 2026</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Theme Logic Management Engine -->
    <script>
        const htmlElement = document.documentElement;
        const themeToggleBtn = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');

        // Check local storage or system preference on load
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        const initialTheme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
        setTheme(initialTheme);

        // Click Event Listener
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });

        // Function to handle layout changes dynamically
        function setTheme(theme) {
            htmlElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);

            // Dynamically change the icon graphic interface
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill';
                themeToggleBtn.title = 'Switch to Light Mode';
            } else {
                themeIcon.className = 'bi bi-moon-stars-fill';
                themeToggleBtn.title = 'Switch to Dark Mode';
            }
        }
    </script>
</body>

</html>