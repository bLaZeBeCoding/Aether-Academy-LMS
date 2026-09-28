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

// Get enrolled courses count
$c_courses = mysqli_fetch_array(mysqli_query($data, "SELECT COUNT(*) FROM enrollment WHERE student_id='$student_id'"))[0];

// Get total classes and attended classes
$a_res = mysqli_query($data, "SELECT COUNT(*) as total, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present FROM attendance WHERE student_id='$student_id'");
$a_data = mysqli_fetch_assoc($a_res);

$total_classes = (int)$a_data['total'];
$present_classes = (int)$a_data['present'];

$attendance_percentage = $total_classes > 0 ? round(($present_classes / $total_classes) * 100) : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Aether Academy</title>
    <?php include 'student_css.php'; ?>
    <style>
        .stat-card {
            border-radius: 12px;
            padding: 25px;
            color: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 3rem;
            opacity: 0.8;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
        }
        .bg-gradient-primary { background: linear-gradient(45deg, #06b6d4, #3b82f6); }
        .bg-gradient-success { background: linear-gradient(45deg, #10b981, #059669); }
        .bg-gradient-warning { background: linear-gradient(45deg, #f59e0b, #d97706); }
    </style>
</head>
<body>

<?php include 'student_sidebar.php'; ?>

<div class="content">
    <h1 class="mb-4">My Dashboard Overview</h1>
    <p class="text-muted mb-5">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>.</p>
    
    <div class="row g-4">
        <!-- Total Courses Enrolled -->
        <div class="col-md-6">
            <a href="student_courses.php" class="text-decoration-none">
                <div class="stat-card bg-gradient-primary h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1 text-white">Enrolled Courses</h6>
                            <div class="stat-number"><?php echo $c_courses; ?></div>
                        </div>
                        <div class="stat-icon"><i class="fas fa-book"></i></div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Combined Attendance -->
        <div class="col-md-6">
            <a href="student_attendance.php" class="text-decoration-none">
                <div class="stat-card bg-gradient-success h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1 text-white">Overall Attendance</h6>
                            <div class="stat-number">
                                <?php echo $attendance_percentage; ?>% 
                                <span class="fs-4 fw-normal opacity-75 ms-2">(<?php echo $present_classes; ?> / <?php echo $total_classes; ?>)</span>
                            </div>
                        </div>
                        <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
    
</body>
</html>
