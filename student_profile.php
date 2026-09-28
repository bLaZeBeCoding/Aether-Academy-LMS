<?php

session_start();

// if no username, go to (login.php)
if(!isset($_SESSION['username']))
{
    header("location:login.php");
}

// admin type redirected to login.php
elseif($_SESSION['usertype']=='admin')
{
    header("location:login.php");
}


$data = mysqli_connect('localhost','root','','collegepr',3307);

$name = $_SESSION['username'];

$sql = "SELECT * FROM user WHERE username = '$name' " ;

$result = mysqli_query($data,$sql);

$info = mysqli_fetch_assoc($result);


if(isset($_POST['update_profile']))
{
    // Prevent SQL Injection
    $s_email = mysqli_real_escape_string($data, trim($_POST['email']));
    $s_phone = mysqli_real_escape_string($data, trim($_POST['phone']));
    $s_password = trim($_POST['password']);
    
    if (!empty($s_password)) {
        // Hashing the new password
        $hashed_password = password_hash($s_password, PASSWORD_DEFAULT);
        $sql2 = "UPDATE user SET email = '$s_email', phone = '$s_phone', password = '$hashed_password' WHERE username = '$name' ";
    } else {
        // Do not update password if left blank
        $sql2 = "UPDATE user SET email = '$s_email', phone = '$s_phone' WHERE username = '$name' ";
    }
    
    $result2 = mysqli_query($data,$sql2);

    if($result2)
    {
        header('location:student_profile.php');
        exit;
    }
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

<?php include 'student_css.php'; ?>
</head>
<body>

<?php include 'student_sidebar.php'; ?>

<div class="content">
    <div class="card shadow-sm border-0" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body p-5">
            <h2 class="mb-4 text-center fw-bold">Update Profile</h2>

            <form action="#" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($info['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input type="number" class="form-control" name="phone" value="<?php echo htmlspecialchars($info['phone']); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" name="update_profile">
                        <i class="fas fa-save me-2"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    
</body>
</html>
