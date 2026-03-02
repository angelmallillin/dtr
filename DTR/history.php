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

// 1. FETCH USER DETAILS
$u_stmt = $conn->prepare("SELECT username, course, school, agency FROM users WHERE id = ?");
$u_stmt->bind_param("i", $user_id);
$u_stmt->execute();
$u_data = $u_stmt->get_result()->fetch_assoc();

// 2. GET DATE RANGE FILTERS
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// 3. FETCH ATTENDANCE GROUPED BY MONTH
$query_str = "SELECT *, 
    DATE_FORMAT(log_date, '%M %Y') as month_group,
    (TIME_TO_SEC(TIMEDIFF(am_out, am_in)) + TIME_TO_SEC(TIMEDIFF(pm_out, pm_in))) / 3600 as daily_total
    FROM attendance 
    WHERE user_id = ? AND log_date BETWEEN ? AND ?
    ORDER BY log_date ASC";

$stmt = $conn->prepare($query_str);
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$logs_by_month = [];
while($row = $result->fetch_assoc()) {
    $logs_by_month[$row['month_group']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance History | Pro System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-pink: #db2777;
            --bg-light: #f8fafc;
            --glass: rgba(255, 255, 255, 0.9);
            --border-color: #e2e8f0;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #1e293b;
        }

        /* --- FILTERS DESIGN --- */
        .filter-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            padding: 25px;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #f1f5f9;
            padding: 10px 15px;
            font-weight: 500;
        }

        .form-control:focus {
            border-color: var(--primary-pink);
            box-shadow: 0 0 0 4px rgba(219, 39, 119, 0.1);
        }

        /* --- DTR PAGE/PAPER DESIGN --- */
        .dtr-container {
            background: white;
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 40px;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .month-label {
            display: inline-block;
            background: #fdf2f8;
            color: var(--primary-pink);
            padding: 5px 15px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            padding: 15px;
            border-bottom: 2px solid #f1f5f9;
        }

        .table tbody td {
            padding: 15px;
            font-size: 0.9rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: #fdf2f8;
        }

        /* --- BUTTONS --- */
        .btn-pink-pro {
            background: linear-gradient(135deg, #db2777 0%, #be185d 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            padding: 12px 25px;
            transition: 0.3s;
        }

        .btn-pink-pro:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(219, 39, 119, 0.3);
            color: white;
        }

        /* --- PRINT SETTINGS --- */
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .dtr-container { 
                box-shadow: none; 
                border: 1px solid #000; 
                padding: 20px; 
                margin: 0; 
                page-break-after: always;
            }
            .table th, .table td { border: 1px solid #000 !important; font-size: 10px; }
            .month-label { border: 1px solid #db2777; }
        }
    </style>
</head>
<body>

<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h3 class="fw-800 mb-0">Attendance History</h3>
            <p class="text-muted small">Manage and print your Daily Time Records</p>
        </div>
        <a href="dashboard.php" class="btn btn-light border-0 shadow-sm fw-bold px-4" style="border-radius: 12px;">
            <i class="bi bi-house-door me-2"></i>Dashboard
        </a>
    </div>

    <div class="filter-card mb-5 no-print">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">START DATE</label>
                <input type="date" id="start_date" class="form-control" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">END DATE</label>
                <input type="date" id="end_date" class="form-control" value="<?php echo $end_date; ?>">
            </div>
            <div class="col-md-6 d-flex gap-2">
                <button onclick="applyFilter()" class="btn btn-dark fw-bold px-4" style="border-radius: 12px;">
                    <i class="bi bi-funnel me-2"></i>Apply Filter
                </button>
                <button onclick="window.print()" class="btn btn-pink-pro px-4">
                    <i class="bi bi-printer me-2"></i>Print Bulk DTR
                </button>
            </div>
        </div>
    </div>

    <?php if(empty($logs_by_month)): ?>
        <div class="text-center py-5 no-print">
            <div class="mb-3"><i class="bi bi-calendar-x fs-1 text-muted"></i></div>
            <h5 class="text-muted fw-bold">No records found</h5>
            <p class="small text-secondary">Try adjusting your date range filter.</p>
        </div>
    <?php endif; ?>

    <?php foreach($logs_by_month as $month_name => $logs): ?>
    <div class="dtr-container">
        <div class="text-center">
            <div class="month-label"><?php echo $month_name; ?></div>
            <h4 class="fw-800 mb-1">DAILY TIME RECORD</h4>
            <div class="row text-start mt-4 px-3">
                <div class="col-6 mb-3">
                    <label class="text-muted d-block" style="font-size: 0.65rem; font-weight: 800;">NAME</label>
                    <span class="fw-bold text-uppercase"><?php echo $u_data['username']; ?></span>
                </div>
                <div class="col-6 mb-3">
                    <label class="text-muted d-block" style="font-size: 0.65rem; font-weight: 800;">SCHOOL</label>
                    <span class="fw-bold text-uppercase"><?php echo $u_data['school'] ?: '---'; ?></span>
                </div>
                <div class="col-6">
                    <label class="text-muted d-block" style="font-size: 0.65rem; font-weight: 800;">COURSE</label>
                    <span class="fw-bold text-uppercase"><?php echo $u_data['course'] ?: '---'; ?></span>
                </div>
                <div class="col-6">
                    <label class="text-muted d-block" style="font-size: 0.65rem; font-weight: 800;">AGENCY</label>
                    <span class="fw-bold text-uppercase"><?php echo $u_data['agency'] ?: '---'; ?></span>
                </div>
            </div>
        </div>

        <div class="table-responsive mt-4">
            <table class="table text-center">
                <thead>
                    <tr>
                        <th rowspan="2">Day</th>
                        <th colspan="2">Morning</th>
                        <th colspan="2">Afternoon</th>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr>
                        <th>In</th>
                        <th>Out</th>
                        <th>In</th>
                        <th>Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $month_total = 0;
                    foreach($logs as $log): 
                        $month_total += $log['daily_total'];
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo date('d', strtotime($log['log_date'])); ?></td>
                        <td><?php echo ($log['am_in'] != '00:00:00') ? date('h:i A', strtotime($log['am_in'])) : '-'; ?></td>
                        <td><?php echo ($log['am_out'] != '00:00:00') ? date('h:i A', strtotime($log['am_out'])) : '-'; ?></td>
                        <td><?php echo ($log['pm_in'] != '00:00:00') ? date('h:i A', strtotime($log['pm_in'])) : '-'; ?></td>
                        <td><?php echo ($log['pm_out'] != '00:00:00') ? date('h:i A', strtotime($log['pm_out'])) : '-'; ?></td>
                        <td class="fw-800 text-pink"><?php echo number_format($log['daily_total'], 2); ?>h</td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="table-light">
                        <td colspan="5" class="text-end fw-800 small py-3">GRAND TOTAL HOURS:</td>
                        <td class="fw-800 text-pink py-3" style="font-size: 1.1rem;"><?php echo number_format($month_total, 2); ?>h</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-5 pt-4">
            <div class="row">
                <div class="col-6 text-center">
                    <div style="border-top: 2px solid #1e293b; width: 70%; margin: 0 auto;"></div>
                    <small class="fw-bold text-muted">Trainee Signature</small>
                </div>
                <div class="col-6 text-center">
                    <div style="border-top: 2px solid #1e293b; width: 70%; margin: 0 auto;"></div>
                    <small class="fw-bold text-muted">Supervisor Signature</small>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

</div>

<script>
function applyFilter() {
    const start = document.getElementById('start_date').value;
    const end = document.getElementById('end_date').value;
    if(start && end) {
        window.location.href = `history.php?start_date=${start}&end_date=${end}`;
    } else {
        alert("Please select both dates.");
    }
}
</script>

</body>
</html>