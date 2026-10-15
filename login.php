<?php header("X-Author: Uday Praveen (PRN: 23030124196)"); ?>
<!--
================================================================
* Aether Academy - Student Management System
* Architect & Lead Developer: Uday Praveen (PRN: 23030124196)
* Unauthorized reproduction or uncredited use is strictly prohibited.
================================================================
-->
<?php
error_reporting(0);
session_start();

// If already logged in, bounce them to their dashboard
if(isset($_SESSION['username'])) {
    if($_SESSION['usertype'] == 'admin') {
        header("location:adminhome.php");
    } else {
        header("location:studenthome.php");
    }
    exit;
}

$timeout_msg = false;
if(isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $timeout_msg = true;
}

$message = '';
if(isset($_SESSION['loginMessage'])) {
    $message = $_SESSION['loginMessage'];
    session_destroy(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        (function() {
            var theme = localStorage.getItem('aether-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Aether Academy</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.95)), url('school1.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #f8fafc;
        }
        .login-card {
            background: rgba(30, 41, 59, 0.95); /* Slate 800 */
            backdrop-filter: blur(15px);
            border: 1px solid #334155;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }
        .login-card .logo-area {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-card .logo-area img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #06b6d4;
            margin-bottom: 15px;
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);
        }
        .login-card .logo-area h2 {
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 5px;
        }
        .login-card .logo-area p {
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .form-control {
            background-color: #0f172a !important;
            border: 1px solid #334155;
            border-left: none;
            color: #f8fafc !important;
            padding: 12px 15px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(6, 182, 212, 0.25);
            border-color: #06b6d4;
        }
        .input-group-text {
            background-color: #0f172a;
            border: 1px solid #334155;
            border-right: none;
            color: #06b6d4;
        }
        .form-control::placeholder {
            color: #64748b;
        }
        .btn-neon {
            background: linear-gradient(45deg, #8b5cf6, #06b6d4);
            border: none;
            color: white;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-neon:hover {
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.5);
            color: white;
            transform: translateY(-2px);
        }
        .back-link {
            text-align: center;
            display: block;
            margin-top: 20px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #06b6d4;
        }
    
        /* Theme Toggle Button */
        .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #06b6d4;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .theme-toggle-btn:hover {
            transform: scale(1.1) rotate(15deg);
            border-color: #06b6d4;
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.4);
            color: #38bdf8;
        }
        [data-theme="light"] .theme-toggle-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #f59e0b;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        [data-theme="light"] .theme-toggle-btn:hover {
            border-color: #f59e0b;
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);
            color: #d97706;
        }
        [data-theme="light"] body {
            background: linear-gradient(rgba(15, 23, 42, 0.5), rgba(15, 23, 42, 0.7)), url('school1.jpg') no-repeat center center/cover !important;
            color: #0f172a !important;
        }
        [data-theme="light"] .login-card {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08) !important;
        }
        [data-theme="light"] .login-card .logo-area h2 {
            color: #0f172a !important;
        }
        [data-theme="light"] .login-card .logo-area p {
            color: #64748b !important;
        }
        [data-theme="light"] .form-label {
            color: #1e293b !important;
        }
        [data-theme="light"] .form-control {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-left: none !important;
            color: #0f172a !important;
        }
        [data-theme="light"] .input-group-text {
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            border-right: none !important;
            color: #0284c7 !important;
        }
        [data-theme="light"] .back-link {
            color: #64748b !important;
        }
        [data-theme="light"] .back-link:hover {
            color: #0284c7 !important;
        }

    
        [data-theme="light"] .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid #cbd5e1 !important;
            color: #ea580c !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }
        [data-theme="light"] .theme-toggle-btn:hover {
            border-color: #ea580c !important;
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.4) !important;
            color: #c2410c !important;
        }
        [data-theme="light"] .btn-neon {
            background: linear-gradient(135deg, #f97316, #ea580c) !important;
            box-shadow: 0 4px 15px rgba(234, 88, 12, 0.35) !important;
        }
        [data-theme="light"] .btn-neon:hover {
            background: linear-gradient(135deg, #ea580c, #c2410c) !important;
        }
        [data-theme="light"] .input-group-text {
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-right: none !important;
            color: #ea580c !important;
        }
        [data-theme="light"] .form-control:focus {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 0.25rem rgba(249, 115, 22, 0.2) !important;
        }
        [data-theme="light"] .back-link:hover {
            color: #ea580c !important;
        }
        [data-theme="light"] .login-card .logo-area img {
            border-color: #f97316 !important;
            box-shadow: 0 0 15px rgba(249, 115, 22, 0.3) !important;
        }

    </style>
</head>
<body>

    <!-- Floating Theme Toggle -->
    <div style="position: fixed; top: 75px; right: 25px; z-index: 1060;">
        <button class="theme-toggle-btn shadow" title="Toggle Light/Dark Theme" type="button" onclick="toggleTheme()">
            <i class="fas fa-moon"></i>
        </button>
    </div>


    <?php if(isset($_SESSION['easter_egg'])): ?>
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.8);" id="easterEggModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #0f172a; border: 2px solid #06b6d4; box-shadow: 0 0 30px rgba(6, 182, 212, 0.4);">
          <div class="modal-body text-center p-5">
            <h2 style="color: #06b6d4; font-weight: bold; text-shadow: 0 0 10px #06b6d4; letter-spacing: 2px;">AETHER ACADEMY</h2>
            <p class="text-white mt-3 fs-5">Architected & Developed by</p>
            <h3 style="color: #8b5cf6; font-weight: bold; font-size: 2.2rem; margin: 15px 0;">Uday Praveen</h3>
            <p class="text-muted" style="font-size: 1.1rem;">PRN: 23030124196 | Aether Academy</p>
            <button type="button" class="btn btn-neon mt-4 px-5 py-2" onclick="document.getElementById('easterEggModal').style.display='none'">Acknowledge</button>
          </div>
        </div>
      </div>
    </div>
    <?php unset($_SESSION['easter_egg']); endif; ?>

    <?php if($timeout_msg): ?>
    <div id="timeoutAlert" class="position-absolute shadow-lg" style="top: 75px; right: 25px; background-color: rgba(220, 38, 38, 0.85); backdrop-filter: blur(10px); color: white; padding: 18px 25px; border-radius: 8px; font-weight: 600; font-size: 1rem; z-index: 1050; border: 1px solid rgba(239, 68, 68, 0.5); overflow: hidden; min-width: 320px;">
        <div style="display: flex; align-items: center; gap: 12px; position: relative; z-index: 2; margin-bottom: 2px;">
            <i class="fas fa-clock fs-4"></i>
            <span>You were logged out due to inactivity</span>
        </div>
        <!-- Time Slider -->
        <div id="timeSlider" style="position: absolute; bottom: 0; left: 0; height: 4px; background-color: rgba(255, 255, 255, 0.8); width: 100%; transition: width 7s linear;"></div>
    </div>
    <script>
        // Trigger the slider animation right after render
        setTimeout(function() {
            var slider = document.getElementById("timeSlider");
            if(slider) {
                slider.style.width = "0%";
            }
        }, 50);

        // Remove the toast after 7 seconds
        setTimeout(function() {
            var alertBox = document.getElementById("timeoutAlert");
            if(alertBox) {
                alertBox.style.transition = "opacity 0.5s ease-out, transform 0.5s ease-out";
                alertBox.style.opacity = "0";
                alertBox.style.transform = "translateY(-10px)";
                setTimeout(function() { alertBox.remove(); }, 500);
            }
        }, 7000);
    </script>
    <?php endif; ?>

    <div class="login-card">
        <div class="logo-area">
            <img src="logo.png" alt="Aether Academy Logo">
            <h2>Welcome Back</h2>
            <p>Login to your account (Admin or Student)</p>
        </div>

        <?php if($message != ''): ?>
            <div class="alert alert-danger text-center bg-transparent border-danger text-danger shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="login_check.php" method="POST">
            <div class="mb-4">
                <label class="form-label fw-bold">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" name="username" placeholder="Enter your username" required autofocus>
                </div>
            </div>

            <div class="mb-5">
                <label class="form-label fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
                </div>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" name="submit" class="btn btn-neon btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>
            </div>
        </form>

        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left me-1"></i> Back to Homepage
        </a>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  console.log('%c Aether Academy %c Developed by Uday Praveen ', 'background: #ea580c; color: #fff; border-radius: 3px 0 0 3px; padding: 2px 5px; font-weight: bold;', 'background: #0f172a; color: #fff; border-radius: 0 3px 3px 0; padding: 2px 5px;');
</script>

<script>
    function toggleTheme() {
        var current = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', current);
        localStorage.setItem('aether-theme', current);
        updateThemeIcons(current);
    }
    function updateThemeIcons(theme) {
        var icons = document.querySelectorAll('.theme-toggle-btn i');
        icons.forEach(function(icon) {
            if(theme === 'light') {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        var theme = localStorage.getItem('aether-theme') || 'dark';
        updateThemeIcons(theme);
    });
</script>

</body>
</html>
