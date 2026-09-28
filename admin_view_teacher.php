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

$data = mysqli_connect('localhost','root','','collegepr',3307);

$sql = "SELECT * FROM teacher";

$result = mysqli_query($data,$sql);


if($_GET['teacher_id'])
{
    $t_id = $_GET['teacher_id'];

    $sql2 = "DELETE FROM teacher WHERE id = '$t_id' ";

    $result2 = mysqli_query($data,$sql2);

    if($result2)
    {
        header('location:admin_view_teacher.php');
    }
}


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
            <h1 class="mb-4">View All Teacher Data</h1>

            <div class="table-responsive">
                <table id="teacherTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Teacher Name</th>
                            <th>About Teacher</th>
                            <th>Teacher Image</th>
                            <th>Delete</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($info=$result->fetch_assoc())
                        {
                        ?>
                        <tr>
                            <td class="fw-bold">
                                <?php echo "{$info['name']}" ?>
                            </td>
                            <td>
                            <?php echo "{$info['description']}" ?>
                            </td>
                            <td>
                                <img class="rounded shadow-sm" style="height: 100px; width: 100px; object-fit: cover;" src="<?php echo "{$info['image']}" ?>">
                            </td>
                            <td>
                            <?php
                            echo "<a onClick=\"javascript:return confirm('Are You Sure You Want To Delete This ?')\" class='btn btn-danger btn-sm' href='admin_view_teacher.php?teacher_id={$info['id']}'> <i class='fas fa-trash'></i> Delete </a>";
                            ?>
                            </td>
                            <td>
                                <?php
                                echo "<a href='admin_update_teacher.php?teacher_id={$info['id']}' class='btn btn-primary btn-sm'> <i class='fas fa-edit'></i> Update </a>";
                                ?>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#teacherTable').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Quick Search:"
            }
        });
    });
</script>
    
</body>
</html>
