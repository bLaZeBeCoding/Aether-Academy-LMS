<?php

session_start();

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

if(isset($_POST['add_teacher']))
{
    $t_name = $_POST['name'];
    
    $t_description = $_POST['description'];
    
    // getting image from folder
    $file = $_FILES['image']['name'];

    $dst = "./image/".$file;

    // to save
    $dst_db = "image/".$file;

    move_uploaded_file($_FILES['image']['tmp_name'],$dst);

    $sql = "INSERT INTO teacher (name,description,image) VALUES('$t_name','$t_description','$dst_db')" ;

    $result = mysqli_query($data,$sql);

    if($result)
    {
        header('location:admin_add_teacher.php');
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
            <h2 class="mb-4 text-center fw-bold">Add New Teacher</h2>

            <form action="#" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-bold">Teacher Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Profile Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" name="add_teacher">
                        <i class="fas fa-chalkboard-teacher me-2"></i> Add Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    
</body>
</html>
