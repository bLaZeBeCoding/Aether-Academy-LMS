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

$data = mysqli_connect('localhost','root','','collegepr',3307);

$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_POST['ajax']);

$error_msg = "";
$success_msg = "";

if(isset($_POST['add_teacher']) || isset($_POST['ajax']))
{
    $t_name = trim($_POST['name'] ?? '');
    $t_description = trim($_POST['description'] ?? '');

    // 1. Name Validation
    if(empty($t_name)) {
        $msg = "Please enter the faculty member's name.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    } elseif(strlen($t_name) < 2 || strlen($t_name) > 60) {
        $msg = "Teacher name must be between 2 and 60 characters.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    }
    // 2. Description Validation
    elseif(empty($t_description)) {
        $msg = "Please provide an academic bio or description.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    } elseif(strlen($t_description) < 10) {
        $msg = "Description must be at least 10 characters long.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    }
    // 3. Image File Validation
    elseif(!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $msg = "Please select a valid profile image for the faculty member.";
        if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
        $error_msg = $msg;
    } else {
        $file_name = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if(!in_array($ext, $allowed)) {
            $msg = "Invalid file type. Allowed image formats: JPG, JPEG, PNG, WEBP, GIF.";
            if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
            $error_msg = $msg;
        } else {
            // Create target folder if not exists
            if(!is_dir("./image")) {
                mkdir("./image", 0777, true);
            }

            // Generate clean, collision-free filename
            $clean_basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file_name, PATHINFO_FILENAME));
            $final_filename = 'teacher_' . time() . '_' . substr($clean_basename, 0, 15) . '.' . $ext;
            $dst = "./image/" . $final_filename;
            $dst_db = "image/" . $final_filename;

            if(move_uploaded_file($_FILES['image']['tmp_name'], $dst)) {
                $t_name_clean = mysqli_real_escape_string($data, $t_name);
                $t_desc_clean = mysqli_real_escape_string($data, $t_description);
                $dst_db_clean = mysqli_real_escape_string($data, $dst_db);

                $sql = "INSERT INTO teacher (name, description, image) VALUES('$t_name_clean', '$t_desc_clean', '$dst_db_clean')";
                $result = mysqli_query($data, $sql);

                if($result) {
                    $new_id = mysqli_insert_id($data);
                    $success_text = "Faculty member <strong>" . htmlspecialchars($t_name) . "</strong> has been added successfully to Aether Academy!";
                    if($is_ajax) {
                        echo json_encode([
                            "status" => "success",
                            "message" => $success_text,
                            "teacher_name" => $t_name,
                            "image" => $dst_db,
                            "id" => $new_id
                        ]);
                        exit;
                    }
                    $success_msg = $success_text;
                } else {
                    $msg = "Database error: Could not save teacher details.";
                    if($is_ajax) { echo json_encode(["status" => "error", "message" => $msg]); exit; }
                    $error_msg = $msg;
                }
            } else {
                $msg = "Failed to upload image file to the server.";
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
    <title>Add New Teacher | Admin Console</title>

    <?php include 'admin_css.php'; ?>
    <!-- SweetAlert2 for rich confirmation dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .image-preview-container {
            display: none;
            width: 130px;
            height: 130px;
            border-radius: 12px;
            border: 2px dashed var(--accent-orange);
            overflow: hidden;
            margin: 12px auto 0;
            position: relative;
            background: rgba(0,0,0,0.2);
        }
        .image-preview-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card shadow-sm border-0" style="max-width: 620px; margin: 0 auto;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <span class="badge py-2 px-3 rounded-pill mb-2" style="background: rgba(249,115,22,0.15); color: var(--accent-orange); border: 1px solid rgba(249,115,22,0.3); font-size: 0.82rem; font-weight: 600;">
                    <i class="fas fa-user-plus me-1"></i> Faculty Onboarding
                </span>
                <h2 class="fw-bold mb-1">Add New Teacher</h2>
                <p class="text-muted small">Register an instructor to appear across faculty listings and course rosters.</p>
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

            <form action="" method="POST" enctype="multipart/form-data" id="addTeacherForm" novalidate>
                <div class="mb-3">
                    <label class="form-label fw-bold">Teacher Name</label>
                    <input type="text" class="form-control" name="name" id="teacherName" placeholder="e.g. Prof. Harvey Specter" required>
                    <div class="invalid-feedback">Please enter a valid faculty name (at least 2 characters).</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Description / Academic Bio</label>
                    <textarea name="description" id="teacherDesc" class="form-control" rows="4" placeholder="Enter department, areas of expertise, and credentials..." required></textarea>
                    <div class="invalid-feedback">Description must be at least 10 characters long.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Profile Image</label>
                    <input type="file" class="form-control" name="image" id="teacherImage" accept="image/png, image/jpeg, image/jpg, image/webp, image/gif" required>
                    <div class="invalid-feedback">Please select an image file (JPG, PNG, WEBP).</div>
                    
                    <!-- Live Thumbnail Preview -->
                    <div class="image-preview-container" id="previewBox">
                        <img id="imagePreview" src="#" alt="Preview">
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold" id="submitBtn" name="add_teacher">
                        <i class="fas fa-chalkboard-teacher me-2"></i> Add Teacher
                    </button>
                    <a href="admin_view_teacher.php" class="btn btn-outline-secondary">
                        <i class="fas fa-users me-1"></i> View Teacher Roster
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- AJAX Submission & Confirmation Script -->
<script>
$(document).ready(function() {
    // 1. Live Image Preview
    $('#teacherImage').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#previewBox').fadeIn();
            }
            reader.readAsDataURL(file);
            $(this).removeClass('is-invalid');
        } else {
            $('#previewBox').hide();
        }
    });

    // Clear invalid state on typing
    $('#teacherName, #teacherDesc').on('input', function() {
        $(this).removeClass('is-invalid');
    });

    // 2. AJAX Submission Handler
    $('#addTeacherForm').on('submit', function(e) {
        e.preventDefault();

        var name = $('#teacherName').val().trim();
        var desc = $('#teacherDesc').val().trim();
        var fileInput = document.getElementById('teacherImage');
        var valid = true;

        // Reset states
        $('#ajaxAlert').addClass('d-none').removeClass('alert-success alert-danger');
        $('.form-control').removeClass('is-invalid');

        // Client validation
        if (name.length < 2) {
            $('#teacherName').addClass('is-invalid').focus();
            valid = false;
        }

        if (desc.length < 10) {
            $('#teacherDesc').addClass('is-invalid');
            if (valid) { $('#teacherDesc').focus(); }
            valid = false;
        }

        if (!fileInput.files || fileInput.files.length === 0) {
            $('#teacherImage').addClass('is-invalid');
            valid = false;
        }

        if (!valid) {
            return false;
        }

        // Prepare FormData for file upload
        var formData = new FormData(this);
        formData.append('ajax', '1');

        var $btn = $('#submitBtn');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Adding Teacher...');

        $.ajax({
            url: 'admin_add_teacher.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(res) {
                $btn.prop('disabled', false).html(originalText);

                if (res.status === 'success') {
                    // 1. Show in-page dismissible banner
                    $('#ajaxAlert').removeClass('d-none alert-danger').addClass('alert-success')
                        .html('<div class="d-flex justify-content-between align-items-center"><div><i class="fas fa-check-circle me-2"></i>' + res.message + '</div><a href="admin_view_teacher.php" class="btn btn-sm btn-success ms-3"><i class="fas fa-arrow-right me-1"></i> View Roster</a></div>');

                    // 2. Trigger SweetAlert2 confirmation popup
                    var isLight = document.documentElement.getAttribute('data-theme') === 'light';
                    Swal.fire({
                        title: 'Faculty Added Successfully!',
                        html: '<strong>' + $('<div>').text(res.teacher_name).html() + '</strong> has been added to the faculty list and will automatically appear centered on the homepage.',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#ea580c',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="fas fa-users me-1"></i> View Teacher Roster',
                        cancelButtonText: '<i class="fas fa-plus me-1"></i> Add Another Teacher',
                        background: isLight ? '#ffffff' : '#111827',
                        color: isLight ? '#0f172a' : '#f8fafc',
                        customClass: {
                            popup: 'rounded-4 shadow-lg border border-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'admin_view_teacher.php';
                        }
                    });

                    // 3. Reset form and image preview
                    $('#addTeacherForm')[0].reset();
                    $('#previewBox').hide();
                } else {
                    $('#ajaxAlert').removeClass('d-none alert-success').addClass('alert-danger')
                        .html('<i class="fas fa-exclamation-triangle me-2"></i><strong>Submission Rejected:</strong> ' + res.message);
                }
            },
            error: function() {
                $btn.prop('disabled', false).html(originalText);
                $('#ajaxAlert').removeClass('d-none alert-success').addClass('alert-danger')
                    .html('<i class="fas fa-exclamation-circle me-2"></i> Network or server error occurred. Please try again.');
            }
        });
    });
});
</script>

</body>
</html>
