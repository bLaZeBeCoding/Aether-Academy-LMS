<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['usertype'] == 'student') {
    header("location:login.php");
    exit;
}

$data = mysqli_connect("localhost", "root", "", "collegepr", 3307);

$courses = mysqli_query($data, "SELECT id, name FROM course");

$selected_course = isset($_GET['course_id']) ? (int)$_GET['course_id'] : '';
$selected_date = isset($_GET['attendance_date']) ? $_GET['attendance_date'] : date('Y-m-d');

// Save attendance
if(isset($_POST['save_attendance'])) {
    $c_id = (int)$_POST['course_id'];
    $a_date = mysqli_real_escape_string($data, $_POST['attendance_date']);
    
    // Loop through all submitted statuses
    if(isset($_POST['status'])) {
        foreach($_POST['status'] as $student_id => $status) {
            $s_id = (int)$student_id;
            $stat = mysqli_real_escape_string($data, $status);
            
            // Upsert fallback without unique key
            $check = mysqli_query($data, "SELECT id FROM attendance WHERE student_id='$s_id' AND course_id='$c_id' AND attendance_date='$a_date'");
            if(mysqli_num_rows($check) > 0) {
                mysqli_query($data, "UPDATE attendance SET status='$stat' WHERE student_id='$s_id' AND course_id='$c_id' AND attendance_date='$a_date'");
            } else {
                mysqli_query($data, "INSERT INTO attendance (student_id, course_id, attendance_date, status) VALUES ('$s_id', '$c_id', '$a_date', '$stat')");
            }
        }
        $success = "Attendance saved for $a_date!";
    }
}

// Fetch enrolled students if a course is selected
$enrolled = [];
if($selected_course != '') {
    $query = "SELECT user.id, user.username, 
              (SELECT status FROM attendance WHERE student_id = user.id AND course_id = '$selected_course' AND attendance_date = '$selected_date' LIMIT 1) as current_status
              FROM enrollment 
              JOIN user ON enrollment.student_id = user.id 
              WHERE enrollment.course_id = '$selected_course'";
    $enrolled = mysqli_query($data, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <?php include 'admin_css.php'; ?>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <h1 class="mb-4">Course Attendance</h1>

    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <!-- Filter Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row align-items-end" id="filterForm">
                <div class="col-md-5">
                    <label class="form-label fw-bold">Select Course</label>
                    <select class="form-select form-control" name="course_id" onchange="document.getElementById('filterForm').submit();" required>
                        <option value="" disabled selected>Choose...</option>
                        <?php while($c = mysqli_fetch_assoc($courses)): ?>
                            <option value="<?php echo $c['id']; ?>" <?php if($selected_course == $c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold">Date</label>
                    <input type="date" class="form-control" name="attendance_date" value="<?php echo htmlspecialchars($selected_date); ?>" onchange="document.getElementById('filterForm').submit();" required>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Load Class</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Form -->
    <?php if($selected_course != ''): ?>
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Marking attendance for: <?php echo htmlspecialchars($selected_date); ?></h5>
            
            <?php if(mysqli_num_rows($enrolled) > 0): ?>
            <form method="POST" action="">
                <input type="hidden" name="course_id" value="<?php echo $selected_course; ?>">
                <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($selected_date); ?>">
                
                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Student Name</th>
                                <th>Present</th>
                                <th>Absent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($s = mysqli_fetch_assoc($enrolled)): ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($s['username']); ?></td>
                                <td>
                                    <input class="form-check-input fs-5" type="radio" name="status[<?php echo $s['id']; ?>]" value="Present" required <?php if($s['current_status'] == 'Present') echo 'checked'; ?>>
                                </td>
                                <td>
                                    <input class="form-check-input fs-5" type="radio" name="status[<?php echo $s['id']; ?>]" value="Absent" <?php if($s['current_status'] == 'Absent') echo 'checked'; ?>>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" name="save_attendance" class="btn btn-success btn-lg px-5">
                    <i class="fas fa-save me-2"></i> Save Attendance
                </button>
            </form>
            <?php else: ?>
                <div class="alert alert-warning">No students are currently enrolled in this course.</div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>
    
</body>
</html>
