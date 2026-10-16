<?php
session_start();

// if no username, go to (login.php)
if(!isset($_SESSION['username']))
{
    header("location:login.php");
    exit;
}
// student type redirected to login.php
elseif($_SESSION['usertype']=='student')
{
    header("location:login.php");
    exit;
}

$data = mysqli_connect("localhost","root","","collegepr",3307);

// pulling id
$id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

$sql = "SELECT * FROM user WHERE id = '$id'";
$result = mysqli_query($data,$sql);
$info = $result ? $result->fetch_assoc() : null;

if(!$info) {
    header("location:view_student.php");
    exit;
}

$error_msg = "";
$success_msg = "";

if (isset($_POST['update']))
{
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 1. Username Check
    if(empty($name)) {
        $error_msg = "Username cannot be empty.";
    } elseif(strlen($name) < 2 || strlen($name) > 50) {
        $error_msg = "Username must be between 2 and 50 characters.";
    }
    // 2. Strict Email RFC & Domain TLD Check
    elseif(empty($email)) {
        $error_msg = "Email address cannot be empty.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $email)) {
        $error_msg = "Please enter a valid email address with a complete domain (e.g. name@example.com). Incomplete domains like '@gmail' are rejected.";
    }
    // 3. Phone Validation (10 digits starting with 6-9; reject dummy repeating digits)
    elseif(empty($phone)) {
        $error_msg = "Phone number cannot be empty.";
    } elseif(!preg_match('/^[6-9]\d{9}$/', $phone)) {
        $error_msg = "Phone number must be exactly 10 digits starting with 6, 7, 8, or 9.";
    } elseif(preg_match('/^(\d)\1{9}$/', $phone)) {
        $error_msg = "Invalid phone number: all digits cannot be identical (e.g. 4444444444).";
    }
    // 4. Password Validation (if updating)
    elseif(!empty($password) && strlen($password) < 6) {
        $error_msg = "New password must be at least 6 characters long.";
    } else {
        $name_clean = mysqli_real_escape_string($data, $name);
        $email_clean = mysqli_real_escape_string($data, $email);
        $phone_clean = mysqli_real_escape_string($data, $phone);

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE user SET username = '$name_clean', email = '$email_clean', phone = '$phone_clean', password = '$hashed_password' WHERE id = '$id'";
        } else {
            $query = "UPDATE user SET username = '$name_clean', email = '$email_clean', phone = '$phone_clean' WHERE id = '$id'";
        }

        $result2 = mysqli_query($data,$query);

        if($result2)
        {
            $success_msg = "Student details updated successfully!";
            $refresh = mysqli_query($data, "SELECT * FROM user WHERE id = '$id'");
            $info = $refresh->fetch_assoc();
        } else {
            $error_msg = "Failed to update student details. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student Details | Admin Dashboard</title>
    <?php include 'admin_css.php'; ?>
    <style>
        input[type="tel"]::-webkit-outer-spin-button,
        input[type="tel"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card shadow-sm border-0" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body p-5">
            <h2 class="mb-4 text-center fw-bold">Update Student Details</h2>

            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error_msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($success_msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="adminUpdateStudentForm" novalidate>
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" class="form-control" name="name" id="usernameInput" value="<?php echo htmlspecialchars($info['username'] ?? ''); ?>" required placeholder="Full student username">
                    <div class="invalid-feedback">Username must be between 2 and 50 characters.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control" name="email" id="emailInput" value="<?php echo htmlspecialchars($info['email'] ?? ''); ?>" required placeholder="name@example.com">
                    <div class="invalid-feedback">Please enter a valid email with a complete domain (e.g. .com).</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input name="phone" class="form-control" type="tel" id="phoneInput" maxlength="10" inputmode="numeric" required placeholder="10-digit mobile number" value="<?php echo htmlspecialchars($info['phone'] ?? ''); ?>">
                    <div class="invalid-feedback">Enter a valid 10-digit mobile number starting with 6, 7, 8, or 9 (no identical repeating digits).</div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" class="form-control" name="password" id="passwordInput" placeholder="Leave blank to keep current password" minlength="6">
                    <div class="form-text text-muted">Leave blank to keep current password. Minimum 6 characters if updating.</div>
                    <div class="invalid-feedback">Password must be at least 6 characters long.</div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold" name="update">
                        <i class="fas fa-save me-2"></i> Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('adminUpdateStudentForm').addEventListener('submit', function(e) {
    let username = document.getElementById('usernameInput').value.trim();
    let email = document.getElementById('emailInput').value.trim();
    let phone = document.getElementById('phoneInput').value.trim();
    let pass = document.getElementById('passwordInput').value.trim();
    let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    let phoneRegex = /^[6-9]\d{9}$/;
    let valid = true;

    document.getElementById('usernameInput').classList.remove('is-invalid');
    document.getElementById('emailInput').classList.remove('is-invalid');
    document.getElementById('phoneInput').classList.remove('is-invalid');
    document.getElementById('passwordInput').classList.remove('is-invalid');

    if(username.length < 2 || username.length > 50) {
        document.getElementById('usernameInput').classList.add('is-invalid');
        valid = false;
    }

    if(!emailRegex.test(email)) {
        document.getElementById('emailInput').classList.add('is-invalid');
        valid = false;
    }

    if(!phoneRegex.test(phone) || /^(\d)\1{9}$/.test(phone)) {
        document.getElementById('phoneInput').classList.add('is-invalid');
        valid = false;
    }

    if(pass !== "" && pass.length < 6) {
        document.getElementById('passwordInput').classList.add('is-invalid');
        valid = false;
    }

    if(!valid) {
        e.preventDefault();
    }
});
</script>

</body>
</html>
