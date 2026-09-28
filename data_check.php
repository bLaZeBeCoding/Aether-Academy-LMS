<?php
session_start(); // Initialize session to handle fallback flash messages

// 1. DATABASE CONNECTION
// Establish connection to MySQL server running on custom port 3307
$data = mysqli_connect("localhost", "root", "", "collegepr", 3307);

if($data === false) {
    echo json_encode(["status" => "error", "message" => "Database connection error!"]);
    exit;
}

if(isset($_POST['apply'])) {
    // 2. INPUT VALIDATION & SANITIZATION (Security Requirement)
    // mysqli_real_escape_string() is crucial here to prevent SQL Injection attacks
    // It safely escapes special characters input by the applicant in the form
    $data_name = mysqli_real_escape_string($data, $_POST['name']);
    $data_email = mysqli_real_escape_string($data, $_POST['email']);
    $data_phone = mysqli_real_escape_string($data, $_POST['phone']);
    $data_message = mysqli_real_escape_string($data, $_POST['message']);

    // 3. DATABASE EXECUTION (CRUD: Create)
    $sql = "INSERT INTO admission(name, email, phone, message) VALUES('$data_name', '$data_email', '$data_phone', '$data_message')";
    $result = mysqli_query($data, $sql);

    // 4. AJAX DETECTION (Modern UI Requirement)
    // This server variable checks if the request was sent seamlessly via jQuery AJAX
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
        // Set response header to JSON so the frontend JavaScript can parse it
        header('Content-Type: application/json');
        
        if($result) {
            echo json_encode(["status" => "success", "message" => "Your application has been sent successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Apply Failed!"]);
        }
        exit;
    } else {
        // 5. TRADITIONAL FALLBACK
        // If the user has JavaScript disabled, it falls back to standard HTTP POST and redirects
        if($result) {
            $_SESSION['message'] = "Your application has been sent successfully!";
            header("location:index.php");
        } else {
            echo "Apply Failed!";
        }
        exit;
    }
}
?>
