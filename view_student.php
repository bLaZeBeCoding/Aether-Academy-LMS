<?php

session_start();
error_reporting(0);

// if no username, go to (login.php)
if(!isset($_SESSION['username']))
{
    header("location:login.php");
}

// student type redirected to login.php
elseif($_SESSION['usertype']=='student')
{
    header("location:login.php");
}


$data = mysqli_connect("localhost","root","","collegepr",3307);

$sql = "SELECT * FROM user WHERE usertype = 'student'";

$result = mysqli_query($data,$sql);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <?php

include 'admin_css.php';

?>

</head>
<body>

<?php
    include 'admin_sidebar.php';
?>

<div class="content">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h1 class="mb-4">Student Data</h1>
            
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <div class="table-responsive">
                <table id="studentTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>UserName</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Password</th>
                            <th>Delete</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($info=$result->fetch_assoc()) { ?>
                        <tr>
                            <td class="fw-bold">
                                <?php echo htmlspecialchars($info['username']);?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($info['email']);?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($info['phone']);?>
                            </td>
                            <td>
                                <span class="text-muted fst-italic">Encrypted</span>
                            </td>
                            <td>
                                <?php echo "<a class='btn btn-danger btn-sm' onClick=\" javascript:return confirm('Are You Sure You Want To Delete This?');\" href='delete.php?student_id={$info['id']}'> <i class='fas fa-trash'></i> Delete </a>";?>
                            </td>
                            <td>
                                <?php echo "<a class='btn btn-primary btn-sm' href='update_student.php?student_id={$info['id']}'> <i class='fas fa-edit'></i> Update </a>";?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#studentTable').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Quick Search:"
            }
        });
    });
</script>
    
</body>
</html>
