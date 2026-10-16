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

$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_POST['ajax']);

$error_msg = "";
$success_msg = "";

if (isset($_POST['add_student']) || isset($_POST['ajax']))
{
    $username = trim($_POST['name'] ?? '');
    $user_email = trim($_POST['email'] ?? '');
    $user_phone = trim($_POST['phone'] ?? '');
    $user_password = $_POST['password'] ?? '';

    // 1. Username Check
    if(empty($username)) {
        $msg = "Please enter a username for the student.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    } elseif(strlen($username) < 2 || strlen($username) > 50) {
        $msg = "Username must be between 2 and 50 characters.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    }
    // 2. Strict Email RFC & Domain TLD Check
    elseif(empty($user_email)) {
        $msg = "Please enter the student's email address.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    } elseif(!filter_var($user_email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $user_email)) {
        $msg = "Please enter a valid email address with a complete domain (e.g. name@example.com). Incomplete domains like '@gmail' are rejected.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    }
    // 3. Phone Validation (10 digits starting with 6, 7, 8, or 9; reject repeating dummy digits)
    elseif(empty($user_phone)) {
        $msg = "Please enter the student's mobile phone number.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    } elseif(!preg_match('/^[6-9]\d{9}$/', $user_phone)) {
        $msg = "Phone number must be exactly 10 digits starting with 6, 7, 8, or 9.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    } elseif(preg_match('/^(\d)\1{9}$/', $user_phone)) {
        $msg = "Invalid phone number: all digits cannot be identical (e.g. 1111111111).";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    }
    // 4. Password Validation
    elseif(empty($user_password)) {
        $msg = "Please provide an initial account password.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    } elseif(strlen($user_password) < 6) {
        $msg = "Password must be at least 6 characters long.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    } else {
        // 5. Duplicate Check
        $username_clean = mysqli_real_escape_string($data, $username);
        $email_clean = mysqli_real_escape_string($data, $user_email);
        $phone_clean = mysqli_real_escape_string($data, $user_phone);

        $check = mysqli_query($data, "SELECT id FROM user WHERE username='$username_clean'");
        if(mysqli_num_rows($check) > 0) {
            $msg = "Username <strong>" . htmlspecialchars($username) . "</strong> is already registered. Please choose another username.";
            if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
            $error_msg = $msg;
        } else {
            // Hashing the password with BCrypt
            $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
            $usertype = "student";

            $sql = "INSERT INTO user(username, email, phone, usertype, password) VALUES ('$username_clean', '$email_clean', '$phone_clean', '$usertype', '$hashed_password')";
            $result = mysqli_query($data, $sql);

            if($result) {
                $new_id = mysqli_insert_id($data);
                $success_text = "Student account <strong>" . htmlspecialchars($username) . "</strong> created successfully!";
                if($is_ajax) {
                    echo json_encode([
                        "status" => "success",
                        "message" => $success_text,
                        "student_name" => $username,
                        "id" => $new_id
                    ]);
                    exit;
                }
                $success_msg = $success_text;
            } else {
                $msg = "Database error: Could not create student account. Please try again.";
                if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
                $error_msg = $msg;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student | Admin Console</title>

    <?php include 'admin_css.php'; ?>
    <!-- SweetAlert2 for rich confirmation dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <div class="text-center mb-4">
                <span class="badge py-2 px-3 rounded-pill mb-2" style="background: rgba(249,115,22,0.15); color: var(--accent-orange); border: 1px solid rgba(249,115,22,0.3); font-size: 0.82rem; font-weight: 600;">
                    <i class="fas fa-user-graduate me-1"></i> Student Onboarding
                </span>
                <h2 class="fw-bold mb-1">Add New Student</h2>
                <p class="text-muted small">Create an active student credential for portal access and course enrollment.</p>
            </div>

            <!-- In-page alert container -->
            <div id="ajaxAlert" class="alert d-none shadow-sm mb-4" role="alert"></div>

            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="addStudentForm" novalidate>
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" class="form-control" name="name" id="studentName" placeholder="e.g. John Doe" required>
                    <div class="invalid-feedback">Username must be between 2 and 50 characters.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control" name="email" id="studentEmail" placeholder="student@example.com" required>
                    <div class="invalid-feedback">Please enter a valid email with a complete domain (e.g. .com).</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input name="phone" class="form-control" type="tel" id="studentPhone" maxlength="10" inputmode="numeric" placeholder="10-digit mobile number" required>
                    <div class="invalid-feedback">Enter a valid 10-digit mobile number starting with 6, 7, 8, or 9 (no identical repeating digits).</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" class="form-control" name="password" id="studentPassword" placeholder="Initial account password (min. 6 characters)" minlength="6" required>
                    <div class="invalid-feedback">Password must be at least 6 characters long.</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold" id="submitBtn" name="add_student">
                        <i class="fas fa-user-plus me-2"></i> Add Student
                    </button>
                    <a href="view_student.php" class="btn btn-outline-secondary">
                        <i class="fas fa-users me-1"></i> View Student Roster
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Clear invalid state on typing
    $('#studentName, #studentEmail, #studentPhone, #studentPassword').on('input', function() {
        $(this).removeClass('is-invalid');
    });

    $('#addStudentForm').on('submit', function(e) {
        e.preventDefault();

        var username = $('#studentName').val().trim();
        var email = $('#studentEmail').val().trim();
        var phone = $('#studentPhone').val().trim();
        var pass = $('#studentPassword').val().trim();

        var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var phoneRegex = /^[6-9]\d{9}$/;
        var repeatingRegex = /^(\d)\1{9}$/;
        var valid = true;

        // Reset states
        $('#ajaxAlert').addClass('d-none').removeClass('alert-success alert-danger');
        $('.form-control').removeClass('is-invalid');

        // 1. Username check
        if (username.length < 2 || username.length > 50) {
            $('#studentName').addClass('is-invalid').focus();
            valid = false;
        }

        // 2. Email domain check (blocks incomplete domains like dfddfd@gmail)
        if (!emailRegex.test(email)) {
            $('#studentEmail').addClass('is-invalid');
            if (valid) { $('#studentEmail').focus(); }
            valid = false;
        }

        // 3. Phone check (blocks repeating digits like 1111111111 and ensures 6-9 start)
        if (!phoneRegex.test(phone) || repeatingRegex.test(phone)) {
            $('#studentPhone').addClass('is-invalid');
            if (valid) { $('#studentPhone').focus(); }
            valid = false;
        }

        // 4. Password check
        if (pass.length < 6) {
            $('#studentPassword').addClass('is-invalid');
            if (valid) { $('#studentPassword').focus(); }
            valid = false;
        }

        if (!valid) {
            return false;
        }

        var $btn = $('#submitBtn');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Registering Student...');

        $.ajax({
            url: 'add_student.php',
            type: 'POST',
            data: $(this).serialize() + '&ajax=1',
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(originalText);

                if (res.status === 'success') {
                    // 1. In-page alert banner
                    $('#ajaxAlert').removeClass('d-none alert-danger').addClass('alert-success')
                        .html('<div class="d-flex justify-content-between align-items-center"><div><i class="fas fa-check-circle me-2"></i>' + res.message + '</div><a href="view_student.php" class="btn btn-sm btn-success ms-3"><i class="fas fa-arrow-right me-1"></i> View Students</a></div>');

                    // 2. SweetAlert2 Confirmation Dialog
                    var isLight = document.documentElement.getAttribute('data-theme') === 'light';
                    Swal.fire({
                        title: 'Student Account Created!',
                        html: 'Student <strong>' + $('<div>').text(res.student_name).html() + '</strong> has been registered successfully.',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#ea580c',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="fas fa-users me-1"></i> View All Students',
                        cancelButtonText: '<i class="fas fa-plus me-1"></i> Add Another Student',
                        background: isLight ? '#ffffff' : '#111827',
                        color: isLight ? '#0f172a' : '#f8fafc',
                        customClass: {
                            popup: 'rounded-4 shadow-lg border border-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'view_student.php';
                        }
                    });

                    // 3. Reset form
                    $('#addStudentForm')[0].reset();
                } else {
                    $('#ajaxAlert').removeClass('d-none alert-success').addClass('alert-danger')
                        .html('<i class="fas fa-exclamation-triangle me-2"></i><strong>Registration Rejected:</strong> ' + res.message);
                }
            },
            error: function() {
                $btn.prop('disabled', false).html(originalText);
                $('#ajaxAlert').removeClass('d-none alert-success').addClass('alert-danger')
                    .html('<i class="fas fa-times-circle me-2"></i> Network or server error occurred. Please try again.');
            }
        });
    });
});
</script>

</body>
</html>
