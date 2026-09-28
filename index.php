<?php header("X-Author: Uday Praveen (PRN: 23030124196) - SICSR Pune"); ?>`n<?php

error_reporting(0);
session_start();

// We will keep the session message check for fallback, but our new AJAX form won't need it.
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aether Academy | Student Management</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS (Modernized) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0f172a; /* Slate 900 */
            color: #f8fafc; /* Slate 50 */
        }
        /* Navbar Styling */
        .navbar {
            background-color: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #1e293b;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 1px;
            color: #ffffff !important;
            background: linear-gradient(90deg, #06b6d4, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-link {
            color: #94a3b8 !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #06b6d4 !important;
        }
        /* Buttons */
        .btn-neon {
            background: linear-gradient(45deg, #8b5cf6, #06b6d4);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-neon:hover {
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.5);
            color: white;
            transform: translateY(-2px);
        }
        .btn-outline-neon {
            border: 2px solid #06b6d4;
            color: #06b6d4;
            background: transparent;
            transition: all 0.3s ease;
        }
        .btn-outline-neon:hover {
            background: #06b6d4;
            color: #0f172a;
            box-shadow: 0 0 10px rgba(6, 182, 212, 0.4);
        }
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.95)), url('mainbg.jpg') no-repeat center center/cover;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            margin-top: 56px;
        }
        .hero-section h1 {
            font-size: 4.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(6, 182, 212, 0.3);
        }
        .hero-section p {
            font-size: 1.2rem;
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
            color: #cbd5e1;
        }
        /* Cards */
        .faculty-img {
            height: 250px;
            object-fit: cover;
            object-position: 50% 15%;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .course-img {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            border-color: #06b6d4;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;
        }
        .text-muted {
            color: #94a3b8 !important;
        }
        /* Admission Form */
        .admission-container {
            background-color: #1e293b;
            border-radius: 16px;
            padding: 40px;
            border: 1px solid #334155;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .form-control {
            background-color: #0f172a !important;
            border: 1px solid #334155;
            color: #f8fafc !important;
        }
        .form-control:focus {
            border-color: #06b6d4;
            box-shadow: 0 0 0 0.25rem rgba(6, 182, 212, 0.25);
        }
        .form-control::placeholder {
            color: #64748b;
        }
        footer {
            background-color: #020617;
            color: #64748b;
            padding: 30px 0;
            text-align: center;
            border-top: 1px solid #1e293b;
        }
        .section-divider {
            width: 80px; 
            height: 4px; 
            background: linear-gradient(90deg, #06b6d4, #8b5cf6); 
            margin: 15px auto; 
            border-radius: 2px;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="logo.jpg" alt="Logo" width="40" height="40" class="me-2 rounded-circle" style="border: 2px solid #06b6d4;">
                Aether Academy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: #334155;">
                <span class="navbar-toggler-icon" style="filter: invert(1) opacity(0.7);"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faculty">Faculty</a></li>
                    <li class="nav-item"><a class="nav-link" href="#admission">Admissions</a></li>
                    <li class="nav-item ms-3"><a href="login.php" class="btn btn-outline-neon rounded-pill px-4 fw-bold">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1>Shape Your Future</h1>
            <p>Join a legacy of excellence, critical thinking, and visionary education established in 1883.</p>
            <a href="#admission" class="btn btn-neon btn-lg mt-4 rounded-pill px-5 py-3 fw-bold shadow-lg">Apply Now</a>
        </div>
    </div>

    <!-- About Section -->
    <div id="about" class="container py-5 mt-5">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <img src="school2.jpg" alt="School Building" class="img-fluid rounded-4 shadow-lg" style="border: 1px solid #334155;">
            </div>
            <div class="col-lg-7 px-lg-5">
                <h2 class="fw-bold mb-4">Welcome to Aether Management Portal</h2>
                <p class="text-muted lh-lg">
                    <strong class="text-white">1883: Founded by Visionary Educator</strong><br>
                    Aether College opened its doors in 1883, established by Professor Eleanor Cross. A pioneer in education, she envisioned a college fostering critical thinking, social responsibility, and a well-rounded education.
                </p>
                <p class="text-muted lh-lg">
                    <strong class="text-white">A Legacy of Excellence</strong><br>
                    Today, Aether College remains true to its founding principles. It offers a diverse range of undergraduate and graduate programs, attracting a global student body, continuing to be a leader in fostering future leaders and scholars.
                </p>
            </div>
        </div>
    </div>

    <!-- Faculty Section -->
    <div id="faculty" class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Meet Our Faculty</h2>
            <div class="section-divider"></div>
        </div>
        <div class="row g-4">
            <?php while($info = $result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <img src="<?php echo htmlspecialchars($info['image']); ?>" class="card-img-top faculty-img" alt="Teacher">
                    <div class="card-body text-center p-4">
                        <h4 class="card-title fw-bold text-white mb-3"><?php echo htmlspecialchars($info['name']); ?></h4>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($info['description']); ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Courses Section -->
    <div id="courses" class="container py-5 my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Top Courses</h2>
            <div class="section-divider"></div>
        </div>
        <div class="row g-4 px-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <img src="webdev.jpeg" class="card-img-top course-img" alt="Web Development">
                    <div class="card-body text-center p-4">
                        <h5 class="fw-bold text-white">Web Development</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <img src="graphics.jpeg" class="card-img-top course-img" alt="Graphics Design">
                    <div class="card-body text-center p-4">
                        <h5 class="fw-bold text-white">Graphics Design</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <img src="law.jpeg" class="card-img-top course-img" alt="Corporate Law">
                    <div class="card-body text-center p-4">
                        <h5 class="fw-bold text-white">Corporate Law</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admission Section (AJAX Form) -->
    <div id="admission" class="container py-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="admission-container">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold">Want To Join Us?</h2>
                        <p class="text-muted">Fill out the admission form below and our team will contact you.</p>
                    </div>

                    <!-- AJAX Alert Box (Hidden by default) -->
                    <div id="ajax-alert" class="alert d-none" role="alert"></div>

                    <!-- Fallback PHP Alert -->
                    <?php if($message != ''): ?>
                        <div class="alert alert-success bg-transparent border-success text-success"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form id="admissionForm" action="data_check.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-white">Full Name</label>
                            <input type="text" class="form-control form-control-lg" name="name" required placeholder="John Doe">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-white">Email Address</label>
                            <input type="email" class="form-control form-control-lg" name="email" required placeholder="john@example.com">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-white">Phone Number</label>
                            <input type="tel" pattern="^\d{10}$" class="form-control form-control-lg" name="phone" required placeholder="10-digit number" title="Please enter a valid 10-digit phone number">
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold text-white">Message / Queries</label>
                            <textarea class="form-control form-control-lg" name="message" rows="4" placeholder="Tell us about yourself..."></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="apply" class="btn btn-neon btn-lg fw-bold py-3" id="submitBtn">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p class="mb-0">&copy; 2026 Aether Academy College. All rights reserved.</p>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery Implementation (Syllabus Requirement) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // 1. EVENT LISTENER
            // Intercept the form submission event to handle it dynamically without page reload
            $('#admissionForm').on('submit', function(e) {
                e.preventDefault(); // Prevent standard HTTP form action

                // 2. UI STATE MANAGEMENT
                // Change button state to show loading so the user doesn't double-submit
                var $btn = $('#submitBtn');
                var originalText = $btn.text();
                $btn.prop('disabled', true).text('Submitting...');

                // 3. AJAX REQUEST
                // Send the asynchronous HTTP POST request to data_check.php
                $.ajax({
                    type: 'POST',
                    url: 'data_check.php',
                    data: $(this).serialize() + '&apply=1', // Serialize form fields into URL-encoded string
                    success: function(response) {
                        // 4a. SUCCESS CALLBACK
                        // Dynamically update the DOM to show the success message returned from the PHP server
                        $('#ajax-alert').removeClass('d-none alert-danger').addClass('alert-success')
                            .text('Success! Your application has been sent.');
                        
                        // Clear the form fields
                        $('#admissionForm')[0].reset();
                    },
                    error: function() {
                        // 4b. ERROR CALLBACK
                        // Handle server or network failures gracefully
                        $('#ajax-alert').removeClass('d-none alert-success').addClass('alert-danger')
                            .text('An error occurred. Please try again.');
                    },
                    complete: function() {
                        // 5. RESTORE UI
                        // Re-enable the button regardless of success or failure
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
    </script>

<script>
  console.log('%c Aether Academy %c Developed by Uday Praveen (SICSR Pune) ', 'background: #06b6d4; color: #fff; border-radius: 3px 0 0 3px; padding: 2px 5px; font-weight: bold;', 'background: #8b5cf6; color: #fff; border-radius: 0 3px 3px 0; padding: 2px 5px;');
</script>
</body>
</html>

