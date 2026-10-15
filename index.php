<?php header("X-Author: Uday Praveen (PRN: 23030124196) - SICSR Pune"); ?>
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
* Institution: SICSR Pune
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
    <title>Aether Academy | Modern LMS & Student Management</title>
    
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

        /* Sign Out Pill (Matching Symbiosis Image 2 & 5) */
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

        /* HERO SECTION (Modeled on Symbiosis LMS Image 2) */
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
            color: var(--accent-orange);
            font-weight: 800;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text-heading);
            line-height: 1.1;
        }
        .hero-title .accent-text {
            color: var(--accent-orange);
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
            color: var(--text-heading);
            letter-spacing: 0.02em;
        }

        .hero-divider {
            width: 80px;
            height: 4px;
            background: var(--accent-orange);
            border-radius: 2px;
        }

        .hero-tagline {
            color: var(--text-sub);
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

        /* CARDS & CONTENT SECTIONS */
        .card {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--card-border) !important;
            border-radius: 14px;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }
        .card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-orange) !important;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12) !important;
        }
        .card h3 {
            color: var(--text-heading) !important;
            font-weight: 700;
        }
        .card p {
            color: var(--text-muted) !important;
            font-size: 0.95rem;
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

        footer {
            background-color: var(--navbar-bg);
            border-top: 1px solid var(--navbar-border);
            padding: 40px 0;
            color: var(--text-muted);
        }
        footer h5 {
            color: var(--text-heading) !important;
            font-weight: 700;
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
                            <!-- Student Logged In Nav (Matching Image 2 & 5) -->
                            <li class="nav-item"><a class="nav-link active-dash" href="studenthome.php"><i class="fas fa-th-large me-1"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="student_courses.php"><i class="fas fa-book me-1"></i> My Courses</a></li>
                            <li class="nav-item"><a class="nav-link" href="student_attendance.php"><i class="fas fa-clipboard-check me-1"></i> My Attendance</a></li>
                            <li class="nav-item"><a class="nav-link" href="student_profile.php"><i class="fas fa-user-circle me-1"></i> Profile</a></li>
                        <?php else: ?>
                            <!-- Admin Logged In Nav -->
                            <li class="nav-item"><a class="nav-link active-dash" href="adminhome.php"><i class="fas fa-chart-line me-1"></i> Admin Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="view_student.php"><i class="fas fa-user-graduate me-1"></i> Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="admin_view_teacher.php"><i class="fas fa-chalkboard-teacher me-1"></i> Faculty</a></li>
                            <li class="nav-item"><a class="nav-link" href="admin_attendance.php"><i class="fas fa-clipboard-check me-1"></i> Attendance</a></li>
                        <?php endif; ?>
                        <li class="nav-item ms-3 d-flex align-items-center gap-2">
                            <button class="theme-toggle-btn" title="Toggle Light/Dark Theme" type="button" onclick="toggleTheme()">
                                <i class="fas fa-moon"></i>
                            </button>
                            <a href="logout.php" class="btn btn-signout"><i class="fas fa-sign-out-alt me-1"></i> Sign Out</a>
                        </li>
                    <?php else: ?>
                        <!-- Guest Navigation -->
                        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#faculty">Faculty</a></li>
                        <li class="nav-item"><a class="nav-link" href="#admission">Admissions</a></li>
                        <li class="nav-item ms-3 d-flex align-items-center gap-2">
                            <button class="theme-toggle-btn" title="Toggle Light/Dark Theme" type="button" onclick="toggleTheme()">
                                <i class="fas fa-moon"></i>
                            </button>
                            <a href="login.php" class="btn btn-login-pill fw-bold"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section (Inspired by Symbiosis LMS Banner - Image 2) -->
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
                    
                    <!-- 3 Core Value Pillars (Image 2) -->
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
                                <div class="text-white-50" style="font-size: 0.8rem;">SICSR Pune LMS &bull; Academic Portal</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div id="about" class="container py-5 mt-4">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <img src="school2.jpg" alt="School Building" class="img-fluid rounded-4 shadow-lg" style="border: 2px solid var(--accent-orange);">
            </div>
            <div class="col-lg-7 px-lg-5">
                <h2 class="fw-bold mb-3" style="color: var(--text-heading);">Welcome to Aether Management Portal</h2>
                <div class="hero-divider mb-3"></div>
                <p class="text-muted lh-lg">
                    <strong style="color: var(--text-heading);">1883: Founded by Visionary Educator</strong><br>
                    Aether College opened its doors in 1883, established by Professor Eleanor Cross. A pioneer in education, she envisioned a college fostering critical thinking, social responsibility, and a well-rounded education. Starting with a modest curriculum, Eleanor's dedication quickly attracted students and prominent faculty, cementing the college's reputation for academic rigor.
                </p>
                <div class="mt-4">
                    <a href="#faculty" class="btn btn-hero-secondary rounded-pill px-4 py-2 fw-bold">
                        Meet Our Faculty <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty Section -->
    <div id="faculty" class="container py-5 my-3">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: var(--text-heading);">Distinguished Faculty</h2>
            <div class="hero-divider mx-auto mb-2"></div>
            <p class="text-muted">Learn from world-class legal minds, theorists, and visionary educators.</p>
        </div>

        <div class="row g-4">
            <?php while($info = $result->fetch_assoc()): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 text-center p-3">
                        <img class="teacher-img" src="<?php echo $info['image']; ?>" alt="<?php echo htmlspecialchars($info['name']); ?>" style="height: 220px; object-fit: cover; border-radius: 10px;">
                        <div class="card-body">
                            <h4 class="fw-bold mb-2"><?php echo htmlspecialchars($info['name']); ?></h4>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars($info['description']); ?></p>
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
                        <h2 class="fw-bold" style="color: var(--text-heading);">Apply For Admission</h2>
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

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <h5 class="mb-2">Aether Academy</h5>
            <p class="small mb-1">Symbiosis Institute of Computer Studies and Research (SICSR), Pune</p>
            <p class="small text-muted mb-0">&copy; 2026 Aether Academy. All Rights Reserved. Engineered by Uday Praveen (PRN: 23030124196).</p>
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
      console.log('%c Aether Academy %c Developed by Uday Praveen (SICSR Pune) ', 'background: #ea580c; color: #fff; border-radius: 3px 0 0 3px; padding: 2px 5px; font-weight: bold;', 'background: #0f172a; color: #fff; border-radius: 0 3px 3px 0; padding: 2px 5px;');
    </script>
</body>
</html>
