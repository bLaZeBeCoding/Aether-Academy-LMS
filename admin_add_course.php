<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['usertype'] == 'student') {
    header("location:login.php");
    exit;
}

$data = mysqli_connect("localhost", "root", "", "collegepr", 3307);

if(isset($_POST['add_course'])) {
    $course_name = mysqli_real_escape_string($data, trim($_POST['course_name']));
    $teacher_id = (int)$_POST['teacher_id'];

    $sql = "INSERT INTO course(name, teacher_id) VALUES ('$course_name', '$teacher_id')";
    $result = mysqli_query($data, $sql);

    if($result) {
        $message = "Course Added Successfully!";
    } else {
        $message = "Failed to add course.";
    }
}

// Fetch teachers for the dropdown
$sql_teachers = "SELECT id, name FROM teacher";
$result_teachers = mysqli_query($data, $sql_teachers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <?php include 'admin_css.php'; ?>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card shadow-sm border-0" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body p-5">
            <h2 class="mb-4 text-center fw-bold">Add New Course</h2>

            <?php if(isset($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="#" method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold">Course Name</label>
                    <input type="text" class="form-control" name="course_name" placeholder="e.g. Corporate Law 101" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Assign Faculty</label>
                    <select class="form-select form-control" name="teacher_id" required>
                        <option value="" disabled selected>Select a Professor</option>
                        <?php while($t_info = $result_teachers->fetch_assoc()): ?>
                            <option value="<?php echo $t_info['id']; ?>"><?php echo htmlspecialchars($t_info['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" name="add_course">
                        <i class="fas fa-plus-circle me-2"></i> Add Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    
</body>
</html>
