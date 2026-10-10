<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['usertype'] == 'student') {
    header("location:login.php");
    exit;
}

$data = mysqli_connect("localhost", "root", "", "collegepr", 3307);

// Fetch counts for the dashboard
$c_student = mysqli_fetch_array(mysqli_query($data, "SELECT COUNT(*) FROM user WHERE usertype='student'"))[0];
$c_teacher = mysqli_fetch_array(mysqli_query($data, "SELECT COUNT(*) FROM teacher"))[0];
$c_course  = mysqli_fetch_array(mysqli_query($data, "SELECT COUNT(*) FROM course"))[0];
$c_admit   = mysqli_fetch_array(mysqli_query($data, "SELECT COUNT(*) FROM admission"))[0];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Aether Academy</title>
    <?php include 'admin_css.php'; ?>
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
            font-size: 2.2rem;
            opacity: 0.8;
        }
        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
        }
        .bg-gradient-primary { background: linear-gradient(45deg, #06b6d4, #3b82f6); }
        .bg-gradient-success { background: linear-gradient(45deg, #10b981, #059669); }
        .bg-gradient-warning { background: linear-gradient(45deg, #f59e0b, #d97706); }
        .bg-gradient-danger  { background: linear-gradient(45deg, #ef4444, #b91c1c); }
        .bg-gradient-purple  { background: linear-gradient(45deg, #8b5cf6, #6d28d9); }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <h1 class="mb-4">Dashboard Overview</h1>
    <p class="text-muted mb-5">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>. Here is the current status of Aether Academy.</p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
        <!-- Total Students -->
        <div>
            <a href="view_student.php" class="text-decoration-none">
                <div class="stat-card bg-gradient-primary h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1 text-white">Total Students</h6>
                            <div class="stat-number"><?php echo $c_student; ?></div>
                        </div>
                        <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Faculty -->
        <div>
            <a href="admin_view_teacher.php" class="text-decoration-none">
                <div class="stat-card bg-gradient-purple h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1 text-white">Total Faculty</h6>
                            <div class="stat-number"><?php echo $c_teacher; ?></div>
                        </div>
                        <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Courses -->
        <div>
            <a href="admin_view_course.php" class="text-decoration-none">
                <div class="stat-card bg-gradient-success h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1 text-white">Active Courses</h6>
                            <div class="stat-number"><?php echo $c_course; ?></div>
                        </div>
                        <div class="stat-icon"><i class="fas fa-book"></i></div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Pending Admissions -->
        <div>
            <a href="admission.php" class="text-decoration-none">
                <div class="stat-card bg-gradient-warning h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1 text-white">Admissions</h6>
                            <div class="stat-number"><?php echo $c_admit; ?></div>
                        </div>
                        <div class="stat-icon"><i class="fas fa-envelope-open-text"></i></div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
    
</body>
</html>


