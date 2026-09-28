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

if($_GET['teacher_id'])
{
    $t_id = $_GET['teacher_id'];

    $sql = "SELECT * FROM teacher WHERE id = '$t_id' ";

    $result = mysqli_query($data,$sql);
    
    $info = $result->fetch_assoc();
}


if(isset($_POST['update_teacher']))
{
    $id = $_POST['id'];

    $t_name = $_POST['name'];

    $t_desc = $_POST['description'];

    $file = $_FILES['image']['name'];
    $dst = "./image/".$file;
    $dst_db = "image/".$file;
    move_uploaded_file($_FILES['image']['tmp_name'],$dst);

    if($file)
    {
        $sql2 = "UPDATE teacher SET name = '$t_name',description = '$t_desc',image = '$dst_db'WHERE id = '$id' ";
    }
    else
    {
        $sql2 = "UPDATE teacher SET name = '$t_name',description = '$t_desc' WHERE id = '$id' ";
    }

    

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
    <?php include 'admin_css.php'; ?>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card shadow-sm border-0" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body p-5">
            <h2 class="mb-4 text-center fw-bold">Update Teacher Data</h2>

            <form action="#" method="POST" enctype="multipart/form-data">
                <input type="text" name="id" value="<?php echo htmlspecialchars($info['id']); ?>" hidden>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Teacher Name</label>
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($info['name']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">About Teacher</label>
                    <textarea class="form-control" rows="4" name="description" required><?php echo htmlspecialchars($info['description']); ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Current Image</label><br>
                    <img class="rounded shadow-sm" style="height: 120px; width: 120px; object-fit: cover;" src="<?php echo htmlspecialchars($info['image']); ?>">
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Upload New Image (Optional)</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" name="update_teacher">
                        <i class="fas fa-save me-2"></i> Update Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    
</body>
</html>
