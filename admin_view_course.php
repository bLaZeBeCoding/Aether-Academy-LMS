<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['usertype'] == 'student') {
    header("location:login.php");
    exit;
}

$data = mysqli_connect("localhost", "root", "", "collegepr", 3307);

// Handle deletion
if(isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    mysqli_query($data, "DELETE FROM course WHERE id = '$del_id'");
    header("location:admin_view_course.php");
    exit;
}

// Fetch all courses with their assigned teacher's name
$sql = "SELECT course.id, course.name as course_name, teacher.name as teacher_name 
        FROM course 
        JOIN teacher ON course.teacher_id = teacher.id";
$result = mysqli_query($data, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Courses</title>
    <?php include 'admin_css.php'; ?>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h1 class="mb-4">All Courses</h1>

            <div class="table-responsive">
                <table id="courseTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Course Name</th>
                            <th>Faculty Assigned</th>
                            <th>Manage Enrollments</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($info = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($info['course_name']); ?></td>
                            <td><i class="fas fa-user-tie text-muted me-2"></i><?php echo htmlspecialchars($info['teacher_name']); ?></td>
                            <td>
                                <a href="admin_enroll.php?course_id=<?php echo $info['id']; ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-user-plus"></i> Enroll Students
                                </a>
                            </td>
                            <td>
                                <a href="admin_view_course.php?delete_id=<?php echo $info['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');">
                                    <i class="fas fa-trash"></i> Delete
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

<script>
    $(document).ready(function() {
        $('#courseTable').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Quick Search:"
            }
        });
    });
</script>
    
</body>
</html>
