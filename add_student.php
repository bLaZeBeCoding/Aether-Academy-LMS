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

if (isset($_POST['add_student']))
{
    // 1. INPUT VALIDATION & ESCAPING
    // Escaping all inputs to prevent SQL Injection when admins enter arbitrary data
    $username = mysqli_real_escape_string($data, trim($_POST['name']));
    $user_email = mysqli_real_escape_string($data, trim($_POST['email']));
    $user_phone = mysqli_real_escape_string($data, trim($_POST['phone']));
    
    // 2. PASSWORD HASHING (Security)
    // We do NOT store plaintext passwords. We use password_hash() which uses the robust BCrypt algorithm.
    // It automatically generates a secure salt and a 60-character hash.
    $user_password = $_POST['password'];
    $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

    // Hardcoding the usertype to 'student' to enforce Role Based Access Control logic
    $usertype = "student";

    // 3. DUPLICATE CHECK
    // Check if the username already exists in the database to prevent duplicate accounts
    $check = "SELECT * FROM user WHERE username='$username'";
    $check_user = mysqli_query($data,$check);
    $row_count = mysqli_num_rows($check_user);

    if($row_count > 0)
    {
        // 4a. FEEDBACK: Duplicate User
        echo "<script type='text/javascript'>
            alert('Username Already Exists! Try Another One.')
            </script>";
    }
    else
    {
        // 4b. DATABASE EXECUTION (CRUD: Create)
        // Insert with the HASHED password, never plaintext
        $sql = "INSERT INTO user(username,email,phone,usertype,password) VALUES ('$username','$user_email','$user_phone','$usertype','$hashed_password')";
        $result = mysqli_query($data,$sql);

        if($result)
        {
            echo "<script type='text/javascript'>
            alert('Data Upload Success!')
            </script>";
        }
        else
        {
            echo "Upload Failed!";
        }
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
            <h2 class="mb-4 text-center fw-bold">Add New Student</h2>

            <form action="#" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input name="phone" class="form-control" type="tel" pattern="^\d{10}$" required title="Enter a valid 10 digit phone number">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="text" class="form-control" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" name="add_student">
                        <i class="fas fa-user-plus me-2"></i> Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    
</body>
</html>
