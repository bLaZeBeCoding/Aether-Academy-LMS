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


$data = mysqli_connect("localhost","root","","collegepr",3307);

// pulling id
$id = $_GET['student_id'];

$sql = "SELECT * FROM user WHERE id = '$id' ";

$result = mysqli_query($data,$sql);

$info = $result->fetch_assoc();


// form submit on click (name='update')

if (isset($_POST['update']))
{
    $name = mysqli_real_escape_string($data, trim($_POST['name']));
    $email = mysqli_real_escape_string($data, trim($_POST['email']));
    $phone = mysqli_real_escape_string($data, trim($_POST['phone']));
    $password = trim($_POST['password']);

    if (!empty($password)) {
        // Hashing the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE user SET username = '$name', email = '$email', phone = '$phone', password = '$hashed_password' WHERE id = '$id'";
    } else {
        // Do not update password if left blank
        $query = "UPDATE user SET username = '$name', email = '$email', phone = '$phone' WHERE id = '$id'";
    }

    $result2 = mysqli_query($data,$query);

    if($result2)
    {
        header("location:view_student.php");
        exit;
    }
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student Details</title>
    <?php include 'admin_css.php'; ?>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card shadow-sm border-0" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body p-5">
            <h2 class="mb-4 text-center fw-bold">Update Student Details</h2>

            <form action="#" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($info['username']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($info['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input name="phone" class="form-control" type="tel" pattern="^\d{10}$" required title="Enter a valid 10 digit phone number" value="<?php echo htmlspecialchars($info['phone']); ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" name="update">
                        <i class="fas fa-save me-2"></i> Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    
</body>
</html>
