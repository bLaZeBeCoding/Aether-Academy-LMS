<?php
session_start();

// if no username, go to (login.php)
if(!isset($_SESSION['username']))
{
    header("location:login.php");
    exit;
}
// admin type redirected to login.php
elseif($_SESSION['usertype']=='admin')
{
    header("location:login.php");
    exit;
}

$data = mysqli_connect('localhost','root','','collegepr',3307);

$name = $_SESSION['username'];

$error_msg = "";
$success_msg = "";

if(isset($_POST['update_profile']))
{
    $s_email = trim($_POST['email'] ?? '');
    $s_phone = trim($_POST['phone'] ?? '');
    $s_password = trim($_POST['password'] ?? '');

    // 1. Email Validation (Strict RFC & TLD domain check)
    if(empty($s_email)) {
        $error_msg = "Email address cannot be empty.";
    } elseif(!filter_var($s_email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $s_email)) {
        $error_msg = "Please enter a valid email address with a complete domain (e.g. name@example.com). Incomplete domains like '@gmail' are rejected.";
    }
    // 2. Phone Validation (10 digits starting with 6, 7, 8, or 9; reject repeating dummy digits)
    elseif(empty($s_phone)) {
        $error_msg = "Mobile phone number cannot be empty.";
    } elseif(!preg_match('/^[6-9]\d{9}$/', $s_phone)) {
        $error_msg = "Phone number must be exactly 10 digits starting with 6, 7, 8, or 9.";
    } elseif(preg_match('/^(\d)\1{9}$/', $s_phone)) {
        $error_msg = "Invalid phone number: all digits cannot be identical (e.g. 1111111111).";
    }
    // 3. Password Validation (if updating)
    elseif(!empty($s_password) && strlen($s_password) < 6) {
        $error_msg = "New password must be at least 6 characters long.";
    } else {
        $email_clean = mysqli_real_escape_string($data, $s_email);
        $phone_clean = mysqli_real_escape_string($data, $s_phone);

        if(!empty($s_password)) {
            $hashed_password = password_hash($s_password, PASSWORD_DEFAULT);
            $query = "UPDATE user SET email = '$email_clean', phone = '$phone_clean', password = '$hashed_password' WHERE username = '$name'";
        } else {
            $query = "UPDATE user SET email = '$email_clean', phone = '$phone_clean' WHERE username = '$name'";
        }

        $result2 = mysqli_query($data, $query);

        if($result2) {
            $success_msg = "Profile updated successfully!";
        } else {
            $error_msg = "Failed to update profile. Please try again.";
        }
    }
}

// Fetch user data
$sql = "SELECT * FROM user WHERE username = '$name'";
$result = mysqli_query($data, $sql);
$info = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Update Profile</title>
    <?php include 'student_css.php'; ?>
    <style>
        input[type="tel"]::-webkit-outer-spin-button,
        input[type="tel"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>
<body>

<?php include 'student_sidebar.php'; ?>

<div class="content">
    <div class="card shadow-sm border-0" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body p-5">
            <h2 class="mb-4 text-center fw-bold">Update Profile</h2>

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

            <form action="" method="POST" id="studentProfileForm" novalidate>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control" name="email" id="emailInput" value="<?php echo htmlspecialchars($info['email'] ?? ''); ?>" required placeholder="name@example.com">
                    <div class="invalid-feedback">Please enter a valid email with a domain extension (e.g. .com).</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input type="tel" class="form-control" name="phone" id="phoneInput" maxlength="10" inputmode="numeric" value="<?php echo htmlspecialchars($info['phone'] ?? ''); ?>" required placeholder="10-digit mobile number">
                    <div class="invalid-feedback">Enter a valid 10-digit mobile number starting with 6, 7, 8, or 9 (no identical repeating digits).</div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" class="form-control" name="password" id="passwordInput" placeholder="Leave blank to keep current password" minlength="6">
                    <div class="form-text text-muted">Leave blank to keep current password. Minimum 6 characters if updating.</div>
                    <div class="invalid-feedback">Password must be at least 6 characters long.</div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold" name="update_profile">
                        <i class="fas fa-save me-2"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('studentProfileForm').addEventListener('submit', function(e) {
    let email = document.getElementById('emailInput').value.trim();
    let phone = document.getElementById('phoneInput').value.trim();
    let pass = document.getElementById('passwordInput').value.trim();
    let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    let phoneRegex = /^[6-9]\d{9}$/;
    let valid = true;

    document.getElementById('emailInput').classList.remove('is-invalid');
    document.getElementById('phoneInput').classList.remove('is-invalid');
    document.getElementById('passwordInput').classList.remove('is-invalid');

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
