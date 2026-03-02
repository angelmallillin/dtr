<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include 'db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// --- 1. FETCH USER DATA ---
$user_query = $conn->prepare("SELECT username, profile_pic, target_hours, course, school, agency FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_data = $user_query->get_result()->fetch_assoc();

$display_name = $user_data['username'] ?? "User";
$target_hours = ($user_data['target_hours'] > 0) ? $user_data['target_hours'] : 500; 
$profile_pic = $user_data['profile_pic'];
$course = $user_data['course'] ?? "";
$school = $user_data['school'] ?? "";
$agency = $user_data['agency'] ?? "";
$initial = strtoupper(substr($display_name, 0, 1));

// --- 2. CHECK TODAY'S LOGS ---
$check_today = $conn->prepare("SELECT am_in, am_out, pm_in, pm_out FROM attendance WHERE user_id = ? AND log_date = ?");
$check_today->bind_param("is", $user_id, $today);
$check_today->execute();
$today_record = $check_today->get_result()->fetch_assoc();

function formatTimeDisplay($time) {
    if (empty($time) || $time == "00:00:00" || $time == null) {
        return ""; 
    }
    return date("h:i A", strtotime($time));
}

// --- 3. PROGRESS COMPUTATION ---
$stats_query = $conn->prepare("SELECT 
    SUM(TIME_TO_SEC(TIMEDIFF(am_out, am_in)) / 3600) as am_hrs,
    SUM(TIME_TO_SEC(TIMEDIFF(pm_out, pm_in)) / 3600) as pm_hrs
    FROM attendance WHERE user_id = ?");
$stats_query->bind_param("i", $user_id);
$stats_query->execute();
$stats_result = $stats_query->get_result()->fetch_assoc();

$total_hours = round(($stats_result['am_hrs'] + $stats_result['pm_hrs']), 2);
$remaining_hours = max(0, $target_hours - $total_hours);
$progress_percent = ($total_hours / $target_hours) * 100;

// Progress Status Logic
$status_text = "In Progress";
$status_color = "#4f46e5";
if ($progress_percent >= 100) {
    $progress_percent = 100;
    $status_text = "Completed";
    $status_color = "#10b981";
} elseif ($progress_percent >= 75) {
    $status_text = "Almost There";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTR | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* --- MODERN PRO UI ADDITIONS --- */
/* --- THEME-AWARE PRO UI --- */
:root {
    --modal-bg: #ffffff;
    --modal-text: #1e293b;
    --input-bg: #f8fafc;
    --input-border: #e2e8f0;
    --input-text: #1e293b;
    --label-color: #64748b;
    --primary-pink: #db2777;
}

[data-theme='dark'] {
    --modal-bg: #1e293b; /* Dark Blue/Slate */
    --modal-text: #f8fafc;
    --input-bg: #0f172a; /* Deeper Dark Blue */
    --input-border: #334155;
    --input-text: #f8fafc;
    --label-color: #94a3b8;
}

/* Modal Content Base */
.modal-content {
    background-color: var(--modal-bg) !important;
    color: var(--modal-text) !important;
    border-radius: 24px !important;
    border: 1px solid var(--input-border) !important;
    transition: all 0.3s ease;
}

.modal-header {
    background: transparent !important;
    border-bottom: 1px solid var(--input-border) !important;
}

/* Inputs na nagbabago kulay */
.form-control {
    background-color: var(--input-bg) !important;
    border: 2px solid var(--input-border) !important;
    color: var(--input-text) !important;
    border-radius: 12px !important;
    padding: 12px 16px !important;
    transition: all 0.2s ease !important;
}

.form-control:focus {
    background-color: var(--modal-bg) !important;
    border-color: var(--primary-pink) !important;
    box-shadow: 0 0 0 4px rgba(219, 39, 119, 0.2) !important;
    color: var(--input-text) !important;
}

.form-label {
    color: var(--label-color) !important;
    font-size: 0.75rem !important;
    font-weight: 800;
    letter-spacing: 0.05em;
}

/* Close button adjustment para sa dark mode */
[data-theme='dark'] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

.btn-save-pro {
    background: linear-gradient(135deg, #db2777 0%, #be185d 100%);
    color: white !important;
    border: none;
    padding: 14px !important;
    border-radius: 14px !important;
    font-weight: 700 !important;
    width: 100%;
    margin-top: 10px;
}


.profile-upload-container {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
}

.profile-preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
        :root {
            --bg-color: #f1f3f5;
            --text-color: #1e293b;
            --card-bg: white;
            --nav-bg: white;
            --input-bg: white;
            --tab-bg: #e5e7eb;
            --log-box-bg: #f9fafb;
            --border-color: #e2e8f0;
        }

        [data-theme="dark"] {
            --bg-color: #0f172a;
            --text-color: #f1f5f9;
            --card-bg: #1e293b;
            --nav-bg: #1e293b;
            --input-bg: #334155;
            --tab-bg: #334155;
            --log-box-bg: #334155;
            --border-color: #475569;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-color); color: var(--text-color); transition: all 0.3s ease; }
        .top-nav { padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; background: var(--nav-bg); border-bottom: 1px solid var(--border-color); }
        .profile-circle { width: 45px; height: 45px; background: #4f46e5; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; cursor: pointer; overflow: hidden; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .profile-circle img { width: 100%; height: 100%; object-fit: cover; }
        
        .main-container { max-width: 500px; margin: 0 auto; padding: 10px 15px; text-align: center; }
        #liveClock { font-size: 3rem; font-weight: 800; color: #10b981; margin: 5px 0; }
        [data-theme="dark"] #liveClock { color: #34d399; }
        
        .status-badge { background: #dcfce7; color: #166534; padding: 4px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-block; margin-bottom: 20px; }
        [data-theme="dark"] .status-badge { background: #064e3b; color: #dcfce7; }

        .tab-group { display: flex; background: var(--tab-bg); border-radius: 12px; padding: 4px; margin-bottom: 20px; }
        .tab-item { flex: 1; padding: 10px; border-radius: 10px; border: none; background: transparent; color: var(--text-color); font-weight: 700; font-size: 0.85rem; cursor: pointer; opacity: 0.7; }
        .tab-item.active { background: var(--card-bg); color: var(--text-color); box-shadow: 0 2px 4px rgba(0,0,0,0.1); opacity: 1; }

        .session-card { background: var(--card-bg); border-radius: 15px; padding: 20px; text-align: left; margin-bottom: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border-color); }
        .log-box { background: var(--log-box-bg); border-radius: 10px; padding: 12px; text-align: center; border: 1px solid var(--border-color); min-height: 80px; }
        .logged-txt { color: #10b981; font-weight: 700; font-size: 0.75rem; display: block; margin-top: 5px; }
        
        .form-control, .form-select { background-color: var(--input-bg); color: var(--text-color); border-radius: 10px; padding: 10px; font-size: 0.9rem; font-weight: 600; border: 1px solid var(--border-color); }
        .form-control:focus { background-color: var(--input-bg); color: var(--text-color); border-color: #4f46e5; }
        
        textarea.form-control { resize: none; }
        .theme-switch { cursor: pointer; font-size: 1.5rem; color: var(--text-color); display: flex; align-items: center; }

        /* Enhanced Progress Section Styles */
        .progress-section { background: var(--card-bg); border-radius: 15px; padding: 20px; text-align: left; margin-top: 20px; border: 1px solid var(--border-color); }
        .stat-val { font-weight: 800; font-size: 1.2rem; }
        .stat-lbl { font-size: 0.7rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .progress { height: 12px; border-radius: 10px; background: var(--tab-bg); margin: 15px 0; overflow: hidden; }
        .progress-bar { background: linear-gradient(90deg, #4f46e5, #10b981); transition: width 1s ease-in-out; }
        .progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .pct-text { font-weight: 800; color: #4f46e5; }
        [data-theme="dark"] .pct-text { color: #818cf8; }
    </style>
</head>
<body>

    <div class="top-nav">
        <div class="d-flex align-items-center gap-3">
            <div class="profile-circle" data-bs-toggle="modal" data-bs-target="#profileModal">
                <?php if($profile_pic): ?>
                    <img src="uploads/profile/<?php echo $profile_pic; ?>" alt="Profile">
                <?php else: ?>
                    <span style="font-size: 1.2rem;"><?php echo $initial; ?></span>
                <?php endif; ?>
            </div>
            <div class="fw-bold small"><?php echo htmlspecialchars($display_name); ?></div>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <a href="history.php" class="text-decoration-none" style="color: var(--text-color);">
                <i class="bi bi-clock-history fs-4"></i>
            </a>
            <div class="theme-switch" id="themeToggle">
                <i class="bi bi-moon-fill" id="themeIcon"></i>
            </div>
            <a href="logout.php" class="text-danger"><i class="bi bi-power fs-4"></i></a>
        </div>
    </div>

    <div class="main-container">
        <div id="liveDate" class="fw-bold text-secondary"></div>
        <div id="liveClock">00:00:00 PM</div>
        <div class="status-badge">• Status: ONLINE</div>

        <div class="tab-group">
            <button class="tab-item active" id="tabQuick" onclick="switchView('quick')">Today (Quick Log)</button>
            <button class="tab-item" id="tabPast" onclick="switchView('past')">Encode Past</button>
        </div>

        <div id="viewQuick">
            <div class="session-card">
                <div class="fw-bold mb-3 small text-uppercase">AM Session</div>
                <div class="log-grid d-flex gap-2">
                    <div class="log-box flex-fill">
                        <div class="small text-secondary mb-1">AM IN</div>
                        <div class="fw-bold"><?php echo formatTimeDisplay($today_record['am_in'] ?? null); ?></div>
                        <?php if(!empty($today_record['am_in'])) echo '<span class="logged-txt"><i class="bi bi-check-circle-fill"></i> LOGGED</span>'; ?>
                    </div>
                    <div class="log-box flex-fill">
                        <div class="small text-secondary mb-1">AM OUT</div>
                        <div class="fw-bold"><?php echo formatTimeDisplay($today_record['am_out'] ?? null); ?></div>
                        <?php if(!empty($today_record['am_out'])) echo '<span class="logged-txt"><i class="bi bi-check-circle-fill"></i> LOGGED</span>'; ?>
                    </div>
                </div>
            </div>
            <div class="session-card">
                <div class="fw-bold mb-3 small text-uppercase">PM Session</div>
                <div class="log-grid d-flex gap-2">
                    <div class="log-box flex-fill">
                        <div class="small text-secondary mb-1">PM IN</div>
                        <div class="fw-bold"><?php echo formatTimeDisplay($today_record['pm_in'] ?? null); ?></div>
                        <?php if(!empty($today_record['pm_in'])) echo '<span class="logged-txt"><i class="bi bi-check-circle-fill"></i> LOGGED</span>'; ?>
                    </div>
                    <div class="log-box flex-fill">
                        <div class="small text-secondary mb-1">PM OUT</div>
                        <div class="fw-bold"><?php echo formatTimeDisplay($today_record['pm_out'] ?? null); ?></div>
                        <?php if(!empty($today_record['pm_out'])) echo '<span class="logged-txt"><i class="bi bi-check-circle-fill"></i> LOGGED</span>'; ?>
                    </div>
                </div>
            </div>
            <a class="btn btn-dark w-100 py-3 fw-bold mt-2" style="border-radius: 12px;">GO TO QUICK LOG</a>
        </div>

        <div id="viewPast" style="display:none;">
            <form action="manual_process.php" method="POST">
                <div class="session-card">
                    <div class="mb-4">
                        <label class="small fw-bold text-uppercase mb-2 d-block" style="letter-spacing: 1px; opacity: 0.8;">
                            <i class="bi bi-calendar3 me-1"></i> Select Date
                        </label>
                        <input type="date" name="manual_date" class="form-control form-control-lg border-2" 
                               value="<?php echo date('Y-m-d'); ?>" required 
                               style="border-radius: 12px; font-size: 1rem;">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <small class="fw-bold text-uppercase text-secondary" style="font-size: 0.7rem;">AM Session</small>
                            <hr class="mt-1 mb-2" style="opacity: 0.1;">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold mb-1">AM IN</label>
                            <input type="time" name="am_in" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold mb-1">AM OUT</label>
                            <input type="time" name="am_out" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <small class="fw-bold text-uppercase text-secondary" style="font-size: 0.7rem;">PM Session</small>
                            <hr class="mt-1 mb-2" style="opacity: 0.1;">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold mb-1">PM IN</label>
                            <input type="time" name="pm_in" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold mb-1">PM OUT</label>
                            <input type="time" name="pm_out" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-uppercase mb-2 d-block" style="opacity: 0.8;">Daily Status</label>
                        <select name="status_exception" class="form-select border-2" style="border-radius: 12px;">
                            <option>Regular Duty Day</option>
                            <option>Absent</option>
                            <option>Holiday</option>
                            <option>Suspended</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-uppercase mb-1 d-block" style="opacity: 0.8;">
                            Internal Note <span class="text-danger" style="text-transform: none;">(Will NOT print)</span>
                        </label>
                        <textarea name="internal_note" class="form-control" rows="2" 
                                  placeholder="Add private remarks here..." 
                                  style="border-radius: 12px; font-size: 0.85rem;"></textarea>
                    </div>

                    <button type="submit" name="save_manual" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" 
                            style="border-radius: 15px; background: linear-gradient(45deg, #4f46e5, #6366f1); border: none;">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> SAVE ENTRY
                    </button>
                </div>
            </form>
        </div>

        <div class="progress-section shadow-sm">
            <div class="progress-header">
                <div class="stat-lbl fw-bold">OJT Progress</div>
                <div class="pct-text"><?php echo round($progress_percent, 1); ?>%</div>
            </div>
            
            <div class="progress">
                <div class="progress-bar" role="progressbar" 
                     style="width: <?php echo $progress_percent; ?>%" 
                     aria-valuenow="<?php echo $progress_percent; ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                </div>
            </div>

            <div class="d-flex justify-content-between text-center mt-2">
                <div class="flex-fill">
                    <div class="stat-lbl">Rendered</div>
                    <div class="stat-val"><?php echo number_format($total_hours, 1); ?> <small class="text-secondary" style="font-size: 0.6rem;">HRS</small></div>
                </div>
                <div class="flex-fill border-start border-end">
                    <div class="stat-lbl">Target</div>
                    <div class="stat-val"><?php echo $target_hours; ?> <small class="text-secondary" style="font-size: 0.6rem;">HRS</small></div>
                </div>
                <div class="flex-fill">
                    <div class="stat-lbl">Remaining</div>
                    <div class="stat-val" style="color: #ef4444;"><?php echo number_format($remaining_hours, 1); ?> <small class="text-secondary" style="font-size: 0.6rem;">HRS</small></div>
                </div>
            </div>

            <div class="mt-3 pt-2 border-top text-center">
                <span class="small fw-bold text-uppercase" style="color: <?php echo $status_color; ?>; font-size: 0.75rem;">
                    <i class="bi bi-info-circle-fill me-1"></i> Current Status: <?php echo $status_text; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 d-flex align-items-center">
                <div>
                    <h5 class="fw-800 mb-0" style="color: #1e293b;">Edit Profile</h5>
                    <p class="text-muted small mb-0">Update your account information</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="update_settings.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="profile-upload-container">
                            <?php if($profile_pic): ?>
                                <img src="uploads/profile/<?php echo $profile_pic; ?>" class="profile-preview-img" alt="Profile">
                            <?php else: ?>
                                <div class="profile-preview-img d-flex align-items-center justify-content-center bg-pink text-white fs-2 fw-bold" style="background: var(--primary-pink);">
                                    <?php echo $initial; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <label class="form-label d-block fw-800">PROFILE PICTURE</label>
                        <input type="file" name="profile_image" class="form-control mb-1">
                        <span class="text-muted" style="font-size: 0.7rem;">Recommended: Square image, max 2MB</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-800"><i class="bi bi-person me-1"></i> FULL NAME / USERNAME</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($display_name); ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-800"><i class="bi bi-book me-1"></i> COURSE / PROGRAM</label>
                            <input type="text" name="course" class="form-control" placeholder="e.g. BS in Information Technology" value="<?php echo htmlspecialchars($course); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-800"><i class="bi bi-building me-1"></i> SCHOOL / UNIVERSITY</label>
                            <input type="text" name="school" class="form-control" placeholder="e.g. University of the Philippines" value="<?php echo htmlspecialchars($school); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-800"><i class="bi bi-briefcase me-1"></i> ASSIGNED AGENCY / OFFICE</label>
                            <input type="text" name="agency" class="form-control" placeholder="e.g. Department of Agriculture" value="<?php echo htmlspecialchars($agency); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-800 text-pink"><i class="bi bi-target me-1"></i> REQUIRED OJT HOURS</label>
                            <input type="number" name="target_hours" class="form-control fw-bold" value="<?php echo $target_hours; ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" name="save_profile" class="btn btn-save-pro w-100">
                        <i class="bi bi-check2-circle me-2"></i>SAVE CHANGES
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const currentTheme = localStorage.getItem('theme') || 'light';

        if (currentTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill');
        }

        themeToggle.addEventListener('click', () => {
            let theme = document.documentElement.getAttribute('data-theme');
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'light');
                themeIcon.classList.replace('bi-sun-fill', 'bi-moon-fill');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill');
                localStorage.setItem('theme', 'dark');
            }
        });

        function switchView(view) {
            document.getElementById('viewQuick').style.display = (view === 'quick') ? 'block' : 'none';
            document.getElementById('viewPast').style.display = (view === 'past') ? 'block' : 'none';
            document.getElementById('tabQuick').classList.toggle('active', view === 'quick');
            document.getElementById('tabPast').classList.toggle('active', view === 'past');
        }

        function updateTime() {
            const now = new Date();
            document.getElementById('liveDate').innerText = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            document.getElementById('liveClock').innerText = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
        }
        setInterval(updateTime, 1000); updateTime();
    </script>
</body>
</html>