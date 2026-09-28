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

// Get enrolled courses
$sql = "SELECT course.id as course_id, course.name as course_name, teacher.name as teacher_name 
        FROM enrollment 
        JOIN course ON enrollment.course_id = course.id 
        JOIN teacher ON course.teacher_id = teacher.id 
        WHERE enrollment.student_id = '$student_id'";
$result = mysqli_query($data, $sql);

// Array of CSS gradients to randomly assign to courses based on ID
$gradients = [
    'linear-gradient(135deg, #0ea5e9, #2563eb)', // Blue
    'linear-gradient(135deg, #10b981, #059669)', // Green
    'linear-gradient(135deg, #8b5cf6, #6d28d9)', // Purple
    'linear-gradient(135deg, #f59e0b, #d97706)', // Orange
    'linear-gradient(135deg, #ec4899, #be185d)'  // Pink
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses | Aether Academy</title>
    <?php include 'student_css.php'; ?>
    <style>
        .course-card {
            border-radius: 12px;
            overflow: hidden;
            background-color: #1e293b;
            border: 1px solid #334155;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }
        .course-banner {
            height: 140px;
            width: 100%;
            position: relative;
            /* We will apply inline background gradient based on course ID */
        }
        .course-banner::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.15) 0%, transparent 50%),
                              radial-gradient(circle at 80% 90%, rgba(0,0,0,0.15) 0%, transparent 50%);
        }
        .course-body {
            padding: 20px;
        }
        .course-title {
            color: #0ea5e9 !important; /* Standout blue title matching the screenshot */
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .course-meta {
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .course-footer {
            padding: 10px 20px;
            border-top: 1px solid #334155;
            text-align: right;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<?php include 'student_sidebar.php'; ?>

<div class="content">
    <h1 class="mb-4">Course Overview</h1>
    
    <!-- Filters row matching screenshot (visual only for now) -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <select class="form-select w-auto"><option>All</option></select>
        <input type="text" class="form-control w-auto" placeholder="Search">
        <select class="form-select w-auto"><option>Sort by course name</option></select>
        <select class="form-select w-auto"><option>Card</option></select>
    </div>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php while($row = mysqli_fetch_assoc($result)): 
                // Deterministic gradient based on course ID so it doesn't change on refresh
                $grad_idx = $row['course_id'] % count($gradients);
                $bg_gradient = $gradients[$grad_idx];
            ?>
            <div class="col">
                <div class="course-card">
                    <div class="course-banner" style="background: <?php echo $bg_gradient; ?>;"></div>
                    <div class="course-body">
                        <div class="course-title"><?php echo htmlspecialchars($row['course_name']); ?></div>
                        <div class="course-meta">Instructor : <?php echo htmlspecialchars($row['teacher_name']); ?></div>
                    </div>
                    <div class="course-footer">
                        <i class="fas fa-ellipsis-v cursor-pointer"></i>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info bg-transparent border-info text-info">You are not currently enrolled in any courses.</div>
    <?php endif; ?>
</div>
    
</body>
</html>
