<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['usertype'] == 'admin') {
    header("location:login.php");
    exit;
}

$data = mysqli_connect("localhost", "root", "", "collegepr", 3307);
$username = $_SESSION['username'];

// Get user ID
$u_res = mysqli_query($data, "SELECT id FROM user WHERE username='$username'");
$u_info = mysqli_fetch_assoc($u_res);
$student_id = $u_info['id'];

// Get attendance stats per course
$sql = "SELECT course.name as course_name, 
        COUNT(attendance.id) as total_classes,
        SUM(CASE WHEN attendance.status = 'Present' THEN 1 ELSE 0 END) as present_classes
        FROM enrollment 
        JOIN course ON enrollment.course_id = course.id 
        LEFT JOIN attendance ON enrollment.course_id = attendance.course_id AND attendance.student_id = '$student_id'
        WHERE enrollment.student_id = '$student_id'
        GROUP BY course.id";
$result = mysqli_query($data, $sql);

$course_stats = [];
$total_sessions_all = 0;
$total_attended_all = 0;
$sum_percentages = 0;
$course_count = 0;

while($row = mysqli_fetch_assoc($result)) {
    $total = (int)$row['total_classes'];
    $present = (int)$row['present_classes'];
    $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0.00;
    
    $course_stats[] = [
        'name' => $row['course_name'],
        'total' => $total,
        'present' => $present,
        'percentage' => $percentage
    ];
    
    $total_sessions_all += $total;
    $total_attended_all += $present;
    if($total > 0) {
        $sum_percentages += $percentage;
        $course_count++;
    }
}

$total_percentage = $total_sessions_all > 0 ? round(($total_attended_all / $total_sessions_all) * 100, 2) : 0.00;
$average_percentage = $course_count > 0 ? round($sum_percentages / $course_count, 2) : 0.00;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance | Aether Academy</title>
    <?php include 'student_css.php'; ?>
    <style>
        .attendance-table {
            border: 1px solid #334155;
            background-color: #1e293b;
        }
        .attendance-table thead th {
            background-color: #0f172a !important;
            color: #38bdf8 !important; /* light blue for headers */
            text-align: center;
            border-bottom: 2px solid #334155;
            padding: 15px;
        }
        .attendance-table td {
            text-align: center;
            vertical-align: middle;
            border-color: #334155;
            color: #f8fafc;
            padding: 15px;
        }
        .attendance-table td:first-child {
            text-align: left;
            font-weight: 600;
        }
        .text-danger-custom {
            color: #ef4444 !important;
            font-weight: 700;
        }
        .text-success-custom {
            color: #10b981 !important;
            font-weight: 700;
        }
        .totals-box {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-top: none;
            padding: 24px 30px;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            color: var(--text-heading) !important;
            font-size: 1rem;
            box-shadow: var(--shadow-card);
        }
        .stat-metric-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(249, 115, 22, 0.08);
            border: 1px solid rgba(249, 115, 22, 0.25);
            border-radius: 50px;
            font-weight: 700;
            color: var(--accent-orange);
            margin: 4px 6px;
        }
        [data-theme="light"] .stat-metric-pill {
            background: #fff7ed;
            border-color: #fdba74;
            color: #ea580c;
        }
        .stat-metric-success {
            background: rgba(16, 185, 129, 0.1) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
            color: #10b981 !important;
        }
        [data-theme="light"] .stat-metric-success {
            background: #ecfdf5 !important;
            border-color: #a7f3d0 !important;
            color: #059669 !important;
        }
        .attendance-title {
            color: var(--accent-orange) !important;
            letter-spacing: -0.02em;
        }
    </style>
</head>
<body>

<?php include 'student_sidebar.php'; ?>

<div class="content">
    
    <div class="card shadow-sm border-0 bg-transparent">
        <div class="card-body p-0">
            <!-- Student Header matching screenshot -->
            <h2 class="mb-4 text-center fw-bold text-uppercase attendance-title">
                <i class="fas fa-clipboard-check me-2"></i><?php echo htmlspecialchars($username); ?> - ATTENDANCE RECORD
            </h2>
            
            <?php if(count($course_stats) > 0): ?>
            <div class="table-responsive">
                <table class="table attendance-table mb-0">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Total Sessions</th>
                            <th>Marked Sessions</th>
                            <th>Attended Sessions</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($course_stats as $stat): 
                            // Determine color for percentage
                            $color_class = $stat['percentage'] < 75 ? 'text-danger-custom' : 'text-success-custom';
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($stat['name']); ?></td>
                            <td><?php echo $stat['total']; ?></td>
                            <td><?php echo $stat['total']; ?></td>
                            <td><?php echo $stat['present']; ?></td>
                            <td class="<?php echo $color_class; ?> fs-6"><?php echo number_format($stat['percentage'], 2); ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- High Contrast Totals Footer -->
                <div class="totals-box text-center">
                    <div class="d-flex flex-wrap justify-content-center align-items-center mb-3">
                        <div class="stat-metric-pill">
                            <i class="fas fa-calendar-alt"></i> Total Sessions: <strong><?php echo $total_sessions_all; ?></strong>
                        </div>
                        <div class="stat-metric-pill stat-metric-success">
                            <i class="fas fa-check-circle"></i> Attended Sessions: <strong><?php echo $total_attended_all; ?></strong>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-wrap justify-content-center align-items-center">
                        <div class="stat-metric-pill stat-metric-success fs-6 px-4 py-2">
                            Overall Attendance: <strong><?php echo number_format($total_percentage, 2); ?>%</strong>
                        </div>
                        <div class="stat-metric-pill fs-6 px-4 py-2">
                            Class Average: <strong><?php echo number_format($average_percentage, 2); ?>%</strong>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-info bg-transparent border-info text-info text-center">You are not enrolled in any courses to track attendance.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
    
</body>
</html>
