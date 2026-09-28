<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['usertype'] == 'student') {
    header("location:login.php");
    exit;
}

$data = mysqli_connect("localhost", "root", "", "collegepr", 3307);

if(!isset($_GET['course_id'])) {
    header("location:admin_view_course.php");
    exit;
}
$course_id = (int)$_GET['course_id'];

// Get Course Info
$c_query = "SELECT course.name as course_name, teacher.name as teacher_name 
            FROM course JOIN teacher ON course.teacher_id = teacher.id 
            WHERE course.id = '$course_id'";
$c_result = mysqli_query($data, $c_query);
$course_info = mysqli_fetch_assoc($c_result);

// Handle Enrollment
if(isset($_POST['enroll'])) {
    $student_id = (int)$_POST['student_id'];
    
    // Check if already enrolled
    $check = mysqli_query($data, "SELECT * FROM enrollment WHERE student_id='$student_id' AND course_id='$course_id'");
    if(mysqli_num_rows($check) > 0) {
        $error = "Student is already enrolled in this course.";
    } else {
        mysqli_query($data, "INSERT INTO enrollment(student_id, course_id) VALUES ('$student_id', '$course_id')");
        $success = "Student successfully enrolled!";
    }
}

// Handle Unenroll
if(isset($_GET['remove_id'])) {
    $rem_id = (int)$_GET['remove_id'];
    mysqli_query($data, "DELETE FROM enrollment WHERE student_id='$rem_id' AND course_id='$course_id'");
    header("location:admin_enroll.php?course_id=$course_id");
    exit;
}

// Fetch all students to populate dropdown
$students = mysqli_query($data, "SELECT id, username FROM user WHERE usertype='student'");

// Fetch enrolled students
$enrolled = mysqli_query($data, "SELECT user.id, user.username, user.email 
                                 FROM enrollment 
                                 JOIN user ON enrollment.student_id = user.id 
                                 WHERE enrollment.course_id = '$course_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Students</title>
    <?php include 'admin_css.php'; ?>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="mb-4">
        <a href="admin_view_course.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Courses</a>
    </div>

    <h2 class="mb-1 text-white fw-bold"><?php echo htmlspecialchars($course_info['course_name']); ?></h2>
    <p class="text-muted fs-5"><i class="fas fa-user-tie me-2"></i>Taught by: <?php echo htmlspecialchars($course_info['teacher_name']); ?></p>

    <div class="row mt-4">
        <!-- Enrollment Form -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Enroll New Student</h5>
                    
                    <?php if(isset($error)) echo "<div class='alert alert-danger p-2'>$error</div>"; ?>
                    <?php if(isset($success)) echo "<div class='alert alert-success p-2'>$success</div>"; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <select class="form-select form-control" name="student_id" required>
                                <option value="" disabled selected>Select Student...</option>
                                <?php while($s = mysqli_fetch_assoc($students)): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['username']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" name="enroll" class="btn btn-success w-100">
                            <i class="fas fa-plus"></i> Enroll Student
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Enrolled List -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Currently Enrolled Students</h5>
                    <div class="table-responsive">
                        <table id="enrolledTable" class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($e = mysqli_fetch_assoc($enrolled)): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($e['username']); ?></td>
                                    <td><?php echo htmlspecialchars($e['email']); ?></td>
                                    <td>
                                        <a href="admin_enroll.php?course_id=<?php echo $course_id; ?>&remove_id=<?php echo $e['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Remove student from course?');">
                                            <i class="fas fa-times"></i> Unenroll
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#enrolledTable').DataTable({
            "pageLength": 5,
            "language": { "search": "Search Enrolled:" }
        });
    });
</script>
    
</body>
</html>
