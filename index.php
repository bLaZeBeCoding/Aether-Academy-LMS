<?php header("X-Author: Uday Praveen (PRN: 23030124196)"); ?>
<?php

error_reporting(0);
session_start();

$is_logged_in = isset($_SESSION['username']);
$usertype = isset($_SESSION['usertype']) ? $_SESSION['usertype'] : '';
$username_session = isset($_SESSION['username']) ? $_SESSION['username'] : '';

$message = '';
if(isset($_SESSION['message']))
{
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$data = mysqli_connect('localhost','root','','collegepr',3307);
$sql = "SELECT * FROM teacher";
$result = mysqli_query($data,$sql);

?>

<!--
================================================================
* Aether Academy - Student Management System
* Architect & Lead Developer: Uday Praveen (PRN: 23030124196)
* Unauthorized reproduction or uncredited use is strictly prohibited.
================================================================
-->
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
    <title>Aether Academy | Academic LMS & Student Management Portal</title>
    
    <!-- Google Fonts: Technical Pro & Academic Standard -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-canvas: #0b1120;
            --text-heading: #f8fafc;
            --text-sub: #cbd5e1;
            --text-muted: #94a3b8;
            --navbar-bg: rgba(3, 7, 18, 0.95);
            --navbar-border: #1f2937;
            --card-bg: #111827;
            --card-border: #1f2937;
            --accent-orange: #f97316;
            --accent-orange-hover: #ea580c;
            --signout-red: #dc2626;
            --pillar-card-bg: rgba(255, 255, 255, 0.03);
            --pillar-border: #1f2937;
            --footer-bg: #030712;
            --footer-border: #1e293b;
        }

        [data-theme="light"] {
            --bg-canvas: #f8fafc;
            --text-heading: #0f172a;
            --text-sub: #1e293b;
            --text-muted: #64748b;
            --navbar-bg: rgba(255, 255, 255, 0.97);
            --navbar-border: #e2e8f0;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --accent-orange: #ea580c;
            --accent-orange-hover: #c2410c;
            --signout-red: #dc2626;
            --pillar-card-bg: #ffffff;
            --pillar-border: #fed7aa;
            --footer-bg: #ffffff;
            --footer-border: #e2e8f0;
        }

        html {
            scroll-behavior: smooth;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            letter-spacing: -0.01em;
        }

        body {
            background-color: var(--bg-canvas) !important;
            color: var(--text-sub) !important;
            transition: background-color 0.25s ease, color 0.25s ease;
            overflow-x: hidden;
        }

        /* High-Contrast Universal Typography (Fixes unreadable dark mode) */
        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6 {
            color: var(--text-heading) !important;
            font-weight: 700;
        }

        p, .lead, .card-text {
            color: var(--text-sub) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .small, small {
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .text-muted,
        [data-theme="dark"] .small,
        [data-theme="dark"] small {
            color: #94a3b8 !important;
        }

        [data-theme="dark"] p,
        [data-theme="dark"] .card-text,
        [data-theme="dark"] .about-text {
            color: #cbd5e1 !important;
        }

        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6 {
            color: #f8fafc !important;
        }

        [data-theme="light"] .text-muted,
        [data-theme="light"] .small,
        [data-theme="light"] small {
            color: #64748b !important;
        }

        [data-theme="light"] p,
        [data-theme="light"] .card-text,
        [data-theme="light"] .about-text {
            color: #334155 !important;
        }

        /* Navbar Styling */
        .navbar {
            background-color: var(--navbar-bg) !important;
            backdrop-filter: blur(12px);
            border-bottom: 2px solid var(--accent-orange);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            padding: 12px 0;
            transition: all 0.25s ease;
        }
        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-heading) !important;
            font-size: 1.25rem;
        }
        .navbar-brand span {
            color: var(--accent-orange);
        }
        .nav-link {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-sub) !important;
            padding: 8px 16px !important;
            transition: color 0.2s ease;
        }
        .nav-link:hover {
            color: var(--accent-orange) !important;
        }
        .nav-link.active-dash {
            color: var(--accent-orange) !important;
        }

        /* Theme Toggle Button */
        .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--accent-orange);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .theme-toggle-btn:hover {
            transform: scale(1.08) rotate(15deg);
            border-color: var(--accent-orange);
            box-shadow: 0 0 12px rgba(249, 115, 22, 0.4);
        }
        [data-theme="light"] .theme-toggle-btn {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #ea580c;
        }

        /* Sign Out Pill */
        .btn-signout {
            background-color: var(--signout-red) !important;
            color: #ffffff !important;
            border: none;
            padding: 8px 22px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
            text-decoration: none !important;
            transition: all 0.2s ease;
        }
        .btn-signout:hover {
            background-color: #b91c1c !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
            color: #ffffff !important;
        }

        .btn-login-pill {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: #ffffff !important;
            border: none;
            padding: 8px 24px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35);
            text-decoration: none !important;
            transition: all 0.2s ease;
        }
        .btn-login-pill:hover {
            background: linear-gradient(135deg, #ea580c, #c2410c);
            transform: translateY(-1px);
        }

        /* HERO SECTION */
        .hero-section {
            padding: 130px 0 80px;
            position: relative;
            background: linear-gradient(135deg, rgba(11, 17, 32, 0.92) 0%, rgba(3, 7, 18, 0.96) 100%), url('mainbg.jpg') center/cover;
            border-bottom: 1px solid var(--navbar-border);
            overflow: hidden;
        }
        [data-theme="light"] .hero-section {
            background: linear-gradient(135deg, rgba(248, 250, 252, 0.60) 0%, rgba(241, 245, 249, 0.75) 100%), url('mainbg.jpg') center/cover !important;
        }

        .hero-subheading {
            font-size: 0.85rem;
            letter-spacing: 0.12em;
            color: var(--accent-orange) !important;
            font-weight: 800;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text-heading) !important;
            line-height: 1.1;
        }
        .hero-title .accent-text {
            color: var(--accent-orange) !important;
            display: inline-block;
        }

        .pillar-card {
            background: var(--pillar-card-bg);
            border: 1px solid var(--pillar-border);
            border-radius: 12px;
            padding: 18px 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .pillar-card:hover {
            transform: translateY(-3px);
            border-color: var(--accent-orange);
        }
        .pillar-icon {
            font-size: 1.8rem;
            color: var(--accent-orange);
            margin-bottom: 8px;
        }
        .pillar-card h5 {
            font-size: 1.05rem;
            color: var(--text-heading) !important;
            letter-spacing: 0.02em;
        }
        .pillar-card p {
            color: var(--text-muted) !important;
        }

        .hero-divider {
            width: 80px;
            height: 4px;
            background: var(--accent-orange);
            border-radius: 2px;
        }

        .hero-tagline {
            color: var(--text-sub) !important;
            font-size: 1.15rem;
            font-weight: 600;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: #ffffff !important;
            border: none;
            box-shadow: 0 6px 20px rgba(234, 88, 12, 0.4);
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        .btn-hero-primary:hover {
            background: linear-gradient(135deg, #ea580c, #c2410c);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(234, 88, 12, 0.5);
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--card-border);
            color: var(--text-heading) !important;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        [data-theme="light"] .btn-hero-secondary {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a !important;
        }
        .btn-hero-secondary:hover {
            border-color: var(--accent-orange);
            color: var(--accent-orange) !important;
            transform: translateY(-2px);
        }

        .hero-preview-img {
            border: 2px solid var(--accent-orange);
            max-height: 380px;
            width: 100%;
            object-fit: cover;
        }
        .hero-floating-badge {
            position: absolute;
            bottom: -15px;
            left: 20px;
            background: #0f172a;
            border: 1px solid var(--accent-orange);
            border-radius: 12px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            text-align: left;
        }
        .badge-icon {
            font-size: 1.8rem;
            color: var(--accent-orange);
        }

        /* GENERAL CARDS */
        .card {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--card-border) !important;
            border-radius: 14px;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-orange) !important;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12) !important;
        }
        .card h3, .card h4, .card h5, .card-title {
            color: var(--text-heading) !important;
            font-weight: 700;
        }
        .card p, .card-text {
            color: var(--text-sub) !important;
            font-size: 0.95rem;
        }

        /* FACULTY CARDS & PHOTO ALIGNMENT (Fixes cut-off heads) */
        .faculty-card {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--card-border) !important;
            border-radius: 14px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .faculty-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-orange) !important;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2) !important;
        }
        .faculty-img-wrapper {
            width: 100%;
            height: 280px;
            background-color: #030712;
            overflow: hidden;
            position: relative;
        }
        [data-theme="light"] .faculty-img-wrapper {
            background-color: #f1f5f9;
        }
        .faculty-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            display: block;
            transition: transform 0.35s ease;
        }
        .faculty-card:hover .faculty-img {
            transform: scale(1.05);
        }
        .faculty-body {
            padding: 22px 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            text-align: center;
        }
        .faculty-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-heading) !important;
            margin-bottom: 8px;
        }
        .faculty-desc {
            color: var(--text-muted) !important;
            font-size: 0.92rem;
            line-height: 1.55;
            margin-bottom: 0;
        }

        /* COURSES CARDS */
        .course-img-wrapper {
            width: 100%;
            height: 210px;
            overflow: hidden;
            position: relative;
        }
        .course-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }
        .card:hover .course-img {
            transform: scale(1.05);
        }

        /* ADMISSION FORM */
        .admission-container {
            background-color: var(--card-bg);
            border-radius: 16px;
            padding: 44px;
            border: 1px solid var(--card-border);
            box-shadow: 0 12px 35px rgba(0,0,0,0.08);
        }
        .form-control {
            background-color: var(--bg-canvas) !important;
            border: 1px solid var(--card-border) !important;
            color: var(--text-heading) !important;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.95rem;
        }
        [data-theme="light"] .form-control {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        .form-control:focus {
            border-color: var(--accent-orange) !important;
            box-shadow: 0 0 0 0.25rem rgba(249, 115, 22, 0.2) !important;
        }
        .form-label {
            color: var(--text-heading) !important;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 6px;
        }

        /* FOOTER COMPREHENSIVE STYLING */
        .footer-section {
            background-color: var(--footer-bg);
            border-top: 1px solid var(--footer-border);
            color: var(--text-muted);
            transition: all 0.25s ease;
        }
        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s ease, transform 0.2s ease;
            display: inline-block;
        }
        .footer-links a:hover {
            color: var(--accent-orange);
            transform: translateX(4px);
        }
        .text-orange {
            color: var(--accent-orange) !important;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="logo.png" alt="Aether Logo" width="42" height="42" class="me-2 rounded-circle" style="border: 2px solid var(--accent-orange);">
                AETHER <span>ACADEMY</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: var(--card-border);">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <?php if($is_logged_in): ?>
                        <?php if($usertype == 'student'): ?>
                            <li class="nav-item"><a class="nav-link active-dash" href="studenthome.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="student_courses.php"><i class="fas fa-book me-1"></i> My Courses</a></li>
                            <li class="nav-item"><a class="nav-link" href="student_attendance.php"><i class="fas fa-clipboard-check me-1"></i> Attendance</a></li>
                            <li class="nav-item"><a class="nav-link" href="student_profile.php"><i class="fas fa-user-circle me-1"></i> Profile</a></li>
                        <?php elseif($usertype == 'admin'): ?>
                            <li class="nav-item"><a class="nav-link active-dash" href="adminhome.php"><i class="fas fa-tachometer-alt me-1"></i> Console</a></li>
                            <li class="nav-item"><a class="nav-link" href="admission.php"><i class="fas fa-user-plus me-1"></i> Admissions</a></li>
                            <li class="nav-item"><a class="nav-link" href="view_student.php"><i class="fas fa-users me-1"></i> Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="admin_view_teacher.php"><i class="fas fa-chalkboard-teacher me-1"></i> Teachers</a></li>
                            <li class="nav-item"><a class="nav-link" href="admin_courses.php"><i class="fas fa-graduation-cap me-1"></i> Courses</a></li>
                        <?php endif; ?>
                        
                        <li class="nav-item ms-lg-2 my-2 my-lg-0">
                            <button class="theme-toggle-btn shadow-sm" title="Toggle Light/Dark Theme" type="button" onclick="toggleTheme()">
                                <i class="fas fa-moon"></i>
                            </button>
                        </li>

                        <li class="nav-item ms-lg-2">
                            <a class="btn-signout d-inline-block" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i> Sign Out
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#courses">Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="#faculty">Faculty</a></li>
                        <li class="nav-item"><a class="nav-link" href="#admission">Admission</a></li>
                        <li class="nav-item ms-lg-2 my-2 my-lg-0">
                            <button class="theme-toggle-btn shadow-sm" title="Toggle Light/Dark Theme" type="button" onclick="toggleTheme()">
                                <i class="fas fa-moon"></i>
                            </button>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="btn-login-pill d-inline-block" href="login.php">
                                <i class="fas fa-user-lock me-1"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 text-start">
                    <div class="hero-subheading text-uppercase mb-2">
                        <i class="fas fa-certificate me-1"></i> EMPOWERING LEARNERS &bull; ENABLING EXCELLENCE &bull; SHAPING TOMORROW
                    </div>
                    <h1 class="hero-title mb-4">
                        AETHER <span class="accent-text">LMS</span>
                    </h1>
                    
                    <!-- 3 Core Value Pillars -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4 col-sm-6">
                            <div class="pillar-card">
                                <div class="pillar-icon"><i class="fas fa-book-open"></i></div>
                                <h5 class="fw-bold mb-1">LEARNING</h5>
                                <p class="text-muted small mb-0">Deliver Engaging Academic Experiences</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="pillar-card">
                                <div class="pillar-icon"><i class="fas fa-cogs"></i></div>
                                <h5 class="fw-bold mb-1">MANAGEMENT</h5>
                                <p class="text-muted small mb-0">Streamline Courses, Users & Operations</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="pillar-card">
                                <div class="pillar-icon"><i class="fas fa-chart-line"></i></div>
                                <h5 class="fw-bold mb-1">SUCCESS</h5>
                                <p class="text-muted small mb-0">Track Progress, Drive Real Outcomes</p>
                            </div>
                        </div>
                    </div>

                    <div class="hero-divider mb-3"></div>

                    <p class="hero-tagline mb-4">
                        Your Gateway to Knowledge, Collaboration & Institutional Growth.
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        <?php if($is_logged_in): ?>
                            <a href="<?php echo ($usertype == 'admin') ? 'adminhome.php' : 'studenthome.php'; ?>" class="btn btn-hero-primary rounded-pill px-4 py-3 fw-bold">
                                <i class="fas fa-tachometer-alt me-2"></i>Go To Dashboard
                            </a>
                        <?php else: ?>
                            <a href="#admission" class="btn btn-hero-primary rounded-pill px-4 py-3 fw-bold">
                                <i class="fas fa-paper-plane me-2"></i>Apply For Admission
                            </a>
                            <a href="login.php" class="btn btn-hero-secondary rounded-pill px-4 py-3 fw-bold">
                                <i class="fas fa-user-lock me-2"></i>Sign In Portal
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-5 d-none d-lg-block text-center position-relative mt-4 mt-lg-0">
                    <div class="position-relative d-inline-block">
                        <img src="school1.jpg" alt="Aether Campus" class="img-fluid rounded-4 shadow-lg hero-preview-img">
                        <div class="hero-floating-badge">
                            <div class="badge-icon"><i class="fas fa-graduation-cap"></i></div>
                            <div>
                                <div class="fw-bold text-white" style="font-size: 0.95rem;">Aether Academy</div>
                                <div class="text-white-50" style="font-size: 0.8rem;">Next-Gen LMS &bull; Academic Portal</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section (Fixed: Removed useless 'Meet our faculty' button) -->
    <div id="about" class="container py-5 mt-4">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <img src="school2.jpg" alt="School Building" class="img-fluid rounded-4 shadow-lg" style="border: 2px solid var(--accent-orange);">
            </div>
            <div class="col-lg-7 px-lg-5">
                <h2 class="fw-bold mb-3">Welcome to Aether Management Portal</h2>
                <div class="hero-divider mb-3"></div>
                <p class="about-text lh-lg mb-3">
                    <strong>1883: Founded by Visionary Educator</strong><br>
                    Aether College opened its doors in 1883, established by Professor Eleanor Cross. A pioneer in education, she envisioned a college fostering critical thinking, social responsibility, and a well-rounded education. Starting with a modest curriculum, Eleanor's dedication quickly attracted students and prominent faculty, cementing the college's reputation for academic rigor.
                </p>
                <p class="about-text lh-lg">
                    <strong>A Legacy of Modern Academic Excellence</strong><br>
                    Today, Aether Academy remains true to its founding principles while pioneering modern digital learning management. It offers a diverse range of undergraduate and graduate programs, serving a global student body with real-time academic analytics, streamlined admissions, and personalized faculty mentorship.
                </p>
            </div>
        </div>
    </div>

    <!-- Courses Section -->
    <div id="courses" class="container py-5 my-3">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Featured Academic Programs</h2>
            <div class="hero-divider mx-auto mb-2"></div>
            <p class="text-muted">Comprehensive, career-oriented curriculums designed by industry pioneers.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4 col-sm-6">
                <div class="card h-100">
                    <div class="course-img-wrapper">
                        <img src="webdev.jpeg" class="course-img" alt="Web Development">
                    </div>
                    <div class="card-body p-4 text-center">
                        <h4 class="fw-bold mb-2">Web Development</h4>
                        <p class="card-text text-muted mb-3">Full-stack web architecture, modern frontend frameworks, secure backend systems, and database engineering.</p>
                        <span class="badge rounded-pill" style="background: rgba(249,115,22,0.15); color: var(--accent-orange); border: 1px solid var(--accent-orange);">Engineering</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="card h-100">
                    <div class="course-img-wrapper">
                        <img src="graphics.jpeg" class="course-img" alt="Graphic Design">
                    </div>
                    <div class="card-body p-4 text-center">
                        <h4 class="fw-bold mb-2">Graphic Design &amp; UI/UX</h4>
                        <p class="card-text text-muted mb-3">Visual branding, digital media production, typography, interaction design, and user journey optimization.</p>
                        <span class="badge rounded-pill" style="background: rgba(249,115,22,0.15); color: var(--accent-orange); border: 1px solid var(--accent-orange);">Design</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div class="card h-100">
                    <div class="course-img-wrapper">
                        <img src="law.jpeg" class="course-img" alt="Corporate Law">
                    </div>
                    <div class="card-body p-4 text-center">
                        <h4 class="fw-bold mb-2">Corporate Law</h4>
                        <p class="card-text text-muted mb-3">International corporate governance, legal risk mitigation, contracts, intellectual property, and compliance.</p>
                        <span class="badge rounded-pill" style="background: rgba(249,115,22,0.15); color: var(--accent-orange); border: 1px solid var(--accent-orange);">Jurisprudence</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty Section (Fixed: Perfectly aligned photos with top-center framing) -->
    <div id="faculty" class="container py-5 my-3">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Distinguished Faculty</h2>
            <div class="hero-divider mx-auto mb-2"></div>
            <p class="text-muted">Learn from world-class legal minds, theorists, and visionary educators.</p>
        </div>

        <div class="row g-4">
            <?php while($info = $result->fetch_assoc()): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="faculty-card">
                        <div class="faculty-img-wrapper">
                            <img class="faculty-img" src="<?php echo htmlspecialchars($info['image']); ?>" alt="<?php echo htmlspecialchars($info['name']); ?>">
                        </div>
                        <div class="faculty-body">
                            <h4 class="faculty-name"><?php echo htmlspecialchars($info['name']); ?></h4>
                            <p class="faculty-desc"><?php echo htmlspecialchars($info['description']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Admission Section (AJAX Form) -->
    <div id="admission" class="container py-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="admission-container">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Apply For Admission</h2>
                        <div class="hero-divider mx-auto mb-2"></div>
                        <p class="text-muted">Fill out the official application form below. Our admissions office will process your candidate review.</p>
                    </div>

                    <!-- AJAX Alert Box -->
                    <div id="ajax-alert" class="alert d-none" role="alert"></div>

                    <?php if($message != ''): ?>
                        <div class="alert alert-success bg-transparent border-success text-success"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form id="admissionForm" action="data_check.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control form-control-lg" name="name" required placeholder="e.g. John Doe">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control form-control-lg" name="email" required placeholder="john@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" pattern="^\d{10}$" class="form-control form-control-lg" name="phone" required placeholder="10-digit mobile number" title="Please enter a valid 10-digit phone number">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Statement of Purpose / Message</label>
                            <textarea class="form-control" name="message" rows="4" required placeholder="Briefly introduce your academic interests and career goals..."></textarea>
                        </div>
                        <div class="d-grid mt-4">
                            <button id="submitBtn" type="submit" class="btn btn-hero-primary btn-lg rounded-pill fw-bold py-3">Submit Application</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Comprehensive Academic Footer -->
    <footer class="footer-section">
        <div class="container py-5">
            <div class="row g-4">
                <!-- Col 1: Brand & Mission -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <img src="logo.png" alt="Aether Logo" width="46" height="46" class="me-2 rounded-circle" style="border: 2px solid var(--accent-orange);">
                        <h4 class="fw-bold mb-0 text-white">AETHER <span class="text-orange">ACADEMY</span></h4>
                    </div>
                    <p class="small text-muted mb-3" style="line-height: 1.7;">
                        Next-Generation Academic Management &amp; Learning Portal. Delivering seamless administration, student attendance tracking, curated course management, and admissions for tomorrow's leaders.
                    </p>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge py-2 px-3 rounded-pill" style="background: rgba(249,115,22,0.12); color: var(--accent-orange); border: 1px solid rgba(249,115,22,0.3); font-size: 0.8rem;">
                            <i class="fas fa-shield-alt me-1"></i> Accredited Academic LMS
                        </span>
                        <span class="badge py-2 px-3 rounded-pill" style="background: rgba(16,185,129,0.12); color: #10b981; border: 1px solid rgba(16,185,129,0.3); font-size: 0.8rem;">
                            <i class="fas fa-check-circle me-1"></i> Active Portal v3.4
                        </span>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="text-uppercase fw-bold text-white mb-3" style="letter-spacing: 0.05em; font-size: 0.85rem;">Platform</h6>
                    <ul class="list-unstyled footer-links small">
                        <li class="mb-2"><a href="index.php"><i class="fas fa-chevron-right me-1 text-orange"></i> Homepage</a></li>
                        <li class="mb-2"><a href="#about"><i class="fas fa-chevron-right me-1 text-orange"></i> About Academy</a></li>
                        <li class="mb-2"><a href="#courses"><i class="fas fa-chevron-right me-1 text-orange"></i> Courses Offered</a></li>
                        <li class="mb-2"><a href="#faculty"><i class="fas fa-chevron-right me-1 text-orange"></i> Faculty Directory</a></li>
                        <li class="mb-2"><a href="#admission"><i class="fas fa-chevron-right me-1 text-orange"></i> Apply Online</a></li>
                    </ul>
                </div>

                <!-- Col 3: Academic Portals & Modules -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="text-uppercase fw-bold text-white mb-3" style="letter-spacing: 0.05em; font-size: 0.85rem;">Portals &amp; Modules</h6>
                    <ul class="list-unstyled footer-links small">
                        <li class="mb-2"><a href="login.php"><i class="fas fa-lock me-1 text-orange"></i> Student &amp; Staff Login</a></li>
                        <li class="mb-2"><a href="studenthome.php"><i class="fas fa-user-graduate me-1 text-orange"></i> Student Dashboard</a></li>
                        <li class="mb-2"><a href="student_attendance.php"><i class="fas fa-clipboard-check me-1 text-orange"></i> Attendance Tracker</a></li>
                        <li class="mb-2"><a href="adminhome.php"><i class="fas fa-tools me-1 text-orange"></i> Administration Console</a></li>
                        <li class="mb-2"><a href="admission.php"><i class="fas fa-file-alt me-1 text-orange"></i> Admissions Queue</a></li>
                    </ul>
                </div>

                <!-- Col 4: Campus & Inquiries -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-uppercase fw-bold text-white mb-3" style="letter-spacing: 0.05em; font-size: 0.85rem;">Contact &amp; Campus</h6>
                    <ul class="list-unstyled text-muted small footer-contact mb-3">
                        <li class="mb-2 d-flex align-items-start">
                            <i class="fas fa-map-marker-alt text-orange mt-1 me-2"></i>
                            <span>University Boulevard, Academic District, Building 4A</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fas fa-envelope text-orange me-2"></i>
                            <span>admissions@aetheracademy.edu</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fas fa-phone-alt text-orange me-2"></i>
                            <span>+1 (800) 238-4370</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fas fa-clock text-orange me-2"></i>
                            <span>Mon - Fri: 8:00 AM - 6:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Divider -->
            <hr class="my-4" style="border-color: var(--navbar-border); opacity: 0.6;">

            <!-- Bottom Row: Copyright & Legal/Social -->
            <div class="row align-items-center small">
                <div class="col-md-7 text-center text-md-start mb-2 mb-md-0 text-muted">
                    &copy; 2026 Aether Academy. All Rights Reserved. Engineered by <strong>Uday Praveen</strong> (PRN: 23030124196).
                </div>
                <div class="col-md-5 text-center text-md-end">
                    <div class="d-inline-flex gap-3 text-muted">
                        <a href="#" class="text-muted text-decoration-none">Privacy Policy</a>
                        <span>&bull;</span>
                        <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                        <span>&bull;</span>
                        <a href="#" class="text-muted text-decoration-none">Academic Code</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        function toggleTheme() {
            var current = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', current);
            localStorage.setItem('aether-theme', current);
            updateThemeIcons(current);
        }
        function updateThemeIcons(theme) {
            $('.theme-toggle-btn i').each(function() {
                if(theme === 'light') {
                    $(this).removeClass('fa-moon').addClass('fa-sun');
                } else {
                    $(this).removeClass('fa-sun').addClass('fa-moon');
                }
            });
        }
        $(document).ready(function() {
            var theme = localStorage.getItem('aether-theme') || 'dark';
            updateThemeIcons(theme);
        });
    </script>

    <!-- AJAX Form Handling -->
    <script>
        $(document).ready(function() {
            $('#admissionForm').on('submit', function(e) {
                e.preventDefault();
                var $btn = $('#submitBtn');
                var originalText = $btn.text();
                $btn.prop('disabled', true).text('Submitting Application...');

                $.ajax({
                    type: 'POST',
                    url: 'data_check.php',
                    data: $(this).serialize() + '&apply=1',
                    success: function(response) {
                        $('#ajax-alert').removeClass('d-none alert-danger').addClass('alert-success')
                            .text('Application received successfully! Our admissions office will contact you.');
                        $('#admissionForm')[0].reset();
                    },
                    error: function() {
                        $('#ajax-alert').removeClass('d-none alert-success').addClass('alert-danger')
                            .text('An error occurred. Please try again.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
    </script>

    <script>
      console.log('%c Aether Academy %c Developed by Uday Praveen ', 'background: #ea580c; color: #fff; border-radius: 3px 0 0 3px; padding: 2px 5px; font-weight: bold;', 'background: #0f172a; color: #fff; border-radius: 0 3px 3px 0; padding: 2px 5px;');
    </script>
</body>
</html>
